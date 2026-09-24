<?php

namespace App\Models;

use App\Support\AddJobModules;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class JobModuleClient extends Model
{
    protected $table = 'job_module_clients';

    protected $fillable = ['module', 'client_code'];

    /** @var array<string, string>|null */
    private static ?array $codeCache = null;

    /** @return array<string, string> module => default client_code */
    public static function defaultCodes(): array
    {
        return [
            'lbs' => 'LBS01',
            'general_assembly' => 'GENEA01',
            'bph' => 'BPH01',
            'efficient_living' => 'EL01',
            'luntian' => 'LT01',
            'bluinq' => 'BLQ01',
            'amt' => 'AMT01',
            'fyrs' => 'FYRS01',
            'csp' => 'CSP01',
            'nh' => 'NH01',
            'leading_energy' => 'LE01',
            'lc_home_builder' => 'LC_HB01',
        ];
    }

    /** Historical codes for the same vertical (used when retagging Job Requests). */
    public static function aliasCodes(string $module): array
    {
        return match ($module) {
            'general_assembly' => ['GENEA01', 'GA01', 'GEA01'],
            'bluinq' => ['BLQ01', 'BLUINQ01'],
            'luntian' => ['LT01', 'LUNTIAN'],
            default => array_values(array_filter([self::defaultCodes()[$module] ?? ''])),
        };
    }

    public static function defaultCodeFor(string $module): string
    {
        $defaults = self::defaultCodes();
        $code = $defaults[$module] ?? '';

        if ($module === 'general_assembly' && Schema::hasTable('clients')) {
            $hasGenea = DB::table('clients')->whereRaw('UPPER(TRIM(client_code)) = ?', ['GENEA01'])->exists();
            if (! $hasGenea && DB::table('clients')->whereRaw('UPPER(TRIM(client_code)) = ?', ['GA01'])->exists()) {
                return 'GA01';
            }
        }

        return $code;
    }

    public static function flushCodeCache(): void
    {
        self::$codeCache = null;
    }

    public static function codeFor(string $module): string
    {
        if (self::$codeCache === null) {
            if (! Schema::hasTable('job_module_clients')) {
                self::$codeCache = [];
            } else {
                self::$codeCache = static::query()
                    ->pluck('client_code', 'module')
                    ->map(fn ($code) => trim((string) $code))
                    ->all();
            }
        }

        $mapped = trim((string) (self::$codeCache[$module] ?? ''));
        if ($mapped !== '') {
            return $mapped;
        }

        return self::defaultCodeFor($module);
    }

    /** @return array<string, string> module => client_code for every add-job module */
    public static function codesByModule(): array
    {
        $out = [];
        foreach (AddJobModules::keys() as $module) {
            $out[$module] = self::codeFor($module);
        }

        return $out;
    }
}
