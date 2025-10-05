<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Subscription Report</title>
    <style>
        body { font-family: sans-serif; margin: 20px; }

        /* Header */
        .header { text-align: center; margin-bottom: 30px; }
        .logo { width: 120px; margin-bottom: 10px; }
        h2 { margin: 0; font-size: 24px; }
        p { margin: 0; font-size: 14px; color: #555; }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }

        th {
            background-color: #0d6efd; /* Bootstrap primary color */
            color: white;
            font-weight: bold;
        }

        /* Alternate row shading */
        tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        tbody tr:hover {
            background-color: #e2e6ea;
        }

        /* Footer / Note */
        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #888;
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- Header -->
  <!-- Header -->
<div class="header" style="text-align: center;">
    <img src="{{ $logoPath }}" class="logo" style="display: block; margin: 0 auto;">
    <h2>Subscription Report</h2>
    <p>Generated: {{ now()->format('F d, Y h:i A') }}</p>
</div>


    <!-- Table -->
    <!-- Table -->
<table>
    <thead>
        <tr>
            <th>Month</th>
            <th>Subscribers</th>
            <th>Income (PHP)</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($subscriptions as $sub)
            <tr>
                <td>{{ $sub->month }}</td>
                <td>{{ $sub->count }}</td>
                <td>PHP {{ number_format($sub->total_amount, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2" style="text-align: right; font-weight: bold; background-color: #e8f6ff; padding: 6px; border-top: 2px solid #0d6efd;">
                Total Income:
            </td>
            <td style="font-weight: bold; background-color: #e8f6ff; padding: 6px; border-top: 2px solid #0d6efd;">
                PHP {{ number_format($totalIncome, 2) }}
            </td>
        </tr>
    </tfoot>
</table>

    <div class="footer">
        &copy; {{ now()->year }} DormHub. All rights reserved.
    </div>
</body>
</html>
