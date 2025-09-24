<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pembayaran</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .report-title {
            font-size: 16px;
            font-weight: bold;
            margin-top: 10px;
        }
        .summary {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            background-color: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
        }
        .summary-item {
            text-align: center;
            flex: 1;
        }
        .summary-value {
            font-size: 16px;
            font-weight: bold;
            color: #2563eb;
        }
        .summary-label {
            font-size: 11px;
            color: #666;
            margin-top: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #4f46e5;
            color: white;
            font-weight: bold;
            text-align: center;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .status-badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        .status-ontime {
            background-color: #dcfce7;
            color: #166534;
        }
        .status-late {
            background-color: #fef2f2;
            color: #dc2626;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 11px;
            color: #666;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">RANET PROVIDER</div>
        <div>Internet Service Provider</div>
        <div class="report-title">LAPORAN PEMBAYARAN</div>
        <div style="font-size: 12px; margin-top: 10px;">
            Periode: {{ $filters['from_date'] ?? 'Semua' }} - {{ $filters['to_date'] ?? 'Semua' }}
        </div>
    </div>

    <div class="summary">
        <div class="summary-item">
            <div class="summary-value">{{ $totalPayments }}</div>
            <div class="summary-label">Total Pembayaran</div>
        </div>
        <div class="summary-item">
            <div class="summary-value">Rp {{ number_format($totalAmount, 0, ',', '.') }}</div>
            <div class="summary-label">Total Nominal</div>
        </div>
        <div class="summary-item">
            <div class="summary-value">{{ $onTimePayments }}</div>
            <div class="summary-label">Tepat Waktu</div>
        </div>
        <div class="summary-item">
            <div class="summary-value">{{ $latePayments }}</div>
            <div class="summary-label">Terlambat</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="12%">Tanggal</th>
                <th width="10%">No. Pelanggan</th>
                <th width="15%">Nama Pelanggan</th>
                <th width="12%">No. Invoice</th>
                <th width="12%">Jumlah</th>
                <th width="10%">Metode</th>
                <th width="10%">Jatuh Tempo</th>
                <th width="8%">Status</th>
                <th width="6%">Ref</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $index => $payment)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $payment->payment_date->format('d/m/Y') }}</td>
                    <td>{{ $payment->invoice->customer->customer_number ?? 'N/A' }}</td>
                    <td>{{ $payment->invoice->customer->name ?? 'N/A' }}</td>
                    <td>{{ $payment->invoice->invoice_number ?? 'N/A' }}</td>
                    <td class="text-right">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                    <td class="text-center">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                    <td class="text-center">
                        {{ $payment->invoice ? $payment->invoice->due_date->format('d/m/Y') : 'N/A' }}
                    </td>
                    <td class="text-center">
                        @php
                            $isLate = $payment->invoice && $payment->payment_date > $payment->invoice->due_date;
                        @endphp
                        <span class="status-badge {{ $isLate ? 'status-late' : 'status-ontime' }}">
                            {{ $isLate ? 'Terlambat' : 'Tepat Waktu' }}
                        </span>
                    </td>
                    <td class="text-center">{{ $payment->reference_number ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <div>Dicetak pada: {{ $printDate->format('d/m/Y H:i:s') }}</div>
        <div>Sistem Manajemen RANET Provider</div>
    </div>
</body>
</html>
