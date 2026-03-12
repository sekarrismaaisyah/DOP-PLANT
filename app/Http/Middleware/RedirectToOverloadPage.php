<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectToOverloadPage
{
    /**
     * Handle an incoming request.
     * Semua route web akan diarahkan ke halaman 404 (system overload).
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        return response()->view('404')->setStatusCode(503);
    }
}
