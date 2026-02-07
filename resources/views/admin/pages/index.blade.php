@extends('admin.layout.layout')

@section('title', 'Admin | Home')

@section('admin-content')

    <div class="d-xl-flex justify-content-between align-items-start">
        <h1 class="fw-bold" style="color: #003C51;">Dashboard
            <span class="mx-3"><img src="https://cdn-icons-png.flaticon.com/128/9823/9823663.png" alt=""
                    style="width: 60px;"></span>
        </h1>
        <div class="d-sm-flex justify-content-xl-between align-items-center mb-2">

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
                        <div class="col-xl-3 col-lg-3 col-sm-3 grid-margin stretch-card">
                            <div class="card ">
                                <a href="javascript:void(0);" style="text-decoration:none;">
                                    <div class="card-body">
                                        <h2 class="mb-2 text-danger font-weight-normal py-2">Online Users <span
                                                class="mx-3">
                                                <img src="https://cdn-icons-png.flaticon.com/128/1239/1239719.png"
                                                    alt="" style="width: 30px;">
                                            </span></h2>
                                        <ul class="list-unstyled" style="overflow-y: auto;">
                                            @forelse($activeMembers as $user)
                                                <li class="text-dark fw-bold">
                                                    {{ $user->name ?? $user->email }}
                                                    <small class="text-muted">({{ class_basename($user) }})</small>
                                                </li>
                                            @empty
                                                <li class="text-dark fw-bold">No users online</li>
                                            @endforelse
                                        </ul>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-sm-6 grid-margin stretch-card">
                            <div class="card">
                                <canvas id="orderPaymentsChart"></canvas>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6 col-sm-6 grid-margin stretch-card">
                            <div class="card">
                                <a href="{{ route('admin.brand-payments.get') }}" style="text-decoration:none;">
                                    <div class="card-body text-center">
                                        <h5 class="mb-2 text-danger font-weight-normal py-2">Revenue</h5>
                                        <div
                                            class="dashboard-progress dashboard-progress-3 d-flex align-items-center justify-content-center item-parent my-5 ">
                                            <img src="https://cdn-icons-png.flaticon.com/128/5412/5412646.png"
                                                alt="" style="width: 60px;">
                                        </div>
                                        <h2 class="mb-4 text-danger font-weight-bold  ">{{ money_cents($revenue ?? 0) }}
                                            <div class="my-3">
                                                <h6 class="text-muted">Paid : {{ money_cents($paymentPaid ?? 0) }}</h6>
                                                <h6 class="text-sm text-muted">Dues : {{ money_cents($paymentDue ?? 0) }}
                                                </h6>
                                            </div>
                                        </h2>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-3 col-lg-6 col-sm-6 grid-margin stretch-card">
                            <div class="card">
                                <a href="{{ route('admin.brands.get') }}" style="text-decoration:none;">
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
                                <a href="{{ route('admin.leads.get') }}" style="text-decoration:none;">
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
                                <a href="{{ route('admin.orders.get') }}" style="text-decoration:none;">
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
                                <a href="{{ route('admin.orders.get') }}" style="text-decoration:none;">
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
                                <div class="card-body">
                                    <h2 class="mb-2 text-danger font-weight-normal py-2">
                                        Lead Views
                                        <span class="mx-3">
                                            <img src="https://cdn-icons-png.flaticon.com/128/4310/4310155.png"
                                                alt="" style="width: 30px;">
                                        </span>
                                        <form method="POST" action="{{ route('admin.lead.logs.clear') }}"
                                            onsubmit="return confirm('Clear all lead logs?')">
                                            @csrf
                                            <button class="btn btn-danger btn-sm">
                                                <i class="fa fa-trash"></i> Clear Logs
                                            </button>
                                        </form>
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        setInterval(function() {
            $("#onlineUsers").load("/online-users #onlineUsers");
        }, 60000);
    </script>


    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctxOrder = document.getElementById('orderPaymentsChart').getContext('2d');
        // Gradient for fill
        let gradient = ctxOrder.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(54, 162, 235, 0.4)'); // top (blue)
        gradient.addColorStop(1, 'rgba(54, 162, 235, 0)'); // bottom (transparent)

        new Chart(ctxOrder, {
            type: 'line',
            data: {
                labels: @json($months), // e.g. ["2025-01","2025-02"]
                datasets: [{
                    label: 'Payments ($)',
                    data: @json($totals), // e.g. [1200,800,1500]
                    fill: true,
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: gradient,
                    tension: 0.4, // smooth curve
                    borderWidth: 3,
                    pointRadius: 4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: 'rgba(54, 162, 235, 1)',
                    pointHoverRadius: 7,
                    pointHoverBackgroundColor: 'rgba(54, 162, 235, 1)'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value; // format as money
                            }
                        }
                    }
                }
            }
        });
    </script>

@endsection
