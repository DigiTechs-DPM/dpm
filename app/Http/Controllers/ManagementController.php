<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Brand;
use App\Models\Order;
use App\Models\Client;
use App\Models\PaymentLink;
use Illuminate\Http\Request;
use App\Models\LeadAssignment;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SendClientAccountCRMLink;

class ManagementController extends Controller
{
    public function clientBriefs($id)
    {
        $admin = auth('admin')->user();
        $seller = auth('seller')->user();

        // ✅ Allow access to either admin OR seller
        abort_unless($admin || $seller, 403, 'Unauthorized access.');

        // Get client and their orders with briefs
        $client = Client::findOrFail($id);

        $orders = Order::with([
            'brand:id,brand_name',
            'seller:id,name',
            'client:id,name,email',
            'brief'
        ])
            ->where('client_id', $client->id)
            ->latest('id')
            ->get();

        // ✅ Load appropriate view
        if ($admin) {
            return view('admin.pages.client-brief-forms', compact('orders', 'client'));
        }

        return view('sellers.pages.client-brief-forms', compact('orders', 'client'));
    }

    public function deleteDomain(Request $request)
    {
        $brand = Brand::find($request->domain_id);
        // dd($client, $request->all());
        if (!$brand) {
            return response()->json(['success' => false, 'message' => 'Domain not found']);
        }
        $brand->delete();
        return response()->json(['success' => true]);
    }

    public function deleteLeads(Request $request, $id = null)
    {
        $status = $request->status;
        // dd($request->all(), $status,$id);
        // Delete by ID (single lead)
        if ($id) {
            $lead = Lead::find($id);

            if (!$lead) {
                return back()->with('error', "No lead found with ID {$id}.");
            }
            // Delete related assignments first
            LeadAssignment::where('lead_id', $lead->id)->delete();
            // Delete the lead (cascade handles orders, payments, etc.)
            $lead->delete();
            return back()->with('success', "Lead with ID {$id} and related data deleted successfully.");
        }
        // Delete by Status (bulk delete)
        if ($status) {
            $leads = Lead::where('status', $status)->get();
            if ($leads->isEmpty()) {
                return back()->with('info', "No leads found with status '{$status}'.");
            }
            $leadIds = $leads->pluck('id')->toArray();
            LeadAssignment::whereIn('lead_id', $leadIds)->delete();
            Lead::whereIn('id', $leadIds)->delete();
            // return response()->json([
            //     'status'  => true,
            //     'message' => "All leads with status '{$status}' and related data deleted successfully.",
            //     'deleted_count' => count($leadIds)
            // ]);
            return back()->with('info', "All leads with status '{$status}' and related data deleted successfully.");
        }
        // If neither ID nor Status provided
        // return response()->json([
        //     'status'  => false,
        //     'message' => 'Please provide either a lead ID or a status.'
        // ], 400);
        return back()->with('info', "Please provide either a lead ID or a status.");
    }

    public function updateDomainStatus(Request $request)
    {
        $domain = Brand::find($request->domain_id); // or client model if separate
        if (!$domain) {
            return response()->json(['success' => false]);
        }
        $domain->status = $request->status; // active / penidng
        $domain->save();

        return response()->json(['success' => true]);
    }

    public function deleteClient(Request $request)
    {
        $client = Client::find($request->user_id);
        // dd($client, $request->all());
        if (!$client) {
            return response()->json(['success' => false, 'message' => 'Client not found']);
        }
        $client->delete();
        return response()->json(['success' => true]);
    }

    public function updateClientStatus(Request $request)
    {
        $client = Client::find($request->user_id); // or client model if separate

        if (!$client) {
            return response()->json(['success' => false]);
        }

        $client->status = $request->status; // active / inactive
        $client->save();

        return response()->json(['success' => true]);
    }

    public function updateLeadStatus(Request $request)
    {
        $lead = Lead::find($request->lead_id);
        // dd($lead);
        $lead->status = $request->status;
        $lead->save();
        return back()->with('success', 'Lead status updated.');
    }

    public function clientAccountAccess(Request $request)
    {
        // Validate the input
        $request->validate([
            'client_id' => 'required|integer|exists:clients,id',
            'password'  => 'required|string|min:6|max:12',
        ]);
        // Retrieve the client by ID
        $client = Client::findOrFail($request->client_id);
        $client->password = $request->password;
        $plainPassword = $request->password;
        $client->save();
        // dd($client);

        // Create the login URL (you can change this if needed)
        $loginUrl = route('client.login.get');
        // Ensure the email is valid
        if (!filter_var($client->email, FILTER_VALIDATE_EMAIL)) {
            return back()->with('error', 'Invalid email address.');
        }
        // Send the notification with a 5-second delay
        try {
            Notification::route('mail', $client->email)
                ->notify(
                    (new SendClientAccountCRMLink($client, $plainPassword, $loginUrl))
                        ->delay(now()->addSeconds(5))
                );
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send the email. Error: ' . $e->getMessage());
        }

        // Return a success message
        return back()->with('success', 'Client account password updated successfully.');
    }

    public function changePaylinkStatus(Request $request)
    {
        $request->validate([
            'id'             => ['required', 'integer', 'exists:payment_links,id'],
            'is_active_link' => ['required', 'in:true,false,1,0'],
        ]);
        $link = PaymentLink::findOrFail($request->id);
        // Convert string → boolean
        $isActive = filter_var($request->is_active_link, FILTER_VALIDATE_BOOLEAN);
        $link->is_active_link = $isActive;
        $link->save();
        return response()->json([
            'success' => true,
            'message' => "Payment link updated successfully.",
            'active'  => $isActive
        ]);
    }

    public function generateInvoice(Request $request, ?Order $order = null)
    {
        // dd($order);
        return view('admin.pages.invoice', compact('order'));
    }

    public function toggleSettingDown()
    {
        Artisan::call('down', [
            '--render' => 'errors.503',
        ]);
        $status = 'maintainance mode';

        return redirect()->back()->with('status', "Site is now {$status}.");
    }

    public function toggleSettingUp()
    {
        Artisan::call('up');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        Artisan::call('optimize:clear');
        return redirect()->route('welcome.get')->with('status', 'Site is now live again!');
    }
}
