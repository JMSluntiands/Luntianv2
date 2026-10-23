<?php

namespace App\Http\Controllers;

use App\Services\AllocatedJobsTaskFeed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class DiscussionReplyController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'source' => ['required', Rule::in(['run_comments', 'comments', 'bph_run_comments', 'bph_comments'])],
            'parent_id' => ['required', 'integer', 'min:1'],
            'job_id' => ['required', 'integer', 'min:1'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $message = trim(preg_replace('/\s+/u', ' ', strip_tags($data['message'])) ?? '');
        if ($message === '') {
            return response()->json(['status' => 'error', 'message' => 'Write a reply first.'], 422);
        }

        $idColumn = str_contains($data['source'], 'run_comment') ? 'run_comment_id' : 'comment_id';
        if (! Schema::hasTable($data['source'])) {
            return response()->json(['status' => 'error', 'message' => 'Comment not found.'], 404);
        }

        $parent = DB::table($data['source'])
            ->where($idColumn, (int) $data['parent_id'])
            ->where('job_id', (int) $data['job_id'])
            ->first();
        if (! $parent) {
            return response()->json(['status' => 'error', 'message' => 'Comment not found.'], 404);
        }

        $this->ensureTable();
        $name = trim((string) (session('user_name') ?? ''));
        if ($name === '') {
            $name = 'LUNTIAN';
        }
        $createdAt = now('Asia/Manila')->format('M d, Y h:i A');
        $id = DB::table('discussion_replies')->insertGetId([
            'source' => $data['source'],
            'parent_id' => (int) $data['parent_id'],
            'job_id' => (int) $data['job_id'],
            'author_name' => $name,
            'message' => $message,
            'created_at' => $createdAt,
        ]);

        try {
            $this->notifyOthers($parent, $data['source'], (int) $data['parent_id'], (int) $data['job_id'], (int) $id, $createdAt);
        } catch (\Throwable) {
            // The reply is already saved. A notification failure should not block it.
        }

        return response()->json([
            'status' => 'success',
            'reply' => [
                'id' => $id,
                'source' => $data['source'],
                'parent_id' => (int) $data['parent_id'],
                'author_name' => $name,
                'message' => $message,
                'created_at' => $createdAt,
            ],
        ]);
    }

    public function open(int $id)
    {
        $userId = (int) session('user_id', 0);
        if ($userId <= 0 || ! Schema::hasTable('discussion_reply_notifications')) {
            abort(404);
        }

        $row = DB::table('discussion_reply_notifications')
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();
        if (! $row) {
            abort(404);
        }

        if ($row->read_at === null) {
            DB::table('discussion_reply_notifications')
                ->where('id', $id)
                ->update(['read_at' => now()]);
        }

        $target = trim((string) ($row->target_url ?? ''));
        $appUrl = rtrim((string) url('/'), '/');
        if ($target !== '' && str_starts_with($target, $appUrl)) {
            return redirect()->to($target);
        }

        return redirect()->route('task_management.job_notes');
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => ['required', 'file', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:5120'],
        ]);

        $file = $request->file('image');
        $ext = strtolower((string) ($file->getClientOriginalExtension() ?: $file->extension() ?: 'png'));
        if (! in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
            $ext = 'png';
        }
        $name = bin2hex(random_bytes(16)).'.'.$ext;
        $file->storeAs('discussion-images', $name, 'local');

        return response()->json([
            'status' => 'success',
            'url' => route('discussion.image', ['file' => $name], false),
        ]);
    }

    public function image(string $file)
    {
        if (! preg_match('/^[A-Za-z0-9._-]+$/', $file)) {
            abort(404);
        }

        $path = 'discussion-images/'.$file;
        if (! Storage::disk('local')->exists($path)) {
            abort(404);
        }

        return Storage::disk('local')->response($path);
    }

    private function notifyOthers(object $parent, string $source, int $parentId, int $jobId, int $replyId, string $createdAt): void
    {
        $this->ensureNotificationsTable();
        $actorId = (int) session('user_id', 0);
        $recipientIds = $this->recipientIds($parent, $source, $parentId, $jobId, $actorId);
        if ($recipientIds === []) {
            return;
        }

        $target = AllocatedJobsTaskFeed::discussionAnchorUrl($source, $jobId, $parentId);
        $rows = [];
        foreach ($recipientIds as $userId) {
            $rows[] = [
                'user_id' => $userId,
                'reply_id' => $replyId,
                'job_id' => $jobId,
                'target_url' => $target,
                'created_at' => $createdAt,
                'read_at' => null,
            ];
        }

        DB::table('discussion_reply_notifications')->insert($rows);
    }

    /**
     * @return list<int>
     */
    private function recipientIds(object $parent, string $source, int $parentId, int $jobId, int $actorId): array
    {
        if (! Schema::hasTable('users')) {
            return [];
        }

        $codes = [];
        if (preg_match_all('/data-code=(["\'])([^"\']+)\1/i', (string) ($parent->message ?? ''), $matches)) {
            foreach ($matches[2] as $code) {
                $code = strtoupper(trim(html_entity_decode((string) $code, ENT_QUOTES | ENT_HTML5, 'UTF-8')));
                if ($code !== '') {
                    $codes[$code] = true;
                }
            }
        }

        $names = [];
        foreach (['name', 'username'] as $column) {
            $value = mb_strtolower(trim((string) ($parent->{$column} ?? '')));
            if ($value !== '') {
                $names[$value] = true;
            }
        }
        if (Schema::hasTable('discussion_replies')) {
            $authors = DB::table('discussion_replies')
                ->where('source', $source)
                ->where('parent_id', $parentId)
                ->where('job_id', $jobId)
                ->pluck('author_name');
            foreach ($authors as $author) {
                $value = mb_strtolower(trim((string) $author));
                if ($value !== '') {
                    $names[$value] = true;
                }
            }
        }

        $ids = [];
        $users = DB::table('users')->get(['id', 'unique_code', 'fullname', 'username']);
        foreach ($users as $user) {
            $userId = (int) $user->id;
            if ($userId <= 0 || $userId === $actorId) {
                continue;
            }
            $code = strtoupper(trim((string) ($user->unique_code ?? '')));
            $full = mb_strtolower(trim((string) ($user->fullname ?? '')));
            $username = mb_strtolower(trim((string) ($user->username ?? '')));
            if (($code !== '' && isset($codes[$code])) || ($full !== '' && isset($names[$full])) || ($username !== '' && isset($names[$username]))) {
                $ids[$userId] = $userId;
            }
        }

        return array_values($ids);
    }

    private function ensureNotificationsTable(): void
    {
        if (Schema::hasTable('discussion_reply_notifications')) {
            return;
        }

        Schema::create('discussion_reply_notifications', function ($table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('reply_id');
            $table->unsignedBigInteger('job_id');
            $table->string('target_url', 500)->nullable();
            $table->string('created_at', 50)->nullable();
            $table->timestamp('read_at')->nullable();
            $table->unique(['user_id', 'reply_id']);
            $table->index(['user_id', 'read_at']);
        });
    }

    private function ensureTable(): void
    {
        if (Schema::hasTable('discussion_replies')) {
            return;
        }

        Schema::create('discussion_replies', function ($table) {
            $table->id();
            $table->string('source', 40);
            $table->unsignedBigInteger('parent_id');
            $table->unsignedBigInteger('job_id');
            $table->string('author_name', 120);
            $table->text('message');
            $table->string('created_at', 50)->nullable();
            $table->index(['source', 'parent_id']);
            $table->index('job_id');
        });
    }
}
