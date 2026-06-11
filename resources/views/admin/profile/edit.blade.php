<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Edit Profile - Sales Ledger</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased">

    <form action="{{ route('admin.profile.update') }}" method="POST" class="min-h-screen flex flex-col max-w-md mx-auto bg-white relative shadow-2xl overflow-hidden pb-10">
        @csrf
        
        <div class="px-6 pt-12 pb-4 flex items-center bg-white border-b border-gray-100 relative z-10">
            <button type="button" onclick="window.history.back()" class="text-[#0F47A1] hover:text-blue-900 transition p-2 -ml-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path></svg>
            </button>
            <h1 class="text-[18px] font-bold text-gray-900 flex-grow text-center">Edit</h1>
            <button type="submit" class="text-[#0F47A1] hover:text-blue-900 transition p-2 -mr-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
            </button>
        </div>

        <div class="px-5 pt-8 space-y-6 flex-grow overflow-y-auto">
            
            <div class="flex justify-center relative">
                <div class="relative w-28 h-28">
                    <div class="w-full h-full bg-blue-50 border border-blue-100 rounded-full flex items-center justify-center text-[#0F47A1] shadow-inner">
                        <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"></path></svg>
                    </div>
                    <div class="absolute bottom-0 right-1 w-7 h-7 bg-white border border-gray-200 rounded-full flex items-center justify-center text-[#0F47A1] shadow-sm cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                    </div>
                </div>
            </div>

            @if ($errors->any())
                <div class="p-3.5 bg-red-50 border border-red-200 rounded-2xl text-red-500 text-xs font-medium space-y-1">
                    @foreach ($errors->all() as $error) <p>• {{ $error }}</p> @endforeach
                </div>
            @endif

            <div class="space-y-4">
                <div>
                    <label class="block text-[12px] font-bold text-gray-900 mb-1.5 pl-1">Nama</label>
                    <input type="text" name="full_name" value="{{ old('full_name', $user->full_name) }}" 
                           class="w-full border border-gray-200 rounded-xl px-4 py-3.5 text-[14px] font-medium focus:ring-2 focus:ring-[#0F47A1] focus:outline-none transition">
                </div>
                <div>
                    <label class="block text-[12px] font-bold text-gray-900 mb-1.5 pl-1">No. tlp</label>
                    <input type="text" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}" placeholder="Contoh: 081234567890"
                           class="w-full border border-gray-200 rounded-xl px-4 py-3.5 text-[14px] font-medium focus:ring-2 focus:ring-[#0F47A1] focus:outline-none transition">
                </div>
            </div>

        </div>
    </form>

</body>
</html>