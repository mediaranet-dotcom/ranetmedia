<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Package;
use App\Models\Payment;

class DashboardController extends Controller
{
    public function index()
    {
        // Jika request ingin view dashboard tradisional
        if (request()->has('view') && request()->get('view') === 'traditional') {
            $stats = [
                'total_customers' => Customer::count(),
                'active_packages' => Package::where('is_active', true)->count(),
                'total_income' => Payment::sum('amount'),
                'recent_payments' => Payment::where('payment_date', '>=', now()->subDays(30))->count(),
            ];

            return view('admin.dashboard', compact('stats'));
        }

        // Default: Redirect ke Filament admin panel karena menggunakan Filament untuk dashboard
        return redirect('/admin');
    }

    /**
     * Get dashboard statistics as JSON for API usage
     */
    public function getStats()
    {
        $stats = [
            'total_customers' => Customer::count(),
            'active_packages' => Package::where('is_active', true)->count(),
            'total_income' => Payment::sum('amount'),
            'recent_payments' => Payment::where('payment_date', '>=', now()->subDays(30))->count(),
            'formatted_income' => 'Rp ' . number_format(Payment::sum('amount'), 0, ',', '.'),
        ];

        return response()->json($stats);
    }

    /**
     * Get customer status badge color
     */
    public function showCustomerStatusBadge(Customer $customer)
    {
        return response()->json([
            'status' => $customer->status,
            'badge_color' => $customer->getStatusBadgeColor()
        ]);
    }
}
