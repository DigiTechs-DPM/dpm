<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="{{ url('admin-assets/dpm-logos/dpm-fav.png') }}" rel="icon">
    <link rel="shortcut icon" type="image/x-icon" href="{{ url('admin-assets/dpm-logos/dpm-fav.png') }}">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>Payment Success</title>
    <style>
        body,
        html {
            height: 100%;
        }

        .form-wrapper {
            height: 100vh;
        }
    </style>
</head>

<body>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"
        integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.1/css/toastr.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.1/js/toastr.js"></script>

    <script>
        $(document).ready(function() {
            toastr.options.timeOut = 10000;

            @if (Session::has('success'))
                toastr.success('{{ Session::get('success') }}');
            @endif

            @if (Session::has('info'))
                toastr.info('{{ Session::get('info') }}');
            @endif

            @if (Session::has('warning'))
                toastr.warning('{{ Session::get('warning') }}');
            @endif

            @if (Session::has('error'))
                toastr.error('{{ Session::get('error') }}');
            @endif
        });
    </script>
    @php
        // prefer Payment row, else fall back to link columns
        $paidAt = $link->paid_at ?? optional($order?->paid_at);
        $txnId = $latest->provider_payment_intent_id ?? $link->provider_payment_intent_id;
        $provider = $latest->provider ?? (str_contains($txnId ?? '', 'pi_') ? 'stripe' : 'paypal');
    @endphp

    <div class="container-fluid d-flex justify-content-center align-items-center min-vh-100 bg-light form-wrapper">
        <div class="col-md-7">
            <div class="card shadow-lg border-0 rounded-3">
                <div class="card-body p-5 text-center">

                    {{-- Success Icon --}}
                    <div class="mb-4">
                        <div class="rounded-circle d-inline-flex justify-content-center align-items-center">
                            <img src="{{ url('admin-assets/dpm-logos/4.png') }}" alt=""
                                style="width: 100%; height: auto">
                        </div>
                    </div>
                    <h3 class="fw-bold text-success">Payment Successful 🎉</h3>
                    <p class="text-muted mb-4">Thank you! Your payment has been processed successfully.</p>
                    <div class="text-left mb-4">
                        <div class="list-group list-group-flush">
                            <div class="list-group-item d-flex justify-content-between">
                                <span class="fw-semibold">Service:</span>
                                <span>{{ $link->service_name }}</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between">
                                <span class="fw-semibold">Amount:</span>
                                <span>{{ number_format($link->unit_amount / 100, 2) }}
                                    {{ strtoupper($link->currency) }}</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between">
                                <span class="fw-semibold">Paid at:</span>
                                <span>{{ $link->paid_at?->toDayDateTimeString() ?? '—' }}</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between">
                                <span class="fw-semibold">Transaction ID:</span>
                                <span
                                    class="text-monospace">{{ $link->provider_payment_intent_id ?? ($link->provider_txn_id ?? '—') }}</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between">
                                <span class="fw-semibold">Provider:</span>
                                <span class="text-capitalize">{{ ucfirst($provider) }}</span>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('index.get') }}" class="btn btn-sm rounded-3 px-4" style="background: linear-gradient(0deg, #000000, #ff0000f7);color: white !important;">
                        <i class="fas fa-home mr-2"></i> Visit Us
                    </a>
                </div>
            </div>
        </div>
    </div>



</body>

</html>
