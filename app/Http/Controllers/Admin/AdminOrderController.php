<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminOrderController extends Controller
{
    // optimized logic
    public function adminOrders(Request $request)
    {
        $seller = auth('seller')->user();
        $isAdmin = auth('admin')->check();
        if (!$isAdmin) {
            return redirect()->route('admin.login.get')->with('error', 'You must be logged in.');
        }

        $query = Order::with([
            'brand:id,brand_name',
            'client:id,name,email',
            'seller:id,name,email,sudo_name,brand_id',
            'latestPaymentLink:id,order_id,is_active_link,last_issued_url'
        ]);
        // --- Filters ---
        $query
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('brand_id'), fn($q) => $q->where('brand_id', (int) $request->brand_id))
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = trim($request->q);
                $q->where(
                    fn($w) =>
                    $w->where('service_name', 'like', "%{$term}%")
                        ->orWhere('buyer_name', 'like', "%{$term}%")
                        ->orWhere('buyer_email', 'like', "%{$term}%")
                );
            });

        $orders = $query->where('order_type', 'original')->paginate(20)->withQueryString();

        // dd($query,$orders);
        return view('admin.pages.orders', compact('orders'));
    }

    public function adminOrderRenewals(Request $request, int $orderId)
    {
        if (!isSeller() && !isAdmin()) {
            return redirect()->route('admin.login.get')
                ->with('error', 'You must be logged in.');
        }

        // Fetch main/original order using the orderId passed to the route (client_id should match)
        $order = Order::with([
            'brand:id,brand_name',
            'client:id,name,email',
            'seller:id,name,email,sudo_name,brand_id',
            'latestPaymentLink:id,order_id,is_active_link,last_issued_url'
        ])->where('client_id', $orderId) // Use where condition on client_id to find the order
            ->first(); // Use `first()` to get the first matching result

        // Debugging output (check the order retrieved)
        // dd('ok', $order);

        // If no order is found, redirect with an error message
        if (!$order) {
            return redirect()->back()
                ->with('error', 'Order Not Found.');
        }

        // Fetch renewal orders of this specific order (parent_order_id should match the original order's id)
        $renewals = Order::with([
            'brand:id,brand_name',
            'client:id,name,email',
            'seller:id,name,email,sudo_name,brand_id',
            'latestPaymentLink:id,order_id,is_active_link,last_issued_url'
        ])
            ->where('parent_order_id', $order->id) // Get renewal orders based on the original order's id
            ->orderByDesc('created_at')
            ->get(); // Use `get()` to retrieve multiple renewal orders

        return view('admin.pages.renewed-orders', compact('order', 'renewals'));
    }

    // optimize client renewal order
    // public function adminClientRenewedOrders(Request $request, int $clientId)
    // {
    //     $seller = auth('seller')->user();
    //     $isAdmin = auth('admin')->check();
    //     $query = Order::with([
    //         'brand:id,brand_name',
    //         'client:id,name,email',
    //         'seller:id,name,email,sudo_name,brand_id',
    //     ])
    //         ->where('client_id', $clientId)
    //         ->orderByDesc('created_at');

    //     // --- Visibility rules ---
    //     if ($seller) {
    //         $role = $seller->role ?? $seller->is_seller;
    //         $query->where('brand_id', $seller->brand_id);
    //         if ($role !== 'front_seller') {
    //             $query->where('seller_id', $seller->id);
    //         }
    //     } elseif (! $isAdmin) {
    //         return redirect()
    //             ->route('admin.login.get')
    //             ->with('error', 'You must be logged in.');
    //     }

    //     // --- Load once, separate after fetch ---
    //     $orders = $query->get();
    //     $originalOrders = $orders->whereNull('parent_order_id');
    //     $renewalOrders  = $orders->where('order_type', 'renewal');

    //     return view('admin.pages.renewed-orders', compact('originalOrders', 'renewalOrders'));
    // }

    public function adminPMOrders(Request $request)
    {
        $seller = auth('seller')->user();
        $isAdmin = auth('admin')->check();

        $query = Order::with([
            'brand:id,brand_name',
            'client:id,name,email',
            'seller:id,name,email,sudo_name,brand_id',     // ← add this
        ]);

        // Visibility rules (apply to the QUERY, not paginator)
        if ($seller) {
            $role = $seller->role ?? $seller->is_seller; // 'front_seller' | 'project_manager'
            if ($role === 'front_seller') {
                // only all orders
                $query->where('brand_id', $seller->brand_id);
            } else {
                // project_manager: own orders in their brand
                $query->where('brand_id', $seller->brand_id)->where('seller_id', auth('seller')->id());
            }
        } elseif (! $isAdmin) {
            return redirect()
                ->route('admin.login.get')
                ->with('error', 'You must be logged in.');
        }
        // optional filters
        if ($request->filled('status'))   $query->where('status', $request->string('status'));
        if ($request->filled('brand_id')) $query->where('brand_id', (int) $request->brand_id);
        if ($request->filled('q')) {
            $q = trim($request->q);
            $query->where(function ($w) use ($q) {
                $w->where('service_name', 'like', "%{$q}%")
                    ->orWhere('buyer_name', 'like', "%{$q}%")
                    ->orWhere('buyer_email', 'like', "%{$q}%");
            });
        }

        $orders = $query->paginate(20)->withQueryString();
        return view('admin.pages.pm-orders', compact('orders'));
    }

    public function adminPayments(Request $request)
    {
        $seller = auth('seller')->user();
        $isAdmin = auth('admin')->check();

        $query = Order::with([
            'brand:id,brand_name',
            'client:id,name,email',
            'seller:id,name,email,sudo_name,brand_id',     // ← add this
        ]);

        // Visibility rules (apply to the QUERY, not paginator)
        if ($seller) {
            $role = $seller->role ?? $seller->is_seller; // 'front_seller' | 'project_manager'
            if ($role === 'front_seller') {
                // only all orders
                $query->where('brand_id', $seller->brand_id);
            } else {
                // project_manager: own orders in their brand
                $query->where('brand_id', $seller->brand_id)->where('seller_id', auth('seller')->id());
            }
        } elseif (! $isAdmin) {
            return redirect()
                ->route('admin.login.get')
                ->with('error', 'You must be logged in.');
        }
        // optional filters
        if ($request->filled('status'))   $query->where('status', $request->string('status'));
        if ($request->filled('brand_id')) $query->where('brand_id', (int) $request->brand_id);
        if ($request->filled('q')) {
            $q = trim($request->q);
            $query->where(function ($w) use ($q) {
                $w->where('service_name', 'like', "%{$q}%")
                    ->orWhere('buyer_name', 'like', "%{$q}%")
                    ->orWhere('buyer_email', 'like', "%{$q}%");
            });
        }

        $orders = $query->paginate(20)->withQueryString();
        return view('admin.pages.payment-data', compact('orders'));
    }
}
