@extends('admin.layout.layout')

@section('title', 'Seller | Client Briefs')

@section('admin-content')

    <style>
        .nav-tabs .nav-link.active,
        .nav-tabs .nav-item.show .nav-link {
            background: linear-gradient(135deg, #db165b, #673187, #f7b63e);
            color: white !important;
            border-color: #e3e5ef #e3e5ef #ffffff;
        }
    </style>

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="heading d-flex justify-content-between">
                    <div>
                        <h1 class="fw-bold" style="color: #003C51;">{{ $client->name }}’s Project Briefs</h1>
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
        @php
            // Only show brief for the **first order of each service**
            $filteredOrders = collect();
            $seenServices = [];

            foreach ($orders as $order) {
                $service = $order->service_name;

                // Skip renewal orders if original exists
                if (isset($seenServices[$service])) {
                    continue;
                }

                // Mark service as seen
                $seenServices[$service] = true;

                // Push order to filtered list (prefer original over renewal)
                $filteredOrders->push($order);
            }
        @endphp
        <div class="row my-5">
            <div class="col-lg-12">
                <!-- Bootstrap 4 Tabs -->
                <ul class="nav nav-tabs" id="briefTab" role="tablist">
                    @foreach ($filteredOrders as $index => $order)
                        <li class="nav-item">
                            <a class="text-dark nav-link mx-1 {{ $loop->first ? 'active' : '' }}" id="tab-{{ $index }}"
                                data-toggle="tab" href="#pane-{{ $index }}" role="tab"
                                aria-controls="pane-{{ $index }}"
                                aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                {{ $order->service_name }} (INV#000{{ $order->id }})
                            </a>
                        </li>
                    @endforeach
                </ul>
                <!-- Tab Content -->
                <div class="tab-content mt-4" id="briefTabContent">
                    @forelse($filteredOrders as $index => $order)
                        @php
                            $brief = $order->brief;
                            $service = $order->service_name;
                        @endphp

                        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="pane-{{ $index }}"
                            role="tabpanel">

                            @if ($service == 'Logo Design')
                                @include('questionnaires.logo', compact('order', 'brief'))
                            @elseif ($service == 'Video Animation')
                                @include('questionnaires.video-animation', compact('order', 'brief'))
                            @elseif ($service == 'Content Writing')
                                @include('questionnaires.content', compact('order', 'brief'))
                            @elseif ($service == 'Website Design & Development')
                                @include('questionnaires.web', compact('order', 'brief'))
                            @elseif ($service == 'Social Media Marketing')
                                @include('questionnaires.social-media', compact('order', 'brief'))
                            @elseif ($service == 'Merchandise')
                                @include('questionnaires.merchandise', compact('order', 'brief'))
                            @elseif ($service == 'Domain & Hosting')
                                @include('questionnaires.domain-hosting', compact('order', 'brief'))
                            @elseif ($service == 'Online Reputation Management')
                                @include('questionnaires.online-reputation', compact('order', 'brief'))
                            @elseif ($service == 'Ebook Design & Formatting Brief')
                                @include('questionnaires.ebook', compact('order', 'brief'))
                            @endif
                        </div>

                    @empty
                        <div class="text-center alert alert-info m-0">
                            <h6>The brief is not filled yet !!!</h6>
                        </div>
                    @endforelse

                </div>
            </div>
        </div>
    </div>

@endsection
