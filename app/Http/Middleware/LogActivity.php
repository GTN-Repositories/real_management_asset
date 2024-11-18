<?php

namespace App\Http\Middleware;

use App\Models\LogActivity as ModelsLogActivity;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Symfony\Component\HttpFoundation\Response;

class LogActivity
{
    /**
     * Routes that are related to asset operations
     */
    protected $assetRoutes = [
        'asset.show',
        'asset.update',
        'asset.destroy',
        'asset.note',
        'asset.appreciation-data',
        'asset.depreciation-data',
        'asset.statusData',
        'asset.updateFiles'
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $assetId = $this->getAssetId($request);

            $selectedProjectId = session('selected_project_id');

            if ($selectedProjectId) {
                $selectedProjectId = Crypt::decrypt($selectedProjectId);
            }

            ModelsLogActivity::create([
                'user_id' => Auth::user()->id,
                'page' => $request->path(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'asset_id' => $assetId,
                'project_id' => $selectedProjectId,
            ]);
        }

        return $next($request);
    }

    /**
     * Get asset_id from request based on different scenarios
     */
    protected function getAssetId(Request $request): ?int
    {
        $routeName = $request->route()->getName();

        if (!in_array($routeName, $this->assetRoutes)) {
            return null;
        }

        return $this->getAssetIdFromRequest($request);
    }

    /**
     * Extract and decrypt asset_id from different request sources
     */
    protected function getAssetIdFromRequest(Request $request): ?int
    {
        try {
            if ($request->route('asset')) {
                return $this->decryptId($request->route('asset'));
            }

            if ($request->query('asset_id')) {
                return $this->decryptId($request->query('asset_id'));
            }

            if ($request->input('asset_id')) {
                return $this->decryptId($request->input('asset_id'));
            }

            if ($request->route('id')) {
                return $this->decryptId($request->route('id'));
            }

            return null;
        } catch (\Exception $e) {
            report(new Exception('Failed to decrypt asset_id in LogActivity middleware: ' . $e->getMessage()));
            return null;
        }
    }

    /**
     * Safely decrypt an encrypted ID
     */
    protected function decryptId($encryptedId): ?int
    {
        if (empty($encryptedId)) {
            return null;
        }

        try {
            return (int) Crypt::decrypt($encryptedId);
        } catch (\Exception $e) {
            if (is_numeric($encryptedId)) {
                return (int) $encryptedId;
            }
            throw $e;
        }
    }
}
