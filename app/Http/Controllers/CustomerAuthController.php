<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class CustomerAuthController extends Controller
{
    /**
     * Show customer login form
     */
    public function showLogin()
    {
        return view('customer.login');
    }

    /**
     * Handle customer login
     */
    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'customer_id' => 'nullable|string',
        ]);

        // Find customer by phone - use original input format
        $query = Customer::where('phone', $request->phone);

        // If customer_id provided, add it to query (check both customer_id and customer_number)
        if ($request->filled('customer_id')) {
            $query->where(function ($q) use ($request) {
                $q->where('customer_id', $request->customer_id)
                    ->orWhere('customer_number', $request->customer_id);
            });
        }

        $customer = $query->first();

        if (!$customer) {
            // Debug logging
            Log::info('Login failed', [
                'input_phone' => $request->phone,
                'customer_id' => $request->customer_id,
                'query_sql' => $query->toSql(),
                'query_bindings' => $query->getBindings()
            ]);

            return back()->withErrors([
                'phone' => 'Nomor telepon tidak ditemukan atau ID pelanggan tidak cocok.'
            ])->withInput();
        }

        // Store customer in session
        Session::put('customer_id', $customer->id);
        Session::put('customer_data', [
            'id' => $customer->id,
            'name' => $customer->name,
            'phone' => $customer->phone,
            'customer_id' => $customer->customer_id,
        ]);

        return redirect()->route('customer.dashboard')
            ->with('success', 'Selamat datang, ' . $customer->name . '!');
    }

    /**
     * Show customer dashboard
     */
    public function dashboard()
    {
        $customerId = Session::get('customer_id');

        if (!$customerId) {
            return redirect()->route('customer.login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        $customer = Customer::find($customerId);

        if (!$customer) {
            Session::forget(['customer_id', 'customer_data']);
            return redirect()->route('customer.login')
                ->with('error', 'Sesi tidak valid. Silakan login kembali.');
        }

        // Get customer services
        $services = Service::where('customer_id', $customer->id)
            ->with('package')
            ->get();

        // Get recent invoices
        $recentInvoices = Invoice::where('customer_id', $customer->id)
            ->with(['service.package'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Calculate stats
        $activeServices = $services->where('status', 'active')->count();
        $pendingInvoices = Invoice::where('customer_id', $customer->id)
            ->whereIn('status', ['draft', 'sent', 'pending', 'overdue', 'partial_paid'])
            ->where('outstanding_amount', '>', 0)
            ->count();
        $totalOutstanding = Invoice::where('customer_id', $customer->id)
            ->whereIn('status', ['draft', 'sent', 'pending', 'overdue', 'partial_paid'])
            ->where('outstanding_amount', '>', 0)
            ->sum('outstanding_amount');

        // Get company settings for payment info
        $companySetting = \App\Models\CompanySetting::getActive();

        return view('customer.dashboard', compact(
            'customer',
            'services',
            'recentInvoices',
            'activeServices',
            'pendingInvoices',
            'totalOutstanding',
            'companySetting'
        ));
    }

    /**
     * Handle customer logout
     */
    public function logout()
    {
        Session::forget(['customer_id', 'customer_data']);

        return redirect()->route('customer.login')
            ->with('success', 'Anda telah berhasil logout.');
    }

    /**
     * Clean phone number format
     */
    private function cleanPhoneNumber(string $phone): string
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Convert to standard format
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        } elseif (!str_starts_with($phone, '62')) {
            $phone = '62' . $phone;
        }

        return $phone;
    }

    /**
     * Middleware to check if customer is logged in
     */
    public function checkAuth()
    {
        $customerId = Session::get('customer_id');

        if (!$customerId) {
            return redirect()->route('customer.login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        return null;
    }
}
