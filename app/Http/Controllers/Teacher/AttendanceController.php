<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Mail\AttendanceCodeMail;
use App\Models\Attendance;
use App\Models\AttendanceCode;
use App\Models\ClassSession;
use App\Models\SessionEnrollment;
use App\Models\StudentNotification;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AttendanceController extends Controller
{
    private function checkTeacher(): void
    {
        if (! Auth::check() || Auth::user()->role !== 'teacher') {
            abort(403, 'Unauthorized access.');
        }
    }

    public function index(): View
    {
        $this->checkTeacher();

        /** @var \App\Models\User $teacher */
        $teacher = Auth::user();
        $teacherCourse = $teacher->course;
        $teacherYearLevel = $teacher->year_level;

        // Get student IDs that match teacher's course and year level
        $studentIds = User::where('role', 'student')
            ->when($teacherCourse && $teacherYearLevel, function ($query) use ($teacherCourse, $teacherYearLevel) {
                $query->where('course', $teacherCourse)
                    ->where('year_level', $teacherYearLevel);
            }, function ($query) {
                // If course or year level not assigned to teacher, return none
                $query->whereRaw('1 = 0');
            })
            ->pluck('id');

        $attendances = Attendance::whereIn('user_id', $studentIds)
            ->with(['user', 'subject', 'attendanceCode', 'attendanceCode.classSession'])
            ->orderBy('date', 'desc')
            ->orderBy('time_in', 'desc')
            ->paginate(20);

        // Filter class sessions by teacher's course if available
        $classSessions = ClassSession::where('is_active', true)
            ->when($teacherCourse, function ($query) use ($teacherCourse) {
                $query->where('course', $teacherCourse);
            })
            ->orderBy('start_time')
            ->get();

        // Filter codes to only show those created by this teacher
        $recentCodes = AttendanceCode::with(['classSession', 'creator'])
            ->where('is_active', true)
            ->where('expires_at', '>', now())
            ->where('created_by', $teacher->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $allCodes = AttendanceCode::with(['classSession', 'creator'])
            ->where('created_by', $teacher->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('teacher.attendance.index', [
            'attendances' => $attendances,
            'classSessions' => $classSessions,
            'recentCodes' => $recentCodes,
            'allCodes' => $allCodes,
        ]);
    }

    public function generateCode(Request $request): RedirectResponse
    {
        $this->checkTeacher();

        $validated = $request->validate([
            'class_session_id' => ['required', 'exists:class_sessions,id'],
            'date' => ['required', 'date'],
        ]);

        $classSession = ClassSession::findOrFail($validated['class_session_id']);

        $sessionDate = now()->parse($validated['date'])->startOfDay();
        $expiresAt = null;

        // Parse end time from class session
        if ($classSession->end_time) {
            // Use end_time field if available (format: "09:30:00")
            $endTimeParts = explode(':', $classSession->end_time);
            // Set expiration to 2 minutes after class ends to allow submissions until class ends
            $expiresAt = $sessionDate->copy()
                ->setTime((int) $endTimeParts[0], (int) ($endTimeParts[1] ?? 0), (int) ($endTimeParts[2] ?? 0))
                ->addMinutes(2);
        } elseif ($classSession->time) {
            // Fallback to parsing from time string field (format: "8:00 AM - 9:30 AM")
            $timeString = trim($classSession->time);

            // Try multiple patterns to handle different time formats
            $endHour = null;
            $endMinute = null;
            $endPeriod = null;

            // Pattern 1: "8:00 AM - 9:30 AM" or "8:00 AM to 9:30 AM" (with spaces)
            if (preg_match('/(\d{1,2}):(\d{2})\s*(AM|PM)\s*[-to]+\s*(\d{1,2}):(\d{2})\s*(AM|PM)/i', $timeString, $matches)) {
                $endHour = (int) $matches[4];
                $endMinute = (int) $matches[5];
                $endPeriod = strtoupper($matches[6]);
            }
            // Pattern 2: "8:00AM-9:30AM" (no spaces between time and AM/PM)
            elseif (preg_match('/(\d{1,2}):(\d{2})(AM|PM)\s*[-to]+\s*(\d{1,2}):(\d{2})(AM|PM)/i', $timeString, $matches)) {
                $endHour = (int) $matches[4];
                $endMinute = (int) $matches[5];
                $endPeriod = strtoupper($matches[6]);
            }

            // If we found a match, convert to 24-hour format and set expiration
            if ($endHour !== null && $endMinute !== null && $endPeriod !== null) {
                // Convert to 24-hour format
                if ($endPeriod === 'PM' && $endHour !== 12) {
                    $endHour += 12;
                } elseif ($endPeriod === 'AM' && $endHour === 12) {
                    $endHour = 0;
                }

                // Set expiration to 2 minutes after class ends to allow submissions until class ends
                // This accounts for clock differences and ensures students can submit until the class actually ends
                $expiresAt = $sessionDate->copy()
                    ->setTime($endHour, $endMinute, 0)
                    ->addMinutes(2);
            }
        }

        // If we still don't have a valid expiration time, return error with helpful message
        if (! $expiresAt) {
            $sessionName = $classSession->name ?? 'Selected session';
            $hasEndTime = ! empty($classSession->end_time);
            $hasTime = ! empty($classSession->time);

            $errorMessage = "Unable to determine class end time for '{$sessionName}'. ";
            if (! $hasEndTime && ! $hasTime) {
                $errorMessage .= "The class session is missing time information. Please contact an administrator to update the class session with a valid time (e.g., '8:00 AM - 9:30 AM').";
            } elseif ($hasTime) {
                $errorMessage .= "The time format '{$classSession->time}' could not be parsed. Please ensure the time is in the format '8:00 AM - 9:30 AM'.";
            } else {
                $errorMessage .= 'Please contact an administrator to update the class session time configuration.';
            }

            Log::warning('Failed to determine class end time for attendance code generation', [
                'class_session_id' => $classSession->id,
                'class_session_name' => $sessionName,
                'has_end_time' => $hasEndTime,
                'has_time' => $hasTime,
                'end_time_value' => $classSession->end_time,
                'time_value' => $classSession->time,
                'teacher_id' => Auth::id(),
            ]);

            return redirect()->route('teacher.attendance.index')
                ->with('error', $errorMessage);
        }

        do {
            $code = strtoupper(substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 6));
        } while (AttendanceCode::where('code', $code)->where('is_active', true)->exists());

        /** @var \App\Models\User $teacher */
        $teacher = Auth::user();
        $teacherCourse = $teacher->course;
        $teacherYearLevel = $teacher->year_level;

        $attendanceCode = AttendanceCode::create([
            'class_session_id' => $validated['class_session_id'],
            'code' => $code,
            'date' => $validated['date'],
            'expires_at' => $expiresAt,
            'is_active' => true,
            'created_by' => $teacher->id,
        ]);

        // Only get students that match the teacher's course and year level
        $registeredStudents = User::where('role', 'student')
            ->when($teacherCourse && $teacherYearLevel, function ($query) use ($teacherCourse, $teacherYearLevel) {
                $query->where('course', $teacherCourse)
                    ->where('year_level', $teacherYearLevel);
            }, function ($query) {
                // If course or year level not assigned to teacher, return none
                $query->whereRaw('1 = 0');
            })
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('enrollments')
                    ->whereColumn('enrollments.user_id', 'users.id')
                    ->where('enrollments.status', 'approved');
            })
            ->get();

        foreach ($registeredStudents as $student) {
            $existing = SessionEnrollment::where('user_id', $student->id)
                ->where('class_session_id', $classSession->id)
                ->where('session_date', $validated['date'])
                ->first();

            if (! $existing) {
                SessionEnrollment::create([
                    'user_id' => $student->id,
                    'class_session_id' => $classSession->id,
                    'attendance_code_id' => $attendanceCode->id,
                    'session_date' => $validated['date'],
                    'enrolled_at' => now(),
                    'is_active' => true,
                ]);
            } else {
                $existing->update([
                    'attendance_code_id' => $attendanceCode->id,
                    'is_active' => true,
                ]);
            }
        }

        foreach ($registeredStudents as $student) {
            StudentNotification::create([
                'user_id' => $student->id,
                'attendance_code_id' => $attendanceCode->id,
                'type' => 'attendance_code',
                'title' => 'New Attendance Code: '.$code,
                'message' => "Attendance code for {$classSession->name} ({$classSession->time_range}) on {$sessionDate->format('M d, Y')}. Code expires at {$expiresAt->format('g:i A')}.",
            ]);

            if ($student->email) {
                try {
                    Mail::to($student->email)->send(new AttendanceCodeMail($attendanceCode));
                } catch (\Exception $e) {
                    Log::error('Failed to send attendance code email to '.$student->email.': '.$e->getMessage());
                }
            }
        }

        return redirect()->route('teacher.attendance.index')
            ->with('success', "Attendance code generated: {$code}.");
    }

    public function deactivateCode(int $id): RedirectResponse
    {
        $this->checkTeacher();

        /** @var \App\Models\User $teacher */
        $teacher = Auth::user();

        $code = AttendanceCode::where('id', $id)
            ->where('created_by', $teacher->id)
            ->firstOrFail();

        $code->update(['is_active' => false]);

        return redirect()->route('teacher.attendance.index')
            ->with('success', 'Attendance code deactivated successfully.');
    }
}
