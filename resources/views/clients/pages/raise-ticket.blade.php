@extends('clients.layouts.layout')

@section('title', 'CRM | Raised Ticket')

@section('mian-content')

    <style>
        input[type="file"] {
            display: block !important;
        }
    </style>

    <section class="profile-section">
        <div class="container bg-colored">
            <form action="{{ route('client.raised-tickets.post') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row align-items-center mx-auto">
                    <div class="col-lg-10 offset-lg-1">
                        <div class="ticket-form-parent">
                            <h3 class="mb-4 fw-bold">Ticket About: &nbsp; &nbsp; &nbsp; {{ $order->service_name ?? '—' }}
                            </h3>
                            <hr>
                            <input type="hidden" class="form-control" name="order_id" value="{{ $order->id }}">
                            <div class="mb-3">
                                <label for="subject" class="form-label">Subject</label>
                                <input type="text" class="form-control" id="subject" name="subject"
                                    placeholder="Enter ticket subject">
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Message</label>
                                <textarea class="form-control" id="description" name="description" rows="6" placeholder="Describe your issue..."></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="priority" class="form-label">Priority</label>
                                <select class="form-select" id="priority" name="priority">
                                    <option value="medium">Medium</option>
                                    <option value="low">Low</option>
                                    <option value="high">High</option>
                                    <option value="urgent">Urgent</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="attachments" class="form-label">Attachments (optional)</label>
                                <input type="file" class="form-control" name="attachments[]" id="attachments" multiple
                                    accept=".jpg,.jpeg,.png,.pdf,.docx,.xlsx">
                            </div>
                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-primary">Submit Ticket</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </section>

@endsection
