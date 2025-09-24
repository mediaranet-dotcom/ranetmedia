<div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg border-t">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="text-center">
            <div class="text-2xl font-bold text-green-600">
                Rp {{ number_format($total, 0, ',', '.') }}
            </div>
            <div class="text-sm text-gray-500 dark:text-gray-400">Total Pembayaran</div>
        </div>
        <div class="text-center">
            <div class="text-2xl font-bold text-blue-600">
                {{ $count }}
            </div>
            <div class="text-sm text-gray-500 dark:text-gray-400">Jumlah Transaksi</div>
        </div>
    </div>
</div>
