<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Snapshot of a Bulletin post or comment taken right before it was edited.
 */
class ForumRevision extends Model
{
    public const TYPE_POST = 'post';

    public const TYPE_COMMENT = 'comment';

    protected $fillable = [
        'revisable_type',
        'revisable_id',
        'edited_by',
        'title',
        'body',
        'post_type',
        'image_path',
    ];

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'edited_by');
    }

    /**
     * Servers deployed without running migrations still get the table.
     */
    public static function ensureTable(): bool
    {
        try {
            if (! Schema::hasTable('forum_revisions')) {
                Schema::create('forum_revisions', function (Blueprint $table) {
                    $table->id();
                    $table->string('revisable_type', 20);
                    $table->unsignedBigInteger('revisable_id');
                    $table->unsignedBigInteger('edited_by')->nullable();
                    $table->string('title', 200)->nullable();
                    $table->longText('body')->nullable();
                    $table->string('post_type', 30)->nullable();
                    $table->string('image_path')->nullable();
                    $table->timestamps();
                    $table->index(['revisable_type', 'revisable_id']);
                });
            }

            return true;
        } catch (\Throwable $e) {
            report($e);

            return false;
        }
    }

    public static function snapshotPost(ForumPost $post, int $editorId): void
    {
        if (! self::ensureTable()) {
            return;
        }

        self::create([
            'revisable_type' => self::TYPE_POST,
            'revisable_id' => $post->id,
            'edited_by' => $editorId > 0 ? $editorId : null,
            'title' => $post->title,
            'body' => $post->body,
            'post_type' => $post->post_type,
            'image_path' => $post->image_path,
        ]);
    }

    public static function snapshotComment(ForumComment $comment, int $editorId): void
    {
        if (! self::ensureTable()) {
            return;
        }

        self::create([
            'revisable_type' => self::TYPE_COMMENT,
            'revisable_id' => $comment->id,
            'edited_by' => $editorId > 0 ? $editorId : null,
            'body' => $comment->body,
        ]);
    }

    /**
     * @param  iterable<int>  $ids
     * @return array<int, int> revisable_id => number of edits
     */
    public static function countsFor(string $type, iterable $ids): array
    {
        $ids = collect($ids)->map(fn ($id) => (int) $id)->filter()->unique()->values();
        if ($ids->isEmpty() || ! Schema::hasTable('forum_revisions')) {
            return [];
        }

        return self::query()
            ->where('revisable_type', $type)
            ->whereIn('revisable_id', $ids->all())
            ->groupBy('revisable_id')
            ->selectRaw('revisable_id, COUNT(*) as edits')
            ->pluck('edits', 'revisable_id')
            ->map(fn ($n) => (int) $n)
            ->all();
    }

    public static function deleteFor(string $type, iterable $ids): void
    {
        $ids = collect($ids)->map(fn ($id) => (int) $id)->filter()->values();
        if ($ids->isEmpty() || ! Schema::hasTable('forum_revisions')) {
            return;
        }

        self::query()->where('revisable_type', $type)->whereIn('revisable_id', $ids->all())->delete();
    }
}
