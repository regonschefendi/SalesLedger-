<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Edit Toko - Sales Ledger</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
    <style>
        body { font-family: 'Inter', sans-serif; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-white text-gray-800 antialiased">

    <div class="min-h-screen flex flex-col max-w-md mx-auto bg-white relative pb-24 overflow-hidden">
        
        <!-- Header -->
        <div class="px-6 pt-12 pb-4 flex items-center bg-white z-10 relative">
            <a href="{{ route('admin.toko.show', $toko->id) }}" class="text-[#0F47A1] p-2 -ml-2 hover:text-blue-900 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path></svg>
            </a>
            <h1 class="text-[18px] font-bold text-gray-900 flex-grow text-center pr-8">Edit Toko</h1>
        </div>

        <!-- Content/Form -->
        <div class="flex-grow overflow-y-auto no-scrollbar">
            
            <form action="{{ route('admin.toko.update', $toko->id) }}" method="POST" class="space-y-5 px-6 pt-2 pb-10">
                @csrf
                @method('PUT')

                <!-- Field Nama Toko -->
                <div>
                    <label class="block text-[14px] font-bold text-gray-900 mb-2">Nama Toko</label>
                    <input type="text" name="nama_toko" value="{{ old('nama_toko', $toko->nama_toko) }}" required
                           class="w-full px-4 py-3.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:border-[#2563EB] text-[14px] text-gray-900 placeholder-gray-400 transition-colors"
                           placeholder="Masukkan nama toko">
                    @error('nama_toko') <span class="text-red-500 text-[11px] mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Field Nomor Telepon -->
                <div>
                    <label class="block text-[14px] font-bold text-gray-900 mb-2">Nomor Telepon</label>
                    <input type="tel" name="no_telp" value="{{ old('no_telp', $toko->no_telp) }}" 
                           class="w-full px-4 py-3.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:border-[#2563EB] text-[14px] text-gray-900 placeholder-gray-400 transition-colors"
                           placeholder="Masukkan nomor telepon">
                    @error('no_') <span class="text-red-500 text-[11px] mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Field Alamat -->
                <div>
                    <label class="block text-[14px] font-bold text-gray-900 mb-2">Alamat</label>
                    <input type="text" name="alamat" value="{{ old('alamat', $toko->alamat) }}" 
                           class="w-full px-4 py-3.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:border-[#2563EB] text-[14px] text-gray-900 placeholder-gray-400 transition-colors"
                           placeholder="Masukkan alamat toko">
                    @error('alamat') <span class="text-red-500 text-[11px] mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Field Provinsi -->
                <div>
                    <label class="block text-[14px] font-bold text-gray-900 mb-2">Provinsi</label>
                    <input type="text" name="provinsi" value="{{ old('provinsi', $toko->provinsi ?? '') }}" 
                           class="w-full px-4 py-3.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:border-[#2563EB] text-[14px] text-gray-900 placeholder-gray-400 transition-colors"
                           placeholder="Masukkan nama provinsi">
                    @error('provinsi') <span class="text-red-500 text-[11px] mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Field Kabupaten / Kota -->
                <div>
                    <label class="block text-[14px] font-bold text-gray-900 mb-2">Kabupaten / Kota</label>
                    <input type="text" name="kota" value="{{ old('kota', $toko->kota ?? '') }}" 
                           class="w-full px-4 py-3.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:border-[#2563EB] text-[14px] text-gray-900 placeholder-gray-400 transition-colors"
                           placeholder="Masukkan nama kabupaten / kota">
                    @error('kota') <span class="text-red-500 text-[11px] mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Field Kode Pos -->
                <div>
                    <label class="block text-[14px] font-bold text-gray-900 mb-2">Kode Pos</label>
                    <input type="text" name="kode_pos" value="{{ old('kode_pos', $toko->kode_pos ?? '') }}" 
                           class="w-full px-4 py-3.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:border-[#2563EB] text-[14px] text-gray-900 placeholder-gray-400 transition-colors"
                           placeholder="Masukkan kode pos">
                    @error('kode_pos') <span class="text-red-500 text-[11px] mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Sticky Submit Button -->
                <div class="fixed bottom-0 max-w-md mx-auto inset-x-0 bg-white border-t border-gray-100 p-5 z-20">
                    <button type="submit" class="w-full bg-[#2563EB] hover:bg-blue-800 text-white font-bold py-3.5 rounded-xl shadow-md transition duration-200 text-[14px]">
                        Simpan Perubahan
                    </button>
                </div>
            </form>

        </div>
    </div>
</body>
</html>