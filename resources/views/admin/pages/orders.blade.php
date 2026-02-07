@extends('admin.layout.layout')

@section('title', 'Admin | Orders')

@section('admin-content')

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="heading d-flex justify-content-between">
                    <div>
                        <h1 class="fw-bold" style="color: #003C51;">Orders</h1>
                        <!-- It display CSV for both admin and white wolf -->
                        <a href="{{ route('export.csv', ['table' => 'orders', 'columns' => 'id,order_type,service_name,currency,unit_amount,amount_paid,balance_due,status,buyer_name,buyer_email,provider_session_id,provider_payment_intent_id,paid_at']) }}"
                            style="text-decoration: none;">
                            <button class="btn btn-sm bg-gradient-3" type="button">
                                <i class="fa fa-file-excel-o"></i> CSV
                            </button>
                        </a>
                    </div>
                    <div class="examplesearch-form mx-3">
                        <form action="" method="" class="example">
                            <div class="d-flex">
                                <input type="text" placeholder="Search.." value="" name="search"
                                    class="form-control">
                                <button type="submit" class="btn text-white bg-gradient-3"><i
                                        class="fa fa-search"></i></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="row my-5 fullInfo">
            <div class="col-lg-12">
                <div class="table-responsive">
                    <table class="table table-striped" id="invoiceTable">
                        <thead class="text-white" style="background:#000;">
                            <tr>
                                <th>#</th>
                                <th>Seller</th>
                                <th>Brand / Service</th>
                                <th>Client</th>
                                <th>Total Amount</th>
                                <th>Paid Amount</th>
                                <th>Due Amount</th>
                                <th>Status</th>
                                <th>Paid at</th>
                                <th class="text-end">Action</th>
                                <th class="text-end">Payment Link</th>
                            </tr>
                        </thead>
                        <tbody class="border">
                            @forelse($orders as $i => $order)
                                @php
                                    $seller = auth('seller')->user();
                                    $isSeller = auth('seller')->check() && !auth('admin')->check();
                                    $ownsOrder =
                                        $isSeller &&
                                        $seller->id === $order->seller_id &&
                                        $seller->brand_id === $order->brand_id;
                                    // dump($seller, $isSeller, $ownsOrder);
                                @endphp
                                <tr>
                                    <td>{{ $orders->firstItem() + $i }}</td>
                                    <td>
                                        @if ($isSeller && $seller->id == $order->seller->id)
                                            <div class="text-muted fw-semibold">It's your's</div>
                                        @else
                                            <div class="fw-semibold">{{ $order->seller->name ?? '—' }}</div>
                                            <div class="text-muted small">
                                                {{ $order->seller->email ?? ($order->seller->sudo_name ?? '—') }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $order->brand->brand_name ?? '—' }}
                                        <p class="text-sm text-muted small">{{ $order->service_name ?? '—' }}</p>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $order->client->name ?? '—' }}

                                        </div>
                                        <div class="text-muted small">
                                            {{ $order->client->email ?? ($order->buyer_email ?? '—') }}
                                        </div>
                                    </td>
                                    <td>
                                        {{ number_format(($order->unit_amount ?? 0) / 100, 2) }}
                                        {{ $order->currency ?? 'USD' }}
                                    </td>
                                    <td>{{ number_format(($order->amount_paid ?? 0) / 100, 2) }}
                                        {{ $order->currency ?? 'USD' }}</td>
                                    <td>{{ number_format(($order->balance_due ?? 0) / 100, 2) }}
                                        {{ $order->currency ?? 'USD' }} </td>

                                    <td>
                                        @php
                                            $due = (int) ($order->balance_due ?? 0);
                                            $sellerUser = auth('seller')->user();
                                            $adminUser = auth('admin')->check();

                                            $isAdmin = $adminUser !== null;
                                            $isFront =
                                                $sellerUser &&
                                                ($sellerUser->role ?? $sellerUser->is_seller) === 'front_seller';
                                            // front seller can generate link for ANY order in their brand
                                            $sameBrand =
                                                $sellerUser && (int) $sellerUser->brand_id === (int) $order->brand_id;

                                            $canGenerateAsFront = $isFront && $sameBrand;
                                            $canGenerateAsAdmin = $isAdmin;
                                            $canGenerate =
                                                ($canGenerateAsFront || $canGenerateAsAdmin) &&
                                                $due > 0 &&
                                                $order->status !== 'paid';
                                        @endphp
                                        @if ($canGenerate)
                                            <a
                                                href="{{ route('generate-link-form', [
                                                    'brand' => $order->brand_id,
                                                    'lead' => $order->lead_id,
                                                    'order' => $order->id,
                                                ]) }}">
                                                <button type="button" class="badge badge-info">
                                                    <i class="fa fa-plus-circle"></i> Generate Link
                                                </button>
                                            </a>
                                            <small class="d-block text-muted mt-2">
                                                Due: {{ number_format($due / 100, 2) }} {{ $order->currency ?? 'USD' }}
                                            </small>
                                        @else
                                            @if ($due <= 0)
                                                <span class="badge badge-success">
                                                    <i class="fa fa-check-circle"></i> Paid in Full
                                                </span>
                                            @else
                                                <button type="button" class="badge badge-secondary" disabled>
                                                    <i class="fa fa-lock"></i> Pending
                                                </button>
                                            @endif
                                        @endif
                                    </td>
                                    <td>{{ optional($order->paid_at)->toDayDateTimeString() ?? '—' }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.order-tickets.get', $order) }}"
                                            style="text-decoration: none;">
                                            <badge type="button" class="badge badge-info">
                                                <i class="fa fa-ticket"></i> Tickets
                                            </badge>
                                        </a>
                                        <a href="{{ route('order.generate-invoice', $order) }}"><button type="submit"
                                                class="badge btn-sm btn-outline-danger">invoice</button></a>
                                        <a href="{{ route('admin.renewed-orders.get', $order->client->id) }}">
                                            <badge type="button" class="badge badge-light">
                                                <i class="fa fa-plus-circle"></i> Check
                                            </badge>
                                        </a>
                                    </td>
                                    <td>
                                        @if ($order->latestPaymentLink->is_active_link)
                                            <a href="javascript:void(0);" class="badge badge-success btn-sm togglePaylink"
                                                data-toggle="tooltip" data-id="{{ $order->latestPaymentLink->id }}"
                                                data-status="false">
                                                Active
                                            </a>
                                            @if ($order->latestPaymentLink->last_issued_url)
                                                <button type="button" class="badge btn-outline-info copyBtn"
                                                    data-url="{{ $order->latestPaymentLink->last_issued_url }}">
                                                    Copy
                                                </button>
                                            @endif
                                        @else
                                            <a href="javascript:void(0);" class="badge badge-danger btn-sm togglePaylink"
                                                data-toggle="tooltip" data-id="{{ $order->latestPaymentLink->id }}"
                                                data-status="true">
                                                Inactive
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12">
                                        <div class="text-center alert alert-info m-0">
                                            <h6>You don't have any orders yet !!!</h6>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="paginate d-flex justify-content-center align-item-center bg-light p-2"
                    style="border-radius:10px;">
                    <div class="text-dark pt-3">
                        {{ $orders->links() }}
                        <div hidden>
                            @if ($orders->lastPage() > 1)
                                <ul class="pagination justify-content-center">
                                    <li class="page-item {{ $orders->currentPage() == 1 ? ' disabled' : '' }}">
                                        <a class="page-link border_none_pagination"
                                            href="{{ $orders->url($orders->currentPage() - 1) }}">Previous</a>
                                    </li>
                                    @for ($i = $orders->currentPage(); $i <= $orders->currentPage() + 8; $i++)
                                        <li class="page-item">
                                            <a class="page-link {{ $orders->currentPage() == $i ? ' border_active' : 'border_non_active' }} border_none2"
                                                href="{{ $orders->url($i) }}">{{ $i }}</a>
                                        </li>
                                    @endfor
                                    <li
                                        class="page-item {{ $orders->currentPage() == $orders->lastPage() ? ' disabled' : '' }}">
                                        <a class="page-link border_none_pagination"
                                            href="{{ $orders->url($orders->currentPage() + 1) }}">Next</a>
                                    </li>
                                </ul>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script>
        $(document).on("click", ".togglePaylink", function() {
            let id = $(this).data("id");
            let newStatus = $(this).data("status");

            $.ajax({
                url: "{{ route('change.paylink-status') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id,
                    is_active_link: newStatus
                },
                success: function(res) {
                    if (res.success) {
                        toastr.success(res.message);
                        setTimeout(() => location.reload(), 800);
                    }
                }
            });
        });


        // copy logic
        $(document).on("click", ".copyBtn", function() {
            let btn = $(this);
            let url = btn.data("url");

            // Copy to clipboard
            navigator.clipboard.writeText(url).then(() => {

                // Change to green + text
                btn.removeClass("badge-info").addClass("badge-success").text("Copied!");

                // Revert after 3 seconds
                setTimeout(() => {
                    btn.removeClass("badge-success").addClass("badge-info").text("Copy");
                }, 3000);
            });
        });
    </script>
@endsection
