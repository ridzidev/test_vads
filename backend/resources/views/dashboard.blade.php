@extends('app')

@section('title', 'Dashboard - VADS System')
@section('page-title', 'Dashboard Customer')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Card 1: Total Chats -->
    <div class="bg-gradient-to-br from-blue-50 to-blue-200 rounded-xl shadow-lg p-6 border-l-4 border-blue-600 hover:scale-105 hover:shadow-2xl transition-all duration-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Total Chat</p>
                <p class="text-3xl font-bold text-gray-800">0</p>
            </div>
            <i class="fas fa-comments text-4xl text-blue-200"></i>
        </div>
    </div>

    <!-- Card 2: Waiting -->
    <div class="bg-gradient-to-br from-yellow-50 to-yellow-200 rounded-xl shadow-lg p-6 border-l-4 border-yellow-600 hover:scale-105 hover:shadow-2xl transition-all duration-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Sedang Menunggu</p>
                <p class="text-3xl font-bold text-gray-800">0</p>
            </div>
            <i class="fas fa-hourglass-half text-4xl text-yellow-200"></i>
        </div>
    </div>

    <!-- Card 3: Completed -->
    <div class="bg-gradient-to-br from-green-50 to-green-200 rounded-xl shadow-lg p-6 border-l-4 border-green-600 hover:scale-105 hover:shadow-2xl transition-all duration-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Selesai</p>
                <p class="text-3xl font-bold text-gray-800">0</p>
            </div>
            <i class="fas fa-check-circle text-4xl text-green-200"></i>
        </div>
    </div>

    <!-- Card 4: Rating -->
    <div class="bg-gradient-to-br from-purple-50 to-purple-200 rounded-xl shadow-lg p-6 border-l-4 border-purple-600 hover:scale-105 hover:shadow-2xl transition-all duration-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Rating Rata-rata</p>
                <p class="text-3xl font-bold text-gray-800">-</p>
            </div>
            <i class="fas fa-star text-4xl text-purple-200"></i>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left Column -->
    <div class="lg:col-span-2">
        <!-- Recent Activities -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-6 border border-blue-100">
            <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-history text-blue-600"></i> Riwayat Aktivitas
            </h2>
            <div class="space-y-4">
                <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-lg">
                    <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                        <i class="fas fa-check text-blue-600"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold text-gray-800">Akun Berhasil Dibuat</p>
                        <p class="text-gray-500 text-sm">Akun Anda telah terdaftar di sistem</p>
                    </div>
                    <span class="text-xs text-gray-500">Baru saja</span>
                </div>
                <div class="text-center py-6 text-gray-500">
                    <i class="fas fa-inbox text-4xl mb-2 opacity-20"></i>
                    <p>Belum ada aktivitas lainnya</p>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-lg p-6 border border-yellow-100">
            <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-lightning-bolt text-yellow-600"></i> Aksi Cepat
            </h2>
            <div class="grid grid-cols-1 gap-4">
                <a href="/chat-room" class="bg-gradient-to-br from-green-500 to-green-600 text-white p-4 rounded-lg hover:shadow-lg transition transform hover:scale-105">
                    <i class="fas fa-comments text-2xl mb-2 block"></i>
                    <p class="font-semibold">Chat</p>
                    <p class="text-xs opacity-90">Mulai chat baru</p>
                </a>
            </div>
        </div>
    </div>

    <!-- Right Column -->
    <div>
        <!-- Profile Info -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-6 border border-blue-100">
            <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-user-circle text-blue-600"></i> Profil Saya
            </h2>
            <div class="text-center">
                <div class="w-20 h-20 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white text-3xl font-bold mx-auto mb-4">
                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                </div>
                <p class="font-semibold text-gray-800">{{ Auth::user()->name ?? 'Customer' }}</p>
                <p class="text-gray-500 text-sm">{{ Auth::user()->email }}</p>
                <p class="text-gray-500 text-xs mt-2">Customer ID: #{{ Auth::user()->id }}</p>
            </div>
            <hr class="my-4">
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">Status:</span>
                    <span class="font-semibold text-green-600">Aktif</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Member Sejak:</span>
                    <span class="font-semibold">{{ Auth::user()->created_at->format('d M Y') }}</span>
                </div>
            </div>
        </div>

        <!-- Help Box -->
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl shadow-lg p-6 border border-blue-200">
            <h3 class="text-lg font-bold text-blue-900 mb-3 flex items-center gap-2">
                <i class="fas fa-question-circle"></i> Butuh Bantuan?
            </h3>
            <p class="text-blue-800 text-sm mb-4">
                Kami siap membantu Anda 24/7. Gunakan fitur chat atau antrian untuk mendapatkan dukungan.
            </p>
            <a href="/chat-room" class="inline-block bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-sm font-semibold">
                <i class="fas fa-headset mr-2"></i> Hubungi Kami
            </a>
        </div>
    </div>
</div>
@endsection
