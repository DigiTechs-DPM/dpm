@extends('clients.layouts.layout')

@section('title', 'CRM | Invoice Details')

@section('mian-content')

    <style>
        .invoice-listing .card::before {
            content: "";
            position: absolute;
            inset: 0;
            background: url(http://127.0.0.1:8000/admin-assets/dpm-logos/dpm-fav.png) center / 220px no-repeat;
            opacity: 0.20;
            z-index: -1;
            background-position: center;
            background-repeat: no-repeat;
            background-size: contain;
        }

        .invoice-listing .card {
            position: relative;
            z-index: 100;
        }
    </style>

    <section class="invoice-listing invoice">
        <div class="container bg-colored" id="selector">
            <div class="row align-items-start invoice-listing-select-bar">
                <div class="card invoiceCard">
                    <div class="row g-4">
                        <div class="col-lg-7">
                            <div class="invoice-header d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h2 class="h4 mb-1">Invoice #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</h2>
                                    <p class="mb-0 text-muted">Issued on {{ $order->created_at?->toFormattedDateString() }}
                                    </p>
                                </div>
                                <div>
                                    <button id="generate-pdf" class="btn btn-light border pdfBtn">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="width: 50px;">
                                            <title>file-pdf-box</title>
                                            <path
                                                d="M19 3H5C3.9 3 3 3.9 3 5V19C3 20.1 3.9 21 5 21H19C20.1 21 21 20.1 21 19V5C21 3.9 20.1 3 19 3M9.5 11.5C9.5 12.3 8.8 13 8 13H7V15H5.5V9H8C8.8 9 9.5 9.7 9.5 10.5V11.5M14.5 13.5C14.5 14.3 13.8 15 13 15H10.5V9H13C13.8 9 14.5 9.7 14.5 10.5V13.5M18.5 10.5H17V11.5H18.5V13H17V15H15.5V9H18.5V10.5M12 10.5H13V13.5H12V10.5M7 10.5H8V11.5H7V10.5Z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div class="summary mb-3">
                                <h3 class="h5">Summary</h3>
                                <table class="table mail-table border-0 mb-0">
                                    <thead>
                                        <tr>
                                            <th scope="col" style="width:120px;">To</th>
                                            <th scope="col">{{ $client->name ?? ($order->buyer_name ?? '—') }}</th>
                                            <th scope="col">{{ $client->email ?? ($order->buyer_email ?? '—') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th scope="row">From</th>
                                            <td>{{ $order->brand->brand_name ?? '—' }}</td>
                                            <td>{{ config('mail.from.address') }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="border mb-3"></div>

                            <div class="cost-break-down-table">
                                <table class="table table-hover table-bordered">
                                    <thead>
                                        <tr class="colored-table-row">
                                            <th scope="col">Services</th>
                                            <th scope="col" class="text-end">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{ $order->service_name }}</td>
                                            <td class="text-end">{{ money_cents($order->unit_amount, $order->currency) }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            @if ($order->payments->count())
                                <div class="mt-4">
                                    <h5 class="mb-2">Payments</h5>
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th class="text-end">Amount</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($order->payments as $p)
                                                <tr>
                                                    <td>{{ $p->created_at?->toDayDateTimeString() }}</td>
                                                    <td class="text-end">{{ money_cents($p->amount, $p->currency) }}</td>
                                                    <td>{{ ucfirst($p->status) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif

                        </div>
                        <div class="col-lg-5">
                            <div class="p-3 border rounded mb-3">
                                <p class="fs-4 mb-2">
                                    <strong>{{ money_cents($order->unit_amount, $order->currency) }}</strong>
                                </p>
                                <div class="d-flex align-items-center">
                                    <badge class="badge bg-success">{{ ucfirst($order->status) }}</badge>
                                </div>

                                <div class="mt-2 small text-muted">
                                    Paid: {{ money_cents($order->amount_paid, $order->currency) }} &nbsp;|&nbsp;
                                    Due: {{ money_cents($order->balance_due, $order->currency) }}
                                </div>
                            </div>

                            <div class="main-id-card p-3 border rounded">
                                <div class="main-id-user mb-3">
                                    <p class="mb-1"><span class="me-2"><i class="fa-regular fa-user"></i></span>
                                        <span>{{ $client->name ?? ($order->buyer_name ?? '—') }}</span>
                                    </p>
                                    <p class="mb-1"><span class="me-2"><i
                                                class="fa-regular fa-calendar-days"></i></span>
                                        <span>{{ $order->created_at?->toFormattedDateString() }}</span>
                                    </p>
                                    <p class="mb-1"><span class="me-2"><i class="fa-regular fa-folder"></i></span>
                                        <span>{{ $order->service_name }}</span>
                                    </p>
                                    {{-- @if (optional($order->paymentLinks->last())->last_issued_url)
                                        <p class="mb-1"><span class="me-2"><img
                                                    src="https://designcrm.net/images/link-img.png" class="img-fluid"
                                                    alt=""></span>
                                            <span class="text-truncate d-inline-block" style="max-width: 100%;">
                                                {{ optional($order->paymentLinks->last())->last_issued_url }}
                                            </span>
                                        </p>
                                    @endif --}}
                                </div>

                                <div class="main-id-date">
                                    <p>
                                        <span class="detail"><i class="fa-regular fa-circle"></i> Invoice created</span>
                                        <span class="date">{{ $order->created_at?->toFormattedDateString() }}</span>
                                    </p>
                                    <p class="main-border">
                                        <span class="detail"><i class="fa-regular fa-circle"></i> Last link issued</span>
                                        <span class="date">
                                            {{ optional($order->paymentLinks->last()?->last_issued_at)?->toFormattedDateString() ?? '—' }}
                                        </span>
                                    </p>
                                    <p class="{{ $order->status === 'paid' ? 'active' : '' }}">
                                        <span class="detail"><i class="fa-regular fa-circle"></i> Invoice paid</span>
                                        <span class="date">{{ $order->paid_at?->toFormattedDateString() ?? '—' }}</span>
                                    </p>
                                </div>

                                <div class="pay-link">
                                    @php
                                        $due = (int) ($order->balance_due ?? 0);
                                        $currency = $order->currency ?? 'USD';
                                    @endphp
                                    @if ($due > 0)
                                        @if (!empty($latestActiveLink))
                                            <div class="main-id-btn mt-3" id="pay">
                                                <a href="{{ $latestActiveLink->last_issued_url ?? route('paylinks.show', $latestActiveLink->token) }}"
                                                    class="btn btn-primary w-100" target="_blank" rel="noopener">
                                                    Pay {{ number_format(($latestActiveLink->unit_amount ?? 0) / 100, 2) }}
                                                    {{ $currency }}
                                                    @if ($latestActiveLink->expires_at)
                                                        <span class="small text-light-50"> (expires
                                                            {{ $latestActiveLink->expires_at->diffForHumans() }})</span>
                                                    @endif
                                                </a>
                                                <small class="text-muted d-block mt-1">
                                                    Outstanding balance: {{ number_format($due / 100, 2) }}
                                                    {{ $currency }}
                                                </small>
                                            </div>
                                        @else
                                            <div class="alert alert-warning mt-3">
                                                An installment is due ({{ number_format($due / 100, 2) }}
                                                {{ $currency }}),
                                                but
                                                no active payment link is available yet.
                                                Please contact your seller for a new link.
                                            </div>
                                        @endif
                                    @else
                                        <div class="alert alert-success mt-3 mb-0">
                                            This invoice is fully paid. Thank you!
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function() {
            const $downloadBtn = $('#generate-pdf');
            const $payLinks = $('.pay-link, .pdfBtn, [data-role="paylink"]');
            // 👆 match your actual pay link buttons (adjust selector as needed)

            $('#generate-pdf').on('click', function() {
                // Hide buttons before screenshot
                $downloadBtn.hide();
                $payLinks.hide();
                const element = document.querySelector('.invoiceCard');
                const {
                    jsPDF
                } = window.jspdf;
                html2canvas(element, {
                        scale: 2,
                        useCORS: true,
                        backgroundColor: '#ffffff',
                        logging: false
                    }).then((canvas) => {
                        const imgData = canvas.toDataURL('image/png');
                        const pdf = new jsPDF('p', 'mm', 'a4');
                        // Keep proportions based on canvas size
                        const pageWidth = pdf.internal.pageSize.getWidth();
                        const imgHeight = (canvas.height * pageWidth) / canvas.width;
                        pdf.addImage(imgData, 'PNG', 0, 0, pageWidth, imgHeight);
                        pdf.save(`invoice-{{ $order->id }}.pdf`);
                    })
                    .catch((err) => console.error('PDF Error:', err))
                    .finally(() => {
                        // Restore buttons after save
                        $downloadBtn.show();
                        $payLinks.show();
                    });
            });
        });
    </script>




@endsection
