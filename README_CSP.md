# Content Security Policy (CSP) Setup Guide

## Quick Start

1. **Environment Configuration**
   Add to your `.env` file:
   ```
   CSP_ENABLED=true
   CSP_STRICT=false
   CSP_REPORT_ONLY=false
   CSP_REPORT_URI=/csp-report
   ```

2. **Test the Implementation**
   Visit `/test-csp` to validate CSP functionality with Alpine.js

3. **Monitor CSP Violations**
   Check browser console or application logs for CSP reports

## Configuration Options

### Environment Variables

| Variable | Default | Description |
|----------|---------|-------------|
| `CSP_ENABLED` | `true` | Enable/disable CSP headers globally |
| `CSP_STRICT` | `false` | Use strict CSP (may break Alpine.js) |
| `CSP_REPORT_ONLY` | `false` | Report violations without blocking |
| `CSP_REPORT_URI` | `/csp-report` | Endpoint for violation reports |

### CSP Modes

#### 1. Development Mode (Recommended)
```env
CSP_ENABLED=true
CSP_STRICT=false
CSP_REPORT_ONLY=false
```
- Allows Alpine.js to work with `'unsafe-eval'`
- Reports violations for monitoring
- Secure baseline protection

#### 2. Testing Mode
```env
CSP_ENABLED=true
CSP_STRICT=false
CSP_REPORT_ONLY=true
```
- Reports violations without blocking
- Useful for testing changes
- Gradual rollout approach

#### 3. Strict Mode (Advanced)
```env
CSP_ENABLED=true
CSP_STRICT=true
CSP_REPORT_ONLY=false
```
- ⚠️ **May break Alpine.js functionality**
- Maximum security
- Requires Alpine.js alternatives

## Alpine.js Compatibility

### Current Status
✅ **Alpine.js 3.14.1 works with CSP** when `'unsafe-eval'` is allowed

❌ **Alpine.js requires `'unsafe-eval'`** due to `AsyncFunction` usage in expression evaluation

### CSP-Safe Component Usage

Instead of inline `x-data`:
```html
<!-- ❌ Avoid: Inline data objects -->
<div x-data="{ count: 0, increment() { this.count++ } }">

<!-- ✅ Recommended: Pre-defined components -->
<div x-data="counter(0)">
  <button x-on:click="increment()">+</button>
</div>
```

### Available CSP-Safe Components

- `counter(initial)` - Counter with increment/decrement/reset
- `toggle(initial)` - Show/hide functionality
- `form(data)` - Form management with validation
- `list(items)` - Array/list manipulation

## Security Benefits

### Implemented Protections
- ✅ Nonce-based inline script protection
- ✅ Same-origin resource restrictions
- ✅ Frame ancestors protection
- ✅ Base URI restrictions  
- ✅ Form action restrictions
- ✅ CSP violation reporting

### Risk Mitigation
- 🔒 **XSS Prevention**: Blocks unauthorized script execution
- 🔒 **Code Injection**: Prevents dynamic code evaluation (except Alpine.js)
- 🔒 **Clickjacking**: Frame protection prevents embedding
- 📊 **Monitoring**: Violation reports for security insights

## Troubleshooting

### Common Issues

#### 1. Alpine.js Not Working
**Symptoms**: Alpine.js components not initializing
**Solution**: Ensure `CSP_STRICT=false` in development

#### 2. CSP Violations in Console
**Symptoms**: Browser console shows CSP violations
**Check**: 
- Are you using inline event handlers? (use Alpine.js directives)
- Are scripts loaded without nonces? (add nonce attribute)
- Third-party scripts? (add to allowed sources)

#### 3. Styles Not Loading
**Symptoms**: CSS styles not applied
**Solution**: Tailwind CSS requires `'unsafe-inline'` for styles (already configured)

### Debug Steps

1. **Check CSP Headers**
   ```bash
   curl -I http://localhost/test-csp
   ```

2. **Monitor Violations**
   - Browser Developer Tools > Console
   - Network tab for blocked resources

3. **Test Different Modes**
   - Start with `CSP_REPORT_ONLY=true`
   - Gradually tighten restrictions

## Migration Path

### Phase 1: Implementation (Current)
- CSP middleware active
- Alpine.js compatible mode
- Violation monitoring enabled

### Phase 2: Optimization
- Increase CSP-safe component usage
- Reduce inline JavaScript
- Monitor and fix violations

### Phase 3: Hardening (Future)
- Consider Alpine.js alternatives
- Implement strict CSP
- Hash-based script validation

## Alternative Frontend Solutions

For stricter CSP requirements, consider:

### 1. Vue.js 3 with CSP Build
- CSP-compatible expression evaluation
- Better TypeScript support
- Server-side rendering

### 2. Vanilla JavaScript + Web Components
- No expression evaluation
- Maximum CSP compatibility
- Progressive enhancement

### 3. htmx + Alpine.js Minimal
- Reduce Alpine.js usage
- Server-driven interactions
- CSP-friendly patterns

## Production Deployment

### Recommended Settings
```env
CSP_ENABLED=true
CSP_STRICT=false
CSP_REPORT_ONLY=false
CSP_REPORT_URI=/csp-report
```

### Additional Security Headers
Consider adding to your web server configuration:
```
Strict-Transport-Security: max-age=31536000; includeSubDomains
X-Content-Type-Options: nosniff
X-Frame-Options: DENY
Referrer-Policy: strict-origin-when-cross-origin
```

## Support

For CSP-related issues:
1. Check `CSP_IMPLEMENTATION.md` for technical details
2. Test with `/test-csp` route
3. Review browser console for specific violations
4. Adjust configuration based on application needs