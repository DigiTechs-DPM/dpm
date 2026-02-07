<?php

namespace App\Http\Controllers\Admin;

use App\Models\Lead;
use App\Models\Order;
use App\Models\Seller;
use Illuminate\Http\Request;
use App\Jobs\NotifyAdminOfLeadView;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class LeadsController extends Controller
{
    public function adminLeads(Request $request)
    {
        $seller  = auth('seller')->user();
        $isAdmin = auth('admin')->check();

        $query = Lead::query()
            ->with([
                'brand:id,brand_name',
                'seller:id,name,email,brand_id,is_seller',
                'client:id,name,email',
                'assignments:id,lead_id,status,assigned_to,assigned_role,assigned_by'
            ])
            ->withCount([
                // only links issued for THIS lead
                'paymentLinks as paid_links_count' => fn($q) => $q->where('status', 'paid'),
            ])
            ->addSelect([
                // strictly the most recent order created for THIS lead
                'latest_order_id' => Order::select('id')
                    ->whereColumn('orders.lead_id', 'leads.id')
                    ->orderByDesc('orders.id')
                    ->limit(1),

                'latest_order_balance_due' => Order::select('balance_due')
                    ->whereColumn('orders.lead_id', 'leads.id')
                    ->orderByDesc('orders.id')
                    ->limit(1),

                'latest_order_currency' => Order::select('currency')
                    ->whereColumn('orders.lead_id', 'leads.id')
                    ->orderByDesc('orders.id')
                    ->limit(1),
            ]);

        if ($seller) {
            $role = $seller->role ?? $seller->is_seller; // 'front_seller'|'project_manager'
            if ($role === 'front_seller') {
                $query->where('brand_id', $seller->brand_id); // all leads in brand
            } else {
                $query->where('seller_id', $seller->id);      // only PM’s own leads
            }
        } elseif (!$isAdmin) {
            return redirect()->route('admin.login.get')->with('error', 'You must be logged in.');
        }

        // filters…
        if ($request->filled('status'))   $query->where('status', $request->string('status'));
        if ($request->filled('brand_id')) $query->where('brand_id', (int)$request->brand_id);
        if ($request->filled('q')) {
            $q = trim($request->q);
            $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%")
                    ->orWhere('message', 'like', "%{$q}%");
            });
        }

        $leads = $query->paginate(20)->withQueryString();
        $pmSellers = Seller::where('is_seller', 'project_manager')->get();
        // dd($leads,$pmSellers);
        return view('admin.pages.leads', compact('leads', 'pmSellers'));
    }

    public function adminLeadDetails($id)
    {
        $lead = Lead::with([
            'brand:id,brand_name,brand_url',
            'seller:id,name,email,brand_id,is_seller',
            'client:id,name,email,phone'
        ])->findOrFail($id);

        // $leadId = $lead->id;
        // $cacheKey = "lead_viewed_{$lead->seller->id}_{$leadId}_" . now()->format('Y-m-d');
        // if (!Cache::has($cacheKey)) {
        //     Cache::put($cacheKey, true, now()->addDay());
        //     // Dispatch async job
        //     NotifyAdminOfLeadView::dispatch($lead->seller, $lead);
        // }
        $user = auth('seller')->user();
        $leadId = $lead->id;
        if ($user) {
            $sessionKey = "viewed_lead_{$leadId}";
            if (!session()->has($sessionKey)) {
                // Store in session
                session()->put($sessionKey, now()->toDateTimeString());
                // Log to custom file
                Log::build([
                    'driver' => 'single',
                    'path' => storage_path('logs/lead-views.log'),
                ])->info('Lead viewed', [
                    'seller_id' => $user->id,
                    'seller_name' => $user->name,
                    'lead_id' => $leadId,
                    'lead_name' => $lead->name,
                    'viewed_at' => now()->toDateTimeString(),
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            }
        }

        return view('admin.pages.lead-details', compact('lead'));
    }
    
    public function clearLeadViewLogs()
    {
        $directory = storage_path('logs/brands');

        try {
            if (File::exists($directory)) {
                File::deleteDirectory($directory);   // Deletes folder + all brand logs
            }

            // Recreate empty brands folder so logging continues
            File::makeDirectory($directory, 0755, true);

            return back()->with('success', 'All brand lead view logs cleared successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to clear logs: ' . $e->getMessage());
        }
    }

    public function sellerAssignedLeads(Request $request)
    {
        $seller  = auth('seller')->user();
        $isAdmin = auth('admin')->check();

        $query = Lead::query()
            ->with([
                'brand:id,brand_name',
                'client:id,name,email',
                // ↓ only the assignment for the logged-in seller; include the assignee record
                'assignments' => function ($q) use ($seller, $isAdmin) {
                    if ($seller && !$isAdmin) {
                        $q->where('assigned_to', $seller->id);
                    }
                    $q->latest('assigned_at')
                        ->with(['assignee:id,name,email']); // Seller (PM) who owns this assignment
                },
            ])
            ->withCount([
                'paymentLinks as paid_links_count' => fn($q) => $q->where('status', 'paid'),
            ])
            ->addSelect([
                'latest_order_id' => Order::select('id')
                    ->whereColumn('orders.lead_id', 'leads.id')
                    ->orderByDesc('orders.id')
                    ->limit(1),
                'latest_order_balance_due' => Order::select('balance_due')
                    ->whereColumn('orders.lead_id', 'leads.id')
                    ->orderByDesc('orders.id')
                    ->limit(1),
                'latest_order_currency' => Order::select('currency')
                    ->whereColumn('orders.lead_id', 'leads.id')
                    ->orderByDesc('orders.id')
                    ->limit(1),
            ]);

        if ($seller) {
            // show only leads that have any assignment to this seller
            $query->whereHas('assignments', fn($q) => $q->where('assigned_to', $seller->id));
        } elseif (!$isAdmin) {
            return redirect()->route('admin.login.get')->with('error', 'You must be logged in.');
        }

        // filters…
        if ($request->filled('status'))   $query->where('status', $request->string('status'));
        if ($request->filled('brand_id')) $query->where('brand_id', (int) $request->brand_id);
        if ($request->filled('q')) {
            $q = trim($request->q);
            $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%")
                    ->orWhere('message', 'like', "%{$q}%");
            });
        }

        $leads = $query->paginate(20)->withQueryString();

        return view('admin.pages.assigned-leads', compact('leads'));
    }

    // old assigned leads code
    // public function sellerAssignedLeads(Request $request)
    // {
    //     $seller  = auth('seller')->user();
    //     $isAdmin = auth('admin')->check();
    //     $query = Lead::query()
    //         ->with([
    //             'brand:id,brand_name',
    //             'seller:id,name,email,brand_id,is_seller',
    //             'client:id,name,email',
    //             'assignments:id,lead_id,status,assigned_to,assigned_role,assigned_by'
    //         ])
    //         ->withCount([
    //             'paymentLinks as paid_links_count' => fn($q) => $q->where('status', 'paid'),
    //         ])
    //         ->addSelect([
    //             'latest_order_id' => Order::select('id')
    //                 ->whereColumn('orders.lead_id', 'leads.id')
    //                 ->orderByDesc('orders.id')
    //                 ->limit(1),

    //             'latest_order_balance_due' => Order::select('balance_due')
    //                 ->whereColumn('orders.lead_id', 'leads.id')
    //                 ->orderByDesc('orders.id')
    //                 ->limit(1),

    //             'latest_order_currency' => Order::select('currency')
    //                 ->whereColumn('orders.lead_id', 'leads.id')
    //                 ->orderByDesc('orders.id')
    //                 ->limit(1),
    //         ]);

    //     if ($seller) {
    //         // Only leads assigned to this seller
    //         $query->whereHas('assignments', function ($q) use ($seller) {
    //             $q->where('assigned_to', $seller->id);
    //         });
    //     } elseif (!$isAdmin) {
    //         return redirect()->route('admin.login.get')->with('error', 'You must be logged in.');
    //     }

    //     // filters
    //     if ($request->filled('status')) {
    //         $query->where('status', $request->string('status'));
    //     }
    //     if ($request->filled('brand_id')) {
    //         $query->where('brand_id', (int)$request->brand_id);
    //     }
    //     if ($request->filled('q')) {
    //         $q = trim($request->q);
    //         $query->where(function ($w) use ($q) {
    //             $w->where('name', 'like', "%{$q}%")
    //                 ->orWhere('email', 'like', "%{$q}%")
    //                 ->orWhere('phone', 'like', "%{$q}%")
    //                 ->orWhere('message', 'like', "%{$q}%");
    //         });
    //     }

    //     $leads = $query->paginate(20)->withQueryString();
    //     // dd($query,$leads);

    //     return view('admin.pages.assigned-leads', compact('leads'));
    // }
}
