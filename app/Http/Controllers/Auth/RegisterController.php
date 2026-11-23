<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Services\CartService;
use App\Models\ActivityLog;

class RegisterController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function showRegistrationForm()
    {
        if (Auth::check()) {
            return redirect()->route('customer.account');
        }

        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'phone'    => ['required', 'string', 'regex:/^(?:\+?8801|01)[3-9]\d{8}$/'],
            'password' => [
                'required',
                'confirmed',
                'min:8',
            ],
            'terms'    => 'accepted',
        ], [
            'terms.accepted' => 'You must accept the Terms and Conditions to create an account.',
            'phone.regex'    => 'Please provide a valid Bangladeshi phone number (e.g. 01712345678).',
        ]);

        $user = User::create([
            'name'       => $request->name,
            'email'      => strtolower(trim($request->email)),
            'phone'      => $request->phone,
            'password'   => Hash::make($request->password),
            'role'       => 'customer', // strictly customer role
            'status'     => 1,
            'created_by' => 'Self Registration',
        ]);

        // Auto log in customer
        Auth::login($user);

        // Merge cart
        $this->cartService->getCart();

        ActivityLog::log('Customer Registered', 'User', $user->id, "New customer account created for {$user->email}");

        return redirect()->route('customer.account')
            ->with('success', 'Registration successful! Welcome to your account dashboard.');
    }
}
