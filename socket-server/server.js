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

    console.log('User connected:', socket.id);

    socket.on('disconnect', () => {
        console.log('User disconnected:', socket.id);
    });
});

app.post('/new-property', (req, res) => {
    const property = req.body;

    io.emit('property-added', property);


    console.log('✅ User connected:', socket.id);

    socket.on('disconnect', () => {
        console.log('❌ User disconnected:', socket.id);
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

const PORT = 3000;

server.listen(PORT, () => {
    console.log(`WebSocket server running on port ${PORT}`);

// UPDATE PROPERTY - ADD THIS!
app.post('/update-property', (req, res) => {
    const property = req.body;
    console.log('✏️ Update property received:', property.title);
    io.emit('property-updated', property);
    return res.json({
        success: true,
        message: 'Property update broadcasted'
    });
});

// DELETE PROPERTY - ADD THIS!
app.post('/delete-property', (req, res) => {
    const data = req.body;
    console.log('🗑️ Delete property received:', data.id);
    io.emit('property-deleted', data);
    return res.json({
        success: true,
        message: 'Property deleted broadcasted'
    });
});

// ARCHIVE PROPERTY - ADD THIS!
app.post('/archive-property', (req, res) => {
    const data = req.body;
    console.log('📦 Archive property received:', data.id);
    io.emit('property-archived', data);
    return res.json({
        success: true,
        message: 'Property archived broadcasted'
    });
});

// UNARCHIVE PROPERTY - ADD THIS!
app.post('/unarchive-property', (req, res) => {
    const data = req.body;
    console.log('🔄 Unarchive property received:', data.id);
    io.emit('property-unarchived', data);
    return res.json({
        success: true,
        message: 'Property unarchived broadcasted'
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