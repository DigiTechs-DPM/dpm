@extends('sellers.layout.layout')

@section('title', 'Seller | Dashboard')

@section('sellers-content')

    <div class="d-xl-flex justify-content-between align-items-start">
        <h1 class="fw-bold" style="color: #003C51;">Dashboard
            <span class="mx-3"><img src="https://cdn-icons-png.flaticon.com/128/9823/9823663.png" alt=""
                    style="width: 60px;"></span>
        </h1>
        <div class="d-sm-flex justify-content-xl-between align-items-center mb-2">
            <div class="btn">
                <a href="{{ route('seller.domain-script.get') }}"><button class="btn btn-info">Get Script</button></a>
            </div>
            <div class="dropdownTime ml-0 ml-md-4 mt-2 mt-lg-0" style="box-shadow: 0px 6px 30px rgba(1, 170, 156, 0.521);">
                <button class="btn bg-white p-3 d-flex align-items-center" type="button" id="dropdownMenuButton1">
                    <i class="mdi mdi-calendar mr-1 mx-3 text-success" id="current-date">{{ now()->format('Y-m-d') }}</i>
                    <span id="current-time" class="text-danger fw-bolder">{{ now()->format('H:i:s') }}</span>
                </button>
            </div>
        </div>
    </div>

    <script>
        function updateTime() {
            var currentTime = new Date();
            var seconds = currentTime.getSeconds().toString().padStart(2, '0');
            document.getElementById('current-time').textContent = currentTime.getHours() + ':' + currentTime.getMinutes() +
                ':' + seconds;
        }
        setInterval(updateTime, 1000);
        document.querySelector('.dropdownTime').classList.add('animate-light');
    </script>


    <div class="row">
        <div class="col-md-12">
            <hr>
            <div class="tab-content tab-transparent-content">
                <div class="tab-pane fade show active" id="business-1" role="tabpanel" aria-labelledby="business-tab">
                    <div class="row">
                        <div class="col-xl-3 col-lg-6 col-sm-6 grid-margin stretch-card">
                            <div class="card">
                                <a href="{{ route('seller.brands.get') }}" style="text-decoration:none;">
                                    <div class="card-body text-center">
                                        <h5 class="mb-2 text-danger font-weight-normal py-2">Brands</h5>
                                        <div
                                            class="dashboard-progress dashboard-progress-3 d-flex align-items-center justify-content-center item-parent my-5 ">
                                            <img src="https://cdn-icons-png.flaticon.com/128/7991/7991055.png"
                                                alt="" style="width: 60px;">
                                        </div>
                                        <h2 class="mb-4 text-danger font-weight-bold  ">{{ $brands ?? 0 }}</h2>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="col-xl-3  col-lg-6 col-sm-6 grid-margin stretch-card">
                            <div class="card">
                                <a href="{{ route('seller.leads.get') }}" style="text-decoration:none;">
                                    <div class="card-body text-center">
                                        <h5 class="mb-2 text-danger font-weight-normal py-2">Leads</h5>
                                        <div
                                            class="dashboard-progress dashboard-progress-3 d-flex align-items-center justify-content-center item-parent my-5 ">
                                            <img src="https://cdn-icons-png.flaticon.com/128/2275/2275248.png"
                                                alt="" style="width: 60px;">
                                        </div>
                                        <h2 class="mb-4 text-danger font-weight-bold">{{ $leads ?? 0 }}</h2>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6 col-sm-6 grid-margin stretch-card">
                            <div class="card">
                                <a href="{{ route('seller.orders.get') }}" style="text-decoration:none;">
                                    <div class="card-body text-center">
                                        <h5 class="mb-2 text-danger font-weight-normal py-2">Orders</h5>
                                        <div
                                            class="dashboard-progress dashboard-progress-3 d-flex align-items-center justify-content-center item-parent my-5 ">
                                            <img src="https://cdn-icons-png.flaticon.com/128/5530/5530389.png"
                                                alt="" style="width: 60px;">
                                        </div>
                                        <h2 class="mb-4 text-danger font-weight-bold  ">
                                            {{ $orders ?? 0 }}
                                        </h2>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6 col-sm-6 grid-margin stretch-card">
                            <div class="card">
                                <a href="{{ route('seller.orders.get') }}" style="text-decoration:none;">
                                    <div class="card-body text-center">
                                        <h5 class="mb-2 text-danger font-weight-normal py-2">Payments</h5>
                                        <div
                                            class="dashboard-progress dashboard-progress-3 d-flex align-items-center justify-content-center item-parent my-5">
                                            <img src="https://cdn-icons-png.flaticon.com/128/2059/2059129.png"
                                                alt="" style="width: 60px;">
                                        </div>
                                        <h2 class="mb-4 text-danger font-weight-bold">{{ $payments ?? 0 }}

                                        </h2>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-6 col-lg-6 col-sm-6 grid-margin stretch-card">
                            <div class="card ">
                                <a href="javascript:void(0);" style="text-decoration:none;">
                                    <div class="card-body">
                                        <h2 class="mb-2 text-danger font-weight-normal py-2">
                                            Lead Views
                                            <span class="mx-3">
                                                <img src="https://cdn-icons-png.flaticon.com/128/4310/4310155.png"
                                                    alt="" style="width: 30px;">
                                            </span>
                                        </h2>
                                        @if (!empty($logs) && count($logs) > 0)
                                            <ul class="list-unstyled" style="max-height: 300px; overflow-y: auto;">
                                                @foreach ($logs as $log)
                                                    <li
                                                        style="background: #111; color: #0f0; padding: 10px; font-size: 14px; border-bottom: 1px solid #333;">
                                                        {{ $log }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <div class="alert alert-warning my-2">
                                                <i class="fa fa-exclamation-circle"></i> No lead view logs available.
                                            </div>
                                        @endif
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
