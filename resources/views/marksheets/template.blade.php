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
            border: 3px solid #000;
            position: relative;
        }
        .header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
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
            margin-right: 20px;
            object-fit: cover;
        }
        .header-content {
            flex: 1;
            text-align: center;
        }
        .college-name {
            font-size: 24px;
            font-weight: bold;
            color: #000;
            margin-bottom: 5px;
        }
        .college-address {
            font-size: 12px;
            color: #000;
            margin-bottom: 10px;
        }
        .marksheet-title {
            font-size: 18px;
            font-weight: bold;
            color: #000;
            margin-top: 10px;
            text-decoration: underline;
        }
        .student-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .student-left, .student-right {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        .info-row {
            display: flex;
            gap: 10px;
        }
        .info-label {
            font-weight: bold;
            color: #000;
            min-width: 80px;
        }
        .info-value {
            color: #000;
        }
        .marks-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border: 1px solid #000;
        }
        .marks-table th {
            background-color: #fff;
            color: #000;
            padding: 8px;
            text-align: center;
            font-weight: bold;
            font-size: 12px;
            border: 1px solid #000;
        }
        .marks-table td {
            padding: 8px;
            text-align: center;
            border: 1px solid #000;
            font-size: 12px;
        }
        .subject-name {
            text-align: left !important;
            font-weight: 500;
        }
        .total-row {
            font-weight: bold;
        }
        .summary {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .summary-left, .summary-right {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        .summary-row {
            display: flex;
            gap: 10px;
        }
        .summary-label {
            font-weight: bold;
            color: #000;
            min-width: 120px;
        }
        .summary-value {
            color: #000;
        }
        .grading-scale {
            margin: 20px 0;
            font-size: 12px;
            text-align: center;
        }
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            text-align: center;
        }
        .signature-block {
            border-top: 1px solid #000;
            padding-top: 10px;
            width: 200px;
        }
        .signature-title {
            font-weight: bold;
            color: #000;
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
                <img src="{{ asset('storage/' . $collegeSettings->logo_path) }}" alt="College Logo" class="college-logo" onerror="this.style.display='none'">
            @endif
            <div class="header-content">
                <div class="college-name">Bajra International College</div>
                <div class="college-address">Boudha-Jorpati</div>
                <div class="marksheet-title">Progress Report</div>
            </div>
        </div>

        <!-- Student Information -->
        <div class="student-info">
            <div class="student-left">
                <div class="info-row">
                    <span class="info-label">Name:</span>
                    <span class="info-value">{{ $student->user->first_name }} {{ $student->user->last_name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Class:</span>
                    <span class="info-value">{{ $exam->class->name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Roll No:</span>
                    <span class="info-value">{{ $student->student_id }}</span>
                </div>
            </div>
            <div class="student-right">
                <div class="info-row">
                    <span class="info-label">Exam:</span>
                    <span class="info-value">{{ $exam->title }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Year:</span>
                    <span class="info-value">{{ $exam->academicYear->name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Date:</span>
                    <span class="info-value">{{ now()->format('Y-m-d') }}</span>
                </div>
            </div>
        </div>

        <!-- Marks Table -->
        <table class="marks-table">
            <thead>
                <tr>
                    <th style="width: 8%;">S.N</th>
                    <th style="width: 40%;">Subjects</th>
                    <th colspan="2" style="width: 24%;">Marks</th>
                    <th style="width: 15%;">Total Marks</th>
                    <th style="width: 13%;">Grades</th>
                </tr>
                <tr>
                    <th></th>
                    <th></th>
                    <th style="width: 12%;">Theory</th>
                    <th style="width: 12%;">Practical</th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($marks as $index => $mark)
                    @php
                        $theoryMax = $mark->subject->theory_marks ?? 60;
                        $practicalMax = $mark->subject->practical_marks ?? 40;
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="subject-name">{{ $mark->subject->name }}<br><small>(Theory: {{ $theoryMax }}, Practical: {{ $practicalMax }})</small></td>
                        <td>{{ $mark->theory_marks ? number_format($mark->theory_marks, 0) : '-' }}</td>
                        <td>{{ $mark->practical_marks ? number_format($mark->practical_marks, 0) : '-' }}</td>
                        <td>{{ number_format($mark->obtained_marks, 0) }}/{{ number_format($mark->total_marks, 0) }}</td>
                        <td>{{ $mark->grade_letter ?? 'A' }}</td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td></td>
                    <td class="subject-name">Total</td>
                    <td>{{ number_format($marks->sum('theory_marks'), 0) }}</td>
                    <td>{{ number_format($marks->sum('practical_marks'), 0) }}</td>
                    <td>{{ number_format($totalObtained, 0) }}/{{ number_format($totalMaximum, 0) }}</td>
                    <td>{{ number_format($totalMaximum, 0) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Summary -->
        <div class="summary">
            <div class="summary-left">
                <div class="summary-row">
                    <span class="summary-label">Grand Total:</span>
                    <span class="summary-value">{{ number_format($totalObtained, 0) }}</span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Percentage:</span>
                    <span class="summary-value">{{ number_format($overallPercentage, 2) }}%</span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Result:</span>
                    <span class="summary-value">{{ $resultStatus }}</span>
                </div>
            </div>
            <div class="summary-right">
                <div class="summary-row">
                    <span class="summary-label">Grade:</span>
                    <span class="summary-value">{{ $overallGrade->grade_letter ?? 'A' }}</span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">GPA:</span>
                    <span class="summary-value">{{ number_format($gpa, 2) }}</span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Rank:</span>
                    <span class="summary-value">-</span>
                </div>
            </div>
        </div>

        <!-- Grading Scale -->
        <div class="grading-scale">
            @php
                $gradingSystem = $exam->getEffectiveGradingSystem();
                $gradeScales = $gradingSystem ? $gradingSystem->gradeScales : collect();
            @endphp
            @if($gradeScales->count() > 0)
                Grading Scale:
                @foreach($gradeScales as $scale)
                    {{ $scale->grade_letter }}({{ number_format($scale->min_percent, 0) }}-{{ number_format($scale->max_percent, 0) }}%){{ !$loop->last ? ', ' : '' }}
                @endforeach
            @else
                Grading Scale: A(90-100%), A-(80-90%), B+(70-80%), B(60-70%), F(0-60%)
            @endif
        </div>

        <!-- Signatures -->
        <div class="signatures">
            <div class="signature-block">
                <div class="signature-title">Class Teacher</div>
            </div>
            <div class="signature-block">
                <div class="signature-title">Deepak Thakur</div>
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
