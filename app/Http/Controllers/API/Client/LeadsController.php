<?php

namespace App\Http\Controllers\API\Client;

use App\Models\Lead;
use App\Models\Brand;
use App\Models\Order;
use App\Models\Client;
use function Rubix\ML\comb;
use App\Models\ClientTicket;
use Illuminate\Http\Request;

use App\Services\ProjectNotify;
use App\Http\Controllers\Controller;
use App\Services\NotifyTicketStakeholders;

class LeadsController extends Controller
{
    private function host(?string $url): ?string
    {
        if (!$url) return null;
        if (!preg_match('~^https?://~i', $url)) $url = 'https://' . $url;
        $h = parse_url($url, PHP_URL_HOST);
        return $h ? strtolower(preg_replace('/^www\./i', '', $h)) : null;
    }

    private function brandFromUrl(?string $url): ?Brand
    {
        $h = $this->host($url);
        if (!$h) return null;
        return Brand::where('brand_host', $h)
            ->orWhereJsonContains('allowed_origins', $h)
            ->orWhereJsonContains('allowed_origins', 'www.' . $h)
            ->first();
    }

    private function brandFromOrigin(Request $r): ?Brand
    {
        $origin = $r->headers->get('Origin') ?: $r->headers->get('Referer');
        return $this->brandFromUrl($origin);
    }

    public function storeLead(Request $req)
    {
        $data = $req->validate([
            'name'   => 'required|string|max:255',
            'email'  => 'required|email|max:255',
            'phone'  => 'nullable|string|max:30',
            'service' => 'nullable|string|max:255',
            'message' => 'nullable|string|max:4000',
            'url'    => 'nullable|url',
            'utm_source' => 'nullable|string|max:100',
            'utm_medium' => 'nullable|string|max:100',
            'utm_campaign' => 'nullable|string|max:150',
            'referrer' => 'nullable|string|max:2048',
            'session_id' => 'nullable|string|max:64',
        ]);

        // Resolve brand by submitted URL first, then by Origin/Referer
        $brand = $this->brandFromUrl($data['url'] ?? null) ?? $this->brandFromOrigin($req);
        abort_unless($brand, 422, 'Unknown brand');

        // If this endpoint is hit from the browser, keep $brand->require_hmac = false for that brand.
        // (If you want HMAC, use server→server posting instead.)

        // Optional: Idempotency
        $idem = $req->header('Idempotency-Key');
        if ($idem && Lead::where('brand_id', $brand->id)->where('meta->idem', $idem)->exists()) {
            return response()->json(['ok' => true, 'duplicate' => true], 200);
        }

        // Find existing client by email or create new one
        $client = Client::firstOrCreate(
            ['email' => strtolower(trim($data['email']))], // lookup key
            [
                'name'  => $data['name'] ?? null,
                'phone' => $data['phone'] ?? null,
            ]
        );

        // // Upsert client, assign seller, create lead (same as you already do)
        // $client = Client::firstOrCreate(['email' => $data['email']], [
        //     'name' => $data['name'],
        //     'phone' => $data['phone'] ?? null,
        // ]);

        $seller = app(\App\Services\LeadAssigner::class)->assignNext($brand);

        $lead = Lead::create([
            'brand_id'   => $brand->id,
            'seller_id'  => $seller->id,
            'client_id'  => $client->id,
            'name'       => $data['name'],
            'email'      => $data['email'],
            'phone'      => $data['phone'] ?? null,
            'message'    => $data['message'] ?? null,
            'status'     => 'new',
            'domain_url' => $this->host($data['url'] ?? ($req->headers->get('Referer') ?: '')),
            'meta'       => array_filter([
                'service'      => $data['service'] ?? null,
                'utm_source'   => $data['utm_source'] ?? null,
                'utm_medium'   => $data['utm_medium'] ?? null,
                'utm_campaign' => $data['utm_campaign'] ?? null,
                'referrer'     => $data['referrer'] ?? null,
                'session_id'   => $data['session_id'] ?? null,
                'idem'         => $idem,
                'ip'           => $req->ip(),
                'ua'           => substr((string)$req->userAgent(), 0, 255),
            ]),
        ]);

        return response()->json(['ok' => true, 'lead_id' => $lead->id], 201);
    }

    public function clientTickets()
    {
        $tickets = ClientTicket::paginate(12);
        // dd($tickets);
        return view('clients.pages.tickets', compact('tickets'));
    }

    public function clientRaiseTicket(Order $order)
    {
        return view('clients.pages.raise-ticket', compact('order'));
    }

    public function clientTicketStore(Request $request)
    {
        $data = $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'order_id' => 'nullable|exists:orders,id',
        ]);

        $client = auth('client')->user();
        $order = Order::findorFail($request->order_id);
        $ticket = new ClientTicket();
        $ticket->client_id = $client->id;
        $ticket->brand_id = $order->brand_id;
        $ticket->seller_id = $order->seller_id;
        $ticket->order_id = $request->order_id;
        $ticket->subject = $data['subject'];
        $ticket->description = $data['description'];
        $ticket->priority = $data['priority'];
        $ticket->status = 'open';
        $ticket->source = 'crm';

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/attachments/'), $fileName);
            $ticket->attachment = $fileName;
        }
        // dd($request->all(), $data,$ticket,$order);
        $ticket->save();

        // Notify admins, sellers, PM and client
        ProjectNotify::created($ticket);

        return back()->with('success', 'Ticket created successfully!');
    }
}
