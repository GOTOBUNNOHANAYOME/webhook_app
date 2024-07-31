<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BasicAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $authorization_header = $request->header('Authorization');

        if ($authorization_header && preg_match('/^Basic\s+(.*)$/i', $authorization_header, $matches)) {
            $credentials = base64_decode($matches[1]);
            list($user_name, $password) = explode(':', $credentials, 2);

            if ($user_name === config('app.basic.user') && $password === config('app.basic.password')) {
                return $next($request);
            }
        }

        abort(401);
    }
}
