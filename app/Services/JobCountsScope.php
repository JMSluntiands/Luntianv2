<?php

namespace App\Services;

use App\Models\JobRequest;
use App\Models\RolePermission;
use App\Models\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Job counts / lists are no longer assignment-scoped by role.
 * Staff, Checker, and User can view all encoded jobs.
 * Branch/office users still rely on {@see RolePermission::dashboardStatCardsBranchFilter()}
 * (user branch → vertical).
 */
class JobCountsScope
{
    public static function normalizeRole(): string
    {
        return strtolower(trim((string) session('user_role', '')));
    }

    public static function isGlobalAdmin(): bool
    {
        return self::normalizeRole() === 'admin'
            && RolePermission::normalizeBranch((string) session('user_branch', '')) === '';
    }

    public static function shouldScopeToAssignment(): bool
    {
        if (! session()->has('user_id')) {
            return false;
        }
        if (self::isGlobalAdmin()) {
            return false;
        }

        return false;
    }

    public static function assignmentCodeUpper(): ?string
    {
        if (! self::shouldScopeToAssignment()) {
            return null;
        }
        $code = User::query()->whereKey((int) session('user_id'))->value('unique_code');
        $code = $code !== null ? strtoupper(trim((string) $code)) : '';

        return $code !== '' ? $code : null;
    }

    public static function applyJobsTableAssignment(Builder $q, string $staffCol = 'staff_id', string $checkerCol = 'checker_id'): void
    {
        $code = self::assignmentCodeUpper();
        if ($code === null) {
            return;
        }
        $q->where(function ($w) use ($code, $staffCol, $checkerCol) {
            $w->whereRaw("UPPER(TRIM(COALESCE({$staffCol}, ''))) = ?", [$code])
                ->orWhereRaw("UPPER(TRIM(COALESCE({$checkerCol}, ''))) = ?", [$code]);
        });
    }

    public static function applyJobBphAssignment(Builder $q): void
    {
        $code = self::assignmentCodeUpper();
        if ($code === null) {
            return;
        }
        $q->where(function ($w) use ($code) {
            $w->whereRaw('UPPER(TRIM(COALESCE(assigned, \'\'))) = ?', [$code])
                ->orWhereRaw('UPPER(TRIM(COALESCE(checked, \'\'))) = ?', [$code]);
        });
    }

    /**
     * Branch role: user.branch maps to one product line; limit job_bph-based lists to that line.
     */
    public static function applyJobBphBranchVerticalScope(Builder $q): void
    {
        if (self::normalizeRole() !== 'branch' || self::isGlobalAdmin()) {
            return;
        }
        $ub = RolePermission::normalizeBranch((string) session('user_branch', ''));
        if ($ub === '') {
            return;
        }
        $mapped = RolePermission::mapBranchStringToDashboardStatLabel($ub) ?? $ub;
        $label = strtoupper(trim((string) $mapped));

        if ($label === 'BPH') {
            $q->whereRaw('LOWER(TRIM(COALESCE(client_code, \'\'))) NOT IN (?, ?, ?)', ['bluinq01', 'amt01', 'fyrs01']);
        } elseif ($label === 'BLUINQ') {
            $q->whereRaw('LOWER(TRIM(client_code)) = ?', ['bluinq01']);
        } elseif ($label === 'A&M') {
            $q->whereRaw('LOWER(TRIM(client_code)) = ?', ['amt01']);
        } elseif ($label === 'FYRS ENERGY WISE') {
            $q->whereRaw('LOWER(TRIM(client_code)) = ?', ['fyrs01']);
        } else {
            $q->whereRaw('1 = 0');
        }
    }

    /**
     * After computing a badge count: Branch users only see counts for their mapped vertical.
     *
     * @param  non-empty-string  $statCardLabel  e.g. LBS, BPH, BLUINQ, EFFICIENT LIVING
     */
    public static function sidebarCountForBranchVertical(string $statCardLabel, int $count): int
    {
        if (self::normalizeRole() !== 'branch' || self::isGlobalAdmin()) {
            return $count;
        }
        $ub = RolePermission::normalizeBranch((string) session('user_branch', ''));
        if ($ub === '') {
            return $count;
        }
        $mapped = RolePermission::mapBranchStringToDashboardStatLabel($ub) ?? $ub;
        if (strcasecmp(trim($mapped), trim($statCardLabel)) === 0) {
            return $count;
        }

        return 0;
    }

    /** Branch user whose office maps away from main LBS list should see an empty list. */
    public static function branchBlocksLbsStandardList(): bool
    {
        if (self::normalizeRole() !== 'branch' || self::isGlobalAdmin()) {
            return false;
        }
        $ub = RolePermission::normalizeBranch((string) session('user_branch', ''));
        if ($ub === '') {
            return false;
        }
        $mapped = RolePermission::mapBranchStringToDashboardStatLabel($ub) ?? $ub;
        $label = strtoupper(trim((string) $mapped));

        return ! in_array($label, ['', 'LBS'], true);
    }

    public static function branchBlocksEfficientLivingList(): bool
    {
        if (self::normalizeRole() !== 'branch' || self::isGlobalAdmin()) {
            return false;
        }
        $ub = RolePermission::normalizeBranch((string) session('user_branch', ''));
        if ($ub === '') {
            return false;
        }
        $mapped = RolePermission::mapBranchStringToDashboardStatLabel($ub) ?? $ub;
        $label = strtoupper(preg_replace('/\s+/u', ' ', trim((string) $mapped)));

        return $label !== '' && $label !== 'EFFICIENT LIVING';
    }

    public static function branchBlocksLuntianList(): bool
    {
        if (self::normalizeRole() !== 'branch' || self::isGlobalAdmin()) {
            return false;
        }
        $ub = RolePermission::normalizeBranch((string) session('user_branch', ''));
        if ($ub === '') {
            return false;
        }
        $mapped = RolePermission::mapBranchStringToDashboardStatLabel($ub) ?? $ub;
        $label = strtoupper(preg_replace('/\s+/u', ' ', trim((string) $mapped)));

        return $label !== '' && $label !== 'LUNTIAN';
    }

    /**
     * Branch role: only rows for lists tied to this product line (e.g. CSP job_csp lists).
     * Uses the same mapping as dashboard stat cards.
     *
     * @param  non-empty-string  $statCardLabel  e.g. CSP, NH, LC HOME BUILDER, LEADING ENERGY
     */
    public static function applyBranchExclusiveStatLabel(Builder $q, string $statCardLabel): void
    {
        if (self::normalizeRole() !== 'branch' || self::isGlobalAdmin()) {
            return;
        }
        $ub = RolePermission::normalizeBranch((string) session('user_branch', ''));
        if ($ub === '') {
            return;
        }
        $mapped = RolePermission::mapBranchStringToDashboardStatLabel($ub) ?? $ub;
        if (strcasecmp(trim((string) $mapped), trim($statCardLabel)) === 0) {
            return;
        }
        $q->whereRaw('1 = 0');
    }

    /** @return list<string> */
    public static function luntianJobRequestClientCodes(): array
    {
        return ['LT01', 'Luntian'];
    }

    private static function jobRequestIdColumn(string $alias): string
    {
        $alias = trim($alias);

        return $alias !== '' ? "{$alias}.job_request_id" : 'job_request_id';
    }

    /** @return list<string> */
    public static function luntianJobRequestIds(): array
    {
        return JobRequest::query()
            ->forLuntianVertical()
            ->pluck('job_request_id')
            ->map(fn ($id) => (string) $id)
            ->filter()
            ->values()
            ->all();
    }

    public static function applyLuntianJobsScope(Builder $q, string $alias = 'j'): void
    {
        $ids = self::luntianJobRequestIds();
        $col = self::jobRequestIdColumn($alias);
        $q->where(function ($w) use ($col, $ids) {
            $w->whereRaw("{$col} LIKE 'EA\\_LT\\_%'");
            if ($ids !== []) {
                $w->orWhereIn($col, $ids);
            }
        });
    }

    public static function applyExcludeLuntianJobsScope(Builder $q, string $alias = 'j'): void
    {
        $ids = self::luntianJobRequestIds();
        $col = self::jobRequestIdColumn($alias);
        $q->where(function ($w) use ($col, $ids) {
            $w->whereNull($col)
                ->orWhere($col, '')
                ->orWhere(function ($inner) use ($col, $ids) {
                    $inner->whereRaw("{$col} NOT LIKE 'EA\\_LT\\_%'");
                    if ($ids !== []) {
                        $inner->whereNotIn($col, $ids);
                    }
                });
        });
    }

    public static function applyExcludeEfficientLivingJobsScope(Builder $q, string $alias = 'j'): void
    {
        $col = self::jobRequestIdColumn($alias);
        $q->where(function ($w) use ($col) {
            $w->whereNull($col)
                ->orWhere($col, '')
                ->orWhereRaw("{$col} NOT LIKE 'EA\\_EL\\_%'");
        });
    }

    public static function branchBlocksGeneralAssemblyList(): bool
    {
        if (self::normalizeRole() !== 'branch' || self::isGlobalAdmin()) {
            return false;
        }
        $ub = RolePermission::normalizeBranch((string) session('user_branch', ''));
        if ($ub === '') {
            return false;
        }
        $mapped = RolePermission::mapBranchStringToDashboardStatLabel($ub) ?? $ub;
        $label = strtoupper(preg_replace('/\s+/u', ' ', trim((string) $mapped)));

        return $label !== '' && $label !== RolePermission::GENERIC_ASSESSMENT_STAT_LABEL;
    }

    /** Main LBS pipeline: shared `jobs` table rows that are not EL or Luntian verticals. */
    public static function applyLbsStandardJobsScope(Builder $q, string $alias = 'j'): void
    {
        self::applyExcludeEfficientLivingJobsScope($q, $alias);
        self::applyExcludeLuntianJobsScope($q, $alias);
    }
}
