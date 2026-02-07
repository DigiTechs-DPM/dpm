@include('emails.tickets.layout', [
    'title' => "New Ticket Created",
    'body' => "
        Hello <strong>{$client->name}</strong>,<br><br>

        A new support ticket has been created for your order.<br><br>

        <strong>Order ID:</strong> #{$order->id}<br>
        <strong>Subject:</strong> {$ticket->subject}<br>
        <strong>Priority:</strong> {$ticket->priority}<br>
        <strong>Status:</strong> {$ticket->status}<br>
        <strong>Description:</strong><br>
        {$ticket->description}<br><br>

        <a href='{$url}'
            style=\"display:inline-block;
            background: linear-gradient(135deg,#db165b,#673187,#f7b63e);
            color:#fff !important;
            text-decoration:none;
            padding:12px 25px;
            border-radius:6px;
            font-weight:bold;\">
            View Ticket
        </a>
    "
])
