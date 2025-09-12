<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Content Security Policy Configuration
    |--------------------------------------------------------------------------
    |
    | Configure Content Security Policy settings for the application.
    | This helps prevent XSS attacks and other code injection vulnerabilities.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | CSP Enabled
    |--------------------------------------------------------------------------
    |
    | Enable or disable CSP headers globally.
    |
    */
    'enabled' => env('CSP_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Strict Mode
    |--------------------------------------------------------------------------
    |
    | When enabled, uses stricter CSP policies without 'unsafe-eval'.
    | Note: This may break Alpine.js functionality.
    |
    */
    'strict' => env('CSP_STRICT', false),

    /*
    |--------------------------------------------------------------------------
    | Report Only Mode
    |--------------------------------------------------------------------------
    |
    | When enabled, CSP violations are reported but not blocked.
    | Useful for testing and gradual rollout.
    |
    */
    'report_only' => env('CSP_REPORT_ONLY', false),

    /*
    |--------------------------------------------------------------------------
    | Report URI
    |--------------------------------------------------------------------------
    |
    | URL to send CSP violation reports to.
    |
    */
    'report_uri' => env('CSP_REPORT_URI', '/csp-report'),

    /*
    |--------------------------------------------------------------------------
    | Policy Directives
    |--------------------------------------------------------------------------
    |
    | CSP policy directives. These can be overridden per environment.
    |
    */
    'directives' => [
        'default-src' => ["'self'"],
        'script-src' => [
            "'self'",
            // Alpine.js requires 'unsafe-eval' for expression evaluation
            env('CSP_STRICT', false) ? null : "'unsafe-eval'",
        ],
        'style-src' => [
            "'self'",
            "'unsafe-inline'", // Required for Tailwind CSS
        ],
        'img-src' => [
            "'self'",
            'data:',
            'https:',
        ],
        'font-src' => ["'self'"],
        'connect-src' => ["'self'"],
        'frame-ancestors' => ["'none'"],
        'base-uri' => ["'self'"],
        'form-action' => ["'self'"],
    ],

    /*
    |--------------------------------------------------------------------------
    | Nonce Generation
    |--------------------------------------------------------------------------
    |
    | Configure nonce generation for inline scripts and styles.
    |
    */
    'nonce' => [
        'enabled' => true,
        'length' => 16, // bytes
    ],

];