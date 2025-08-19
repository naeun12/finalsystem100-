<!DOCTYPE html>
<html>

<head>
    <title>Landlord Report</title>
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
            color: #2c3e50;
            margin-bottom: 5px;
            font-size: 22px;
        }

        p {
            margin-top: 0;
            font-size: 12px;
            color: #666;
        }

        .income-box {
            background-color: #f0f8ff;
            border: 1px solid #cce7ff;
            border-left: 5px solid #3498db;
            padding: 12px 15px;
            border-radius: 6px;
            font-size: 15px;
            font-weight: bold;
            margin: 20px 0;
        }

        .section-title {
            background-color: #f8f9fa;
            border-left: 4px solid #3498db;
            padding: 6px 12px;
            font-weight: 600;
            font-size: 14px;
            margin-top: 25px;
            margin-bottom: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            font-size: 12px;
        }

        th,
        td {
            border: 1px solid #e0e0e0;
            padding: 8px 10px;
            text-align: left;
        }

        th {
            background-color: #f6f9fc;
            font-weight: 600;
            color: #34495e;
        }

        tr:nth-child(even) {
            background-color: #fafafa;
        }
    </style>
</head>

<body>
    <h2>Landlord Full Report</h2>
    <p>Date Generated: {{ now()->format('F d, Y - h:i A') }}</p>

    <div class="income-box">
        Total Income: PHP {{ number_format($totalIncome, 2) }}
    </div>

    <div class="section-title">Bookings</div>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Tenant Name</th>
                <th>Dorm</th>
                <th>Room</th>
                <th>Contact</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($bookings as $index => $b)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $b->firstname }} {{ $b->lastname }}</td>
                    <td>{{ $b->room->dorm->dormName ?? 'N/A' }}</td>
                    <td>{{ $b->room->roomNumber ?? 'N/A' }}</td>
                    <td>{{ $b->contactNumber }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">Reservations</div>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Tenant Name</th>
                <th>Dorm</th>
                <th>Room</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reservations as $index => $r)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $r->firstname }} {{ $r->lastname }}</td>
                    <td>{{ $r->room->dorm->dormName ?? 'N/A' }}</td>
                    <td>{{ $r->room->roomNumber ?? 'N/A' }}</td>
                    <td>{{ $r->status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
