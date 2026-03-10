<?php

namespace App\Http\Controllers\API\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Questionnair;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BriefController extends Controller
{
    public function clientBriefs()
    {
        $client = auth('client')->user();

        // Fetch client orders with their brief (if exists)
        $orders = Order::query()
            ->with(['brand:id,brand_name', 'seller:id,name', 'client:id,name,email', 'brief'])
            ->where('client_id', $client->id)
            ->where('order_type', 'original')
            ->paginate(20)
            ->withQueryString();
        // dd($orders);
        return view('clients.pages.briefs', compact('orders'));
    }

    public function showBriefForm(string $token)
    {
        $brief = Questionnair::query()
            ->where('brief_token', $token)
            ->where(function ($q) {
                $q->whereNull('brief_token_expires_at')
                    ->orWhere('brief_token_expires_at', '>', now());
            })
            ->with(['order:id,client_id,service_name,brand_id'])
            ->firstOrFail();

        // If client is logged in, ensure they own it (extra protection)
        if (auth('client')->check()) {
            abort_unless((int)$brief->client_id === (int)auth('client')->id(), 403, 'Unauthorized brief access.');
        }

        return view('clients.pages.brief-token', [
            'order'        => $brief->order,
            'brief'        => $brief->meta ?? [],
            'questionnair' => $brief,
            'token'        => $token,
            'mode'         => 'token',
        ]);
    }

    public function submit(Request $request, string $token)
    {
        $brief = Questionnair::query()
            ->where('brief_token', $token)
            ->where(function ($q) {
                $q->whereNull('brief_token_expires_at')
                    ->orWhere('brief_token_expires_at', '>', now());
            })
            ->with('order:id,client_id')
            ->firstOrFail();

        if (auth('client')->check()) {
            abort_unless((int)$brief->client_id === (int)auth('client')->id(), 403, 'Unauthorized brief access.');
        }

        $validated = $request->validate([
            'query' => ['required', 'array'],
            'attachments.*' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,doc,docx,zip', 'max:10240'],
            'remove_attachments' => ['nullable', 'array'],
            'remove_attachments.*' => ['string'],
        ]);

        $this->saveBriefMetaAndFiles($brief, $validated, $request);

        $brief->status = 'completed';
        $brief->save();

        return back()->with('success', 'Your brief was submitted successfully!');
    }

    private function saveBriefMetaAndFiles(Questionnair $brief, array $validated, Request $request): void
    {
        $meta = $brief->meta ?? [];

        // ✅ remove attachments securely (only allow removing files that belong to this brief)
        if (!empty($validated['remove_attachments'])) {
            $existing = $meta['attachments'] ?? [];
            $toRemove = array_values(array_intersect($existing, $validated['remove_attachments']));

            foreach ($toRemove as $relPath) {
                // store only relative paths like "uploads/brief-attachments/xyz.png"
                Storage::disk('public')->delete($relPath);
            }
            $meta['attachments'] = array_values(array_diff($existing, $toRemove));
        }

        // ✅ add new uploads
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $extension    = $file->getClientOriginalExtension();

                $safeName = Str::slug($originalName) . '_' . now()->format('Ymd_His') . '_' . Str::random(6) . '.' . $extension;

                $path = $file->storeAs('uploads/brief-attachments', $safeName, 'public');

                // store RELATIVE path, not "storage/..."
                $meta['attachments'][] = $path;
            }
        }

        // ✅ store query only (not arbitrary request keys)
        $meta = array_merge($meta, ['query' => $validated['query']]);

        $brief->meta = $meta;
        $brief->save();
    }

    public function clientBriefPost(Request $request)
    {
        $client = auth('client')->user();

        $validated = $request->validate([
            'order_id' => ['required', 'integer', 'exists:orders,id'],
            'query' => ['required', 'array'],
            'attachments.*' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,doc,docx,zip', 'max:10240'],
            'remove_attachments' => ['nullable', 'array'],
            'remove_attachments.*' => ['string'],
        ]);

        $order = Order::select(['id', 'client_id', 'service_name'])->findOrFail($validated['order_id']);
        abort_unless((int)$order->client_id === (int)$client->id, 403);

        $brief = Questionnair::updateOrCreate(
            ['order_id' => $order->id],
            [
                'client_id' => $client->id,
                'service_name' => $order->service_name ?? 'Unknown Service',
            ]
        );

        $this->saveBriefMetaAndFiles($brief, $validated, $request);

        return back()->with('success', 'Brief updated successfully!');
    }
}
