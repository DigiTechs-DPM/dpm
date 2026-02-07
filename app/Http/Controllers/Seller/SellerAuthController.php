<?php

namespace App\Http\Controllers\Seller;

use Carbon\Carbon;
use App\Models\Admin;
use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class SellerAuthController extends Controller
{
    public function sellerLoginPage()
    {
        return view('sellers.pages.auth.login');
    }
    public function sellerForgotPage()
    {
        return view('sellers.pages.auth.forgot');
    }
    public function sellerResetPage($token)
    {
        return view('sellers.pages.auth.reset', compact('token'));
    }

    public function sellerLoginPost(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (!isWithinWorkingHours()) {
            // return redirect()->route('seller.login.get')->with('error', 'Login allowed only during working hours or from office.');
            return view('errors.restricted');
        }
        // 🔹 Seller Login (with Active status check)
        $seller = Seller::where('email', $credentials['email'])->first();
        // dd($seller);
        if ($seller && Hash::check($credentials['password'], $seller->password)) {
            if ($seller->status !== 'Active') {
                return back()->with('error', 'Your account is inactive. Please contact support.');
            }
            Auth::guard('seller')->login($seller);
            if (isProjectManager() || isFrontSeller()) {
                session(['role' => 'project_manager' || 'front_seller']);
                return redirect()
                    ->route('seller.seller-performance.get', $seller->id)
                    ->with('success', 'Welcome ' . $seller->name);
            }
            session(['role' => 'seller']);
            return redirect()->route('seller.index.get')->with('success', 'Login as Seller Successfully !!!');
        }
        // ❌ Invalid login
        return back()->with('error', '❌ Record not matched with data !!!');
    }

    public function sellerForgotPost(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Check if email exists in admins or sellers
        $seller = Seller::where('email', $request->email)->first();
        if (!$seller) {
            return back()->with('error', 'Email not found in our records.');
        }

        // Prevent spam: existing token in last 15 minutes
        $existingToken = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('created_at', '>=', Carbon::now()->subMinutes(15))
            ->first();

        if ($existingToken) {
            return back()->with('error', 'A password reset token has already been sent in the last 15 minutes.');
        }

        $token = mt_rand(100000, 999999);

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        DB::table('password_reset_tokens')->insert([
            'email'      => $request->email,
            'token'      => $token,
            'created_at' => Carbon::now(),
        ]);

        // Send email
        Mail::send('emails.seller-password', ['token' => $token], function ($message) use ($request) {
            $message->to($request->email);
            $message->subject('Reset Your Password');
        });

        return back()->with('success', 'Password reset code sent! Please check your email.');
    }


    public function sellerResetPost(Request $request)
    {
        $request->validate([
            'email'     => 'required|email',
            'password'  => 'required|min:8',
            'cpassword' => 'required|same:password',
            'token'     => 'required'
        ]);

        $resetRequest = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->where('created_at', '>=', Carbon::now()->subMinutes(30)) // 30 min expiry
            ->first();

        if (!$resetRequest) {
            return back()->with('error', 'Invalid or expired password reset token.');
        }

        // Update password in the right table
        if (Seller::where('email', $request->email)->exists()) {
            Seller::where('email', $request->email)->update([
                'password' => Hash::make($request->password)
            ]);
        } else {
            return back()->with('error', 'Account not found.');
        }
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('seller.login.get')->with('success', 'Password updated successfully!');
    }

    // public function sellerlogout()
    // {
    //     $seller = Auth::guard('seller')->user();

    //     if ($seller) {

    //         // 🔹 1) Remove ONLY session view flags
    //         foreach (session()->all() as $key => $val) {
    //             if (str_starts_with($key, 'viewed_lead_')) {
    //                 session()->forget($key);
    //             }
    //         }

    //         // 🔹 2) Logout seller
    //         session()->flush();
    //         Auth::guard('seller')->logout();
    //     }

    //     // 🔹 3) Clear & regenerate session
    //     session()->invalidate();
    //     session()->regenerateToken();

    //     return redirect()
    //         ->route('seller.login.get')
    //         ->with('success', 'Logout Successfully !!!');
    // }

    public function sellerlogout()
    {
        $seller = Auth::guard('seller')->user();
        if ($seller) {
            // Remove session entries before flush
            foreach (session()->all() as $key => $val) {
                if (str_starts_with($key, 'viewed_lead_')) {
                    session()->forget($key);
                }
            }
            // Remove seller log entries from log file
            $logFile = storage_path('logs/lead-views.log');
            if (file_exists($logFile)) {
                $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                // Filter out logs for this seller
                $filtered = array_filter($lines, function ($line) use ($seller) {
                    return !str_contains($line, "\"seller_id\":{$seller->id}");
                });
                // Save filtered logs back
                file_put_contents($logFile, implode(PHP_EOL, $filtered) . PHP_EOL);
            }
            // Clear session
            session()->flush();
            Auth::guard('seller')->logout();
        }
        // Logout admin if logged in
        session()->invalidate();
        session()->regenerateToken();
        return redirect()->route('seller.login.get')->with('success', 'Logout Successfully !!!');
    }
}
