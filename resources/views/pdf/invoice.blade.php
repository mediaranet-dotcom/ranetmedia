@php
$company = \App\Models\CompanySetting::getActive();
@endphp
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.3;
            color: #333;
        }

        @page {
            margin: 12mm;
            size: A4;
        }

        .no-break {
            page-break-inside: avoid;
        }

        .container {
            max-width: 700px;
            margin: 0 auto;
            padding: 12px;
        }

        .header {
            display: table;
            width: 100%;
            margin-bottom: 15px;
            border-bottom: 2px solid #3b82f6;
            padding-bottom: 10px;
        }

        .company-info {
            display: table-cell;
            width: 60%;
            vertical-align: top;
        }

        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #3b82f6;
            margin-bottom: 2px;
        }

        .company-tagline {
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 10px;
        }

        .company-details {
            font-size: 11px;
            color: #6b7280;
            line-height: 1.5;
        }

        .invoice-info {
            display: table-cell;
            width: 40%;
            vertical-align: top;
            text-align: right;
        }

        .invoice-title {
            font-size: 20px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 3px;
        }

        .invoice-number {
            font-size: 14px;
            color: #3b82f6;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .invoice-dates {
            font-size: 10px;
            color: #6b7280;
        }

        .billing-section {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }

        .bill-to,
        .service-info {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 20px;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 10px;
            text-transform: uppercase;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 5px;
        }

        .customer-info,
        .service-details {
            font-size: 12px;
            line-height: 1.6;
        }

        .customer-name {
            font-weight: bold;
            font-size: 14px;
            color: #1f2937;
            margin-bottom: 5px;
        }

        .customer-number {
            color: #3b82f6;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 11px;
        }

        .items-table th {
            background-color: #f3f4f6;
            color: #1f2937;
            font-weight: bold;
            padding: 8px 6px;
            text-align: left;
            border-bottom: 2px solid #d1d5db;
        }

        .items-table td {
            padding: 6px 6px;
            border-bottom: 1px solid #e5e7eb;
        }

        .items-table .text-right {
            text-align: right;
        }

        .items-table .text-center {
            text-align: center;
        }

        .totals-section {
            float: right;
            width: 250px;
            margin-bottom: 15px;
        }

        .totals-table {
            width: 100%;
            font-size: 11px;
        }

        .totals-table td {
            padding: 5px 8px;
            border-bottom: 1px solid #e5e7eb;
        }

        .totals-table .total-row {
            font-weight: bold;
            font-size: 14px;
            background-color: #f3f4f6;
            border-top: 2px solid #3b82f6;
        }

        .payment-info {
            clear: both;
            margin-top: 15px;
            padding: 12px;
            background-color: #f9fafb;
            border-radius: 6px;
            border-left: 3px solid #3b82f6;
        }

        .payment-title {
            font-size: 12px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 6px;
        }

        .payment-details {
            font-size: 9px;
            line-height: 1.4;
            color: #4b5563;
        }

        .footer {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 9px;
            color: #6b7280;
            line-height: 1.3;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-paid {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-partial {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-overdue {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .status-sent {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .status-draft {
            background-color: #f3f4f6;
            color: #374151;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="company-info">
                @if($company->logo_path && ($company->invoice_settings['show_logo'] ?? true))
                <div style="margin-bottom: 10px;">
                    <img src="{{ public_path('storage/' . $company->logo_path) }}"
                        alt="{{ $company->company_name }}"
                        style="height: 40px; max-width: 200px; object-fit: contain;">
                </div>
                @endif
                <div class="company-name">{{ $company->company_name }}</div>
                <div class="company-tagline">Internet Service Provider</div>
                <div class="company-details">
                    @if($company->company_address)
                    {{ $company->company_address }}<br>
                    @endif
                    @if($company->company_phone || $company->company_email)
                    @if($company->company_phone)Telp: {{ $company->company_phone }}@endif
                    @if($company->company_phone && $company->company_email) | @endif
                    @if($company->company_email)Email: {{ $company->company_email }}@endif
                    <br>
                    @endif
                    @if($company->company_website)
                    Website: {{ $company->company_website }}<br>
                    @endif
                    @if($company->tax_number && ($company->invoice_settings['show_tax_number'] ?? true))
                    NPWP: {{ $company->tax_number }}<br>
                    @endif
                    @if($company->business_license && ($company->invoice_settings['show_business_license'] ?? true))
                    NIB: {{ $company->business_license }}
                    @endif
                </div>
            </div>
            <div class="invoice-info">
                <div class="invoice-title">INVOICE</div>
                <div class="invoice-number"># {{ $invoice->invoice_number }}</div>
                <div class="invoice-dates">
                    <strong>Tanggal:</strong> {{ $invoice->invoice_date->format('d M Y') }}<br>
                    <strong>Jatuh Tempo:</strong> {{ $invoice->due_date->format('d M Y') }}<br>
                    <span class="status-badge status-{{ $invoice->status }}">
                        {{ match($invoice->status) {
                            'draft' => 'Draft',
                            'sent' => 'Terkirim',
                            'paid' => 'Lunas',
                            'partial_paid' => 'Sebagian',
                            'overdue' => 'Terlambat',
                            'cancelled' => 'Dibatalkan',
                            default => ucfirst($invoice->status)
                        } }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Billing Information -->
        <div class="billing-section">
            <div class="bill-to">
                <div class="section-title">Tagihan Untuk</div>
                <div class="customer-info">
                    <div class="customer-number">{{ $invoice->customer->customer_number }}</div>
                    <div class="customer-name">{{ $invoice->customer->name }}</div>
                    <div>{{ $invoice->customer->email }}</div>
                    <div>{{ $invoice->customer->phone }}</div>
                    <div style="margin-top: 8px;">
                        {{ $invoice->customer->address }}
                    </div>
                </div>
            </div>
            <div class="service-info">
                <div class="section-title">Detail Layanan</div>
                <div class="service-details">
                    <strong>Paket:</strong> {{ $invoice->service->package->name }}<br>
                    <strong>Kecepatan:</strong> {{ $invoice->service->package->speed }}<br>
                    <strong>Periode:</strong> {{ $invoice->billing_period_start->format('d M Y') }} - {{ $invoice->billing_period_end->format('d M Y') }}<br>
                    @if($invoice->service->odp)
                    <strong>ODP:</strong> {{ $invoice->service->odp->name }}<br>
                    <strong>Port:</strong> {{ $invoice->service->odp_port }}
                    @endif
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table no-break">
            <thead>
                <tr>
                    <th style="width: 50%">Deskripsi</th>
                    <th style="width: 15%" class="text-center">Qty</th>
                    <th style="width: 20%" class="text-right">Harga Satuan</th>
                    <th style="width: 15%" class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                <tr>
                    <td>
                        <strong>{{ $item->description }}</strong>
                        @if($item->service_period_start && $item->service_period_end)
                        <br><small style="color: #6b7280;">
                            Periode: {{ $item->service_period_start->format('d M Y') }} - {{ $item->service_period_end->format('d M Y') }}
                        </small>
                        @endif
                    </td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($item->total_price, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals -->
        <div class="totals-section no-break">
            <table class="totals-table">
                <tr>
                    <td>Subtotal:</td>
                    <td class="text-right">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</td>
                </tr>
                @if($invoice->discount_amount > 0)
                <tr>
                    <td>Diskon:</td>
                    <td class="text-right">- Rp {{ number_format($invoice->discount_amount, 0, ',', '.') }}</td>
                </tr>
                @endif
                @if($invoice->tax_rate > 0)
                <tr>
                    <td>PPN ({{ number_format($invoice->tax_rate * 100, 0) }}%):</td>
                    <td class="text-right">Rp {{ number_format($invoice->tax_amount, 0, ',', '.') }}</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td><strong>TOTAL:</strong></td>
                    <td class="text-right"><strong>Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</strong></td>
                </tr>
                @if($invoice->paid_amount > 0)
                <tr>
                    <td>Sudah Dibayar:</td>
                    <td class="text-right">- Rp {{ number_format($invoice->paid_amount, 0, ',', '.') }}</td>
                </tr>
                <tr style="color: #dc2626; font-weight: bold;">
                    <td>Sisa Tagihan:</td>
                    <td class="text-right">Rp {{ number_format($invoice->outstanding_amount, 0, ',', '.') }}</td>
                </tr>
                @endif
            </table>
        </div>

        @if($company->bank_details && ($company->invoice_settings['show_bank_details'] ?? true))
        <!-- Payment Information -->
        <div class="payment-info no-break">
            <div class="payment-title">Informasi Pembayaran</div>
            <div class="payment-details">
                {!! nl2br(e($company->bank_details)) !!}<br>
                <strong>Catatan:</strong> Sertakan nomor invoice ({{ $invoice->invoice_number }}) sebagai keterangan.
            </div>
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            @if($company->invoice_settings['footer_text'] ?? null)
            <p>{{ $company->invoice_settings['footer_text'] }}</p>
            @endif
            <p>Invoice ini dibuat secara otomatis pada {{ now()->format('d M Y H:i') }}</p>
        </div>
    </div>
</body>

</html>