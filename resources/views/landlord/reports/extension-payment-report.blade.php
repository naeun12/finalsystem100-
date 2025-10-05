<!DOCTYPE html>
<html>
<head>
    <title>Extension Payment Report</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            font-size: 11px;
            color: #333;
            padding: 20px;
            background-color: #fff;
        }
        h2 {
            text-align: center;
            color: #2c3e50;
            font-size: 18px;
            margin-bottom: 5px;
        }
        p {
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        .logo-text {
            font-family: "Comic Sans MS", cursive, sans-serif;
            font-size: 24px;
            letter-spacing: -2px;
            color: #4edce2;
            font-weight: 700;
            margin-top: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            table-layout: fixed;
            word-wrap: break-word;
            font-size: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 4px 6px;
            text-align: left;
            vertical-align: middle;
        }
        th {
            background-color: #3498db;
            color: #fff;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 10px;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .badge {
            display: inline-block;
            padding: 0.15em 0.4em;
            font-size: 10px;
            font-weight: 600;
            color: #fff;
            border-radius: 0.25rem;
            text-align: center;
        }
        .badge-success { background-color: #28a745; }
        .table-container {
            overflow-x: auto;
            width: 100%;
        }
    </style>
</head>
<body>
    <!-- Logo -->
    <div style="text-align: center; margin-bottom: 10px;">
        <img src="{{ $logoPath }}" style="width: 20%; display: block; margin: 0 auto;">
        <div class="logo-text">DormHub</div>
    </div>

    <h2>Extension Payment Report</h2>
    <p>Report generated on: {{ \Carbon\Carbon::now('Asia/Manila')->format('F j, Y, g:i a') }}</p>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tenant Name</th>
                    <th>Email</th>
                    <th>Contact</th>
                    <th>Dorm</th>
                    <th>Room</th>
                    <th>Move-in Date</th>
                    <th>Payment Amount</th>
                    <th>Payment Method</th>
                    <th>Payment Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tenants as $index => $tenant)
                @php
                    $payment = $tenant->payments->first();
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $tenant->firstname }} {{ $tenant->lastname }}</td>
                    <td>{{ Str::limit($tenant->contactEmail, 20) }}</td>
                    <td>{{ $tenant->contactNumber }}</td>
                    <td>{{ Str::limit($tenant->room->dorm->dormName ?? 'N/A', 15) }}</td>
                    <td>{{ $tenant->room->roomNumber ?? 'N/A' }}</td>
                    <td>{{ \Carbon\Carbon::parse($tenant->moveInDate)->format('M d, Y') }}</td>
                    <td>PHP {{ number_format($payment->amount ?? 0, 2) }}</td>
                    <td>{{ $payment->paymentType ?? 'N/A' }}</td>
                    <td>{{ isset($payment->created_at) ? \Carbon\Carbon::parse($payment->created_at)->format('M d, Y') : 'N/A' }}</td>
                    <td><span class="badge badge-success">PAID</span></td>
                </tr>
                @endforeach
            </tbody>
        <tfoot>
    <tr>
        <td colspan="8" style="text-align: right; font-weight: 700; background-color: #e8f6ff; padding: 6px; border-top: 2px solid #3498db;">
            Total Income:
        </td>
        <td colspan="3" style="font-weight: 700; background-color: #e8f6ff; padding: 6px; border-top: 2px solid #3498db;">
            PHP {{ number_format($totalIncome, 2) }}
        </td>
    </tr>
</tfoot>


        </table>
    </div>
    <div class="footer">
    &copy; {{ now()->year }} DormHub. All rights reserved.
</div>
</body>
</html>
<style>
     .footer {
    font-size: 12px;
    color: #666;
    text-align: center;
    margin-top: 30px;        /* space above footer */
    padding: 10px 0;   
    width: 100%;      /* top/bottom padding */
    border-top: 1px solid #ddd; /* subtle separator */
    background-color: #f9f9f9;  /* optional light background */
}
</style>