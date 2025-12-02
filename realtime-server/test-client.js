const io = require('socket.io-client');

const url = 'http://localhost:3000/customer';
const socket = io(url, { transports: ['websocket'] });

const customerData = { name: 'TestUser', email: 'test@example.com', phone: '081234', sessionId: 'test_session_1' };

socket.on('connect', () => {
  console.log('Connected as test client', socket.id);
  socket.emit('join_queue', customerData);
});

socket.on('queue_joined', (data) => {
  console.log('queue_joined', data);
});

socket.on('chat_ready', (data) => {
  console.log('chat_ready', data);
  // send a message to the room
  socket.emit('send_message', { roomId: data.roomId, message: 'Hello from test client', name: customerData.name, customerId: customerData.sessionId });
});

socket.on('receive_message', (data) => {
  console.log('receive_message', data);
});

socket.on('disconnect', () => {
  console.log('disconnected');
});

socket.on('queue_timeout', (d) => console.log('queue_timeout', d));

setTimeout(() => {
  console.log('Test client exiting');
  socket.disconnect();
  process.exit(0);
}, 20000);
