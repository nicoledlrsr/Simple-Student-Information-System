@extends('layouts.app')

@section('content')
    @include('layouts.teacher-sidebar')
    @include('layouts.datatables')

    <div class="teacher-container" style="padding:24px; padding-top:80px;">
        <div style="background: #FFFFFF; border-radius: 16px; padding: 32px; box-shadow: 0 4px 16px rgba(0,0,0,0.1);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                <div>
                    <h1 style="color: #1C6EA4; margin-bottom: 8px;">Student Management</h1>
                    <p style="color: #6B7280;">View students for your course and year level.</p>
                    @if($teacherCourse && $teacherYearLevel)
                        <p style="color:#374151; margin-top:4px;">
                            Course: <strong>{{ $teacherCourse }}</strong> &nbsp;|&nbsp;
                            Year Level: <strong>{{ $teacherYearLevel }}</strong>
                        </p>
                    @else
                        <p style="color:#B91C1C; margin-top:4px;">No course or year level assigned to your account yet.</p>
                    @endif
                </div>
            </div>

            @if (session('success'))
                <div class="flash-message success-message" style="background: #D1FAE5; color: #065F46; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px;">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="flash-message error-message" style="background: #FEE2E2; color: #991B1B; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px;">
                    {{ session('error') }}
                </div>
            @endif

            <div style="overflow-x: auto;">
                @if($students->count() === 0)
                    <div style="padding: 20px; color: #6B7280;">No students found for this course and year level.</div>
                @else
                    <table id="studentsTable" class="display" style="width:100%">
                        <thead>
                            <tr>
                                <th>Institutional ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Course</th>
                                <th>Year Level</th>
                                <th>Section</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $student)
                                <tr>
                                    <td>{{ $student->student_id ?? 'N/A' }}</td>
                                    <td>{{ $student->name }}</td>
                                    <td>{{ $student->email }}</td>
                                    <td>{{ $student->course ?? 'Not Set' }}</td>
                                    <td>{{ $student->year_level ?? 'Not Set' }}</td>
                                    <td>{{ $student->section->name ?? 'Not Assigned' }}</td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="action-btn view-btn" onclick="openModal('view-student-modal-{{ $student->id }}')">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            <script src="{{ asset('js/modals.js') }}"></script>
            <script>
                $(document).ready(function() {
                    if ($.fn.DataTable.isDataTable('#studentsTable')) {
                        $('#studentsTable').DataTable().destroy();
                    }
                    $('#studentsTable').DataTable({
                        pageLength: 10,
                        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                        order: [[1, 'asc']],
                        language: {
                            search: "",
                            searchPlaceholder: "Search students..."
                        }
                    });
                });
            </script>
        </div>
    </div>

    {{-- View Student Modals --}}
    @foreach($students as $student)
        <div class="modal" id="view-student-modal-{{ $student->id }}">
            <div class="modal-content">
                <div class="form-header">
                    <h3>Student Information System</h3>
                    <p>View Student Record</p>
                    <h4>Student Details</h4>
                </div>
                <div class="form-section">
                    <h5><i class="fas fa-user"></i> Personal Information</h5>
                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label>Institutional ID</label>
                                <div class="view-field">{{ $student->student_id ?? 'N/A' }}</div>
                            </div>
                        </div>
                        <div class="form-col">
                            <div class="form-group">
                                <label>Full Name</label>
                                <div class="view-field">{{ $student->name }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label>Email</label>
                                <div class="view-field">{{ $student->email }}</div>
                            </div>
                        </div>
                        <div class="form-col">
                            <div class="form-group">
                                <label>Birthday</label>
                                <div class="view-field">{{ $student->birthday?->format('F d, Y') ?? 'Not provided' }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label>Gender</label>
                                <div class="view-field">{{ $student->gender ?? 'Not provided' }}</div>
                            </div>
                        </div>
                        <div class="form-col">
                            <div class="form-group">
                                <label>Contact Number</label>
                                <div class="view-field">{{ $student->contact_number ?? 'Not provided' }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-col" style="grid-column: 1 / -1;">
                            <div class="form-group">
                                <label>Address</label>
                                <div class="view-field">{{ $student->address ?? 'Not provided' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-section">
                    <h5><i class="fas fa-graduation-cap"></i> Academic Information</h5>
                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label>Course</label>
                                <div class="view-field">{{ $student->course ?? 'Not Set' }}</div>
                            </div>
                        </div>
                        <div class="form-col">
                            <div class="form-group">
                                <label>Year Level</label>
                                <div class="view-field">{{ $student->year_level ?? 'Not Set' }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label>Section</label>
                                <div class="view-field">
                                    @if($student->section_id)
                                        {{ $student->section ? $student->section->name : 'Section #' . $student->section_id }}
                                    @else
                                        Not Assigned
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-section">
                    <h5><i class="fas fa-users"></i> Guardian Information</h5>
                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label>Guardian Name</label>
                                <div class="view-field">{{ $student->guardian_name ?? 'Not provided' }}</div>
                            </div>
                        </div>
                        <div class="form-col">
                            <div class="form-group">
                                <label>Guardian Contact</label>
                                <div class="view-field">{{ $student->guardian_contact ?? 'Not provided' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                @if($student->documents && $student->documents->count() > 0)
                <div class="form-section">
                    <h5><i class="fas fa-file-alt"></i> Documents</h5>
                    <div class="form-row">
                        <div class="form-col" style="grid-column: 1 / -1;">
                            <div class="form-group">
                                @foreach($student->documents as $document)
                                    <div style="background: #F9FAFB; padding: 12px; border-radius: 8px; margin-bottom: 8px;">
                                        <div style="color: #111827; font-weight: 500;">{{ $document->document_name ?? 'Document' }}</div>
                                        <div style="color: #6B7280; font-size: 0.875rem; margin-top: 4px;">
                                            Uploaded: {{ $document->uploaded_at?->format('M d, Y') ?? 'N/A' }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                <div class="modal-buttons">
                    <button type="button" class="btn exit-btn" onclick="closeModal('view-student-modal-{{ $student->id }}')">
                        <i class="fas fa-times"></i> Close
                    </button>
                </div>
            </div>
        </div>
    @endforeach

@endsection
