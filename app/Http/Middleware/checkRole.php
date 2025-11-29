<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        try {
            // Kiểm tra auth an toàn
            if (!auth()->check()) {
                return redirect()->route('login')->with('error', 'Vui lòng đăng nhập.');
            }

            $user = auth()->user();
            
            // Kiểm tra user tồn tại và có role
            if (!$user || !isset($user->role)) {
                Auth::logout();
                return redirect()->route('login')->with('error', 'Phiên đăng nhập không hợp lệ.');
            }

            // Kiểm tra role
            if (!in_array($user->role, $roles)) {
                return redirect('/')->with('error', 'Bạn không có quyền truy cập trang này.');
            }

            return $next($request);

        } catch (\Exception $e) {
            // Fallback an toàn
            return redirect('/')->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}