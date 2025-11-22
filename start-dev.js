const { execSync } = require('child_process');
const { networkInterfaces } = require('os');

// Function to get local IP address
function getLocalIP() {
    const nets = networkInterfaces();
    for (const name of Object.keys(nets)) {
        for (const net of nets[name]) {
            // Skip over non-IPv4 and internal (i.e. 127.0.0.1) addresses
            if (net.family === 'IPv4' && !net.internal) {
                return net.address;
            }
        }
    }
    return 'localhost';
}

const currentIP = getLocalIP();
console.log(`🚀 Starting Vite development server...`);
console.log(`📱 Your local IP is: ${currentIP}`);
console.log(`🌐 Access your app at: http://${currentIP}:8000`);
console.log(`⚡ Vite dev server at: http://${currentIP}:5173`);
console.log(`\nPress Ctrl+C to stop the server\n`);

// Start Vite development server
try {
    execSync('npm run dev', { stdio: 'inherit' });
} catch (error) {
    console.error('Failed to start Vite server:', error.message);
} 