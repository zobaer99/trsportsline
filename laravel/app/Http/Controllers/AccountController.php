<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AccountController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $existing = Customer::where('phone', $request->input('phone'))->first();
        $phoneRules = ['required','regex:/^01[3-9][0-9]{8}$/'];
        if (!$existing || !$existing->is_guest) $phoneRules[] = 'unique:customers,phone';
        $data = $request->validate(['name' => ['required','string','max:120'], 'phone' => $phoneRules, 'email' => ['nullable','email','max:255'], 'password' => ['required','string','min:8','confirmed']]);
        $customer = $existing && $existing->is_guest ? $existing : new Customer();
        $customer->fill(['name' => $data['name'], 'phone' => $data['phone'], 'email' => $data['email'] ?? null, 'password' => $data['password'], 'is_guest' => false, 'guest_access_token' => null]);
        $customer->save();
        return $this->authenticated($customer);
    }

    public function login(Request $request): JsonResponse
    {
        $data = $request->validate(['phone' => ['required','regex:/^01[3-9][0-9]{8}$/'], 'password' => ['required','string']]);
        $customer = Customer::where('phone', $data['phone'])->where('is_guest', false)->first();
        if (!$customer || !$customer->password || !Hash::check($data['password'], $customer->password)) {
            throw ValidationException::withMessages(['phone' => 'The phone number or password is incorrect.']);
        }
        return $this->authenticated($customer);
    }

    public function guestLogin(Request $request): JsonResponse
    {
        $data = $request->validate(['phone' => ['required','regex:/^01[3-9][0-9]{8}$/'], 'access_code' => ['required','string','size:10']]);
        $customer = Customer::where('phone', $data['phone'])->where('is_guest', true)->first();
        if (!$customer || !$customer->guest_access_token || !Hash::check(strtoupper($data['access_code']), $customer->guest_access_token)) {
            throw ValidationException::withMessages(['access_code' => 'The phone number or guest access code is incorrect.']);
        }
        return $this->authenticated($customer);
    }

    public function profile(Request $request): JsonResponse
    {
        $customer = $this->customer($request);
        return response()->json(['data' => $customer->only(['id','name','phone','email','district','address','is_guest','created_at'])]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $data = $request->validate(['name' => ['required','string','max:120'], 'email' => ['nullable','email','max:255'], 'district' => ['nullable','string','max:80'], 'address' => ['nullable','string','max:1000']]);
        $customer = $this->customer($request);
        $customer->update($data);
        return $this->profile($request);
    }

    public function updatePassword(Request $request): JsonResponse
    {
        $customer = $this->customer($request);
        $data = $request->validate(['password' => ['required','string','min:8','confirmed']]);
        $customer->password = $data['password'];
        $customer->is_guest = false;
        $customer->guest_access_token = null;
        $customer->save();
        return response()->json(['message' => 'Your account now has a password. Sign in with your phone number next time.']);
    }

    public function orders(Request $request): JsonResponse
    {
        $orders = $this->customer($request)->orders()->with('items')->latest()->paginate(20);
        return response()->json(['data' => $orders->items(), 'meta' => ['current_page' => $orders->currentPage(), 'last_page' => $orders->lastPage()]]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()?->currentAccessToken()?->delete();
        return response()->json(['message' => 'Signed out.']);
    }

    private function authenticated(Customer $customer): JsonResponse
    {
        $customer->tokens()->where('name', 'customer-dashboard')->delete();
        $token = $customer->createToken('customer-dashboard', ['customer'])->plainTextToken;
        return response()->json(['token' => $token, 'data' => $customer->only(['id','name','phone','email','district','address','is_guest'])]);
    }

    private function customer(Request $request): Customer
    {
        abort_unless($request->user() instanceof Customer, 403, 'Customer access is required.');
        return $request->user();
    }
}
