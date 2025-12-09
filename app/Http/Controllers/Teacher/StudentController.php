<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
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

        $students = User::where('role', 'student')
            ->when($teacherCourse && $teacherYearLevel, function ($query) use ($teacherCourse, $teacherYearLevel) {
                $query->where('course', $teacherCourse)
                    ->where('year_level', $teacherYearLevel);
            }, function ($query) {
                // If course or year level not assigned to teacher, return none
                $query->whereRaw('1 = 0');
            })
            ->with(['section', 'documents'])
            ->orderBy('name')
            ->paginate(20);

        return view('teacher.students.index', [
            'students' => $students,
            'teacherCourse' => $teacherCourse,
            'teacherYearLevel' => $teacherYearLevel,
        ]);
    }
}
