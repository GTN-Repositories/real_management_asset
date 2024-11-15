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
     * Permission action mapping
     */
    private const ACTION_MAPPING = [
        'create' => 'create',
        'edit' => 'edit',
        'update' => 'edit',     // map update to edit permission
        'delete' => 'delete',
        'destroy' => 'delete',  // map destroy to delete permission
    ];

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

        $pathInfo = $this->parsePathInfo($request);
        $currentPath = $pathInfo['resource'];
        $action = $pathInfo['action'];

        // Get menu from database based on route
        $menu = $this->getMenuByRoute($currentPath);

        // Jika ada action khusus (create, edit, delete), cek permission khusus
        if ($action && isset(self::ACTION_MAPPING[$action])) {
            $permissionName = $this->getActionPermission($currentPath, $action);
            if (!$user || !$user->hasPermissionTo($permissionName)) {
                return redirect()->back()->with('error', "Forbidden: You don't have permission to {$action} {$currentPath}.");
            }
            return $next($request);
        }

        // Jika tidak ada action khusus, gunakan logika view permission yang lama
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
     * Parse path info from request
     *
     * @param Request $request
     * @return array{resource: string, action: ?string}
     */
    private function parsePathInfo(Request $request): array
    {
        $path = trim($request->path(), '/');
        $segments = explode('/', $path);

        // Default values
        $result = [
            'resource' => $segments[0],
            'action' => null
        ];

        // Handle different URL patterns
        if (count($segments) >= 2) {
            // Check for specific actions in URL
            if (in_array($segments[1], ['create', 'edit', 'delete'])) {
                $result['action'] = $segments[1];
            }
            // Check for edit/delete with ID: users/1/edit or users/1/delete
            elseif (count($segments) >= 3 && in_array($segments[2], ['edit', 'delete'])) {
                $result['action'] = $segments[2];
            }
            // Check HTTP method for updates and deletes
            elseif (is_numeric($segments[1])) {
                $result['action'] = $this->getActionFromMethod($request->method());
            }
        }

        return $result;
    }

    /**
     * Get action from HTTP method
     */
    private function getActionFromMethod(string $method): ?string
    {
        return match (strtoupper($method)) {
            'POST' => 'create',
            'PUT', 'PATCH' => 'edit',
            'DELETE' => 'delete',
            default => null,
        };
    }

    /**
     * Get the appropriate permission name based on action
     */
    private function getActionPermission(string $resource, string $action): string
    {
        $mappedAction = self::ACTION_MAPPING[strtolower($action)] ?? $action;
        return "{$resource}-{$mappedAction}";
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
