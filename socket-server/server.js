const express = require('express');
const http = require('http');
const { Server } = require('socket.io');
const cors = require('cors');

const app = express();

app.use(cors());
app.use(express.json());

const server = http.createServer(app);

const io = new Server(server, {
    cors: {
        origin: '*',
        methods: ['GET', 'POST']
    }
});

// Store connected users for private messaging
const connectedUsers = new Map(); // { userId: socketId }

io.on('connection', (socket) => {
    console.log('✅ User connected:', socket.id);

    // Register user with their user ID for private messaging
    socket.on('register', (userId) => {
        if (userId) {
            connectedUsers.set(userId.toString(), socket.id);
            console.log(`📝 User ${userId} registered with socket ${socket.id}`);
            socket.emit('registered', { userId, status: 'online' });
        }
    });

    // Handle private messages between users
    socket.on('private message', (data) => {
        const { to, content, from, fromName, propertyId } = data;
        
        console.log(`💬 Private message from ${fromName} (${from}) to user ${to}`);
        console.log(`   Message: ${content.substring(0, 50)}${content.length > 50 ? '...' : ''}`);
        
        // Save message to database using built-in http module (no node-fetch needed)
        const postData = JSON.stringify({
            sender_id: parseInt(from),
            receiver_id: parseInt(to),
            message: content,
            property_id: propertyId ? parseInt(propertyId) : null
        });
        
        const options = {
            hostname: 'localhost',
            port: 8080,
            path: '/api/save-message',
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Content-Length': Buffer.byteLength(postData)
            }
        };
        
        const request = http.request(options, (response) => {
            let responseData = '';
            response.on('data', (chunk) => {
                responseData += chunk;
            });
            response.on('end', () => {
                console.log('💾 Message saved to database:', responseData);
            });
        });
        
        request.on('error', (error) => {
            console.error('❌ Failed to save message:', error.message);
        });
        
        request.write(postData);
        request.end();
        
        // Send real-time message to recipient if they're online
        const recipientSocketId = connectedUsers.get(to.toString());
        
        if (recipientSocketId) {
            io.to(recipientSocketId).emit('private message', {
                from: from,
                fromName: fromName,
                content: content,
                propertyId: propertyId,
                timestamp: new Date().toISOString(),
                status: 'delivered'
            });
            
            // Confirm to sender that message was delivered
            socket.emit('message delivered', { 
                to, 
                content, 
                timestamp: new Date().toISOString() 
            });
            console.log(`✅ Message delivered to user ${to}`);
        } else {
            // Recipient is offline - message saved to DB only
            socket.emit('message sent', { 
                to, 
                content, 
                status: 'saved',
                timestamp: new Date().toISOString()
            });
            console.log(`⚠️ User ${to} is offline - message saved to database`);
        }
    });

    // Handle user disconnection - remove from connected users map
    socket.on('disconnect', () => {
        // Find and remove the disconnected user
        for (let [userId, socketId] of connectedUsers.entries()) {
            if (socketId === socket.id) {
                connectedUsers.delete(userId);
                console.log(`📴 User ${userId} disconnected`);
                break;
            }
        }
        console.log('❌ User disconnected:', socket.id);
    });
    
    // Optional: Handle real-time offer events directly from client
    socket.on('new-offer', (data) => {
        console.log('💰 New offer via socket:', data);
        io.emit('new-offer', data);
    });
    
    socket.on('offer-status-updated', (data) => {
        console.log('📋 Offer status update via socket:', data);
        io.emit('offer-status-updated', data);
    });
});

// NEW PROPERTY
app.post('/new-property', (req, res) => {
    const property = req.body;
    console.log('📦 New property received:', property.title);
    io.emit('property-added', property);
    return res.json({
        success: true,
        message: 'Property broadcasted'
    });
});

// UPDATE PROPERTY
app.post('/update-property', (req, res) => {
    const property = req.body;
    console.log('✏️ Update property received:', property.title);
    io.emit('property-updated', property);
    return res.json({
        success: true,
        message: 'Property update broadcasted'
    });
});

// DELETE PROPERTY
app.post('/delete-property', (req, res) => {
    const data = req.body;
    console.log('🗑️ Delete property received:', data.id);
    io.emit('property-deleted', data);
    return res.json({
        success: true,
        message: 'Property deleted broadcasted'
    });
});

// ARCHIVE PROPERTY
app.post('/archive-property', (req, res) => {
    const data = req.body;
    console.log('📦 Archive property received:', data.id);
    io.emit('property-archived', data);
    return res.json({
        success: true,
        message: 'Property archived broadcasted'
    });
});

// UNARCHIVE PROPERTY
app.post('/unarchive-property', (req, res) => {
    const data = req.body;
    console.log('🔄 Unarchive property received:', data.id);
    io.emit('property-unarchived', data);
    return res.json({
        success: true,
        message: 'Property unarchived broadcasted'
    });
});

// NEW OFFER ENDPOINT
app.post('/new-offer', (req, res) => {
    const offer = req.body;
    console.log('💰 New offer received:', offer);
    console.log(`   Amount: ₱${offer.amount}, Property: ${offer.property_title}, Buyer: ${offer.buyer_name}`);
    io.emit('new-offer', offer);
    return res.json({
        success: true,
        message: 'Offer broadcasted to sellers'
    });
});

// OFFER STATUS UPDATE ENDPOINT
app.post('/offer-status-updated', (req, res) => {
    const offerUpdate = req.body;
    console.log('📋 Offer status update received:', offerUpdate);
    console.log(`   Status: ${offerUpdate.status}, Property: ${offerUpdate.property_title}, Buyer: ${offerUpdate.buyer_name}`);
    io.emit('offer-status-updated', offerUpdate);
    return res.json({
        success: true,
        message: `Offer ${offerUpdate.status} broadcasted to buyer`
    });
});

// Check if a user is online
app.post('/check-online-status', (req, res) => {
    const { userId } = req.body;
    const isOnline = connectedUsers.has(userId?.toString());
    res.json({ userId, online: isOnline });
});

// Test endpoint
app.get('/test', (req, res) => {
    res.json({ message: 'Server is running!' });
});

const PORT = 3000;

server.listen(PORT, () => {
    console.log(`🚀 WebSocket server running on port ${PORT}`);
    console.log(`📍 Waiting for connections...`);
});