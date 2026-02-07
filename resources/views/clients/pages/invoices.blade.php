@extends('clients.layouts.layout')

@section('title', 'Client | Invoices')

@php
    function status_badge($status)
    {
        $class = match ($status) {
            'paid' => 'text-success',
            'pending' => 'text-warning',
            'canceled' => 'text-danger',
            default => 'text-muted',
        };
        return '<span class="' . $class . '">' . ucfirst($status) . '</span>';
    }
@endphp

@section('mian-content')

    <section class="invoice-listing">
        <div class="container bg-colored">
            <form action="" method="GET">
                <div class="row align-items-start invoice-listing-select-bar">
                    <div class="col-lg-3">
                        <input type="text" class="form-control" placeholder="Search Package" name="package" value=""
                            style="border-radius: 20px;">
                    </div>
                    <div class="col-lg-3">
                        <input type="text" class="form-control" placeholder="Search Invoice#" name="invoice"
                            value="" style="border-radius: 20px;">
                    </div>
                    <div class="col-lg-3">
                        <select class="form-select" aria-label="Default select example" name="status" id="status">
                            <option selected>Select Status</option>
                            <option value="0" selected>Any</option>
                            <option value="2">Paid</option>
                            <option value="1">Unpaid</option>
                        </select>
                    </div>
                    <div class="col-lg-3">
                        <div class="profile-details-save-btn">
                            <button class="btn custom-btn blue">
                                Search Result
                            </button>
                        </div>
                    </div>
                </div>
            </form>
            <div class="row align-items-center">
                <div class="table-responsive">
                    <!-- Table for larger screens -->
                    <div class="d-none d-md-block">
                        <table class="table table-striped">
                            <thead class="text-white" style="background: #000;">
                                <tr>
                                    <th>#</th>
                                    <th>Package / Brand</th>
                                    <th>Service</th>
                                    <th>Status</th>
                                    <th>Total</th>
                                    <th>Paid</th>
                                    <th>Due</th>
                                    <th>Actions</th>
                                    <th>Order Type</th>
                                </tr>
                            </thead>
                            <tbody class="border text-center">
                                @forelse ($orders as $order)
                                    <tr>
                                        <td>{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</td>
                                        <td>
                                            <div class="fw-semibold">{{ $order->brand->brand_name ?? '—' }}</div>
                                            <div class="text-muted small">
                                                {{ $order->buyer_name ?? ($order->client->name ?? '—') }}</div>
                                        </td>
                                        <td><span class="btn btn-info btn-sm mb-1">{{ $order->service_name ?? '—' }}</span>
                                        </td>
                                        <td>{!! status_badge($order->status) !!}</td>
                                        <td>{{ money_cents($order->unit_amount, $order->currency) }}</td>
                                        <td>{{ money_cents($order->amount_paid, $order->currency) }}</td>
                                        <td>{{ money_cents($order->balance_due, $order->currency) }}</td>
                                        <td>
                                            <a href="{{ route('client.invoice.details', $order) }}"
                                                class="btn btn-sm btn-info" title="Invoice detail">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            <a href="{{ route('client.raise-ticket.get', $order) }}"
                                                class="btn btn-sm btn-info" title="Pay now">
                                                <i class="fas fa-plus"></i> Raise Ticket
                                            </a>
                                        </td>
                                        <td>
                                            @if ($order->order_type === 'renewal')
                                                <span
                                                    class="badge badge-danger badge-sm text-white rounded-pill shadow-sm btn-info">
                                                    <i class="fa fa-sync-alt me-1"></i> Renewed
                                                </span>
                                            @else
                                                <span
                                                    class="badge badge-success badge-sm text-white rounded-pill shadow-sm btn-info">
                                                    <i class="fa fa-sync-alt me-1"></i> Original
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted">No orders yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Card layout for mobile devices -->
                    <div class="d-md-none">
                        @forelse ($orders as $order)
                            <div class="card p-3 mb-3">
                                <div class="row">
                                    <!-- Left side (titles) for 4 columns -->
                                    <div class="col-6">
                                        <div><strong>#</strong>: {{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</div>
                                        <div><strong>Package / Brand</strong>: {{ $order->brand->brand_name ?? '—' }}</div>
                                        <div><strong>Service</strong>: {{ $order->service_name ?? '—' }}</div>
                                        <div><strong>Status</strong>: {!! status_badge($order->status) !!}</div>
                                    </div>

                                    <!-- Right side (data for the 8 columns) -->
                                    <div class="col-6">
                                        <div><strong>Total</strong>:
                                            {{ money_cents($order->unit_amount, $order->currency) }}</div>
                                        <div><strong>Paid</strong>:
                                            {{ money_cents($order->amount_paid, $order->currency) }}</div>
                                        <div><strong>Due</strong>: {{ money_cents($order->balance_due, $order->currency) }}
                                        </div>
                                        <div class="text-center">
                                            <a href="{{ route('client.invoice.details', $order) }}"
                                                class="btn btn-sm btn-info" title="Invoice detail">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            <a href="{{ route('client.raise-ticket.get', $order) }}"
                                                class="btn btn-sm btn-info" title="Pay now">
                                                <i class="fas fa-plus"></i> Raise Ticket
                                            </a>
                                        </div>
                                        <div>
                                            @if ($order->order_type === 'renewal')
                                                <span
                                                    class="badge badge-danger badge-sm text-white rounded-pill shadow-sm btn-info">
                                                    <i class="fa fa-sync-alt me-1"></i> Renewed
                                                </span>
                                            @else
                                                <span
                                                    class="badge badge-success badge-sm text-white rounded-pill shadow-sm btn-info">
                                                    <i class="fa fa-sync-alt me-1"></i> Original
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted">No orders yet.</div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    @if ($orders->hasPages())
                        <div class="card-footer">
                            {{ $orders->links() }}
                        </div>
                    @endif
                </div>


            </div>
        </div>
    </section>


@endsection
