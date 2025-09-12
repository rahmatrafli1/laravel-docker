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

        // Check if CSP is enabled
        if (!config('csp.enabled', true)) {
            return $response;
        }

        // Generate a nonce for inline scripts
        $nonce = $this->generateNonce();
        $request->attributes->set('csp_nonce', $nonce);

        // Build CSP policy
        $cspPolicy = $this->buildCspPolicy($nonce);

        // Apply CSP headers
        if (config('csp.report_only', false)) {
            $response->headers->set('Content-Security-Policy-Report-Only', $cspPolicy);
        } else {
            $response->headers->set('Content-Security-Policy', $cspPolicy);
        }

        return $response;
    }

    /**
     * Generate a cryptographically secure nonce
     */
    private function generateNonce()
    {
        $length = config('csp.nonce.length', 16);
        return base64_encode(random_bytes($length));
    }

    /**
     * Build the CSP policy string
     */
    private function buildCspPolicy($nonce)
    {
        $directives = config('csp.directives', []);
        $policy = [];

        foreach ($directives as $directive => $sources) {
            $sources = array_filter($sources); // Remove null values
            
            // Add nonce to script-src and style-src if enabled
            if (in_array($directive, ['script-src', 'style-src']) && config('csp.nonce.enabled', true)) {
                $sources[] = "'nonce-{$nonce}'";
            }
            
            $policy[] = $directive . ' ' . implode(' ', $sources);
        }

        // Add report URI if configured
        if ($reportUri = config('csp.report_uri')) {
            $policy[] = 'report-uri ' . $reportUri;
        }

        return implode('; ', $policy);
    }

}