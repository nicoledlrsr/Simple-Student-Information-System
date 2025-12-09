<?php

use App\Http\Controllers\AcademicsController;
use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\ClassSessionController;
use App\Http\Controllers\Admin\GradeManagementController;
use App\Http\Controllers\Admin\StudentManagementController;
use App\Http\Controllers\Admin\SubjectManagementController;
use App\Http\Controllers\Admin\TeacherManagementController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\Student\AttendanceController;
use App\Http\Controllers\Student\InstructorsController;
use App\Http\Controllers\Student\PortalController;
use App\Http\Controllers\Student\RequestController;
use App\Http\Controllers\Student\SettingsController;
use App\Http\Controllers\StudentInfoController;
use App\Http\Middleware\EnsureTeacher;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');

Route::middleware('auth')->group(function () {
    // Handle both GET and POST for logout (GET also logs out and redirects)
    Route::get('/logout', [LoginController::class, 'logout']);
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/student-info/update', [StudentInfoController::class, 'update'])->name('student-info.update');
    Route::get('/enrollment', [EnrollmentController::class, 'show'])->name('enrollment');
    Route::post('/enrollment', [EnrollmentController::class, 'store'])->name('enrollment.submit');
    Route::get('/enrollment/waiting', [EnrollmentController::class, 'waiting'])->name('enrollment.waiting');
    Route::get('/academics', [AcademicsController::class, 'index'])->name('academics');
    Route::get('/instructors', [InstructorsController::class, 'index'])->name('instructors');
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance');
    Route::post('/attendance/submit-code', [AttendanceController::class, 'submitCode'])->name('attendance.submit-code');
    Route::post('/notifications/{id}/read', [AttendanceController::class, 'markNotificationRead'])->name('notifications.read');
    Route::get('/grades', [\App\Http\Controllers\Student\GradesController::class, 'index'])->name('grades');

    // Student Portal with Tabs
    Route::get('/student-portal', [PortalController::class, 'index'])->name('student.portal');

    // Notifications
    Route::get('/notifications', [\App\Http\Controllers\Student\NotificationController::class, 'index'])->name('notifications');
    Route::get('/api/notifications', [\App\Http\Controllers\Student\NotificationController::class, 'fetch'])->name('notifications.fetch');
    Route::post('/api/notifications/{id}/read', [\App\Http\Controllers\Student\NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/api/notifications/read-all', [\App\Http\Controllers\Student\NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');

    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');

    // Shift Requests (replaces Documents)
    Route::get('/documents', [\App\Http\Controllers\Student\RequestController::class, 'index'])->name('documents');
    Route::get('/requests', [\App\Http\Controllers\Student\RequestController::class, 'index'])->name('requests');
    Route::post('/requests', [\App\Http\Controllers\Student\RequestController::class, 'store'])->name('requests.store');

    // Settings CRUD
    Route::post('/settings/account', [SettingsController::class, 'updateAccount'])->name('settings.account');
    Route::post('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile');
    Route::post('/settings/notifications', [SettingsController::class, 'updateNotifications'])->name('settings.notifications');
    Route::post('/settings/interface', [SettingsController::class, 'updateInterface'])->name('settings.interface');

    // Student Request routes
    Route::get('/requests', [RequestController::class, 'index'])->name('requests');
    Route::post('/requests', [RequestController::class, 'store'])->name('requests.store');

    // Messaging
    Route::get('/messages', [\App\Http\Controllers\Student\MessageController::class, 'index'])->name('messages');
    Route::post('/api/messages/send', [\App\Http\Controllers\Student\MessageController::class, 'send'])->name('messages.send');
    Route::get('/api/messages/fetch', [\App\Http\Controllers\Student\MessageController::class, 'fetch'])->name('messages.fetch');
    Route::get('/messages/{message}/download', [\App\Http\Controllers\Student\MessageController::class, 'download'])->name('messages.download');

    // Teacher routes
    Route::prefix('teacher')->name('teacher.')->middleware(EnsureTeacher::class)->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Teacher\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/students', [\App\Http\Controllers\Teacher\StudentController::class, 'index'])->name('students.index');
        Route::get('/subjects', [\App\Http\Controllers\Teacher\SubjectController::class, 'index'])->name('subjects.index');
        Route::resource('grades', \App\Http\Controllers\Teacher\GradeController::class)->only(['index', 'create', 'store', 'edit', 'update']);
        Route::get('/attendance', [\App\Http\Controllers\Teacher\AttendanceController::class, 'index'])->name('attendance.index');
        Route::post('/attendance/generate-code', [\App\Http\Controllers\Teacher\AttendanceController::class, 'generateCode'])->name('attendance.generate-code');
        Route::post('/attendance/deactivate-code/{id}', [\App\Http\Controllers\Teacher\AttendanceController::class, 'deactivateCode'])->name('attendance.deactivate-code');
        Route::get('/messages', [\App\Http\Controllers\Teacher\MessageController::class, 'index'])->name('messages.index');
        Route::post('/messages/send', [\App\Http\Controllers\Teacher\MessageController::class, 'send'])->name('messages.send');
        Route::get('/messages/fetch', [\App\Http\Controllers\Teacher\MessageController::class, 'fetch'])->name('messages.fetch');
        Route::get('/messages/{message}/download', [\App\Http\Controllers\Teacher\MessageController::class, 'download'])->name('teacher.messages.download');
        Route::get('/settings', [\App\Http\Controllers\Teacher\SettingsController::class, 'index'])->name('settings.index');
        Route::post('/settings/profile', [\App\Http\Controllers\Teacher\SettingsController::class, 'updateProfile'])->name('settings.profile');
        Route::post('/settings/password', [\App\Http\Controllers\Teacher\SettingsController::class, 'updatePassword'])->name('settings.password');
    });

    // Admin routes - role check handled in controller
    Route::prefix('admin')->name('admin.')->group(function () {
        // Admin Dashboard
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        // Enrollment Approval
        Route::get('/enrollment', [AdminController::class, 'index'])->name('enrollment.index');
        Route::get('/', [AdminController::class, 'dashboard'])->name('index');
        Route::post('/enrollment/{id}/approve', [AdminController::class, 'approve'])->name('enrollment.approve');
        Route::post('/enrollment/{id}/reject', [AdminController::class, 'reject'])->name('enrollment.reject');
        Route::get('/enrollment/{id}', [AdminController::class, 'view'])->name('enrollment.view');

        // Student Management (Full CRUD)
        Route::resource('students', StudentManagementController::class);
        Route::post('/students/{student}/archive', [StudentManagementController::class, 'archive'])->name('students.archive');
        Route::patch('/students/{id}/restore', [StudentManagementController::class, 'restore'])->name('students.restore');

        // Teacher Management (Full CRUD)
        Route::resource('teachers', TeacherManagementController::class);

        // Subject Management (Full CRUD)
        Route::resource('subjects', SubjectManagementController::class);

        // Grade Management (Full CRUD + Approval)
        Route::resource('grades', GradeManagementController::class);
        Route::post('grades/{grade}/approve', [GradeManagementController::class, 'approve'])
            ->name('grades.approve');

        // Class Sessions Management
        Route::resource('class-sessions', ClassSessionController::class);

        // Attendance Control
        Route::get('/attendance', [AdminController::class, 'attendance'])->name('attendance.index');
        Route::post('/attendance/generate-code', [AdminController::class, 'generateCode'])->name('attendance.generate-code');
        Route::post('/attendance/deactivate-code/{id}', [AdminController::class, 'deactivateCode'])->name('attendance.deactivate-code');

        // Announcements (Full CRUD)
        Route::resource('announcements', AnnouncementController::class);

        // Student Requests
        Route::get('/requests', [AdminController::class, 'requests'])->name('requests.index');
        Route::post('/requests/{id}/approve', [AdminController::class, 'approveRequest'])->name('requests.approve');
        Route::post('/requests/{id}/reject', [AdminController::class, 'rejectRequest'])->name('requests.reject');

        // Archive / Restore
        Route::get('/archive', [AdminController::class, 'archive'])->name('archive.index');

        // Settings
        Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
        Route::post('/settings/profile', [AdminController::class, 'updateProfile'])->name('settings.profile');
        Route::post('/settings/password', [AdminController::class, 'updatePassword'])->name('settings.password');
        Route::post('/settings/notifications', [AdminController::class, 'updateNotifications'])->name('settings.notifications');
        Route::post('/settings/preferences', [AdminController::class, 'updatePreferences'])->name('settings.preferences');

        // Student ID Management
        Route::get('/student-ids', [\App\Http\Controllers\Admin\StudentIdController::class, 'index'])->name('student-ids.index');
        Route::post('/student-ids', [\App\Http\Controllers\Admin\StudentIdController::class, 'store'])->name('student-ids.store');
        Route::delete('/student-ids/{id}', [\App\Http\Controllers\Admin\StudentIdController::class, 'destroy'])->name('student-ids.destroy');

        // Messaging
        Route::get('/messages', [\App\Http\Controllers\Admin\MessageController::class, 'index'])->name('messages.index');
        Route::post('/messages/conversation', [\App\Http\Controllers\Admin\MessageController::class, 'conversation'])->name('messages.conversation');
        Route::post('/api/messages/send/{course}', [\App\Http\Controllers\Admin\MessageController::class, 'send'])->name('messages.send');
        Route::get('/api/messages/fetch/{course}', [\App\Http\Controllers\Admin\MessageController::class, 'fetch'])->name('messages.fetch');
    });
});
