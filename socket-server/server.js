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

    return res.json({
        success: true,
        message: 'Property broadcasted'
    });
});

const PORT = 3000;

server.listen(PORT, () => {
    console.log(`WebSocket server running on port ${PORT}`);
});