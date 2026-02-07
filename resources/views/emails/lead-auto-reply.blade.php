<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta charset="UTF-8">
    <title>We received your request</title>
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
                        <tr>
                            <td style="padding: 20px; font-family:'Asap', Helvetica, sans-serif; font-size:16px; color:#2a2a2a; line-height:1.6; text-align:center;">

                                Hello <strong>{{ $lead->name ?? 'Valued Customer' }}</strong>,<br><br>

                                Thank you for contacting us! 🎉<br>
                                We’ve received your request and a member of our team will reach out to you shortly.<br><br>

                                <strong>Service Requested:</strong> {{ data_get($lead->meta, 'service', 'Not specified') }}<br>
                                <strong>Email:</strong> {{ $lead->email ?? 'Not provided' }}<br>
                                <strong>Phone:</strong> {{ $lead->phone ?? 'Not provided' }}<br><br>

                                <em>We appreciate your interest and look forward to assisting you!</em><br><br>

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
