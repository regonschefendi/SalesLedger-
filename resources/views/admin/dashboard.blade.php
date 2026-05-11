<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100">

    <div class="max-w-6xl mx-auto py-10 px-4">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Dashboard Admin: Rekap Faktur</h1>
            <a href="/" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700">+ Scan Faktur Baru</a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white shadow-md rounded-xl overflow-hidden">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr>
                        <th class="px-5 py-4 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                        <th class="px-5 py-4 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Toko</th>
                        <th class="px-5 py-4 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal Nota</th>
                        <th class="px-5 py-4 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Total Tagihan</th>
                        <th class="px-5 py-4 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nominal Tagihan</th>
                        <th class="px-5 py-4 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Waktu Upload</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($fakturs as $index => $faktur)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-5 py-4 border-b border-gray-200 text-sm">
                                {{ $index + 1 }}
                            </td>
                            <td class="px-5 py-4 border-b border-gray-200 text-sm font-medium text-gray-900">
                                {{ $faktur->nama_toko }}
                            </td>
                            <td class="px-5 py-4 border-b border-gray-200 text-sm text-gray-600">
                                {{ $faktur->tanggal_nota ?? '-' }}
                            </td>
                            <td class="px-5 py-4 border-b border-gray-200 text-sm font-bold text-blue-600">
                                Rp {{ number_format($faktur->total_tagihan, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-4 border-b border-gray-200 text-sm font-bold text-blue-600">
                                Rp {{ number_format($faktur->nominal_tagihan, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-4 border-b border-gray-200 text-sm text-gray-500">
                                {{ $faktur->created_at->diffForHumans() }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-8 border-b border-gray-200 text-center text-gray-500 text-sm">
                                Belum ada data faktur yang disetor oleh Sales.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>