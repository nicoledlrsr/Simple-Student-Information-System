<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class SubjectManagementController extends Controller
{
    private function checkAdmin(): void
    {
        if (! Auth::check() || Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized access.');
        }
    }

    public function index(): View
    {
        $this->checkAdmin();

        $subjects = Subject::with('instructor')
            ->orderBy('course')
            ->orderBy('year_level')
            ->paginate(20);

        $instructors = User::where('role', 'teacher')->orderBy('name')->get();
        $courses = $this->availableCourses();

        return view('admin.subjects.index', [
            'subjects' => $subjects,
            'instructors' => $instructors,
            'courses' => $courses,
        ]);
    }

    public function create(): View
    {
        $this->checkAdmin();
        $instructors = User::where('role', 'teacher')->orderBy('name')->get();
        $courses = $this->availableCourses();

        return view('admin.subjects.create', [
            'instructors' => $instructors,
            'courses' => $courses,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->checkAdmin();

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:255', 'unique:subjects,subject_code'],
            'name' => ['required', 'string', 'max:255'],
            'course' => ['required', 'string', 'max:255'],
            'custom_course' => ['nullable', 'string', 'max:255'],
            'year_level' => ['required', 'string', 'max:255'],
            'semester' => ['nullable', 'string', 'max:255'],
            'units' => ['required', 'integer', 'min:1'],
            'hours_per_week' => ['nullable', 'integer'],
            'schedule' => ['nullable', 'string', 'max:255'],
            'time' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'instructor_id' => ['nullable', 'exists:users,id'],
        ]);

        $course = $validated['course'] === 'other'
            ? ($validated['custom_course'] ?? null)
            : $validated['course'];

        if (! $course) {
            return back()->withErrors(['course' => 'Please select a course or enter a custom course.'])->withInput();
        }

        Subject::create([
            'subject_code' => $validated['code'],
            'subject_name' => $validated['name'],
            'course' => $course,
            'year_level' => $validated['year_level'],
            'semester' => $validated['semester'] ?? null,
            'units' => $validated['units'],
            'hours_per_week' => $validated['hours_per_week'] ?? null,
            'schedule' => $validated['schedule'] ?? null,
            'time' => $validated['time'] ?? null,
            'description' => $validated['description'] ?? null,
            'instructor_id' => $validated['instructor_id'] ?? null,
        ]);

        return redirect()->route('admin.subjects.index')->with('success', 'Subject created successfully.');
    }

    public function edit(Subject $subject): View
    {
        $this->checkAdmin();
        $instructors = User::where('role', 'teacher')->orderBy('name')->get();
        $courses = $this->availableCourses();

        return view('admin.subjects.edit', [
            'subject' => $subject,
            'instructors' => $instructors,
            'courses' => $courses,
        ]);
    }

    public function update(Request $request, Subject $subject): RedirectResponse
    {
        $this->checkAdmin();

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:255', 'unique:subjects,subject_code,'.$subject->id],
            'name' => ['required', 'string', 'max:255'],
            'course' => ['required', 'string', 'max:255'],
            'custom_course' => ['nullable', 'string', 'max:255'],
            'year_level' => ['required', 'string', 'max:255'],
            'semester' => ['nullable', 'string', 'max:255'],
            'units' => ['required', 'integer', 'min:1'],
            'hours_per_week' => ['nullable', 'integer'],
            'schedule' => ['nullable', 'string', 'max:255'],
            'time' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'instructor_id' => ['nullable', 'exists:users,id'],
        ]);

        $course = $validated['course'] === 'other'
            ? ($validated['custom_course'] ?? null)
            : $validated['course'];

        if (! $course) {
            return back()->withErrors(['course' => 'Please select a course or enter a custom course.'])->withInput();
        }

        $subject->update([
            'subject_code' => $validated['code'],
            'subject_name' => $validated['name'],
            'course' => $course,
            'year_level' => $validated['year_level'],
            'semester' => $validated['semester'] ?? null,
            'units' => $validated['units'],
            'hours_per_week' => $validated['hours_per_week'] ?? null,
            'schedule' => $validated['schedule'] ?? null,
            'time' => $validated['time'] ?? null,
            'description' => $validated['description'] ?? null,
            'instructor_id' => $validated['instructor_id'] ?? null,
        ]);

        return redirect()->route('admin.subjects.index')->with('success', 'Subject updated successfully.');
    }

    public function destroy(Subject $subject): RedirectResponse
    {
        $this->checkAdmin();
        $subject->delete();

        return redirect()->route('admin.subjects.index')->with('success', 'Subject deleted successfully.');
    }

    private function availableCourses(): Collection
    {
        $fromSubjects = Subject::query()
            ->whereNotNull('course')
            ->where('course', '!=', '')
            ->distinct()
            ->pluck('course');

        $fromUsers = User::query()
            ->whereNotNull('course')
            ->where('course', '!=', '')
            ->distinct()
            ->pluck('course');

        return $fromSubjects
            ->merge($fromUsers)
            ->filter()
            ->unique()
            ->sort()
            ->values();
    }
}
