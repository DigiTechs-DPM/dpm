<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Notification' }}</title>

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
                                <img src="{{ url('admin-assets/dpm-logos/4.png') }}" width="200" alt="DPM Logo"
                                    style="display:block; margin:0 auto; border:0; outline:none;">
                            </td>
                        </tr>

                        <!-- Divider -->
                        <tr>
                            <td style="padding:0 20px;">
                                <hr style="border:0; border-top:1px solid #e5e5e5; margin:0;">
                            </td>
                        </tr>

                        <!-- Dynamic Content -->
                        <tr>
                            <td style="padding: 25px 20px; text-align:center; font-family:'Asap', sans-serif;">

                                {!! $body !!}

                                <br><br>
                                <em style="font-size:15px; color:#444;">
                                    Thank you!<br>
                                    The DPM Team
                                </em>

                            </td>
                        </tr>

                    </table>

                </td>
            </tr>
        </table>
    </center>

</body>

</html>
