<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulk Progress Reports - {{ $exam->title }}</title>
    <style>
        @page {
            size: A4;
            margin: 15mm;
        }
        
        body {
            font-family: 'Times New Roman', serif;
            margin: 0;
            padding: 0;
            font-size: 12px;
            line-height: 1.2;
            background: #f5f5f5;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
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
        
        .exam-info h1 {
            font-size: 24px;
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
        
        .marksheet-container {
            width: 100%;
            max-width: 210mm;
            margin: 0 auto 30px;
            border: 2px solid #000;
            padding: 10px;
            background: white;
            page-break-after: always;
        }
        
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 1px solid #000;
            padding-bottom: 10px;
        }
        
        .college-logo {
            width: 60px;
            height: 60px;
            float: left;
            margin-right: 15px;
            border-radius: 50%;
        }
        
        .college-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 3px;
            color: #000;
        }
        
        .college-address {
            font-size: 11px;
            margin-bottom: 3px;
        }
        
        .report-title {
            font-size: 16px;
            font-weight: bold;
            margin-top: 10px;
            text-decoration: underline;
        }
        
        .student-info {
            margin: 15px 0;
            overflow: hidden;
        }
        
        .student-info-left {
            float: left;
            width: 60%;
        }
        
        .student-info-right {
            float: right;
            width: 35%;
            text-align: right;
        }
        
        .info-row {
            margin-bottom: 5px;
            font-size: 11px;
        }
        
        .marks-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 10px;
        }
        
        .marks-table th,
        .marks-table td {
            border: 1px solid #000;
            padding: 3px 5px;
            text-align: center;
            vertical-align: middle;
        }
        
        .marks-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 9px;
        }
        
        .subject-name {
            text-align: left !important;
            padding-left: 8px !important;
            font-size: 9px;
        }
        
        .sn-column {
            width: 25px;
        }
        
        .subject-column {
            width: 80px;
        }
        
        .marks-column {
            width: 35px;
        }
        
        .grade-column {
            width: 25px;
        }
        
        .total-row {
            font-weight: bold;
            background-color: #f5f5f5;
        }
        
        .result-section {
            margin: 15px 0;
            overflow: hidden;
        }
        
        .result-left {
            float: left;
            width: 50%;
            font-size: 11px;
        }
        
        .result-right {
            float: right;
            width: 45%;
            font-size: 11px;
        }
        
        .signatures {
            margin-top: 30px;
            overflow: hidden;
        }
        
        .signature-left {
            float: left;
            width: 45%;
            text-align: center;
        }
        
        .signature-right {
            float: right;
            width: 45%;
            text-align: center;
        }
        
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 40px;
            padding-top: 5px;
            font-size: 10px;
        }
        
        .grade-scale {
            margin: 10px 0;
            font-size: 9px;
            text-align: right;
        }
        
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 48px;
            color: rgba(0, 0, 0, 0.05);
            font-weight: bold;
            z-index: -1;
        }
        
        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            body {
                background: white !important;
                padding: 0 !important;
                margin: 0 !important;
                font-size: 12px !important;
                line-height: 1.2 !important;
            }

            .header-controls {
                display: none !important;
            }

            .container {
                padding: 0 !important;
            }

            .marksheet-container {
                border: 2px solid #000 !important;
                box-shadow: none !important;
                page-break-after: always !important;
                margin-bottom: 0 !important;
                padding: 10px !important;
                background: white !important;
                width: 100% !important;
                box-sizing: border-box !important;
            }

            .marks-table {
                width: 100% !important;
                border-collapse: collapse !important;
                margin: 15px 0 !important;
                font-size: 10px !important;
            }

            .marks-table th,
            .marks-table td {
                border: 1px solid #000 !important;
                padding: 3px 5px !important;
                text-align: center !important;
                vertical-align: middle !important;
                background: white !important;
            }

            .marks-table th {
                background-color: #f0f0f0 !important;
                font-weight: bold !important;
                font-size: 9px !important;
            }

            .total-row {
                background-color: #f8f9fa !important;
                font-weight: bold !important;
            }

            .subject-name {
                text-align: left !important;
                font-weight: bold !important;
            }

            .result-section {
                margin: 15px 0 !important;
                font-size: 11px !important;
            }

            .result-left,
            .result-right {
                width: 48% !important;
                display: inline-block !important;
                vertical-align: top !important;
            }

            .signatures {
                margin-top: 30px !important;
                display: flex !important;
                justify-content: space-between !important;
            }

            .signature-block {
                width: 30% !important;
                text-align: center !important;
            }

            .signature-line {
                border-top: 1px solid #000 !important;
                margin-top: 40px !important;
                padding-top: 5px !important;
                font-size: 10px !important;
            }

            .grade-scale {
                font-size: 9px !important;
                margin: 10px 0 !important;
                text-align: center !important;
            }

            .watermark {
                display: none !important;
            }

            /* Ensure all text is black */
            * {
                color: #000 !important;
            }

            /* College header styling */
            .college-name {
                font-size: 16px !important;
                font-weight: bold !important;
                text-align: center !important;
                margin: 5px 0 !important;
            }

            .college-address {
                font-size: 12px !important;
                text-align: center !important;
                margin: 5px 0 !important;
            }

            .marksheet-title {
                font-size: 14px !important;
                font-weight: bold !important;
                text-align: center !important;
                margin: 10px 0 !important;
                text-decoration: underline !important;
            }

            /* Student info styling */
            .student-info {
                margin: 15px 0 !important;
                font-size: 11px !important;
            }

            .info-row {
                margin-bottom: 5px !important;
                font-size: 11px !important;
            }

            /* Force page breaks and avoid breaking inside marksheets */
            .marksheet-container {
                page-break-inside: avoid !important;
            }
        }
        
        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header Controls -->
        <div class="header-controls">
            <div class="exam-info">
                <h1>{{ $exam->title }} - Bulk Progress Reports</h1>
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

        <!-- Individual Progress Reports -->
        @foreach($marksheetData as $data)
        <div class="marksheet-container">
            <!-- Watermark -->
            @if($collegeSettings->show_watermark && $collegeSettings->watermark_text)
            <div class="watermark">{{ $collegeSettings->watermark_text }}</div>
            @endif
            
            <!-- Header -->
            <div class="header clearfix">
                @if($collegeSettings->show_college_logo && $collegeSettings->logo_path)
                    <img src="{{ asset('storage/' . $collegeSettings->logo_path) }}" alt="College Logo" class="college-logo" onerror="this.style.display='none'">
                @endif
                <div class="college-name">{{ $collegeSettings->college_name }}</div>
                <div class="college-address">{{ $collegeSettings->college_address }}</div>
                @if($collegeSettings->affiliation)
                    <div class="college-address">{{ $collegeSettings->affiliation }}</div>
                @endif
                <div class="report-title">Progress Report</div>
            </div>
            
            <!-- Student Information -->
            <div class="student-info clearfix">
                <div class="student-info-left">
                    <div class="info-row">
                        <strong>Name:</strong> {{ $data['student']->user->first_name }} {{ $data['student']->user->last_name }}
                    </div>
                    <div class="info-row">
                        <strong>Class:</strong> {{ $exam->class->name ?? 'N/A' }}
                    </div>
                    <div class="info-row">
                        <strong>Roll No:</strong> {{ $data['student']->student_id }}
                    </div>
                </div>
                <div class="student-info-right">
                    <div class="info-row">
                        <strong>Exam:</strong> {{ $exam->title }}
                    </div>
                    <div class="info-row">
                        <strong>Year:</strong> {{ $exam->academicYear->name ?? date('Y') }}
                    </div>
                    <div class="info-row">
                        <strong>Date:</strong> {{ $exam->exam_date ? $exam->exam_date->format('Y-m-d') : date('Y-m-d') }}
                    </div>
                </div>
            </div>
            
            <!-- Marks Table -->
            <table class="marks-table">
                <thead>
                    <tr>
                        <th rowspan="2" class="sn-column">S.N</th>
                        <th rowspan="2" class="subject-column">Subjects</th>
                        <th colspan="2">Marks</th>
                        <th rowspan="2" class="marks-column">Total Marks</th>
                        <th rowspan="2" class="grade-column">Grades</th>
                    </tr>
                    <tr>
                        <th class="marks-column">Theory</th>
                        <th class="marks-column">Practical</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $serialNumber = 1;
                    @endphp
                    
                    @foreach($data['marks'] as $mark)
                    @php
                        // Get the exam subject configuration for this subject
                        $examSubject = $examSubjects->where('subject_id', $mark->subject_id)->first();

                        // Use actual theory and practical marks from database
                        $theoryMarks = $mark->theory_marks ?? 0;
                        $practicalMarks = $mark->practical_marks ?? 0;
                        $totalMarks = $mark->obtained_marks;
                        $maxMarks = $mark->total_marks;

                        // Get configured maximum marks for this subject
                        $maxTheoryMarks = $examSubject ? $examSubject->theory_marks : 0;
                        $maxPracticalMarks = $examSubject ? $examSubject->practical_marks : 0;
                        $subjectTotalMax = $maxTheoryMarks + $maxPracticalMarks;

                        $percentage = $maxMarks > 0 ? ($totalMarks / $maxMarks) * 100 : 0;
                        $grade = $exam->getGradeByPercentage($percentage);
                    @endphp
                    <tr>
                        <td>{{ $serialNumber++ }}</td>
                        <td class="subject-name">
                            {{ $mark->subject->name }}
                            @if($maxTheoryMarks > 0 && $maxPracticalMarks > 0)
                                <br><small style="font-size: 8px; color: #666;">(Theory: {{ $maxTheoryMarks }}, Practical: {{ $maxPracticalMarks }})</small>
                            @elseif($maxTheoryMarks > 0)
                                <br><small style="font-size: 8px; color: #666;">(Theory: {{ $maxTheoryMarks }})</small>
                            @endif
                        </td>
                        <td>{{ $theoryMarks > 0 ? number_format($theoryMarks, 0) : '-' }}</td>
                        <td>{{ $practicalMarks > 0 ? number_format($practicalMarks, 0) : '-' }}</td>
                        <td>{{ number_format($totalMarks, 0) }}/{{ number_format($subjectTotalMax, 0) }}</td>
                        <td>{{ $grade ? $grade->grade_letter : '-' }}</td>
                    </tr>
                    @endforeach
                    
                    <!-- Total Row -->
                    @php
                        $totalTheoryMarks = $data['marks']->sum('theory_marks');
                        $totalPracticalMarks = $data['marks']->sum('practical_marks');
                    @endphp
                    <tr class="total-row">
                        <td colspan="2"><strong>Total</strong></td>
                        <td><strong>{{ $totalTheoryMarks > 0 ? number_format($totalTheoryMarks, 0) : '-' }}</strong></td>
                        <td><strong>{{ $totalPracticalMarks > 0 ? number_format($totalPracticalMarks, 0) : '-' }}</strong></td>
                        <td><strong>{{ number_format($data['totalObtained'], 0) }}/{{ number_format($data['totalMaximum'], 0) }}</strong></td>
                        <td><strong>{{ number_format($data['totalMaximum'], 0) }}</strong></td>
                    </tr>
                </tbody>
            </table>
            
            <!-- Result Section -->
            <div class="result-section clearfix">
                <div class="result-left">
                    <div><strong>Grand Total:</strong> {{ number_format($data['totalObtained'], 0) }}</div>
                    <div><strong>Percentage:</strong> {{ number_format($data['overallPercentage'], 1) }}%</div>
                    <div><strong>Result:</strong> {{ $data['resultStatus'] }}</div>
                </div>
                <div class="result-right">
                    <div><strong>Grade:</strong> {{ $data['overallGrade'] ? $data['overallGrade']->grade_letter : 'N/A' }}</div>
                    <div><strong>GPA:</strong> {{ number_format($data['gpa'], 2) }}</div>
                    <div><strong>Rank:</strong> -</div>
                </div>
            </div>
            
            <!-- Grade Scale -->
            @if($collegeSettings->show_grade_scale && $gradingSystem && $gradingSystem->gradeScales->count() > 0)
            <div class="grade-scale">
                <strong>Grading Scale:</strong>
                @foreach($gradingSystem->gradeScales->take(6) as $scale)
                    {{ $scale->grade_letter }}({{ number_format($scale->min_percentage, 0) }}-{{ number_format($scale->max_percentage, 0) }}%){{ !$loop->last ? ', ' : '' }}
                @endforeach
            </div>
            @endif
            
            <!-- Signatures -->
            <div class="signatures clearfix">
                <div class="signature-left">
                    <div class="signature-line">
                        {{ $collegeSettings->class_teacher_name ?? 'Class Teacher' }}
                    </div>
                </div>
                <div class="signature-right">
                    <div class="signature-line">
                        {{ $collegeSettings->principal_name ?? 'Principal' }}
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            @if($collegeSettings->result_footer)
            <div style="text-align: center; margin-top: 20px; font-size: 10px; color: #666;">
                {{ $collegeSettings->result_footer }}
            </div>
            @endif
        </div>
        @endforeach
    </div>
</body>
</html>
