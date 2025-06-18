<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Fee Statement - {{ $student->user->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }
        
        .header h2 {
            margin: 5px 0 0 0;
            font-size: 16px;
            color: #666;
            font-weight: normal;
        }
        
        .student-info {
            background-color: #f8f9fa;
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
        }
        
        .student-info h3 {
            margin: 0 0 10px 0;
            font-size: 16px;
            color: #333;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        
        .info-label {
            font-weight: bold;
            color: #555;
        }
        
        .summary-cards {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        
        .summary-card {
            flex: 1;
            text-align: center;
            padding: 15px;
            margin: 0 5px;
            border: 1px solid #ddd;
            background-color: #f8f9fa;
        }
        
        .summary-card h4 {
            margin: 0 0 5px 0;
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
        }
        
        .summary-card .amount {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }
        
        .summary-card.outstanding .amount {
            color: #dc3545;
        }
        
        .summary-card.paid .amount {
            color: #28a745;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
        }
        
        td {
            font-size: 11px;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .status {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status.paid {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status.partially-paid {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status.overdue {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .status.sent {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        
        .status.draft {
            background-color: #e2e3e5;
            color: #383d41;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
        
        .no-data {
            text-align: center;
            padding: 40px;
            color: #666;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Student Fee Statement</h1>
        <h2>{{ $student->user->name }} ({{ $student->admission_number }})</h2>
    </div>

    <div class="student-info">
        <h3>Student Information</h3>
        <div class="info-row">
            <span class="info-label">Name:</span>
            <span>{{ $student->user->name }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Admission Number:</span>
            <span>{{ $student->admission_number }}</span>
        </div>
        @if($student->user->email)
        <div class="info-row">
            <span class="info-label">Email:</span>
            <span>{{ $student->user->email }}</span>
        </div>
        @endif
        @if($student->user->phone)
        <div class="info-row">
            <span class="info-label">Phone:</span>
            <span>{{ $student->user->phone }}</span>
        </div>
        @endif
        @if($student->faculty)
        <div class="info-row">
            <span class="info-label">Faculty:</span>
            <span>{{ $student->faculty->name }}</span>
        </div>
        @endif
        @if($student->department)
        <div class="info-row">
            <span class="info-label">Department:</span>
            <span>{{ $student->department->name }}</span>
        </div>
        @endif
    </div>

    <div class="summary-cards">
        <div class="summary-card">
            <h4>Total Billed</h4>
            <div class="amount">₹{{ number_format($totalBilled, 2) }}</div>
        </div>
        <div class="summary-card paid">
            <h4>Total Paid</h4>
            <div class="amount">₹{{ number_format($totalPaid, 2) }}</div>
        </div>
        <div class="summary-card {{ $outstandingBalance > 0 ? 'outstanding' : '' }}">
            <h4>Outstanding Balance</h4>
            <div class="amount">₹{{ number_format($outstandingBalance, 2) }}</div>
        </div>
    </div>

    @if($invoices->count() > 0)
    <table>
        <thead>
            <tr>
                <th>Invoice Number</th>
                <th>Date</th>
                <th>Academic Year</th>
                <th class="text-right">Amount</th>
                <th class="text-right">Paid</th>
                <th class="text-right">Balance</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoices as $invoice)
            <tr>
                <td>{{ $invoice->invoice_number }}</td>
                <td>{{ $invoice->issue_date->format('M d, Y') }}</td>
                <td>{{ $invoice->academicYear->name }}</td>
                <td class="text-right">₹{{ number_format($invoice->total_amount, 2) }}</td>
                <td class="text-right">₹{{ number_format($invoice->amount_paid, 2) }}</td>
                <td class="text-right">₹{{ number_format($invoice->balance, 2) }}</td>
                <td class="text-center">
                    <span class="status {{ str_replace('_', '-', $invoice->status) }}">
                        {{ ucfirst(str_replace('_', ' ', $invoice->status)) }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="no-data">
        No invoices found for this student.
    </div>
    @endif

    <div class="footer">
        <p>This statement was generated on {{ now()->format('M d, Y \a\t g:i A') }}</p>
        <p>For any queries, please contact the finance office.</p>
    </div>
</body>
</html>
