@extends('app')

@section('title', 'Master Customer - VADS')
@section('page-title', 'Master Customer')

@section('content')
<div class="max-w-7xl mx-auto p-6">
    <!-- Header Section -->
    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl shadow-lg p-6 mb-6 border border-blue-200">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Daftar Pelanggan Potensial</h2>
        <p class="text-gray-600 mb-4">Data diambil dari sumber publik untuk referensi database pelanggan</p>

        <div class="flex gap-4 flex-wrap">
            <button 
                id="loadDataBtn"
                class="bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 text-white px-6 py-2 rounded-xl font-semibold transition flex items-center gap-2 shadow-md hover:scale-105"
            >
                <i class="fas fa-download mr-2"></i>Load Data
            </button>
            <button 
                id="refreshBtn"
                class="bg-gradient-to-r from-green-600 to-green-500 hover:from-green-700 hover:to-green-600 text-white px-6 py-2 rounded-xl font-semibold transition flex items-center gap-2 shadow-md hover:scale-105"
            >
                <i class="fas fa-redo mr-2"></i>Refresh
            </button>
            <button 
                id="exportBtn"
                class="bg-gradient-to-r from-purple-600 to-purple-500 hover:from-purple-700 hover:to-purple-600 text-white px-6 py-2 rounded-xl font-semibold transition flex items-center gap-2 shadow-md hover:scale-105"
            >
                <i class="fas fa-download mr-2"></i>Export CSV
            </button>
        </div>
    </div>

    <!-- Loading Spinner -->
    <div id="loadingSpinner" class="hidden text-center py-12">
        <i class="fas fa-spinner text-blue-600 text-5xl animate-spin"></i>
        <p class="text-gray-600 mt-4">Mengambil data pelanggan...</p>
    </div>

    <!-- Data Table -->
    <div id="tableContainer" class="hidden bg-white rounded-xl shadow-lg overflow-hidden border border-blue-100">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100 border-b-2 border-gray-300">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">No</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Photo</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Nama Lengkap</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Email</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Username</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Login UUID</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Telepon</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Ponsel</th>
                        <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <!-- Data rows will be inserted here -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Empty State -->
    <div id="emptyState" class="bg-white rounded-xl shadow-lg p-12 text-center border border-blue-100">
        <i class="fas fa-inbox text-gray-400 text-5xl mb-4"></i>
        <p class="text-gray-500 text-lg">Belum ada data. Klik tombol "Load Data" untuk mengambil data pelanggan.</p>
    </div>
</div>

<!-- Detail Modal -->
<div id="detailModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-2xl p-6 max-w-md w-full max-h-96 overflow-y-auto border border-blue-200">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-800">Detail Pelanggan</h2>
            <button id="closeModalBtn" class="text-gray-500 hover:text-blue-700 text-2xl font-bold">×</button>
        </div>
        <div id="detailContent" class="space-y-3">
            <!-- Details will be inserted here -->
        </div>
    </div>
</div>

@endsection

@section('extra-js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    let masterCustomerData = [];

    // Flatten nested JSON data
    function flattenCustomerData(rawData) {
        return rawData.map((user, index) => {
            return {
                no: index + 1,
                name: `${user.name.title} ${user.name.first} ${user.name.last}`,
                email: user.email,
                login_uuid: user.login.uuid,
                login_username: user.login.username,
                login_password: user.login.password,
                phone: user.phone,
                cell: user.cell,
                picture: user.picture.medium,
                picture_large: user.picture.large,
                // Additional data
                gender: user.gender,
                nat: user.nat,
                location_city: user.location.city,
                location_country: user.location.country,
                dob_date: user.dob.date,
                dob_age: user.dob.age
            };
        });
    }

    // Load data from randomuser.me API
    $('#loadDataBtn').click(function() {
        $('#loadingSpinner').removeClass('hidden');
        $('#tableContainer').addClass('hidden');
        $('#emptyState').addClass('hidden');

        $.ajax({
            url: 'https://randomuser.me/api?results=10&page=1',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                masterCustomerData = flattenCustomerData(response.results);
                renderTable();
                $('#loadingSpinner').addClass('hidden');
                $('#tableContainer').removeClass('hidden');
            },
            error: function(error) {
                console.error('Error fetching data:', error);
                $('#loadingSpinner').addClass('hidden');
                $('#emptyState').removeClass('hidden').find('p').text('Gagal mengambil data. Silakan coba lagi.');
            }
        });
    });

    // Refresh data (with different page)
    $('#refreshBtn').click(function() {
        const page = Math.floor(Math.random() * 10) + 1;
        $('#loadingSpinner').removeClass('hidden');
        $('#tableContainer').addClass('hidden');

        $.ajax({
            url: `https://randomuser.me/api?results=10&page=${page}`,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                masterCustomerData = flattenCustomerData(response.results);
                renderTable();
                $('#loadingSpinner').addClass('hidden');
                $('#tableContainer').removeClass('hidden');
            },
            error: function() {
                $('#loadingSpinner').addClass('hidden');
                $('#emptyState').removeClass('hidden');
            }
        });
    });

    // Render table
    function renderTable() {
        $('#tableBody').empty();

        masterCustomerData.forEach((customer, index) => {
            const row = `
                <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                    <td class="px-6 py-4 text-sm text-gray-700 font-semibold">${customer.no}</td>
                    <td class="px-6 py-4 text-sm">
                        <img src="${customer.picture}" alt="Photo" class="w-10 h-10 rounded-full">
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-800 font-semibold">${customer.name}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">${customer.email}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">${customer.login_username}</td>
                    <td class="px-6 py-4 text-sm text-gray-500 font-mono text-xs">${customer.login_uuid}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">${customer.phone}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">${customer.cell}</td>
                    <td class="px-6 py-4 text-sm text-center">
                        <button data-idx="${index}" class="view-detail px-3 py-1 bg-blue-600 text-white rounded text-sm">Detail</button>
                    </td>
                </tr>
            `;
            $('#tableBody').append(row);
        });

        // Attach detail handlers
        $('.view-detail').on('click', function() {
            const idx = $(this).data('idx');
            const c = masterCustomerData[idx];
            $('#detailContent').html(`
                <div class="flex items-center gap-4">
                    <img src="${c.picture_large}" class="w-20 h-20 rounded-full" />
                    <div>
                        <p class="font-bold text-lg">${c.name}</p>
                        <p class="text-sm text-gray-600">${c.email}</p>
                        <p class="text-sm text-gray-500">${c.location_city}, ${c.location_country}</p>
                    </div>
                </div>
                <div class="mt-4 text-sm text-gray-700">
                    <p><strong>Phone:</strong> ${c.phone}</p>
                    <p><strong>Cell:</strong> ${c.cell}</p>
                    <p><strong>Username:</strong> ${c.login_username}</p>
                    <p><strong>Age:</strong> ${c.dob_age}</p>
                </div>
            `);
            $('#detailModal').removeClass('hidden');
        });

        $('#closeModalBtn').on('click', function() {
            $('#detailModal').addClass('hidden');
        });
    }
</script>
@endsection
