<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Link Error - {{ config('app.name') }}</title>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js" crossorigin="anonymous"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.1/css/toastr.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.1/js/toastr.js"></script>

    <style>
        body {
            background: #f7f7f7;
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 40px;
        }

        .container {
            max-width: 550px;
            margin: auto;
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0px 5px 20px rgba(0, 0, 0, 0.1);
        }

        .footer {
            margin-top: 20px;
            opacity: 0.6;
        }
    </style>
</head>

<body>

    <script>
        $(document).ready(function() {
            toastr.error("{{ $message ?? 'This payment link is not active.' }}");
        });
    </script>

    <div class="container">
        <h1 style="font-size: 72px; margin-bottom: 0;">410</h1>
        <h2 style="margin-top: 5px;">Payment Link Error</h2>

        <p class="text-danger" style="font-size: 20px; margin-top: 25px;">
            {{ $message ?? 'This payment link is not active.' }}
        </p>

        <p>Please contact your seller or support team to receive a new payment link.</p>

        <a href="{{ route('client.login.get') }}" class="btn btn-primary mt-3"
            style="padding: 8px 20px; background:#007bff; color:#fff; border-radius:5px; text-decoration:none;">
            Go Back
        </a>

        <div class="footer">— {{ config('app.name') }}</div>
    </div>

</body>

</html>
