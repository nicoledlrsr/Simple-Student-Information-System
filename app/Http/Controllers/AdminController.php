<?php

namespace App\Http\Controllers;

use App\Mail\AttendanceCodeMail;
use App\Models\Attendance;
use App\Models\AttendanceCode;
use App\Models\ClassSession;
use App\Models\Enrollment;
use App\Models\SchoolEvent;
use App\Models\Section;
use App\Models\SessionEnrollment;
use App\Models\StudentNotification;
use App\Models\StudentPreference;
use App\Models\StudentRequest;
use App\Models\User;
use App\Services\SectionAssignmentService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    private function checkAdmin(): void
    {
        if (! Auth::check() || Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized access.');
        }
    }

    public function dashboard(): View
    {
        $this->checkAdmin();

        // Key metrics - enrollment summary
        $totalEnrolledStudents = User::where('role', 'student')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('enrollments')
                    ->whereColumn('enrollments.user_id', 'users.id')
                    ->where('enrollments.status', 'approved');
            })
            ->count();

        $approvedEnrollments = Enrollment::where('status', 'approved')->count();
        $pendingEnrollments = Enrollment::where('status', 'pending')->count();
        $rejectedEnrollments = Enrollment::where('status', 'rejected')->count();
        $totalEnrollments = Enrollment::count();
        $totalTeachers = User::where('role', 'teacher')->count();

        // Calendar data (matching student dashboard behavior)
        $requestMonth = request()->input('month');
        $requestYear = request()->input('year');

        $selectedDate = $requestMonth && $requestYear
            ? Carbon::create((int) $requestYear, (int) $requestMonth, 1)
            : Carbon::now();

        $currentMonth = (int) $selectedDate->month;
        $currentYear = (int) $selectedDate->year;

        $events = SchoolEvent::query()
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->orderBy('date')
            ->get();

        $eventsByDate = $events->groupBy(function (SchoolEvent $event): string {
            $eventDate = $event->date instanceof Carbon ? $event->date : Carbon::parse($event->date);

            return $eventDate->format('Y-m-d');
        });

        $prevMonth = $selectedDate->copy()->subMonth();
        $nextMonth = $selectedDate->copy()->addMonth();

        return view('admin.dashboard', [
            'totalEnrolledStudents' => $totalEnrolledStudents,
            'approvedEnrollments' => $approvedEnrollments,
            'pendingEnrollments' => $pendingEnrollments,
            'rejectedEnrollments' => $rejectedEnrollments,
            'totalEnrollments' => $totalEnrollments,
            'totalTeachers' => $totalTeachers,
            'eventsByDate' => $eventsByDate,
            'currentMonth' => $currentMonth,
            'currentYear' => $currentYear,
            'selectedDate' => $selectedDate,
            'prevMonth' => $prevMonth,
            'nextMonth' => $nextMonth,
        ]);
    }

    public function index(): View
    {
        $this->checkAdmin();

        // Sync approved enrollments with user profiles (fix existing data)
        $this->syncApprovedEnrollments();

        $enrollments = Enrollment::query()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        // Enrollment statistics for graph
        $enrollmentStats = [
            'pending' => Enrollment::where('status', 'pending')->count(),
            'approved' => Enrollment::where('status', 'approved')->count(),
            'rejected' => Enrollment::where('status', 'rejected')->count(),
            'total' => Enrollment::count(),
        ];

        // Get sections for dropdown
        $sections = Section::all();

        return view('admin.enrollment-approval', [
            'enrollments' => $enrollments,
            'enrollmentStats' => $enrollmentStats,
            'sections' => $sections,
        ]);
    }

    /**
     * Sync approved enrollments with user profiles
     * This ensures that students with approved enrollments have their course, year_level, and section updated
     */
    private function syncApprovedEnrollments(): void
    {
        $approvedEnrollments = Enrollment::where('status', 'approved')
            ->with('user')
            ->get();

        $sectionService = new SectionAssignmentService;

        foreach ($approvedEnrollments as $enrollment) {
            if (! $enrollment->user) {
                continue;
            }

            $updateData = [];

            // Always update course and year_level from enrollment if they exist
            if (! empty($enrollment->course_selected)) {
                $updateData['course'] = $enrollment->course_selected;
            }
            if (! empty($enrollment->year_level)) {
                $updateData['year_level'] = $enrollment->year_level;
            }

            // Update user if needed
            if (! empty($updateData)) {
                $enrollment->user->update($updateData);
            }

            // Assign section if user doesn't have one and enrollment has course/year_level
            if (empty($enrollment->user->section_id) && ! empty($enrollment->course_selected) && ! empty($enrollment->year_level)) {
                $sectionService->assignSection(
                    $enrollment->user,
                    $enrollment->course_selected,
                    $enrollment->year_level,
                    $enrollment->semester ?? null,
                    $enrollment->academic_year ?? null
                );
            }
        }
    }

    public function approve(Request $request, int $id): RedirectResponse
    {
        $this->checkAdmin();

        $validated = $request->validate([
            'section_id' => ['nullable', 'exists:sections,id'],
        ]);

        $enrollment = Enrollment::query()->with('user')->findOrFail($id);

        // Update enrollment status
        $enrollment->update([
            'status' => 'approved',
            'remarks' => 'Enrollment approved by admin.',
        ]);

        // Update user profile with enrollment data
        // Always use enrollment data when approving (enrollment data takes priority)
        $updateData = [
            'name' => $enrollment->full_name ?? $enrollment->user->name,
            'address' => $enrollment->address ?? $enrollment->user->address,
            'email' => $enrollment->email ?? $enrollment->user->email,
            'birthday' => $enrollment->birthday ?? $enrollment->user->birthday,
            'gender' => $enrollment->gender ?? $enrollment->user->gender,
            'guardian_name' => $enrollment->guardian_name ?? $enrollment->user->guardian_name,
            'guardian_contact' => $enrollment->guardian_contact ?? $enrollment->user->guardian_contact,
        ];

        // Always update course and year_level from enrollment if they exist
        if (! empty($enrollment->course_selected)) {
            $updateData['course'] = $enrollment->course_selected;
        }
        if (! empty($enrollment->year_level)) {
            $updateData['year_level'] = $enrollment->year_level;
        }

        $enrollment->user->update($updateData);

        // Automatically assign section if not manually specified
        if (empty($validated['section_id'])) {
            $sectionService = new SectionAssignmentService;
            $section = $sectionService->assignSection(
                $enrollment->user,
                $enrollment->course_selected,
                $enrollment->year_level,
                $enrollment->semester ?? null,
                $enrollment->academic_year ?? null
            );
        } else {
            // Manual section assignment
            $enrollment->user->update(['section_id' => $validated['section_id']]);
        }

        // Refresh user to get latest section relationship
        $enrollment->user->refresh();

        // Send welcome notification to student
        $assignedSection = $enrollment->user->section;
        $sectionMessage = $assignedSection ? " You have been assigned to section {$assignedSection->name}." : '';

        StudentNotification::create([
            'user_id' => $enrollment->user->id,
            'type' => 'enrollment',
            'title' => 'Enrollment Approved ✅',
            'message' => 'You have been successfully enrolled. Access to Attendance and Subjects is now enabled.'.$sectionMessage,
        ]);

        $successMessage = 'Enrollment approved successfully. Student profile updated.';
        if ($assignedSection) {
            $successMessage .= " Student assigned to section {$assignedSection->name}.";
        }

        return redirect()->route('admin.enrollment.index')->with('success', $successMessage);
    }

    public function reject(Request $request, int $id): RedirectResponse
    {
        $this->checkAdmin();

        $enrollment = Enrollment::query()->findOrFail($id);
        $enrollment->update([
            'status' => 'rejected',
            'remarks' => $request->input('remarks', 'Enrollment rejected by admin.'),
        ]);

        return redirect()->route('admin.enrollment.index')->with('success', 'Enrollment rejected.');
    }

    public function view(int $id): View
    {
        $this->checkAdmin();

        $enrollment = Enrollment::query()->with('user')->findOrFail($id);

        return view('admin.enrollment-details', [
            'enrollment' => $enrollment,
        ]);
    }

    public function attendance(): View
    {
        $this->checkAdmin();

        $attendances = Attendance::with(['user', 'subject', 'attendanceCode', 'attendanceCode.classSession'])
            ->orderBy('date', 'desc')
            ->orderBy('time_in', 'desc')
            ->paginate(20);

        $classSessions = ClassSession::where('is_active', true)->orderBy('start_time')->get();
        // Only show active, non-expired codes in recent codes (exclude deactivated codes)
        $recentCodes = AttendanceCode::with(['classSession', 'creator'])
            ->where('is_active', true)
            ->where('expires_at', '>', now())
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get all codes for the data table (including deactivated ones)
        $allCodes = AttendanceCode::with(['classSession', 'creator'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.attendance.index', [
            'attendances' => $attendances,
            'classSessions' => $classSessions,
            'recentCodes' => $recentCodes,
            'allCodes' => $allCodes,
        ]);
    }

    public function generateCode(Request $request): RedirectResponse
    {
        $this->checkAdmin();

        $validated = $request->validate([
            'class_session_id' => ['required', 'exists:class_sessions,id'],
            'date' => ['required', 'date'],
        ]);

        $classSession = ClassSession::findOrFail($validated['class_session_id']);

        // Calculate expiration time based on session end time
        $sessionDate = \Carbon\Carbon::parse($validated['date'])->startOfDay();
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
            $timeString = $classSession->time;
            // Try to extract end time from string like "8:00 AM - 9:30 AM" or "8:00AM-9:30AM"
            if (preg_match('/(\d{1,2}):(\d{2})\s*(AM|PM)\s*-\s*(\d{1,2}):(\d{2})\s*(AM|PM)/i', $timeString, $matches)) {
                $endHour = (int) $matches[4];
                $endMinute = (int) $matches[5];
                $endPeriod = strtoupper($matches[6]);

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

        // If we still don't have a valid expiration time, return error
        if (! $expiresAt) {
            return redirect()->route('admin.attendance.index')
                ->with('error', 'Unable to determine class end time. Please ensure the class session has a valid time configuration.');
        }

        // Generate unique 6-character code
        do {
            $code = strtoupper(substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 6));
        } while (AttendanceCode::where('code', $code)->where('is_active', true)->exists());

        // Create attendance code
        $attendanceCode = AttendanceCode::create([
            'class_session_id' => $validated['class_session_id'],
            'code' => $code,
            'date' => $validated['date'],
            'expires_at' => $expiresAt,
            'is_active' => true,
            'created_by' => Auth::id(),
        ]);

        // Auto-enroll all registered students (students with approved enrollment) into this session
        $registeredStudents = User::where('role', 'student')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('enrollments')
                    ->whereColumn('enrollments.user_id', 'users.id')
                    ->where('enrollments.status', 'approved');
            })
            ->get();

        // Create temporary session enrollments for today
        foreach ($registeredStudents as $student) {
            // Check if already enrolled
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
                // Update existing enrollment with code
                $existing->update([
                    'attendance_code_id' => $attendanceCode->id,
                    'is_active' => true,
                ]);
            }
        }

        // Send notifications and emails to all enrolled students
        foreach ($registeredStudents as $student) {
            // Create in-app notification
            StudentNotification::create([
                'user_id' => $student->id,
                'attendance_code_id' => $attendanceCode->id,
                'type' => 'attendance_code',
                'title' => 'New Attendance Code: '.$code,
                'message' => "Attendance code for {$classSession->name} ({$classSession->time_range}) on {$sessionDate->format('M d, Y')}. Code expires at {$expiresAt->format('g:i A')}.",
            ]);

            // Send email notification
            if ($student->email) {
                try {
                    Mail::to($student->email)->send(new AttendanceCodeMail($attendanceCode));
                } catch (\Exception $e) {
                    // Log error but don't fail the entire process
                    Log::error('Failed to send attendance code email to '.$student->email.': '.$e->getMessage());
                }
            }
        }

        return redirect()->route('admin.attendance.index')
            ->with('success', "Attendance code generated: {$code}. Notifications sent to {$registeredStudents->count()} students.");
    }

    public function deactivateCode(int $id): RedirectResponse
    {
        $this->checkAdmin();

        $code = AttendanceCode::findOrFail($id);
        $code->update(['is_active' => false]);

        return redirect()->route('admin.attendance.index')
            ->with('success', 'Attendance code deactivated successfully.');
    }

    public function requests(): View
    {
        $this->checkAdmin();

        $requests = StudentRequest::with(['user', 'processedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.requests.index', [
            'requests' => $requests,
        ]);
    }

    public function approveRequest(Request $httpRequest, int $id): RedirectResponse
    {
        $this->checkAdmin();

        $validated = $httpRequest->validate([
            'remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        $request = StudentRequest::findOrFail($id);

        // If it's a shift request, update the student's course
        if ($request->request_type === 'shift' && $request->target_course) {
            $student = $request->user;
            $student->update([
                'course' => $request->target_course,
                // Clear section when shifting courses
                'section_id' => null,
            ]);
        }

        $request->update([
            'status' => 'approved',
            'admin_remarks' => $validated['remarks'] ?? null,
            'processed_by' => Auth::id(),
            'processed_at' => now(),
        ]);

        return redirect()->route('admin.requests.index')->with('success', 'Request approved successfully.');
    }

    public function rejectRequest(Request $httpRequest, int $id): RedirectResponse
    {
        $this->checkAdmin();

        $validated = $httpRequest->validate([
            'remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        $request = StudentRequest::findOrFail($id);
        $request->update([
            'status' => 'rejected',
            'admin_remarks' => $validated['remarks'] ?? 'Request rejected by admin.',
            'processed_by' => Auth::id(),
            'processed_at' => now(),
        ]);

        return redirect()->route('admin.requests.index')->with('success', 'Request rejected.');
    }

    public function archive(): View
    {
        $this->checkAdmin();

        $archivedStudents = User::onlyTrashed()
            ->where('role', 'student')
            ->with('section')
            ->paginate(20);

        return view('admin.archive.index', [
            'archivedStudents' => $archivedStudents,
        ]);
    }

    public function settings(): View
    {
        $this->checkAdmin();

        /** @var User $user */
        $user = Auth::user();
        $preference = $user->preference ?? StudentPreference::create([
            'user_id' => $user->id,
            'theme' => 'light',
            'language' => 'en',
            'sidebar_mode' => 'expanded',
        ]);

        return view('admin.settings', [
            'user' => $user,
            'preference' => $preference,
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $this->checkAdmin();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.Auth::id()],
            'contact_number' => ['nullable', 'string', 'max:255'],
            'profile_image' => ['nullable', 'image', 'max:2048'],
        ]);

        /** @var User $user */
        $user = Auth::user();
        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'contact_number' => $validated['contact_number'] ?? $user->contact_number,
        ];

        if ($request->hasFile('profile_image')) {
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }
            $updateData['profile_image'] = $request->file('profile_image')->store('profiles/'.$user->id, 'public');
        }

        $user->update($updateData);

        return redirect()->route('admin.settings')->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $this->checkAdmin();

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        /** @var User $user */
        $user = Auth::user();

        if (! Hash::check($validated['current_password'], $user->password)) {
            return redirect()->route('admin.settings')->with('error', 'Current password is incorrect.');
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('admin.settings')->with('success', 'Password updated successfully.');
    }

    public function updateNotifications(Request $request): RedirectResponse
    {
        $this->checkAdmin();

        // For now, just return success - can be extended to save preferences to database
        return redirect()->route('admin.settings')->with('success', 'Notification preferences saved successfully.');
    }

    public function updatePreferences(Request $request): RedirectResponse
    {
        $this->checkAdmin();

        $validated = $request->validate([
            'theme' => ['required', 'string', 'in:light,dark,auto'],
            'language' => ['required', 'string', 'in:en,tl'],
            'items_per_page' => ['nullable', 'integer', 'in:10,25,50,100'],
            'date_format' => ['nullable', 'string'],
            'time_format' => ['nullable', 'string', 'in:12,24'],
            'show_statistics' => ['nullable', 'boolean'],
            'show_recent_activity' => ['nullable', 'boolean'],
            'auto_refresh' => ['nullable', 'boolean'],
        ]);

        /** @var User $user */
        $user = Auth::user();
        $preference = $user->preference ?? StudentPreference::create([
            'user_id' => $user->id,
            'theme' => 'light',
            'language' => 'en',
        ]);

        $preference->update([
            'theme' => $validated['theme'],
            'language' => $validated['language'],
        ]);

        // Store other preferences in notifications JSON field for now
        $otherPrefs = [
            'items_per_page' => $validated['items_per_page'] ?? null,
            'date_format' => $validated['date_format'] ?? null,
            'time_format' => $validated['time_format'] ?? null,
            'show_statistics' => isset($validated['show_statistics']),
            'show_recent_activity' => isset($validated['show_recent_activity']),
            'auto_refresh' => isset($validated['auto_refresh']),
        ];

        $notifications = $preference->notifications ?? [];
        $notifications = array_merge($notifications, ['preferences' => $otherPrefs]);
        $preference->update(['notifications' => $notifications]);

        return redirect()->route('admin.settings')->with('success', 'Preferences saved successfully.');
    }
}
