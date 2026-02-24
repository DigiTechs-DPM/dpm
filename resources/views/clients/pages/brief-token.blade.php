@extends('clients.layouts.layout')

@section('title', 'Client | Brief Form')

@section('mian-content')

    @php
        $map = [
            'Logo Design' => 'logo',
            'Video Animation' => 'video-animation',
            'Content Writing' => 'content',
            'Website Design & Development' => 'web',
            'Social Media Marketing' => 'social-media',
            'Merchandise' => 'merchandise',
            'Domain & Hosting' => 'domain-hosting',
            'Online Reputation Management' => 'online-reputation',
            'Ebook Design & Formatting Brief' => 'ebook',
        ];

        $viewKey = $map[$order->service_name] ?? \Illuminate\Support\Str::slug($order->service_name);
    @endphp

    <div class="container bg-colored">
        <h2 class="heading-2">Fill in the questionnaire to get started!</h2>

        @includeIf("clients.pages.questionnaires.$viewKey", [
            'order' => $order,
            'brief' => $brief, // meta array
            'questionnair' => $questionnair, // model
            'mode' => $mode ?? 'dashboard',
            'token' => $token ?? null,
        ])
    </div>

@endsection
