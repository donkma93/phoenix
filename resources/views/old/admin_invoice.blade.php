<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Invoice</title>

    <style>
        html, body {
            padding: 0;
            margin: 0;
        }
        * {
            box-sizing: border-box
        }
        .invoice-wrapper {
            margin: auto;
            width: 624px;
        }
        .invoice-divider {
            display: block;
            height: 22px;
            background: black;
        }
        .invoice-info-table {
            border-collapse: collapse;
            width: 100%;
        }
        .invoice-info-table tr.odd {
            background-color: #d9d9d9;
        }
        .invoice-info-table tr th,
        .invoice-info-table tr td {
            vertical-align: top;
            border: 1px solid black;
            padding: 0 6px;
            text-align: center;
        }
        .invoice-info-table tr th {
            height: 22px;
            line-height: 22px;
            font-weight: 500;
            color: white;
            background-color: black;
        }
        .text-left {
            text-align: left !important;
        }
        .abg-white {
            background: white;
        }
        .abg-black {
            background: black;
        }
        .atext-black {
            color: black;
        }
        .atext-white {
            color: white;
        }
        .font-14 {
            font-size: 14px;
            line-height: 22px;
        }
        .font-30 {
            font-size: 30px;
        }
        .font-48 {
            font-size: 48px;
            line-height: 110px;
        }
        .flex-center {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .justify-content-between {
            justify-content: space-between;
        }
        .text-uppercase {
            text-transform: uppercase;
        }
        .text-center {
            text-align: center
        }
        .fw-medium {
            font-weight: 700;
        }
        .apx-8 {
            padding-right: 8px;
            padding-left: 8px;
        }
        .apx-24 {
            padding-right: 24px;
            padding-left: 24px;
        }
        .apy-32 {
            padding-top: 32px;
            padding-bottom: 32px;
        }
        .amy-32 {
            margin-top: 32px;
            margin-bottom: 32px;
        }
        .amb-12 {
            margin-bottom: 12px;
        }
        .amb-32 {
            margin-bottom: 32px;
        }
        .amb-48 {
            margin-bottom: 48px;
        }
        .amt-32 {
            margin-top: 32px;
        }
        .m-0 {
            margin: 0;
        }
        .col-7 {
            width: 60%;
            float: left;
        }
        .col-5 {
            width: 40%;
            float: left;
        }
        .col-4 {
            max-width: 40%;
            float: right;
        }
        .border {
            border: 1px solid
        }
        .border-black {
            border-color: black;
        }
        .w-100 {
            width: 100%
        }
        .logo {
            float: right;
        }
        .d-inline-block {
            display: inline-block;
        }
    </style>
</head>

<body class="abg-white atext-black">
    <div class="invoice-wrapper font-14">
        <h1 class="font-48 text-uppercase apx-8 d-inline-block m-0">invoice {{ $targetYear }}-{{ $targetMonth }}</h1>
        <img src="" width="177" height="110" alt="" class="logo">

        <div class="apx-24">
            <h2 class="font-14 text-uppercase amb-12 fw-medium">User sent invoice successfully</h2>
            @foreach ($success as $email)
                <p class="amb-12">Email:<b>{{ $email }}</b></p>
            @endforeach

            @if (!count($success))
                <p class="amb-12">No User</b></p>
            @endif

        </div>

        <hr>

        <div class="apx-24">
            <h2 class="font-14 text-uppercase amb-12 fw-medium">User sent invoice failed</h2>
            @foreach ($fail as $email)
                <p class="amb-12">Email:<b>{{ $email }}</b></p>
            @endforeach

            @if (!count($fail))
                <p class="amb-12">No User</b></p>
            @endif
        </div>
    </div>
</body>
</html>
