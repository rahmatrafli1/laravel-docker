<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ContentSecurityPolicy
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Generate a nonce for inline scripts
        $nonce = base64_encode(random_bytes(16));
        $request->attributes->set('csp_nonce', $nonce);

        // Get the hash of the main app.js file for integrity
        $appJsHash = $this->getScriptHash('/js/app.js');
        
        $isDev = config('app.debug', false);
        
        if ($isDev) {
            // Development CSP - allows eval for Alpine.js but with reporting
            $csp = [
                "default-src 'self'",
                "script-src 'self' 'nonce-{$nonce}' 'unsafe-eval'",
                "style-src 'self' 'unsafe-inline'",
                "img-src 'self' data: https:",
                "font-src 'self'",
                "connect-src 'self'",
                "frame-ancestors 'none'",
                "base-uri 'self'",
                "form-action 'self'",
                "report-uri /csp-report"
            ];
        } else {
            // Production CSP - more restrictive but allows necessary Alpine.js functionality
            $csp = [
                "default-src 'self'",
                "script-src 'self' 'nonce-{$nonce}'" . ($appJsHash ? " 'sha256-{$appJsHash}'" : ''),
                "style-src 'self' 'unsafe-inline'", // Tailwind CSS needs this
                "img-src 'self' data: https:",
                "font-src 'self'",
                "connect-src 'self'",
                "frame-ancestors 'none'",
                "base-uri 'self'",
                "form-action 'self'",
                "report-uri /csp-report"
            ];
        }

        // Set both CSP and CSP Report Only for gradual deployment
        $response->headers->set('Content-Security-Policy-Report-Only', implode('; ', $csp));
        
        // For now, use a permissive CSP to avoid breaking functionality
        $permissiveCsp = [
            "default-src 'self'",
            "script-src 'self' 'nonce-{$nonce}' 'unsafe-eval' 'unsafe-inline'",
            "style-src 'self' 'unsafe-inline'",
            "img-src 'self' data: https:",
            "font-src 'self'",
            "connect-src 'self'",
            "frame-ancestors 'none'",
            "base-uri 'self'",
            "form-action 'self'"
        ];
        
        $response->headers->set('Content-Security-Policy', implode('; ', $permissiveCsp));

        return $response;
    }

    /**
     * Get the SHA256 hash of a script file for CSP
     */
    private function getScriptHash($scriptPath)
    {
        $fullPath = public_path($scriptPath);
        
        if (!file_exists($fullPath)) {
            return null;
        }
        
        $content = file_get_contents($fullPath);
        return base64_encode(hash('sha256', $content, true));
    }
}