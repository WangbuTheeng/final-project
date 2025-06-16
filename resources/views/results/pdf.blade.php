<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $exam->title }} - Results</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        
        .college-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #2563eb;
        }
        
        .college-address {
            font-size: 14px;
            color: #666;
            margin-bottom: 15px;
        }
        
        .exam-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #1f2937;
        }
        
        .exam-details {
            font-size: 14px;
            color: #4b5563;
        }
        
        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            background-color: #f8fafc;
            padding: 15px;
            border-radius: 8px;
        }
        
        .info-item {
            text-align: center;
        }
        
        .info-label {
            font-size: 11px;
            color: #6b7280;
            text-transform: uppercase;
            font-weight: bold;
        }
        
        .info-value {
            font-size: 16px;
            font-weight: bold;
            color: #1f2937;
            margin-top: 2px;
        }
        
        .results-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 11px;
        }
        
        .results-table th {
            background-color: #374151;
            color: white;
            padding: 8px 6px;
            text-align: center;
            font-weight: bold;
            border: 1px solid #d1d5db;
        }
        
        .results-table td {
            padding: 6px;
            border: 1px solid #d1d5db;
            text-align: center;
        }
        
        .results-table tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }
        
        .results-table tbody tr:hover {
            background-color: #f3f4f6;
        }
        
        .rank-cell {
            font-weight: bold;
        }
        
        .rank-1 { color: #d97706; }
        .rank-2 { color: #6b7280; }
        .rank-3 { color: #dc2626; }
        
        .student-name {
            text-align: left;
            font-weight: 600;
        }
        
        .student-id {
            font-size: 10px;
            color: #6b7280;
        }
        
        .grade-a { background-color: #dcfce7; color: #166534; }
        .grade-b { background-color: #dbeafe; color: #1e40af; }
        .grade-c { background-color: #fef3c7; color: #92400e; }
        .grade-d { background-color: #fed7aa; color: #c2410c; }
        .grade-f { background-color: #fecaca; color: #dc2626; }
        
        .pass { background-color: #dcfce7; color: #166534; font-weight: bold; }
        .fail { background-color: #fecaca; color: #dc2626; font-weight: bold; }
        
        .statistics {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
        }
        
        .stat-box {
            background-color: #f8fafc;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            width: 30%;
            border: 1px solid #e5e7eb;
        }
        
        .stat-title {
            font-size: 12px;
            color: #6b7280;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .stat-value {
            font-size: 18px;
            font-weight: bold;
            color: #1f2937;
        }
        
        .grading-scale {
            margin-top: 30px;
            background-color: #f8fafc;
            padding: 15px;
            border-radius: 8px;
        }
        
        .grading-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #1f2937;
        }
        
        .grading-table {
            width: 100%;
            font-size: 11px;
        }
        
        .grading-table td {
            padding: 4px 8px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
            padding-top: 15px;
        }
        
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="college-name">Bajra International College</div>
        <div class="college-address">Affiliated to Tribhuvan University</div>
        <div class="exam-title">{{ $exam->title }} - Comprehensive Results</div>
        <div class="exam-details">
            {{ $exam->class->course->title }} | {{ $exam->class->name }} | {{ $exam->academicYear->name }}
            <br>
            Exam Date: {{ $exam->exam_date->format('F d, Y') }} | Generated: {{ now()->format('F d, Y g:i A') }}
        </div>
    </div>

    <!-- Statistics -->
    <div class="info-section">
        <div class="info-item">
            <div class="info-label">Total Students</div>
            <div class="info-value">{{ $classStats['total_students'] }}</div>
        </div>
        <div class="info-item">
            <div class="info-label">Passed</div>
            <div class="info-value" style="color: #059669;">{{ $classStats['passed'] }}</div>
        </div>
        <div class="info-item">
            <div class="info-label">Failed</div>
            <div class="info-value" style="color: #dc2626;">{{ $classStats['failed'] }}</div>
        </div>
        <div class="info-item">
            <div class="info-label">Pass Rate</div>
            <div class="info-value" style="color: #7c3aed;">{{ number_format($classStats['pass_rate'], 1) }}%</div>
        </div>
        <div class="info-item">
            <div class="info-label">Class Average</div>
            <div class="info-value">{{ number_format($classStats['average_percentage'], 1) }}%</div>
        </div>
        <div class="info-item">
            <div class="info-label">Highest Score</div>
            <div class="info-value" style="color: #059669;">{{ number_format($classStats['highest_percentage'], 1) }}%</div>
        </div>
    </div>

    <!-- Results Table -->
    <table class="results-table">
        <thead>
            <tr>
                <th style="width: 8%;">Rank</th>
                <th style="width: 25%;">Student Name</th>
                <th style="width: 12%;">Student ID</th>
                <th style="width: 15%;">Total Marks</th>
                <th style="width: 12%;">Percentage</th>
                <th style="width: 8%;">Grade</th>
                <th style="width: 8%;">GPA</th>
                <th style="width: 12%;">Result</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sortedResults as $result)
                <tr>
                    <td class="rank-cell {{ $result['rank'] <= 3 ? 'rank-' . $result['rank'] : '' }}">
                        {{ $result['rank'] }}
                        @if($result['rank'] <= 3)
                            🏆
                        @endif
                    </td>
                    <td class="student-name">
                        {{ $result['student']->user->first_name }} {{ $result['student']->user->last_name }}
                    </td>
                    <td class="student-id">{{ $result['student']->student_id }}</td>
                    <td>{{ number_format($result['total_obtained'], 1) }}/{{ number_format($result['total_maximum'], 1) }}</td>
                    <td><strong>{{ number_format($result['percentage'], 2) }}%</strong></td>
                    <td class="grade-{{ strtolower($result['letter_grade']) }}">
                        <strong>{{ $result['letter_grade'] }}</strong>
                    </td>
                    <td><strong>{{ number_format($result['gpa'], 2) }}</strong></td>
                    <td class="{{ strtolower($result['result_status']) }}">
                        {{ $result['result_status'] }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Additional Statistics -->
    <div class="statistics">
        <div class="stat-box">
            <div class="stat-title">Grade Distribution</div>
            <div style="font-size: 10px; text-align: left;">
                @php
                    $gradeDistribution = $sortedResults->groupBy('letter_grade')->map->count();
                @endphp
                @foreach(['A', 'B+', 'B', 'C+', 'C', 'D', 'F'] as $grade)
                    <div>{{ $grade }}: {{ $gradeDistribution->get($grade, 0) }} students</div>
                @endforeach
            </div>
        </div>
        
        <div class="stat-box">
            <div class="stat-title">Performance Analysis</div>
            <div style="font-size: 10px; text-align: left;">
                <div>Excellent (80%+): {{ $sortedResults->where('percentage', '>=', 80)->count() }}</div>
                <div>Good (60-79%): {{ $sortedResults->whereBetween('percentage', [60, 79.99])->count() }}</div>
                <div>Average (40-59%): {{ $sortedResults->whereBetween('percentage', [40, 59.99])->count() }}</div>
                <div>Below Average (&lt;40%): {{ $sortedResults->where('percentage', '<', 40)->count() }}</div>
            </div>
        </div>
        
        <div class="stat-box">
            <div class="stat-title">GPA Analysis</div>
            <div style="font-size: 10px; text-align: left;">
                <div>4.0 GPA: {{ $sortedResults->where('gpa', 4.0)->count() }}</div>
                <div>3.6+ GPA: {{ $sortedResults->where('gpa', '>=', 3.6)->count() }}</div>
                <div>3.0+ GPA: {{ $sortedResults->where('gpa', '>=', 3.0)->count() }}</div>
                <div>2.0+ GPA: {{ $sortedResults->where('gpa', '>=', 2.0)->count() }}</div>
                <div>Below 2.0: {{ $sortedResults->where('gpa', '<', 2.0)->count() }}</div>
            </div>
        </div>
    </div>

    <!-- Grading Scale -->
    <div class="grading-scale">
        <div class="grading-title">Tribhuvan University Grading Scale</div>
        <table class="grading-table">
            <tr>
                <td><strong>Grade</strong></td>
                <td><strong>Percentage</strong></td>
                <td><strong>GPA</strong></td>
                <td><strong>Description</strong></td>
            </tr>
            <tr>
                <td>A</td>
                <td>80-100%</td>
                <td>4.0</td>
                <td>Excellent</td>
            </tr>
            <tr>
                <td>B+</td>
                <td>70-79%</td>
                <td>3.6</td>
                <td>Very Good</td>
            </tr>
            <tr>
                <td>B</td>
                <td>60-69%</td>
                <td>3.2</td>
                <td>Good</td>
            </tr>
            <tr>
                <td>C+</td>
                <td>50-59%</td>
                <td>2.8</td>
                <td>Above Average</td>
            </tr>
            <tr>
                <td>C</td>
                <td>45-49%</td>
                <td>2.4</td>
                <td>Average</td>
            </tr>
            <tr>
                <td>D</td>
                <td>40-44%</td>
                <td>2.0</td>
                <td>Below Average</td>
            </tr>
            <tr>
                <td>F</td>
                <td>Below 40%</td>
                <td>0.0</td>
                <td>Fail</td>
            </tr>
        </table>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>This is a computer-generated document. No signature is required.</p>
        <p>Generated on {{ now()->format('F d, Y \a\t g:i A') }} | Bajra International College Management System</p>
    </div>
</body>
</html>
