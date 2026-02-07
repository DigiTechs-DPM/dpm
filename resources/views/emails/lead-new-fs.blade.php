<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta charset="UTF-8">
    <title>New Lead Notification</title>
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
                                style="padding: 20px; font-family:'Asap', Helvetica, sans-serif;
                       font-size:16px; color:#2a2a2a; line-height:1.6; text-align:center;">

                                Hello <strong>{{ $seller->name }}</strong>,<br><br>

                                A new lead has just been assigned to you. 🎉<br>
                                Please follow up as soon as possible.<br><br>

                                <strong>Lead Details:</strong><br>
                                <strong>Name:</strong> {{ $lead->name ?? 'N/A' }}<br>
                                <strong>Email:</strong> {{ $lead->email ?? 'N/A' }}<br>
                                <strong>Phone:</strong> {{ $lead->phone ?? 'N/A' }}<br>
                                <strong>Service:</strong> {{ data_get($lead->meta, 'service', 'N/A') }}<br>
                                <strong>Message:</strong> {{ $lead->message ?? 'No message provided' }}<br><br>

                                <em>Please respond promptly. Quick follow-up increases conversion rate
                                    significantly.</em><br><br>

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
