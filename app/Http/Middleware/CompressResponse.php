<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CompressResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);          
        // Check if the client supports gzip compression
        if (strpos($request->header('Accept-Encoding'), 'gzip') !== false) {
            // Compress the response content using gzip
            $compressed = gzencode($response->getContent(), 9);

            // Create a new response with compressed content
            $response = new Response($compressed);

            // Set the appropriate headers for gzip compression
            $response->headers->set('Content-Encoding', 'gzip');
            $response->headers->set('Vary', 'Accept-Encoding');
        }        
        return $response;
    }
}
