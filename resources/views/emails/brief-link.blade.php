<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta charset="UTF-8">
    <title>Brief Form Required</title>
    <link href='https://fonts.googleapis.com/css?family=Asap:400,700' rel='stylesheet'>
</head>

<body style="background: linear-gradient(135deg,#db165b,#673187,#f7b63e); margin:0; padding:0;">
    <center>
        <table width="100%" style="background: linear-gradient(135deg,#db165b,#673187,#f7b63e);">
            <tr>
                <td align="center">

                    <table width="600" style="max-width:600px; background:#f7f7ff;">

                        <!-- Header -->
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
                                       text-align:left;">

                                Hello <strong>{{ $client->name }}</strong>,<br><br>

                                We need your **project brief** to begin working on your order.<br><br>

                                <strong>Order Details:</strong><br>
                                <strong>Service:</strong> {{ $order->service_name }}<br>
                                <strong>Brand:</strong> {{ $brand }}<br>
                                <strong>Order ID:</strong> #{{ $order->id }}<br>
                                <strong>Status:</strong> {{ ucfirst($order->status) }}<br><br>

                                Please click the secure link below to submit your questionnaire:<br><br>

                                <a href="{{ $briefUrl }}" target="_blank"
                                    style="background:#b10da9;
                                          color:#fff;
                                          padding:12px 20px;
                                          text-decoration:none;
                                          border-radius:5px;
                                          font-weight:bold;
                                          display:inline-block;">
                                    Fill Out Brief Form
                                </a>

                                <br><br>
                                This link is valid until
                                <strong>{{ $order->brief->brief_token_expires_at ?? 'N/A' }}</strong><br><br>

                                <em>Once submitted, your project manager will begin working on your order.</em><br><br>

                                <h4 style="color:#91268b;">– The DPM Team</h4>

                            </td>
                        </tr>

                    </table>

                </td>
            </tr>
        </table>
    </center>
</body>

</html>
