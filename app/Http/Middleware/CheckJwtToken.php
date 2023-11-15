<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;

class CheckJwtToken
{
    public function handle($request, Closure $next)
    {

        if (Session::has('jwt_token')) {
            // Token tồn tại trong session
            // Thực hiện các hành động liên quan đến token
            return $next($request);
        }

        // Token không tồn tại trong session, có thể chuyển hướng đến trang đăng nhập
        return redirect()->route('login.home');
    }
}
