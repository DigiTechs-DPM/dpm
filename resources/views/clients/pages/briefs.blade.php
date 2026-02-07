@extends('clients.layouts.layout')

@section('title', 'Client | Brief')

@section('mian-content')

    <section class="brief-form-section">
        <div class="container bg-colored">
            <h2 class="heading-2">
                Fill in the questionnaire to get started!
            </h2>
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    @php $i = 0; @endphp
                    @foreach ($orders as $order)
                        <button class="nav-link mx-1 {{ $i === 0 ? 'active' : '' }}"
                            id="form-brief-tab-2022-5561-{{ $i }}" data-bs-toggle="tab"
                            data-bs-target="#form-brief-2022-5561-{{ $i }}" type="button" role="tab"
                            aria-controls="form-brief-2022-5561-{{ $i }}"
                            aria-selected="{{ $i === 0 ? 'true' : 'false' }}">
                            {{ $order->service_name }} INV#000{{ $order->id }}
                        </button>
                        @php $i++; @endphp
                    @endforeach
                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                @foreach ($orders as $index => $order)
                    <div class="tab-pane fade show {{ $index === 0 ? 'active' : '' }}"
                        id="form-brief-2022-5561-{{ $index }}" role="tabpanel"
                        aria-labelledby="form-brief-tab-2022-5561-{{ $index }}" tabindex="0">

                        @php
                            $brief = $order->brief; // ✅ Get this order's brief
                        @endphp

                        @if ($order->service_name == 'Logo Design')
                            @include('clients.pages.questionnaires.logo', [
                                'order' => $order,
                                'brief' => $brief,
                            ])
                        @elseif ($order->service_name == 'Video Animation')
                            @include('clients.pages.questionnaires.video-animation', [
                                'order' => $order,
                                'brief' => $brief,
                            ])
                        @elseif ($order->service_name == 'Content Writing')
                            @include('clients.pages.questionnaires.content', [
                                'order' => $order,
                                'brief' => $brief,
                            ])
                        @elseif ($order->service_name == 'Website Design & Development')
                            @include('clients.pages.questionnaires.web', [
                                'order' => $order,
                                'brief' => $brief,
                            ])
                        @elseif ($order->service_name == 'Social Media Marketing')
                            @include('clients.pages.questionnaires.social-media', [
                                'order' => $order,
                                'brief' => $brief,
                            ])
                        @elseif ($order->service_name == 'Merchandise')
                            @include('clients.pages.questionnaires.merchandise', [
                                'order' => $order,
                                'brief' => $brief,
                            ])
                        @elseif ($order->service_name == 'Domain & Hosting')
                            @include('clients.pages.questionnaires.domain-hosting', [
                                'order' => $order,
                                'brief' => $brief,
                            ])
                        @elseif ($order->service_name == 'Online Reputation Management')
                            @include('clients.pages.questionnaires.online-reputation', [
                                'order' => $order,
                                'brief' => $brief,
                            ])
                        @elseif ($order->service_name == 'Ebook Design & Formatting Brief')
                            @include('clients.pages.questionnaires.ebook', [
                                'order' => $order,
                                'brief' => $brief,
                            ])
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </section>

@endsection
