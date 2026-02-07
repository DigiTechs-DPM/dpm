<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment Link</title>

    <link href="https://fonts.googleapis.com/css?family=Asap:400,700" rel="stylesheet">

    <style type="text/css">
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

            .mcnTextContent,
            .mcnBoxedTextContentColumn {
                padding-right: 18px !important;
                padding-left: 18px !important;
                font-size: 16px !important;
                line-height: 150% !important;
            }
        }
    </style>
</head>

<body style="margin:0; padding:0; background: linear-gradient(135deg,#db165b,#673187,#f7b63e);">

    <center>

        <table id="bodyTable" width="100%" border="0" cellpadding="0" cellspacing="0"
            style="background: linear-gradient(135deg,#db165b,#673187,#f7b63e); height:100%; margin:0; padding:0;">
            <tr>
                <td id="bodyCell" align="center" valign="top">

                    <!-- Main Container -->
                    <table class="templateContainer" width="100%" border="0" cellpadding="0" cellspacing="0"
                        style="max-width:600px; background-color:#f7f7ff;">

                        <!-- Header -->
                        <tr>
                            <td align="center" style="padding: 16px 0; border-bottom: 1px solid red;">
<img src="{{ asset('admin-assets/dpm-logos/4.png') }}" width="200" alt="DPM Logo"
                                    style="display:block; margin:0 auto; border:0; outline:none;">
                            </td>
                        </tr>

                        <!-- Divider -->
                        <tr>
                            <td style="padding:0 20px;">
                                <hr style="border:0; border-top:1px solid #e5e5e5; margin:0;">
                            </td>
                        </tr>

                        <!-- Content -->
                        <tr>
                            <td style="padding: 25px 20px; text-align:center; font-family:'Asap', sans-serif;">

                                <p style="font-size:16px; color:#2a2a2a; line-height:1.6;">

                                    Hello <strong>{{ $lead->name ?? 'Valued Customer' }}</strong>,<br><br>

                                    Thank you for choosing our service. To complete your order, please proceed with your
                                    payment below.

                                </p>

                                <!-- Payment Details -->
                                <hr style="margin:25px 0; border:0; border-top:1px solid #ddd;">

                                <h3 style="color:#c443e4; margin-bottom:10px;">Complete Your Payment</h3>

                                <p style="text-align:left; font-size:16px; line-height:1.6; color:#2a2a2a;">

                                    <strong>Service:</strong> {{ $service }}<br>
                                    <strong>Brand:</strong> {{ $brandName }}<br>
                                    <strong>Amount:</strong> {{ $amount }}<br>

                                    @isset($expiresAt)
                                        <strong>Expires:</strong> {{ $expiresAt->toDayDateTimeString() }}<br>
                                    @endisset

                                </p>

                                <!-- Pay Button -->
                                <a href="{{ $url }}"
                                    style="display:inline-block;
                                  background: linear-gradient(135deg,#db165b,#673187,#f7b63e);
                                  color:#fff !important;
                                  text-decoration:none;
                                  padding:12px 25px;
                                  border-radius:6px;
                                  font-weight:bold;">
                                    Pay Now
                                </a>

                                <p style="margin-top:15px; font-size:14px; color:#2a2a2a; width: 300px;">
                                    If the button doesn’t work, copy and paste this link into your browser:<br>
                                    <a href="{{ $url }}" style="color:#673187; width: 300px;">{{ $url }}</a>
                                </p>

                                <br>

                                <em style="font-size:15px; color:#444;">Thank you for your business!<br>The DPM
                                    Team</em>

                            </td>
                        </tr>

                    </table>

                </td>
            </tr>
        </table>

    </center>

</body>

</html>
