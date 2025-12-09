@extends('layouts.app')

@section('content')
    @include('layouts.admin-sidebar')

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: #FFFFFF;
            color: #1F2937;
        }

        .admin-container {
            max-width: 800px;
            margin: 0 auto;
            margin-left: 0;
            padding: 24px;
            padding-top: 80px;
            transition: margin-left 0.3s ease-in-out;
        }

        @media (max-width: 768px) {
            .admin-container {
                padding-top: 70px;
            }
        }

        .admin-header {
            margin-bottom: 32px;
        }

        .admin-header h1 {
            font-size: 2rem;
            color: #0046FF;
            margin-bottom: 8px;
        }

        .admin-header p {
            color: #6B7280;
        }

        .card {
            background: #FFFFFF;
            border-radius: 16px;
            padding: 32px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
            border: 1px solid #E5E7EB;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: #374151;
            margin-bottom: 8px;
        }

        .form-input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #D1D5DB;
            border-radius: 8px;
            font-size: 0.875rem;
            transition: border-color 0.2s;
        }

        .form-input:focus {
            outline: none;
            border-color: #0046FF;
            box-shadow: 0 0 0 3px rgba(0, 70, 255, 0.1);
        }

        .form-textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #D1D5DB;
            border-radius: 8px;
            font-size: 0.875rem;
            min-height: 100px;
            resize: vertical;
            font-family: inherit;
        }

        .form-textarea:focus {
            outline: none;
            border-color: #0046FF;
            box-shadow: 0 0 0 3px rgba(0, 70, 255, 0.1);
        }

        .form-checkbox {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: #0046FF;
            color: #FFFFFF;
        }

        .btn-primary:hover {
            background: #0033CC;
        }

        .btn-secondary {
            background: #6B7280;
            color: #FFFFFF;
        }

        .btn-secondary:hover {
            background: #4B5563;
        }

        .form-actions {
            display: flex;
            gap: 12px;
            margin-top: 24px;
        }

        .error-message {
            color: #EF4444;
            font-size: 0.75rem;
            margin-top: 4px;
        }
    </style>

    <div class="admin-container">
        <div class="admin-header">
            <h1>Edit Class Session</h1>
            <p>Update class schedule entry</p>
        </div>

        <div class="card">
            <form method="POST" action="{{ route('admin.class-sessions.update', $session->id) }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label class="form-label" for="course">Course *</label>
                    <select name="course" id="course" class="form-input" required>
                        <option value="">Select Course</option>
                        @foreach($courses as $courseOption)
                            <option value="{{ $courseOption }}" {{ old('course', $session->course) === $courseOption ? 'selected' : '' }}>
                                {{ $courseOption }}
                            </option>
                        @endforeach
                    </select>
                    @error('course')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="code">Code *</label>
                    <select name="code" id="code" class="form-input" required>
                        <option value="">Select Code</option>
                        @foreach($subjects as $subjectOption)
                            <option value="{{ $subjectOption->subject_code }}" 
                                data-course="{{ $subjectOption->course }}"
                                data-subject-name="{{ $subjectOption->subject_name }}"
                                {{ old('code', $session->course_id) === $subjectOption->subject_code ? 'selected' : '' }}>
                                {{ $subjectOption->subject_code }}
                            </option>
                        @endforeach
                    </select>
                    @error('code')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="subject">Subject *</label>
                    <select name="subject" id="subject" class="form-input" required>
                        <option value="">Select Subject</option>
                        @foreach($subjects as $subjectOption)
                            <option value="{{ $subjectOption->subject_name }}" 
                                data-course="{{ $subjectOption->course }}"
                                data-code="{{ $subjectOption->subject_code }}"
                                data-schedule="{{ $subjectOption->schedule ?? '' }}"
                                data-time="{{ $subjectOption->time ?? '' }}"
                                {{ old('subject', $session->subject) === $subjectOption->subject_name ? 'selected' : '' }}>
                                {{ $subjectOption->subject_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('subject')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="schedule">Schedule (Day/s) *</label>
                    <input type="text" name="schedule" id="schedule" class="form-input" value="{{ old('schedule', $session->schedule) }}" required>
                    @error('schedule')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="time">Time *</label>
                    <input type="text" name="time" id="time" class="form-input" value="{{ old('time', $session->time) }}" required>
                    @error('time')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="instructor">Instructor *</label>
                    <select name="instructor" id="instructor" class="form-input" required>
                        <option value="">Select Instructor</option>
                        @foreach($instructors as $instructor)
                            <option value="{{ $instructor->name }}" {{ old('instructor', $session->instructor) === $instructor->name ? 'selected' : '' }}>
                                {{ $instructor->name }} @if($instructor->position)({{ $instructor->position }})@endif
                            </option>
                        @endforeach
                    </select>
                    @error('instructor')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="room">Room *</label>
                    <input type="text" name="room" id="room" class="form-input" value="{{ old('room', $session->room) }}" required>
                    @error('room')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="description">Description</label>
                    <textarea name="description" id="description" class="form-textarea">{{ old('description', $session->description) }}</textarea>
                    @error('description')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" name="is_active" value="1" class="form-checkbox" {{ old('is_active', $session->is_active) ? 'checked' : '' }}>
                        <span class="form-label" style="margin: 0;">Active Session</span>
                    </label>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Update Session</button>
                    <a href="{{ route('admin.class-sessions.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const courseSelect = document.getElementById('course');
            const codeSelect = document.getElementById('code');
            const subjectSelect = document.getElementById('subject');

            // Filter code and subject dropdowns based on selected course
            function filterDropdowns() {
                const selectedCourse = courseSelect.value;
                
                // Filter code dropdown
                Array.from(codeSelect.options).forEach(option => {
                    if (option.value === '') {
                        option.style.display = 'block';
                    } else {
                        const optionCourse = option.getAttribute('data-course');
                        option.style.display = (selectedCourse === '' || optionCourse === selectedCourse) ? 'block' : 'none';
                    }
                });

                // Filter subject dropdown
                Array.from(subjectSelect.options).forEach(option => {
                    if (option.value === '') {
                        option.style.display = 'block';
                    } else {
                        const optionCourse = option.getAttribute('data-course');
                        option.style.display = (selectedCourse === '' || optionCourse === selectedCourse) ? 'block' : 'none';
                    }
                });

                // Reset selections if current selection doesn't match course
                if (selectedCourse) {
                    const selectedCodeCourse = codeSelect.options[codeSelect.selectedIndex]?.getAttribute('data-course');
                    if (selectedCodeCourse && selectedCodeCourse !== selectedCourse) {
                        codeSelect.value = '';
                    }

                    const selectedSubjectCourse = subjectSelect.options[subjectSelect.selectedIndex]?.getAttribute('data-course');
                    if (selectedSubjectCourse && selectedSubjectCourse !== selectedCourse) {
                        subjectSelect.value = '';
                    }
                }
            }

            // When course changes, filter dropdowns
            courseSelect.addEventListener('change', filterDropdowns);

            // When code is selected, auto-select corresponding subject
            codeSelect.addEventListener('change', function() {
                const selectedCode = this.value;
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption && selectedOption.getAttribute('data-subject-name')) {
                    const subjectName = selectedOption.getAttribute('data-subject-name');
                    subjectSelect.value = subjectName;
                }
            });

            // When subject is selected, auto-select corresponding code and populate schedule/time
            subjectSelect.addEventListener('change', function() {
                const selectedSubject = this.value;
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption) {
                    // Auto-select corresponding code
                    if (selectedOption.getAttribute('data-code')) {
                        const code = selectedOption.getAttribute('data-code');
                        codeSelect.value = code;
                    }
                    
                    // Auto-populate schedule and time from subject
                    const schedule = selectedOption.getAttribute('data-schedule');
                    const time = selectedOption.getAttribute('data-time');
                    const scheduleInput = document.getElementById('schedule');
                    const timeInput = document.getElementById('time');
                    
                    if (schedule && scheduleInput) {
                        scheduleInput.value = schedule;
                    }
                    if (time && timeInput) {
                        timeInput.value = time;
                    }
                }
            });

            // Initial filter
            filterDropdowns();
        });
    </script>
@endsection

