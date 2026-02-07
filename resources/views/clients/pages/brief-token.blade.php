@extends('clients.layouts.layout')

@section('title', 'Client | Brief Form')

@section('mian-content')

    <div class="container bg-colored">
        <h2 class="heading-2">Fill in the questionnaire to get started!</h2>
        {{-- Automatically load the correct brief file --}}
        @includeIf('clients.pages.questionnaires.' . Str::slug($order->service_name), [
            'order' => $order,
            'brief' => $brief,
            'questionnair' => $questionnair,
        ])
    </div>
@endsection
