/**
 * CSP-Safe Alpine.js Configuration
 * 
 * This module provides utilities and configurations to make Alpine.js
 * more compatible with strict Content Security Policies.
 */

/**
 * CSP-Safe Alpine.js Data Components
 * These replace common inline x-data patterns with pre-defined components
 */
export const cspSafeComponents = {
    
    // Counter component - replaces x-data="{ count: 0 }"
    counter(initialValue = 0) {
        return {
            count: initialValue,
            increment() {
                this.count++;
            },
            decrement() {
                this.count--;
            },
            reset() {
                this.count = initialValue;
            }
        };
    },

    // Toggle component - replaces x-data="{ open: false }"
    toggle(initialState = false) {
        return {
            open: initialState,
            toggle() {
                this.open = !this.open;
            },
            show() {
                this.open = true;
            },
            hide() {
                this.open = false;
            }
        };
    },

    // Form component - replaces x-data="{ form: {} }"
    form(initialData = {}) {
        return {
            form: { ...initialData },
            errors: {},
            loading: false,
            
            setField(field, value) {
                this.form[field] = value;
            },
            
            clearErrors() {
                this.errors = {};
            },
            
            setError(field, message) {
                this.errors[field] = message;
            },

            reset() {
                this.form = { ...initialData };
                this.errors = {};
                this.loading = false;
            }
        };
    },

    // List management component
    list(initialItems = []) {
        return {
            items: [...initialItems],
            selectedItem: null,
            
            add(item) {
                this.items.push(item);
            },
            
            remove(index) {
                this.items.splice(index, 1);
                if (this.selectedItem === this.items[index]) {
                    this.selectedItem = null;
                }
            },
            
            select(item) {
                this.selectedItem = item;
            },
            
            clear() {
                this.items = [];
                this.selectedItem = null;
            }
        };
    }
};

/**
 * Register CSP-safe components with Alpine.js
 */
export function registerCspSafeComponents(Alpine) {
    Object.keys(cspSafeComponents).forEach(componentName => {
        Alpine.data(componentName, cspSafeComponents[componentName]);
    });
}

/**
 * CSP-Safe event handlers
 * Pre-defined functions to avoid inline event handlers
 */
export const cspSafeHandlers = {
    
    // Prevent default and stop propagation
    preventAndStop(event) {
        event.preventDefault();
        event.stopPropagation();
    },
    
    // Submit form with loading state
    submitForm(formElement, loadingElement) {
        return async function() {
            if (loadingElement) loadingElement.style.display = 'block';
            
            try {
                // Form submission logic here
                await fetch(formElement.action, {
                    method: formElement.method || 'POST',
                    body: new FormData(formElement)
                });
            } finally {
                if (loadingElement) loadingElement.style.display = 'none';
            }
        };
    },
    
    // Debounced input handler
    debounce(func, delay = 300) {
        let timeoutId;
        return function(...args) {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => func.apply(this, args), delay);
        };
    }
};

/**
 * Initialize CSP-safe Alpine.js configuration
 */
export function initCspSafeAlpine(Alpine) {
    // Register components
    registerCspSafeComponents(Alpine);
    
    // Add global utilities
    Alpine.store('csp', {
        handlers: cspSafeHandlers,
        
        // Utility to create safe data objects
        createData(data) {
            return typeof data === 'function' ? data() : data;
        }
    });
    
    console.log('CSP-Safe Alpine.js configuration loaded');
}