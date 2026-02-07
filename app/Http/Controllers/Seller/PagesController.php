<?php

namespace App\Http\Controllers\Seller;

use App\Models\Lead;
use App\Models\Admin;
use App\Models\Brand;
use App\Models\Order;
use App\Models\Client;
use App\Models\Seller;
use App\Models\Payment;
use App\Models\AccountKey;
use App\Models\RiskyClient;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Questionnair;
use Illuminate\Support\Facades\File;

class PagesController extends Controller
{
    /**
     * Seller Domain Scripts
     */
    public function sellerDomainScripts()
    {
        $seller = auth('seller')->user();
        abort_unless($seller, 403, 'Unauthorized access.');

        return view('sellers.pages.domain-script');
    }

    /**
     * Seller Dashboard (auth:seller only)
     */
    public function sellerDashboard()
    {
        $seller = auth('seller')->user();
        abort_unless($seller, 403, 'Unauthorized access.');

        if ($seller->is_seller === 'finance') {
            return redirect()
                ->route('admin.brand-payments.get')
                ->with('info', 'Finance accounts cannot access dashboard.');
        }

        // --- Quick counts ---
        $stats = [
            'leads'    => Lead::where('brand_id', $seller->brand_id)->count(),
            'brands'   => Brand::count(),
            'orders'   => Order::where('brand_id', $seller->brand_id)->count(),
            'payments' => Payment::count(),
        ];

        // --- Active users (last 5 mins) ---
        $fiveMinutesAgo = now()->subMinutes(5);
        $activeClients = Client::where('last_seen', '>=', $fiveMinutesAgo)->get(['id', 'name', 'last_seen']);
        $activeSellers = Seller::where('last_seen', '>=', $fiveMinutesAgo)->get(['id', 'name', 'last_seen']);
        $activeAdmins  = Admin::where('last_seen', '>=', $fiveMinutesAgo)->get(['id', 'name', 'last_seen', 'role']);
        $activeMembers = collect()->merge($activeClients)->merge($activeSellers)->merge($activeAdmins);

        // --- Monthly revenue ---
        $revenueData = DB::table('orders')
            ->selectRaw('MONTH(created_at) as month, SUM(unit_amount)/100 as total_income')
            ->where('status', 'paid')
            ->where('brand_id', $seller->brand_id)
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $months = $revenueData->map(fn($r) => date("F", mktime(0, 0, 0, $r->month, 1)))->toArray();
        $totals = $revenueData->pluck('total_income')->toArray();

        // --- Payment summary ---
        $paymentSummary = DB::table('payment_links')
            ->selectRaw('
                SUM(CASE WHEN status="paid" THEN unit_amount ELSE 0 END) as paid,
                SUM(order_total_snapshot) as snapshot
            ')
            ->where('brand_id', $seller->brand_id)
            ->first();

        $paymentPaid  = $paymentSummary->paid ?? 0;
        $paymentTotal = $paymentSummary->snapshot ?? 0;
        $paymentDue   = $paymentTotal - $paymentPaid;
        $revenue      = $paymentPaid;

        // --- Lead view logs (per brand) ---
        $brand = $seller->brand;
        $brandSlug = Str::slug($brand->brand_name ?? 'unknown-brand', '_');
        $logPath = storage_path("logs/brands/{$brandSlug}/lead-views.log");

        $logs = [];

        if (File::exists($logPath)) {
            try {
                $lines = file($logPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                $logs  = array_slice(array_reverse($lines), 0, 20); // latest 20
            } catch (\Throwable $e) {
                $logs = ["⚠️ Failed to read log file: " . $e->getMessage()];
            }
        } else {
            $logs = ["No lead view logs found for {$brand->brand_name}."];
        }


        return view('sellers.pages.index', [
            ...$stats,
            'seller'        => $seller,
            'activeMembers' => $activeMembers,
            'months'        => $months,
            'totals'        => $totals,
            'revenue'       => $revenue,
            'paymentPaid'   => $paymentPaid,
            'paymentDue'    => $paymentDue,
            'logs'          => $logs,
        ]);
    }

    /**
     * Seller Clients
     */
    public function sellerClients()
    {
        $seller = auth('seller')->user();
        abort_unless($seller, 403, 'Unauthorized access.');

        $clients = Client::paginate(20);

        $riskyClients = RiskyClient::with([
            'client:id,name,email',
            // 'client.orders:id,client_id,status,created_at,total_amount',
            'client.orders.payments:id,order_id,amount,status,created_at'
        ])
            ->orderByDesc('risk_score')
            ->limit(20)
            ->get();

        // dd($clients, $riskyClients);

        return view('sellers.pages.clients', compact('clients', 'riskyClients'));
    }

    public function sellerClientBriefs($id)
    {
        $seller = auth('seller')->user();
        abort_unless($seller, 403, 'Unauthorized access.');

        $client = Client::findOrFail($id);

        // Get all orders of that client + related brief and brand, seller, etc.
        $orders = Order::with(['brand:id,brand_name', 'seller:id,name', 'client:id,name,email', 'brief'])
            ->where('client_id', $client->id)
            ->get();

        return view('sellers.pages.client-brief-forms', compact('orders', 'client'));
    }

    /**
     * Seller Account Keys
     */
    public function sellerAccountKeys()
    {
        $seller = auth('seller')->user();
        abort_unless($seller, 403, 'Unauthorized access.');

        $keys = AccountKey::with('brand')->get();
        $brands = Brand::all();

        return view('sellers.pages.account-keys', compact('brands', 'keys'));
    }

    /**
     * Store new Account Key
     */
    public function accountKeyStore(Request $request)
    {
        $validated = $request->validate([
            'brand_id'             => 'nullable|exists:brands,id',
            'stripe_secret_key'    => 'nullable|string',
            'stripe_publishable_key' => 'nullable|string',
            'stripe_webhook_secret'  => 'nullable|string',
            'paypal_client_id'     => 'nullable|string',
            'paypal_secret'        => 'nullable|string',
            'paypal_webhook_id'    => 'nullable|string',
            'paypal_base_url'      => 'nullable|url',
        ]);

        $commonData = [
            'stripe_secret_key'      => $validated['stripe_secret_key'] ?? null,
            'stripe_publishable_key' => $validated['stripe_publishable_key'] ?? null,
            'stripe_webhook_secret'  => $validated['stripe_webhook_secret'] ?? null,
            'paypal_client_id'       => $validated['paypal_client_id'] ?? null,
            'paypal_secret'          => $validated['paypal_secret'] ?? null,
            'paypal_webhook_id'      => $validated['paypal_webhook_id'] ?? null,
            'paypal_base_url'        => $validated['paypal_base_url'] ?? null,
        ];

        if ($request->filled('brand_id')) {
            AccountKey::updateOrCreate(
                ['brand_id' => $request->brand_id],
                array_merge($commonData, [
                    'brand_url' => Brand::find($request->brand_id)?->brand_url,
                ])
            );
        } else {
            AccountKey::updateOrCreate(['brand_id' => null], $commonData);
        }

        return back()->with('success', 'Payment keys saved successfully.');
    }

    /**
     * Update existing Account Key
     */
    public function accountKeysUpdate(Request $request, $id)
    {
        $request->validate([
            'stripe_secret_key'       => 'nullable|string',
            'stripe_publishable_key'  => 'nullable|string',
            'stripe_webhook_secret'   => 'nullable|string',
            'paypal_client_id'        => 'nullable|string',
            'paypal_secret'           => 'nullable|string',
            'paypal_webhook_id'       => 'nullable|string',
            'paypal_base_url'         => 'nullable|url',
            'status'                  => 'required|in:active,inactive',
        ]);

        $key = AccountKey::findOrFail($id);
        $key->update([
            'stripe_publishable_key' => $request->stripe_publishable_key,
            'stripe_secret_key'      => $request->stripe_secret_key,
            'stripe_webhook_secret'  => $request->stripe_webhook_secret,
            'paypal_client_id'       => $request->paypal_client_id,
            'paypal_secret'          => $request->paypal_secret,
            'paypal_webhook_id'      => $request->paypal_webhook_id,
            'paypal_base_url'        => $request->paypal_base_url,
            'status'                 => $request->status,
        ]);

        return back()->with('success', 'Keys updated successfully.');
    }
}
