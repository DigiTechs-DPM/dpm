@include('emails.layout', [
    'title' => "Ticket Deadline Notification",
    'body' => "
        Hello <strong>{$client->name}</strong>,<br><br>

        This is a reminder regarding your active ticket.<br><br>

        <strong>Ticket:</strong> {$ticket->title}<br>
        <strong>Order ID:</strong> #{$order->id}<br>
        <strong>Deadline:</strong> {$ticket->deadline->toDayDateTimeString()}<br>
        <strong>Status:</strong> {$ticket->status}<br><br>

        <strong>Reminder Type:</strong> " . ucfirst($stage) . "<br><br>
    "
])
