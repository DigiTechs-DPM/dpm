@extends('admin.layout.layout')

@section('title', 'Admin | Leads')

@section('admin-content')


    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="heading d-flex justify-content-between">
                    <div>
                        <h1 class="fw-bold" style="color: #003C51;">Leads</h1>
                        @if (isAdmin())
                            <a href="{{ route('export.csv', ['table' => 'leads', 'columns' => 'id,name,email,phone,domain,message,status,prediction,meta']) }}"
                                style="text-decoration: none;">
                                {{-- id="download-csv" --}}
                                <button class="btn btn-sm bg-gradient-3" type="button">
                                    <i class="fa fa-file-excel-o"></i> CSV
                                </button>
                            </a>
                            <button type="submit" class="btn btn-sm bg-gradient-3" data-toggle="modal"
                                data-target="#deleteLeads" data-toggle="tooltip" title="Delete Leads"><i
                                    class="fa fa-trash text-white"></i> Leads</button>
                        @endif
                    </div>
                    <div class="d-flex">
                        <div class="examplesearch-form mx-3">
                            <form method="GET" class="flex gap-2">
                                <div class="d-flex">
                                    @php
                                        $statuses = [
                                            'new' => 'New',
                                            'contacted' => 'Contacted',
                                            'qualified' => 'Qualified',
                                            'proposal_sent' => 'Proposal Sent',
                                            'first_paid' => 'First Paid',
                                            'in_progress' => 'In Progress',
                                            'completed' => 'Completed',
                                            'renewal_due' => 'Renewal Due',
                                            'on_hold' => 'On Hold',
                                            'disqualified' => 'Disqualified',
                                            'cancelled' => 'Cancelled',
                                        ];
                                        // Determine selected status
                                        $selectedStatus = $lead->status ?? request('status');
                                    @endphp
                                    <select name="status" class="form-control" onchange="this.form.submit()">
                                        <option value="">All statuses</option>
                                        @foreach ($statuses as $value => $label)
                                            <option value="{{ $value }}"
                                                {{ $selectedStatus === $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button class="btn btn-success bg-gradient-3" type="submit">Filter</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="row my-5">
            <div class="col-lg-12">
                <div class="table-responsive">
                    <table class="table table-striped" id="invoiceTable">
                        <thead class=" text-white text-center" style="background: #000;">
                            <th>#id</th>
                            <th>Seller</th>
                            <th>Client</th>
                            <th>Lead Domain</th>
                            <th>Payment</th>
                            <th>Assigned Seller</th>
                            <th>Assigned Status</th>
                            <th>Status</th>
                            <th>Action</th>
                        </thead>
                        <tbody class="border text-center">
                            @if ($leads->isEmpty())
                                <tr>
                                    <td colspan="12">
                                        <div class="alert alert-info m-0">
                                            <h6>You don't have any leads assigned yet !!!</h6>
                                        </div>
                                    </td>
                                </tr>
                            @else
                                @foreach ($leads as $i => $lead)
                                    @php
                                        $seller = auth('seller')->user();
                                        $isSeller = auth('seller')->check() && !auth('admin')->check();
                                        $ownsLead =
                                            $isSeller &&
                                            $seller->id === $lead->seller_id &&
                                            $seller->brand_id === $lead->brand_id;
                                    @endphp
                                    <tr>
                                        <td>
                                            {{ $leads->firstItem() + $i }}
                                        </td>
                                        <td>
                                            @if ($isSeller && $seller->id == $lead->seller->id)
                                                <div class="text-muted fw-semibold">It's your's</div>
                                            @else
                                                <div class="fw-semibold">{{ $lead->seller->name ?? '—' }}</div>
                                                <div class="text-muted small">
                                                    {{ $lead->seller->email ?? ($lead->seller->sudo_name ?? '—') }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="blurred">
                                            {{ $lead->name }}
                                            <a href="mailto:{{ $lead->email }}">
                                                <div class="text-muted small">
                                                    {{ $lead->email ?? ($lead->email ?? '—') }}
                                                </div>
                                            </a>
                                            <a href="tel:{{ $lead->phone }}">
                                                <span class="text-muted small">
                                                    {{ $lead->phone ?? ($lead->phone ?? '—') }}
                                                </span>
                                            </a>
                                        </td>
                                        <td>
                                            <div>
                                                {{ $lead->domain_url }}
                                            </div>
                                            <span class="small text-muted">Servcie:
                                                {{ $lead->meta['service'] ?? '—' }}</span>
                                        </td>
                                        @php
                                            $authSeller = auth('seller')->user();
                                            $isAdmin = auth('admin')->check();
                                            $role = $authSeller?->role ?? $authSeller?->is_seller; // 'front_seller' | 'project_manager' | null
                                            $isFront = $role === 'front_seller';
                                            $canGenerateFirst = false;
                                            if ($isAdmin) {
                                                // Admins can always generate links
                                                $canGenerateFirst = true;
                                            } elseif (
                                                $isFront &&
                                                $authSeller &&
                                                (int) $authSeller->brand_id === (int) $lead->brand_id
                                            ) {
                                                // Only front_seller in same brand can generate
                                                $canGenerateFirst = true;
                                            }
                                            $orderId = $lead->latest_order_id;
                                            $due = (int) ($lead->latest_order_balance_due ?? 0);
                                            $currency = $lead->latest_order_currency ?? 'USD';
                                            $hasOrder = !empty($orderId);
                                            $isPaidAll = $hasOrder && $due <= 0;

                                            // ✅ Can change lead status if Admin OR Seller
                                            $canChangeStatus = $isAdmin || $isSeller;
                                        @endphp
                                        <td>
                                            @if (!$hasOrder)
                                                <span class="badge badge-secondary"><i class="fa fa-info-circle"></i> No
                                                    order
                                                    yet</span>
                                            @elseif ($isPaidAll)
                                                <span class="badge badge-success"><i class="fa fa-check-circle"></i> Paid in
                                                    Full</span>
                                            @else
                                                <span class="badge badge-warning">
                                                    <i class="fa fa-clock"></i> Due: {{ number_format($due / 100, 2) }}
                                                    {{ $currency }}
                                                </span>
                                            @endif
                                            @if ($canGenerateFirst)
                                                @if (!$hasOrder)
                                                    <a
                                                        href="{{ route('generate-link-form', ['brand' => $lead->brand_id, 'lead' => $lead->id]) }}">
                                                        <button type="button" class="badge badge-info mt-2">
                                                            <i class="fa fa-plus-circle"></i> Generate Link
                                                        </button>
                                                    </a>
                                                @elseif (!$isPaidAll)
                                                    <small class="d-block text-muted mt-2">Create next link from
                                                        Orders</small>
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            <form method="POST" action="{{ route('lead-assign.post') }}">
                                                @csrf
                                                <input type="hidden" name="lead_id" value="{{ $lead['id'] }}">
                                                <select name="seller_id" class="form-select form-select-sm form-control"
                                                    onchange="this.form.submit()">
                                                    <option value="">-- select --</option>
                                                    @foreach ($pmSellers as $pmSeller)
                                                        <option value="{{ $pmSeller->id }}"
                                                            @if ($lead->assignments->isNotEmpty() && $lead->assignments->first()->assigned_to == $pmSeller->id) selected @endif>
                                                            {{ $pmSeller->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </form>
                                        </td>
                                        <td>
                                            @if ($lead->latestAssignment)
                                                <span
                                                    class="badge {{ $lead->latestAssignment->status === 'pending'
                                                        ? 'bg-warning'
                                                        : ($lead->latestAssignment->status === 'assigned'
                                                            ? 'bg-info'
                                                            : 'bg-success') }}">
                                                    {{ ucfirst($lead->latestAssignment->status) }}
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">Not Assigned</span>
                                            @endif
                                        </td>

                                        <td>
                                            @if ($canChangeStatus)
                                                <form method="POST" action="{{ route('lead.update-status') }}">
                                                    @csrf
                                                    <input type="hidden" name="lead_id" value="{{ $lead['id'] }}">

                                                    <select name="status" class="form-control"
                                                        onchange="this.form.submit()">
                                                        <option value="new"
                                                            {{ $lead->status == 'new' ? 'selected' : '' }}>New</option>
                                                        <option value="contacted"
                                                            {{ $lead->status == 'contacted' ? 'selected' : '' }}>Contacted
                                                        </option>
                                                        <option value="qualified"
                                                            {{ $lead->status == 'qualified' ? 'selected' : '' }}>Qualified
                                                        </option>
                                                        <option value="proposal_sent"
                                                            {{ $lead->status == 'proposal_sent' ? 'selected' : '' }}>
                                                            Proposal Sent</option>
                                                        <option value="first_paid"
                                                            {{ $lead->status == 'first_paid' ? 'selected' : '' }}>First
                                                            Paid</option>
                                                        <option value="in_progress"
                                                            {{ $lead->status == 'in_progress' ? 'selected' : '' }}>In
                                                            Progress</option>
                                                        <option value="completed"
                                                            {{ $lead->status == 'completed' ? 'selected' : '' }}>Completed
                                                        </option>
                                                        <option value="renewal_due"
                                                            {{ $lead->status == 'renewal_due' ? 'selected' : '' }}>Renewal
                                                            Due</option>
                                                        <option value="on_hold"
                                                            {{ $lead->status == 'on_hold' ? 'selected' : '' }}>On Hold
                                                        </option>
                                                        <option value="disqualified"
                                                            {{ $lead->status == 'disqualified' ? 'selected' : '' }}>
                                                            Disqualified</option>
                                                        <option value="cancelled"
                                                            {{ $lead->status == 'cancelled' ? 'selected' : '' }}>Cancelled
                                                        </option>
                                                    </select>
                                                </form>
                                            @else
                                                <button type="button" class="badge badge-secondary" disabled>
                                                    <i class="fa fa-lock"></i> {{ $lead->status }}
                                                </button>
                                            @endif

                                        </td>
                                        <td>
                                            <a href="{{ route('admin.lead-details.get', $lead->id) }}"
                                                style="text-decoration: none;">
                                                <button type="button" class="badge badge-light">
                                                    <i class="fa fa-eye text-info"></i>
                                                </button>
                                            </a>
                                            <a href="{{ route('admin.leads.delete', $lead->id) }}">
                                                <button type="button" class="badge badge-primary">
                                                    <i class="fa fa-trash "></i>
                                                </button>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>

                    </table>
                </div>
                <div class="paginate d-flex justify-content-center align-item-center bg-light p-2"
                    style="border-radius:10px;">
                    <div class="text-dark pt-3">
                        {{ $leads->links() }}
                        <div hidden>
                            @if ($leads->lastPage() > 1)
                                <ul class="pagination justify-content-center">
                                    <li class="page-item {{ $leads->currentPage() == 1 ? ' disabled' : '' }}">
                                        <a class="page-link border_none_pagination"
                                            href="{{ $leads->url($leads->currentPage() - 1) }}">Previous</a>
                                    </li>
                                    @for ($i = $leads->currentPage(); $i <= $leads->currentPage() + 8; $i++)
                                        <li class="page-item">
                                            <a class="page-link {{ $leads->currentPage() == $i ? ' border_active' : 'border_non_active' }} border_none2"
                                                href="{{ $leads->url($i) }}">{{ $i }}</a>
                                        </li>
                                    @endfor
                                    <li
                                        class="page-item {{ $leads->currentPage() == $leads->lastPage() ? ' disabled' : '' }}">
                                        <a class="page-link border_none_pagination"
                                            href="{{ $leads->url($leads->currentPage() + 1) }}">Next</a>
                                    </li>
                                </ul>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="deleteLeads" data-backdrop="static" data-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Lead Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('admin.leads.delete') }}" class="leadform" id="form1">
                        @csrf
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="form-group mb-3">
                                    <select name="status" class="form-control">
                                        <option value="new">New</option>
                                        <option value="contacted">Contacted</option>
                                        <option value="qualified">Qualified</option>
                                        <option value="proposal_sent">Proposal Sent</option>
                                        <option value="first_paid">First Paid</option>
                                        <option value="in_progress">In Progress</option>
                                        <option value="completed">Completed</option>
                                        <option value="renewal_due">Renewal Due</option>
                                        <option value="on_hold">On Hold</option>
                                        <option value="disqualified">Disqualified</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                </div>
                            </div>
                            <hr>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="d-flex align-items-center justify-content-center text-center m-auto">
                                    <button class="btn btn-success text-white">Submit</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Convert table to CSV (with skipped columns)
        function tableToCSV(skipCols = []) {
            let csv = [];
            let rows = document.querySelectorAll("#invoiceTable tr");

            for (let row of rows) {
                let cols = row.querySelectorAll("td, th");
                let rowData = [];
                cols.forEach((col, index) => {
                    if (!skipCols.includes(index)) { // Skip unwanted column indexes
                        rowData.push('"' + col.innerText.replace(/"/g, '""') + '"');
                    }
                });
                csv.push(rowData.join(","));
            }
            return csv.join("\n");
        }

        // Download CSV file
        document.getElementById("download-csv").addEventListener("click", function() {
            // Example: skip column 0 and last column
            let csv = tableToCSV([0, 6]);
            let blob = new Blob([csv], {
                type: "text/csv"
            });
            let link = document.createElement("a");
            link.href = window.URL.createObjectURL(blob);
            link.download = "client-leads.csv";
            link.click();
        });
    </script>



@endsection
