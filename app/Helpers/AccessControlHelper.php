<?php


function isWithinWorkingHours(): bool
{
    $now = now()->timezone('Asia/Karachi');

    $start = $now->copy()->setTime(21, 0); // 9 PM
    $end = $now->copy()->addDay()->setTime(6, 0); // 6 AM next day

    if ($now->hour < 6) {
        $start->subDay();
        $end->subDay();
    }

    return $now->between($start, $end);
}

function isOfficeIp(): bool
{
    $allowedIps = [
        '119.73.104.124', // Sales Dept or your system IP
        'X.X.X.X',        // Add more IPv4 addresses for other departments
    ];

    return in_array(request()->ip(), $allowedIps);
}


// function isWithinWorkingHours(): bool
// {
//     $now = now()->timezone('Asia/Karachi'); // use correct timezone
//     $start = $now->copy()->setTime(9, 0);  // 9:00 AM
//     $end = $now->copy()->setTime(18, 0);   // 6:00 PM

//     return $now->between($start, $end);
// }
