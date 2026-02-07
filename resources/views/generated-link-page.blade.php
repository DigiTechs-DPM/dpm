<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Payment Link</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
        integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <link href="{{ url('admin-assets/dpm-logos/dpm-fav.png') }}" rel="icon">

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"
        integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous">
    </script>
</head>

<body>

    <style>
        body {
            background: #f9f9f9;
        }

        .payment-container {
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            max-width: 900px;
            margin: 30px auto;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .section-header {
            background: #00A9B7;
            color: #fff;
            padding: 5px 10px;
            margin-top: 20px;
            font-weight: bold;
        }

        .btn-submit {
            background: #4CAF50;
            color: white;
            font-weight: bold;
            border: none;
            padding: 10px 25px;
            border-radius: 20px;
        }

        .safe-checkout {
            display: flex;
            align-items: center;
            margin-top: 20px;
        }

        .safe-checkout img {
            height: 50px;
        }

        .brand-logo {
            max-height: 60px;
        }
    </style>

    @php
        $iconUrl =
            filter_var($brand->brand_url, FILTER_VALIDATE_URL) &&
            !preg_match('/\.(jpg|jpeg|png|gif|svg)$/i', $brand->brand_url)
                ? 'https://www.google.com/s2/favicons?sz=64&domain=' . parse_url($brand->brand_url, PHP_URL_HOST)
                : $brand->brand_url;

        $payKeys = DB::table('account_keys')->where('brand_id', $brand->id)->first();
        // dd($payKeys);

        $fullName = $client->name; // e.g., "Frankline George"
        $nameParts = explode(' ', $fullName, 2); // split into 2 parts only

        $fName = $nameParts[0] ?? ''; // First name
        $lName = $nameParts[1] ?? ''; // Last name or empty if not provided

    @endphp


    <div class="payment-container">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h3>Billing Details</h3>
            {{-- crossorigin="anonymous" --}}
            <img src="{{ $iconUrl }}" alt="Brand Logo" class="brand-logo" id="brandLogo">
        </div>

        <form method="POST" action="{{ route('paylinks.checkout', $token) }}">
            @csrf
            <input type="hidden" name="brand_id" value="{{ $brand->id }}">
            {{-- <input type="hidden" id="payment_link_token" value="{{ $token }}"> --}}
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label>Service Package</label>
                    <input type="text" class="form-control" value="{{ isset($service) ? str($service) : '' }}"
                        readonly>
                </div>
                <div class="form-group col-md-6">
                    <label>Amount Payable</label>
                    <input type="text" class="form-control"
                        value="{{ isset($amount) ? number_format($amount / 100, 2) : '' }}" readonly>
                </div>
                <div class="form-group col-md-6">
                    <label>Currency</label>
                    <input type="text" class="form-control" value="{{ $currency }}" readonly>
                </div>
            </div>

            {{-- <div class="stripe-card-element">
                <div id="card-element" class="form-control"></div>
                <div id="card-errors" role="alert" class="text-danger mt-2"></div>
            </div> --}}

            <div class="section-header mb-3">PAYER DETAILS</div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <input type="text" name="first_name" value="{{ $fName ?? '-' }}" class="form-control"
                        placeholder="First name" readonly>
                </div>
                <div class="form-group col-md-6">
                    <input type="text" name="last_name" value="{{ $lName ?? '-' }}" class="form-control"
                        placeholder="Last name" readonly>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <input type="email" name="email" value="{{ $client->email ?? '-' }}" class="form-control"
                        placeholder="Email" readonly required>
                </div>
                <div class="form-group col-md-6">
                    <input type="text" name="phone" value="{{ $client->phone ?? '-' }}" class="form-control"
                        placeholder="Phone" readonly>
                </div>
            </div>

            {{-- <div class="section-header mb-3">BILLING DETAILS (optional)</div>
            <input type="text" name="address" class="form-control mb-2" placeholder="Address">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <input type="text" name="city" class="form-control" placeholder="City">
                </div>
                <div class="form-group col-md-6">
                    <input type="text" name="state" class="form-control" placeholder="State/Province">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <input type="text" name="zip" class="form-control" placeholder="Zip/Postal Code">
                </div>
                <div class="form-group col-md-6">
                    <input type="text" name="country" class="form-control" placeholder="Country (ISO-2, e.g., US)">
                </div>
            </div> --}}
            <hr>
            {{-- No card inputs here. Stripe hosts the payment form. --}}
            <div class="text-center">
                <button type="submit" class="btn btn-submit">
                    @if ($link->provider === 'paypal')
                        <i class="fab fa-paypal"></i> Pay with PayPal
                    @else
                        <i class="fab fa-cc-stripe"></i> Pay with Stripe
                    @endif
                </button>
            </div>
        </form>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/color-thief/2.3.2/color-thief.umd.js"></script>
        <script>
            const img = document.getElementById('brandLogo');
            const colorThief = new ColorThief();

            img.onload = () => {
                const color = colorThief.getColor(img);
                console.log("Dominant color:", color); // [R,G,B]
            };
        </script>
    </div>

    {{-- <script src="https://js.stripe.com/v3/"></script>
    <script>
        document.addEventListener("DOMContentLoaded", async function() {
            const stripe = Stripe('{{ $payKeys->stripe_publishable_key }}');
            const elements = stripe.elements();
            const card = elements.create("card");
            card.mount("#card-element");

            const form = document.getElementById("payment-form");
            const cardErrors = document.getElementById("card-errors");

            form.addEventListener("submit", async function(event) {
                event.preventDefault();
                const {
                    paymentIntentClientSecret
                } = await fetch('/api/payment-intent', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]')
                            .value,
                    },
                    body: JSON.stringify({
                        token: document.getElementById("payment_link_token").value,
                        name: document.getElementById("name").value,
                        email: document.getElementById("email").value
                    })
                }).then(r => r.json());

                const result = await stripe.confirmCardPayment(paymentIntentClientSecret, {
                    payment_method: {
                        card: card,
                        billing_details: {
                            name: document.getElementById("name").value,
                            email: document.getElementById("email").value,
                        },
                    },
                });

                if (result.error) {
                    cardErrors.textContent = result.error.message;
                } else {
                    if (result.paymentIntent.status === 'succeeded') {
                        window.location.href = '/payments/thanks';
                    }
                }
            });
        });
    </script> --}}
</body>

</html>
