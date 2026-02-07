<?php

namespace App\Http\Controllers\API\Client;

use Carbon\Carbon;
use App\Models\Client;
use Illuminate\Http\Request;
use App\Models\ProfileDetail;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function clientLoginPage()
    {
        return view('clients.pages.auth.login');
    }
    public function clientForgotPage()
    {
        return view('clients.pages.auth.forgot');
    }
    public function clientResetPage($token)
    {
        return view('clients.pages.auth.reset', compact('token'));
    }

    public function clientLoginPost(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // First check if client exists
        $client = Client::where('email', $credentials['email'])->first();
        if (!$client) {
            return back()->with('error', 'No account found with this email.');
        }

        // Check status before login
        if ($client->status !== 'Active') {
            return back()->with('error', 'Your account is inactive. Please contact support.');
        }

        // Now attempt login
        if (Auth::guard('client')->attempt($credentials)) {
            session(['role' => 'client']);

            // Check if there's a stored URL to redirect the client back to the brief form
            if (session()->has('redirect_to_brief')) {
                $redirectUrl = session('redirect_to_brief');
                session()->forget('redirect_to_brief'); // Clear the redirect session after using it
                return redirect($redirectUrl)->with('success', 'Login Successfully! You are now redirected to the brief form.');
            }

            return redirect()->route('client.index.get')->with('success', 'Login Successfully!');
        }

        return back()->with('error', 'Invalid credentials.');
    }

    public function clientForgotPost(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:clients,email',
        ]);

        // Check existing token within last 15 minutes
        $existingToken = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('created_at', '>=', Carbon::now()->subMinutes(15))
            ->first();

        if ($existingToken) {
            return back()->with('error', 'A password reset token has already been sent in the last 15 minutes.');
        }

        $token = mt_rand(100000, 999999);

        // Delete old tokens for this email
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        DB::table('password_reset_tokens')->insert([
            'email'      => $request->email,
            'token'      => $token,
            'created_at' => Carbon::now()
        ]);

        // Send email
        Mail::send('emails.client-password', ['token' => $token], function ($message) use ($request) {
            $message->to($request->email);
            $message->subject('Reset Your Password!');
        });

        return back()->with('success', 'Password reset code sent! Please check your email.');
    }

    public function clientResetPost(Request $request)
    {
        $request->validate([
            'email'     => 'required|email|exists:clients,email',
            'password'  => 'required|min:8',
            'cpassword' => 'required|same:password',
            'token'     => 'required'
        ]);

        $resetRequest = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->where('created_at', '>=', now()->subMinutes(30)) // 30 min expiry
            ->first();

        if (!$resetRequest) {
            return back()->with('error', 'Invalid or expired password reset token.');
        }

        // 🔹 Get client and update password — mutator handles hashing + plain meta
        $client = Client::where('email', $request->email)->firstOrFail();
        $client->password = $request->password;
        $client->save();

        // Clean up the token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('client.login.get')->with('success', 'Password updated successfully!');
    }


    //todo: admin logout functionality
    public function clientlogout()
    {
        Auth::guard('client')->logout();
        // Alert::success('Success', 'Admin Logout Successfully !!!');
        return redirect()->route('client.login.get')->with('success', 'Logout Successfully !!!');
    }

    public function updateProfile(Request $request)
    {
        // Detect logged-in guard
        $user = Auth::guard('admin')->user()
            ?? Auth::guard('seller')->user()
            ?? Auth::guard('client')->user();

        if (!$user) {
            return redirect()->back()->with('error', 'No user logged in');
        }
        $request->validate([
            'first_name'      => 'nullable|string|max:255',
            'last_name'       => 'nullable|string|max:255',
            'email'           => 'nullable|email|max:255|unique:admins,email,' . $user->id
                . '|unique:sellers,email,' . $user->id
                . '|unique:clients,email,' . $user->id,
            'alternate_email' => 'nullable|email|max:255',
            'phone'           => 'nullable|string|max:20',
            'address'         => 'nullable|string|max:500',
            'profile'         => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'password'        => 'nullable|min:8',
            'confirm_password' => 'nullable|min:8|same:password',
        ]);
        // Normalize email to lowercase
        $user->email = strtolower(trim($user->email));
        $altEmail = $request->input('alternate_email') ? strtolower(trim($request->input('alternate_email'))) : null;
        // Fetch existing profile record
        $profile = ProfileDetail::where('user_id', $user->id)
            ->where('user_type', get_class($user))
            ->first();
        $fileName = $profile->profile ?? null;
        // Handle profile image upload
        if ($request->hasFile('profile')) {
            // Delete old image if exists
            if ($fileName && file_exists(public_path('uploads/profiles/' . $fileName))) {
                @unlink(public_path('uploads/profiles/' . $fileName));
            }
            // Save new image
            $file = $request->file('profile');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/profiles/'), $fileName);
        }
        // Build full name
        $fullName = trim($request->input('first_name') . ' ' . $request->input('last_name'));

        /** Update Guard Table (admins / sellers / clients) */
        $user->name  = $fullName ?: $user->name;
        if ($request->filled('email') && $request->email !== $user->email) {
            $user->email = $request->email;
        }
        // if ($request->filled('phone') && $request->phone !== $user->phone) {
        //     $user->phone = $request->phone;
        // }
        if ($request->filled('password')) {
            $user->password = $request->password;
        }
        $user->save();

        /** Sync ProfileDetails Table */
        $profile = ProfileDetail::updateOrCreate(
            [
                'user_id'   => $user->id,
                'user_type' => get_class($user),
            ],
            [
                'name'            => $user->name,
                'email'           => $user->email,
                'alternate_email' => $request->input('alternate_email'),
                'phone'           => $request->input('phone'),
                'address'         => $request->input('address'),
                'profile'         => $fileName,
            ]
        );
        /** Two-way Sync: Ensure ProfileDetail stays in sync */
        if ($profile->wasChanged(['name', 'email'])) {
            $user->name  = $profile->name;
            $user->email = $profile->email;
            $user->save();
        }
        // Logout if password changed
        if ($request->filled('password')) {
            Auth::logout();
            return redirect()->route('admin.login.get')->with('status', 'Password changed, please log in again.');
        }

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }
}
