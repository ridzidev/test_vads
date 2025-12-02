@extends('app')

@section('title', 'Live Chat - VADS System')
@section('page-title', 'Live Chat Customer')

@section('content')
<div class="h-screen bg-gray-100 flex flex-col relative overflow-hidden">
    
    <!-- HEADER -->
    <div class="bg-white p-4 shadow-sm border-b border-gray-200 z-10 flex justify-between items-center">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-blue-600 to-cyan-500 flex items-center justify-center text-white shadow-lg">
                <i class="fas fa-headset text-lg"></i>
            </div>
            <div>
                <h1 class="font-bold text-gray-800 text-lg leading-tight">VADS Support</h1>
                <div class="flex items-center gap-1.5">
                    <span id="statusIndicator" class="w-2.5 h-2.5 rounded-full bg-yellow-400 animate-pulse"></span>
                    <p id="statusText" class="text-xs text-gray-500 font-medium">Menghubungkan...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN CONTENT WRAPPER -->
    <div class="flex-1 relative">
        
        <!-- 1. LOADING / WAITING STATE (Default Visible) -->
        <div id="loadingState" class="absolute inset-0 flex flex-col items-center justify-center bg-gray-50 z-20 transition-opacity duration-500">
            <div class="text-center p-8 max-w-sm mx-auto">
                <div class="relative w-24 h-24 mx-auto mb-6">
                    <div class="absolute inset-0 border-4 border-blue-100 rounded-full"></div>
                    <div class="absolute inset-0 border-4 border-blue-500 rounded-full border-t-transparent animate-spin"></div>
                    <i class="fas fa-comments absolute inset-0 flex items-center justify-center text-blue-500 text-3xl"></i>
                </div>
                <h2 class="text-xl font-bold text-gray-800 mb-2">Mohon Tunggu Sebentar</h2>
                <p class="text-gray-500 text-sm leading-relaxed mb-6">
                    Kami sedang menghubungkan Anda dengan Agent terbaik kami. Jangan tutup halaman ini agar antrian Anda tidak hilang.
                </p>
                <div class="bg-blue-50 text-blue-700 px-4 py-2 rounded-lg text-xs font-semibold inline-flex items-center gap-2">
                    <i class="fas fa-users"></i>
                    <span>Anda berada dalam antrian prioritas</span>
                </div>
            </div>
        </div>

        <!-- 2. CHAT INTERFACE (Hidden by Default) -->
        <div id="mainChatInterface" class="absolute inset-0 flex flex-col bg-gray-100 opacity-0 transform translate-y-4 transition-all duration-500 pointer-events-none">
            
            <!-- Chat Bubbles Container -->
            <div id="chatContainer" class="flex-1 overflow-y-auto p-4 space-y-4 scroll-smooth">
                <!-- System Message Example -->
                <div class="flex justify-center my-4">
                    <span class="bg-gray-200 text-gray-600 text-xs px-3 py-1 rounded-full shadow-sm">
                        <i class="fas fa-lock mr-1"></i> Percakapan ini aman dan terenkripsi
                    </span>
                </div>
            </div>

            <!-- Input Area -->
            <div class="bg-white p-4 border-t border-gray-200">
                <form id="chatForm" class="flex items-end gap-3 max-w-4xl mx-auto w-full">
                    <div class="flex-1 bg-gray-100 rounded-2xl flex items-center px-4 py-2 border border-transparent focus-within:border-blue-500 focus-within:bg-white focus-within:ring-2 focus-within:ring-blue-100 transition-all duration-300">
                        <input 
                            type="text" 
                            id="chatInput" 
                            class="w-full bg-transparent border-none focus:ring-0 text-gray-700 placeholder-gray-400 py-2"
                            placeholder="Ketik pesan Anda..." 
                            autocomplete="off"
                            disabled
                        >
                    </div>
                    <button 
                        type="submit" 
                        id="sendBtn" 
                        class="w-12 h-12 bg-gray-300 text-white rounded-full flex items-center justify-center shadow-md transform active:scale-95 transition-all duration-300 cursor-not-allowed" 
                        disabled>
                        <i class="fas fa-paper-plane text-lg translate-x-0.5 translate-y-0.5"></i>
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection

@section('extra-js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.socket.io/4.5.4/socket.io.min.js"></script>
<script>
    // --- 1. Session & Config ---
    let storedSession = localStorage.getItem('vads_chat_session');
    if (!storedSession) {
        storedSession = 'session_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        localStorage.setItem('vads_chat_session', storedSession);
    }

    const customerData = {
        name: '{{ Auth::user()->name ?? "Pelanggan" }}',
        email: '{{ Auth::user()->email ?? "customer@example.com" }}',
        phone: '{{ Auth::user()->phone ?? "-" }}',
        sessionId: storedSession
    };

    let socket;
    let roomId = null;
    let isChatActive = false;

    // --- 2. UI Control Functions (KUNCI PERBAIKAN) ---

    function showLoading() {
        $('#loadingState').removeClass('hidden').css('opacity', '1');
        $('#mainChatInterface').css('opacity', '0').css('pointer-events', 'none');
        
        $('#statusIndicator').removeClass('bg-green-500').addClass('bg-yellow-400 animate-pulse');
        $('#statusText').text('Menunggu antrian...');
    }

    function showChat() {
        if(isChatActive) return; // Prevent double trigger
        isChatActive = true;

        // Hide Loading with fade out
        $('#loadingState').css('opacity', '0');
        setTimeout(() => $('#loadingState').addClass('hidden'), 500);

        // Show Chat with fade in
        const chatUI = $('#mainChatInterface');
        chatUI.removeClass('hidden');
        
        // Sedikit delay untuk efek animasi slide up
        setTimeout(() => {
            chatUI.css('opacity', '1')
                  .css('transform', 'translateY(0)')
                  .css('pointer-events', 'auto');
        }, 100);

        // Update Header Status
        $('#statusIndicator').removeClass('bg-yellow-400 animate-pulse').addClass('bg-green-500');
        $('#statusText').text('Terhubung dengan Agent');

        enableInput();
    }

    function addMessage(msg, type, name) {
        // Pastikan UI Chat Muncul jika ada pesan masuk
        showChat();

        const time = new Date().toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'});
        let html = '';

        if (type === 'customer') {
            html = `
                <div class="flex justify-end mb-4 animate-fade-in-up">
                    <div class="max-w-[75%]">
                        <div class="bg-blue-600 text-white px-5 py-3 rounded-2xl rounded-tr-sm shadow-md">
                            <p class="text-sm leading-relaxed">${escapeHtml(msg)}</p>
                        </div>
                        <p class="text-[10px] text-gray-400 text-right mt-1 font-medium mr-1">${time}</p>
                    </div>
                </div>
            `;
        } else {
            // Agent / SD
            html = `
                <div class="flex justify-start mb-4 animate-fade-in-up">
                    <div class="max-w-[75%]">
                        <div class="flex items-center gap-2 mb-1 ml-1">
                            <span class="text-xs font-bold text-gray-700">${name}</span>
                            <span class="bg-blue-100 text-blue-700 text-[10px] px-1.5 py-0.5 rounded font-bold">Admin</span>
                        </div>
                        <div class="bg-white text-gray-800 px-5 py-3 rounded-2xl rounded-tl-sm shadow-sm border border-gray-100">
                            <p class="text-sm leading-relaxed">${escapeHtml(msg)}</p>
                        </div>
                        <p class="text-[10px] text-gray-400 mt-1 ml-1 font-medium">${time}</p>
                    </div>
                </div>
            `;
        }
        
        $('#chatContainer').append(html);
        scrollToBottom();
    }

    function addSystemMessage(msg, type = 'info') {
        let colorClass = 'bg-gray-200 text-gray-600';
        let icon = 'fa-info-circle';

        if (type === 'bot') { 
            // Bot messages look slightly different
            addMessage(msg, 'agent', '🤖 VADS Bot');
            return; 
        }
        
        if (type === 'error') { colorClass = 'bg-red-100 text-red-600'; icon = 'fa-exclamation-triangle'; }
        if (type === 'success') { colorClass = 'bg-green-100 text-green-700'; icon = 'fa-check'; }

        const html = `
            <div class="flex justify-center mb-4 animate-fade-in">
                <div class="${colorClass} px-4 py-1.5 rounded-full text-xs font-medium flex items-center gap-2 shadow-sm">
                    <i class="fas ${icon}"></i>
                    <span>${escapeHtml(msg)}</span>
                </div>
            </div>
        `;
        $('#chatContainer').append(html);
        scrollToBottom();
    }

    function enableInput() {
        $('#chatInput').prop('disabled', false).focus();
        $('#sendBtn').prop('disabled', false)
            .removeClass('bg-gray-300 text-white cursor-not-allowed')
            .addClass('bg-gradient-to-r from-blue-600 to-blue-500 text-white hover:shadow-lg hover:scale-105');
    }

    function disableInput() {
        $('#chatInput').prop('disabled', true);
        $('#sendBtn').prop('disabled', true)
            .addClass('bg-gray-300 text-white cursor-not-allowed')
            .removeClass('bg-gradient-to-r from-blue-600 to-blue-500 hover:shadow-lg hover:scale-105');
    }

    function scrollToBottom() {
        const container = document.getElementById('chatContainer');
        container.scrollTop = container.scrollHeight;
    }

    function escapeHtml(text) {
        return text ? text.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;") : '';
    }

    // --- 3. Socket Logic ---

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

    (async () => {
        const base = await probeServerUrl();
        if (!base) {
            alert("Gagal koneksi ke server realtime.");
            return;
        }

        socket = io(base + '/customer', {
            reconnection: true,
            transports: ['websocket'],
            query: customerData
        });

        // Event: Connected
        socket.on('connect', () => {
            console.log('Connected');
            socket.emit('join_queue', customerData);
        });

        // Event: Masuk Antrian
        socket.on('queue_joined', () => {
            console.log('In Queue');
            // Pastikan tetap di loading screen
            if(!isChatActive) showLoading();
        });

        // Event: CHAT READY (KUNCI UTAMA)
        socket.on('chat_ready', (data) => {
            console.log('Chat Ready!', data);
            roomId = data.roomId;
            // Panggil fungsi transisi UI
            showChat();
            addSystemMessage('Anda telah terhubung dengan Agent.', 'success');
        });

        // Event: Receive Message
        socket.on('receive_message', (data) => {
            console.log('Msg:', data);
            
            if(data.type === 'sd') {
                if(!roomId && data.roomId) roomId = data.roomId;
                // Jika tiba-tiba dapat pesan admin, paksa buka chat
                showChat(); 
                addMessage(data.message, 'agent', data.sdName || 'Service Desk');
            } 
            else if(data.type === 'bot') {
                // Pesan bot juga ditampilkan di chat bubble
                showChat(); // Bot memicu tampilan chat
                addMessage(data.message, 'agent', '🤖 VADS Bot');
            } 
            else if(data.type === 'customer' && data.customerId !== customerData.sessionId) {
                // Sync tab lain
                showChat();
                addMessage(data.message, 'customer', 'Anda');
            }
        });

        socket.on('chat_ended', () => {
            disableInput();
            addSystemMessage('Sesi chat telah berakhir.', 'error');
            $('#statusText').text('Sesi Berakhir');
            $('#statusIndicator').removeClass('bg-green-500').addClass('bg-gray-400');
            localStorage.removeItem('vads_chat_session');
            setTimeout(() => window.location.href = '/dashboard', 3000);
        });

    })();

    // Handle Submit
    $('#chatForm').submit(function(e) {
        e.preventDefault();
        const input = $('#chatInput');
        const msg = input.val().trim();

        if (msg && roomId) {
            addMessage(msg, 'customer', 'Anda');
            socket.emit('send_message', {
                roomId: roomId,
                message: msg,
                name: customerData.name,
                customerId: customerData.sessionId,
                type: 'customer'
            });
            input.val('');
        }
    });

    // Handle Enter Key
    $('#chatInput').on('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            $('#chatForm').submit();
        }
    });

    // Additional CSS for Animations
    $('<style>')
        .prop('type', 'text/css')
        .html(`
            @keyframes fadeInUp {
                from { opacity: 0; transform: translateY(10px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .animate-fade-in-up {
                animation: fadeInUp 0.3s ease-out forwards;
            }
            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }
            .animate-fade-in {
                animation: fadeIn 0.3s ease-out forwards;
            }
        `)
        .appendTo('head');
</script>
@endsection