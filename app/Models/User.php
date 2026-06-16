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
        'add_job_modules',
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
            'add_job_modules' => 'array',
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

    public function appearsInAddJobModule(?string $module): bool
    {
        if ($module === null || trim($module) === '') {
            return true;
        }

        $modules = $this->add_job_modules;
        if (! is_array($modules) || $modules === []) {
            return false;
        }

        return in_array($module, $modules, true);
    }

    public static function assignmentUsersForSelect(?string $module = null): Collection
    {
        return static::query()
            ->forJobAssignment()
            ->orderBy('unique_code')
            ->get(['id', 'unique_code', 'username', 'fullname', 'add_job_modules'])
            ->unique('unique_code')
            ->filter(fn (self $user) => $user->appearsInAddJobModule($module))
            ->values();
    }

    public static function assignmentUserCodes(): Collection
    {
        return static::query()
            ->forJobAssignment()
            ->orderBy('unique_code')
            ->pluck('unique_code')
            ->filter()
            ->map(fn ($v) => strtoupper((string) $v))
            ->unique()
            ->values();
    }

    /** @return list<string> */
    public static function allowedAssignmentUserCodes(): array
    {
        $codes = static::assignmentUserCodes()->all();
        if (! in_array('GM', $codes, true)) {
            $codes[] = 'GM';
        }

        return array_values(array_filter($codes, fn ($v) => $v !== ''));
    }
}
