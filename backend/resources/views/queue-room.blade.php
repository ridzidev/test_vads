@extends('app')

@section('title', 'Antrian - Real-Time Web Chat Application')
@section('page-title', 'Ruang Antrian')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-md p-8 text-center">
            <!-- Header -->
            <div class="mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gradient-to-br from-indigo-500 to-blue-600 mb-4">
                    <i class="fas fa-clock text-white text-2xl"></i>
                </div>
                <h1 class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-blue-600 bg-clip-text text-transparent">Ruang Tunggu</h1>
                <p class="text-gray-600 mt-2 font-medium">Menunggu agen siap melayani</p>
            </div>

            <!-- Queue Status -->
            <div id="queueStatus" class="bg-gradient-to-br from-indigo-50 to-blue-50 border-2 border-indigo-200 rounded-xl p-7 mb-6 shadow-lg">
                <!-- Spinner -->
                <div class="mb-6">
                    <div class="inline-block">
                        <i class="fas fa-spinner text-indigo-600 text-5xl animate-spin"></i>
                    </div>
                </div>
                
                <!-- Greeting -->
                <p class="text-gray-800 font-bold text-lg mb-3">
                    Halo <span id="customerName" class="bg-gradient-to-r from-indigo-600 to-blue-600 bg-clip-text text-transparent"></span>! 👋
                </p>
                <p class="text-gray-600 mb-6 leading-relaxed">
                    Kami sedang mencari agen yang tersedia untuk melayani Anda dengan cepat.
                </p>

        <!-- Timer Card -->
        <div class="bg-white rounded-lg p-4 mb-6 shadow-md border-l-4 border-blue-600">
            <p class="text-sm text-gray-600 mb-2 font-medium">Sisa Waktu Tunggu</p>
            <div class="flex justify-center items-center gap-2">
                <i class="fas fa-hourglass-end text-blue-600 text-lg"></i>
                <span id="waitTimer" class="text-4xl font-bold text-blue-600 font-mono">3:00</span>
            </div>
            <p class="text-xs text-gray-500 mt-2">Waktu tunggu maksimal: 3 menit</p>
        </div>

        <!-- Status Messages -->
        <div id="statusMessage" class="text-sm text-gray-600 mb-6 px-2">
            <p class="animate-pulse"><i class="fas fa-spinner animate-spin mr-2 text-blue-600"></i>Jangan tutup halaman ini, kami akan segera melayani...</p>
        </div>

        <!-- Cancel Button -->
        <form action="/logout" method="POST" class="mb-4">
            @csrf
            <button type="submit" class="w-full bg-red-600 text-white font-semibold py-3 rounded-lg hover:bg-red-700 transition">
                <i class="fas fa-times mr-2"></i> Batalkan Antrian
            </button>
        </form>
    </div>
</div>

@endsection
