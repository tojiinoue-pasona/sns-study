<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        // Content Security Policy (CSP)
        $response->headers->set('Content-Security-Policy', 
            "default-src 'self'; " .
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net; " .
            // allow Font Awesome served via cdnjs.cloudflare.com and Google Fonts
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://fonts.bunny.net https://cdnjs.cloudflare.com; " .
            // style-src-elem explicitly allows external stylesheet elements
            "style-src-elem 'self' 'unsafe-inline' https://fonts.googleapis.com https://fonts.bunny.net https://cdnjs.cloudflare.com; " .
            // allow fonts from Google, Bunny and cdnjs/fontawesome if needed
            "font-src 'self' https://fonts.gstatic.com https://fonts.bunny.net https://cdnjs.cloudflare.com https://use.fontawesome.com; " .
            "img-src 'self' data: https:; " .
            "frame-ancestors 'none';"
        );
        
        // X-Frame-Options (クリックジャッキング対策)
        $response->headers->set('X-Frame-Options', 'DENY');
        
        // X-Content-Type-Options (MIMEスニッフィング対策)
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        
        // X-XSS-Protection (XSS保護)
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        
        // Referrer-Policy (リファラー情報の制御)
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        
        // Permissions-Policy (機能の無効化)
        $response->headers->set('Permissions-Policy', 
            'microphone=(), camera=(), geolocation=(), payment=()'
        );
        
        return $response;
    }
}
