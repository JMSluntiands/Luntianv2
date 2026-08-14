<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Schema;

class ForumPost extends Model
{
    public const TYPE_DISCUSSION = 'discussion';

    public const TYPE_ANNOUNCEMENT = 'announcement';

    protected $fillable = [
        'user_id',
        'title',
        'body',
        'post_type',
        'image_path',
        'pinned_at',
    ];

    protected $casts = [
        'pinned_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(ForumComment::class)->orderBy('created_at');
    }

    public function allowsComments(): bool
    {
        return $this->normalizeType() === self::TYPE_DISCUSSION;
    }

    public function isAnnouncement(): bool
    {
        return $this->normalizeType() === self::TYPE_ANNOUNCEMENT;
    }

    public function isPinned(): bool
    {
        return $this->pinned_at !== null;
    }

    /**
     * Pinned posts first (newest pin), then newest created.
     */
    public function scopeFeedOrder(Builder $query): Builder
    {
        if (Schema::hasColumn($this->getTable(), 'pinned_at')) {
            $query->orderByRaw('CASE WHEN pinned_at IS NULL THEN 1 ELSE 0 END')
                ->orderByDesc('pinned_at');
        }

        return $query->orderByDesc('created_at')->orderByDesc('id');
    }

    public function normalizeType(): string
    {
        $type = strtolower(trim((string) ($this->post_type ?? '')));

        return $type === self::TYPE_ANNOUNCEMENT
            ? self::TYPE_ANNOUNCEMENT
            : self::TYPE_DISCUSSION;
    }

    public function imageUrl(): ?string
    {
        $path = trim((string) ($this->image_path ?? ''));
        if ($path === '') {
            return null;
        }

        if (! \Illuminate\Support\Facades\Route::has('forum_thread.image')) {
            return null;
        }

        return route('forum_thread.image', ['path' => basename($path)]);
    }
}
