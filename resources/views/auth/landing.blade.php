<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Welcome - Sales Ledger</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-100 antialiased">

    <div class="min-h-screen flex flex-col max-w-md mx-auto bg-gradient-to-b from-[#70B5FF] via-[#C2E0FF] to-[#F8F9FE] relative shadow-2xl overflow-hidden px-8">
        
        <div class="flex-grow flex flex-col items-center justify-center pt-20">
            <h1 class="text-[36px] font-bold text-white tracking-wide drop-shadow-sm">SalesLedger</h1>
        </div>

        <div class="pb-20 space-y-6">
            <div class="space-y-3">
                <button onclick="goToLogin()" class="w-full bg-[#0F47A1] hover:bg-blue-900 text-white font-semibold py-4 rounded-xl shadow-md transition duration-200 text-sm">
                    Sign In
                </button>
                <button onclick="goToSignUp()" class="w-full bg-white border border-[#0F47A1] text-[#0F47A1] hover:bg-gray-50 font-semibold py-4 rounded-xl shadow-sm transition duration-200 text-sm">
                    Create Account
                </button>
            </div>
        </div>
    </div>

    <script>
        let selectedRole = 'admin';

        function setRole(role) {
            selectedRole = role;
            const btnAdmin = document.getElementById('btn-admin');
            const btnSales = document.getElementById('btn-sales');

            if (role === 'admin') {
                btnAdmin.className = "flex-1 py-2 text-xs font-bold rounded-full transition duration-200 bg-[#0F47A1] text-white shadow-sm";
                btnSales.className = "flex-1 py-2 text-xs font-bold text-gray-500 rounded-full transition duration-200 hover:text-gray-700";
            } else {
                btnSales.className = "flex-1 py-2 text-xs font-bold rounded-full transition duration-200 bg-[#0F47A1] text-white shadow-sm";
                btnAdmin.className = "flex-1 py-2 text-xs font-bold text-gray-500 rounded-full transition duration-200 hover:text-gray-700";
            }
        }

        function goToLogin() {
            window.location.href = `/login?role=${selectedRole}`;
        }

        function goToSignUp() {
            window.location.href = `/signup?role=${selectedRole}`;
        }
    </script>
</body>
</html>