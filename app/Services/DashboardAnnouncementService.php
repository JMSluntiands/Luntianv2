<?php

namespace App\Services;

use App\Models\ForumPost;
use Illuminate\Support\Facades\Schema;

class DashboardAnnouncementService
{
    /**
     * Bulletin posts marked as announcements, newest first.
     *
     * @return list<array<string, mixed>>
     */
    public static function recentPayload(int $limit = 8): array
    {
        if (! Schema::hasTable('forum_posts')) {
            return [];
        }

        $query = ForumPost::query()->with('user:id,fullname,username');

        if (Schema::hasColumn('forum_posts', 'post_type')) {
            $query->where('post_type', ForumPost::TYPE_ANNOUNCEMENT);
        }

        $rows = $query
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit(max(1, min($limit, 20)))
            ->get();

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
            if ($title === '') {
                $title = self::titleFromPlain($plain);
            }
            $excerpt = self::excerptFromPlain($plain);

            $detailUrl = route('forum_thread', [], false).'#post-'.$post->id;

            return [
                'id' => (int) $post->id,
                'title' => $title,
                'message' => $plain,
                'excerpt' => $excerpt,
                'author' => $authorName,
                'status' => ForumPost::TYPE_ANNOUNCEMENT,
                'image_url' => $post->imageUrl(),
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

    private static function titleFromPlain(string $plain): string
    {
        if ($plain === '') {
            return 'Announcement';
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
