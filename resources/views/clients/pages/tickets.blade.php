@extends('clients.layouts.layout')

@section('title', 'Client | Tickets')

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
                <!-- Table for larger screens -->
                <div class="col-lg-12 table-responsive d-none d-md-block">
                    <table class="table table-hover table-bordered mb-0">
                        <thead class="colored-table-row"
                            style="background: linear-gradient(135deg, #db165b, #673187, #f7b63e); color: white;">
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Package / Brand</th>
                                <th scope="col">Seller</th>
                                <th scope="col">Service</th>
                                <th scope="col">Status</th>
                                <th scope="col" class="text-end">Subject</th>
                                <th scope="col" class="text-end">Priority</th>
                                <th scope="col" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($tickets as $ticket)
                                <tr>
                                    <td>
                                        <span
                                            class="btn btn-sm btn-dark">#{{ str_pad($ticket->id, 6, '0', STR_PAD_LEFT) }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $ticket->brand->brand_name ?? '—' }}</div>
                                        <div class="text-muted small">
                                            {{ $ticket->buyer_name ?? ($ticket->client->name ?? '—') }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $ticket->seller->name ?? '—' }}</div>
                                        <div class="text-muted text-sm small">
                                            {{ $ticket->seller->email ?? ($ticket->seller->email ?? '—') }}</div>
                                    </td>
                                    <td>
                                        <span
                                            class="btn btn-info btn-sm mb-1">{{ $ticket->order->service_name ?? '—' }}</span>
                                    </td>
                                    <td>{!! $ticket->status !!}</td>
                                    <td class="text-end">{{ $ticket->subject }}</td>
                                    <td class="text-end">{{ $ticket->priority }}</td>
                                    <td class="text-center">
                                        <a href="" class="btn btn-sm btn-info" title="Invoice detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">No tickets raised yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    @if ($tickets->hasPages())
                        <div class="card-footer">
                            {{ $tickets->links() }}
                        </div>
                    @endif
                </div>

                <!-- Card layout for mobile devices -->
                <div class="d-md-none">
                    @foreach ($tickets as $ticket)
                        <div class="card p-3 mb-3">
                            <div class="row">
                                <!-- Left side (titles) for 4 columns -->
                                <div class="col-6">
                                    <div><strong>#</strong>: {{ str_pad($ticket->id, 6, '0', STR_PAD_LEFT) }}</div>
                                    <div><strong>Package / Brand</strong>: {{ $ticket->brand->brand_name ?? '—' }}</div>
                                    <div><strong>Seller</strong>: {{ $ticket->seller->name ?? '—' }}</div>
                                    <div><strong>Service</strong>: {{ $ticket->order->service_name ?? '—' }}</div>
                                </div>

                                <!-- Right side (data for the 4 columns) -->
                                <div class="col-6">
                                    <div><strong>Status</strong>: {!! $ticket->status !!}</div>
                                    <div><strong>Subject</strong>: {{ $ticket->subject }}</div>
                                    <div><strong>Priority</strong>: {{ $ticket->priority }}</div>
                                    <div class="text-center">
                                        <a href="" class="btn btn-sm btn-info" title="Invoice detail">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>


@endsection
