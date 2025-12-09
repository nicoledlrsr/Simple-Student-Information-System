@extends('layouts.app')

@section('content')
    @include('layouts.admin-sidebar')

    <div class="admin-container">
        <div style="background: #FFFFFF; border-radius: 16px; padding: 32px; box-shadow: 0 4px 16px rgba(0,0,0,0.1); max-width: 700px;">
            <h1 style="color: #1C6EA4; margin-bottom: 24px;">Add New Subject</h1>

            <form method="POST" action="{{ route('admin.subjects.store') }}">
                @csrf

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div>
                        <label style="display: block; margin-bottom: 6px; color: #374151; font-weight: 500;">Subject Code *</label>
                        <input type="text" name="code" required value="{{ old('code') }}" style="width: 100%; padding: 10px; border: 1px solid #D1D5DB; border-radius: 6px;">
                        @error('code')
                            <p style="color: #EF4444; font-size: 0.875rem; margin-top: 4px;">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label style="display: block; margin-bottom: 6px; color: #374151; font-weight: 500;">Units *</label>
                        <input type="number" name="units" required min="1" value="{{ old('units') }}" style="width: 100%; padding: 10px; border: 1px solid #D1D5DB; border-radius: 6px;">
                        @error('units')
                            <p style="color: #EF4444; font-size: 0.875rem; margin-top: 4px;">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div style="margin-bottom: 16px;">
                    <label style="display: block; margin-bottom: 6px; color: #374151; font-weight: 500;">Subject Name *</label>
                    <input type="text" name="name" required value="{{ old('name') }}" style="width: 100%; padding: 10px; border: 1px solid #D1D5DB; border-radius: 6px;">
                    @error('name')
                        <p style="color: #EF4444; font-size: 0.875rem; margin-top: 4px;">{{ $message }}</p>
                    @enderror
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div>
                        <label style="display: block; margin-bottom: 6px; color: #374151; font-weight: 500;">Course *</label>
                        <select name="course" required style="width: 100%; padding: 10px; border: 1px solid #D1D5DB; border-radius: 6px;">
                            <option value="">Select Course</option>
                            @foreach($courses as $course)
                                <option value="{{ $course }}" {{ old('course') == $course ? 'selected' : '' }}>{{ $course }}</option>
                            @endforeach
                            <option value="other" {{ old('course') == 'other' ? 'selected' : '' }}>Other (type below)</option>
                        </select>
                        @error('course')
                            <p style="color: #EF4444; font-size: 0.875rem; margin-top: 4px;">{{ $message }}</p>
                        @enderror
                        <div style="margin-top:8px;">
                            <input type="text" name="custom_course" value="{{ old('custom_course') }}" placeholder="Custom course/program" style="width: 100%; padding: 10px; border: 1px solid #D1D5DB; border-radius: 6px;">
                        </div>
                    </div>

                    <div>
                        <label style="display: block; margin-bottom: 6px; color: #374151; font-weight: 500;">Year Level *</label>
                        <select name="year_level" required style="width: 100%; padding: 10px; border: 1px solid #D1D5DB; border-radius: 6px;">
                            <option value="">Select Year Level</option>
                            <option value="1st Year" {{ old('year_level') == '1st Year' ? 'selected' : '' }}>1st Year</option>
                            <option value="2nd Year" {{ old('year_level') == '2nd Year' ? 'selected' : '' }}>2nd Year</option>
                            <option value="3rd Year" {{ old('year_level') == '3rd Year' ? 'selected' : '' }}>3rd Year</option>
                            <option value="4th Year" {{ old('year_level') == '4th Year' ? 'selected' : '' }}>4th Year</option>
                        </select>
                        @error('year_level')
                            <p style="color: #EF4444; font-size: 0.875rem; margin-top: 4px;">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div>
                        <label style="display: block; margin-bottom: 6px; color: #374151; font-weight: 500;">Semester</label>
                        <input type="text" name="semester" value="{{ old('semester') }}" style="width: 100%; padding: 10px; border: 1px solid #D1D5DB; border-radius: 6px;">
                    </div>

                    <div>
                        <label style="display: block; margin-bottom: 6px; color: #374151; font-weight: 500;">Hours Per Week</label>
                        <input type="number" name="hours_per_week" min="1" value="{{ old('hours_per_week') }}" style="width: 100%; padding: 10px; border: 1px solid #D1D5DB; border-radius: 6px;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div>
                        <label style="display: block; margin-bottom: 6px; color: #374151; font-weight: 500;">Schedule (Day/s)</label>
                        <input type="text" name="schedule" value="{{ old('schedule') }}" placeholder="e.g., MWF" style="width: 100%; padding: 10px; border: 1px solid #D1D5DB; border-radius: 6px;">
                        @error('schedule')
                            <p style="color: #EF4444; font-size: 0.875rem; margin-top: 4px;">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label style="display: block; margin-bottom: 6px; color: #374151; font-weight: 500;">Time</label>
                        <input type="text" name="time" value="{{ old('time') }}" placeholder="e.g., 8:00 AM - 9:30 AM" style="width: 100%; padding: 10px; border: 1px solid #D1D5DB; border-radius: 6px;">
                        @error('time')
                            <p style="color: #EF4444; font-size: 0.875rem; margin-top: 4px;">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div style="margin-bottom: 16px;">
                    <label style="display: block; margin-bottom: 6px; color: #374151; font-weight: 500;">Instructor</label>
                    <select name="instructor_id" style="width: 100%; padding: 10px; border: 1px solid #D1D5DB; border-radius: 6px;">
                        <option value="">Select Instructor</option>
                        @foreach($instructors as $instructor)
                            <option value="{{ $instructor->id }}" {{ old('instructor_id') == $instructor->id ? 'selected' : '' }}>{{ $instructor->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="margin-bottom: 24px;">
                    <label style="display: block; margin-bottom: 6px; color: #374151; font-weight: 500;">Description</label>
                    <textarea name="description" rows="3" style="width: 100%; padding: 10px; border: 1px solid #D1D5DB; border-radius: 6px;">{{ old('description') }}</textarea>
                </div>

                <div style="display: flex; gap: 12px;">
                    <a href="{{ route('admin.subjects.index') }}" style="padding: 10px 20px; background: #6B7280; color: white; text-decoration: none; border-radius: 6px;">Cancel</a>
                    <button type="submit" style="padding: 10px 20px; background: #1C6EA4; color: white; border: none; border-radius: 6px; cursor: pointer;">Create Subject</button>
                </div>
            </form>
        </div>
    </div>

    <style>
        @media (max-width: 768px) {
            .admin-container {
                padding-top: 70px;
            }
            div[style*="grid-template-columns: 1fr 1fr"] {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
@endsection

