<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceCode;
use App\Models\SessionEnrollment;
use App\Models\StudentNotification;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    protected function checkEnrollmentStatus($user): ?RedirectResponse
    {
        if ($user && $user->role === 'student') {
            $enrollment = $user->latestEnrollment;
            if ($enrollment) {
                if ($enrollment->status === 'pending') {
                    return redirect()->route('enrollment.waiting');
                }
                if ($enrollment->status === 'rejected') {
                    return redirect()->route('enrollment')
                        ->with('error', 'Your enrollment was rejected. Please contact the registrar for more information.');
                }
            } else {
                // No enrollment record - redirect to enrollment page
                return redirect()->route('enrollment')
                    ->with('error', 'You must complete your enrollment before accessing this feature.');
            }
        }

        return null;
    }

    public function index(): View|RedirectResponse
    {
        $user = Auth::user();

        // Check if enrollment is approved
        $enrollmentStatus = $this->checkEnrollmentStatus($user);
        if ($enrollmentStatus !== null) {
            return $enrollmentStatus;
        }
        $now = now();
        $today = $now->toDateString();
        $currentTime = $now->format('H:i:s');

        $attendances = Attendance::where('user_id', $user->id)
            ->with(['subject', 'attendanceCode', 'attendanceCode.classSession'])
            ->orderBy('date', 'desc')
            ->orderBy('time_in', 'desc')
            ->paginate(20);

        // Get unread notifications with valid (active and non-expired) attendance codes
        $unreadNotifications = $user->unreadNotifications()
            ->where('type', 'attendance_code')
            ->with('attendanceCode')
            ->orderBy('created_at', 'desc')
            ->get()
            ->filter(function ($notification) {
                // Only show notifications with valid (active and non-expired) codes
                if (! $notification->attendanceCode) {
                    return false;
                }

                return $notification->attendanceCode->isValid();
            })
            ->take(5)
            ->values();

        // Automatically mark expired/inactive code notifications as read
        $user->unreadNotifications()
            ->where('type', 'attendance_code')
            ->with('attendanceCode')
            ->get()
            ->filter(function ($notification) {
                if (! $notification->attendanceCode) {
                    return true; // Mark as read if code doesn't exist
                }

                return ! $notification->attendanceCode->isValid(); // Mark as read if expired or inactive
            })
            ->each(function ($notification) {
                $notification->markAsRead();
            });

        // Find active session for today
        $activeSession = null;
        $activeSessionEnrollment = SessionEnrollment::where('user_id', $user->id)
            ->where('session_date', $today)
            ->where('is_active', true)
            ->with('classSession')
            ->first();

        if ($activeSessionEnrollment && $activeSessionEnrollment->classSession) {
            $session = $activeSessionEnrollment->classSession;
            $endTime = $session->end_time;

            // Show active session if it hasn't ended yet (or if end time is not set)
            if ($endTime) {
                // Check if session hasn't ended yet
                if ($currentTime < $endTime) {
                    $activeSession = $session;
                }
            } else {
                // If no end time is set, show the session
                $activeSession = $session;
            }
        }

        return view('student.attendance', [
            'attendances' => $attendances,
            'unreadNotifications' => $unreadNotifications,
            'activeSession' => $activeSession,
        ]);
    }

    public function submitCode(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // Check if enrollment is approved
        $enrollmentStatus = $this->checkEnrollmentStatus($user);
        if ($enrollmentStatus !== null) {
            return $enrollmentStatus;
        }

        $validated = $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $code = strtoupper($validated['code']);

        // Find active attendance code
        $attendanceCode = AttendanceCode::where('code', $code)
            ->where('is_active', true)
            ->first();

        if (! $attendanceCode) {
            return back()->withErrors(['code' => 'Invalid or inactive attendance code.']);
        }

        // Check if code is expired
        if ($attendanceCode->isExpired()) {
            return back()->withErrors(['code' => 'This attendance code has expired.']);
        }

        // Check if code is still active
        if (! $attendanceCode->is_active) {
            return back()->withErrors(['code' => 'This attendance code has been deactivated.']);
        }

        // Get the class session
        $classSession = $attendanceCode->classSession;
        if (! $classSession) {
            return back()->withErrors(['code' => 'Invalid session for this code.']);
        }

        // Validate submission time is within session window
        $now = now();
        $sessionDate = \Carbon\Carbon::parse($attendanceCode->date)->startOfDay();

        // Parse session times - handle both TIME fields and time string field
        $sessionStart = null;
        $sessionEnd = null;

        if ($classSession->start_time && $classSession->end_time) {
            // Use TIME fields if available (format: "07:00:00")
            $startTimeParts = explode(':', $classSession->start_time);
            $endTimeParts = explode(':', $classSession->end_time);

            $sessionStart = $sessionDate->copy()
                ->setTime((int) $startTimeParts[0], (int) ($startTimeParts[1] ?? 0), (int) ($startTimeParts[2] ?? 0));
            $sessionEnd = $sessionDate->copy()
                ->setTime((int) $endTimeParts[0], (int) ($endTimeParts[1] ?? 0), (int) ($endTimeParts[2] ?? 0));
        } elseif ($classSession->time) {
            // Fallback to parsing from time string field (format: "8:00 AM - 9:30 AM")
            $timeString = $classSession->time;
            // Try to extract times from string like "8:00 AM - 9:30 AM" or "8:00AM-9:30AM"
            if (preg_match('/(\d{1,2}):(\d{2})\s*(AM|PM)\s*-\s*(\d{1,2}):(\d{2})\s*(AM|PM)/i', $timeString, $matches)) {
                $startHour = (int) $matches[1];
                $startMinute = (int) $matches[2];
                $startPeriod = strtoupper($matches[3]);
                $endHour = (int) $matches[4];
                $endMinute = (int) $matches[5];
                $endPeriod = strtoupper($matches[6]);

                // Convert to 24-hour format
                if ($startPeriod === 'PM' && $startHour !== 12) {
                    $startHour += 12;
                } elseif ($startPeriod === 'AM' && $startHour === 12) {
                    $startHour = 0;
                }
                if ($endPeriod === 'PM' && $endHour !== 12) {
                    $endHour += 12;
                } elseif ($endPeriod === 'AM' && $endHour === 12) {
                    $endHour = 0;
                }

                $sessionStart = $sessionDate->copy()->setTime($startHour, $startMinute, 0);
                $sessionEnd = $sessionDate->copy()->setTime($endHour, $endMinute, 0);
            }
        }

        // If we still don't have valid times, return error
        if (! $sessionStart || ! $sessionEnd) {
            return back()->withErrors(['code' => 'Invalid session time configuration. Please contact your instructor.']);
        }

        // Allow submissions at any time as long as the code hasn't expired
        // The code expiration time is already checked above, so we only need to verify
        // that the submission is within a reasonable window (code expiration handles the end time)
        // No need to check for early submission restriction - attendance can be submitted at any time

        // Check if student has already used this code
        $existingAttendance = Attendance::where('user_id', $user->id)
            ->where('attendance_code_id', $attendanceCode->id)
            ->first();

        if ($existingAttendance) {
            return back()->withErrors(['code' => 'You have already submitted this attendance code.']);
        }

        // Check if student is enrolled in this session (temporary enrollment)
        $sessionEnrollment = SessionEnrollment::where('user_id', $user->id)
            ->where('class_session_id', $attendanceCode->class_session_id)
            ->where('session_date', $attendanceCode->date)
            ->where('is_active', true)
            ->first();

        if (! $sessionEnrollment) {
            return back()->withErrors(['code' => 'You are not enrolled in this class session.']);
        }

        // Capture the exact submission time right before creating the record
        $submissionTime = now();

        // Create attendance record with all required fields
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'subject_id' => $attendanceCode->subject_id, // Keep for backward compatibility
            'attendance_code_id' => $attendanceCode->id,
            'date' => $attendanceCode->date,
            'status' => 'present',
            'time_in' => $submissionTime->format('H:i:s'),
            'time_out' => null,
        ]);

        // Update session enrollment with attendance code reference
        $sessionEnrollment->update(['attendance_code_id' => $attendanceCode->id]);

        // Mark notification as read if exists
        StudentNotification::where('user_id', $user->id)
            ->where('attendance_code_id', $attendanceCode->id)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return redirect()->route('attendance')
            ->with('success', 'Attendance recorded successfully!');
    }

    public function markNotificationRead(int $id): RedirectResponse
    {
        $user = Auth::user();
        $notification = StudentNotification::where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        $notification->markAsRead();

        return back()->with('success', 'Notification marked as read.');
    }
}
