require('dotenv').config();
const express = require('express');
const http = require('http');
const cors = require('cors');
const { Server } = require('socket.io');

const app = express();
app.use(cors());

const server = http.createServer(app);

const io = new Server(server, {
  cors: { origin: '*', methods: ['GET', 'POST'] }
});

const PORT = process.env.PORT || 3000;

const userSessions = {}; 
const sdSessions = {};   

// --- Helper Functions ---

function broadcastQueue() {
  const queueList = Object.keys(userSessions)
    .filter(id => userSessions[id].inQueue && !userSessions[id].inChat)
    .map(id => ({
      customerId: id,
      name: userSessions[id].name,
      email: userSessions[id].email,
      phone: userSessions[id].phone,
      joinedAt: userSessions[id].joinedAt
    }))
    .sort((a, b) => a.joinedAt - b.joinedAt);

  io.of('/sd').emit('queue_list', queueList);
}

// --- Customer Namespace ---
const customerNS = io.of('/customer');

customerNS.on('connection', (socket) => {
  const querySessionId = socket.handshake.query.sessionId;
  console.log('[Customer CONNECT] Socket: ' + socket.id + ' | Session: ' + querySessionId);

  socket.on('join_queue', (data) => {
    const customerId = data.sessionId;
    
    if (userSessions[customerId]) {
        console.log('[Customer RECONNECT] ' + customerId + ' updated socket to ' + socket.id);
        userSessions[customerId].socketId = socket.id;
        
        if (userSessions[customerId].inChat && userSessions[customerId].roomId) {
            const rid = userSessions[customerId].roomId;
            socket.join(rid);
            socket.emit('chat_ready', { roomId: rid });
            
            // Re-emit last greeting if needed logic here
        } else {
            socket.emit('queue_joined', { customerId: customerId, name: userSessions[customerId].name });
        }
    } else {
        console.log('[Customer NEW] ' + customerId);
        userSessions[customerId] = {
          socketId: socket.id,
          name: data.name,
          email: data.email,
          phone: data.phone,
          joinedAt: Date.now(),
          inQueue: true,
          inChat: false,
          sdId: null,
          roomId: null,
          queueTimeout: null
        };

        // Timeout Logic
        userSessions[customerId].queueTimeout = setTimeout(() => {
            const s = userSessions[customerId];
            if (s && !s.inChat) {
                const msg = 'Halo ' + s.name + '. Saya Agent BOT apa yang kamu ingin tanyakan?';
                socket.emit('receive_message', { message: msg, type: 'bot', timestamp: Date.now() });
            }
        }, 3 * 60 * 1000);

        socket.emit('queue_joined', { customerId: customerId, name: data.name });
        io.of('/sd').emit('new_customer_in_queue', userSessions[customerId]);
    }
    broadcastQueue();
  });

  socket.on('send_message', (data) => {
    if (!data.roomId) return;
    io.of('/sd').to(data.roomId).emit('receive_message', {
        message: data.message,
        type: 'customer',
        customerName: data.name,
        timestamp: Date.now(),
        customerId: data.customerId
    });
  });

  socket.on('disconnect', () => {
    console.log('[Customer DISCONNECT] ' + socket.id);
  });
});

// --- Service Desk Namespace ---
const sdNS = io.of('/sd');

sdNS.on('connection', (socket) => {
  console.log('[SD CONNECT] ' + socket.id);

  socket.on('sd_login', (data) => {
    sdSessions[socket.id] = { socketId: socket.id, name: data.name, email: data.email };
    broadcastQueue();
  });

  socket.on('get_queue', () => broadcastQueue());

  socket.on('pickup_customer', (data) => {
    const customerId = data.customerId;
    const session = userSessions[customerId];

    if (!session) {
        socket.emit('error', { message: 'Customer sudah tidak aktif.' });
        broadcastQueue();
        return;
    }

    console.log('[SD PICKUP] Agent ' + socket.id + ' picking customer ' + customerId);

    if (session.queueTimeout) clearTimeout(session.queueTimeout);

    const roomId = 'room_' + customerId + '_' + socket.id;
    
    session.inQueue = false;
    session.inChat = true;
    session.sdId = socket.id;
    session.roomId = roomId;

    // 1. Agent Join Room
    socket.join(roomId);

    // 2. Customer Join Room (METODE DIRECT SOCKET OBJECT - LEBIH STABIL)
    // Kita cari object socket customer berdasarkan ID-nya di namespace /customer
    const customerSocket = io.of('/customer').sockets.get(session.socketId);

    if (customerSocket) {
        console.log('---> Customer Socket FOUND: ' + session.socketId + '. Joining ' + roomId);
        customerSocket.join(roomId);
        customerSocket.emit('chat_ready', { roomId: roomId });
    } else {
        console.log('---> Customer Socket NOT FOUND (Mungkin disconnect). ID: ' + session.socketId);
    }

    // 3. Info ke SD
    socket.emit('customer_picked', {
        customerId: customerId,
        customerName: session.name,
        customerEmail: session.email,
        customerPhone: session.phone,
        roomId: roomId
    });

    // 4. Greeting
    let agentName = 'Service Desk';
    if (sdSessions[socket.id] && sdSessions[socket.id].name) {
        agentName = sdSessions[socket.id].name;
    }

    const greeting = 'Halo ' + session.name + ', saya ' + agentName + '. Ada yang bisa saya bantu?';
    
    // Broadcast Greeting ke Room (biar customer & SD dapet)
    io.of('/customer').to(roomId).emit('receive_message', {
        message: greeting,
        type: 'sd',
        sdName: agentName,
        timestamp: Date.now()
    });
    
    // Backup: Kirim ke SD langsung (takutnya SD belum ready di room)
    socket.emit('receive_message', {
        message: greeting,
        type: 'sd',
        customerId: customerId, 
        timestamp: Date.now()
    });

    broadcastQueue();
  });

  socket.on('send_message', (data) => {
      if(!data.roomId) return;
      // Kirim ke Customer
      io.of('/customer').to(data.roomId).emit('receive_message', {
          message: data.message,
          type: 'sd',
          sdName: data.sdName,
          timestamp: Date.now()
      });
      // Kirim ke SD lain (Sync tab)
      socket.to(data.roomId).emit('receive_message', {
          message: data.message,
          type: 'sd',
          customerId: data.customerId,
          timestamp: Date.now()
      });
  });
  
  socket.on('get_customer_details', (data) => {
      const s = userSessions[data.customerId];
      if(s) socket.emit('customer_details', { name: s.name, email: s.email, phone: s.phone, joinedAt: s.joinedAt });
  });

  socket.on('sd_logout', () => {
      delete sdSessions[socket.id];
      socket.disconnect();
  });

  socket.on('disconnect', () => {
      delete sdSessions[socket.id];
  });
});

app.get('/', (req, res) => res.send('Realtime Server Active'));

const startServer = (port) => {
  server.listen(port, () => {
    console.log(`✅ Realtime server BERHASIL jalan di port ${port}`);
  });

  server.on('error', (err) => {
    if (err.code === 'EADDRINUSE') {
      console.error(`❌ Port ${port} masih dipakai (Zombie Process)!`);
      console.error(`   Jalankan: 'killall -9 node' di terminal untuk mematikan process lama.`);
      process.exit(1); // Matikan process biar tidak bingung
    } else {
      console.error('Server error:', err);
    }
  });
};

startServer(PORT);