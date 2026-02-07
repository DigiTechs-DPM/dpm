@extends('admin.layout.layout')

@section('title', 'Admin | Payment Accounts')

@section('admin-content')



    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="heading d-flex justify-content-between">
                    <h1 class="fw-bold" style="color: #003C51;">Payment Accounts</h1>

                    <div class="d-flex">
                        <div class="d-flex">
                            <button type="submit" class="btn bg-gradient-3" data-toggle="modal" data-target="#addKeys">Add
                                Keys</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="row my-5 fullInfo">
            <div class="col-lg-12">
                <div class="row">
                    @forelse ($keys as $key)
                        @php
                            // dd($key);
                            $iconUrl =
                                filter_var($key->brand->brand_url, FILTER_VALIDATE_URL) &&
                                !preg_match('/\.(jpg|jpeg|png|gif|svg)$/i', $key->brand->brand_url)
                                    ? 'https://www.google.com/s2/favicons?sz=64&domain=' .
                                        parse_url($key->brand->brand_url, PHP_URL_HOST)
                                    : $key->brand->brand_url;
                        @endphp
                        <div class="col-md-6 mb-4">
                            <form action="{{ route('admin.account-keys-update', $key->id) }}" method="POST"
                                class="card p-3 shadow-sm">
                                @csrf
                                <div class="d-flex align-items-center justify-content-between">
                                    <h5 class="text-info">
                                        {{ $key->brand->brand_name ?? '🌐 Super (Global Account)' }}
                                        <p class="text-muted mb-3">{{ $key->brand->brand_url ?? '—' }}</p>
                                    </h5>
                                    <input type="hidden" name="brand_id" value="{{ $key->brand_id }}">
                                    <img src="{{ $iconUrl }}" alt="Brand Logo" class="brand-logo" id="brandLogo">
                                </div>
                                <hr>
                                <div class="col-lg-12 mb-3">
                                    <label class="form-label fw-bold">CRM Module</label>
                                    <select name="module" class="form-control" required>
                                        <option selected disabled>-- select module --</option>
                                        <option value="ppc" @selected($key->module == 'ppc')>PPC</option>
                                        <option value="upwork" @selected($key->module == 'upwork')>Upwork</option>
                                    </select>
                                </div>

                                <!-- Stripe -->
                                <div class="mb-2">
                                    <label>Stripe Publishable Key</label>
                                    <input type="text" name="stripe_publishable_key" class="form-control form-control-sm"
                                        value="{{ $key->stripe_publishable_key }}">
                                </div>
                                <div class="mb-2">
                                    <label>Stripe Secret Key</label>
                                    <input type="text" name="stripe_secret_key" class="form-control form-control-sm"
                                        value="{{ $key->stripe_secret_key }}">
                                </div>
                                <div class="mb-2">
                                    <label>Stripe Webhook Secret</label>
                                    <input type="text" name="stripe_webhook_secret" class="form-control form-control-sm"
                                        value="{{ $key->stripe_webhook_secret }}">
                                </div>

                                <!-- PayPal -->
                                <div class="mb-2">
                                    <label>PayPal Client ID</label>
                                    <input type="text" name="paypal_client_id" class="form-control form-control-sm"
                                        value="{{ $key->paypal_client_id }}">
                                </div>
                                <div class="mb-2">
                                    <label>PayPal Secret</label>
                                    <input type="text" name="paypal_secret" class="form-control form-control-sm"
                                        value="{{ $key->paypal_secret }}">
                                </div>
                                <div class="mb-2">
                                    <label>PayPal Webhook ID</label>
                                    <input type="text" name="paypal_webhook_id" class="form-control form-control-sm"
                                        value="{{ $key->paypal_webhook_id }}">
                                </div>
                                <div class="mb-2">
                                    <label>PayPal Base URL</label>
                                    <input type="text" name="paypal_base_url" class="form-control form-control-sm"
                                        value="{{ $key->paypal_base_url }}">
                                </div>
                                <!-- Status -->
                                <div class="mb-2">
                                    <label>Status</label>
                                    <select name="status" class="form-control form-control-sm">
                                        <option value="active" @selected($key->status === 'active')>Active</option>
                                        <option value="inactive" @selected($key->status === 'inactive')>Inactive</option>
                                    </select>
                                </div>
                                <hr>
                                <div class="text-center mx-auto">
                                    <button type="submit" class="btn btn-sm btn-success">💾 Update</button>
                                </div>
                            </form>
                        </div>
                    @empty
                    @endforelse
                </div>
            </div>

        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="addKeys" data-backdrop="static" data-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Account Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('admin.account-keys.post') }}">
                        @csrf
                        <div class="row">
                            <!-- Brand (Domain) Select -->
                            <div class="col-lg-12 mb-3">
                                <label class="form-label fw-bold">CRM Module</label>
                                <select name="module" class="form-control">
                                    <option selected disabled>-- select model --</option>
                                    <option value="ppc">
                                        PPC
                                    </option>
                                    <option value="upwork">
                                        Upwork
                                    </option>
                                </select>
                            </div>

                            <!-- Brand (Domain) Select -->
                            <div class="col-lg-12 mb-3">
                                <label class="form-label fw-bold">Select Brand / Domain</label>
                                <select name="brand_id" class="form-control">
                                    <option value="">-- Select Domain --</option>
                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand->id }}">
                                            {{ $brand->brand_name }} - ({{ $brand->brand_url }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Stripe Keys -->
                            <div class="col-lg-6 mb-3">
                                <label class="form-label">Stripe Secret Key</label>
                                <input type="text" name="stripe_secret_key" class="form-control">
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="form-label">Stripe Publishable Key</label>
                                <input type="text" name="stripe_publishable_key" class="form-control">
                            </div>
                            <div class="col-lg-12 mb-3">
                                <label class="form-label">Stripe Webhook Secret</label>
                                <input type="text" name="stripe_webhook_secret" class="form-control">
                            </div>

                            <!-- PayPal Keys -->
                            <div class="col-lg-6 mb-3">
                                <label class="form-label">PayPal Client ID</label>
                                <input type="text" name="paypal_client_id" class="form-control">
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="form-label">PayPal Secret</label>
                                <input type="text" name="paypal_secret" class="form-control">
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="form-label">PayPal Webhook ID</label>
                                <input type="text" name="paypal_webhook_id" class="form-control">
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="form-label">PayPal Base URL</label>
                                <input type="text" name="paypal_base_url" class="form-control"
                                    placeholder="e.g. https://api.paypal.com">
                            </div>

                            <!-- Submit -->
                            <div class="col-lg-12 m-auto text-center mt-3">
                                <button class="btn btn-success w-50">Save Keys</button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

@endsection
