<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    protected $table = 'role_permissions';

    public $timestamps = false;

    protected $fillable = ['role', 'route_name'];

    public static function hasAccess(string $role, string $routeName): bool
    {
        $allowed = static::where('role', $role)->pluck('route_name')->toArray();
        if (empty($allowed)) {
            return true;
        }
        return in_array($routeName, $allowed, true);
    }

    public static function allowedRoutesForRole(string $role): array
    {
        return static::where('role', $role)->pluck('route_name')->toArray();
    }
}
