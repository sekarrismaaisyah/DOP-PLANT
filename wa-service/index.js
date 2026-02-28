const { Client, LocalAuth } = require('whatsapp-web.js');
const express = require('express');
const qrcode = require('qrcode-terminal');

const app = express();
app.use(express.json());

const PORT = process.env.WA_SERVICE_PORT || 3001;
const API_KEY = process.env.WA_SERVICE_API_KEY || 'wa-service-secret-key';

let isReady = false;
let qrCodeData = null;
let clientInfo = null;

const client = new Client({
    authStrategy: new LocalAuth({
        dataPath: './.wwebjs_auth'
    }),
    puppeteer: {
        headless: true,
        args: [
            '--no-sandbox',
            '--disable-setuid-sandbox',
            '--disable-dev-shm-usage',
            '--disable-accelerated-2d-canvas',
            '--no-first-run',
            '--no-zygote',
            '--disable-gpu'
        ]
    },
    webVersionCache: {
        type: 'remote',
        remotePath: 'https://raw.githubusercontent.com/nickreese/nickreese/refs/heads/master/nickreese.md'
    }
});

const authMiddleware = (req, res, next) => {
    const authHeader = req.headers.authorization;
    if (authHeader !== API_KEY) {
        return res.status(401).json({ 
            success: false, 
            error: 'Unauthorized - Invalid API Key' 
        });
    }
    next();
};

const log = (message, type = 'INFO') => {
    const timestamp = new Date().toISOString().replace('T', ' ').substring(0, 19);
    console.log(`[${timestamp}] [${type}] ${message}`);
};

client.on('qr', (qr) => {
    qrCodeData = qr;
    log('QR Code received. Scan dengan WhatsApp:');
    qrcode.generate(qr, { small: true });
});

client.on('authenticated', () => {
    log('WhatsApp authenticated!', 'SUCCESS');
    qrCodeData = null;
});

client.on('auth_failure', (msg) => {
    log(`Authentication failed: ${msg}`, 'ERROR');
    isReady = false;
});

client.on('ready', () => {
    isReady = true;
    clientInfo = client.info;
    log(`WhatsApp ready! Connected as: ${clientInfo.pushname} (${clientInfo.wid.user})`, 'SUCCESS');
});

client.on('disconnected', (reason) => {
    log(`WhatsApp disconnected: ${reason}`, 'WARN');
    isReady = false;
    clientInfo = null;
    
    setTimeout(() => {
        log('Attempting to reconnect...');
        client.initialize();
    }, 5000);
});

client.on('message', (msg) => {
    log(`Message received from ${msg.from}: ${msg.body.substring(0, 50)}...`);
});

app.get('/status', (req, res) => {
    res.json({
        success: true,
        ready: isReady,
        qrPending: qrCodeData !== null,
        connectedAs: clientInfo ? {
            name: clientInfo.pushname,
            phone: clientInfo.wid.user
        } : null,
        uptime: process.uptime()
    });
});

app.get('/qr', (req, res) => {
    if (isReady) {
        return res.json({
            success: true,
            message: 'Already connected',
            qr: null
        });
    }
    
    if (qrCodeData) {
        return res.json({
            success: true,
            message: 'Scan QR code with WhatsApp',
            qr: qrCodeData
        });
    }
    
    res.json({
        success: false,
        message: 'QR code not available yet. Wait a moment.',
        qr: null
    });
});

app.post('/send', authMiddleware, async (req, res) => {
    const { phone, message } = req.body;
    
    if (!phone || !message) {
        return res.json({
            success: false,
            id: null,
            status: 'error',
            response: { error: 'Phone and message are required' }
        });
    }
    
    if (!isReady) {
        return res.json({
            success: false,
            id: null,
            status: 'error',
            response: { error: 'WhatsApp not ready. Please scan QR code first.' }
        });
    }
    
    try {
        let normalizedPhone = phone.toString().replace(/[\s\-\(\)\+]+/g, '');
        
        if (normalizedPhone.startsWith('0')) {
            normalizedPhone = '62' + normalizedPhone.substring(1);
        }
        
        if (!normalizedPhone.startsWith('62')) {
            normalizedPhone = '62' + normalizedPhone;
        }
        
        const chatId = normalizedPhone + '@c.us';
        
        log(`Sending message to ${chatId}...`);
        
        const result = await client.sendMessage(chatId, message);
        
        log(`Message sent successfully to ${normalizedPhone}`, 'SUCCESS');
        
        res.json({
            success: true,
            id: result.id._serialized,
            status: 'success',
            response: { 
                detail: 'Message sent successfully',
                timestamp: result.timestamp,
                to: normalizedPhone
            }
        });
    } catch (error) {
        log(`Failed to send message: ${error.message}`, 'ERROR');
        
        res.json({
            success: false,
            id: null,
            status: 'error',
            response: { error: error.message }
        });
    }
});

app.post('/send-bulk', authMiddleware, async (req, res) => {
    const { recipients, delayMs = 3000 } = req.body;
    
    if (!Array.isArray(recipients) || recipients.length === 0) {
        return res.json({
            success: false,
            error: 'Recipients array is required'
        });
    }
    
    if (!isReady) {
        return res.json({
            success: false,
            error: 'WhatsApp not ready'
        });
    }
    
    const results = [];
    
    for (const recipient of recipients) {
        const { phone, message } = recipient;
        
        try {
            let normalizedPhone = phone.toString().replace(/[\s\-\(\)\+]+/g, '');
            if (normalizedPhone.startsWith('0')) {
                normalizedPhone = '62' + normalizedPhone.substring(1);
            }
            if (!normalizedPhone.startsWith('62')) {
                normalizedPhone = '62' + normalizedPhone;
            }
            
            const chatId = normalizedPhone + '@c.us';
            const result = await client.sendMessage(chatId, message);
            
            results.push({
                phone: normalizedPhone,
                success: true,
                id: result.id._serialized
            });
            
            log(`Bulk: Sent to ${normalizedPhone}`, 'SUCCESS');
        } catch (error) {
            results.push({
                phone: recipient.phone,
                success: false,
                error: error.message
            });
            log(`Bulk: Failed to send to ${recipient.phone}: ${error.message}`, 'ERROR');
        }
        
        if (delayMs > 0) {
            await new Promise(resolve => setTimeout(resolve, delayMs));
        }
    }
    
    res.json({
        success: true,
        total: recipients.length,
        sent: results.filter(r => r.success).length,
        failed: results.filter(r => !r.success).length,
        results
    });
});

app.post('/logout', authMiddleware, async (req, res) => {
    try {
        await client.logout();
        isReady = false;
        clientInfo = null;
        res.json({ success: true, message: 'Logged out successfully' });
    } catch (error) {
        res.json({ success: false, error: error.message });
    }
});

app.get('/health', (req, res) => {
    res.json({ status: 'ok', timestamp: new Date().toISOString() });
});

log('Initializing WhatsApp client...');
client.initialize();

app.listen(PORT, () => {
    log(`WA Service running on http://localhost:${PORT}`);
    log(`API Key: ${API_KEY}`);
    log('Endpoints:');
    log('  GET  /status     - Check connection status');
    log('  GET  /qr         - Get QR code data');
    log('  GET  /health     - Health check');
    log('  POST /send       - Send single message');
    log('  POST /send-bulk  - Send bulk messages');
    log('  POST /logout     - Logout WhatsApp');
});

process.on('SIGINT', async () => {
    log('Shutting down...');
    await client.destroy();
    process.exit(0);
});
