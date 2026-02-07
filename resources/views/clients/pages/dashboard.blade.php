@extends('clients.layouts.layout')

@section('title', 'Client | Dashboard')

@section('mian-content')

    <style>
        canvas {
            border-radius: 16px;
            background: linear-gradient(90deg, #e0f7fa, #ffffff);
            padding: 10px;
        }
    </style>
    <section class="dashboard my">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="parent-dashboard-card">
                        <div class="row mx-auto">
                            <div class="col-md-3 dashboard-cards"
                                style="margin: 10px 10px 0px 10px !important; min-width: 356px;">
                                <figure>
                                    <img src="https://designcrm.net/images/card-icon-1.png" class="img-fluid" alt="">
                                </figure>
                                <h2 class="heading-2">
                                    <span class="d-block">
                                        Welcome To
                                    </span>
                                    Design Crm
                                </h2>
                                <p class="description_para">
                                    Effortlessly view and manage your messages to stay connected and collaborate
                                    seamlessly.
                                </p>
                                <a href="{{ route('client.raised-tickets.get') }}" class="btn custom-btn transparent">View
                                    Tickets</a>
                            </div>
                            <div class="col-md-3 dashboard-cards"
                                style="margin: 10px 10px 0px 10px !important; min-width: 356px;">
                                <figure>
                                    <img src="https://designcrm.net/images/card-icon-2.png" class="img-fluid"
                                        alt="">
                                </figure>
                                <h2 class="heading-2">
                                    <span class="d-block">
                                        Get started with
                                    </span>
                                    Your Project
                                </h2>
                                <p class="description_para">
                                    Begin by filling out the brief forms to kickstart your creative journey.
                                </p>
                                <a href="{{ route('client.brief.get') }}" class="btn custom-btn transparent">View
                                    brief forms</a>
                            </div>
                            <div class="col-md-3 dashboard-cards"
                                style="margin: 10px 10px 0px 10px !important; min-width: 356px;">
                                <figure>
                                    <img src="https://designcrm.net/images/card-icon-3.png" class="img-fluid"
                                        alt="">
                                </figure>
                                <h2 class="heading-2">
                                    <span class="d-block">
                                        Find Your
                                    </span>
                                    Invoices
                                </h2>
                                <p class="description_para">
                                    Easily find, view, and pay your invoices in one place.
                                </p>
                                <a href="{{ route('client.invoice.get') }}" class="btn custom-btn transparent">View
                                    invoices</a>
                            </div>
                        </div>
                    </div>
                    <div class="chart-parent">
                        <div class="chart-header">
                            <h3 class="heading-2">
                                Service Progress
                            </h3>
                        </div>
                        <hr>
                        <div class="chart-container">
                            <canvas id="leadStatusChart" width="100%" style="max-height: 300px !important;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        const progressData = @json($chartData);

        const labels = progressData.map(p => p.status);
        const counts = progressData.map(p => p.count);

        const ctx = document.getElementById('leadStatusChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Service Progress',
                    data: counts,
                    borderRadius: 12,
                    backgroundColor: [
                        '#673187', // In progress
                        '#FFCE56', // On Hold
                        '#db165b', // Completed
                        '#FF6384', // Cancelled
                        '#9966FF' // New / others
                    ],
                }]
            },
            options: {
                indexAxis: 'x', // ⬅️ makes it horizontal
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    // title: {
                    //     display: true,
                    //     text: 'Service Progress',
                    //     font: {
                    //         size: 20,
                    //         weight: 'bold'
                    //     }
                    // }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        },
                        grid: {
                            color: '#eee'
                        }
                    },
                    y: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    </script>


@endsection
