<?php

namespace App\Http\Controllers\Admin;

use App\Models\Lead;
use App\Models\Admin;
use App\Models\Brand;
use App\Models\Order;
use App\Models\Client;
use App\Models\Seller;
use App\Models\Payment;
use App\Models\AccountKey;
use App\Models\RiskyClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;

class ViewsController extends Controller
{
    public function adminDashboard()
    {
        // dd(ini_get('memory_limit'));
        $admin = auth('admin')->user();

        // Block finance role from seeing dashboard
        if ($admin && $admin->role === 'finance') {
            return redirect()
                ->route('admin.brand-payments.get') // 👈 send them to finance page
                ->with('info', 'Finance accounts cannot access dashboard.');
        }
        // Stats
        $leads    = Lead::count();
        $brands   = Brand::count();
        $orders   = Order::count();
        $payments = Payment::count();

        // Active users in last 5 mins
        $now     = now()->subMinutes(5);
        $users   = Client::where('last_seen', '>=', $now)->get();
        $admins  = Admin::where('last_seen', '>=', $now)->get();
        $sellers = Seller::where('last_seen', '>=', $now)->get();

        $allOnline = collect()
            ->merge($users)
            ->merge($admins)
            ->merge($sellers);

        // Revenue chart (from orders)
        $data = DB::table('orders')
            ->selectRaw('MONTH(created_at) as month, SUM(unit_amount) / 100 as total_income')
            ->where('status', 'paid')
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        $months = [];
        $totals = [];
        foreach ($data as $row) {
            $months[] = date("F", mktime(0, 0, 0, $row->month, 1));
            $totals[] = $row->total_income;
        }

        // --- Fix payments (using payment_links grouped per order) ---
        $orderPayments = DB::table('payment_links')
            ->select('order_id')
            ->selectRaw('MAX(order_total_snapshot) as snapshot')
            ->selectRaw('SUM(CASE WHEN status = "paid" THEN unit_amount ELSE 0 END) as paid')
            ->groupBy('order_id')
            ->get();

        $paymentPaidAmount = $orderPayments->sum('paid');
        $totalOrderSnapshot = $orderPayments->sum('snapshot');
        $paymentDueAmount = $totalOrderSnapshot - $paymentPaidAmount;
        // Total revenue (optional, from orders table if you keep it)
        $revenue = $paymentPaidAmount; // 👈 use payments, not orders.amount_paid

        // dd($paymentPaidAmount, $paymentDueAmount, $totalOrderSnapshot, $revenue,$providerPayments);

        // --- Lead view logs (all brands for admin) ---
        $logs = [];
        $logsDir = storage_path("logs/brands");
        if (File::exists($logsDir)) {
            $brandLogs = File::allFiles($logsDir);
            $allLines = [];
            foreach ($brandLogs as $file) {
                try {
                    $lines = file($file->getPathname(), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                    foreach ($lines as $line) {
                        $allLines[] = '[' . basename($file->getPath()) . '] ' . $line;
                    }
                } catch (\Throwable $e) {
                    $allLines[] = "⚠️ Failed to read {$file->getFilename()}: " . $e->getMessage();
                }
            }
            $logs = collect($allLines)
                ->sortDesc() // most recent first (rough order)
                ->take(50)   // limit for performance
                ->values()
                ->toArray();
        } else {
            $logs = ["No brand lead view logs found."];
        }


        // --- Upwork payments (using upwork_payment_links grouped per order) ---
        $upworkOrderPayments = DB::table('upwork_payment_links')
            ->select('order_id')
            ->selectRaw('MAX(order_total_snapshot) as snapshot')
            ->selectRaw('SUM(CASE WHEN status = "paid" THEN unit_amount ELSE 0 END) as paid')
            ->groupBy('order_id')
            ->get();
        $upworkPaymentPaidAmount  = $upworkOrderPayments->sum('paid');
        $upworkTotalOrderSnapshot = $upworkOrderPayments->sum('snapshot');
        $upworkPaymentDueAmount   = $upworkTotalOrderSnapshot - $upworkPaymentPaidAmount;

        return view('admin.pages.index', [
            'leads'        => $leads,
            'orders'       => $orders,
            'brands'       => $brands,
            'payments'     => $payments,
            'activeMembers' => $allOnline,
            'months'       => $months,
            'totals'       => $totals,
            'revenue'      => $revenue,
            'paymentPaid'  => $paymentPaidAmount,
            'paymentDue'   => $paymentDueAmount,
            'logs' => $logs,
            // upwork payments
            'upworkPaymentPaid' => $upworkPaymentPaidAmount,
            'upworkPaymentDue'  => $upworkPaymentDueAmount,
            'upworkRevenue'     => $upworkPaymentPaidAmount,
            'upworkSnapshot'    => $upworkTotalOrderSnapshot,
        ]);
    }

    public function adminClients()
    {
        $clients = Client::paginate(20);

        // --- Load top risky clients with recent orders and payments ---
        $riskyClients = RiskyClient::with([
            'client:id,name,email',
            'client.orders' => function ($q) {
                $q->select('id', 'client_id', 'status', 'created_at', 'unit_amount', 'amount_paid', 'balance_due')
                    ->latest()
                    ->take(5);
            },
            'client.orders.payments:id,order_id,amount,status,created_at'
        ])
            ->orderByDesc('risk_score')
            ->limit(20)
            ->get();

        return view('admin.pages.clients', [
            'clients' => $clients,
            'riskyClients' => $riskyClients,
        ]);
    }

    public function adminAccountKeys()
    {
        $keys = AccountKey::with('brand')->where('brand_id', '!=', '')->get();
        $brands = Brand::all();
        return view('admin.pages.account-keys', compact('brands', 'keys'));
    }

    public function accountKeyStore(Request $request)
    {
        $rules = [
            'module' => 'required|in:ppc,upwork', // Ensure module is either 'ppc' or 'upwork'
            'stripe_secret_key'       => 'nullable|string',
            'stripe_publishable_key'  => 'nullable|string',
            'stripe_webhook_secret'   => 'nullable|string',
            'paypal_client_id'        => 'nullable|string',
            'paypal_secret'           => 'nullable|string',
            'paypal_webhook_id'       => 'nullable|string',
            'paypal_base_url'         => 'nullable|url',
        ];

        if ($request->filled('brand_id')) {
            $rules['brand_id'] = 'exists:brands,id';
        }

        $validated = $request->validate($rules);

        // Common data to be saved
        $commonData = [
            'module'                 => $request->module,
            'stripe_secret_key'      => $request->stripe_secret_key,
            'stripe_publishable_key' => $request->stripe_publishable_key,
            'stripe_webhook_secret'  => $request->stripe_webhook_secret,
            'paypal_client_id'       => $request->paypal_client_id,
            'paypal_secret'          => $request->paypal_secret,
            'paypal_webhook_id'      => $request->paypal_webhook_id,
            'paypal_base_url'        => $request->paypal_base_url,
        ];

        // Check if brand_id is provided and set appropriate conditions
        if ($request->filled('brand_id')) {
            $brandUrl = Brand::find($request->brand_id)?->brand_url;
            // Prevent saving duplicate data for the same domain and module
            if (AccountKey::where('brand_url', $brandUrl)->where('module', $request->module)->exists()) {
                return back()->with('error', 'Duplicate domain data for the same module exists.');
            }

            // Save or update AccountKey data
            AccountKey::updateOrCreate(
                ['brand_id' => $request->brand_id, 'module' => $request->module],
                array_merge($commonData, [
                    'brand_url' => $brandUrl,
                ])
            );
        } else {
            // Prevent saving duplicate data for the same module with no brand_id
            if (AccountKey::whereNull('brand_id')->where('module', $request->module)->exists()) {
                return back()->with('error', 'Duplicate data for the same module exists.');
            }

            // Save or update AccountKey data with no brand_id
            AccountKey::updateOrCreate(
                ['brand_id' => null, 'module' => $request->module],
                $commonData
            );
        }

        return back()->with('success', 'Payment keys saved successfully.');
    }

    public function accountKeysUpdate(Request $request, $id)
    {
        $request->validate([
            'module' => 'required|in:ppc,upwork', // Ensure module is either 'ppc' or 'upwork'
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

        // Update the key with new data, including module and status
        $key->update([
            'module'                 => $request->module, // Ensure module is updated
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
