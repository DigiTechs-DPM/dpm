<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Password Reset</title>

    <link href="https://fonts.googleapis.com/css?family=Asap:400,700" rel="stylesheet">

    <style>
        @media only screen and (min-width:768px) {
            .templateContainer {
                width: 600px !important;
            }
        }

        @media only screen and (max-width:480px) {

            body,
            table,
            td,
            p,
            a,
            li,
            blockquote {
                -webkit-text-size-adjust: none !important;
            }

            body {
                width: 100% !important;
                min-width: 100% !important;
            }

            #bodyCell {
                padding-top: 10px !important;
            }

            .mcnImage {
                width: 100% !important;
            }

            .mcnTextContent {
                padding-left: 18px !important;
                padding-right: 18px !important;
                font-size: 16px !important;
                line-height: 150% !important;
            }
        }
    </style>
</head>

<body style="margin:0; padding:0; background:linear-gradient(135deg,#db165b,#673187,#f7b63e);">

    <center>

        <table id="bodyTable" width="100%" border="0" cellspacing="0" cellpadding="0"
            style="height:100%; margin:0; padding:0; background:linear-gradient(135deg,#db165b,#673187,#f7b63e);">
            <tr>
                <td id="bodyCell" align="center" valign="top">

                    <!-- Main Container -->
                    <table class="templateContainer" width="100%" border="0" cellspacing="0" cellpadding="0"
                        style="max-width:600px; background:#f7f7ff;">

                        <tr>
                            <td align="center" style="padding: 16px 0; border-bottom: 1px solid red;">
                                <img src="{{ url('admin-assets/dpm-logos/4.png') }}" width="200" alt="DPM Logo"
                                    style="display:block; margin:0 auto; border:0; outline:none;">
                            </td>
                        </tr>

                        <!-- Banner Image -->
                        <tr>
                            <td align="center" style="padding:0;">
                                <img src="https://www.freeiconspng.com/uploads/forgot-password-icon-27.png"
                                    alt="Reset Password" width="600"
                                    style="max-width:50%; border:0; display:block;">
                            </td>
                        </tr>

                        <!-- Content -->
                        <tr>
                            <td style="padding:30px 25px; text-align:center; font-family:'Asap',sans-serif;">

                                <h1 style="margin:0; text-transform:uppercase; font-size:32px; color:#2a2a2a;">
                                    Forgot Your Password?
                                </h1>

                                <p style="margin-top:20px; font-size:16px; color:#2a2a2a; line-height:1.6;">
                                    We can’t send your existing password, but we can help you create a new one.<br>
                                    Click the button below to safely reset your password.
                                </p>

                                <!-- Reset Button -->
                                <a href="{{ route('upwork.reset.get', $token) }}"
                                    style="display:inline-block;
                                  margin-top:25px;
                                  background:linear-gradient(135deg,#db165b,#673187,#f7b63e);
                                  color:#fff !important;
                                  padding:12px 30px;
                                  border-radius:6px;
                                  font-weight:bold;
                                  text-decoration:none;
                                  text-transform:uppercase;">
                                    Reset Password
                                </a>
                                <br>

                                <em style="font-size:15px; color:#555;">Stay secure,<br>The DPM Team</em>

                            </td>
                        </tr>

                    </table>
                </td>
            </tr>
        </table>

    </center>

</body>

</html>
