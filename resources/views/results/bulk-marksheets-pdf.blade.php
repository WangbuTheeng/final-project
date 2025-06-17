<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulk Marksheets - {{ $exam->title }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        .page-break {
            page-break-before: always;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 20px;
        }

        .college-name {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #2563eb;
        }

        .college-address {
            font-size: 16px;
            color: #666;
            margin-bottom: 15px;
        }

        .marksheet-title {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #1f2937;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .exam-details {
            font-size: 14px;
            color: #4b5563;
        }

        .student-info {
            background-color: #f8fafc;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            border: 1px solid #e5e7eb;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px dotted #d1d5db;
        }

        .info-label {
            font-weight: bold;
            color: #374151;
        }

        .info-value {
            color: #1f2937;
        }

        .marks-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
            font-size: 11px;
        }

        .marks-table th {
            background-color: #374151;
            color: white;
            padding: 12px 8px;
            text-align: center;
            font-weight: bold;
            border: 1px solid #d1d5db;
        }

        .marks-table td {
            padding: 10px 8px;
            border: 1px solid #d1d5db;
            text-align: center;
        }

        .marks-table tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .subject-name {
            text-align: left;
            font-weight: 600;
        }

        .subject-code {
            font-size: 10px;
            color: #6b7280;
            display: block;
        }

        .grade-a { background-color: #dcfce7; color: #166534; font-weight: bold; }
        .grade-b { background-color: #dbeafe; color: #1e40af; font-weight: bold; }
        .grade-c { background-color: #fef3c7; color: #92400e; font-weight: bold; }
        .grade-d { background-color: #fed7aa; color: #c2410c; font-weight: bold; }
        .grade-f { background-color: #fecaca; color: #dc2626; font-weight: bold; }

        .pass { background-color: #dcfce7; color: #166534; font-weight: bold; }
        .fail { background-color: #fecaca; color: #dc2626; font-weight: bold; }

        .summary-section {
            background-color: #f8fafc;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            border: 2px solid #e5e7eb;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            text-align: center;
        }

        .summary-item {
            background-color: white;
            padding: 15px;
            border-radius: 6px;
            border: 1px solid #d1d5db;
        }

        .summary-value {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .summary-label {
            font-size: 11px;
            color: #6b7280;
            text-transform: uppercase;
            font-weight: bold;
        }

        .result-declaration {
            background-color: #f0f9ff;
            border: 2px solid #0ea5e9;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 25px;
        }

        .result-status {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .result-pass { color: #059669; }
        .result-fail { color: #dc2626; }

        .grading-scale {
            margin-bottom: 25px;
            background-color: #f8fafc;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }

        .grading-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #1f2937;
            text-align: center;
        }

        .grading-table {
            width: 100%;
            font-size: 10px;
            border-collapse: collapse;
        }

        .grading-table th,
        .grading-table td {
            padding: 6px 8px;
            border: 1px solid #d1d5db;
            text-align: center;
        }

        .grading-table th {
            background-color: #f3f4f6;
            font-weight: bold;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #6b7280;
            border-top: 2px solid #e5e7eb;
            padding-top: 20px;
        }

        .signature-section {
            margin-top: 40px;
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 40px;
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #374151;
            padding-top: 5px;
            margin-top: 40px;
            font-size: 11px;
            font-weight: bold;
        }

        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 80px;
            color: rgba(0, 0, 0, 0.05);
            z-index: -1;
            font-weight: bold;
        }
    </style>
</head>
<body>
    @foreach($results as $index => $result)
        @if($index > 0)
            <div class="page-break"></div>
        @endif

        <!-- Watermark -->
        <div class="watermark">{{ $result['collegeSettings']->name ?? 'BAJRA INTERNATIONAL COLLEGE' }}</div>

        <!-- Header -->
        <div class="header">
            <div class="college-name">{{ $result['collegeSettings']->name ?? 'Bajra International College' }}</div>
            <div class="college-address">
                {{ $result['collegeSettings']->address ?? 'Affiliated to Tribhuvan University' }}
                @if($result['collegeSettings']->phone)
                    <br>Phone: {{ $result['collegeSettings']->phone }}
                @endif
                @if($result['collegeSettings']->email)
                    | Email: {{ $result['collegeSettings']->email }}
                @endif
            </div>
            <div class="marksheet-title">Academic Transcript / Marksheet</div>
            <div class="exam-details">
                {{ $exam->title }} ({{ ucfirst($exam->exam_type) }})
                <br>
                Academic Year: {{ $exam->academicYear->name }} | Exam Date: {{ $exam->exam_date->format('F d, Y') }}
            </div>
        </div>

        <!-- Student Information -->
        <div class="student-info">
            <div class="info-grid">
                <div>
                    <div class="info-item">
                        <span class="info-label">Student Name:</span>
                        <span class="info-value">{{ $result['student']->user->first_name }} {{ $result['student']->user->last_name }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Student ID:</span>
                        <span class="info-value">{{ $result['student']->student_id }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Course:</span>
                        <span class="info-value">{{ $exam->class->course->title }}</span>
                    </div>
                </div>
                <div>
                    <div class="info-item">
                        <span class="info-label">Class/Section:</span>
                        <span class="info-value">{{ $exam->class->name }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Faculty:</span>
                        <span class="info-value">{{ $exam->class->course->faculty->name }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Date of Issue:</span>
                        <span class="info-value">{{ now()->format('F d, Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Subject-wise Marks Table -->
        <table class="marks-table">
            <thead>
                <tr>
                    <th style="width: 25%;">Subject</th>
                    <th style="width: 12%;">Theory</th>
                    <th style="width: 12%;">Practical</th>
                    <th style="width: 12%;">Internal</th>
                    <th style="width: 15%;">Total Marks</th>
                    <th style="width: 12%;">Percentage</th>
                    <th style="width: 8%;">Grade</th>
                    <th style="width: 12%;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($result['marks'] as $mark)
                    <tr>
                        <td class="subject-name">
                            {{ $mark->subject->name }}
                            <span class="subject-code">{{ $mark->subject->code }}</span>
                        </td>
                        <td>
                            @if($mark->theory_marks > 0)
                                {{ number_format($mark->theory_marks, 1) }}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($mark->practical_marks > 0)
                                {{ number_format($mark->practical_marks, 1) }}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($mark->internal_marks > 0)
                                {{ number_format($mark->internal_marks, 1) }}
                            @else
                                -
                            @endif
                        </td>
                        <td><strong>{{ number_format($mark->obtained_marks, 1) }}/{{ number_format($mark->total_marks, 1) }}</strong></td>
                        <td><strong>{{ number_format($mark->percentage, 2) }}%</strong></td>
                        <td class="grade-{{ strtolower($mark->grade_letter) }}">
                            <strong>{{ $mark->grade_letter }}</strong>
                        </td>
                        <td class="{{ $mark->percentage >= 40 ? 'pass' : 'fail' }}">
                            {{ $mark->percentage >= 40 ? 'PASS' : 'FAIL' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Summary Section -->
        <div class="summary-section">
            <div class="summary-grid">
                <div class="summary-item">
                    <div class="summary-value" style="color: #2563eb;">{{ number_format($result['totalObtained'], 1) }}</div>
                    <div class="summary-label">Total Obtained</div>
                </div>
                <div class="summary-item">
                    <div class="summary-value" style="color: #1f2937;">{{ number_format($result['totalMaximum'], 1) }}</div>
                    <div class="summary-label">Total Maximum</div>
                </div>
                <div class="summary-item">
                    <div class="summary-value" style="color: #7c3aed;">{{ number_format($result['overallPercentage'], 2) }}%</div>
                    <div class="summary-label">Overall Percentage</div>
                </div>
                <div class="summary-item">
                    <div class="summary-value" style="color: #059669;">{{ number_format($result['gpa'], 2) }}</div>
                    <div class="summary-label">Grade Point Average</div>
                </div>
            </div>
        </div>

        <!-- Result Declaration -->
        <div class="result-declaration">
            <div class="result-status {{ $result['resultStatus'] == 'Pass' ? 'result-pass' : 'result-fail' }}">
                RESULT: {{ strtoupper($result['resultStatus']) }}
            </div>
            <div style="font-size: 16px; font-weight: bold; color: #374151;">
                Overall Grade: {{ $result['overallGrade'] }} | GPA: {{ number_format($result['gpa'], 2) }}
            </div>
            @if($result['resultStatus'] == 'Pass')
                <div style="font-size: 12px; color: #059669; margin-top: 10px;">
                    Congratulations! You have successfully passed the examination.
                </div>
            @else
                <div style="font-size: 12px; color: #dc2626; margin-top: 10px;">
                    You need to improve your performance in the failed subjects.
                </div>
            @endif
        </div>

        <!-- Grading Scale -->
        <div class="grading-scale">
            <div class="grading-title">Tribhuvan University Grading Scale</div>
            <table class="grading-table">
                <thead>
                    <tr>
                        <th>Grade</th>
                        <th>Percentage</th>
                        <th>GPA</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td>A</td><td>80-100%</td><td>4.0</td><td>Excellent</td></tr>
                    <tr><td>B+</td><td>70-79%</td><td>3.6</td><td>Very Good</td></tr>
                    <tr><td>B</td><td>60-69%</td><td>3.2</td><td>Good</td></tr>
                    <tr><td>C+</td><td>50-59%</td><td>2.8</td><td>Above Average</td></tr>
                    <tr><td>C</td><td>45-49%</td><td>2.4</td><td>Average</td></tr>
                    <tr><td>D</td><td>40-44%</td><td>2.0</td><td>Below Average</td></tr>
                    <tr><td>F</td><td>Below 40%</td><td>0.0</td><td>Fail</td></tr>
                </tbody>
            </table>
        </div>

        <!-- Signature Section -->
        <div class="signature-section">
            <div>
                <div class="signature-line">Prepared By</div>
            </div>
            <div>
                <div class="signature-line">Verified By</div>
            </div>
            <div>
                <div class="signature-line">Principal</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>Note:</strong> This is a computer-generated marksheet. Any alteration or forgery is punishable by law.</p>
            <p>Generated on {{ now()->format('F d, Y \a\t g:i A') }} | {{ $result['collegeSettings']->name ?? 'Bajra International College' }} Management System</p>
            <p>For verification, contact: {{ $result['collegeSettings']->email ?? 'info@bajracollege.edu.np' }} | {{ $result['collegeSettings']->phone ?? '+977-1-XXXXXXX' }}</p>
        </div>
    @endforeach
</body>
</html>