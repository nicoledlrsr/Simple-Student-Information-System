<?php

namespace App\Console\Commands;

use App\Models\AttendanceCode;
use App\Models\ClassSession;
use App\Models\SessionEnrollment;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ManageClassSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sessions:manage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically manage class sessions: activate, enroll students, deactivate, and remove enrollments';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $now = now();
        $today = $now->toDateString();
        $currentTime = $now->format('H:i:s');

        // Automatically deactivate all expired attendance codes
        $expiredCodes = AttendanceCode::where('is_active', true)
            ->where('expires_at', '<=', $now)
            ->get();

        if ($expiredCodes->isNotEmpty()) {
            $deactivatedCount = AttendanceCode::where('is_active', true)
                ->where('expires_at', '<=', $now)
                ->update(['is_active' => false]);

            if ($deactivatedCount > 0) {
                $this->info("Automatically deactivated {$deactivatedCount} expired attendance code(s).");
            }
        }

        // Get all active class sessions
        $sessions = ClassSession::where('is_active', true)->get();

        foreach ($sessions as $session) {
            $startTime = $session->start_time;
            $endTime = $session->end_time;

            // Check if session should be active now
            $isSessionActive = $currentTime >= $startTime && $currentTime < $endTime;
            $hasSessionStarted = $currentTime >= $startTime;
            $hasSessionEnded = $currentTime >= $endTime;

            // Find or create attendance code for today's session
            $attendanceCode = AttendanceCode::where('class_session_id', $session->id)
                ->whereDate('date', $today)
                ->first();

            if ($hasSessionStarted && ! $hasSessionEnded && ! $attendanceCode) {
                // Session just started - auto-enroll all registered students
                $this->autoEnrollStudents($session, $today);
            } elseif ($hasSessionEnded) {
                // Session has ended - deactivate code and remove enrollments
                if ($attendanceCode && $attendanceCode->is_active) {
                    $attendanceCode->update(['is_active' => false]);
                    $this->info("Deactivated attendance code for session: {$session->name}");
                }

                // Remove all active enrollments for this session
                $removed = SessionEnrollment::where('class_session_id', $session->id)
                    ->where('session_date', $today)
                    ->where('is_active', true)
                    ->update([
                        'is_active' => false,
                        'resigned_at' => now(),
                    ]);

                if ($removed > 0) {
                    $this->info("Removed {$removed} students from session: {$session->name}");
                }
            }
        }

        return Command::SUCCESS;
    }

    private function autoEnrollStudents(ClassSession $session, string $date): void
    {
        // Get all registered students (students with approved enrollment)
        $students = User::where('role', 'student')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('enrollments')
                    ->whereColumn('enrollments.user_id', 'users.id')
                    ->where('enrollments.status', 'approved');
            })
            ->get();

        $enrolled = 0;
        foreach ($students as $student) {
            // Check if already enrolled
            $existing = SessionEnrollment::where('user_id', $student->id)
                ->where('class_session_id', $session->id)
                ->where('session_date', $date)
                ->first();

            if (! $existing) {
                SessionEnrollment::create([
                    'user_id' => $student->id,
                    'class_session_id' => $session->id,
                    'session_date' => $date,
                    'enrolled_at' => now(),
                    'is_active' => true,
                ]);
                $enrolled++;
            }
        }

        if ($enrolled > 0) {
            $this->info("Auto-enrolled {$enrolled} students into session: {$session->name}");
        }
    }
}
