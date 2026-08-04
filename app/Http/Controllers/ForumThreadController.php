<?php

namespace App\Http\Controllers;

use App\Models\ForumComment;
use App\Models\ForumPost;
use App\Models\RolePermission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class ForumThreadController extends Controller
{
    public function index()
    {
        $posts = ForumPost::query()
            ->with(['user:id,fullname,username,profile_image', 'comments.user:id,fullname,username,profile_image'])
            ->withCount('comments')
            ->orderByDesc('created_at')
            ->paginate(20);

        $userId = (int) session('user_id', 0);
        $role = strtolower(trim((string) session('user_role', '')));

        return view('forum-thread.index', [
            'sidebar_active' => 'forum_thread',
            'posts' => $posts,
            'currentUser' => $userId > 0 ? User::find($userId) : null,
            'canPost' => RolePermission::userMayAccessRoute('forum_thread.post'),
            'canComment' => RolePermission::userMayAccessRoute('forum_thread.comment'),
            'canDeletePost' => RolePermission::userMayAccessRoute('forum_thread.destroy'),
            'canDeleteComment' => RolePermission::userMayAccessRoute('forum_thread.comment.destroy'),
            'isAdmin' => $role === 'admin',
            'currentUserId' => $userId,
        ]);
    }

    public function recent()
    {
        return response()->json([
            'posts' => self::recentPostsPayload(8),
            'feed_url' => route('forum_thread', [], false),
        ]);
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function recentPostsPayload(int $limit = 8): array
    {
        $posts = ForumPost::query()
            ->with(['user:id,fullname,username,profile_image'])
            ->withCount('comments')
            ->orderByDesc('created_at')
            ->limit(max(1, min($limit, 20)))
            ->get();

        return $posts->map(static function (ForumPost $post): array {
            $user = $post->user;
            $name = trim((string) ($user?->fullname ?? ''));
            if ($name === '') {
                $name = trim((string) ($user?->username ?? ''));
            }
            if ($name === '') {
                $name = 'User';
            }

            $img = trim((string) ($user?->profile_image ?? ''));
            $avatar = null;
            if ($img !== '') {
                if (str_starts_with($img, 'http://') || str_starts_with($img, 'https://') || str_starts_with($img, '/')) {
                    $avatar = $img;
                } else {
                    $avatar = asset('storage/'.$img);
                }
            }

            $plain = trim(html_entity_decode(strip_tags((string) $post->body), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            $plain = preg_replace('/\s+/u', ' ', $plain) ?? $plain;
            if (mb_strlen($plain) > 160) {
                $plain = rtrim(mb_substr($plain, 0, 157)).'…';
            }

            $title = trim((string) ($post->title ?? ''));
            if ($title === '') {
                $title = $plain !== '' ? (mb_strlen($plain) > 80 ? rtrim(mb_substr($plain, 0, 77)).'…' : $plain) : 'Post';
            }

            return [
                'id' => (int) $post->id,
                'author' => $name,
                'avatar' => $avatar,
                'title' => $title,
                'excerpt' => $plain,
                'has_image' => trim((string) ($post->image_path ?? '')) !== '',
                'comments_count' => (int) $post->comments_count,
                'created_at' => $post->created_at?->timezone('Asia/Manila')->format('M j, Y · g:i A'),
                'created_at_iso' => $post->created_at?->toIso8601String(),
            ];
        })->values()->all();
    }

    public function store(Request $request)
    {
        if (! RolePermission::userMayAccessRoute('forum_thread.post')) {
            return $this->deny($request, 'You do not have permission to create posts.');
        }

        $userId = (int) session('user_id', 0);
        if ($userId <= 0) {
            return $this->deny($request, 'Please log in first.', 401);
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'body' => ['nullable', 'string', 'max:20000'],
            'post_type' => ['required', 'in:discussion,announcement'],
            'image' => ['nullable', 'file', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:4096'],
        ], [
            'title.required' => 'Please enter a title.',
            'image.uploaded' => 'The image failed to upload. It may be too large — try a photo under 4MB (JPG, PNG, GIF, or WebP).',
            'image.image' => 'The selected file must be an image (JPG, PNG, GIF, or WebP).',
            'image.mimes' => 'The image must be a JPG, PNG, GIF, or WebP file.',
            'image.max' => 'The cover image may not be greater than 4MB.',
        ]);

        $title = trim((string) ($data['title'] ?? ''));
        $body = $this->sanitizePostHtml((string) ($data['body'] ?? ''));
        $postType = strtolower(trim((string) ($data['post_type'] ?? ForumPost::TYPE_DISCUSSION)));
        if (! in_array($postType, [ForumPost::TYPE_DISCUSSION, ForumPost::TYPE_ANNOUNCEMENT], true)) {
            $postType = ForumPost::TYPE_DISCUSSION;
        }
        $uploaded = $request->file('image');
        $hasImage = $uploaded instanceof \Illuminate\Http\UploadedFile && $uploaded->isValid();

        if ($uploaded && ! $hasImage) {
            return redirect()
                ->route('forum_thread')
                ->with('error', 'The image failed to upload. It may be too large or the file is corrupted. Try a smaller JPG/PNG under 4MB.')
                ->withInput();
        }

        if ($title === '') {
            return redirect()
                ->route('forum_thread')
                ->with('error', 'Please enter a title.')
                ->withInput();
        }

        if ($this->isEmptyHtml($body) && ! $hasImage) {
            return redirect()
                ->route('forum_thread')
                ->with('error', 'Write a description or attach a cover image to post.')
                ->withInput();
        }

        $imagePath = null;
        if ($hasImage) {
            try {
                Storage::disk('public')->makeDirectory('forum');
                $ext = strtolower($uploaded->getClientOriginalExtension() ?: 'jpg');
                if (! in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
                    $ext = 'jpg';
                }
                $safeName = 'post_'.now('Asia/Manila')->format('YmdHis').'_'.Str::lower(Str::random(8)).'.'.$ext;
                $stored = $uploaded->storeAs('forum', $safeName, 'public');
                if (! $stored || ! Storage::disk('public')->exists($stored)) {
                    return redirect()
                        ->route('forum_thread')
                        ->with('error', 'Could not save the image. Please try again.')
                        ->withInput();
                }
                $imagePath = $stored;
            } catch (\Throwable $e) {
                report($e);

                return redirect()
                    ->route('forum_thread')
                    ->with('error', 'Could not save the image. Please try again.')
                    ->withInput();
            }
        }

        ForumPost::create([
            'user_id' => $userId,
            'title' => $title,
            'body' => $body,
            'post_type' => $postType,
            'image_path' => $imagePath,
        ]);

        return redirect()
            ->route('forum_thread')
            ->with('success', $postType === ForumPost::TYPE_ANNOUNCEMENT ? 'Announcement posted.' : 'Discussion posted.');
    }

    public function destroy(Request $request, int $id)
    {
        $post = ForumPost::findOrFail($id);
        $userId = (int) session('user_id', 0);
        $isAdmin = strtolower(trim((string) session('user_role', ''))) === 'admin';
        $isOwner = (int) $post->user_id === $userId;

        if (! $isAdmin) {
            if (! RolePermission::userMayAccessRoute('forum_thread.destroy')) {
                return $this->deny($request, 'You do not have permission to delete posts.');
            }
            if (! $isOwner) {
                return $this->deny($request, 'You can only delete your own posts.');
            }
        }

        if ($post->image_path && Storage::disk('public')->exists($post->image_path)) {
            Storage::disk('public')->delete($post->image_path);
        }

        $post->comments()->delete();
        $post->delete();

        return redirect()
            ->route('forum_thread')
            ->with('success', 'Post deleted.');
    }

    public function storeComment(Request $request, int $id)
    {
        if (! RolePermission::userMayAccessRoute('forum_thread.comment')) {
            return $this->deny($request, 'You do not have permission to comment.');
        }

        $userId = (int) session('user_id', 0);
        if ($userId <= 0) {
            return $this->deny($request, 'Please log in first.', 401);
        }

        $post = ForumPost::findOrFail($id);

        if (! $post->allowsComments()) {
            return $this->deny($request, 'Comments are disabled for announcements.');
        }

        $data = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        ForumComment::create([
            'forum_post_id' => $post->id,
            'user_id' => $userId,
            'body' => trim($data['body']),
        ]);

        return redirect()
            ->route('forum_thread')
            ->with('success', 'Comment added.')
            ->withFragment('post-'.$post->id);
    }

    public function destroyComment(Request $request, int $id)
    {
        $comment = ForumComment::findOrFail($id);
        $userId = (int) session('user_id', 0);
        $isAdmin = strtolower(trim((string) session('user_role', ''))) === 'admin';
        $isOwner = (int) $comment->user_id === $userId;

        if (! $isAdmin) {
            if (! RolePermission::userMayAccessRoute('forum_thread.comment.destroy')) {
                return $this->deny($request, 'You do not have permission to delete comments.');
            }
            if (! $isOwner) {
                return $this->deny($request, 'You can only delete your own comments.');
            }
        }

        $postId = (int) $comment->forum_post_id;
        $comment->delete();

        return redirect()
            ->route('forum_thread')
            ->with('success', 'Comment deleted.')
            ->withFragment('post-'.$postId);
    }

    public function image(string $path): Response
    {
        $safe = basename($path);
        $full = 'forum/'.$safe;
        if ($safe === '' || ! Storage::disk('public')->exists($full)) {
            abort(404);
        }

        return response()->file(Storage::disk('public')->path($full));
    }

    private function deny(Request $request, string $message, int $status = 403)
    {
        if ($request->expectsJson()) {
            return response()->json(['status' => 'error', 'message' => $message], $status);
        }

        return redirect()
            ->route('forum_thread')
            ->with('error', $message);
    }

    private function sanitizePostHtml(string $html): string
    {
        $html = trim($html);
        if ($html === '') {
            return '';
        }

        $allowed = '<p><br><b><strong><i><em><u><ul><ol><li><div><span><a>';
        $clean = strip_tags($html, $allowed);
        $clean = preg_replace('/\son\w+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $clean) ?? $clean;
        $clean = preg_replace('/\s(href|src)\s*=\s*("\s*javascript:[^"]*"|\'\s*javascript:[^\']*\'|javascript:[^\s>]+)/i', '', $clean) ?? $clean;
        // Keep only safe href on anchors
        $clean = preg_replace_callback('/<a\b([^>]*)>/i', static function (array $m): string {
            $attrs = $m[1] ?? '';
            $href = '';
            if (preg_match('/\bhref\s*=\s*("([^"]*)"|\'([^\']*)\'|([^\s>]+))/i', $attrs, $hm)) {
                $href = trim((string) ($hm[2] ?? $hm[3] ?? $hm[4] ?? ''));
            }
            if ($href === '' || ! preg_match('#^(https?://|/|#)#i', $href)) {
                return '<a>';
            }

            return '<a href="'.e($href).'" target="_blank" rel="noopener noreferrer">';
        }, $clean) ?? $clean;

        return trim($clean);
    }

    private function isEmptyHtml(string $html): bool
    {
        $text = trim(html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        $text = preg_replace('/\x{00A0}|\s+/u', '', $text) ?? $text;

        return $text === '';
    }
}
