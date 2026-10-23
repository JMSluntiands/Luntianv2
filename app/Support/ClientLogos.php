<?php

namespace App\Support;

use App\Models\RolePermission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

class ClientLogos
{
    /** @var array<string, object>|null */
    private static ?array $cache = null;

    /**
     * Client modules shown in the sidebar, in sidebar order.
     *
     * @return array<string, string>
     */
    public static function modules(): array
    {
        return [
            'lbs' => 'LBS',
            'general_assembly' => RolePermission::GENERIC_ASSESSMENT_NAME,
            'luntian' => 'Luntian',
            'bph' => 'BPH',
            'efficient_living' => 'Efficient Living',
            'bluinq' => 'BluInq',
            'amt' => 'A&M',
            'fyrs' => 'Redistar',
        ];
    }

    /**
     * @return array<string, object>
     */
    public static function all(): array
    {
        if (self::$cache !== null) {
            return self::$cache;
        }

        try {
            self::$cache = Schema::hasTable('client_logos')
                ? DB::table('client_logos')->get()->keyBy('module_key')->all()
                : [];
        } catch (Throwable) {
            self::$cache = [];
        }

        return self::$cache;
    }

    public static function url(string $moduleKey): ?string
    {
        $logo = self::all()[$moduleKey] ?? null;
        if (! $logo) {
            return null;
        }

        $version = $logo->updated_at ? strtotime((string) $logo->updated_at) : 0;

        return route('client_logo.image', ['module' => $moduleKey, 'v' => $version]);
    }

    public static function forget(): void
    {
        self::$cache = null;
    }
}
