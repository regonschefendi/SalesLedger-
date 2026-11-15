@once
<script>
    window.ApiClient = {
        async read(response) {
            const contentType = (response.headers.get('content-type') || '').toLowerCase();

            if (!contentType.includes('application/json')) {
                return null;
            }

            try {
                return await response.json();
            } catch (error) {
                return null;
            }
        },
        message(status) {
            if (status === 419) {
                return 'Sesi halaman sudah kedaluwarsa. Muat ulang halaman ini lalu coba lagi.';
            }
            if (status === 429) {
                return 'Terlalu banyak percobaan. Tunggu sekitar 1 menit lalu coba lagi.';
            }
            if (status === 503 || status === 504) {
                return 'Server sedang sibuk atau tidak merespons. Coba lagi beberapa saat lagi.';
            }
            if (status >= 500) {
                return 'Terjadi kesalahan pada server. Coba lagi beberapa saat lagi.';
            }

            return 'Permintaan gagal diproses (kode ' + status + '). Coba lagi.';
        }
    };
</script>
@endonce
