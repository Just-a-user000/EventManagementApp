const WebSocket = require('ws');
const http = require('http');

const PORT = process.env.WS_PORT || 8080;

const server = http.createServer();
const wss = new WebSocket.Server({ server });

const clients = new Map();

wss.on('connection', (ws, req) => {
    console.log('New client connected');
    
    ws.on('message', (message) => {
        try {
            const data = JSON.parse(message);
            
            if (data.type === 'register' && data.userId) {
                clients.set(data.userId, ws);
                console.log(`User ${data.userId} registered for notifications`);
                
                ws.send(JSON.stringify({
                    type: 'registered',
                    message: 'Successfully registered for notifications'
                }));
            }
        } catch (error) {
            console.error('Error parsing message:', error);
        }
    });
    
    ws.on('close', () => {
        for (const [userId, client] of clients.entries()) {
            if (client === ws) {
                clients.delete(userId);
                console.log(`User ${userId} disconnected`);
                break;
            }
        }
    });
    
    ws.on('error', (error) => {
        console.error('WebSocket error:', error);
    });
});

server.on('request', (req, res) => {
    if (req.method === 'POST' && req.url === '/notify') {
        let body = '';
        
        req.on('data', chunk => {
            body += chunk.toString();
        });
        
        req.on('end', () => {
            try {
                const notification = JSON.parse(body);
                
                if (notification.userId) {
                    const client = clients.get(notification.userId);
                    
                    if (client && client.readyState === WebSocket.OPEN) {
                        client.send(JSON.stringify({
                            type: 'notification',
                            data: notification.data
                        }));
                        
                        res.writeHead(200, { 'Content-Type': 'application/json' });
                        res.end(JSON.stringify({ success: true, message: 'Notification sent' }));
                    } else {
                        res.writeHead(404, { 'Content-Type': 'application/json' });
                        res.end(JSON.stringify({ success: false, message: 'User not connected' }));
                    }
                } else if (notification.userIds && Array.isArray(notification.userIds)) {
                    let sentCount = 0;
                    
                    notification.userIds.forEach(userId => {
                        const client = clients.get(userId);
                        if (client && client.readyState === WebSocket.OPEN) {
                            client.send(JSON.stringify({
                                type: 'notification',
                                data: notification.data
                            }));
                            sentCount++;
                        }
                    });
                    
                    res.writeHead(200, { 'Content-Type': 'application/json' });
                    res.end(JSON.stringify({ 
                        success: true, 
                        message: `Notification sent to ${sentCount} users` 
                    }));
                } else {
                    res.writeHead(400, { 'Content-Type': 'application/json' });
                    res.end(JSON.stringify({ success: false, message: 'Invalid notification format' }));
                }
            } catch (error) {
                console.error('Error processing notification:', error);
                res.writeHead(500, { 'Content-Type': 'application/json' });
                res.end(JSON.stringify({ success: false, message: 'Server error' }));
            }
        });
    } else if (req.method === 'GET' && req.url === '/status') {
        res.writeHead(200, { 'Content-Type': 'application/json' });
        res.end(JSON.stringify({ 
            status: 'running',
            connectedClients: clients.size,
            port: PORT
        }));
    } else {
        res.writeHead(404, { 'Content-Type': 'application/json' });
        res.end(JSON.stringify({ error: 'Not found' }));
    }
});

server.listen(PORT, () => {
    console.log(`WebSocket server is running on port ${PORT}`);
    console.log(`HTTP endpoint available at http://localhost:${PORT}/notify`);
});

process.on('SIGTERM', () => {
    console.log('SIGTERM signal received: closing HTTP server');
    server.close(() => {
        console.log('HTTP server closed');
    });
});
