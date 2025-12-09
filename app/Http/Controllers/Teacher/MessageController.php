<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MessageController extends Controller
{
    private function checkTeacher(): void
    {
        if (! Auth::check() || Auth::user()->role !== 'teacher') {
            abort(403, 'Unauthorized access.');
        }
    }

    public function index(Request $request): View
    {
        $this->checkTeacher();

        $selectedStudentId = $request->query('student');
        $teacher = Auth::user();
        $students = User::where('role', 'student')
            ->where('course', $teacher->course)
            ->when($teacher->year_level, fn ($q) => $q->where('year_level', $teacher->year_level))
            ->orderBy('name')
            ->get();

        return view('teacher.messages.index', [
            'students' => $students,
            'selectedStudentId' => $selectedStudentId,
        ]);
    }

    public function fetch(Request $request): JsonResponse
    {
        $this->checkTeacher();

        $studentId = $request->query('student_id');
        if (! $studentId) {
            return response()->json(['messages' => []]);
        }

        $teacher = Auth::user();

        $student = User::where('id', $studentId)
            ->where('role', 'student')
            ->where('course', $teacher->course)
            ->when($teacher->year_level, fn ($q) => $q->where('year_level', $teacher->year_level))
            ->first();

        if (! $student) {
            return response()->json(['messages' => []]);
        }

        $teacherId = $teacher->id;

        $messages = Message::where(function ($query) use ($teacherId, $studentId) {
            $query->where('sender_id', $teacherId)
                ->where('receiver_id', $studentId);
        })->orWhere(function ($query) use ($teacherId, $studentId) {
            $query->where('sender_id', $studentId)
                ->where('receiver_id', $teacherId);
        })
            ->orderBy('created_at')
            ->get()
            ->map(function ($message) {
                $data = [
                    'id' => $message->id,
                    'sender_id' => $message->sender_id,
                    'receiver_id' => $message->receiver_id,
                    'message' => $message->message,
                    'is_read' => $message->is_read,
                    'created_at' => $message->created_at->toDateTimeString(),
                ];

                // Include file information if present
                if ($message->file_path) {
                    $data['file_path'] = $message->file_path;
                    $data['file_name'] = $message->file_name;
                    $data['file_type'] = $message->file_type;
                    $data['file_size'] = $message->file_size;
                }

                return $data;
            });

        return response()->json([
            'messages' => $messages,
        ]);
    }

    public function send(Request $request): JsonResponse
    {
        $this->checkTeacher();

        $validated = $request->validate([
            'student_id' => ['required', 'exists:users,id'],
            'message' => ['required', 'string'],
        ]);

        $teacher = Auth::user();

        $student = User::where('id', $validated['student_id'])
            ->where('role', 'student')
            ->where('course', $teacher->course)
            ->when($teacher->year_level, fn ($q) => $q->where('year_level', $teacher->year_level))
            ->firstOrFail();

        $teacherId = $teacher->id;

        $message = Message::create([
            'sender_id' => $teacherId,
            'receiver_id' => $student->id,
            'message' => $validated['message'],
            'is_read' => false,
        ]);

        return response()->json([
            'message' => $message,
        ]);
    }

    public function download(Message $message)
    {
        $this->checkTeacher();

        $teacher = Auth::user();

        // Check if teacher is sender or receiver
        if ($message->sender_id !== $teacher->id && $message->receiver_id !== $teacher->id) {
            abort(403, 'Unauthorized access.');
        }

        if (! $message->file_path) {
            abort(404, 'File not found.');
        }

        if (! Storage::disk('public')->exists($message->file_path)) {
            abort(404, 'File not found.');
        }

        $fullPath = Storage::disk('public')->path($message->file_path);

        return response()->download($fullPath, $message->file_name);
    }
}
