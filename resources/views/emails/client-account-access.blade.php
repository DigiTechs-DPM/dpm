<!doctype html>
<html>

<head>
    <meta charset="UTF-8">
    <title>CRM Portal Access</title>
    <link href="https://fonts.googleapis.com/css?family=Asap:400,700" rel="stylesheet">
</head>

<body style="background:#f5f6fa; margin:0; padding:20px; font-family:'Asap', sans-serif;">
    <center>
        <table width="600"
            style="background:#ffffff; border-radius:10px; padding:20px; box-shadow:0 0 10px rgba(0,0,0,0.1);">
            <tr>
                <td style="text-align:center;">
                    <img src="{{ asset('admin-assets/dpm-logos/4.png') }}" width="200" alt="DPM Logo"
                                    style="display:block; margin:0 auto; border:0; outline:none;">
                    <hr>
                    <h2 style="margin-top:20px; color:#333;">Welcome to Your CRM Portal</h2>
                </td>
            </tr>

            <tr>
                <td style="padding:20px; font-size:16px; color:#444;">
                    Hello <strong>{{ $client->name }}</strong>,<br><br>

                    Your CRM portal access has been created successfully! 🎉
                    You can now log in to manage your orders, briefs, tickets, invoices, and communication.
                    <br><br>

                    <strong>Your Login Credentials</strong><br>
                    <strong>Email:</strong> &nbsp; {{ $client->email }}<br>
                    <strong>Password:</strong> &nbsp; {{ $password }}<br><br>

                    <a href="{{ $loginUrl }}"
                        style="background:#db165b; color:#fff; padding:12px 22px;
                              border-radius:5px; text-decoration:none; font-weight:bold;">
                        Login to CRM Portal
                    </a>

                    <br><br>

                    If you need help or support, feel free to contact your assigned project manager anytime.<br><br>

                    <strong>Thanks,<br>The DPM Team</strong>
                </td>
            </tr>
        </table>
    </center>
</body>

</html>
