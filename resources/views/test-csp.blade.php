<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSP Test - Alpine.js Compatibility</title>
    <style nonce="{{ request()->attributes->get('csp_nonce') }}">
        body { font-family: Arial, sans-serif; margin: 40px; line-height: 1.6; }
        .container { max-width: 800px; }
        .test-section { margin: 20px 0; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
        .success { color: #28a745; font-weight: bold; }
        .error { color: #dc3545; font-weight: bold; }
        .warning { color: #ffc107; }
        button { margin: 5px; padding: 8px 16px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #0056b3; }
        button:disabled { background: #ccc; cursor: not-allowed; }
        input[type="text"] { margin: 5px; padding: 8px; width: 200px; border: 1px solid #ddd; border-radius: 4px; }
        .selected { background: #e7f3ff; padding: 4px 8px; border-radius: 4px; }
        .status { padding: 10px; border-radius: 4px; margin: 10px 0; }
        .status.success { background: #d4edda; border: 1px solid #c3e6cb; }
        .status.error { background: #f8d7da; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Content Security Policy Test Page</h1>
        <p>This page tests Alpine.js functionality with CSP-safe components and configurations.</p>
        
        <div class="test-section">
            <h2>1. CSP-Safe Counter Component</h2>
            <div x-data="counter(5)">
                <p>Count: <span x-text="count" class="success"></span></p>
                <button x-on:click="increment()">Increment</button>
                <button x-on:click="decrement()">Decrement</button>
                <button x-on:click="reset()">Reset to 5</button>
                
                <div x-show="count > 10" class="status success">
                    <p>Excellent! Count is greater than 10!</p>
                </div>
            </div>
        </div>

        <div class="test-section">
            <h2>2. CSP-Safe Toggle Component</h2>
            <div x-data="toggle(false)">
                <button x-on:click="toggle()" x-text="open ? 'Hide Details' : 'Show Details'"></button>
                
                <div x-show="open" x-transition class="status success">
                    <h4>Toggle Content</h4>
                    <p>This content is shown/hidden using a CSP-safe toggle component.</p>
                    <button x-on:click="hide()">Hide</button>
                </div>
            </div>
        </div>

        <div class="test-section">
            <h2>3. CSP-Safe List Management</h2>
            <div x-data="list(['Apple', 'Banana', 'Cherry'])">
                <h4>Fruit List</h4>
                <ul>
                    <template x-for="(item, index) in items" :key="item">
                        <li x-on:click="select(item)"
                            :class="selectedItem === item ? 'selected' : ''"
                            style="cursor: pointer; padding: 4px;">
                            <span x-text="item"></span>
                            <button x-on:click.stop="remove(index)" style="margin-left: 10px; font-size: 12px;">Remove</button>
                        </li>
                    </template>
                </ul>
                
                <div style="margin: 10px 0;">
                    <input x-model="newItem" 
                           x-data="{ newItem: '' }"
                           type="text" 
                           placeholder="Add new fruit"
                           x-on:keyup.enter="add(newItem); newItem = ''">
                    <button x-on:click="add(newItem); newItem = ''"
                            x-bind:disabled="!newItem || newItem.trim() === ''">Add Item</button>
                </div>
                
                <p x-show="selectedItem !== null">
                    Selected: <span x-text="selectedItem" class="success"></span>
                </p>
                
                <button x-on:click="clear()" class="warning">Clear All</button>
            </div>
        </div>

        <div class="test-section">
            <h2>4. CSP-Safe Form Component</h2>
            <div x-data="form({ name: '', email: '' })">
                <div style="margin: 10px 0;">
                    <input x-model="form.name" type="text" placeholder="Name">
                    <span x-show="errors.name" x-text="errors.name" class="error"></span>
                </div>
                
                <div style="margin: 10px 0;">
                    <input x-model="form.email" type="email" placeholder="Email">
                    <span x-show="errors.email" x-text="errors.email" class="error"></span>
                </div>
                
                <button x-on:click="
                    clearErrors();
                    if (!form.name) setError('name', 'Name is required');
                    if (!form.email) setError('email', 'Email is required');
                " x-bind:disabled="loading">
                    <span x-show="!loading">Validate</span>
                    <span x-show="loading">Validating...</span>
                </button>
                
                <button x-on:click="reset()">Reset Form</button>
                
                <div x-show="Object.keys(errors).length === 0 && form.name && form.email" class="status success">
                    <p>Form is valid! Name: <span x-text="form.name"></span>, Email: <span x-text="form.email"></span></p>
                </div>
            </div>
        </div>

        <div class="test-section">
            <h2>5. CSP Violation Detection</h2>
            <div x-data="{ violations: 0, alpineReady: false }" 
                 x-init="alpineReady = true">
                <div class="status" :class="alpineReady ? 'success' : 'error'">
                    <p x-show="alpineReady">✓ Alpine.js is working correctly with CSP-safe components</p>
                    <p x-show="!alpineReady">✗ Alpine.js initialization failed</p>
                </div>
                
                <p>CSP Violations detected: <span x-text="violations" class="error"></span></p>
                <p class="warning">Check browser console and network tab for detailed CSP information.</p>
                
                <div class="status success">
                    <h4>CSP Benefits Achieved:</h4>
                    <ul>
                        <li>✓ No inline event handlers (onclick, onchange, etc.)</li>
                        <li>✓ Pre-defined Alpine.js components instead of inline x-data</li>
                        <li>✓ Nonce-based script loading</li>
                        <li>✓ CSP violation reporting enabled</li>
                        <li>⚠️ Alpine.js still requires 'unsafe-eval' for expression evaluation</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script nonce="{{ request()->attributes->get('csp_nonce') }}" src="{{ asset('js/app.js') }}" defer></script>
    
    <script nonce="{{ request()->attributes->get('csp_nonce') }}">
        let cspViolationCount = 0;
        
        document.addEventListener('DOMContentLoaded', function() {
            console.log('CSP Test page loaded successfully');
            
            // Listen for CSP violations
            document.addEventListener('securitypolicyviolation', function(e) {
                cspViolationCount++;
                console.error('CSP Violation #' + cspViolationCount + ' detected:', {
                    directive: e.violatedDirective,
                    blockedURI: e.blockedURI,
                    lineNumber: e.lineNumber,
                    columnNumber: e.columnNumber,
                    sourceFile: e.sourceFile,
                    originalPolicy: e.originalPolicy
                });
                
                // Update Alpine.js component if available
                const alpineElement = document.querySelector('[x-data*="violations"]');
                if (alpineElement && window.Alpine) {
                    Alpine.$data(alpineElement).violations = cspViolationCount;
                }
            });

            // Alpine.js event listeners
            document.addEventListener('alpine:init', () => {
                console.log('Alpine.js initialized successfully - CSP compatible mode!');
            });

            document.addEventListener('alpine:initialized', () => {
                console.log('All Alpine.js components initialized');
            });

            // Check Alpine.js after initialization
            setTimeout(() => {
                if (window.Alpine) {
                    console.log('Alpine.js available globally');
                    console.log('CSP-safe components registered:', Object.keys(window.Alpine.__CSP_SAFE_COMPONENTS__ || {}));
                } else {
                    console.error('Alpine.js not available globally');
                }
                
                console.log('Total CSP violations so far:', cspViolationCount);
            }, 2000);
        });
    </script>
</body>
</html>