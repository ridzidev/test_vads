````markdown
# Step 4: Real-time Chat Module (Soal 1) - Live Chat Web Application

## Deskripsi
Implementasi modul Live Chat real-time menggunakan Node.js (Socket.io) dan Laravel Frontend dengan fitur auto-reply bot, idle detection, dan Service Desk dashboard.

## Arsitektur Teknis

### Backend Real-time (Node.js + Socket.io)
- **Port**: 3000
- **Framework**: Express.js v4.17.1 + Socket.io v3.1.2
- **Namespace**: 
  - `/customer` - Customer chat interface
  - `/sd` - Service Desk agent interface

### Frontend (Laravel Blade + jQuery)
- **Framework**: Laravel 10/11
- **Port**: 8000 (same as API)
- **Styling**: Tailwind CSS (CDN) + Font Awesome
- **Real-time Client**: Socket.io Client Library

---

## Modul 1: Landing Page & Login

### File: `backend/resources/views/login.blade.php`

**Features:**
1. **Email Input** dengan validasi format
2. **Password Input** dengan Show/Hide toggle
3. **Required Field Validation** sebelum submit
4. **Responsive Design** menggunakan Tailwind CSS

**Validasi:**
- Email: Format validation (@domain.com)
- Password: Required field
- Alert jika ada field kosong atau format salah

**URL**: `GET /login` atau `GET /`

---

## Modul 2: Session Register dengan Captcha

### File: `backend/resources/views/register.blade.php`

**Features:**
1. **Form Input**: Name, Email, Phone, Captcha
2. **Image Captcha**: 
   - Random 6-character alphanumeric
   - Rotasi dan offset untuk anti-bot
   - Random styling pada setiap generate
3. **Tombol Refresh**: Refresh captcha image
4. **Validasi Lengkap**:
   - Name: Required, string only
   - Email: Required, valid format
   - Phone: Required, valid phone number format
   - Captcha: Must match the image code

**Error Handling:**
- "this column is mandatory" - jika field kosong
- "Invalid email format" - jika email tidak valid
- "Captcha code does not match" - jika captcha salah

**URL**: `GET /register`

**Session Storage**:
```javascript
sessionStorage.setItem('customerName', name);
sessionStorage.setItem('customerEmail', email);
sessionStorage.setItem('customerPhone', phone);
```

---

## Modul 3: Queue Room (Waiting Room)

### File: `backend/resources/views/queue-room.blade.php`

**Features:**
1. **Timer Countdown**: 3 menit (180 detik)
   - Format: MM:SS
   - Color change to red saat < 1 menit
   
2. **Real-time Connection** via Socket.io
   - Event: `join_queue` - Customer bergabung
   - Event: `chat_ready` - SD menerima pelanggan
   
3. **Timeout Handling**:
   - Jika <= 3 menit dan SD online → Go to chat
   - Jika > 3 menit dan tidak ada SD → Auto-close & redirect to login

4. **Display Info**:
   - Customer name, email, phone
   - Wait time remaining
   - Loading animation

**Socket Events**:
```javascript
// Customer emit
socket.emit('join_queue', {
  name: customerName,
  email: customerEmail,
  phone: customerPhone,
  sessionId: sessionId
});

// Customer listen
socket.on('chat_ready', (data) => {
  sessionStorage.setItem('roomId', data.roomId);
  window.location.href = '/chat-room';
});
```

**URL**: `GET /queue-room`

---

## Modul 4: Chat Room (Customer Interface)

### File: `backend/resources/views/chat-room.blade.php`

**Features:**
1. **Chat Interface** dengan message history
2. **Auto-Reply Bot**:
   - Initial greeting: "Halo {name}. Saya Agent BOT apa yang kamu ingin tanyakan?"
   - Sent after customer picked by SD
   
3. **Idle Detection**:
   - 3 minutes idle: "Saya masih menunggu respons jawaban chat Bapak/Ibu."
   - 4 minutes idle: "Mohon maaf, karena tidak ada respons chat dari Bapak/Ibu, saya akhiri chat ini." + Auto-close
   
4. **Message Display**:
   - Customer message (right, blue)
   - Bot message (left, yellow)
   - SD message (left, gray)
   
5. **Real-time Connection**:
   - Send message event
   - Receive message event
   - Connection status indicator

**Socket Events**:
```javascript
// Customer emit
socket.emit('send_message', {
  roomId: roomId,
  message: message,
  name: customerName
});

// Customer listen
socket.on('receive_message', (data) => {
  // Handle incoming message
});

socket.on('auto_disconnect', (data) => {
  // Handle auto-close
});
```

**URL**: `GET /chat-room`

---

## Modul 5: Service Desk (SD) Dashboard

### File: `backend/resources/views/sd-dashboard.blade.php`

**Features:**
1. **Sidebar Queue List**:
   - Menampilkan customer dalam antrian
   - Click untuk pickup customer
   
2. **Active Chat List**:
   - Menampilkan chat yang sedang berlangsung
   - Click untuk switch antar customer
   
3. **Main Chat Area**:
   - Display pesan dari customer & bot
   - Input textarea untuk reply
   - Send button
   
4. **Customer Information**:
   - Name, Email, Phone
   - Created time
   - Chat history
   
5. **Action Buttons**:
   - View: Detail customer info
   - End Chat: Terminate conversation
   - Sign Out: Logout dari workspace

**Socket Events**:
```javascript
// SD emit
socket.emit('sd_login', { name: sdName, email: sdEmail });
socket.emit('get_queue'); // Get queue list
socket.emit('pickup_customer', { customerId: customerId });
socket.emit('send_message', { roomId, message, customerId });
socket.emit('get_customer_details', { customerId });
socket.emit('sd_logout');

// SD listen
socket.on('new_customer_in_queue', (data) => {});
socket.on('queue_list', (queueList) => {});
socket.on('customer_picked', (data) => {});
socket.on('receive_message', (data) => {});
socket.on('customer_disconnected', (data) => {});
socket.on('customer_details', (data) => {});
```

**URL**: `GET /sd-dashboard`

**Agent Login**: Prompt untuk input nama SD agent

---

## Modul 6: Master Customer (SD Menu)

### File: `backend/resources/views/master-customer.blade.php`

**Features:**
1. **Data Fetching** dari API publik:
   - URL: `https://randomuser.me/api?results=10&page=1`
   
2. **JSON Manipulation (Flattening)**:
   ```javascript
   // Nested structure → Flat structure
   {
     name: `${title} ${first} ${last}`,
     email: email,
     login: {
       uuid: uuid,
       username: username,
       password: password
     },
     phone: phone,
     cell: cell,
     picture: picture_url
   }
   ```
   
3. **Table Display**:
   - No, Photo, Name, Email, Username, Login UUID
   - Telepon, Ponsel, Actions (View detail)
   
4. **Action Buttons**:
   - Load Data: Fetch dari randomuser.me
   - Refresh: Fetch dengan page berbeda
   - Export CSV: Download sebagai CSV file
   - View: Lihat detail customer
   
5. **Detail Modal**:
   - Large photo
   - Complete customer information
   - Login credentials (uuid, username, password)

**API Integration**:
```javascript
$.ajax({
  url: 'https://randomuser.me/api?results=10&page=1',
  type: 'GET',
  dataType: 'json',
  success: function(response) {
    const flatData = flattenCustomerData(response.results);
    renderTable(flatData);
  }
});
```

**URL**: `GET /master-customer`

---

## Node.js Real-time Server

### File: `realtime-server/index.js`

**Features:**
1. **Express Server** di port 3000
2. **Two Namespaces**:
   - `/customer` - Customer koneksi
   - `/sd` - Service Desk koneksi
   
3. **Socket Events**:
   - `join_queue` - Customer masuk antrian
   - `pickup_customer` - SD mengambil customer
   - `send_message` - Send pesan
   - `get_queue` - Get daftar antrian
   - `sd_login` - SD login
   - `disconnect` - Disconnect handling
   
4. **Business Logic**:
   - Queue management
   - Room mapping (customer + SD)
   - Idle timer (3 & 4 menit)
   - Auto-reply bot messages
   - Session storage
   
5. **REST Endpoints**:
   - `GET /health` - Health check
   - `GET /api/sd-agents` - Get active agents
   - `GET /api/queue-info` - Get queue info

---

## Workflow Alur Komunikasi

```
1. CUSTOMER FLOW:
   Login → Register → Queue Room (wait 3 min) → Chat Room → Idle Detection → Auto-close

2. SERVICE DESK FLOW:
   Login → See Queue → Pickup Customer → Chat → Send Message → View Details → Logout

3. REAL-TIME FLOW:
   Browser (Customer) 
       ↓ Socket.io /customer namespace
   Node.js Server (Port 3000)
       ↑↓ Room management
   Browser (SD Agent)
       ↓ Socket.io /sd namespace
```

---

## Setup & Running Instructions

### Prerequisites
```bash
Node.js v12.11.1+
npm 6.11.3+
PHP 8.2+
Laravel 10/11
```

### 1. Setup Node.js Real-time Server
```bash
cd realtime-server
npm install
npm start
# Server will run on http://localhost:3000
```

### 2. Setup Laravel Backend
```bash
cd backend
php artisan serve
# Laravel will run on http://localhost:8000
```

### 3. Access URLs
- Login: http://localhost:8000/login
- Register: http://localhost:8000/register
- Queue: http://localhost:8000/queue-room
- Chat: http://localhost:8000/chat-room
- SD Dashboard: http://localhost:8000/sd-dashboard
- Master Customer: http://localhost:8000/master-customer

---

## Technology Stack Summary

| Component | Technology | Version |
|-----------|-----------|---------|
| Backend Server | Node.js + Express | 12.11.1 + 4.17.1 |
| Real-time | Socket.io | 3.1.2 |
| Frontend | Laravel Blade | 10/11 |
| Styling | Tailwind CSS | v3 (CDN) |
| HTTP Client | jQuery + AJAX | 3.6.0 |
| Database | MySQL/MariaDB | 8.0 |

---

## Dependencies

### Node.js (`realtime-server/package.json`)
```json
{
  "express": "^4.17.1",
  "socket.io": "^3.1.2",
  "cors": "^2.8.5",
  "dotenv": "^17.2.3"
}
```

### Laravel (Backend - Auto)
- Built-in dengan Laravel 10/11

### Frontend Libraries (Via CDN)
- jQuery 3.6.0
- Socket.io Client 3.1.x
- Tailwind CSS v3
- Font Awesome 6.4.0

---

## Browser Compatibility
- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

---

## Security Considerations

1. **CORS Setup** di Socket.io:
   - Origin whitelist untuk localhost:8000
   - Credentials enabled
   
2. **Session Management**:
   - sessionStorage untuk client-side data
   - Socket connection per session
   
3. **Input Validation**:
   - Client-side: Email, phone format
   - Server-side: Message length limit (1000 chars)
   
4. **Auto-disconnect**:
   - Idle timeout untuk security
   - Explicit logout button

---

## Testing Scenario

### Scenario 1: Happy Path (Customer → Chat → Disconnect)
1. Open http://localhost:8000/login
2. Click "Daftar di sini" → /register
3. Fill form + solve captcha → Click "Next" → /queue-room
4. Wait or manually trigger SD pickup
5. Chat interface opens → Send message → Receive bot reply
6. Wait 3 minutes → Idle warning → Wait 1 more minute → Auto-close

### Scenario 2: Service Desk Workflow
1. Open http://localhost:8000/sd-dashboard
2. Input SD name pada prompt
3. View queue list
4. Click customer to pickup
5. Chat interface shows
6. Send message to customer
7. Click "View" untuk lihat detail
8. Click "Master Customer" untuk lihat data
9. Click "Sign Out" untuk logout

### Scenario 3: Master Customer Menu
1. Login as SD
2. Navigate to Master Customer
3. Click "Load Data"
4. View table dari randomuser.me
5. Click "View" untuk detail
6. Click "Export CSV" untuk download

````
