<?php

namespace App\Http\Controllers\API\Client;

use App\Models\Lead;
use App\Models\Order;
use App\Models\ProfileDetail;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PagesController extends Controller
{
    public function clientIndex()
    {
        $client = auth('client')->user();

        // All possible lead statuses (enum list)
        $statuses = [
            'new',
            'contacted',
            'qualified',
            'proposal_sent',
            'first_paid',
            'in_progress',
            'completed',
            'renewal_due',
            'on_hold',
            'disqualified',
            'cancelled',
        ];

        // Count leads grouped by status for this client
        $counts = Lead::where('client_id', $client->id)
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // Prepare chart data ensuring all statuses exist
        $chartData = collect($statuses)->map(function ($status) use ($counts) {
            return [
                'status' => ucfirst(str_replace('_', ' ', $status)),
                'count' => $counts[$status] ?? 0,
            ];
        });

        // Quick debug check (uncomment if chart blank)
        // dd($chartData->toArray());

        return view('clients.pages.dashboard', compact('client', 'chartData'));
    }

    

    public function clientMessages()
    {
        return view('clients.pages.messages');
    }

    public function clientInvoices()
    {
        $client = auth('client')->user();
        // Only this client's orders
        $orders = Order::query()
            ->with(['brand:id,brand_name', 'seller:id,name', 'client:id,name,email'])
            ->where('client_id', $client->id)
            ->paginate(20)
            ->withQueryString();
        // dd($client, $orders);
        return view('clients.pages.invoices', compact('orders', 'client'));
    }

    public function clientInvoiceDetails(Order $order)
    {
        $client = auth('client')->user();

        // dd(auth()->guard('client')->check(), auth('client')->user());
        if (!$client) {
            logger()->error('Client auth failed', [
                'guard' => 'client',
                'auth_check' => auth('client')->check(),
                'session' => session()->all(),
            ]);
            abort(401, 'Client not authenticated');
        }
        // Guard: prevent seeing someone else's order
        // abort_unless($order->client_id === $client->id, 403);

        // Eager-load for display
        $order->load([
            'brand:id,brand_name',
            'seller:id,name',
            'client:id,name,email',
            'paymentLinks:id,order_id,unit_amount,status,paid_at,token,last_issued_url',
            'payments:id,order_id,amount,currency,status,created_at',
        ]);

        // latest ACTIVE (unpaid + not expired) link for THIS order
        $latestActiveLink = $order->paymentLinks()
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->orderByDesc('last_issued_at')
            ->orderByDesc('id')
            ->first();

        // dd($order, $lastLink, $outstandingUrl);
        // return response()->json([
        //     'status'  => true,
        //     'message' => 'Client orders fetched successfully',
        //     'data'    => [
        //         'order' => $order,
        //         'client' => $client,
        //         'lastActiveLink' => $latestActiveLink
        //     ],
        // ]);

        return view('clients.pages.invoice-details', compact('order', 'client', 'latestActiveLink'));
    }

    public function clientProfile()
    {
        $user = Auth::guard('admin')->user()
            ?? Auth::guard('seller')->user()
            ?? Auth::guard('client')->user();
        if (!$user) {
            abort(403, 'No user logged in');
        }
        // Check if profile details exist
        $profile = ProfileDetail::where('user_id', $user->id)
            ->where('user_type', get_class($user))
            ->first();

        // Split name into first/last
        $fullName = $profile->name ?? $user->name;
        $parts = explode(' ', $fullName, 2);
        $firstName = $parts[0] ?? '';
        $lastName  = $parts[1] ?? '';
        // dd($user);
        // return response()->json([
        //     'status'  => true,
        //     'message' => 'Client orders fetched successfully',
        //     'data'    => [
        //         'user' => $user,
        //         'profile' => $profile,
        //         'firstName' => $firstName,
        //         'lastName' => $lastName
        //     ],
        // ]);
        return view('clients.pages.profile', compact('user', 'profile', 'firstName', 'lastName'));
    }
}
