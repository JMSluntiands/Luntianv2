<?php

namespace App\Http\Controllers;

use App\Models\ForumComment;
use App\Models\ForumPost;
use App\Models\RolePermission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class ForumThreadController extends Controller
{
    public function index()
    {
        if (! Schema::hasTable('forum_posts')) {
            return redirect()
                ->route('dashboard')
                ->with('error', 'Bulletin is not ready yet. Run database/jms_fix_forum_thread.sql on the live database.');
        }

        $posts = ForumPost::query()
            ->with(['user:id,fullname,username,profile_image', 'comments.user:id,fullname,username,profile_image'])
            ->withCount('comments')
            ->feedOrder()
            ->paginate(20);

        $userId = (int) session('user_id', 0);
        $role = strtolower(trim((string) session('user_role', '')));
        $isAdmin = $role === 'admin';
        $canPost = RolePermission::userMayAccessRoute('forum_thread.post');

        return view('forum-thread.index', [
            'sidebar_active' => 'forum_thread',
            'posts' => $posts,
            'currentUser' => $userId > 0 ? User::find($userId) : null,
            'canPost' => $canPost,
            'canPin' => $isAdmin || $canPost,
            'canComment' => RolePermission::userMayAccessRoute('forum_thread.comment'),
            'canDeletePost' => RolePermission::userMayAccessRoute('forum_thread.destroy'),
            'canDeleteComment' => RolePermission::userMayAccessRoute('forum_thread.comment.destroy'),
            'isAdmin' => $isAdmin,
            'currentUserId' => $userId,
        ]);
    }

    public function recent()
    {
        return response()->json([
            'posts' => self::recentPostsPayload(5),
            'feed_url' => route('forum_thread', [], false),
        ]);
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function recentPostsPayload(int $limit = 5): array
    {
        if (! Schema::hasTable('forum_posts')) {
            return [];
        }

        $posts = ForumPost::query()
            ->with(['user:id,fullname,username,profile_image'])
            ->withCount('comments')
            ->feedOrder()
            ->limit(max(1, min($limit, 5)))
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
                $plain = rtrim(mb_substr($plain, 0, 157)).'â€¦';
            }

            $title = trim((string) ($post->title ?? ''));
            if ($title === '') {
                $title = $plain !== '' ? (mb_strlen($plain) > 80 ? rtrim(mb_substr($plain, 0, 77)).'â€¦' : $plain) : 'Post';
            }

            return [
                'id' => (int) $post->id,
                'author' => $name,
                'avatar' => $avatar,
                'title' => $title,
                'excerpt' => $plain,
                'has_image' => trim((string) ($post->image_path ?? '')) !== '' || stripos((string) $post->body, '<img') !== false,
                'status' => $post->normalizeType(),
                'is_pinned' => $post->isPinned(),
                'comments_count' => (int) $post->comments_count,
                'created_at' => $post->created_at?->timezone('Asia/Manila')->format('M j, Y Â· g:i A'),
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
            'image.uploaded' => 'The image failed to upload. It may be too large â€” try a photo under 4MB (JPG, PNG, GIF, or WebP).',
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
            $imagePath = $this->storeCoverImage($uploaded);
            if ($imagePath === null) {
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

    public function update(Request $request, int $id)
    {
        $post = ForumPost::findOrFail($id);
        $userId = (int) session('user_id', 0);
        $isAdmin = strtolower(trim((string) session('user_role', ''))) === 'admin';

        if (! $isAdmin) {
            if (! RolePermission::userMayAccessRoute('forum_thread.post')) {
                return $this->deny($request, 'You do not have permission to edit posts.');
            }
            if ((int) $post->user_id !== $userId) {
                return $this->deny($request, 'You can only edit your own posts.');
            }
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'body' => ['nullable', 'string', 'max:20000'],
            'post_type' => ['required', 'in:discussion,announcement'],
            'image' => ['nullable', 'file', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:4096'],
            'remove_image' => ['nullable', 'boolean'],
        ], [
            'title.required' => 'Please enter a title.',
            'image.uploaded' => 'The image failed to upload. It may be too large — try a photo under 4MB (JPG, PNG, GIF, or WebP).',
            'image.image' => 'The selected file must be an image (JPG, PNG, GIF, or WebP).',
            'image.mimes' => 'The image must be a JPG, PNG, GIF, or WebP file.',
            'image.max' => 'The cover image may not be greater than 4MB.',
        ]);

        $failed = fn (string $message) => redirect()
            ->route('forum_thread')
            ->with('error', $message)
            ->withInput();

        $title = trim((string) ($data['title'] ?? ''));
        $body = $this->sanitizePostHtml((string) ($data['body'] ?? ''));
        $postType = strtolower(trim((string) ($data['post_type'] ?? ForumPost::TYPE_DISCUSSION)));
        if (! in_array($postType, [ForumPost::TYPE_DISCUSSION, ForumPost::TYPE_ANNOUNCEMENT], true)) {
            $postType = ForumPost::TYPE_DISCUSSION;
        }
        $uploaded = $request->file('image');
        $hasNewImage = $uploaded instanceof \Illuminate\Http\UploadedFile && $uploaded->isValid();

        if ($uploaded && ! $hasNewImage) {
            return $failed('The image failed to upload. It may be too large or the file is corrupted. Try a smaller JPG/PNG under 4MB.');
        }
        if ($title === '') {
            return $failed('Please enter a title.');
        }

        $oldImagePath = trim((string) ($post->image_path ?? ''));
        $keepsOldImage = $oldImagePath !== '' && ! $hasNewImage && ! $request->boolean('remove_image');
        if ($this->isEmptyHtml($body) && ! $hasNewImage && ! $keepsOldImage) {
            return $failed('Write a description or attach a cover image to post.');
        }

        $imagePath = $keepsOldImage ? $oldImagePath : null;
        if ($hasNewImage) {
            $imagePath = $this->storeCoverImage($uploaded);
            if ($imagePath === null) {
                return $failed('Could not save the image. Please try again.');
            }
        }

        $removedInline = array_diff(
            self::inlineImageNames((string) $post->body),
            self::inlineImageNames($body)
        );

        $post->update([
            'title' => $title,
            'body' => $body,
            'post_type' => $postType,
            'image_path' => $imagePath,
        ]);

        if ($oldImagePath !== '' && $oldImagePath !== $imagePath && Storage::disk('public')->exists($oldImagePath)) {
            Storage::disk('public')->delete($oldImagePath);
        }
        foreach ($removedInline as $inlineName) {
            Storage::disk('public')->delete('forum/'.$inlineName);
        }

        return redirect()
            ->route('forum_thread')
            ->with('success', 'Post updated.')
            ->withFragment('post-'.$post->id);
    }

    public function uploadInlineImage(Request $request)
    {
        if (! RolePermission::userMayAccessRoute('forum_thread.post')) {
            return $this->deny($request, 'You do not have permission to create posts.');
        }

        $request->validate([
            'image' => ['required', 'file', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:4096'],
        ], [
            'image.uploaded' => 'The image failed to upload. Try a picture under 4MB.',
            'image.image' => 'Only JPG, PNG, GIF, or WebP pictures can be added.',
            'image.mimes' => 'Only JPG, PNG, GIF, or WebP pictures can be added.',
            'image.max' => 'Each picture must be 4MB or smaller.',
        ]);

        $uploaded = $request->file('image');
        $ext = strtolower($uploaded->getClientOriginalExtension() ?: $uploaded->extension() ?: 'png');
        if (! in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
            $ext = 'png';
        }
        $name = 'inline_'.now('Asia/Manila')->format('YmdHis').'_'.Str::lower(Str::random(10)).'.'.$ext;
        $stored = $uploaded->storeAs('forum', $name, 'public');
        if (! $stored) {
            return response()->json(['status' => 'error', 'message' => 'Could not save the picture. Please try again.'], 500);
        }

        return response()->json([
            'status' => 'success',
            'url' => route('forum_thread.image', ['path' => $name], false),
        ]);
    }

    public function togglePin(Request $request, int $id)
    {
        $isAdmin = strtolower(trim((string) session('user_role', ''))) === 'admin';
        if (! $isAdmin && ! RolePermission::userMayAccessRoute('forum_thread.post')) {
            return $this->deny($request, 'You do not have permission to pin posts.');
        }

        $post = ForumPost::findOrFail($id);
        $wasPinned = $post->isPinned();
        $post->pinned_at = $wasPinned ? null : now();
        $post->save();

        return redirect()
            ->route('forum_thread')
            ->with('success', $wasPinned ? 'Post unpinned.' : 'Post pinned to the top.')
            ->withFragment('post-'.$post->id);
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
        foreach (self::inlineImageNames((string) $post->body) as $inlineName) {
            Storage::disk('public')->delete('forum/'.$inlineName);
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

    private function storeCoverImage(\Illuminate\Http\UploadedFile $uploaded): ?string
    {
        try {
            Storage::disk('public')->makeDirectory('forum');
            $ext = strtolower($uploaded->getClientOriginalExtension() ?: 'jpg');
            if (! in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
                $ext = 'jpg';
            }
            $safeName = 'post_'.now('Asia/Manila')->format('YmdHis').'_'.Str::lower(Str::random(8)).'.'.$ext;
            $stored = $uploaded->storeAs('forum', $safeName, 'public');

            return $stored && Storage::disk('public')->exists($stored) ? $stored : null;
        } catch (\Throwable $e) {
            report($e);

            return null;
        }
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

        $allowed = '<p><br><b><strong><i><em><u><ul><ol><li><div><span><a><img>';
        $clean = strip_tags($html, $allowed);
        // Only pictures uploaded through the composer are kept; their tags are rebuilt with no other attributes.
        $clean = preg_replace_callback('/<img\b([^>]*)>/i', static function (array $m): string {
            $name = self::inlineImageNameFromAttrs($m[1] ?? '');

            return $name === null
                ? ''
                : '<img src="'.e(route('forum_thread.image', ['path' => $name], false)).'" alt="" loading="lazy">';
        }, $clean) ?? $clean;
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

    private static function inlineImageNameFromAttrs(string $attrs): ?string
    {
        if (! preg_match('/\bsrc\s*=\s*("([^"]*)"|\'([^\']*)\')/i', $attrs, $sm)) {
            return null;
        }
        $src = html_entity_decode(trim((string) (($sm[2] ?? '') !== '' ? $sm[2] : ($sm[3] ?? ''))), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        if (! preg_match('#^(?:https?://[^/]+)?(?:/[^?\#]*)?/dashboard/forum-thread/image/(inline_[A-Za-z0-9._-]+)$#', $src, $nm)) {
            return null;
        }

        return Storage::disk('public')->exists('forum/'.$nm[1]) ? $nm[1] : null;
    }

    /**
     * @return list<string>
     */
    public static function inlineImageNames(string $html): array
    {
        if (! preg_match_all('#/dashboard/forum-thread/image/(inline_[A-Za-z0-9._-]+)#', $html, $m)) {
            return [];
        }

        return array_values(array_unique($m[1]));
    }

    private function isEmptyHtml(string $html): bool
    {
        if (stripos($html, '<img') !== false) {
            return false;
        }
        $text = trim(html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        $text = preg_replace('/\x{00A0}|\s+/u', '', $text) ?? $text;

        return $text === '';
    }
}
