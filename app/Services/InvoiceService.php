<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Service;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;


class InvoiceService
{
    /**
     * Generate invoice for a specific service
     */
    public function generateInvoiceForService(Service $service, Carbon $billingDate = null): Invoice
    {
        $billingDate = $billingDate ?? now();

        // Check customer status (configurable)
        $allowInactiveCustomer = config('billing.allow_inactive_customer_invoice', false);
        if (!$allowInactiveCustomer && $service->customer->status !== 'active') {
            throw new \Exception("Cannot create invoice for customer with status: {$service->customer->status}. Customer must be active to generate invoices.");
        }

        // Calculate billing period
        $billingCycle = $service->billingCycle;
        if (!$billingCycle) {
            throw new \Exception("Service {$service->id} does not have a billing cycle assigned.");
        }

        $periodStart = $billingDate->copy()->startOfMonth();
        $periodEnd = $billingDate->copy()->endOfMonth();

        // Check if invoice already exists for this period
        $existingInvoice = Invoice::where('service_id', $service->id)
            ->where('billing_period_start', $periodStart)
            ->where('billing_period_end', $periodEnd)
            ->first();

        if ($existingInvoice) {
            return $existingInvoice;
        }

        // Create invoice
        $invoice = Invoice::create([
            'customer_id' => $service->customer_id,
            'service_id' => $service->id,
            'invoice_date' => $billingDate,
            'due_date' => $billingCycle->calculateDueDate($billingDate),
            'billing_period_start' => $periodStart,
            'billing_period_end' => $periodEnd,
            'subtotal' => 0,
            'tax_rate' => 0, // Default no tax, user can change manually
            'tax_amount' => 0,
            'discount_amount' => 0,
            'total_amount' => 0,
            'paid_amount' => 0,
            'outstanding_amount' => 0,
            'status' => 'draft',
        ]);

        // Add subscription item
        $monthlyFee = $service->monthly_fee ?? $service->package->price;

        // Ensure monthly fee is not null
        if (!$monthlyFee || $monthlyFee <= 0) {
            throw new \Exception("Service {$service->id} does not have a valid monthly fee or package price.");
        }

        $this->addInvoiceItem($invoice, [
            'description' => "Langganan {$service->package->name} - {$periodStart->format('M Y')}",
            'type' => 'subscription',
            'quantity' => 1,
            'unit_price' => $monthlyFee,
            'total_price' => $monthlyFee * 1, // Explicitly calculate total_price
            'service_period_start' => $periodStart,
            'service_period_end' => $periodEnd,
        ]);

        // Calculate totals
        $this->calculateInvoiceTotals($invoice);

        // Update service billing dates
        $service->update([
            'last_billed_date' => $billingDate,
            'next_billing_date' => $billingCycle->calculateNextBillingDate($billingDate),
        ]);

        return $invoice->fresh();
    }

    /**
     * Generate invoices for all services that are due for billing
     */
    public function generateDueInvoices(Carbon $date = null): Collection
    {
        $date = $date ?? now();

        $query = Service::with(['customer', 'package', 'billingCycle'])
            ->where('status', 'active')
            ->where('auto_billing', true)
            ->where(function ($query) use ($date) {
                $query->whereNull('next_billing_date')
                    ->orWhere('next_billing_date', '<=', $date);
            });

        // Check customer status if enabled in config
        if (config('billing.auto_generation.check_customer_status', true)) {
            $allowedCustomerStatuses = config('billing.auto_generation.allowed_customer_statuses', ['active']);
            $query->whereHas('customer', function ($customerQuery) use ($allowedCustomerStatuses) {
                $customerQuery->whereIn('status', $allowedCustomerStatuses);
            });
        }

        $services = $query->get();

        $generatedInvoices = collect();
        $errors = collect();

        foreach ($services as $service) {
            try {
                $invoice = $this->generateInvoiceForService($service, $date);
                $generatedInvoices->push($invoice);

                // Update next billing date
                $this->updateNextBillingDate($service, $date);

                \Log::info("Invoice generated successfully for service {$service->id}, customer: {$service->customer->name}");
            } catch (\Exception $e) {
                $error = "Failed to generate invoice for service {$service->id} (Customer: {$service->customer->name}): " . $e->getMessage();
                \Log::error($error);
                $errors->push($error);
            }
        }

        // Log summary
        \Log::info("Auto invoice generation completed. Generated: {$generatedInvoices->count()}, Errors: {$errors->count()}");

        return $generatedInvoices;
    }

    /**
     * Update next billing date for service
     */
    private function updateNextBillingDate(Service $service, Carbon $currentDate): void
    {
        $billingCycle = $service->billingCycle;

        if (!$billingCycle) {
            return;
        }

        $nextBillingDate = match ($billingCycle->interval_type) {
            'day' => $currentDate->copy()->addDays($billingCycle->interval_count),
            'week' => $currentDate->copy()->addWeeks($billingCycle->interval_count),
            'month' => $currentDate->copy()->addMonths($billingCycle->interval_count),
            'year' => $currentDate->copy()->addYears($billingCycle->interval_count),
            default => $currentDate->copy()->addMonth(),
        };

        // Set to billing day if specified
        if ($billingCycle->billing_day && $billingCycle->interval_type === 'month') {
            $nextBillingDate->day($billingCycle->billing_day);
        }

        $service->update([
            'next_billing_date' => $nextBillingDate,
            'last_billed_date' => $currentDate,
        ]);
    }

    /**
     * Generate monthly invoices for all active services
     */
    public function generateMonthlyInvoices(int $month = null, int $year = null): Collection
    {
        $month = $month ?? now()->month;
        $year = $year ?? now()->year;

        $billingDate = Carbon::create($year, $month, 1);

        $services = Service::with(['customer', 'package', 'billingCycle'])
            ->where('status', 'active')
            ->where('auto_billing', true)
            ->whereHas('billingCycle', function ($query) {
                $query->where('interval_type', 'month')
                    ->where('interval_count', 1);
            })
            ->get();

        $generatedInvoices = collect();

        foreach ($services as $service) {
            // Check if invoice already exists for this period
            $existingInvoice = Invoice::where('service_id', $service->id)
                ->whereMonth('billing_period_start', $month)
                ->whereYear('billing_period_start', $year)
                ->first();

            if ($existingInvoice) {
                continue; // Skip if already exists
            }

            try {
                $invoice = $this->generateInvoiceForService($service, $billingDate);
                $generatedInvoices->push($invoice);
            } catch (\Exception $e) {
                \Log::error("Failed to generate monthly invoice for service {$service->id}: " . $e->getMessage());
            }
        }

        return $generatedInvoices;
    }

    /**
     * Add item to invoice
     */
    public function addInvoiceItem(Invoice $invoice, array $itemData): InvoiceItem
    {
        // Ensure total_price is calculated if not provided
        if (!isset($itemData['total_price'])) {
            $quantity = $itemData['quantity'] ?? 1;
            $unitPrice = $itemData['unit_price'] ?? 0;
            $itemData['total_price'] = $quantity * $unitPrice;
        }

        // Validate required fields
        if (!isset($itemData['unit_price']) || $itemData['unit_price'] <= 0) {
            throw new \Exception("Invoice item must have a valid unit_price.");
        }

        $item = $invoice->items()->create($itemData);
        $this->calculateInvoiceTotals($invoice);

        return $item;
    }

    /**
     * Calculate invoice totals
     */
    public function calculateInvoiceTotals(Invoice $invoice): void
    {
        $subtotal = $invoice->items()->sum('total_price') ?? 0;
        $taxRate = $invoice->tax_rate ?? 0; // Use manual tax rate
        $taxAmount = $subtotal * $taxRate;
        $discountAmount = $invoice->discount_amount ?? 0;
        $paidAmount = $invoice->paid_amount ?? 0;
        $totalAmount = $subtotal + $taxAmount - $discountAmount;

        $invoice->update([
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
            'outstanding_amount' => $totalAmount - $paidAmount,
        ]);
    }

    /**
     * Mark invoice as sent
     */
    public function markAsSent(Invoice $invoice): void
    {
        $invoice->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }

    /**
     * Process payment for invoice
     */
    public function processPayment(Invoice $invoice, float $amount, array $paymentData = []): void
    {
        $invoice->increment('paid_amount', $amount);
        $invoice->decrement('outstanding_amount', $amount);

        // Create payment record
        $invoice->payments()->create(array_merge([
            'service_id' => $invoice->service_id,
            'amount' => $amount,
            'payment_date' => now(),
            'payment_method' => $paymentData['payment_method'] ?? 'cash',
            'status' => 'completed',
        ], $paymentData));

        // Update invoice status
        if ($invoice->paid_amount >= $invoice->total_amount) {
            $invoice->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);
        } else {
            $invoice->update(['status' => 'partial_paid']);
        }
    }

    /**
     * Get overdue invoices
     */
    public function getOverdueInvoices(): Collection
    {
        return Invoice::with(['customer', 'service.package'])
            ->where('due_date', '<', now())
            ->whereIn('status', ['sent', 'partial_paid'])
            ->orderBy('due_date')
            ->get();
    }

    /**
     * Get aging report
     */
    public function getAgingReport(): array
    {
        $overdueInvoices = $this->getOverdueInvoices();

        $aging = [
            '0-30' => ['count' => 0, 'amount' => 0],
            '31-60' => ['count' => 0, 'amount' => 0],
            '61-90' => ['count' => 0, 'amount' => 0],
            '90+' => ['count' => 0, 'amount' => 0],
        ];

        foreach ($overdueInvoices as $invoice) {
            $daysOverdue = $invoice->getDaysOverdue();
            $amount = $invoice->outstanding_amount;

            if ($daysOverdue <= 30) {
                $aging['0-30']['count']++;
                $aging['0-30']['amount'] += $amount;
            } elseif ($daysOverdue <= 60) {
                $aging['31-60']['count']++;
                $aging['31-60']['amount'] += $amount;
            } elseif ($daysOverdue <= 90) {
                $aging['61-90']['count']++;
                $aging['61-90']['amount'] += $amount;
            } else {
                $aging['90+']['count']++;
                $aging['90+']['amount'] += $amount;
            }
        }

        return $aging;
    }

    /**
     * Generate PDF for invoice
     */
    public function generatePDF(Invoice $invoice)
    {
        // Load invoice with relationships
        $invoice->load(['customer', 'service.package', 'service.odp', 'items']);

        // Generate PDF using Laravel DomPDF
        $pdf = Pdf::loadView('pdf.invoice', compact('invoice'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf;
    }

    /**
     * Download PDF for invoice
     */
    public function downloadPDF(Invoice $invoice)
    {
        $pdf = $this->generatePDF($invoice);
        $filename = "invoice-{$invoice->invoice_number}.pdf";

        // Save to temporary file first
        $tempPath = storage_path('app/temp/' . $filename);

        // Create temp directory if not exists
        if (!file_exists(dirname($tempPath))) {
            mkdir(dirname($tempPath), 0755, true);
        }

        // Save PDF to temp file
        file_put_contents($tempPath, $pdf->output());

        // Return file download response
        return response()->download($tempPath, $filename)->deleteFileAfterSend();
    }

    /**
     * Stream PDF for invoice (view in browser)
     */
    public function streamPDF(Invoice $invoice)
    {
        $pdf = $this->generatePDF($invoice);
        $filename = "invoice-{$invoice->invoice_number}.pdf";

        return $pdf->stream($filename);
    }

    /**
     * Save PDF to storage
     */
    public function savePDF(Invoice $invoice, string $path = null): string
    {
        $pdf = $this->generatePDF($invoice);
        $filename = $path ?? "invoices/invoice-{$invoice->invoice_number}.pdf";

        // Save to storage
        Storage::put($filename, $pdf->output());

        return $filename;
    }
}
