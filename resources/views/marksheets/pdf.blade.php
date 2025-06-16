<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marksheet - {{ $student->user->first_name }} {{ $student->user->last_name }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 15px;
            font-size: 12px;
            line-height: 1.4;
        }
        .marksheet {
            width: 100%;
            max-width: 100%;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .college-name {
            font-size: 20px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 3px;
        }
        .college-address {
            font-size: 11px;
            color: #6b7280;
            margin-bottom: 10px;
        }
        .marksheet-title {
            font-size: 16px;
            font-weight: bold;
            color: #374151;
            margin-top: 10px;
        }
        .student-info {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .student-info td {
            padding: 6px 8px;
            border: 1px solid #d1d5db;
            vertical-align: top;
        }
        .info-label {
            font-weight: bold;
            background-color: #f3f4f6;
            width: 25%;
        }
        .info-value {
            width: 25%;
        }
        .marks-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .marks-table th {
            background-color: #2563eb;
            color: white;
            padding: 8px 4px;
            text-align: center;
            font-weight: bold;
            font-size: 11px;
            border: 1px solid #1e40af;
        }
        .marks-table td {
            padding: 6px 4px;
            text-align: center;
            border: 1px solid #d1d5db;
            font-size: 11px;
        }
        .marks-table tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .subject-name {
            text-align: left !important;
            font-weight: 500;
        }
        .total-row {
            background-color: #dbeafe !important;
            font-weight: bold;
        }
        .total-row td {
            border-top: 2px solid #2563eb;
            padding: 8px 4px;
        }
        .summary {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .summary td {
            padding: 12px;
            text-align: center;
            border: 1px solid #d1d5db;
            font-weight: bold;
        }
        .percentage-cell {
            background-color: #d1fae5;
            color: #065f46;
        }
        .gpa-cell {
            background-color: #e0e7ff;
            color: #3730a3;
        }
        .summary-value {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 3px;
        }
        .summary-label {
            font-size: 10px;
        }
        .result-status {
            text-align: center;
            padding: 12px;
            margin-bottom: 20px;
            font-size: 16px;
            font-weight: bold;
            border: 2px solid;
        }
        .result-pass {
            background-color: #d1fae5;
            color: #065f46;
            border-color: #10b981;
        }
        .result-fail {
            background-color: #fee2e2;
            color: #991b1b;
            border-color: #ef4444;
        }
        .signatures {
            width: 100%;
            margin-top: 30px;
            border-collapse: collapse;
        }
        .signatures td {
            text-align: center;
            padding: 20px 5px 10px 5px;
            border-top: 1px solid #374151;
            width: 20%;
            vertical-align: top;
        }
        .signature-title {
            font-weight: bold;
            color: #374151;
            font-size: 10px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 10px;
        }
        .grade-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 10px;
        }
        .grade-a { background-color: #dcfce7; color: #166534; }
        .grade-b { background-color: #dbeafe; color: #1e40af; }
        .grade-c { background-color: #fef3c7; color: #92400e; }
        .grade-d { background-color: #fed7d7; color: #991b1b; }
        .grade-f { background-color: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>
    <div class="marksheet">
        <!-- Header -->
        <div class="header">
            @if($collegeSettings->logo_path && ($collegeSettings->marksheet_settings['show_logo'] ?? true))
                <div style="text-align: center; margin-bottom: 10px;">
                    <img src="{{ public_path('storage/' . $collegeSettings->logo_path) }}" alt="College Logo" style="height: 60px; width: auto;">
                </div>
            @endif
            <div class="college-name">{{ $collegeSettings->college_name }}</div>
            <div class="college-address">{{ $collegeSettings->college_address }}</div>
            @if($collegeSettings->college_phone || $collegeSettings->college_email)
                <div style="font-size: 10px; color: #6b7280; margin-bottom: 5px;">
                    @if($collegeSettings->college_phone)
                        Phone: {{ $collegeSettings->college_phone }}
                    @endif
                    @if($collegeSettings->college_phone && $collegeSettings->college_email) | @endif
                    @if($collegeSettings->college_email)
                        Email: {{ $collegeSettings->college_email }}
                    @endif
                </div>
            @endif
            @if($collegeSettings->result_header)
                <div style="margin: 8px 0; font-style: italic; font-size: 11px;">{{ $collegeSettings->result_header }}</div>
            @endif
            <div class="marksheet-title">ACADEMIC TRANSCRIPT</div>
            @if($collegeSettings->marksheet_settings['show_issue_date'] ?? true)
                <div style="font-size: 10px; color: #6b7280; margin-top: 5px;">
                    Issue Date: {{ now()->format('F d, Y') }}
                </div>
            @endif
        </div>

        <!-- Student Information -->
        <table class="student-info">
            <tr>
                <td class="info-label">Student Name</td>
                <td class="info-value">{{ $student->user->first_name }} {{ $student->user->last_name }}</td>
                <td class="info-label">Student ID</td>
                <td class="info-value">{{ $student->student_id }}</td>
            </tr>
            <tr>
                <td class="info-label">Course</td>
                <td class="info-value">{{ $exam->class->course->title }}</td>
                <td class="info-label">Class/Section</td>
                <td class="info-value">{{ $exam->class->name }}</td>
            </tr>
            <tr>
                <td class="info-label">Academic Year</td>
                <td class="info-value">{{ $exam->academicYear->name }}</td>
                <td class="info-label">Exam</td>
                <td class="info-value">{{ $exam->title }} ({{ $exam->getExamTypeLabel() }})</td>
            </tr>
        </table>

        <!-- Marks Table -->
        <table class="marks-table">
            <thead>
                <tr>
                    <th style="width: 40%;">Subject</th>
                    <th style="width: 12%;">Theory</th>
                    <th style="width: 12%;">Practical</th>
                    <th style="width: 12%;">Internal</th>
                    <th style="width: 12%;">Total</th>
                    <th style="width: 12%;">Grade</th>
                </tr>
            </thead>
            <tbody>
                @foreach($marks as $mark)
                    <tr>
                        <td class="subject-name">{{ $mark->subject->name }} ({{ $mark->subject->code }})</td>
                        <td>{{ $mark->theory_marks ? number_format($mark->theory_marks, 1) : '-' }}</td>
                        <td>{{ $mark->practical_marks ? number_format($mark->practical_marks, 1) : '-' }}</td>
                        <td>{{ $mark->internal_marks ? number_format($mark->internal_marks, 1) : '-' }}</td>
                        <td>{{ number_format($mark->obtained_marks, 1) }}/{{ number_format($mark->total_marks, 1) }}</td>
                        <td>
                            @if($mark->grade_letter)
                                <span class="grade-badge grade-{{ strtolower(substr($mark->grade_letter, 0, 1)) }}">
                                    {{ $mark->grade_letter }}
                                </span>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td class="subject-name">TOTAL</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>{{ number_format($totalObtained, 1) }}/{{ number_format($totalMaximum, 1) }}</td>
                    <td>
                        @if($overallGrade)
                            <span class="grade-badge grade-{{ strtolower(substr($overallGrade->grade_letter, 0, 1)) }}">
                                {{ $overallGrade->grade_letter }}
                            </span>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Summary -->
        <table class="summary">
            <tr>
                <td class="percentage-cell">
                    <div class="summary-value">{{ number_format($overallPercentage, 2) }}%</div>
                    <div class="summary-label">Overall Percentage</div>
                </td>
                <td class="gpa-cell">
                    <div class="summary-value">{{ number_format($gpa, 2) }}</div>
                    <div class="summary-label">Grade Point Average</div>
                </td>
            </tr>
        </table>

        <!-- Result Status -->
        <div class="result-status {{ $resultStatus === 'FAIL' ? 'result-fail' : 'result-pass' }}">
            {{ $resultStatus }}
        </div>

        <!-- Grading Scale -->
        @if($collegeSettings->marksheet_settings['show_grading_scale'] ?? false)
            <div style="margin: 20px 0; padding: 15px; border: 1px solid #d1d5db; border-radius: 5px; background-color: #f9fafb;">
                <h4 style="font-size: 12px; font-weight: bold; margin-bottom: 10px; text-align: center;">Grading Scale for {{ ucfirst($exam->exam_type) }} Examination</h4>
                <table style="width: 100%; border-collapse: collapse; font-size: 10px;">
                    <thead>
                        <tr style="background-color: #e5e7eb;">
                            <th style="border: 1px solid #d1d5db; padding: 4px; text-align: center;">Grade</th>
                            <th style="border: 1px solid #d1d5db; padding: 4px; text-align: center;">Percentage Range</th>
                            <th style="border: 1px solid #d1d5db; padding: 4px; text-align: center;">Grade Points</th>
                            <th style="border: 1px solid #d1d5db; padding: 4px; text-align: center;">Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $gradeScales = [
                                ['grade' => 'A+', 'min' => 90, 'max' => 100, 'points' => 4.0, 'remarks' => 'Outstanding'],
                                ['grade' => 'A', 'min' => 80, 'max' => 89, 'points' => 3.6, 'remarks' => 'Excellent'],
                                ['grade' => 'B+', 'min' => 70, 'max' => 79, 'points' => 3.2, 'remarks' => 'Very Good'],
                                ['grade' => 'B', 'min' => 60, 'max' => 69, 'points' => 2.8, 'remarks' => 'Good'],
                                ['grade' => 'C+', 'min' => 50, 'max' => 59, 'points' => 2.4, 'remarks' => 'Satisfactory'],
                                ['grade' => 'C', 'min' => 40, 'max' => 49, 'points' => 2.0, 'remarks' => 'Acceptable'],
                                ['grade' => 'D', 'min' => 32, 'max' => 39, 'points' => 1.6, 'remarks' => 'Partially Acceptable'],
                                ['grade' => 'F', 'min' => 0, 'max' => 31, 'points' => 0.0, 'remarks' => 'Fail'],
                            ];
                        @endphp
                        @foreach($gradeScales as $scale)
                            <tr>
                                <td style="border: 1px solid #d1d5db; padding: 4px; text-align: center; font-weight: bold;">{{ $scale['grade'] }}</td>
                                <td style="border: 1px solid #d1d5db; padding: 4px; text-align: center;">{{ $scale['min'] }}% - {{ $scale['max'] }}%</td>
                                <td style="border: 1px solid #d1d5db; padding: 4px; text-align: center;">{{ $scale['points'] }}</td>
                                <td style="border: 1px solid #d1d5db; padding: 4px; text-align: center;">{{ $scale['remarks'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <!-- Signatures -->
        @if($collegeSettings->marksheet_settings['show_signatures'] ?? true)
            <table class="signatures">
                <tr>
                    @if($collegeSettings->class_teacher_name)
                        <td>
                            @if($collegeSettings->class_teacher_signature_path)
                                <img src="{{ public_path('storage/' . $collegeSettings->class_teacher_signature_path) }}" alt="Class Teacher Signature" style="height: 30px; width: auto; margin-bottom: 5px;">
                            @endif
                            <div class="signature-title">{{ $collegeSettings->class_teacher_name }}</div>
                            <div style="font-size: 9px; color: #6b7280;">Class Teacher</div>
                        </td>
                    @endif

                    @if($collegeSettings->hod_name)
                        <td>
                            @if($collegeSettings->hod_signature_path)
                                <img src="{{ public_path('storage/' . $collegeSettings->hod_signature_path) }}" alt="HOD Signature" style="height: 30px; width: auto; margin-bottom: 5px;">
                            @endif
                            <div class="signature-title">{{ $collegeSettings->hod_name }}</div>
                            <div style="font-size: 9px; color: #6b7280;">Head of Department</div>
                        </td>
                    @endif

                    @if($collegeSettings->exam_controller_name)
                        <td>
                            @if($collegeSettings->exam_controller_signature_path)
                                <img src="{{ public_path('storage/' . $collegeSettings->exam_controller_signature_path) }}" alt="Exam Controller Signature" style="height: 30px; width: auto; margin-bottom: 5px;">
                            @endif
                            <div class="signature-title">{{ $collegeSettings->exam_controller_name }}</div>
                            <div style="font-size: 9px; color: #6b7280;">Exam Controller</div>
                        </td>
                    @endif

                    @if($collegeSettings->registrar_name)
                        <td>
                            @if($collegeSettings->registrar_signature_path)
                                <img src="{{ public_path('storage/' . $collegeSettings->registrar_signature_path) }}" alt="Registrar Signature" style="height: 30px; width: auto; margin-bottom: 5px;">
                            @endif
                            <div class="signature-title">{{ $collegeSettings->registrar_name }}</div>
                            <div style="font-size: 9px; color: #6b7280;">Registrar</div>
                        </td>
                    @endif

                    @if($collegeSettings->principal_name)
                        <td>
                            @if($collegeSettings->principal_signature_path)
                                <img src="{{ public_path('storage/' . $collegeSettings->principal_signature_path) }}" alt="Principal Signature" style="height: 30px; width: auto; margin-bottom: 5px;">
                            @endif
                            <div class="signature-title">{{ $collegeSettings->principal_name }}</div>
                            <div style="font-size: 9px; color: #6b7280;">Principal</div>
                        </td>
                    @endif
                </tr>
            </table>
        @endif

        <!-- Footer -->
        <div class="footer">
            @if($collegeSettings->result_footer)
                <div>{{ $collegeSettings->result_footer }}</div>
            @endif
            <div>Generated on {{ now()->format('F d, Y \a\t g:i A') }}</div>
        </div>
    </div>
</body>
</html>
