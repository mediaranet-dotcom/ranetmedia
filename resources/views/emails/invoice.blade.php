<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #2563eb;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f8fafc;
            padding: 30px;
            border: 1px solid #e2e8f0;
        }
        .invoice-details {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #2563eb;
        }
        .amount {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
        }
        .due-date {
            color: #dc2626;
            font-weight: bold;
        }
        .footer {
            background-color: #374151;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 0 0 8px 8px;
            font-size: 14px;
        }
        .button {
            display: inline-block;
            background-color: #10b981;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            margin: 10px 0;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 8px 0;
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .info-label {
            font-weight: bold;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $company_name }}</h1>
        <p>Invoice Tagihan Internet</p>
    </div>

    <div class="content">
        <h2>Halo {{ $customer->name }},</h2>
        
        <p>Terima kasih telah menggunakan layanan internet kami. Berikut adalah invoice tagihan untuk periode bulan ini:</p>

        <div class="invoice-details">
            <div class="info-row">
                <span class="info-label">Nomor Invoice:</span>
                <span><strong>{{ $invoice->invoice_number }}</strong></span>
            </div>
            <div class="info-row">
                <span class="info-label">Tanggal Invoice:</span>
                <span>{{ $invoice->invoice_date->format('d F Y') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Jatuh Tempo:</span>
                <span class="due-date">{{ $invoice->due_date->format('d F Y') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Periode Tagihan:</span>
                <span>{{ $invoice->billing_period_start->format('d F Y') }} - {{ $invoice->billing_period_end->format('d F Y') }}</span>
            </div>
        </div>

        <div class="invoice-details">
            <h3>Detail Layanan</h3>
            <div class="info-row">
                <span class="info-label">Paket Internet:</span>
                <span>{{ $package->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Kecepatan:</span>
                <span>{{ $package->speed }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Teknologi:</span>
                <span>{{ $service->getNetworkTypeLabel() }}</span>
            </div>
            @if($service->ip_address)
            <div class="info-row">
                <span class="info-label">IP Address:</span>
                <span>{{ $service->ip_address }}</span>
            </div>
            @endif
        </div>

        <div class="invoice-details">
            <h3>Rincian Tagihan</h3>
            <div class="info-row">
                <span class="info-label">Subtotal:</span>
                <span>Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</span>
            </div>
            @if($invoice->tax_amount > 0)
            <div class="info-row">
                <span class="info-label">Pajak:</span>
                <span>Rp {{ number_format($invoice->tax_amount, 0, ',', '.') }}</span>
            </div>
            @endif
            @if($invoice->discount_amount > 0)
            <div class="info-row">
                <span class="info-label">Diskon:</span>
                <span>-Rp {{ number_format($invoice->discount_amount, 0, ',', '.') }}</span>
            </div>
            @endif
            <div class="info-row" style="border-top: 2px solid #2563eb; margin-top: 10px; padding-top: 10px;">
                <span class="info-label">TOTAL TAGIHAN:</span>
                <span class="amount">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</span>
            </div>
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <p><strong>Silakan lakukan pembayaran sebelum tanggal jatuh tempo untuk menghindari gangguan layanan.</strong></p>
            
            <h3>Metode Pembayaran:</h3>
            <p>
                💳 <strong>Transfer Bank:</strong><br>
                Bank BCA: 1234567890<br>
                Bank Mandiri: 0987654321<br>
                a.n. {{ $company_name }}<br><br>
                
                💰 <strong>Pembayaran Tunai:</strong><br>
                Kantor {{ $company_name }}<br>
                Senin - Sabtu: 08:00 - 17:00
            </p>
        </div>

        <div style="background-color: #fef3c7; padding: 15px; border-radius: 6px; border-left: 4px solid #f59e0b;">
            <p><strong>⚠️ Penting:</strong></p>
            <ul>
                <li>Harap konfirmasi pembayaran via WhatsApp atau email</li>
                <li>Sertakan nomor invoice saat melakukan pembayaran</li>
                <li>Layanan akan diputus otomatis jika terlambat lebih dari 7 hari</li>
            </ul>
        </div>

        <p>Jika Anda memiliki pertanyaan tentang invoice ini, jangan ragu untuk menghubungi kami.</p>
        
        <p>Terima kasih atas kepercayaan Anda menggunakan layanan {{ $company_name }}.</p>
        
        <p>Salam,<br>
        <strong>Tim {{ $company_name }}</strong></p>
    </div>

    <div class="footer">
        <p><strong>{{ $company_name }}</strong></p>
        @if($company_email)
        <p>📧 Email: {{ $company_email }}</p>
        @endif
        @if($company_phone)
        <p>📞 Telepon: {{ $company_phone }}</p>
        @endif
        <p style="margin-top: 15px; font-size: 12px; color: #9ca3af;">
            Email ini dikirim secara otomatis. Mohon jangan membalas email ini.
        </p>
    </div>
</body>
</html>
