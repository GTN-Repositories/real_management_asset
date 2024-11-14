<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use App\Models\Menu;
use Symfony\Component\HttpFoundation\Response;

class CheckMenuPermission
{
    /**
     * Cache duration in minutes
     */
    private const CACHE_DURATION = 60;

    /**
     * Handle the incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Jika user adalah superAdmin, langsung berikan akses
        if ($this->isSuperAdmin($user)) {
            return $next($request);
        }

        $currentPath = $this->getCurrentPath($request);

        // Get menu from database based on route
        $menu = $this->getMenuByRoute($currentPath);

        if ($menu) {
            // Jika menu ditemukan, gunakan permission berdasarkan nama menu
            $permissionName = 'view-' . strtolower(str_replace(' ', '-', $menu->name));

            if (!$user || !$user->hasPermissionTo($permissionName)) {
                return redirect()->back()->with('error', 'Forbidden: You do not have permission.');
            }
        } else {
            // Jika menu tidak ditemukan, cek permission berdasarkan URL path
            $pathBasedPermission = 'view-' . strtolower(str_replace('/', '-', $currentPath));

            if (!$user || !$user->hasPermissionTo($pathBasedPermission)) {
                return redirect()->back()->with('error', 'Forbidden: You do not have permission.');
            }
        }

        return $next($request);
    }

    /**
     * Check if user is superAdmin
     *
     * @param mixed $user
     * @return bool
     */
    private function isSuperAdmin($user): bool
    {
        if (!$user) {
            return false;
        }

        // Cara 1: Menggunakan role name
        if ($user->hasRole('superAdmin')) {
            return true;
        }

        // Cara 2: Menggunakan ID role (uncomment jika menggunakan cara ini)
        // return $user->roles->contains('id', 1); // Asumsi role_id 1 adalah superAdmin

        // Cara 3: Menggunakan field is_super_admin (uncomment jika menggunakan cara ini)
        // return $user->is_super_admin === true;

        return false;
    }

    /**
     * Get the current path from the request
     */
    private function getCurrentPath(Request $request): string
    {
        // Remove leading slash and get first segment of the URL
        $path = trim($request->path(), '/');
        return explode('/', $path)[0];
    }

    /**
     * Get menu by route with caching
     */
    private function getMenuByRoute(string $route): ?Menu
    {
        $cacheKey = 'menu_route_' . $route;

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($route) {
            return Menu::where('route', $route)->first();
        });
    }
}
