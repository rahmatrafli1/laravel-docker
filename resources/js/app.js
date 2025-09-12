require('./bootstrap');

import Alpine from 'alpinejs';
import { initCspSafeAlpine } from './csp-safe-alpine';

// Initialize CSP-safe Alpine.js configuration
initCspSafeAlpine(Alpine);

// Store Alpine globally
window.Alpine = Alpine;

// Start Alpine.js
Alpine.start();

// Add debugging information
console.log('Alpine.js initialized with CSP-safe configuration');

// Listen for Alpine events
document.addEventListener('alpine:init', () => {
    console.log('Alpine.js fully initialized and ready');
});

document.addEventListener('alpine:initialized', () => {
    console.log('All Alpine.js components have been initialized');
});
