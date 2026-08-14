<?php

namespace App\Services;

use App\Models\ForumPost;
use Illuminate\Support\Facades\Schema;

class DashboardAnnouncementService
{
    /**
     * Bulletin announcements and discussions for the dashboard preview.
     * Pinned posts first, then newest. Default max 5.
     *
     * @return list<array<string, mixed>>
     */
    public static function recentPayload(int $limit = 5): array
    {
        try {
            if (! Schema::hasTable('forum_posts')) {
                return [];
            }
        } catch (\Throwable) {
            return [];
        }

        try {
            $rows = ForumPost::query()
                ->with('user:id,fullname,username')
                ->feedOrder()
                ->limit(max(1, min($limit, 5)))
                ->get();
        } catch (\Throwable) {
            return [];
        }

        return $rows->map(static function (ForumPost $post): array {
            $author = $post->user;
            $authorName = 'Staff';
            if ($author) {
                $name = trim((string) ($author->fullname ?? ''));
                if ($name === '') {
                    $name = trim((string) ($author->username ?? ''));
                }
                if ($name !== '') {
                    $authorName = $name;
                }
            }

            $when = $post->created_at?->timezone('Asia/Manila');
            $plain = self::plainText((string) ($post->body ?? ''));
            $title = trim((string) ($post->title ?? ''));
            $postType = $post->normalizeType();
            $isPinned = $post->isPinned();
            if ($title === '') {
                $title = self::titleFromPlain(
                    $plain,
                    $postType === ForumPost::TYPE_ANNOUNCEMENT ? 'Announcement' : 'Discussion'
                );
            }
            $excerpt = self::excerptFromPlain($plain);

            $detailUrl = \Illuminate\Support\Facades\Route::has('forum_thread')
                ? route('forum_thread', [], false).'#post-'.$post->id
                : '#post-'.$post->id;

            $imageUrl = null;
            try {
                $imageUrl = $post->imageUrl();
            } catch (\Throwable) {
                $imageUrl = null;
            }

            return [
                'id' => (int) $post->id,
                'title' => $title,
                'message' => $plain,
                'excerpt' => $excerpt,
                'author' => $authorName,
                'status' => $postType,
                'is_pinned' => $isPinned,
                'image_url' => $imageUrl,
                'date_label' => $when ? $when->format('F j, Y') : null,
                'time_label' => $when ? $when->format('g:i A') : null,
                'meta_label' => $when
                    ? $when->format('F j, Y').' · '.$when->format('g:i A').' · '.$authorName
                    : $authorName,
                'created_at_iso' => $when?->toIso8601String(),
                'url' => $detailUrl,
            ];
        })->values()->all();
    }

    private static function plainText(string $html): string
    {
        $plain = trim(html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8'));

        return preg_replace('/\s+/u', ' ', $plain) ?? $plain;
    }

    private static function titleFromPlain(string $plain, string $fallback = 'Post'): string
    {
        if ($plain === '') {
            return $fallback;
        }

        $first = preg_split('/(?<=[.!?])\s+/u', $plain, 2)[0] ?? $plain;
        $first = trim((string) $first);
        if ($first === '') {
            $first = $plain;
        }

        if (mb_strlen($first) > 80) {
            return rtrim(mb_substr($first, 0, 77)).'…';
        }

        return $first;
    }

    private static function excerptFromPlain(string $plain): string
    {
        if ($plain === '') {
            return '';
        }

        if (mb_strlen($plain) > 220) {
            return rtrim(mb_substr($plain, 0, 217)).'…';
        }

        return $plain;
    }
}
