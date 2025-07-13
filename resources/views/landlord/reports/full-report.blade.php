<!DOCTYPE html>
<html>

<head>
    <title>Landlord Report</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            font-size: 12px;
            color: #333;
            padding: 20px;
            background-color: #fff;
        }

        h2,
        h4 {
            color: #2c3e50;
            margin-bottom: 5px;
        }

        p {
            margin-top: 0;
            font-size: 11px;
        }

        hr {
            margin: 20px 0;
            border: none;
            border-top: 1px solid #ccc;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 11px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.05);
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f6f9fc;
            font-weight: 600;
            color: #34495e;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .section-title {
            background-color: #ecf0f1;
            padding: 6px 10px;
            border-left: 4px solid #3498db;
            border-radius: 4px;
            margin-bottom: 8px;
        }

        .income-box {
            background-color: #dff9fb;
            padding: 10px;
            border-left: 5px solid #22a6b3;
            font-weight: bold;
            font-size: 13px;
            border-radius: 4px;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <h2>Landlord Full Report</h2>
    <p>Date Generated: {{ now()->format('F d, Y - h:i A') }}</p>

    <div class="income-box">
        <h4>Total Income: PHP{{ number_format($totalIncome, 2) }}</h4>
    </div>

    <div class="section-title"> Bookings</div>
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

    <div class="section-title"> Reservations</div>
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
