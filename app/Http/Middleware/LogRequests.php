<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $data = [
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'method' => $request->method(),
            'headers' => $request->headers->all(),
            'body' => $request->getContent(),
        ];

        // dd($data);
        Log::info('Solicitud Recibida: ', $data);

        return $next($request);
    }

    public function terminate(Request $request, Response $response)
    {
        Log::info('Respuesta enviada: ', [
            'status' => $response->getStatusCode(),
            'content' => $response->getContent(),
        ]);
    }
}
