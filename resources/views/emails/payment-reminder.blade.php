<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengingat Pembayaran</title>
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
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }

        .logo {
            max-width: 200px;
            height: auto;
            margin-bottom: 15px;
        }

        .company-info {
            margin-top: 10px;
            font-size: 14px;
        }

        .website-link {
            color: #ffffff;
            text-decoration: none;
            font-weight: bold;
        }

        .website-link:hover {
            text-decoration: underline;
        }

        .content {
            background-color: #f8fafc;
            padding: 30px;
            border: 1px solid #e2e8f0;
        }

        .alert {
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
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
            color: #dc2626;
        }

        .overdue {
            color: #dc2626;
            font-weight: bold;
            font-size: 18px;
        }

        .footer {
            background-color: #374151;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 0 0 8px 8px;
            font-size: 14px;
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

        /* Mobile Responsive Styles */
        @media only screen and (max-width: 600px) {
            body {
                padding: 10px !important;
                margin: 0 !important;
            }

            .header {
                padding: 15px !important;
            }

            .logo {
                max-width: 150px !important;
                margin-bottom: 10px !important;
            }

            .footer .logo {
                max-width: 100px !important;
                margin-bottom: 8px !important;
            }

            .header h1 {
                font-size: 20px !important;
                margin: 0 0 10px 0 !important;
            }

            .header p {
                font-size: 14px !important;
                margin: 0 !important;
            }

            .content {
                padding: 15px !important;
            }

            .alert {
                padding: 12px !important;
                margin: 15px 0 !important;
            }

            .invoice-details {
                padding: 15px !important;
                margin: 15px 0 !important;
            }

            .info-row {
                flex-direction: column !important;
                align-items: flex-start !important;
                gap: 5px !important;
            }

            .info-label {
                font-size: 14px !important;
            }

            .amount {
                font-size: 20px !important;
            }

            .overdue {
                font-size: 16px !important;
            }

            .footer {
                padding: 15px !important;
                font-size: 12px !important;
            }

            /* Make tables responsive */
            table {
                width: 100% !important;
            }

            /* Ensure text doesn't overflow */
            h1,
            h2,
            h3,
            p,
            span,
            div {
                word-wrap: break-word !important;
                word-break: break-word !important;
            }
        }

        /* Extra small screens */
        @media only screen and (max-width: 480px) {
            .header h1 {
                font-size: 18px !important;
            }

            .amount {
                font-size: 18px !important;
            }

            .content {
                padding: 10px !important;
            }

            .invoice-details {
                padding: 10px !important;
            }

            .alert {
                padding: 10px !important;
            }
        }
    </style>
</head>

@php
$headerBgColor = match($urgency_level) {
'final' => '#dc2626',
'urgent' => '#ea580c',
'reminder' => '#d97706',
default => '#2563eb'
};

$alertStyles = match($urgency_level) {
'final' => 'background-color: #fef2f2; border-left: 4px solid #dc2626; color: #991b1b;',
'urgent' => 'background-color: #fff7ed; border-left: 4px solid #ea580c; color: #c2410c;',
'reminder' => 'background-color: #fffbeb; border-left: 4px solid #d97706; color: #92400e;',
default => 'background-color: #eff6ff; border-left: 4px solid #2563eb; color: #1d4ed8;'
};
@endphp

<body>
    <div class="header" style="background-color:{{ $headerBgColor }}">
        <!-- Logo RANET -->
        <img src="{{ asset('images/ranet-logo.png') }}" alt="RANET Logo" class="logo">

        <h1>{{ $company_name }}</h1>
        <div class="company-info">
            <a href="https://www.adau.net.id" class="website-link" target="_blank">www.adau.net.id</a>
        </div>

        <p>
            @if($urgency_level === 'final')
            🚨 PERINGATAN TERAKHIR
            @elseif($urgency_level === 'urgent')
            ⚠️ PEMBAYARAN URGENT
            @elseif($urgency_level === 'reminder')
            📢 PENGINGAT PEMBAYARAN
            @else
            💌 PENGINGAT PEMBAYARAN
            @endif
        </p>
    </div>

    <div class="content">
        <h2>Halo {{ $customer->name }},</h2>

        <div class="alert" style="{{ $alertStyles }}">
            <p><strong>
                    @if($urgency_level === 'final')
                    🚨 PERINGATAN TERAKHIR: Layanan internet Anda akan diputus dalam 24 jam jika pembayaran tidak segera dilakukan!
                    @elseif($urgency_level === 'urgent')
                    ⚠️ URGENT: Pembayaran Anda sudah terlambat {{ $days_overdue }} hari. Segera lakukan pembayaran untuk menghindari pemutusan layanan.
                    @elseif($urgency_level === 'reminder')
                    📢 Pembayaran Anda sudah terlambat {{ $days_overdue }} hari. Mohon segera lakukan pembayaran.
                    @else
                    💌 Kami ingatkan bahwa tagihan internet Anda sudah jatuh tempo. Mohon segera lakukan pembayaran.
                    @endif
                </strong></p>
        </div>

        <div class="invoice-details">
            <h3>Detail Invoice Terlambat</h3>
            <div class="info-row">
                <span class="info-label">Nomor Invoice:</span>
                <span><strong>{{ $invoice->invoice_number }}</strong></span>
            </div>
            <div class="info-row">
                <span class="info-label">Tanggal Jatuh Tempo:</span>
                <span class="overdue">{{ $invoice->due_date->format('d F Y') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Terlambat:</span>
                <span class="overdue">{{ $days_overdue }} hari</span>
            </div>
            <div class="info-row">
                <span class="info-label">Periode Tagihan:</span>
                <span>{{ $invoice->billing_period_start->format('d F Y') }} - {{ $invoice->billing_period_end->format('d F Y') }}</span>
            </div>
            <div class="info-row" style="border-top: 2px solid #dc2626; margin-top: 10px; padding-top: 10px;">
                <span class="info-label">JUMLAH YANG HARUS DIBAYAR:</span>
                <span class="amount">Rp {{ number_format($invoice->outstanding_amount, 0, ',', '.') }}</span>
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
            @if($service->ip_address)
            <div class="info-row">
                <span class="info-label">IP Address:</span>
                <span>{{ $service->ip_address }}</span>
            </div>
            @endif
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <h3>💳 Metode Pembayaran:</h3>
            <div style="background-color: white; padding: 20px; border-radius: 8px; text-align: left;">
                <p><strong>Transfer Bank:</strong></p>
                <ul>
                    <li>Bank BCA: 1234567890</li>
                    <li>Bank Mandiri: 0987654321</li>
                    <li>a.n. {{ $company_name }}</li>
                </ul>

                <p><strong>Pembayaran Tunai:</strong></p>
                <ul>
                    <li>Kantor {{ $company_name }}</li>
                    <li>Senin - Sabtu: 08:00 - 17:00</li>
                </ul>

                <p><strong>E-Wallet:</strong></p>
                <ul>
                    <li>OVO: 081234567890</li>
                    <li>DANA: 081234567890</li>
                    <li>GoPay: 081234567890</li>
                </ul>
            </div>
        </div>

        @if($urgency_level === 'final')
        <div style="background-color: #fef2f2; padding: 20px; border-radius: 6px; border: 2px solid #dc2626;">
            <h3 style="color: #dc2626; margin-top: 0;">🚨 KONSEKUENSI KETERLAMBATAN:</h3>
            <ul style="color: #991b1b;">
                <li><strong>Layanan internet akan diputus otomatis dalam 24 jam</strong></li>
                <li>Biaya reconnect Rp 100.000 akan dikenakan</li>
                <li>Data dan konfigurasi mungkin akan hilang</li>
                <li>Proses aktivasi ulang membutuhkan waktu 1-3 hari kerja</li>
            </ul>
        </div>
        @elseif($urgency_level === 'urgent')
        <div style="background-color: #fff7ed; padding: 15px; border-radius: 6px; border-left: 4px solid #ea580c;">
            <p style="color: #c2410c;"><strong>⚠️ Peringatan:</strong></p>
            <ul style="color: #c2410c;">
                <li>Layanan akan diputus jika terlambat lebih dari 14 hari</li>
                <li>Biaya keterlambatan mungkin akan dikenakan</li>
                <li>Segera hubungi kami jika ada kendala pembayaran</li>
            </ul>
        </div>
        @endif

        <div style="background-color: #f0f9ff; padding: 15px; border-radius: 6px; border-left: 4px solid #0ea5e9;">
            <p><strong>📞 Butuh Bantuan?</strong></p>
            <p>Jika Anda mengalami kesulitan dalam pembayaran atau memiliki pertanyaan, jangan ragu untuk menghubungi kami:</p>
            <ul>
                @if($company_phone)
                <li>📞 Telepon: {{ $company_phone }}</li>
                @endif
                @if($company_email)
                <li>📧 Email: {{ $company_email }}</li>
                @endif
                <li>💬 WhatsApp: 081234567890</li>
            </ul>
        </div>

        <p><strong>Konfirmasi Pembayaran:</strong><br>
            Setelah melakukan pembayaran, mohon konfirmasi via WhatsApp atau email dengan menyertakan:</p>
        <ul>
            <li>Nomor Invoice: {{ $invoice->invoice_number }}</li>
            <li>Nama: {{ $customer->name }}</li>
            <li>Bukti transfer/pembayaran</li>
        </ul>

        <p>Terima kasih atas perhatian dan kerjasamanya.</p>

        <p>Salam,<br>
            <strong>Tim {{ $company_name }}</strong>
        </p>
    </div>

    <div class="footer">
        <!-- Logo RANET di Footer -->
        <img src="{{ asset('images/ranet-logo.png') }}" alt="RANET Logo" class="logo" style="max-width: 120px; margin-bottom: 10px;">

        <p><strong>{{ $company_name }}</strong></p>
        <p><strong>Internet Network Solution</strong></p>

        @if($company_email)
        <p>📧 Email: {{ $company_email }}</p>
        @endif
        @if($company_phone)
        <p>📞 Telepon: {{ $company_phone }}</p>
        @endif

        <p>🌐 Website: <a href="https://www.adau.net.id" style="color: #ffffff; text-decoration: none;" target="_blank">www.adau.net.id</a></p>

        <p style="margin-top: 15px; font-size: 12px; color: #9ca3af;">
            Email ini dikirim secara otomatis. Mohon jangan membalas email ini.
        </p>
    </div>
</body>

</html>