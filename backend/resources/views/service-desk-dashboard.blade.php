@extends('app')

@section('title', 'Service Desk Dashboard - Real-Time Web Chat Application')
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
                    <!-- FIX: Ambil nama langsung dari Auth Laravel -->
                    <p class="font-semibold" id="agentName">{{ Auth::user()->name ?? 'Admin' }}</p>
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
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                    <p>Menghubungkan...</p>
                </div>
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
            <form action="{{ route('logout') }}" method="POST" id="logoutForm">
                @csrf
                <button type="button" id="logoutBtn"
                    class="w-full bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 text-white font-bold py-3 rounded-lg transition transform hover:scale-105 shadow-lg flex items-center justify-center gap-2">
                    <i class="fas fa-sign-out-alt"></i> Sign Out
                </button>
            </form>
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
                <button id="viewDetailBtn" class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-5 py-2 rounded-lg text-sm font-bold transition transform hover:scale-105 shadow-lg hidden flex items-center gap-2">
                    <i class="fas fa-eye"></i>Detail
                </button>
                <button id="endChatBtn" class="bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 text-white px-5 py-2 rounded-lg text-sm font-bold transition transform hover:scale-105 shadow-lg hidden flex items-center gap-2">
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
                    <textarea id="messageInput" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none resize-none bg-gray-50 hover:bg-white transition" rows="2" placeholder="💬 Ketikkan pesan balasan Anda... (Shift+Enter untuk baris baru)" maxlength="1000"></textarea>
                    <div class="flex justify-between items-center mt-2 px-1">
                        <small class="text-gray-500 text-xs">Tekan Shift+Enter untuk baris baru</small>
                        <small class="text-gray-400 text-xs"><span id="charCount">0</span>/1000</small>
                    </div>
                </div>
                <button id="sendBtn" class="bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white px-6 py-3 rounded-xl font-bold transition transform hover:scale-105 shadow-lg flex items-center gap-2 whitespace-nowrap">
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
            <div id="detailContent" class="space-y-3 text-sm"></div>
        </div>
    </div>
</div>
@endsection

@section('extra-js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.socket.io/4.5.4/socket.io.min.js"></script>
<script>
    // FIX: Gunakan data dari PHP Blade langsung, hapus prompt()
    const sdName = "{{ Auth::user()->name }}";
    const sdEmail = "{{ Auth::user()->email }}";

    // Connect to Socket.io (probe ports 3000/3001)
    async function probeServerUrl() {
        const ports = [3000, 3001];
        for (const port of ports) {
            try {
                const res = await fetch(`http://localhost:${port}/`, { method: 'GET', mode: 'cors' });
                if (res.ok) return `http://localhost:${port}`;
            } catch (e) {}
        }
        return null;
    }

    let socket = null;
    let selectedCustomerId = null;
    let selectedRoomId = null;
    
    // FIX: Gunakan 'let' agar bisa di-reset objectnya
    let queueCustomers = {};
    let activeChats = {};

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

        setupSdHandlers();
    })();

    function setupSdHandlers() {
        socket.on('connect', () => {
            console.log('SD Connected to server');
            socket.emit('sd_login', { name: sdName, email: sdEmail });
            
            // Trigger get queue immediately
            socket.emit('get_queue');
        });

        // Loop untuk memastikan queue selalu update
        setInterval(() => {
            if(socket && socket.connected) {
                socket.emit('get_queue');
            }
        }, 3000);

        socket.on('new_customer_in_queue', (data) => {
            console.log('New customer:', data);
            // Tambahkan ke object existing, jangan di-reset semua
            queueCustomers[data.customerId] = data; 
            renderQueueList();
        });

        socket.on('queue_list', (queueList) => {
            console.log("Queue List Received:", queueList); // Debugging
            $('#queueCount').text(queueList.length);

            // FIX: Reset object dengan aman
            queueCustomers = {}; 
            queueList.forEach(customer => {
                queueCustomers[customer.customerId] = customer;
            });

            renderQueueList();
        });

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

            // Hapus dari antrian lokal karena sudah dipick
            delete queueCustomers[data.customerId];

            updateActiveChatUI(data);
            renderQueueList();
            renderActiveChatList();
            $('#chatContainer').html(''); // Bersihkan chat sebelumnya
        });

        socket.on('receive_message', (data) => {
            if (!selectedCustomerId || data.customerId !== selectedCustomerId) return;
            appendMessage(data);
        });

        socket.on('customer_disconnected', (data) => {
            if (data.customerId === selectedCustomerId) {
                alert(`Pelanggan ${data.customerName} telah menutup chat.`);
                resetChat();
            }
            delete activeChats[data.customerId];
            renderActiveChatList();
        });
    }

    function renderQueueList() {
        const container = $('#queueList');
        container.empty();

        const ids = Object.keys(queueCustomers);
        if (ids.length === 0) {
            container.html('<div class="text-center py-8 text-gray-500"><i class="fas fa-inbox text-4xl mb-2 opacity-30 block"></i><p>Belum ada pelanggan dalam antrian</p></div>');
            return;
        }

        ids.forEach(customerId => {
            const customer = queueCustomers[customerId];
            const html = `
                <div class="customer-item p-3 rounded-lg border border-gray-200 cursor-pointer hover:bg-blue-50 transition mb-2 shadow-sm"
                     onclick="pickupCustomer('${customerId}')">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="font-bold text-sm text-gray-800">${customer.name || 'Pelanggan'}</p>
                            <p class="text-xs text-gray-500">${customer.email || '-'}</p>
                        </div>
                        <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">Pick</span>
                    </div>
                    <p class="text-xs text-gray-400 mt-1"><i class="fas fa-phone mr-1"></i>${customer.phone || '-'}</p>
                </div>
            `;
            container.append(html);
        });
    }

    function renderActiveChatList() {
        const container = $('#activeChatList');
        container.empty();
        $('#activeChatCount').text(Object.keys(activeChats).length);

        const ids = Object.keys(activeChats);
        if (ids.length === 0) {
            container.html('<p class="text-gray-500 text-sm text-center py-4">Tidak ada chat aktif</p>');
            return;
        }

        ids.forEach(customerId => {
            const chat = activeChats[customerId];
            const isActive = customerId === selectedCustomerId;
            const html = `
                <div class="p-3 rounded-lg border border-gray-200 ${isActive ? 'bg-indigo-50 border-indigo-400 ring-1 ring-indigo-300' : 'hover:bg-gray-50'} cursor-pointer transition mb-2"
                     onclick="selectChat('${customerId}')">
                    <p class="font-semibold text-sm text-gray-800">${chat.name}</p>
                    <p class="text-xs text-gray-500">${chat.email}</p>
                </div>
            `;
            container.append(html);
        });
    }

    function pickupCustomer(customerId) {
        if(confirm("Ambil antrian pelanggan ini?")) {
            socket.emit('pickup_customer', { customerId: customerId });
        }
    }

    function selectChat(customerId) {
        if(!activeChats[customerId]) return;
        
        selectedCustomerId = customerId;
        selectedRoomId = activeChats[customerId].roomId;
        updateActiveChatUI({
            customerName: activeChats[customerId].name,
            customerEmail: activeChats[customerId].email,
            customerPhone: activeChats[customerId].phone
        });
        renderActiveChatList();
        $('#chatContainer').html('<div class="text-center text-gray-400 text-sm py-2">Memuat percakapan...</div>');
        // Logic load history chat bisa ditambahkan di sini
    }

    function updateActiveChatUI(data) {
        $('#selectedCustomerName').text(data.customerName);
        $('#infoEmail').text(data.customerEmail);
        $('#infoPhone').text(data.customerPhone);
        $('#selectedCustomerInfo').removeClass('hidden');
        $('#chatContainer').removeClass('hidden');
        $('#noChatSelected').addClass('hidden');
        $('#inputArea').removeClass('hidden');
        $('#viewDetailBtn').removeClass('hidden');
        $('#endChatBtn').removeClass('hidden');
    }

    function appendMessage(data) {
        let senderDisplay = 'Customer';
        let justify = 'justify-start';
        let bubbleStyle = 'bg-gray-100 text-gray-800 rounded-tl-none border-l-4 border-indigo-600';

        if (data.type === 'bot') {
            senderDisplay = '🤖 Bot System';
            bubbleStyle = 'bg-yellow-50 text-gray-800 rounded-tl-none border-2 border-yellow-200';
        } else if (data.type === 'sd') {
            justify = 'justify-end';
            bubbleStyle = 'bg-gradient-to-br from-blue-600 to-indigo-600 text-white rounded-tr-none';
            senderDisplay = 'Anda';
        } else {
            senderDisplay = data.customerName || 'Customer';
        }

        const timeStr = new Date(data.timestamp).toLocaleTimeString('id-ID', { hour:'2-digit', minute:'2-digit'});
        
        const html = `
            <div class="mb-4 flex ${justify}">
                <div class="max-w-xs ${bubbleStyle} px-4 py-2 rounded-2xl shadow-sm">
                    ${data.type !== 'sd' ? `<p class="font-bold text-xs mb-1 opacity-75">${senderDisplay}</p>` : ''}
                    <p class="text-sm leading-relaxed whitespace-pre-wrap">${data.message}</p>
                    <p class="text-[10px] opacity-70 mt-1 text-right">${timeStr}</p>
                </div>
            </div>
        `;
        $('#chatContainer').append(html);
        
        const container = document.getElementById('chatContainer');
        container.scrollTop = container.scrollHeight;
    }

    $('#sendBtn').click(() => sendMessage());
    $('#messageInput').on('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); }
    });

    function sendMessage() {
        const msg = $('#messageInput').val().trim();
        if (!msg || !selectedRoomId) return;

        // UI Optimistic Update
        appendMessage({
            message: msg,
            type: 'sd',
            timestamp: Date.now()
        });

        socket.emit('send_message', {
            roomId: selectedRoomId,
            message: msg,
            customerId: selectedCustomerId,
            sdName: sdName,
            type: 'sd'
        });

        $('#messageInput').val('');
    }

    // View detail & End chat logic (tetap sama)
    $('#logoutBtn').click(function() {
        if (confirm('Apakah Anda yakin ingin logout?')) {
            socket.emit('sd_logout');
            socket.disconnect();
            $('#logoutForm').submit();
        }
    });
</script>
@endsection