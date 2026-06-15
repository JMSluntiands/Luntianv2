<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    protected $table = 'role_permissions';

    public $timestamps = false;

    protected $fillable = ['role', 'branch', 'route_name'];

    /**
     * Stored as '' for “all branches”; otherwise must match users.branch (branch name).
     */
    public static function normalizeBranch(?string $branch): string
    {
        if ($branch === null) {
            return '';
        }

        return trim((string) $branch);
    }

    /**
     * Match session / users.role to the same string used when saving the permission matrix
     * (config permissions.roles uses "Staff", DB often stores "staff").
     */
    public static function canonicalRole(string $role): string
    {
        $role = trim($role);
        if ($role === '') {
            return $role;
        }
        foreach (config('permissions.roles', []) as $configured) {
            if (strcasecmp((string) $configured, $role) === 0) {
                return (string) $configured;
            }
        }

        return $role;
    }

    /**
     * Branch-specific rows (same role + user's branch) override the global row set (branch '').
     * If the user has no branch, only global (branch '') applies.
     */
    public static function hasAccess(string $role, string $routeName, ?string $userBranch = null): bool
    {
        $role = static::canonicalRole($role);
        $userBranch = static::normalizeBranch($userBranch ?? (string) (session('user_branch') ?? ''));

        if ($userBranch !== '') {
            $branchLower = mb_strtolower($userBranch);
            $branchCount = static::where('role', $role)
                ->whereRaw('LOWER(branch) = ?', [$branchLower])
                ->count();
            if ($branchCount > 0) {
                $allowed = static::where('role', $role)
                    ->whereRaw('LOWER(branch) = ?', [$branchLower])
                    ->pluck('route_name')
                    ->toArray();

                return in_array($routeName, $allowed, true);
            }
        }

        $global = static::where('role', $role)->where('branch', '')->pluck('route_name')->toArray();
        if (!empty($global)) {
            return in_array($routeName, $global, true);
        }

        return false;
    }

    /**
     * Same rules as CheckPagePermission: admin / empty role / routes not in config pass;
     * otherwise role_permissions must include the route.
     */
    public static function userMayAccessRoute(?string $routeName): bool
    {
        if ($routeName === null || $routeName === 'unauthorized') {
            return true;
        }

        $role = (string) (session('user_role') ?? '');
        if ($role === '') {
            return true;
        }

        $roleNorm = strtolower(trim($role));
        if ($roleNorm === 'admin') {
            $userId = (int) session('user_id', 0);
            if ($userId > 0 && UserPermission::hasAnyRowForUser($userId)) {
                return UserPermission::hasAccess($userId, $routeName, session('user_branch'));
            }

            return true;
        }

        $allowedRoutes = config('permissions.routes', []);
        $allRouteNames = [];
        foreach ($allowedRoutes as $group) {
            foreach (array_keys($group) as $name) {
                $allRouteNames[$name] = true;
            }
        }
        if (!isset($allRouteNames[$routeName])) {
            return true;
        }

        $userId = (int) session('user_id', 0);
        if ($userId > 0 && UserPermission::hasAnyRowForUser($userId)) {
            return UserPermission::hasAccess($userId, $routeName, session('user_branch'));
        }

        return static::hasAccess($role, $routeName, session('user_branch'));
    }

    public static function allowedRoutesForRole(string $role, ?string $userBranch = null): array
    {
        if (strtolower(trim($role)) === 'admin') {
            $names = [];
            foreach (config('permissions.routes', []) as $group) {
                if (!is_array($group)) {
                    continue;
                }
                foreach (array_keys($group) as $name) {
                    $names[] = $name;
                }
            }

            return array_values(array_unique($names));
        }

        $userId = (int) session('user_id', 0);
        if ($userId > 0 && UserPermission::hasAnyRowForUser($userId)) {
            return UserPermission::allowedRoutesForUser($userId, $userBranch);
        }

        $role = static::canonicalRole($role);
        $userBranch = static::normalizeBranch($userBranch ?? (string) (session('user_branch') ?? ''));

        if ($userBranch !== '') {
            $branchLower = mb_strtolower($userBranch);
            $branchRoutes = static::where('role', $role)
                ->whereRaw('LOWER(branch) = ?', [$branchLower])
                ->pluck('route_name')
                ->toArray();
            if (!empty($branchRoutes)) {
                return array_values(array_unique($branchRoutes));
            }
        }

        return static::where('role', $role)->where('branch', '')->pluck('route_name')->toArray();
    }

    /**
     * Labels used by dashboard stat cards (must match resources/js/components/Dashboard.tsx).
     *
     * @return list<string>
     */
    public static function dashboardStatCardLabels(): array
    {
        return ['LBS', 'LUNTIAN', 'BPH', 'BLUINQ', 'A&M', 'FYRS ENERGY WISE', 'CSP', 'NH', 'LC HOME BUILDER', 'EFFICIENT LIVING', 'LEADING ENERGY'];
    }

    /**
     * Map users.branch / role_permissions.branch strings to a dashboard stat row label.
     */
    public static function mapBranchStringToDashboardStatLabel(string $branch): ?string
    {
        $b = mb_strtolower(preg_replace('/\s+/u', ' ', trim($branch)));
        if ($b === '') {
            return null;
        }

        $aliases = [
            'lbs' => 'LBS',
            'luntian' => 'LUNTIAN',
            'bph' => 'BPH',
            'bluinq' => 'BLUINQ',
            'amt' => 'A&M',
            'a&m' => 'A&M',
            'fyrs' => 'FYRS ENERGY WISE',
            'fyrs energy wise' => 'FYRS ENERGY WISE',
            'csp' => 'CSP',
            'nh' => 'NH',
            'lc home builder' => 'LC HOME BUILDER',
            'lc_home_builder' => 'LC HOME BUILDER',
            'efficient living' => 'EFFICIENT LIVING',
            'efficient_living' => 'EFFICIENT LIVING',
            'leading energy' => 'LEADING ENERGY',
            'leading_energy' => 'LEADING ENERGY',
        ];

        if (isset($aliases[$b])) {
            return $aliases[$b];
        }

        foreach (static::dashboardStatCardLabels() as $label) {
            if (mb_strtolower($label) === $b) {
                return $label;
            }
        }

        foreach ($aliases as $needle => $label) {
            if ($needle !== '' && (str_contains($b, $needle) || str_contains($needle, $b))) {
                return $label;
            }
        }

        return null;
    }

    /**
     * Branch filter for dashboard job cards: one stat row when the user is scoped to a single branch.
     * Uses session user_branch when set; otherwise, if the user may access exactly one job vertical, infer it.
     * Global admins (admin + no branch assignment) get '' = show all rows.
     */
    public static function dashboardStatCardsBranchFilter(): string
    {
        $role = strtolower((string) session('user_role', ''));
        $ub = static::normalizeBranch((string) session('user_branch', ''));

        if ($role === 'admin' && $ub === '') {
            return '';
        }

        if ($ub !== '') {
            return static::mapBranchStringToDashboardStatLabel($ub) ?? $ub;
        }

        $anyMay = static function (array $routes): bool {
            foreach ($routes as $r) {
                if (static::userMayAccessRoute($r)) {
                    return true;
                }
            }

            return false;
        };

        $allowed = [];
        if ($anyMay(['lbs.add', 'lbs.list', 'lbs.completed', 'lbs.review', 'lbs.mailbox', 'lbs.trash'])) {
            $allowed['LBS'] = true;
        }
        if ($anyMay(['luntian.add', 'luntian.list', 'luntian.completed', 'luntian.review', 'luntian.mailbox', 'luntian.trash'])) {
            $allowed['LUNTIAN'] = true;
        }
        if ($anyMay(['bph.add', 'bph.list', 'bph.completed', 'bph.review', 'bph.mailbox', 'bph.trash'])) {
            $allowed['BPH'] = true;
        }
        if ($anyMay(['efficient_living.add', 'efficient_living.list', 'efficient_living.completed', 'efficient_living.review', 'efficient_living.mailbox', 'efficient_living.trash'])) {
            $allowed['EFFICIENT LIVING'] = true;
        }
        if ($anyMay(['bluinq.add', 'bluinq.list', 'bluinq.completed', 'bluinq.review', 'bluinq.mailbox', 'bluinq.trash'])) {
            $allowed['BLUINQ'] = true;
        }
        if ($anyMay(['amt.add', 'amt.rfq', 'amt.purchase', 'amt.list', 'amt.completed', 'amt.review', 'amt.mailbox', 'amt.trash'])) {
            $allowed['A&M'] = true;
        }
        if ($anyMay(['fyrs.add', 'fyrs.list', 'fyrs.completed', 'fyrs.review', 'fyrs.mailbox', 'fyrs.trash'])) {
            $allowed['FYRS ENERGY WISE'] = true;
        }
        if ($anyMay([
            'csp.add', 'csp.store', 'csp.view', 'csp.list', 'csp.completed', 'csp.review', 'csp.mailbox', 'csp.trash',
            'csp.update', 'csp.job.emailPreview', 'csp.job.sendMailboxEmail', 'csp.job.printCompliance',
        ])) {
            $allowed['CSP'] = true;
        }
        if ($anyMay([
            'nh.add', 'nh.store', 'nh.list', 'nh.completed', 'nh.review', 'nh.mailbox', 'nh.trash', 'nh.update',
            'nh.job.sendSlack', 'nh.job.sendSubmissionEmail', 'nh.job.emailPreview', 'nh.job.sendMailboxEmail', 'nh.job.printCompliance',
        ])) {
            $allowed['NH'] = true;
        }
        if ($anyMay([
            'lc_home_builder.add', 'lc_home_builder.store', 'lc_home_builder.list', 'lc_home_builder.completed', 'lc_home_builder.review', 'lc_home_builder.mailbox', 'lc_home_builder.trash',
            'lc_home_builder.update', 'lc_home_builder.job.sendSlack', 'lc_home_builder.job.sendSubmissionEmail', 'lc_home_builder.job.emailPreview', 'lc_home_builder.job.sendMailboxEmail',
        ])) {
            $allowed['LC HOME BUILDER'] = true;
        }
        if ($anyMay([
            'leading_energy.add', 'leading_energy.store', 'leading_energy.list', 'leading_energy.completed', 'leading_energy.review', 'leading_energy.mailbox', 'leading_energy.trash',
            'leading_energy.update', 'leading_energy.job.sendSlack', 'leading_energy.job.sendSubmissionEmail', 'leading_energy.job.emailPreview', 'leading_energy.job.sendMailboxEmail',
        ])) {
            $allowed['LEADING ENERGY'] = true;
        }

        $keys = array_keys($allowed);

        return count($keys) === 1 ? $keys[0] : '';
    }
}
