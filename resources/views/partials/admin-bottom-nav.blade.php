<div class="fixed bottom-0 max-w-md mx-auto inset-x-0 w-full bg-white border-t border-gray-100 h-[72px] flex justify-around items-center px-6 z-40 rounded-t-[24px] shadow-[0_-4px_20px_rgba(0,0,0,0.04)]">
    
    <a href="{{ route('admin.dashboard') }}" class="flex flex-col items-center w-16 transition {{ request()->routeIs('admin.dashboard') ? 'text-[#2563EB]' : 'text-gray-400 hover:text-gray-700' }}">
        <svg class="w-6 h-6 mb-1" fill="currentColor" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"></path></svg>
        <span class="text-[10px] font-bold">Home</span>
    </a>
    
    <a href="{{ route('admin.pembukuan.index') }}" class="flex flex-col items-center w-16 transition {{ request()->routeIs('admin.pembukuan.*') ? 'text-[#2563EB]' : 'text-gray-400 hover:text-gray-700' }}">
        <svg class="w-6 h-6 mb-1" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2h12c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"></path></svg>
        <span class="text-[10px] font-bold">Pembukuan</span>
    </a>
    
    <a href="#" class="flex flex-col items-center w-16 transition {{ request()->routeIs('admin.sales.*') ? 'text-[#2563EB]' : 'text-gray-400 hover:text-gray-700' }}">
        <svg class="w-6 h-6 mb-1" fill="currentColor" viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"></path></svg>
        <span class="text-[10px] font-bold">Sales</span>
    </a>
    
    <a href="{{ route('admin.toko.index') }}" class="flex flex-col items-center w-16 transition {{ request()->routeIs('admin.toko.*') ? 'text-[#2563EB]' : 'text-gray-400 hover:text-gray-700' }}">
        <svg class="w-6 h-6 mb-1" fill="currentColor" viewBox="0 0 24 24"><path d="M20 4H4v2h16V4zm1 10v-2l-1-5H4l-1 5v2h1v6h10v-6h4v6h2v-6h1zm-9 4H6v-4h6v4z"></path></svg>
        <span class="text-[10px] font-bold">Toko</span>
    </a>
</div>