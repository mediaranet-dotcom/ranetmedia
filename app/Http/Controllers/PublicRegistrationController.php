<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Package;
use App\Models\ServiceApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class PublicRegistrationController extends Controller
{
    /**
     * Show customer registration form
     */
    public function showRegistrationForm()
    {
        $packages = Package::where('is_active', true)->get();
        return view('public.registration', compact('packages'));
    }

    /**
     * Handle customer registration
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:customers,email',
            'phone' => 'required|string|max:20',
            'identity_number' => 'required|string|max:50',
            'address' => 'required|string',
            'province' => 'required|string|max:100',
            'regency' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'village' => 'required|string|max:100',
            'hamlet' => 'nullable|string|max:100',
            'rt' => 'nullable|string|max:10',
            'rw' => 'nullable|string|max:10',
            'postal_code' => 'nullable|string|max:10',
            'address_notes' => 'nullable|string',
            'package_id' => 'required|exists:packages,id',
            'installation_notes' => 'nullable|string',
        ], [
            'name.required' => 'Nama lengkap harus diisi.',
            'phone.required' => 'Nomor telepon harus diisi.',
            'identity_number.required' => 'Nomor KTP/SIM harus diisi.',
            'address.required' => 'Alamat jalan/rumah harus diisi.',
            'province.required' => 'Provinsi harus diisi.',
            'regency.required' => 'Kabupaten/Kota harus diisi.',
            'district.required' => 'Kecamatan harus diisi.',
            'village.required' => 'Desa/Kelurahan harus diisi.',
            'package_id.required' => 'Silakan pilih paket layanan.',
            'package_id.exists' => 'Paket layanan yang dipilih tidak valid.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
        ]);

        try {
            DB::beginTransaction();

            // Create customer with pending status
            $customer = Customer::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'identity_number' => $validated['identity_number'],
                'address' => $validated['address'],
                'province' => $validated['province'],
                'regency' => $validated['regency'],
                'district' => $validated['district'],
                'village' => $validated['village'],
                'hamlet' => $validated['hamlet'],
                'rt' => $validated['rt'],
                'rw' => $validated['rw'],
                'postal_code' => $validated['postal_code'],
                'address_notes' => $validated['address_notes'],
                'status' => 'inactive', // Pending approval
            ]);

            // Create service application for admin review
            $serviceApplication = ServiceApplication::create([
                'customer_id' => $customer->id,
                'package_id' => $validated['package_id'],
                'installation_address' => $customer->full_address,
                'installation_notes' => $validated['installation_notes'],
                'status' => 'pending',
            ]);

            // Store registration data in session for success page
            session([
                'registration_data' => [
                    'customer_id' => $customer->id,
                    'package_id' => $validated['package_id'],
                    'installation_notes' => $validated['installation_notes'],
                    'service_application_id' => $serviceApplication->id,
                ]
            ]);

            DB::commit();

            return redirect()->route('registration.success')
                ->with('customer_number', $customer->customer_number);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Registration error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Terjadi kesalahan saat mendaftar: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Show registration success page
     */
    public function success()
    {
        $customerNumber = session('customer_number');
        if (!$customerNumber) {
            return redirect()->route('registration.form');
        }

        return view('public.registration-success', compact('customerNumber'));
    }

    /**
     * Show service application form for existing customers
     */
    public function showServiceForm()
    {
        $packages = Package::where('is_active', true)->get();
        return view('public.service-application', compact('packages'));
    }

    /**
     * Handle service application
     */
    public function applyService(Request $request)
    {
        $validated = $request->validate([
            'customer_number' => 'required|exists:customers,customer_number',
            'package_id' => 'required|exists:packages,id',
            'installation_address' => 'nullable|string',
            'installation_notes' => 'nullable|string',
        ]);

        $customer = Customer::where('customer_number', $validated['customer_number'])->first();

        if (!$customer) {
            return back()->withErrors(['customer_number' => 'Nomor pelanggan tidak ditemukan.']);
        }

        // Create service application record
        ServiceApplication::create([
            'customer_id' => $customer->id,
            'package_id' => $validated['package_id'],
            'installation_address' => $validated['installation_address'],
            'installation_notes' => $validated['installation_notes'],
            'status' => 'pending',
        ]);

        return redirect()->route('service.application.success')
            ->with('customer_number', $customer->customer_number);
    }

    /**
     * Show service application success page
     */
    public function serviceSuccess()
    {
        $customerNumber = session('customer_number');
        if (!$customerNumber) {
            return redirect()->route('service.application.form');
        }

        return view('public.service-application-success', compact('customerNumber'));
    }
}
