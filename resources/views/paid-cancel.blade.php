<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="{{ url('admin-assets/dpm-logos/dpm-fav.png') }}" rel="icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <title>Payment Canceled</title>

    <style>
        body {
            background: #f8f9fa;
        }

        .card {
            border-radius: 12px;
        }
    </style>
</head>

<body>

    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="col-md-7">
            <div class="card shadow-lg">
                <div class="card-body p-5 text-center">

                    <div class="mb-4">
                        <img src="{{ url('admin-assets/dpm-logos/4.png') }}" width="120">
                    </div>

                    <h3 class="fw-bold text-warning">Payment Canceled</h3>
                    <p class="text-muted">You canceled the payment before completing it.</p>

                    <p>If this was a mistake, you can try again using the link provided by your seller.</p>

                    <a href="{{ route('client.login.get') }}" class="btn btn-dark mt-3 px-4 rounded-pill">
                        Return Home
                    </a>

                </div>
            </div>
        </div>
    </div>

</body>

</html>
