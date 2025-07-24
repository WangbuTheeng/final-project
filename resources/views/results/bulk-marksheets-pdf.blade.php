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
            padding: 15px;
            color: #000;
        }

        .page-break {
            page-break-before: always;
        }

        .marksheet {
            width: 100%;
            max-width: 100%;
            border: 2px solid #000;
            padding: 20px;
            margin-bottom: 20px;
        }

        .header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .header-content {
            flex: 1;
            text-align: center;
        }

        .college-logo {
            width: 60px;
            height: 60px;
            margin-right: 15px;
            object-fit: cover;
        }

        .college-name {
            font-size: 18px;
            font-weight: bold;
            color: #000;
            margin-bottom: 3px;
        }

        .college-address {
            font-size: 10px;
            color: #000;
            margin-bottom: 8px;
        }

        .marksheet-title {
            font-size: 14px;
            font-weight: bold;
            color: #000;
            margin-top: 8px;
            text-decoration: underline;
        }

        .student-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 11px;
        }

        .student-left, .student-right {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .info-row {
            display: flex;
            gap: 8px;
        }

        .info-label {
            font-weight: bold;
            color: #000;
            min-width: 60px;
        }

        .info-value {
            color: #000;
        }

        .marks-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            border: 1px solid #000;
        }

        .marks-table th {
            background-color: #fff;
            color: #000;
            padding: 6px 4px;
            text-align: center;
            font-weight: bold;
            font-size: 10px;
            border: 1px solid #000;
        }

        .marks-table td {
            padding: 5px 4px;
            text-align: center;
            border: 1px solid #000;
            font-size: 10px;
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
            margin-bottom: 15px;
            font-size: 11px;
        }

        .summary-left, .summary-right {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .summary-row {
            display: flex;
            gap: 8px;
        }

        .summary-label {
            font-weight: bold;
            color: #000;
            min-width: 80px;
        }

        .summary-value {
            color: #000;
        }

        .grading-scale {
            margin: 15px 0;
            font-size: 10px;
            text-align: center;
        }

        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            text-align: center;
        }

        .signature-block {
            border-top: 1px solid #000;
            padding-top: 8px;
            width: 150px;
        }

        .signature-title {
            font-weight: bold;
            color: #000;
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
    </style>
</head>
<body>
    @foreach($results as $index => $result)
        @if($index > 0)
            <div class="page-break"></div>
        @endif

        <div class="marksheet">
            <!-- Header -->
            <div class="header">
                @if(isset($result['collegeSettings']->logo_path))
                    <img src="{{ public_path('storage/' . $result['collegeSettings']->logo_path) }}" alt="College Logo" class="college-logo">
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
                        <span class="info-value">{{ $result['student']->user->first_name }} {{ $result['student']->user->last_name }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Class:</span>
                        <span class="info-value">{{ $exam->class->name }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Roll No:</span>
                        <span class="info-value">{{ $result['student']->student_id }}</span>
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
                    @foreach($result['marks'] as $markIndex => $mark)
                        @php
                            $theoryMax = $mark->subject->theory_marks ?? 60;
                            $practicalMax = $mark->subject->practical_marks ?? 40;
                        @endphp
                        <tr>
                            <td>{{ $markIndex + 1 }}</td>
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
                        <td>{{ number_format($result['marks']->sum('theory_marks'), 0) }}</td>
                        <td>{{ number_format($result['marks']->sum('practical_marks'), 0) }}</td>
                        <td>{{ number_format($result['totalObtained'], 0) }}/{{ number_format($result['totalMaximum'], 0) }}</td>
                        <td>{{ number_format($result['totalMaximum'], 0) }}</td>
                    </tr>
                </tbody>
            </table>

            <!-- Summary -->
            <div class="summary">
                <div class="summary-left">
                    <div class="summary-row">
                        <span class="summary-label">Grand Total:</span>
                        <span class="summary-value">{{ number_format($result['totalObtained'], 0) }}</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">Percentage:</span>
                        <span class="summary-value">{{ number_format($result['overallPercentage'], 2) }}%</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">Result:</span>
                        <span class="summary-value">{{ $result['resultStatus'] }}</span>
                    </div>
                </div>
                <div class="summary-right">
                    <div class="summary-row">
                        <span class="summary-label">Grade:</span>
                        <span class="summary-value">{{ $result['overallGrade'] ?? 'A' }}</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">GPA:</span>
                        <span class="summary-value">{{ number_format($result['gpa'], 2) }}</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">Rank:</span>
                        <span class="summary-value">-</span>
                    </div>
                </div>
            </div>

            <!-- Grading Scale -->
            <div class="grading-scale">
                Grading Scale: A(90-100%), A-(80-90%), B+(70-80%), B(60-70%), F(0-60%)
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
                <div>Generated on {{ now()->format('F d, Y \a\t g:i A') }}</div>
            </div>
        </div>

    @endforeach
</body>
</html>