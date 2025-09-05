<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Payment Receipt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            margin: 0;
            padding: 0;
        }

        .header {
            background-color: #3e4a61;
            color: white;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header .left {
            font-size: 14px;
        }

        .header .right {
            text-align: right;
            font-size: 14px;
        }

        .receipt-title {
            font-weight: bold;
            font-size: 18px;
        }

        .receipt-body {
            background-color: #f2d0c4;
            padding: 20px;
        }

        .row {
            margin-bottom: 8px;
        }

        .label {
            font-weight: bold;
            display: inline-block;
            width: 80px;
        }

        .options {
            margin-top: 10px;
        }

        .options label {
            margin-right: 15px;
        }

        .signature {
            margin-top: 40px;
            text-align: right;
            font-style: italic;
        }

        .verification {
            margin-top: 30px;
            padding: 10px;
            border: 2px dashed #3e4a61;
            background-color: #fff;
            font-weight: bold;
            color: #3e4a61;
            text-align: center;
        }
    </style>
</head>

<body>

    <!-- HEADER -->
    <div class="header">
        <div class="left">
            <div><strong>{{ $tenant->room->dorm->dormName }}</strong></div>
            <div>Address: {{ $tenant->room->dorm->address }}</div>
            <div>Mail: {{ $tenant->room->landlord->email }}</div>
            <div>Phone: {{ $tenant->room->landlord->phoneNumber }}</div>
        </div>
        <div class="right">
            <div class="receipt-title">RECEIPT</div>
            <div>No. {{ str_pad($tenant->approvedID, 6, '0', STR_PAD_LEFT) }}</div>
        </div>
    </div>

    <!-- BODY -->
    <div class="receipt-body">
        <div class="row">
            <span class="label">Date:</span> {{ \Carbon\Carbon::now()->format('Y-m-d') }}
        </div>
        <div class="row">
            <span class="label">From:</span> {{ $tenant->firstname }} {{ $tenant->lastname }}
        </div>
        <div class="row">
            <span class="label">PHP</span> {{ number_format($totalPaid, 2) }}
        </div>
        <div class="row">
            <span class="label">Room #</span> {{ $tenant->room->roomNumber }}
        </div>
        <div class="row">
            <span class="label">Room Type</span> {{ $tenant->room->roomType }}
        </div>

        <div class="options">
            <input type="radio" style="background: transparent" checked> For {{$tenant->source}} 
            
        </div>

        <div class="row"><span class="label">ACCT.:</span> #{{ $tenant->approvedID }}</div>
        <div class="row"><span class="label">PAID:</span> PHP{{ number_format($totalPaid, 2) }}</div>
        <div class="row"><span class="label">DUE:</span> PHP{{ number_format($balance, 2) }}</div>


        <div class="options">
            <label>
                <input type="checkbox" style="background: transparent"
                    {{ strtolower($tenant->paymentType) == 'gcash' ? 'checked' : '' }}> Gcash
            </label>
         
           
        </div>

        <div class="signature">Authorized Signature</div>

        <div class="verification">
            ✅ Tagged by Landlord for Verification
        </div>
    </div>

</body>

</html>
