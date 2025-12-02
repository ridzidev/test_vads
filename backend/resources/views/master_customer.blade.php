<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master Customer - VADS Test</title>
    <!-- 1. Tailwind CSS (Sesuai Soal) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- 2. Jquery (Sesuai Soal) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">

    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-4 text-gray-800">
            Homepage workspace Live chat SD (Service desk) menu master customer
        </h1>

        <div class="bg-white shadow-md rounded my-6 overflow-x-auto">
            <table class="min-w-full bg-white grid-cols-1">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="w-1/6 text-left py-3 px-4 uppercase font-semibold text-sm">Name</th>
                        <th class="w-1/6 text-left py-3 px-4 uppercase font-semibold text-sm">Email</th>
                        <th class="w-1/6 text-left py-3 px-4 uppercase font-semibold text-sm">Login UUID</th>
                        <th class="w-1/6 text-left py-3 px-4 uppercase font-semibold text-sm">Username</th>
                        <th class="w-1/6 text-left py-3 px-4 uppercase font-semibold text-sm">Password</th>
                        <th class="w-1/6 text-left py-3 px-4 uppercase font-semibold text-sm">Phone</th>
                        <th class="w-1/6 text-left py-3 px-4 uppercase font-semibold text-sm">Cell</th>
                        <th class="w-1/6 text-left py-3 px-4 uppercase font-semibold text-sm">Picture</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700" id="customer-table-body">
                    <!-- Data will be inserted here via Jquery -->
                    <tr>
                        <td colspan="8" class="text-center py-4">Loading data...</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="mt-4 text-sm text-gray-600">
            * Data diambil dari randomuser.me dan dimanipulasi (flattening) sebelum ditampilkan.
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // URL Endpoint External (Sesuai Soal)
            const apiUrl = "https://randomuser.me/api?results=10&page=1";

            $.ajax({
                url: apiUrl,
                dataType: 'json',
                success: function(response) {
                    const rawData = response.results;
                    let tableContent = "";

                    // --- LOGIC MANIPULASI JSON (SOAL 2) ---
                    // Mengubah struktur Nested menjadi Flat sesuai contoh di PDF Hal 7
                    
                    const manipulatedData = rawData.map(user => {
                        return {
                            // Menggabungkan Title + First + Last
                            name: `${user.name.title} ${user.name.first} ${user.name.last}`,
                            email: user.email,
                            // Flattening Login Object
                            login_uuid: user.login.uuid,
                            login_username: user.login.username,
                            login_password: user.login.password,
                            phone: user.phone,
                            cell: user.cell,
                            // Mengambil URL gambar medium
                            picture: user.picture.medium
                        };
                    });

                    // --- RENDER KE TABEL ---
                    if (manipulatedData.length > 0) {
                        $.each(manipulatedData, function(index, user) {
                            tableContent += `
                                <tr class="${index % 2 === 0 ? 'bg-gray-50' : 'bg-white'} hover:bg-gray-100 border-b">
                                    <td class="text-left py-3 px-4">${user.name}</td>
                                    <td class="text-left py-3 px-4 text-blue-600">${user.email}</td>
                                    <td class="text-left py-3 px-4 text-xs font-mono">${user.login_uuid}</td>
                                    <td class="text-left py-3 px-4">${user.login_username}</td>
                                    <td class="text-left py-3 px-4 font-mono">*****</td> 
                                    <td class="text-left py-3 px-4">${user.phone}</td>
                                    <td class="text-left py-3 px-4">${user.cell}</td>
                                    <td class="text-left py-3 px-4">
                                        <img src="${user.picture}" alt="User" class="h-10 w-10 rounded-full border border-gray-300">
                                    </td>
                                </tr>
                            `;
                        });
                        $('#customer-table-body').html(tableContent);
                        
                        // Debugging: Menampilkan hasil manipulasi di Console Browser
                        console.log("Data Asli:", rawData);
                        console.log("Data Sesudah Manipulasi:", manipulatedData);
                    } else {
                        $('#customer-table-body').html('<tr><td colspan="8" class="text-center py-4">No data found</td></tr>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching data:", error);
                    $('#customer-table-body').html('<tr><td colspan="8" class="text-center py-4 text-red-500">Gagal mengambil data dari server.</td></tr>');
                }
            });
        });
    </script>
</body>
</html>