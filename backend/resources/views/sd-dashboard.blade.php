@extends('app')

@section('title', 'Service Desk Dashboard - VADS System')
@section('page-title', 'Service Desk Dashboard')

@section('content')
<div class="flex h-screen bg-gray-100">
    <!-- Sidebar -->
    <div class="w-80 bg-white shadow-lg flex flex-col">
        <!-- Agent Info -->
        <div class="p-4 bg-gradient-to-r from-indigo-600 to-blue-600 text-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                    <i class="fas fa-user text-xl"></i>
                </div>
                <div>
                    <p class="font-semibold" id="agentName">Agent</p>
                    <p class="text-xs text-indigo-100">Service Desk Agent</p>
                </div>
            </div>
        </div>

        <!-- Queue Count Section -->
        <div class="p-4 border-b-2 border-gray-200">
            <h3 class="font-bold text-gray-800 mb-3 flex items-center gap-2 text-sm">
                <span class="flex items-center justify-center w-6 h-6 bg-orange-100 text-orange-600 rounded-full">
                    <i class="fas fa-clock text-xs"></i>
                </span>
                Antrian Menunggu
                <span class="ml-auto bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full text-xs font-bold" id="queueCount">0</span>
            </h3>
            <div id="queueList" class="space-y-2 max-h-48 overflow-y-auto">
                <!-- Queue items will be inserted here -->
            </div>
        </div>

        <!-- Active Chat List Section -->
        <div class="p-4 border-b-2 border-gray-200">
            <h3 class="font-bold text-gray-800 mb-3 flex items-center gap-2 text-sm">
                <span class="flex items-center justify-center w-6 h-6 bg-green-100 text-green-600 rounded-full">
                    <i class="fas fa-comments text-xs"></i>
                </span>
                Chat Aktif
                <span class="ml-auto bg-green-100 text-green-700 px-2 py-0.5 rounded-full text-xs font-bold" id="activeChatCount">0</span>
            </h3>
            <div id="activeChatList" class="space-y-2 max-h-96 overflow-y-auto">
                <!-- Active chat items will be inserted here -->
            </div>
        </div>

        <!-- Logout Button -->
        <div class="p-4 border-t-2 border-gray-200 bg-gray-50 mt-auto">
            <button
                id="logoutBtn"
                class="w-full bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 text-white font-bold py-3 rounded-lg transition transform hover:scale-105 shadow-lg flex items-center justify-center gap-2"
            >
                <i class="fas fa-sign-out-alt"></i> Sign Out
            </button>
        </div>
    </div>

    <!-- Main Content: Chat Area -->
    <div class="flex-1 flex flex-col">
        <!-- Top Bar -->
        <div class="bg-gradient-to-r from-white via-indigo-50 to-white border-b-2 border-indigo-200 p-5 flex justify-between items-center shadow-sm">
            <div>
                <h1 id="selectedCustomerName" class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-blue-600 bg-clip-text text-transparent">
                    <i class="fas fa-comments mr-2"></i>Pilih pelanggan untuk chat
                </h1>
                <div id="selectedCustomerInfo" class="text-sm text-gray-600 mt-2 hidden space-x-3">
                    <span><i class="fas fa-envelope text-blue-600 mr-1"></i><span id="infoEmail"></span></span>
                    <span class="text-gray-400">|</span>
                    <span><i class="fas fa-phone text-green-600 mr-1"></i><span id="infoPhone"></span></span>
                </div>
            </div>
            <div class="flex gap-3">
                <button
                    id="viewDetailBtn"
                    class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-5 py-2 rounded-lg text-sm font-bold transition transform hover:scale-105 shadow-lg hidden flex items-center gap-2"
                >
                    <i class="fas fa-eye"></i>Detail
                </button>
                <button
                    id="endChatBtn"
                    class="bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 text-white px-5 py-2 rounded-lg text-sm font-bold transition transform hover:scale-105 shadow-lg hidden flex items-center gap-2"
                >
                    <i class="fas fa-times"></i>Akhiri
                </button>
            </div>
        </div>

        <!-- Chat Display Area -->
        <div id="chatContainer" class="chat-container flex-1 overflow-y-auto p-5 hidden">
            <!-- Messages will be inserted here -->
        </div>

        <!-- No Chat Selected -->
        <div id="noChatSelected" class="flex-1 flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100">
            <div class="text-center">
                <div class="inline-block bg-gradient-to-br from-indigo-100 to-blue-100 p-6 rounded-full mb-4">
                    <i class="fas fa-comments text-indigo-400 text-5xl"></i>
                </div>
                <p class="text-gray-600 text-lg font-semibold">Belum ada chat yang dipilih</p>
                <p class="text-gray-500 text-sm mt-2">Pilih pelanggan dari daftar untuk memulai percakapan</p>
            </div>
        </div>

        <!-- Input Area -->
        <div id="inputArea" class="chat-input-area p-5 hidden border-t-2 border-gray-200 bg-gradient-to-b from-white to-gray-50 shadow-lg">
            <div class="flex gap-3 items-end">
                <div class="flex-1">
                    <textarea
                        id="messageInput"
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none resize-none bg-gray-50 hover:bg-white transition"
                        rows="2"
                        placeholder="💬 Ketikkan pesan balasan Anda... (Shift+Enter untuk baris baru)"
                        maxlength="1000"
                    ></textarea>
                    <div class="flex justify-between items-center mt-2 px-1">
                        <small class="text-gray-500 text-xs">Tekan Shift+Enter untuk baris baru</small>
                        <small class="text-gray-400 text-xs"><span id="charCount">0</span>/1000</small>
                    </div>
                </div>
                <button
                    id="sendBtn"
                    class="bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white px-6 py-3 rounded-xl font-bold transition transform hover:scale-105 shadow-lg flex items-center gap-2 whitespace-nowrap"
                >
                    <i class="fas fa-paper-plane"></i><span class="hidden sm:inline">Kirim</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Detail Modal -->
    <div id="detailModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl p-6 max-w-md w-full max-h-96 overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-gray-800">Detail Pelanggan</h2>
                <button id="closeModalBtn" class="text-gray-500 hover:text-gray-700 text-2xl">×</button>
            </div>
            <div id="detailContent" class="space-y-3 text-sm">
                <!-- Customer details will be inserted here -->
            </div>
        </div>
    </div>
</div>

@endsection

@section('extra-js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.socket.io/4.5.4/socket.io.min.js"></script>
<script>
    // Get SD data from prompt or use default
    const sdName = prompt('Masukkan nama Anda (Service Desk Agent):') || 'Agent ' + Math.floor(Math.random() * 1000);
    const sdEmail = 'sd@servicecenter.local';

    $('#agentName').text(sdName);

    // Connect to Socket.io (probe ports 3000/3001)
    async function probeServerUrl() {
        const ports = [3000, 3001];
        for (const port of ports) {
            try {
                const res = await fetch(`http://localhost:${port}/`, { method: 'GET', mode: 'cors' });
                if (res.ok) return `http://localhost:${port}`;
            } catch (e) {
                // try next
            }
        }
        return null;
    }

    let socket = null;
    (async () => {
        const base = await probeServerUrl();
        if (!base) {
            alert('Tidak dapat terhubung ke realtime server (port 3000/3001).');
            return;
        }
        socket = io(base + '/sd', {
            reconnection: true,
            transports: ['websocket']
        });

        // Rest of the SD socket handlers rely on `socket` being defined below.
        setupSdHandlers();
    })();

    let selectedCustomerId = null;
    let selectedRoomId = null;
    const queueCustomers = {};
    const activeChats = {};

    function setupSdHandlers() {
        socket.on('connect', () => {
            console.log('SD Connected to server');

            // Login
            socket.emit('sd_login', {
                name: sdName,
                email: sdEmail
            });

            // Get queue every 2 seconds
            setInterval(() => {
                socket.emit('get_queue');
            }, 2000);
        });

        // Login success
        socket.on('sd_login_success', (data) => {
            console.log('SD Login Success:', data);
        });

        // New customer in queue
        socket.on('new_customer_in_queue', (data) => {
            console.log('New customer in queue:', data);
            queueCustomers[data.customerId] = data;
            renderQueueList();
        });

        // Queue list update
        socket.on('queue_list', (queueList) => {
            $('#queueCount').text(queueList.length);

            // Update queueCustomers object
            queueCustomers = {};
            queueList.forEach(customer => {
                queueCustomers[customer.customerId] = customer;
            });

            renderQueueList();
        });

        // Customer picked successfully
        socket.on('customer_picked', (data) => {
            console.log('Customer picked:', data);
            selectedCustomerId = data.customerId;
            selectedRoomId = data.roomId;

            activeChats[data.customerId] = {
                name: data.customerName,
                email: data.customerEmail,
                phone: data.customerPhone,
                roomId: data.roomId
            };

            $('#selectedCustomerName').text(data.customerName);
            $('#infoEmail').text(data.customerEmail);
            $('#infoPhone').text(data.customerPhone);
            $('#selectedCustomerInfo').removeClass('hidden');
            $('#chatContainer').removeClass('hidden');
            $('#noChatSelected').addClass('hidden');
            $('#inputArea').removeClass('hidden');
            $('#viewDetailBtn').removeClass('hidden');
            $('#endChatBtn').removeClass('hidden');

            renderQueueList();
            renderActiveChatList();

            // Clear chat display
            $('#chatContainer').html('');
        });

        // Receive message from customer or bot
        socket.on('receive_message', (data) => {
            console.log('Received message:', data);

            if (!selectedCustomerId || data.customerId !== selectedCustomerId) {
                console.log('Message not for current customer or no customer selected');
                return;
            }

            let senderDisplay = 'Customer';
            let messageClass = 'flex justify-start';
            let bubbleClass = 'bg-gradient-to-br from-gray-100 to-gray-200 text-gray-800 rounded-2xl rounded-tl-none border-l-4 border-indigo-600';

            if (data.type === 'bot') {
                senderDisplay = '🤖 Bot Greeting';
                bubbleClass = 'bg-gradient-to-br from-amber-100 to-yellow-100 text-gray-800 rounded-2xl rounded-tl-none border-2 border-amber-200';
            } else if (data.customerName) {
                senderDisplay = data.customerName + ' (Customer)';
            }

            const timeStr = new Date(data.timestamp).toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });

            const messageHtml = `
                <div class="mb-4 ${messageClass}">
                    <div class="max-w-xs ${bubbleClass} px-5 py-3 shadow-lg">
                        <p class="font-bold text-sm mb-1">
                            <i class="fas ${data.type === 'bot' ? 'fa-robot' : 'fa-headset'} mr-1"></i>${senderDisplay}
                        </p>
                        <p class="text-sm leading-relaxed break-words">${data.message.replace(/\n/g, '<br>')}</p>
                        <p class="text-xs text-gray-600 mt-2 font-medium">${timeStr}</p>
                    </div>
                </div>
            `;

            $('#chatContainer').append(messageHtml);
            scrollChatToBottom();
        });

        // Customer disconnected
        socket.on('customer_disconnected', (data) => {
            if (data.customerId === selectedCustomerId) {
                alert(`Pelanggan ${data.customerName} telah menutup chat.`);
                resetChat();
            }
            delete activeChats[data.customerId];
            renderActiveChatList();
        });
    }

    // Render queue list
    function renderQueueList() {
        $('#queueList').empty();

        Object.keys(queueCustomers).forEach(customerId => {
            const customer = queueCustomers[customerId];
            const html = `
                <div class="customer-item p-3 rounded-lg border border-gray-200 cursor-pointer hover:bg-blue-50 transition"
                     data-customer-id="${customerId}">
                    <p class="font-semibold text-sm text-gray-800">${customer.name}</p>
                    <p class="text-xs text-gray-500">${customer.email}</p>
                    <p class="text-xs text-gray-400 mt-1"><i class="fas fa-phone mr-1"></i>${customer.phone}</p>
                </div>
            `;
            $('#queueList').append(html);
        });

        if (Object.keys(queueCustomers).length === 0) {
            $('#queueList').html('<p class="text-gray-500 text-sm text-center py-4">Tidak ada antrian</p>');
        }
    }

    // Render active chat list
    function renderActiveChatList() {
        $('#activeChatList').empty();
        $('#activeChatCount').text(Object.keys(activeChats).length);

        Object.keys(activeChats).forEach(customerId => {
            const chat = activeChats[customerId];
            const isActive = customerId === selectedCustomerId;
            const html = `
                <div class="customer-item p-3 rounded-lg border border-gray-200 ${isActive ? 'bg-blue-50 border-blue-400' : ''} cursor-pointer hover:bg-gray-100 transition"
                     onclick="selectChat('${customerId}')">
                    <p class="font-semibold text-sm text-gray-800">${chat.name}</p>
                    <p class="text-xs text-gray-500">${chat.email}</p>
                </div>
            `;
            $('#activeChatList').append(html);
        });

        if (Object.keys(activeChats).length === 0) {
            $('#activeChatList').html('<p class="text-gray-500 text-sm text-center py-4">Tidak ada chat aktif</p>');
        }
    }

    // Pickup customer
    function pickupCustomer(customerId) {
        socket.emit('pickup_customer', {
            customerId: customerId
        });

        delete queueCustomers[customerId];
        renderQueueList();
    }

    // Select chat
    function selectChat(customerId) {
        selectedCustomerId = customerId;
        selectedRoomId = activeChats[customerId].roomId;

        $('#selectedCustomerName').text(activeChats[customerId].name);
        $('#infoEmail').text(activeChats[customerId].email);
        $('#infoPhone').text(activeChats[customerId].phone);
        $('#selectedCustomerInfo').removeClass('hidden');
        $('#chatContainer').removeClass('hidden');
        $('#noChatSelected').addClass('hidden');
        $('#inputArea').removeClass('hidden');

        renderActiveChatList();
        $('#chatContainer').html('');
    }

    // Send message
    $('#sendBtn').click(function() {
        sendMessage();
    });

    $('#messageInput').on('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    function sendMessage() {
        if (!selectedCustomerId || !selectedRoomId) {
            alert('Pilih pelanggan terlebih dahulu');
            return;
        }

        const message = $('#messageInput').val().trim();
        if (!message) return;

        console.log('Sending message:', {
            roomId: selectedRoomId,
            message: message,
            customerId: selectedCustomerId,
            sdName: sdName
        });

        // Display SD message immediately
        const timeStr = new Date().toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });

        const messageHtml = `
            <div class="mb-4 flex justify-end">
                <div class="max-w-xs bg-gradient-to-br from-blue-600 to-indigo-600 text-white rounded-2xl rounded-tr-none px-5 py-3 shadow-lg">
                    <p class="text-sm leading-relaxed break-words">${message.replace(/\n/g, '<br>')}</p>
                    <p class="text-xs text-blue-100 mt-2 text-right font-medium">${timeStr}</p>
                </div>
            </div>
        `;

        $('#chatContainer').append(messageHtml);
        scrollChatToBottom();

        // Send via socket to /sd namespace
        socket.emit('send_message', {
            roomId: selectedRoomId,
            message: message,
            customerId: selectedCustomerId,
            sdName: sdName,
            type: 'sd'
        });

        $('#messageInput').val('');
        $('#charCount').text('0');
    }

    // Character count
    $('#messageInput').on('input', function() {
        $('#charCount').text($(this).val().length);
    });

    function scrollChatToBottom() {
        const container = document.getElementById('chatContainer');
        setTimeout(() => {
            container.scrollTop = container.scrollHeight;
        }, 100);
    }

    // View detail button
    $('#viewDetailBtn').click(function() {
        if (!selectedCustomerId) return;

        socket.emit('get_customer_details', {
            customerId: selectedCustomerId
        });

        socket.once('customer_details', (data) => {
            $('#detailContent').html(`
                <div>
                    <p><strong>Nama:</strong> ${data.name}</p>
                    <p><strong>Email:</strong> ${data.email}</p>
                    <p><strong>Telepon:</strong> ${data.phone}</p>
                    <p><strong>Waktu Bergabung:</strong> ${new Date(data.joinedAt).toLocaleString('id-ID')}</p>
                </div>
            `);

            $('#detailModal').removeClass('hidden');
        });
    });

    $('#closeModalBtn').click(function() {
        $('#detailModal').addClass('hidden');
    });

    // End chat button
    $('#endChatBtn').click(function() {
        if (!selectedCustomerId) return;

        if (confirm('Apakah Anda yakin ingin mengakhiri chat ini?')) {
            // Emit end chat event
            socket.emit('send_message', {
                roomId: selectedRoomId,
                message: 'Mohon maaf, karena tidak ada respons chat dari Bapak/Ibu, saya akhiri chat ini.',
                customerId: selectedCustomerId
            });

            setTimeout(() => {
                resetChat();
            }, 500);
        }
    });

    function resetChat() {
        selectedCustomerId = null;
        selectedRoomId = null;
        $('#selectedCustomerName').text('Pilih pelanggan untuk chat');
        $('#selectedCustomerInfo').addClass('hidden');
        $('#chatContainer').addClass('hidden');
        $('#noChatSelected').removeClass('hidden');
        $('#inputArea').addClass('hidden');
        $('#viewDetailBtn').addClass('hidden');
        $('#endChatBtn').addClass('hidden');
        $('#messageInput').val('');
        $('#charCount').text('0');
    }

    // Logout
    $('#logoutBtn').click(function() {
        if (confirm('Apakah Anda yakin ingin logout?')) {
            socket.emit('sd_logout');
            socket.disconnect();
            window.location.href = '/login';
        }
    });

    // Event delegation for queue item clicks
    $('#queueList').on('click', '.customer-item', function() {
        const customerId = $(this).data('customer-id');
        if (customerId) {
            pickupCustomer(customerId);
        }
    });

    socket.on('disconnect', () => {
        console.log('SD disconnected');
    });
</script>
@endsection
