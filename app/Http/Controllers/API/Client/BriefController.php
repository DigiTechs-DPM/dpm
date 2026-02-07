<?php

namespace App\Http\Controllers\API\Client;

use App\Models\Order;
use Illuminate\Support\Str;
use App\Models\Questionnair;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

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

        return view('clients.pages.briefs', compact('orders'));
    }

    public function clientBriefPost(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|integer',
            'query' => 'required|array',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,zip|max:10240',
            'remove_attachments' => 'nullable|array',
        ]);

        $orderId = $validated['order_id'];

        // Ensure directory exists before storing
        $uploadDir = storage_path('app/public/uploads/brief-attachments');
        if (!File::exists($uploadDir)) {
            File::makeDirectory($uploadDir, 0775, true);
        }

        $questionnaire = Questionnair::updateOrCreate(
            ['order_id' => $orderId],
            [
                'client_id' => auth('client')->id(),
                'service_name' => Order::find($orderId)->service_name ?? 'Unknown Service',
                'meta' => $validated['query'],
            ]
        );

        $meta = $questionnaire->meta ?? [];

        // ✅ Remove selected files
        if (!empty($validated['remove_attachments'])) {
            foreach ($validated['remove_attachments'] as $file) {
                if (file_exists(public_path($file))) {
                    @unlink(public_path($file));
                }
            }
            $meta['attachments'] = array_diff($meta['attachments'] ?? [], $validated['remove_attachments']);
        }

        // ✅ Add new uploads — with original file names
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                // Use original name, but append a unique suffix to avoid overwriting
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();

                // Example: "CompanyLogo_2025-11-07_144030.png"
                $safeName = Str::slug($originalName) . '_' . now()->format('Ymd_His') . '.' . $extension;

                $path = $file->storeAs('uploads/brief-attachments', $safeName, 'public');

                $meta['attachments'][] = 'storage/' . $path;
            }
        }

        $questionnaire->update(['meta' => $meta]);

        return back()->with('success', 'Brief updated successfully!');
    }

    public function showBriefForm($token)
    {
        $brief = Questionnair::where('brief_token', $token)
            ->where(function ($q) {
                $q->whereNull('brief_token_expires_at')
                    ->orWhere('brief_token_expires_at', '>', now());
            })
            ->with('order')
            ->firstOrFail();
        if (!$brief || $brief->brief_token_expires_at < now()) {
            // If the token doesn't exist or is expired, show an error
            return back()->with('error', 'This link has expired or is invalid.');
        }

        $order = $brief->order;
        // If the client is not logged in, store the redirect URL in the session
        if (!Auth::check()) {
            // Store the current URL (brief form URL) in the session
            session(['redirect_to_brief' => route('client.brief.get', ['token' => $token])]);
            return redirect()->route('client.login.get')->with('error', 'Please login first to see and fill the required brief form !!!.');
        }

        // Client is logged in, show the brief form
        return view('clients.pages.brief-token', [
            'order' => $order,
            'brief' => $brief->meta,
            'questionnair' => $brief
        ]);
    }

    public function submit(Request $request, $token)
    {
        $brief = Questionnair::where('brief_token', $token)->firstOrFail();
        // Save questionnaire responses
        $brief->meta = $request->except('_token');
        $brief->status = 'completed';
        $brief->save();

        return back()->with('success', 'Your brief was submitted successfully!');
    }

    // public function clientBriefPost(Request $request)
    // {
    //     $validated = $request->validate([
    //         'order_id' => 'required|integer',
    //         'query' => 'required|array',
    //         'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,zip|max:10240',
    //         'remove_attachments' => 'nullable|array',
    //     ]);
    //     $orderId = $validated['order_id'];
    //     $questionnaire = Questionnair::updateOrCreate(
    //         ['order_id' => $orderId],
    //         [
    //             'client_id' => auth('client')->id(),
    //             'service_name' => Order::find($orderId)->service_name ?? 'Unknown Service',
    //             'meta' => $validated['query'],
    //         ]
    //     );

    //     $totalSize = 0;
    //     if ($request->hasFile('attachments')) {
    //         foreach ($request->file('attachments') as $file) {
    //             $totalSize += $file->getSize(); // in bytes
    //         }
    //     }
    //     if ($totalSize > 25 * 1024 * 1024) { // 25MB total
    //         return back()->with('error', 'Total upload size cannot exceed 25MB.');
    //     }


    //     // Handle attachments
    //     $meta = $questionnaire->meta ?? [];
    //     // Remove selected files
    //     if (!empty($validated['remove_attachments'])) {
    //         foreach ($validated['remove_attachments'] as $file) {
    //             if (file_exists(public_path($file))) {
    //                 unlink(public_path($file));
    //             }
    //         }
    //         $meta['attachments'] = array_diff($meta['attachments'] ?? [], $validated['remove_attachments']);
    //     }
    //     // Add new uploads
    //     if ($request->hasFile('attachments')) {
    //         foreach ($request->file('attachments') as $file) {
    //             $fileName = time() . '_' . $file->getClientOriginalName();
    //             $file->move(public_path('uploads/attachments/'), $fileName);
    //             $meta['attachments'][] = 'uploads/attachments/' . $fileName;
    //         }
    //     }
    //     $questionnaire->update(['meta' => $meta]);

    //     return back()->with('success', 'Brief updated successfully!');
    // }
}
