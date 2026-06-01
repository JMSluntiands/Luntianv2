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
            ->where(function (Builder $q) {
                $q->whereNull('role')
                    ->orWhereRaw('LOWER(TRIM(role)) != ?', ['admin']);
            })
            ->where(function (Builder $q) {
                $q->whereRaw('LOWER(TRIM(COALESCE(role, ""))) != ?', ['branch'])
                    ->where(function (Builder $q2) {
                        $q2->whereNull('branch')
                            ->orWhereRaw('TRIM(COALESCE(branch, "")) = ?', ['']);
                    });
            });
    }

    public static function assignmentUsersForSelect(): Collection
    {
        return static::query()
            ->forJobAssignment()
            ->orderBy('unique_code')
            ->get(['id', 'unique_code'])
            ->unique('unique_code')
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
