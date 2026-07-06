<?php

namespace App\Support;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

/**
 * JOIN helpers for legacy utf8mb4_general_ci tables mixed with newer Laravel tables.
 */
final class LegacyDbJoin
{
    public static function leftJoinClientsByClientCode(Builder $query, string $jobsAlias = 'j', string $clientsAlias = 'cl'): Builder
    {
        return $query->leftJoin('clients as '.$clientsAlias, function ($join) use ($jobsAlias, $clientsAlias) {
            $join->whereRaw(
                "{$clientsAlias}.client_code COLLATE utf8mb4_general_ci = {$jobsAlias}.client_code COLLATE utf8mb4_general_ci"
            );
        });
    }
}
