<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marksheet - {{ $student->user->first_name }} {{ $student->user->last_name }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .marksheet {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            border-bottom: 3px solid {{ $collegeSettings->primary_color ?? '#2563eb' }};
            padding-bottom: 20px;
            margin-bottom: 30px;
            background: {{ $collegeSettings->header_background_color ?? '#f8fafc' }};
            position: relative;
        }
        @if($collegeSettings->show_watermark && $collegeSettings->watermark_text)
        .header::before {
            content: "{{ $collegeSettings->watermark_text }}";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 48px;
            color: rgba(0, 0, 0, 0.05);
            font-weight: bold;
            z-index: 0;
        }
        @endif
        .college-logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 15px;
            border-radius: 50%;
            object-fit: cover;
        }
        .college-name {
            font-size: 28px;
            font-weight: bold;
            color: {{ $collegeSettings->primary_color ?? '#1e40af' }};
            margin-bottom: 5px;
            position: relative;
            z-index: 1;
        }
        .college-code {
            font-size: 12px;
            color: {{ $collegeSettings->secondary_color ?? '#6b7280' }};
            margin-bottom: 5px;
        }
        .college-address {
            font-size: 14px;
            color: {{ $collegeSettings->secondary_color ?? '#6b7280' }};
            margin-bottom: 15px;
        }
        .affiliation {
            font-size: 12px;
            color: {{ $collegeSettings->secondary_color ?? '#6b7280' }};
            font-style: italic;
            margin-bottom: 10px;
        }
        .college-motto {
            font-size: 11px;
            color: {{ $collegeSettings->secondary_color ?? '#6b7280' }};
            font-style: italic;
            margin-bottom: 15px;
        }
        .marksheet-title {
            font-size: 24px;
            font-weight: bold;
            color: #374151;
            margin-top: 15px;
            position: relative;
            z-index: 1;
        }
        .student-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
            padding: 20px;
            background-color: #f3f4f6;
            border-radius: 8px;
        }
        .info-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .info-label {
            font-weight: bold;
            color: #374151;
            font-size: 14px;
        }
        .info-value {
            color: #1f2937;
            font-size: 16px;
            padding: 5px 0;
            border-bottom: 1px solid #d1d5db;
        }
        .marks-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        .marks-table th {
            background-color: #2563eb;
            color: white;
            padding: 12px 8px;
            text-align: center;
            font-weight: bold;
            font-size: 14px;
        }
        .marks-table td {
            padding: 10px 8px;
            text-align: center;
            border-bottom: 1px solid #e5e7eb;
            font-size: 14px;
        }
        .marks-table tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .marks-table tr:hover {
            background-color: #f3f4f6;
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
            padding: 15px 8px;
        }
        .summary {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        .summary-card {
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }
        .percentage-card {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }
        .gpa-card {
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
            color: white;
        }
        .summary-value {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .summary-label {
            font-size: 14px;
            opacity: 0.9;
        }
        .result-status {
            text-align: center;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            font-size: 24px;
            font-weight: bold;
        }
        .result-pass {
            background-color: #d1fae5;
            color: #065f46;
            border: 2px solid #10b981;
        }
        .result-fail {
            background-color: #fee2e2;
            color: #991b1b;
            border: 2px solid #ef4444;
        }
        .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 40px;
            margin-top: 50px;
            text-align: center;
        }
        .signature-block {
            border-top: 1px solid #374151;
            padding-top: 10px;
        }
        .signature-title {
            font-weight: bold;
            color: #374151;
            font-size: 12px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 12px;
        }
        .grade-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 12px;
        }
        .grade-a { background-color: #dcfce7; color: #166534; }
        .grade-b { background-color: #dbeafe; color: #1e40af; }
        .grade-c { background-color: #fef3c7; color: #92400e; }
        .grade-d { background-color: #fed7d7; color: #991b1b; }
        .grade-f { background-color: #fee2e2; color: #991b1b; }
        
        @media print {
            body { background-color: white; }
            .marksheet { box-shadow: none; }
        }
    </style>
</head>
<body>
    <div class="marksheet">
        <!-- Header -->
        <div class="header">
            @if($collegeSettings->show_college_logo && $collegeSettings->logo_path)
                <img src="{{ asset('storage/' . $collegeSettings->logo_path) }}" alt="College Logo" class="college-logo">
            @endif
            <div class="college-name">{{ $collegeSettings->college_name }}</div>
            @if($collegeSettings->college_code)
                <div class="college-code">College Code: {{ $collegeSettings->college_code }}</div>
            @endif
            <div class="college-address">{{ $collegeSettings->college_address }}</div>
            @if($collegeSettings->affiliation)
                <div class="affiliation">Affiliated to: {{ $collegeSettings->affiliation }}</div>
            @endif
            @if($collegeSettings->university_name)
                <div class="affiliation">{{ $collegeSettings->university_name }}</div>
            @endif
            @if($collegeSettings->college_motto)
                <div class="college-motto">"{{ $collegeSettings->college_motto }}"</div>
            @endif
            @if($collegeSettings->result_header)
                <div style="margin: 10px 0; font-style: italic;">{{ $collegeSettings->result_header }}</div>
            @endif
            <div class="marksheet-title">ACADEMIC TRANSCRIPT</div>
        </div>

        <!-- Student Information -->
        <div class="student-info">
            <div class="info-group">
                <div class="info-label">Student Name</div>
                <div class="info-value">{{ $student->user->first_name }} {{ $student->user->last_name }}</div>
                
                <div class="info-label">Student ID</div>
                <div class="info-value">{{ $student->student_id }}</div>
                
                <div class="info-label">Course</div>
                <div class="info-value">{{ $exam->class->course->title }}</div>
            </div>
            <div class="info-group">
                <div class="info-label">Class/Section</div>
                <div class="info-value">{{ $exam->class->name }}</div>
                
                <div class="info-label">Academic Year</div>
                <div class="info-value">{{ $exam->academicYear->name }}</div>
                
                <div class="info-label">Exam</div>
                <div class="info-value">{{ $exam->title }} ({{ $exam->getExamTypeLabel() }})</div>
            </div>
        </div>

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
        <div class="summary">
            <div class="summary-card percentage-card">
                <div class="summary-value">{{ number_format($overallPercentage, 2) }}%</div>
                <div class="summary-label">Overall Percentage</div>
            </div>
            <div class="summary-card gpa-card">
                <div class="summary-value">{{ number_format($gpa, 2) }}</div>
                <div class="summary-label">Grade Point Average</div>
            </div>
        </div>

        <!-- Result Status -->
        <div class="result-status {{ $resultStatus === 'FAIL' ? 'result-fail' : 'result-pass' }}">
            {{ $resultStatus }}
        </div>

        <!-- Signatures -->
        <div class="signatures">
            <div class="signature-block">
                <div class="signature-title">{{ $collegeSettings->exam_controller_name ?? 'Exam Controller' }}</div>
            </div>
            <div class="signature-block">
                <div class="signature-title">{{ $collegeSettings->registrar_name ?? 'Registrar' }}</div>
            </div>
            <div class="signature-block">
                <div class="signature-title">{{ $collegeSettings->principal_name ?? 'Principal' }}</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            @if($collegeSettings->result_footer)
                <div>{{ $collegeSettings->result_footer }}</div>
            @endif
            <div>Generated on {{ now()->format('F d, Y \a\t g:i A') }}</div>
        </div>
    </div>

    <script>
        // Auto-print functionality for PDF generation
        if (window.location.search.includes('print=true')) {
            window.print();
        }
    </script>
</body>
</html>
