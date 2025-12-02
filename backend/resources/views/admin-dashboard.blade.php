@extends('app')

@section('title', 'Admin Dashboard - Real-Time Web Chat Application')
@section('page-title', 'Admin Dashboard')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Card 1: Total Users -->
    <div class="bg-gradient-to-br from-blue-50 to-blue-200 rounded-xl shadow-lg p-6 border-l-4 border-blue-600 hover:scale-105 hover:shadow-2xl transition-all duration-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Total Pengguna</p>
                <p class="text-3xl font-bold text-gray-800">{{ $totalUsers ?? 0 }}</p>
            </div>
            <i class="fas fa-users text-4xl text-blue-200"></i>
        </div>
    </div>

    <!-- Card 2: Active Sessions -->
    <div class="bg-gradient-to-br from-green-50 to-green-200 rounded-xl shadow-lg p-6 border-l-4 border-green-600 hover:scale-105 hover:shadow-2xl transition-all duration-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Chat Aktif</p>
                <p class="text-3xl font-bold text-gray-800">0</p>
            </div>
            <i class="fas fa-comments text-4xl text-green-200"></i>
        </div>
    </div>

    <!-- Card 3: Queue Count -->
    <div class="bg-gradient-to-br from-yellow-50 to-yellow-200 rounded-xl shadow-lg p-6 border-l-4 border-yellow-600 hover:scale-105 hover:shadow-2xl transition-all duration-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Antrian Menunggu</p>
                <p class="text-3xl font-bold text-gray-800">0</p>
            </div>
            <i class="fas fa-hourglass-half text-4xl text-yellow-200"></i>
        </div>
    </div>

    <!-- Card 4: Completed -->
    <div class="bg-gradient-to-br from-purple-50 to-purple-200 rounded-xl shadow-lg p-6 border-l-4 border-purple-600 hover:scale-105 hover:shadow-2xl transition-all duration-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Chat Selesai (Hari Ini)</p>
                <p class="text-3xl font-bold text-gray-800">0</p>
            </div>
            <i class="fas fa-check-circle text-4xl text-purple-200"></i>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left Column -->
    <div class="lg:col-span-2">
        <!-- Recent Users Table -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-6 border border-blue-100">
            <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-users text-blue-600"></i> Pengguna Terbaru
            </h2>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-100 border-b">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Nama</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Email</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Terdaftar</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentUsers ?? [] as $user)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-800">{{ $user->name }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $user->email }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $user->created_at->format('d M Y') }}</td>
                                <td class="px-4 py-3 text-sm">
                                    @if($user->is_active)
                                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold">Aktif</span>
                                    @else
                                        <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-semibold">Nonaktif</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                    <i class="fas fa-inbox text-3xl mb-2 block opacity-30"></i>
                                    Belum ada pengguna
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Activity Log -->
        <div class="bg-white rounded-xl shadow-lg p-6 border border-yellow-100">
            <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-history text-blue-600"></i> Log Aktivitas
            </h2>
            <div class="space-y-4">
                <div class="flex items-center gap-4 p-4 bg-blue-50 rounded-lg border-l-4 border-blue-600">
                    <div class="w-10 h-10 rounded-full bg-blue-200 flex items-center justify-center">
                        <i class="fas fa-user-plus text-blue-600 text-sm"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold text-gray-800">Admin berhasil login</p>
                        <p class="text-gray-500 text-sm">Sistem admin dashboard</p>
                    </div>
                    <span class="text-xs text-gray-500">Baru saja</span>
                </div>
                <div class="text-center py-6 text-gray-500">
                    <i class="fas fa-inbox text-4xl mb-2 opacity-20"></i>
                    <p>Tidak ada log lainnya</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column -->
    <div>
        <!-- Admin Info -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-6 border border-purple-100">
            <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-user-shield text-purple-600"></i> Info Admin
            </h2>
            <div class="text-center">
                <div class="w-20 h-20 rounded-full bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center text-white text-3xl font-bold mx-auto mb-4">
                    <i class="fas fa-crown"></i>
                </div>
                <p class="font-semibold text-gray-800">{{ Auth::user()->name ?? 'Administrator' }}</p>
                <p class="text-gray-500 text-sm">{{ Auth::user()->email }}</p>
                <p class="text-gray-500 text-xs mt-2">Administrator Account</p>
            </div>
            <hr class="my-4">
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">Role:</span>
                    <span class="font-semibold text-purple-600">Admin</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Status:</span>
                    <span class="font-semibold text-green-600">Aktif</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Login Terakhir:</span>
                    <span class="font-semibold">
                        {{ Auth::user()->last_login_at ? Auth::user()->last_login_at->format('d M Y H:i') : 'Baru' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- System Info -->
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl shadow-lg p-6 border border-purple-200 mb-6">
            <h3 class="text-lg font-bold text-purple-900 mb-3 flex items-center gap-2">
                <i class="fas fa-server"></i> Sistem
            </h3>
            <div class="space-y-2 text-sm text-purple-800">
                <p><strong>Version:</strong> 1.0.0</p>
                <p><strong>Framework:</strong> Laravel 11</p>
                <p><strong>PHP:</strong> {{ phpversion() }}</p>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-lg p-6 border border-blue-100">
            <h3 class="text-lg font-bold text-gray-800 mb-3 flex items-center gap-2">
                <i class="fas fa-lightning-bolt text-yellow-600"></i> Aksi Cepat
            </h3>
            <div class="space-y-2">
                <a href="/master-customer" class="block bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-sm font-semibold text-center">
                    <i class="fas fa-users mr-2"></i> Master Customer
                </a>
                <a href="/service-desk-dashboard" class="block bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition text-sm font-semibold text-center">
                    <i class="fas fa-headset mr-2"></i> Service Desk
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
