# Content Security Policy (CSP) Implementation

This document describes the CSP implementation for the Laravel application to resolve `eval()` blocking issues with Alpine.js while maintaining security.

## Problem Analysis

The original issue was caused by Alpine.js using `new AsyncFunction()` which is equivalent to `eval()` and violates strict Content Security Policies. This occurred in the `generateFunctionFromString` function within Alpine.js's expression evaluator.

## Solution Overview

### 1. CSP Middleware Implementation

**File**: `app/Http/Middleware/ContentSecurityPolicy.php`

- **Development Mode**: Allows `'unsafe-eval'` for Alpine.js functionality while enabling reporting
- **Production Mode**: More restrictive policy with hash-based script validation
- **Report-Only Mode**: Implemented for gradual deployment and monitoring

Key Features:
- Nonce-based inline script security
- Environment-aware policy switching
- CSP violation reporting endpoint
- Script integrity hashing for production

### 2. CSP-Safe Alpine.js Components

**File**: `resources/js/csp-safe-alpine.js`

Pre-defined Alpine.js components to reduce reliance on inline `x-data` patterns:

- `counter()` - Replaces common counter patterns
- `toggle()` - Handles show/hide functionality
- `form()` - Form management with validation
- `list()` - Array/list manipulation

These components minimize the use of Alpine.js's expression evaluator, reducing CSP violations.

### 3. CSP Configuration Strategy

#### Current Implementation (Development-Friendly)
```
Content-Security-Policy: default-src 'self'; script-src 'self' 'nonce-{random}' 'unsafe-eval' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; ...
```

#### Recommended Production Configuration
```
Content-Security-Policy: default-src 'self'; script-src 'self' 'nonce-{random}' 'sha256-{app-js-hash}'; style-src 'self' 'unsafe-inline'; ...
```

## Implementation Details

### CSP Headers Applied

| Directive | Development | Production | Purpose |
|-----------|------------|------------|---------|
| `default-src` | `'self'` | `'self'` | Default source for all resources |
| `script-src` | `'self' 'nonce-X' 'unsafe-eval'` | `'self' 'nonce-X' 'sha256-X'` | Script execution policy |
| `style-src` | `'self' 'unsafe-inline'` | `'self' 'unsafe-inline'` | CSS styling (Tailwind needs inline) |
| `img-src` | `'self' data: https:` | `'self' data: https:` | Image sources |
| `report-uri` | `/csp-report` | `/csp-report` | Violation reporting |

### Alpine.js Compatibility

1. **Expression Evaluation**: Alpine.js requires `'unsafe-eval'` for its `AsyncFunction` constructor
2. **Nonce Protection**: All inline scripts use CSP nonces for security
3. **Component Pre-definition**: Reduces runtime expression evaluation needs

### Testing Implementation

**Route**: `/test-csp`  
**View**: `resources/views/test-csp.blade.php`

Comprehensive test page that validates:
- Alpine.js functionality with CSP policies
- CSP-safe component usage
- Violation detection and reporting
- Browser compatibility

## Security Benefits Achieved

✅ **Implemented Security Measures:**
- Nonce-based inline script protection
- Restricted resource loading to same-origin
- Frame ancestors protection (`frame-ancestors 'none'`)
- Base URI restrictions
- Form action restrictions
- CSP violation monitoring and reporting

⚠️ **Remaining Considerations:**
- Alpine.js still requires `'unsafe-eval'` for full functionality
- Consider alternative frameworks if stricter CSP is required
- Production deployment should use hash-based script validation

## Migration Path

### Phase 1: Development (Current)
- Permissive CSP with `'unsafe-eval'`
- Full Alpine.js functionality
- Violation monitoring enabled

### Phase 2: Hardening
- Remove `'unsafe-eval'` where possible
- Increase usage of CSP-safe components
- Hash-based script validation

### Phase 3: Production
- Strict CSP without `'unsafe-eval'`
- Alternative to Alpine.js if needed
- Full CSP compliance

## Usage Examples

### CSP-Safe Component Usage

Instead of:
```html
<div x-data="{ count: 0, increment() { this.count++ } }">
```

Use:
```html
<div x-data="counter(0)">
  <button x-on:click="increment()">+</button>
</div>
```

### Proper Script Loading
```html
<script nonce="{{ request()->attributes->get('csp_nonce') }}" src="{{ asset('js/app.js') }}" defer></script>
```

## Monitoring and Debugging

1. **CSP Reports**: Check `/csp-report` endpoint logs
2. **Browser Console**: Monitor for CSP violations
3. **Test Page**: Visit `/test-csp` for functionality validation

## Files Modified

1. `app/Http/Kernel.php` - Registered CSP middleware
2. `app/Http/Middleware/ContentSecurityPolicy.php` - CSP implementation
3. `resources/js/app.js` - Alpine.js configuration
4. `resources/js/csp-safe-alpine.js` - CSP-safe components
5. `resources/views/test-csp.blade.php` - Testing interface
6. `routes/web.php` - Test route and CSP reporting

## Browser Compatibility

- **Chrome/Edge**: Full CSP 3.0 support
- **Firefox**: Full CSP 3.0 support  
- **Safari**: CSP 2.0 support (nonce-based policies work)
- **IE**: Limited CSP support (consider polyfills if needed)

## Future Improvements

1. **Alpine.js Alternatives**: Consider Vue 3 or React with stricter CSP
2. **Server-Side Rendering**: Reduce client-side evaluation needs
3. **Static Analysis**: Build-time CSP policy generation
4. **Progressive Enhancement**: Graceful degradation without JavaScript