<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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
