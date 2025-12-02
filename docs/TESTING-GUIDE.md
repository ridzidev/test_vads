## COMPLETE TESTING GUIDE - Step 4 & 5

### Prerequisites
```bash
# Terminal 1: Node.js Server (Port 3000)
cd realtime-server
npm start

# Terminal 2: Laravel Backend (Port 8000)
cd backend
php artisan serve

# Terminal 3: For manual API testing (curl/postman)
# Optional but recommended
```

---

## TEST SCENARIO 1: Complete Customer Flow (10 minutes)

### Step 1.1: Login Page
```
URL: http://localhost:8000/login

Test:
1. Click input Email field
2. Type: test@example.com
3. Verify: No error yet (blur event)
4. Click Password field
5. Type: password123
6. Verify: Password shown as dots
7. Click eye icon (toggle)
8. Verify: Password now visible
9. Click eye icon again
10. Verify: Password hidden again
11. Clear Email, leave empty, click blur
12. Verify: Error "Format email tidak valid" NOT shown (only required check)
13. Type invalid email: "test@"
14. Click blur
15. Verify: Error "Format email tidak valid" shown
16. Fix to: test@example.com
17. Click blur
18. Verify: Error hidden
19. Clear Password, leave empty
20. Click "Masuk ke Chat"
21. Verify: Error "Password wajib diisi" shown
22. Type password: pass123
23. Click "Masuk ke Chat"
24. Verify: Redirect to /register page
```

**Expected Result**: ✅ Login page works, validation correct, redirects to register

---

### Step 1.2: Register Page  
```
URL: http://localhost:8000/register

Test:
1. Leave Name empty, click blur
   → Verify: Error "Nama wajib diisi"
2. Type Name: John Doe
   → Verify: Error hidden
3. Leave Email empty, click blur
   → Verify: Error "Email wajib diisi"
4. Type Email: invalid-email
   → Verify: Error "Format email tidak valid"
5. Type Email: john@example.com
   → Verify: Error hidden
6. Leave Phone empty, click blur
   → Verify: Error "No. Telepon wajib diisi"
7. Type Phone: 081234567890
   → Verify: Error hidden
8. Look at Captcha image
   → Verify: Shows 6 random characters (rotated, offset)
9. Click Refresh button multiple times
   → Verify: Captcha changes each time
10. In Captcha input, type wrong code: ABCDEF
11. Click Next
    → Verify: Error "Captcha code does not match"
    → Verify: Captcha automatically refreshed
12. Type correct code from current image
13. Click Next
    → Verify: Submitting (button shows loading spinner)
    → Verify: API call to /api/register
    → Wait 1-2 seconds
    → Verify: Redirect to /queue-room
```

**Expected Result**: ✅ Register page works, all validations pass, API integration working, redirect to queue

**API Test (Manual)**:
```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test Customer",
    "email": "test@example.com",
    "phone": "081234567890"
  }'

# Expected Response:
# {
#   "success": true,
#   "message": "Registration successful",
#   "data": {
#     "name": "Test Customer",
#     "email": "test@example.com",
#     "phone": "081234567890",
#     "sessionId": "session_TIMESTAMP"
#   }
# }
```

---

### Step 1.3: Queue Room (Waiting Room)
```
URL: http://localhost:8000/queue-room

Test:
1. Page loads after register
   → Verify: Shows customer name: "Halo John Doe!"
   → Verify: Displays email and phone
   → Verify: Timer shows 3:00 (3 minutes)
2. Watch timer count down
   → Verify: Timer decreases: 2:59, 2:58, ... 
3. Socket.io connection
   → Open browser DevTools (F12) → Network tab
   → Verify: WebSocket connection to localhost:3000
   → Verify: Socket.io handshake successful
4. Check Console for socket events
   → Verify: "Connected to server" message
   → Verify: "Joined queue" event
5. Wait or manually trigger pickup
   → Open second browser tab for SD Agent
   → Login SD dashboard
   → In SD tab, click customer "John Doe"
   → Back to Queue tab
   → Verify: "chat_ready" event triggered
   → Verify: Redirect to /chat-room
```

**Expected Result**: ✅ Queue room connects to Socket.io, timer counts down, redirect when chat ready

**Manual Socket Test (Browser Console)**:
```javascript
// In browser console on queue-room page
io  // Should show Socket.io object

// Check session storage
sessionStorage.getItem('customerName')  // Should be "John Doe"
sessionStorage.getItem('customerEmail')  // Should be "test@example.com"
sessionStorage.getItem('customerPhone')  // Should be "081234567890"
```

---

### Step 1.4: Chat Room (Customer Side)
```
URL: http://localhost:8000/chat-room

Test:
1. Page loads after queue pickup
   → Verify: Shows "Terhubung dengan [Agent Name]"
   → Verify: Shows customer info (name, email, phone)
   → Verify: Green status dot "Terhubung"
2. Wait for bot greeting
   → Verify: Receives message: "Halo John Doe. Saya Agent BOT apa yang kamu ingin tanyakan?"
   → Verify: Message shows as yellow/bot-styled
   → Verify: Timestamp displayed
3. Send message
   → Type in textarea: "Halo, ada yang bisa dibantu?"
   → Press Enter or click Send
   → Verify: Message appears on right side (blue)
   → Verify: Message sent to Socket.io event
4. Receive SD response
   → In SD dashboard tab, send message
   → Verify: Message appears on chat room (left, gray)
   → Verify: Shows SD name and timestamp
5. Test idle detection (3 minutes)
   → Don't send any message
   → Wait 3 minutes (180 seconds)
   → Verify: Bot message appears: "Saya masih menunggu respons jawaban chat Bapak/Ibu."
6. Continue waiting (1 more minute to 4 minutes total)
   → Verify: Bot message: "Mohon maaf, karena tidak ada respons chat dari Bapak/Ibu, saya akhiri chat ini."
   → Verify: Connection closes
   → Verify: Redirect to /login
```

**Expected Result**: ✅ Chat room connects, receives messages, idle detection works, auto-close at 4 min

---

## TEST SCENARIO 2: Service Desk (SD) Agent Flow (10 minutes)

### Step 2.1: SD Dashboard Login
```
URL: http://localhost:8000/sd-dashboard

Test:
1. Page loads
   → Verify: Prompt appears asking for agent name
   → Type: "Agent Smith"
   → Click OK
2. After entering name
   → Verify: Shows "Service Desk" header
   → Verify: Agent name "Agent Smith" displayed
   → Verify: Queue list visible on left
   → Verify: "Chat Aktif" shows (0)
3. Socket.io Connection
   → Open DevTools Console
   → Verify: "SD Connected to server" message
   → Verify: sd_login event sent with name
   → Verify: sd_login_success response received
```

**Expected Result**: ✅ SD dashboard loads, socket connected, agent logged in

---

### Step 2.2: Queue Management
```
Test:
1. In another browser tab, start customer registration
   → Complete register flow
   → Get to queue-room
2. Back to SD dashboard tab
   → Verify: Customer appears in queue list
   → Verify: Shows customer name, email, phone
3. Click on customer in queue
   → Verify: "Picking up customer..." indicator
   → Verify: Customer name appears in main area
   → Verify: Shows customer info (email, phone)
4. Verify socket event
   → Console shows "Customer picked" event
   → Customer moved from queue to "Chat Aktif"
```

**Expected Result**: ✅ Queue list updates, customer pickup works

---

### Step 2.3: SD Chat Messaging
```
Test:
1. After picking up customer
   → Chat area appears
   → Verify: Initial bot greeting visible
2. Type message to customer
   → Type: "Halo, saya siap membantu Anda"
   → Click Send or press Shift+Enter
   → Verify: Message appears on left (gray, SD styled)
3. Customer responds (in customer browser)
   → Customer tab sends message
   → Back to SD tab
   → Verify: Customer message appears in chat
   → Verify: Shows "CUSTOMER" label
   → Verify: Contains customer's message
4. Send multiple messages
   → Continue conversation
   → Verify: All messages appear with timestamps
   → Verify: Correct sender styling
```

**Expected Result**: ✅ Real-time messaging works both directions

---

### Step 2.4: Customer Details
```
Test:
1. Click "View" button in SD dashboard
   → Verify: Modal appears
   → Verify: Shows customer details:
     - Name
     - Email
     - Telepon
     - Waktu Bergabung (timestamp)
2. Close modal (click X or background)
   → Verify: Modal closes
```

**Expected Result**: ✅ Customer detail view works

---

### Step 2.5: Master Customer Menu
```
URL: http://localhost:8000/master-customer

Test:
1. Click "Load Data" button
   → Verify: Loading spinner appears
   → Verify: API call to randomuser.me
   → Wait for response (2-5 seconds)
2. Data loaded
   → Verify: Table appears with customer data
   → Verify: Shows all columns:
     - No
     - Photo (image thumbnail)
     - Nama Lengkap
     - Email
     - Username
     - Login UUID
     - Telepon
     - Ponsel
3. Click "View" button for a customer
   → Verify: Modal opens with:
     - Large photo
     - Name, Email, Username, Password
     - Login UUID
     - Phone, Cell
     - Age, City, Country, Nationality
4. Close modal
5. Click "Refresh" button
   → Verify: New data fetched (different customers)
   → Verify: Different UUIDs and usernames
6. Click "Export CSV" button
   → Verify: CSV file downloaded
   → Verify: Filename format: master_customers_TIMESTAMP.csv
   → Open CSV file
   → Verify: Contains all customer data with headers
```

**Expected Result**: ✅ Master customer menu works, data loads, export works

**API Test (Manual)**:
```bash
curl http://localhost:8000/api/customers?page=1&results=10

# Expected Response:
# {
#   "success": true,
#   "data": [
#     {
#       "name": "Full Name",
#       "email": "email@example.com",
#       "login": {
#         "uuid": "...",
#         "username": "...",
#         "password": "..."
#       },
#       ...
#     }
#   ],
#   "meta": { ... }
# }
```

---

## TEST SCENARIO 3: Multi-Customer Concurrent Test (15 minutes)

### Setup:
```
- Browser Tab 1: Customer 1 (Chrome)
- Browser Tab 2: Customer 2 (Firefox or Edge)
- Browser Tab 3: SD Agent (Chrome)
```

### Test:
```
1. Tab 1: Start customer 1 flow
   - Go to /login → /register → queue-room
   - Wait in queue

2. Tab 2: Start customer 2 flow
   - Go to /login → /register → queue-room
   - Wait in queue

3. Tab 3: SD Login
   - Go to /sd-dashboard
   - Enter agent name
   - Verify: Both customers visible in queue

4. Tab 3: Pickup Customer 1
   - Click customer 1
   - Verify: Chat starts
   - Send message to customer 1

5. Tab 1: Receive message from SD
   - Verify: Message received
   - Send response

6. Tab 3: Switch to Customer 2
   - Click customer 2 in active chat list
   - Send greeting message

7. Tab 2: Receive message
   - Verify: Message from SD received

8. Tab 3: Switch back to Customer 1
   - Verify: Can switch between customers
   - Chat history preserved

9. All Browsers: Monitor Socket.io
   - DevTools Network tab
   - Verify: Multiple namespaces
   - Verify: Correct rooms isolated
```

**Expected Result**: ✅ Multi-customer support works, rooms isolated

---

## TEST SCENARIO 4: Timeout & Disconnection (15 minutes)

### Idle Timeout Test:
```
1. Start customer flow to chat room
2. Receive bot greeting
3. Don't send any message
4. Wait 3 minutes
   → Verify: Bot sends warning message
5. Continue waiting 1 more minute
   → Verify: Bot sends close message
   → Verify: Connection closes
   → Verify: Redirect to /login
```

### Manual Disconnect:
```
1. In chat room, click "Akhiri Chat"
   → Verify: Confirmation dialog
   → Click OK
   → Verify: Connection closes
   → Verify: Redirect to /login

2. In SD dashboard, click "Sign Out"
   → Verify: Confirmation dialog
   → Click OK
   → Verify: Logout successful
   → Verify: Redirect to /login
```

**Expected Result**: ✅ Timeout and disconnect handling works

---

## TEST SCENARIO 5: Error Handling

### Network Error Test:
```
1. Start chat flow
2. Open DevTools Network tab
3. Throttle to Offline (Chrome DevTools)
4. Try to send message
   → Verify: Error shown or message queued
5. Restore connectivity
   → Verify: Message sent or timeout shown
```

### Invalid Captcha Test:
```
1. Go to /register
2. Keep typing wrong captcha (10+ times)
   → Verify: Each time shows error and refreshes
   → Verify: No submission allowed
3. Type correct code
   → Verify: Accepted and proceeds
```

### API Errors:
```bash
# Test invalid registration
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{"name": "Test"}' # Missing email and phone

# Expected: 400 error with validation message
```

**Expected Result**: ✅ Error handling appropriate

---

## TEST SCENARIO 6: Browser Compatibility (5 minutes each)

Test on:
- [ ] Chrome 90+
- [ ] Firefox 88+
- [ ] Safari 14+
- [ ] Edge 90+
- [ ] Mobile Chrome
- [ ] Mobile Safari

**Expected**: All features work without breaking

---

## PERFORMANCE TESTING

### Load Test:
```bash
# Using Apache Bench (ab)
ab -n 100 -c 10 http://localhost:8000/login

# Using Wrk
wrk -t4 -c100 -d30s http://localhost:8000/login
```

### Socket.io Load:
```
- Simulate 50 concurrent customers
- Verify: Server handles without crashing
- Check: Memory usage, CPU usage
- Monitor: Socket.io events/second
```

---

## CHECKLIST - All Tests

### Login Page
- [ ] Email validation works
- [ ] Password show/hide works
- [ ] Required field validation
- [ ] Redirects to register

### Register Page
- [ ] Name validation
- [ ] Email format validation
- [ ] Phone validation
- [ ] Captcha generates random code
- [ ] Captcha refresh works
- [ ] Captcha matching validation
- [ ] API integration (/api/register)
- [ ] Redirect to queue-room

### Queue Room
- [ ] Socket.io connects
- [ ] Timer counts down
- [ ] Customer info displays
- [ ] Redirect on chat ready
- [ ] Timeout handling

### Chat Room (Customer)
- [ ] Socket.io connects
- [ ] Receives bot greeting
- [ ] Sends messages
- [ ] Receives SD messages
- [ ] Idle detection 3 min
- [ ] Auto-close 4 min
- [ ] Manual disconnect
- [ ] Status indicator

### SD Dashboard
- [ ] Socket.io connects
- [ ] Agent login
- [ ] Queue list shows customers
- [ ] Pickup customer works
- [ ] Chat messaging works
- [ ] View customer details
- [ ] Switch between customers
- [ ] Sign out works

### Master Customer
- [ ] Load data from API
- [ ] Table displays correctly
- [ ] JSON flattening correct
- [ ] View detail modal
- [ ] Refresh gets new data
- [ ] Export CSV works

### API Endpoints
- [ ] POST /api/register
- [ ] GET /api/customers
- [ ] GET /api/health
- [ ] GET /api/sd-agents
- [ ] GET /api/chat-history/{id}
- [ ] POST /api/chat-message

### Real-time Features
- [ ] Socket.io connects
- [ ] Events send/receive
- [ ] Multiple rooms isolated
- [ ] Auto-close works
- [ ] Message persistence

### Error Handling
- [ ] Validation errors shown
- [ ] Network errors handled
- [ ] Timeout handled
- [ ] Disconnect handled

---

## SUMMARY

**Total Tests**: 50+
**Estimated Time**: 60-90 minutes
**Pass Criteria**: 95%+ tests passing

**If All Tests Pass**: ✅ Step 4 & 5 complete and ready for Step 6 (Finalization)

