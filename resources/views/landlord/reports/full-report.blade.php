<!DOCTYPE html>
<html>

<head>
    <title>Reservation and Booking Report</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-ENjdO4Dr2bkBIFxQpeo3wLq8Fs0I7bgL7iU3n6hOaM9fF4xfsO6x8uH+MZQ5S+0F" crossorigin="anonymous">

    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
   <div style="text-align: center; margin-bottom: 10px;">
    <img src="{{ $logoPath }}" class="logo" style="width: 25%; display: block; margin: 0 auto;">
    <div class="logo-text">
        DormHub
    </div>
</div>

    <h2>Reservation and Booking Report</h2>
<p>Report generated on: {{ \Carbon\Carbon::now('Asia/Manila')->format('F j, Y, g:i a') }}</p>

   <div class="section-title">Bookings</div>
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Tenant Name</th>
            <th>Dorm</th>
            <th>Room</th>
            <th>Contact</th>
                        <th>Status</th>

            <th>Booking Amount</th>
        </tr>
    </thead>
    <tbody>
        @php $bookingTotal = 0; @endphp
        @foreach ($bookings as $index => $b)
            @php $bookingTotal += $b->total_amount; @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $b->firstname }} {{ $b->lastname }}</td>
                <td>{{ $b->room->dorm->dormName ?? 'N/A' }}</td>
                <td>{{ $b->room->roomNumber ?? 'N/A' }}</td>
                <td>{{ $b->contactNumber }}</td>
               <td>
    <span class="badge bg-success badge-success">{{ $b->status }}</span>
</td>

                <td>PHP {{ number_format($b->total_amount, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="6" class="text-end fw-bold">Bookings Total:</td>
            <td class="fw-bold">PHP {{ number_format($bookingTotal, 2) }}</td>
        </tr>
    </tfoot>
</table>

<div class="section-title">Reservations</div>
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Tenant Name</th>
            <th>Dorm</th>
            <th>Room</th>
            <th>Contact</th>
                        <th>Status</th>
            <th>Reservation Amount</th>
        </tr>
    </thead>
    <tbody>
        @php $reservationTotal = 0; @endphp
        @foreach ($reservations as $index => $r)
            @php $reservationTotal += $r->total_amount; @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $r->firstname }} {{ $r->lastname }}</td>
                <td>{{ $r->room->dorm->dormName ?? 'N/A' }}</td>
                <td>{{ $r->room->roomNumber ?? 'N/A' }}</td>
                <td>{{ $r->contactNumber }}</td>
                <td>
    <span class="badge bg-success badge-success">{{ $r->status }}</span>
</td>
                <td>PHP {{ number_format($r->total_amount, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="6" class="text-end fw-bold">Reservations Total:</td>
            <td class="fw-bold">PHP {{ number_format($reservationTotal, 2) }}</td>
        </tr>
    </tfoot>
   
</table>

<div class="income-box">
    Combined Total Income: PHP {{ number_format($bookingTotal + $reservationTotal, 2) }}
</div>
<div class="footer">
    &copy; {{ now()->year }} DormHub. All rights reserved.
</div>
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
        background-color: #e8f6ff;
        border: 1px solid #b3e0ff;
        border-left: 6px solid #3498db;
        padding: 12px 15px;
        border-radius: 6px;
        font-size: 15px;
        font-weight: bold;
        margin: 20px 0;
        text-align: right;
    }

    .section-title {
        background-color: #f4f6f8;
        border-left: 6px solid #3498db;
        padding: 8px 12px;
        font-weight: 600;
        font-size: 14px;
        margin-top: 25px;
        margin-bottom: 10px;
        color: #2c3e50;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 8px;
        font-size: 12px;
    }

    th,
    td {
        border: 1px solid #ddd;
        padding: 8px 10px;
        text-align: left;
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

    tfoot td {
        background-color: #f1f1f1;
        font-weight: 600;
    }
    .logo-text {
    font-family: "Comic Sans MS", cursive, sans-serif;
    font-size: 29px;
    letter-spacing: -3.2px;
    word-spacing: -2.8px;
    color: #4edce2;
    font-weight: 700;
    text-decoration: none;
    font-style: normal;
    font-variant: small-caps;
    text-transform: capitalize;
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

    .badge-success {
        background-color: #28a745; /* green */
    }
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


</body>

</html>
