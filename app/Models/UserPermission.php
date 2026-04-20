<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPermission extends Model
{
    protected $table = 'user_permissions';

    public $timestamps = false;

    protected $fillable = ['user_id', 'branch', 'route_name'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function hasAnyRowForUser(int $userId): bool
    {
        if ($userId <= 0) {
            return false;
        }

        return static::where('user_id', $userId)->exists();
    }

    /**
     * Same branch rules as {@see RolePermission::hasAccess()} but keyed by user.
     */
    public static function hasAccess(int $userId, string $routeName, ?string $userBranch = null): bool
    {
        if ($userId <= 0) {
            return false;
        }

        $userBranch = RolePermission::normalizeBranch($userBranch ?? (string) (session('user_branch') ?? ''));

        if ($userBranch !== '') {
            $branchLower = mb_strtolower($userBranch);
            $branchCount = static::where('user_id', $userId)
                ->whereRaw('LOWER(branch) = ?', [$branchLower])
                ->count();
            if ($branchCount > 0) {
                $allowed = static::where('user_id', $userId)
                    ->whereRaw('LOWER(branch) = ?', [$branchLower])
                    ->pluck('route_name')
                    ->toArray();

                return in_array($routeName, $allowed, true);
            }
        }

        $global = static::where('user_id', $userId)->where('branch', '')->pluck('route_name')->toArray();
        if (!empty($global)) {
            return in_array($routeName, $global, true);
        }

        return false;
    }

    /**
     * @return list<string>
     */
    public static function allowedRoutesForUser(int $userId, ?string $userBranch = null): array
    {
        if ($userId <= 0) {
            return [];
        }

        $userBranch = RolePermission::normalizeBranch($userBranch ?? (string) (session('user_branch') ?? ''));

        if ($userBranch !== '') {
            $branchLower = mb_strtolower($userBranch);
            $branchRoutes = static::where('user_id', $userId)
                ->whereRaw('LOWER(branch) = ?', [$branchLower])
                ->pluck('route_name')
                ->toArray();
            if (!empty($branchRoutes)) {
                return array_values(array_unique($branchRoutes));
            }
        }

        return static::where('user_id', $userId)->where('branch', '')->pluck('route_name')->toArray();
    }

    /**
     * @return list<string>
     */
    public static function routeNamesForJobModule(string $jobKey): array
    {
        $modules = config('permissions.job_ui_modules', []);
        if (!isset($modules[$jobKey]) || !is_array($modules[$jobKey])) {
            return [];
        }

        $m = $modules[$jobKey];
        $out = [];
        foreach (['sidebar', 'card', 'buttons'] as $k) {
            if (!empty($m[$k]) && is_array($m[$k])) {
                foreach ($m[$k] as $r) {
                    if (is_string($r) && $r !== '') {
                        $out[] = $r;
                    }
                }
            }
        }

        return array_values(array_unique($out));
    }
}
