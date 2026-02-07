<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta charset="UTF-8">
    <title>Renewal Order Created</title>
    <link href='https://fonts.googleapis.com/css?family=Asap:400,700' rel='stylesheet'>
</head>

<body style="background: linear-gradient(135deg,#db165b,#673187,#f7b63e); margin:0; padding:0;">
    <center>
        <table width="100%" style="background: linear-gradient(135deg,#db165b,#673187,#f7b63e);">
            <tr>
                <td align="center">

                    <table width="600" style="max-width:600px; background:#f7f7ff;">

                        <!-- Header Image -->
                        <tr>
                            <td style="padding: 16px; border-bottom: 1px solid red;">
                                <img src="{{ asset('admin-assets/dpm-logos/4.png') }}" width="200" alt="DPM Logo"
                                    style="display:block; margin:0 auto; border:0; outline:none;">
                            </td>
                        </tr>

                        <!-- Body -->
                        <tr>
                            <td
                                style="padding: 20px;
                                       font-family:'Asap', Helvetica, sans-serif;
                                       font-size:16px;
                                       color:#2a2a2a;
                                       line-height:1.6;
                                       text-align:center;">

                                Hello <strong>{{ $client->name }}</strong>,<br><br>

                                Your renewal order has been successfully created! 🎉<br>
                                We appreciate your continued trust in our services.<br><br>

                                <strong>Renewal Order Details:</strong><br>
                                <strong>Service:</strong> {{ $order->service_name }}<br>
                                <strong>Brand:</strong> {{ $brand }}<br>
                                <strong>Order ID:</strong> #{{ $order->id }}<br>
                                <strong>Type:</strong> Renewal<br>
                                <strong>Status:</strong> {{ ucfirst($order->status) }}<br>
                                <br>

                                Your assigned project manager will soon send you a secure payment link to complete this
                                renewal.<br>
                                You’ll be notified again once the payment link is ready.<br><br>

                                <em>Thank you for continuing with us.
                                    We look forward to serving you again!</em><br><br>

                                <h4 style="color:#007bff;">– The DPM Team</h4>

                            </td>
                        </tr>

                    </table>

                </td>
            </tr>
        </table>
    </center>
</body>

</html>
