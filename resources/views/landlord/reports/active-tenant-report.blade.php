<!DOCTYPE html>
<html>
<head>
    <title>Active Tenants Report</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            font-size: 13px;
            color: #333;
            padding: 30px;
            background-color: #fff;
        }

        h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 5px;
            font-size: 22px;
        }

        p {
            text-align: center;
            font-size: 12px;
            color: #666;
        }

        .logo-text {
            font-family: "Comic Sans MS", cursive, sans-serif;
            font-size: 29px;
            letter-spacing: -3.2px;
            word-spacing: -2.8px;
            color: #4edce2;
            font-weight: 700;
            margin-top: 5px;
        }

        table {
            width: 95%;
            margin: 0 auto;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px 10px;
            text-align: left;
            vertical-align: middle;
        }

        th {
            background-color: #3498db;
            color: #fff;
            font-weight: 600;
            text-transform: uppercase;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .badge {
            display: inline-block;
            padding: 0.25em 0.6em;
            font-size: 12px;
            font-weight: 600;
            color: #fff;
            border-radius: 0.25rem;
            text-align: center;
        }

        .badge-success { background-color: #28a745; }
        .badge-secondary { background-color: #6c757d; }
        .badge-warning { background-color: #ffc107; color: #212529; }
        .badge-info { background-color: #17a2b8; }

        tfoot td {
            font-weight: 700;
            background-color: #e8f6ff;
            border-top: 2px solid #3498db;
            padding: 6px;
        }

        .total-label {
            text-align: right;
        }

    </style>
</head>
<body>
    <!-- Logo -->
    <div style="text-align: center; margin-bottom: 10px;">
        <img src="{{ $logoPath }}" style="width: 25%; display: block; margin: 0 auto;">
        <div class="logo-text">DormHub</div>
    </div>

    <h2>Active Tenants Report</h2>
    <p>Report generated on: {{ \Carbon\Carbon::now('Asia/Manila')->format('F j, Y, g:i a') }}</p>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Tenant Name</th>
                <th>Email</th>
                <th>Contact Number</th>
                <th>Dorm Name</th>
                <th>Room No</th>
                <th>Move-in Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tenants as $index => $tenant)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $tenant->firstname }} {{ $tenant->lastname }}</td>
                <td>{{ $tenant->contactEmail }}</td>
                <td>{{ $tenant->contactNumber }}</td>
                <td>{{ $tenant->room->dorm->dormName ?? 'N/A' }}</td>
                <td>{{ $tenant->room->roomNumber ?? 'N/A' }}</td>
                <td>{{ \Carbon\Carbon::parse($tenant->moveInDate)->format('M d, Y') }}</td>
                <td>
                    @php
                        $statusClass = match($tenant->status) {
                            'active' => 'badge-success',
                            'moved_out' => 'badge-secondary',
                            'pending_moveout' => 'badge-warning',
                            'transferring' => 'badge-info',
                            default => 'badge-secondary'
                        };
                    @endphp
                    <span class="badge {{ $statusClass }}">{{ strtoupper(str_replace('_', ' ', $tenant->status)) }}</span>
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="7" class="total-label">Total Tenants:</td>
                <td>{{ $tenants->count() }}</td>
            </tr>
        </tfoot>
       
    </table>
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
