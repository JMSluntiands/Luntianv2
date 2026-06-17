<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'unique_code',
        'username',
        'email',
        'fullname',
        'role',
        'branch',
        'add_job_staff_modules',
        'add_job_checker_modules',
        'task',
        'status',
        'password',
        'profile_image',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'add_job_staff_modules' => 'array',
            'add_job_checker_modules' => 'array',
        ];
    }

    public function userPermissions(): HasMany
    {
        return $this->hasMany(UserPermission::class);
    }

    /**
     * Users eligible for job Assigned To / Checked By dropdowns (excludes Admin and Branch users).
     */
    public function scopeForJobAssignment(Builder $query): Builder
    {
        return $query
            ->whereRaw('LOWER(TRIM(COALESCE(role, ""))) NOT IN (?, ?)', ['admin', 'branch'])
            ->where(function (Builder $q) {
                $q->whereNull('branch')
                    ->orWhereRaw('TRIM(COALESCE(branch, "")) = ?', ['']);
            })
            ->whereNotNull('unique_code')
            ->whereRaw('TRIM(unique_code) != ?', ['']);
    }

    public function assignmentOptionLabel(): string
    {
        return trim((string) $this->unique_code);
    }

    public function appearsInAddJobModule(?string $module, string $assignmentRole = 'staff'): bool
    {
        if ($module === null || trim($module) === '') {
            return false;
        }

        $modules = $assignmentRole === 'checker'
            ? $this->add_job_checker_modules
            : $this->add_job_staff_modules;

        if (! is_array($modules) || $modules === []) {
            return false;
        }

        return in_array($module, $modules, true);
    }

    public static function assignmentUsersForSelect(?string $module = null, string $assignmentRole = 'staff'): Collection
    {
        return static::query()
            ->forJobAssignment()
            ->orderBy('unique_code')
            ->orderByDesc('id')
            ->get(['id', 'unique_code', 'username', 'fullname', 'add_job_staff_modules', 'add_job_checker_modules'])
            ->groupBy(fn (self $user) => strtoupper(trim((string) $user->unique_code)))
            ->map(fn (Collection $group) => $group->first(
                fn (self $user) => $user->appearsInAddJobModule($module, $assignmentRole)
            ))
            ->filter()
            ->sortBy(fn (self $user) => strtoupper(trim((string) $user->unique_code)))
            ->values();
    }

    /** Default Assigned To / Checked By when GM is eligible for that dropdown. */
    public static function defaultAssignmentSelection(Collection $users): string
    {
        foreach ($users as $user) {
            $code = strtoupper(trim((string) (is_object($user) ? ($user->unique_code ?? '') : $user)));
            if ($code === 'GM') {
                return 'GM';
            }
        }

        return '';
    }

    /** @return array{assignmentStaffUsers: Collection, assignmentCheckerUsers: Collection} */
    public static function assignmentSelectLists(?string $module): array
    {
        return [
            'assignmentStaffUsers' => static::assignmentUsersForSelect($module, 'staff'),
            'assignmentCheckerUsers' => static::assignmentUsersForSelect($module, 'checker'),
        ];
    }

    /** @return array{assignmentStaffCodes: list<string>, assignmentCheckerCodes: list<string>} */
    public static function assignmentInitialsViewData(?string $module): array
    {
        return [
            'assignmentStaffCodes' => static::allowedAssignmentUserCodes($module, 'staff'),
            'assignmentCheckerCodes' => static::allowedAssignmentUserCodes($module, 'checker'),
        ];
    }

    /** @return list<string> */
    public static function allowedAssignmentUserCodes(?string $module, string $assignmentRole = 'staff'): array
    {
        return static::assignmentUsersForSelect($module, $assignmentRole)
            ->map(fn (self $user) => strtoupper(trim((string) $user->unique_code)))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
