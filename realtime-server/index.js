require('dotenv').config();
const express = require('express');
const http = require('http');
const cors = require('cors');
const { Server } = require('socket.io');

const app = express();
app.use(cors());

const server = http.createServer(app);

// Use default path for consistency unless explicitly needed
const io = new Server(server, {
  cors: { origin: '*', methods: ['GET', 'POST'] }
});

const PORT = process.env.PORT || 3000;

// Centralized state management
const userSessions = {}; // Stores details for each customer session (key: sessionId)
const sdSessions = {};   // Stores details for each Service Desk agent (key: socket.id)

// --- Helper Functions ---

/**
 * Broadcasts the current queue list to all connected Service Desk agents.
 */
function broadcastQueue() {
  const queueList = Object.keys(userSessions)
    // Filter for customers who are in queue and not currently in a chat
    .filter(id => userSessions[id].inQueue && !userSessions[id].inChat)
    // Map to a clean, serializable data object
    .map(id => ({
      customerId: id,
      name: userSessions[id].name,
      email: userSessions[id].email,
      phone: userSessions[id].phone,
      joinedAt: userSessions[id].joinedAt
    }))
    // Sort by joined time (FIFO)
    .sort((a, b) => a.joinedAt - b.joinedAt);

  io.of('/sd').emit('queue_list', queueList);
  console.log(`[Queue Update] Broadcasted ${queueList.length} items to SD.`);
}

// --- Customer Namespace (/customer) ---
const customerNS = io.of('/customer');

customerNS.on('connection', (socket) => {
  const querySessionId = socket.handshake.query.sessionId;
  
  // Basic log, ensuring we have a session ID
  if (!querySessionId) {
      console.log(`[Customer CONNECT] Socket: ${socket.id} | Session ID missing. Disconnecting.`);
      socket.disconnect();
      return;
  }
  
  console.log(`[Customer CONNECT] Socket: ${socket.id} | Session: ${querySessionId}`);

  socket.on('join_queue', (data) => {
    const customerId = data.sessionId;
    
    if (userSessions[customerId]) {
        // --- RECONNECT LOGIC ---
        
        console.log(`[Customer RECONNECT] ${customerId} updated socket to ${socket.id}`);
        
        // Update the socket ID, important for direct lookup (like in pickup_customer)
        userSessions[customerId].socketId = socket.id;
        
        // Re-join the room if chat is active
        if (userSessions[customerId].inChat && userSessions[customerId].roomId) {
            const rid = userSessions[customerId].roomId;
            socket.join(rid);
            socket.emit('chat_ready', { roomId: rid });
            
            // You might want to send a welcome back message or chat history here
        } else {
            // Still in queue
            socket.emit('queue_joined', { customerId: customerId, name: userSessions[customerId].name });
        }
    } else {
        // --- NEW CUSTOMER LOGIC ---
        
        console.log(`[Customer NEW] ${customerId}`);
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
          queueTimeout: null // Will store the Node.js Timeout object
        };

        // Timeout Logic for auto-response after 3 minutes
        userSessions[customerId].queueTimeout = setTimeout(() => {
            const s = userSessions[customerId];
            if (s && s.socketId === socket.id && s.inQueue && !s.inChat) {
                const msg = `Halo ${s.name}. Saya Agent BOT apa yang kamu ingin tanyakan?`;
                socket.emit('receive_message', { message: msg, type: 'bot', timestamp: Date.now() });
                console.log(`[BOT TIMEOUT] Sent BOT response to ${s.name} (${customerId}).`);
                
                // OPTIONAL: Clear the timeout property after it fires
                userSessions[customerId].queueTimeout = null;
            }
        }, 3 * 60 * 1000);

        socket.emit('queue_joined', { customerId: customerId, name: data.name });
        
        // *** FIX: Send only required data, excluding the circular 'queueTimeout' object ***
        const newCustomerData = {
            customerId: customerId,
            name: userSessions[customerId].name,
            email: userSessions[customerId].email,
            phone: userSessions[customerId].phone,
            joinedAt: userSessions[customerId].joinedAt
        };
        io.of('/sd').emit('new_customer_in_queue', newCustomerData);
    }
    broadcastQueue();
  });

  socket.on('send_message', (data) => {
    // Basic validation
    if (!data.roomId || !data.message) return;
    
    // Broadcast message to Service Desk agent in the same room
    io.of('/sd').to(data.roomId).emit('receive_message', {
        message: data.message,
        type: 'customer',
        customerName: data.name,
        timestamp: Date.now(),
        customerId: data.customerId // Useful for SD app to identify the sender
    });
  });

  socket.on('disconnect', () => {
    console.log(`[Customer DISCONNECT] ${socket.id}`);
    
    // Note: It's better not to immediately delete the session here, 
    // as the customer might quickly reconnect. The session should be managed 
    // based on chat completion or a long inactivity period.
  });
});

// --- Service Desk Namespace (/sd) ---
const sdNS = io.of('/sd');

sdNS.on('connection', (socket) => {
  console.log(`[SD CONNECT] ${socket.id}`);

  socket.on('sd_login', (data) => {
    // Basic validation
    if (!data.name || !data.email) {
        socket.emit('error', { message: 'Login data (name, email) is required.' });
        return;
    }
    
    sdSessions[socket.id] = { socketId: socket.id, name: data.name, email: data.email };
    socket.emit('login_success', { name: data.name });
    console.log(`[SD LOGIN] Agent ${data.name} (${socket.id}) logged in.`);
    broadcastQueue();
  });

  socket.on('get_queue', () => broadcastQueue());

  socket.on('pickup_customer', (data) => {
    const customerId = data.customerId;
    const session = userSessions[customerId];

    // Check if customer exists and is still in queue
    if (!session || !session.inQueue) {
        socket.emit('error', { message: 'Customer sudah tidak aktif atau sudah di-pickup oleh agent lain.' });
        broadcastQueue();
        return;
    }

    console.log(`[SD PICKUP] Agent ${socket.id} picking customer ${customerId}`);

    // 1. Clear the BOT timeout
    if (session.queueTimeout) {
      clearTimeout(session.queueTimeout);
      session.queueTimeout = null;
    }

    // 2. Update session state
    const roomId = `room_${customerId}_${Date.now()}`; // Unique room ID
    
    session.inQueue = false;
    session.inChat = true;
    session.sdId = socket.id;
    session.roomId = roomId;

    // 3. Agent Join Room
    socket.join(roomId);

    // 4. Customer Join Room
    const customerSocket = io.of('/customer').sockets.get(session.socketId);

    if (customerSocket) {
        console.log(`---> Customer Socket FOUND: ${session.socketId}. Joining ${roomId}`);
        customerSocket.join(roomId);
        customerSocket.emit('chat_ready', { roomId: roomId });
    } else {
        console.log(`---> Customer Socket NOT FOUND. ID: ${session.socketId}. Customer might have disconnected.`);
        // Note: The customer will re-join the room upon reconnect in the 'join_queue' handler
    }

    // 5. Info ke SD (Agent)
    let agentName = 'Service Desk';
    if (sdSessions[socket.id] && sdSessions[socket.id].name) {
        agentName = sdSessions[socket.id].name;
    }
    
    socket.emit('customer_picked', {
        customerId: customerId,
        customerName: session.name,
        customerEmail: session.email,
        customerPhone: session.phone,
        roomId: roomId,
        agentName: agentName // Send agent name back for confirmation
    });

    // 6. Greeting (Broadcast to room)
    const greeting = `Halo ${session.name}, saya ${agentName}. Ada yang bisa saya bantu?`;
    
    // Broadcast Greeting to Room (Customer side)
    if (customerSocket) {
      customerSocket.emit('receive_message', {
          message: greeting,
          type: 'sd',
          sdName: agentName,
          timestamp: Date.now()
      });
    }
    
    // Send to SD (The agent that just picked up) for their history/timeline
    socket.emit('receive_message', {
        message: greeting,
        type: 'sd',
        customerId: customerId, 
        timestamp: Date.now()
    });

    broadcastQueue();
  });

  socket.on('send_message', (data) => {
      if(!data.roomId || !data.message) return;
      
      // Kirim ke Customer (Namespace /customer)
      io.of('/customer').to(data.roomId).emit('receive_message', {
          message: data.message,
          type: 'sd',
          sdName: data.sdName,
          timestamp: Date.now()
      });
      
      // Kirim ke SD lain (Sync tab) (excluding self)
      socket.to(data.roomId).emit('receive_message', {
          message: data.message,
          type: 'sd',
          customerId: data.customerId, // Include customer ID for agent's app logic
          timestamp: Date.now()
      });
  });
  
  socket.on('get_customer_details', (data) => {
      const s = userSessions[data.customerId];
      if(s) {
        socket.emit('customer_details', { 
            name: s.name, 
            email: s.email, 
            phone: s.phone, 
            joinedAt: s.joinedAt 
        });
      } else {
        socket.emit('error', { message: 'Customer session not found.' });
      }
  });

  socket.on('sd_logout', () => {
      console.log(`[SD LOGOUT] Agent ${socket.id} logging out.`);
      // Deletion is handled in the disconnect handler below for consistency
      socket.disconnect(); 
  });

  socket.on('disconnect', () => {
      console.log(`[SD DISCONNECT] ${socket.id}`);
      delete sdSessions[socket.id];
      // Note: You might want to implement a 'transfer' or 'end chat' logic here
      // if an agent disconnects while actively chatting with a customer.
  });
});

// --- Server Setup ---

app.get('/', (req, res) => res.send('Realtime Server Active'));

const startServer = (port) => {
  server.listen(port, () => {
    console.log(`✅ Realtime server BERHASIL jalan di port ${port}`);
  });

  server.on('error', (err) => {
    if (err.code === 'EADDRINUSE') {
      console.error(`❌ Port ${port} masih dipakai (Zombie Process)!`);
      console.error(`   Jalankan: 'killall -9 node' di terminal untuk mematikan process lama.`);
      process.exit(1);
    } else {
      console.error('Server error:', err);
      process.exit(1);
    }
  });
};

startServer(PORT);