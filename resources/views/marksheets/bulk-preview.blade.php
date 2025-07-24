<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulk Marksheets Preview - {{ $exam->title }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .header-controls {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .exam-info {
            flex: 1;
        }
        .exam-title {
            font-size: 24px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 5px;
        }
        .exam-details {
            color: #6b7280;
            font-size: 14px;
        }
        .controls {
            display: flex;
            gap: 10px;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-primary {
            background-color: #2563eb;
            color: white;
        }
        .btn-secondary {
            background-color: #6b7280;
            color: white;
        }
        .btn:hover {
            opacity: 0.9;
        }
        .grading-info {
            background: #f0f9ff;
            border: 1px solid #0ea5e9;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .grading-title {
            font-weight: bold;
            color: #0369a1;
            margin-bottom: 8px;
        }
        .marksheet {
            background: white;
            margin-bottom: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            page-break-after: always;
            overflow: hidden;
        }
        .marksheet-header {
            text-align: center;
            border-bottom: 3px solid #2563eb;
            padding: 20px;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        }
        .college-logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 15px;
            border-radius: 50%;
            object-fit: cover;
        }
        .college-name {
            font-size: 24px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 5px;
        }
        .college-address {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 15px;
        }
        .marksheet-title {
            font-size: 20px;
            font-weight: bold;
            color: #374151;
        }
        .student-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            padding: 20px;
            background: #f8fafc;
        }
        .info-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px dotted #d1d5db;
        }
        .info-label {
            font-weight: 600;
            color: #374151;
        }
        .info-value {
            color: #6b7280;
        }
        .marks-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .marks-table th,
        .marks-table td {
            border: 1px solid #d1d5db;
            padding: 12px;
            text-align: center;
        }
        .marks-table th {
            background-color: #f3f4f6;
            font-weight: 600;
            color: #374151;
        }
        .marks-table tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .total-row {
            background-color: #e5e7eb !important;
            font-weight: bold;
        }
        .result-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            padding: 20px;
            background: #f8fafc;
            border-top: 2px solid #e5e7eb;
        }
        .summary-item {
            text-align: center;
            padding: 15px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        .summary-label {
            font-size: 12px;
            color: #6b7280;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .summary-value {
            font-size: 18px;
            font-weight: bold;
            color: #1f2937;
        }
        .pass {
            color: #059669;
        }
        .fail {
            color: #dc2626;
        }
        .signatures {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            padding: 30px 20px;
            border-top: 1px solid #e5e7eb;
        }
        .signature-block {
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #374151;
            margin-top: 40px;
            padding-top: 5px;
            font-size: 12px;
            color: #6b7280;
        }
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .header-controls {
                display: none;
            }
            .grading-info {
                display: none;
            }
            .marksheet {
                box-shadow: none;
                margin-bottom: 0;
                page-break-after: always;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header Controls -->
        <div class="header-controls">
            <div class="exam-info">
                <div class="exam-title">{{ $exam->title }} - Bulk Marksheets Preview</div>
                <div class="exam-details">
                    <strong>Class:</strong> {{ $exam->class->name }} | 
                    <strong>Academic Year:</strong> {{ $exam->academicYear->name }} | 
                    <strong>Date:</strong> {{ $exam->exam_date->format('F j, Y') }} |
                    <strong>Students:</strong> {{ count($marksheetData) }}
                </div>
            </div>
            <div class="controls">
                <button onclick="window.print()" class="btn btn-primary">
                    <i class="fas fa-print"></i> Print All
                </button>
                <a href="{{ route('marksheets.bulk', $exam) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <!-- Grading System Information -->
        @if($gradingSystem)
        <div class="grading-info">
            <div class="grading-title">
                <i class="fas fa-graduation-cap"></i> Grading System: {{ $gradingSystem->name }}
            </div>
            <div>
                <strong>Description:</strong> {{ $gradingSystem->description ?? 'Standard grading system' }} |
                <strong>Pass Percentage:</strong> {{ \App\Models\CollegeSetting::getPassPercentage() }}%
            </div>
        </div>
        @endif

        <!-- Individual Marksheets -->
        @foreach($marksheetData as $data)
        <div class="marksheet">
            <!-- Header -->
            <div class="marksheet-header">
                @if($collegeSettings->logo_path)
                    <img src="{{ asset('storage/' . $collegeSettings->logo_path) }}" alt="College Logo" class="college-logo">
                @endif
                <div class="college-name">{{ $collegeSettings->college_name }}</div>
                <div class="college-address">{{ $collegeSettings->college_address }}</div>
                @if($collegeSettings->result_header)
                    <div style="margin: 10px 0; font-style: italic;">{{ $collegeSettings->result_header }}</div>
                @endif
                <div class="marksheet-title">STUDENT MARKSHEET</div>
            </div>

            <!-- Student Information -->
            <div class="student-info">
                <div class="info-group">
                    <div class="info-item">
                        <span class="info-label">Student Name:</span>
                        <span class="info-value">{{ $data['student']->user->first_name }} {{ $data['student']->user->last_name }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Student ID:</span>
                        <span class="info-value">{{ $data['student']->student_id }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Class:</span>
                        <span class="info-value">{{ $exam->class->name }}</span>
                    </div>
                </div>
                <div class="info-group">
                    <div class="info-item">
                        <span class="info-label">Exam:</span>
                        <span class="info-value">{{ $exam->title }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Date:</span>
                        <span class="info-value">{{ $exam->exam_date->format('F j, Y') }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Academic Year:</span>
                        <span class="info-value">{{ $exam->academicYear->name }}</span>
                    </div>
                </div>
            </div>

            <!-- Marks Table -->
            <table class="marks-table">
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th>Theory Marks</th>
                        <th>Practical Marks</th>
                        <th>Total Obtained</th>
                        <th>Total Marks</th>
                        <th>Percentage</th>
                        <th>Grade</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['marks'] as $mark)
                    <tr>
                        <td style="text-align: left;">{{ $mark->subject->name }}</td>
                        <td>{{ $mark->theory_marks ?? '-' }}</td>
                        <td>{{ $mark->practical_marks ?? '-' }}</td>
                        <td>{{ number_format($mark->obtained_marks, 2) }}</td>
                        <td>{{ number_format($mark->total_marks, 2) }}</td>
                        <td>{{ number_format(($mark->obtained_marks / $mark->total_marks) * 100, 2) }}%</td>
                        <td>
                            @php
                                $percentage = ($mark->obtained_marks / $mark->total_marks) * 100;
                                $grade = $exam->getGradeByPercentage($percentage);
                            @endphp
                            {{ $grade ? $grade->grade_letter : 'N/A' }}
                        </td>
                    </tr>
                    @endforeach
                    <tr class="total-row">
                        <td><strong>TOTAL</strong></td>
                        <td>-</td>
                        <td>-</td>
                        <td><strong>{{ number_format($data['totalObtained'], 2) }}</strong></td>
                        <td><strong>{{ number_format($data['totalMaximum'], 2) }}</strong></td>
                        <td><strong>{{ number_format($data['overallPercentage'], 2) }}%</strong></td>
                        <td><strong>{{ $data['overallGrade'] ? $data['overallGrade']->grade_letter : 'N/A' }}</strong></td>
                    </tr>
                </tbody>
            </table>

            <!-- Result Summary -->
            <div class="result-summary">
                <div class="summary-item">
                    <div class="summary-label">Overall Percentage</div>
                    <div class="summary-value">{{ number_format($data['overallPercentage'], 2) }}%</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Grade</div>
                    <div class="summary-value">{{ $data['overallGrade'] ? $data['overallGrade']->grade_letter : 'N/A' }}</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">GPA</div>
                    <div class="summary-value">{{ number_format($data['gpa'], 2) }}</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Result</div>
                    <div class="summary-value {{ strtolower($data['resultStatus']) }}">{{ $data['resultStatus'] }}</div>
                </div>
            </div>

            <!-- Signatures -->
            <div class="signatures">
                <div class="signature-block">
                    <div class="signature-line">Class Teacher</div>
                </div>
                <div class="signature-block">
                    <div class="signature-line">Exam Controller</div>
                </div>
                <div class="signature-block">
                    <div class="signature-line">Principal</div>
                </div>
            </div>

            @if($collegeSettings->result_footer)
            <div style="text-align: center; padding: 15px; font-size: 12px; color: #6b7280; border-top: 1px solid #e5e7eb;">
                {{ $collegeSettings->result_footer }}
            </div>
            @endif
        </div>
        @endforeach
    </div>

    <script>
        // Add print functionality
        function printMarksheets() {
            window.print();
        }
    </script>
</body>
</html>
