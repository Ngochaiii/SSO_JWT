<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyJsonRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Kiểm tra xem yêu cầu có chứa dữ liệu JSON không

        if ($request->isJson()) {
            // Nếu có, tiếp tục xử lý yêu cầu
            return $next($request);
        }

        // Nếu không, trả về lỗi 415 (Unsupported Media Type)
        return response()->json(['error' => 'Unsupported Media Type'], 415);
    }
}
