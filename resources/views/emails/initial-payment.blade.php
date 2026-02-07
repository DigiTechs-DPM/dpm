<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta charset="UTF-8">
    <title>Payment Received</title>
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
                        <hr>

                        <!-- Body -->
                        <tr>
                            <td
                                style="padding: 24px; font-family:'Asap', Helvetica, sans-serif;
                       font-size:16px; color:#2a2a2a; line-height:1.6; text-align:left;">

                                <h2 style="color:#007bff;">Payment Successfully Received 🎉</h2>

                                Hello <strong>{{ $client->name ?? $order->buyer_name }}</strong>,<br><br>

                                Your payment for <strong>{{ $order->service_name }}</strong> has been received.<br><br>

                                <strong>Amount Paid:</strong> {{ number_format($payment->amount / 100, 2) }}
                                {{ $payment->currency }}<br>
                                <strong>Order Type:</strong> {{ ucfirst($order->order_type) }}<br>
                                <strong>Paid At:</strong> {{ $payment->created_at->format('d M, Y h:i A') }}<br><br>

                                <em>Our team will begin processing your order immediately.</em><br><br>

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
