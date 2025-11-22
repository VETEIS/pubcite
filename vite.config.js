import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { networkInterfaces } from 'os';

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

// Use localhost for HMR by default to prevent CORS issues when accessing via localhost
// Set VITE_HMR_HOST environment variable to use network IP if needed
// Example: VITE_HMR_HOST=192.168.254.183 npm run dev
const hmrHost = process.env.VITE_HMR_HOST || 'localhost';
const localIP = getLocalIP();

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    server: {
        host: '0.0.0.0',
        port: 5173,
        cors: {
            origin: [
                'http://localhost:8000',
                `http://${localIP}:8000`,
                `http://${localIP}:5173`,
            ],
            credentials: true,
        },
        hmr: {
            host: hmrHost,
            // Preserve localStorage during HMR
            overlay: false,
        },
    },
    // Preserve localStorage during development
    define: {
        __VUE_PROD_DEVTOOLS__: false,
    },
});
