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

io.on('connection', (socket) => {
    console.log('✅ User connected:', socket.id);

    socket.on('disconnect', () => {
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

// NEW OFFER ENDPOINT - ADD THIS!
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

// OFFER STATUS UPDATE ENDPOINT - ADD THIS!
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

// Test endpoint
app.get('/test', (req, res) => {
    res.json({ message: 'Server is running!' });
});

const PORT = 3000;

server.listen(PORT, () => {
    console.log(`🚀 WebSocket server running on port ${PORT}`);
    console.log(`📍 Waiting for connections...`);
});