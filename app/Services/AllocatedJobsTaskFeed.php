<?php

namespace App\Services;

use App\Models\RolePermission;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Collects Allocated jobs across all Job Management modules for Task Management.
 * Only client + reference (plus module / assignee code for display) are returned.
 */
class AllocatedJobsTaskFeed
{
    private static ?Collection $taggedMentionCache = null;
    /**
     * @return Collection<int, object{
     *   key: string,
     *   module: string,
     *   client: string,
     *   reference: string,
     *   job_id: int|null,
     *   view_url: string|null,
     *   assignee_code: string|null,
     *   assignee_user_id: int|null,
     *   sort_at: string|null
     * }>
     */
    public static function all(): Collection
    {
        $rows = collect();

        $rows = $rows
            ->concat(self::lbsStyleAllocated(
                module: 'LBS',
                scope: static function ($q): void {
                    JobCountsScope::applyLbsStandardJobsScope($q, '');
                }
            ))
            ->concat(self::lbsStyleAllocated(
                module: 'Efficient Living',
                scope: static function ($q): void {
                    $q->whereRaw("job_request_id LIKE 'EA\\_EL\\_%'");
                }
            ))
            ->concat(self::lbsStyleAllocated(
                module: 'LUNTIAN',
                scope: static function ($q): void {
                    JobCountsScope::applyLuntianJobsScope($q, '');
                }
            ));

        if (Schema::hasTable('job_general_assembly')) {
            $rows = $rows->concat(self::generalAssemblyAllocated());
        }

        $rows = $rows
            ->concat(self::bphStyleAllocated(
                module: 'BPH',
                table: 'job_bph',
                extra: static function ($q): void {
                    $q->whereRaw('LOWER(TRIM(COALESCE(client_code, \'\'))) NOT IN (?, ?, ?)', ['bluinq01', 'amt01', 'fyrs01']);
                }
            ))
            ->concat(self::bphStyleAllocated(
                module: 'BluInq',
                table: 'job_bph',
                extra: static function ($q): void {
                    $q->where('client_code', 'BLUINQ01');
                }
            ));

        $amtTable = Schema::hasTable('job_amt') ? 'job_amt' : 'job_bph';
        $rows = $rows->concat(self::bphStyleAllocated(
            module: 'A&M',
            table: $amtTable,
            extra: $amtTable === 'job_bph'
                ? static function ($q): void {
                    $q->where('client_code', 'AMT01');
                }
                : null
        ));

        $fyrsTable = Schema::hasTable('job_fyrs') ? 'job_fyrs' : 'job_bph';
        $rows = $rows->concat(self::bphStyleAllocated(
            module: 'FYRS ENERGYWISE',
            table: $fyrsTable,
            extra: $fyrsTable === 'job_bph'
                ? static function ($q): void {
                    $q->where('client_code', 'FYRS01');
                }
                : null
        ));

        foreach ([
            'CSP' => 'job_csp',
            'NH' => 'job_nh',
            'LC Home Builder' => 'job_lc_home_builder',
            'Leading Energy' => 'job_leading_energy',
        ] as $module => $table) {
            if (Schema::hasTable($table)) {
                $rows = $rows->concat(self::bphStyleAllocated(module: $module, table: $table));
            }
        }

        $codeToUserId = self::assigneeCodeMap();

        return $rows
            ->map(function (object $row) use ($codeToUserId) {
                $code = $row->assignee_code !== null ? strtoupper(trim((string) $row->assignee_code)) : '';
                $row->assignee_code = $code !== '' ? $code : null;
                $row->assignee_user_id = ($code !== '' && isset($codeToUserId[$code]))
                    ? $codeToUserId[$code]
                    : null;
                $jobId = (int) ($row->job_id ?? 0);
                $row->view_url = $jobId > 0 ? self::viewUrlForModule((string) $row->module, $jobId) : null;

                return $row;
            })
            ->sortByDesc(fn (object $row) => $row->sort_at ?? '')
            ->values();
    }

    /**
     * Jobs that have a note, across the same modules as the task feed.
     *
     * @return Collection<int, object>
     */
    public static function withNotes(): Collection
    {
        $rows = collect();

        $rows = $rows
            ->concat(self::lbsStyleAllocated(
                module: 'LBS',
                scope: static function ($q): void {
                    JobCountsScope::applyLbsStandardJobsScope($q, '');
                },
                notesOnly: true,
            ))
            ->concat(self::lbsStyleAllocated(
                module: 'Efficient Living',
                scope: static function ($q): void {
                    $q->whereRaw("job_request_id LIKE 'EA\\_EL\\_%'");
                },
                notesOnly: true,
            ))
            ->concat(self::lbsStyleAllocated(
                module: 'LUNTIAN',
                scope: static function ($q): void {
                    JobCountsScope::applyLuntianJobsScope($q, '');
                },
                notesOnly: true,
            ));

        if (Schema::hasTable('job_general_assembly')) {
            $rows = $rows->concat(self::generalAssemblyAllocated(notesOnly: true));
        }

        $rows = $rows
            ->concat(self::bphStyleAllocated(
                module: 'BPH',
                table: 'job_bph',
                extra: static function ($q): void {
                    $q->whereRaw('LOWER(TRIM(COALESCE(client_code, \'\'))) NOT IN (?, ?, ?)', ['bluinq01', 'amt01', 'fyrs01']);
                },
                notesOnly: true,
            ))
            ->concat(self::bphStyleAllocated(
                module: 'BluInq',
                table: 'job_bph',
                extra: static function ($q): void {
                    $q->where('client_code', 'BLUINQ01');
                },
                notesOnly: true,
            ));

        $amtTable = Schema::hasTable('job_amt') ? 'job_amt' : 'job_bph';
        $rows = $rows->concat(self::bphStyleAllocated(
            module: 'A&M',
            table: $amtTable,
            extra: $amtTable === 'job_bph'
                ? static function ($q): void {
                    $q->where('client_code', 'AMT01');
                }
                : null,
            notesOnly: true,
        ));

        $fyrsTable = Schema::hasTable('job_fyrs') ? 'job_fyrs' : 'job_bph';
        $rows = $rows->concat(self::bphStyleAllocated(
            module: 'FYRS ENERGYWISE',
            table: $fyrsTable,
            extra: $fyrsTable === 'job_bph'
                ? static function ($q): void {
                    $q->where('client_code', 'FYRS01');
                }
                : null,
            notesOnly: true,
        ));

        foreach ([
            'CSP' => 'job_csp',
            'NH' => 'job_nh',
            'LC Home Builder' => 'job_lc_home_builder',
            'Leading Energy' => 'job_leading_energy',
        ] as $module => $table) {
            if (Schema::hasTable($table)) {
                $rows = $rows->concat(self::bphStyleAllocated(module: $module, table: $table, notesOnly: true));
            }
        }

        $codeToUserId = self::assigneeCodeMap();

        return $rows
            ->map(function (object $row) use ($codeToUserId) {
                $code = $row->assignee_code !== null ? strtoupper(trim((string) $row->assignee_code)) : '';
                $row->assignee_code = $code !== '' ? $code : null;
                $row->assignee_user_id = ($code !== '' && isset($codeToUserId[$code]))
                    ? $codeToUserId[$code]
                    : null;
                $jobId = (int) ($row->job_id ?? 0);
                $row->view_url = $jobId > 0 ? self::viewUrlForModule((string) $row->module, $jobId) : null;
                $row->notes = self::plainNote((string) ($row->notes ?? ''));

                return $row;
            })
            ->filter(fn (object $row) => trim((string) ($row->notes ?? '')) !== '')
            ->sortByDesc(fn (object $row) => $row->sort_at ?? '')
            ->values();
    }

    public static function viewUrlForModule(string $module, int $jobId): ?string
    {
        if ($jobId <= 0) {
            return null;
        }

        $route = match (strtolower(trim($module))) {
            'lbs' => 'lbs.job.view',
            'efficient living' => 'efficient_living.job.view',
            'luntian' => 'luntian.job.view',
            'generic ea', 'general assembly', 'generic assessment' => 'general_assembly.job.view',
            'bph' => 'bph.view',
            'bluinq' => 'bluinq.view',
            'a&m', 'a and m', 'amt' => 'amt.view',
            'fyrs energywise', 'fyrs' => 'fyrs.view',
            'csp' => 'csp.view',
            'leading energy' => 'leading_energy.view',
            default => null,
        };

        if ($route === null || ! \Illuminate\Support\Facades\Route::has($route)) {
            return null;
        }

        return route($route, ['id' => $jobId]);
    }

    public static function discussionAnchorUrl(string $source, int $jobId, int $parentId): ?string
    {
        if ($jobId <= 0 || $parentId <= 0) {
            return null;
        }

        $job = str_starts_with($source, 'bph_')
            ? self::findBphFamilyJob($jobId)
            : self::findJobsFamilyJob($jobId);
        if ($job === null) {
            return null;
        }

        $url = self::viewUrlForModule((string) $job->module, $jobId);
        if ($url === null) {
            return null;
        }

        $kind = str_contains($source, 'run_comment') ? 'run' : 'job';

        return $url.'#discussion-'.$kind.'-'.$parentId;
    }

    /**
     * @return array<string, int>
     */
    private static function assigneeCodeMap(): array
    {
        // Prefer Staff/Checker/User over Admin/Branch when multiple accounts share a code.
        $users = User::query()
            ->whereNotNull('unique_code')
            ->where('unique_code', '!=', '')
            ->orderByRaw(
                "CASE LOWER(TRIM(COALESCE(role, '')))
                    WHEN 'admin' THEN 3
                    WHEN 'branch' THEN 2
                    ELSE 0
                END"
            )
            ->orderBy('id')
            ->get(['id', 'unique_code', 'role']);

        $map = [];
        foreach ($users as $user) {
            $code = strtoupper(trim((string) $user->unique_code));
            if ($code === '' || isset($map[$code])) {
                continue;
            }
            $map[$code] = (int) $user->id;
        }

        return $map;
    }

    /**
     * @param  callable(\Illuminate\Database\Query\Builder): void  $scope
     * @return Collection<int, object>
     */
    private static function lbsStyleAllocated(string $module, callable $scope, bool $notesOnly = false): Collection
    {
        if (! Schema::hasTable('jobs')) {
            return collect();
        }

        $q = DB::table('jobs as j')
            ->leftJoin('client_accounts as ca', 'ca.client_account_id', '=', 'j.client_account_id')
            ->where('j.reference', 'like', 'JOBS%');
        if ($notesOnly) {
            self::whereHasNotes($q, 'j.notes');
        } else {
            $q->where('j.job_status', 'Allocated');
        }
        $scope($q);
        JobCountsScope::applyJobsTableAssignment($q, 'j.staff_id', 'j.checker_id');

        $columns = [
            'j.job_id',
            'j.reference',
            'j.job_reference_no',
            'j.client_code',
            'j.staff_id',
            'j.last_update',
            'j.log_date',
            'ca.client_account_name',
        ];
        if ($notesOnly) {
            $columns[] = 'j.notes';
            $columns[] = 'j.job_status';
            $q->orderByDesc('j.last_update')->limit(200);
        }

        $rows = $q->get($columns);

        return $rows->map(function ($row) use ($module) {
            $client = trim((string) ($row->client_account_name ?? ''));
            if ($client === '') {
                $client = trim((string) ($row->client_code ?? ''));
            }
            $reference = trim((string) ($row->job_reference_no ?? ''));
            if ($reference === '') {
                $reference = trim((string) ($row->reference ?? ''));
            }

            return (object) [
                'key' => 'lbs:'.$module.':'.(int) $row->job_id,
                'module' => $module,
                'client' => $client !== '' ? $client : '—',
                'reference' => $reference !== '' ? $reference : '—',
                'status' => trim((string) ($row->job_status ?? '')),
                'notes' => (string) ($row->notes ?? ''),
                'job_id' => (int) $row->job_id,
                'view_url' => null,
                'assignee_code' => $row->staff_id,
                'assignee_user_id' => null,
                'sort_at' => (string) ($row->last_update ?? $row->log_date ?? ''),
            ];
        });
    }

    /**
     * @return Collection<int, object>
     */
    private static function generalAssemblyAllocated(bool $notesOnly = false): Collection
    {
        $q = DB::table('job_general_assembly as j')
            ->leftJoin('client_accounts as ca', 'ca.client_account_id', '=', 'j.client_account_id')
            ->where('j.reference', 'like', 'JOB%');
        if ($notesOnly) {
            self::whereHasNotes($q, 'j.notes');
        } else {
            $q->where('j.job_status', 'Allocated');
        }
        JobCountsScope::applyJobsTableAssignment($q, 'j.staff_id', 'j.checker_id');

        $columns = [
            'j.job_id',
            'j.reference',
            'j.job_reference_no',
            'j.client_code',
            'j.staff_id',
            'j.last_update',
            'j.log_date',
            'ca.client_account_name',
        ];
        if ($notesOnly) {
            $columns[] = 'j.notes';
            $columns[] = 'j.job_status';
            $q->orderByDesc('j.last_update')->limit(200);
        }

        $rows = $q->get($columns);

        $module = RolePermission::GENERIC_ASSESSMENT_NAME;

        return $rows->map(function ($row) use ($module) {
            $client = trim((string) ($row->client_account_name ?? ''));
            if ($client === '') {
                $client = trim((string) ($row->client_code ?? ''));
            }
            $reference = trim((string) ($row->job_reference_no ?? ''));
            if ($reference === '') {
                $reference = trim((string) ($row->reference ?? ''));
            }

            return (object) [
                'key' => 'ga:'.(int) $row->job_id,
                'module' => $module,
                'client' => $client !== '' ? $client : '—',
                'reference' => $reference !== '' ? $reference : '—',
                'status' => trim((string) ($row->job_status ?? '')),
                'notes' => (string) ($row->notes ?? ''),
                'job_id' => (int) $row->job_id,
                'view_url' => null,
                'assignee_code' => $row->staff_id,
                'assignee_user_id' => null,
                'sort_at' => (string) ($row->last_update ?? $row->log_date ?? ''),
            ];
        });
    }

    /**
     * @param  (callable(\Illuminate\Database\Query\Builder): void)|null  $extra
     * @return Collection<int, object>
     */
    private static function bphStyleAllocated(string $module, string $table, ?callable $extra = null, bool $notesOnly = false): Collection
    {
        if (! Schema::hasTable($table)) {
            return collect();
        }

        $q = DB::table($table);
        if ($notesOnly) {
            self::whereHasNotes($q, 'notes');
        } else {
            $q->whereRaw("LOWER(TRIM(COALESCE(status, ''))) = 'allocated'");
        }
        if ($extra) {
            $extra($q);
        }
        JobCountsScope::applyJobBphAssignment($q);

        $select = ['id', 'reference', 'job_number', 'client_name', 'client_code', 'assigned', 'updated_at', 'created_at'];
        if ($notesOnly) {
            $select[] = 'notes';
            $select[] = 'status';
        }
        $cols = Schema::getColumnListing($table);
        $select = array_values(array_filter($select, fn (string $c) => in_array($c, $cols, true)));
        if ($notesOnly && ! in_array('notes', $select, true)) {
            return collect();
        }
        if ($notesOnly) {
            $order = in_array('updated_at', $cols, true) ? 'updated_at' : (in_array('created_at', $cols, true) ? 'created_at' : 'id');
            $q->orderByDesc($order)->limit(200);
        }

        $rows = $q->get($select);

        return $rows->map(function ($row) use ($module, $table) {
            $client = trim((string) ($row->client_name ?? ''));
            if ($client === '') {
                $client = trim((string) ($row->client_code ?? ''));
            }
            $reference = trim((string) ($row->job_number ?? ''));
            if ($reference === '') {
                $reference = trim((string) ($row->reference ?? ''));
            }

            return (object) [
                'key' => $table.':'.(int) $row->id,
                'module' => $module,
                'client' => $client !== '' ? $client : '—',
                'reference' => $reference !== '' ? $reference : '—',
                'status' => trim((string) ($row->status ?? '')),
                'notes' => (string) ($row->notes ?? ''),
                'job_id' => (int) $row->id,
                'view_url' => null,
                'assignee_code' => $row->assigned ?? null,
                'assignee_user_id' => null,
                'sort_at' => (string) ($row->updated_at ?? $row->created_at ?? ''),
            ];
        });
    }

    private static function whereHasNotes($query, string $column): void
    {
        $query->whereNotNull($column)
            ->whereRaw("TRIM({$column}) <> ''")
            ->whereRaw("TRIM({$column}) NOT IN ('<p></p>', '<p><br></p>', '<p><br/></p>', '<br>', '<br/>')");
    }

    private static function plainNote(string $html): string
    {
        $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\s+/u', ' ', $text) ?? '';

        return trim($text);
    }

    /**
     * Jobs mentioned with @code in Discussion comments.
     * One row per tagged person per job, using the latest comment.
     *
     * @return Collection<int, object>
     */
    public static function taggedMentions(): Collection
    {
        if (self::$taggedMentionCache !== null) {
            return self::$taggedMentionCache;
        }

        $hits = collect();
        foreach ([
            ['run_comments', 'run_comment_id', 'jobs'],
            ['comments', 'comment_id', 'jobs'],
            ['bph_run_comments', 'run_comment_id', 'bph'],
            ['bph_comments', 'comment_id', 'bph'],
        ] as [$table, $idColumn, $family]) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'message')) {
                continue;
            }

            $columns = [$idColumn, 'job_id', 'message', 'created_at'];
            if (Schema::hasColumn($table, 'name')) {
                $columns[] = 'name';
            }
            if (Schema::hasColumn($table, 'username')) {
                $columns[] = 'username';
            }

            $rows = DB::table($table)
                ->where('message', 'like', '%data-code=%')
                ->orderByDesc($idColumn)
                ->limit(400)
                ->get($columns);

            foreach ($rows as $row) {
                $jobId = (int) ($row->job_id ?? 0);
                if ($jobId <= 0) {
                    continue;
                }
                $author = trim((string) ($row->name ?? ''));
                if ($author === '') {
                    $author = trim((string) ($row->username ?? ''));
                }
                $kind = str_contains($table, 'run_comment') ? 'run' : 'job';
                foreach (self::mentionCodes((string) ($row->message ?? '')) as $code) {
                    $hits->push((object) [
                        'family' => $family,
                        'kind' => $kind,
                        'comment_id' => (int) ($row->{$idColumn} ?? 0),
                        'job_id' => $jobId,
                        'code' => $code,
                        'author' => $author,
                        'notes' => self::plainNote((string) ($row->message ?? '')),
                        'created_at' => (string) ($row->created_at ?? ''),
                    ]);
                }
            }
        }

        $latest = [];
        foreach ($hits as $hit) {
            $key = $hit->family.':'.$hit->job_id.':'.$hit->code;
            if (! isset($latest[$key]) || self::commentSortKey($hit) >= self::commentSortKey($latest[$key])) {
                $latest[$key] = $hit;
            }
        }

        $jobs = [];
        $out = collect();
        foreach ($latest as $hit) {
            $cacheKey = $hit->family.':'.$hit->job_id;
            if (! array_key_exists($cacheKey, $jobs)) {
                $jobs[$cacheKey] = $hit->family === 'bph'
                    ? self::findBphFamilyJob($hit->job_id)
                    : self::findJobsFamilyJob($hit->job_id);
            }
            $job = $jobs[$cacheKey];
            if ($job === null) {
                continue;
            }

            $anchor = 'discussion-'.$hit->kind.'-'.$hit->comment_id;
            $viewUrl = self::viewUrlForModule((string) $job->module, $hit->job_id);
            $out->push((object) [
                'key' => $cacheKey.':'.$hit->code,
                'family' => $hit->family,
                'comment_anchor' => $anchor,
                'module' => $job->module,
                'client' => $job->client,
                'reference' => $job->reference,
                'status' => $job->status,
                'notes' => $hit->notes !== '' ? $hit->notes : '@'.$hit->code,
                'author' => (string) ($hit->author ?? ''),
                'tagged' => $hit->code,
                'created_at' => $hit->created_at,
                'job_id' => $hit->job_id,
                'view_url' => $viewUrl ? $viewUrl.'#'.$anchor : null,
                'sort_at' => sprintf('%010d', self::commentSortKey($hit)),
            ]);
        }

        return self::$taggedMentionCache = $out->sortByDesc(fn (object $row) => $row->sort_at)->values();
    }

    public static function unreadTaggedForUser(int $userId, string $code): Collection
    {
        $code = strtoupper(trim($code));
        if ($userId <= 0 || $code === '') {
            return collect();
        }

        $rows = self::taggedMentions()->filter(function (object $row) use ($code) {
            return strtoupper(trim((string) ($row->tagged ?? ''))) === $code;
        })->values();
        if ($rows->isEmpty() || ! Schema::hasTable('job_mention_reads')) {
            return $rows;
        }

        self::ensureMentionSeenColumn();
        $reads = DB::table('job_mention_reads')
            ->where('user_id', $userId)
            ->where('tagged_code', $code)
            ->get(['family', 'job_id', 'tagged_code', 'read_at', 'seen_comment']);
        $readState = [];
        foreach ($reads as $read) {
            $readState[$read->family.':'.(int) $read->job_id.':'.strtoupper((string) $read->tagged_code)] = $read;
        }

        return $rows->filter(function (object $row) use ($readState) {
            $key = ($row->family ?? '').':'.(int) ($row->job_id ?? 0).':'.strtoupper((string) ($row->tagged ?? ''));
            $read = $readState[$key] ?? null;
            if ($read === null) {
                return true;
            }

            return self::mentionIsUnread($row, (string) ($read->seen_comment ?? ''), $read->read_at ?? null);
        })->values();
    }

    public static function mentionIsUnread(object $row, string $seenComment, mixed $readAt): bool
    {
        $anchor = (string) ($row->comment_anchor ?? '');
        if ($seenComment !== '') {
            return $anchor === '' || $anchor !== $seenComment;
        }

        if ($readAt === null || $readAt === '') {
            return true;
        }

        try {
            $readTs = \Carbon\Carbon::parse($readAt)->getTimestamp();
        } catch (\Throwable) {
            return true;
        }

        return self::commentSortKey($row) > $readTs;
    }

    public static function ensureMentionSeenColumn(): void
    {
        if (! Schema::hasTable('job_mention_reads') || Schema::hasColumn('job_mention_reads', 'seen_comment')) {
            return;
        }

        Schema::table('job_mention_reads', function ($table) {
            $table->string('seen_comment', 80)->nullable();
        });
    }

    public static function unreadTaggedCount(int $userId, string $code): int
    {
        return self::unreadTaggedForUser($userId, $code)->count();
    }

    /**
     * @return list<string>
     */
    private static function commentSortKey(object $hit): int
    {
        $created = trim((string) ($hit->created_at ?? ''));
        if ($created !== '') {
            try {
                return \Carbon\Carbon::parse($created, 'Asia/Manila')->getTimestamp();
            } catch (\Throwable) {
                // Fall through to the comment id when the stored date is not parseable.
            }
        }

        return (int) ($hit->comment_id ?? 0);
    }

    private static function mentionCodes(string $html): array
    {
        if (! preg_match_all('/data-code=(["\'])([^"\']+)\1/i', $html, $matches)) {
            return [];
        }

        $codes = [];
        foreach ($matches[2] as $code) {
            $code = strtoupper(trim(html_entity_decode($code, ENT_QUOTES | ENT_HTML5, 'UTF-8')));
            if ($code !== '') {
                $codes[$code] = $code;
            }
        }

        return array_values($codes);
    }

    private static function findJobsFamilyJob(int $jobId): ?object
    {
        if (Schema::hasTable('jobs')) {
            $row = DB::table('jobs as j')
                ->leftJoin('client_accounts as ca', 'ca.client_account_id', '=', 'j.client_account_id')
                ->where('j.job_id', $jobId)
                ->first([
                    'j.reference',
                    'j.job_reference_no',
                    'j.client_code',
                    'j.job_status',
                    'j.job_request_id',
                    'ca.client_account_name',
                ]);
            if ($row) {
                $requestId = (string) ($row->job_request_id ?? '');
                $module = 'LBS';
                if (str_starts_with($requestId, 'EA_EL_')) {
                    $module = 'Efficient Living';
                } elseif (str_starts_with($requestId, 'EA_LT_') || in_array($requestId, JobCountsScope::luntianJobRequestIds(), true)) {
                    $module = 'LUNTIAN';
                }

                return self::jobSummary(
                    $module,
                    (string) ($row->client_account_name ?? ''),
                    (string) ($row->client_code ?? ''),
                    (string) ($row->job_reference_no ?? ''),
                    (string) ($row->reference ?? ''),
                    (string) ($row->job_status ?? '')
                );
            }
        }

        if (! Schema::hasTable('job_general_assembly')) {
            return null;
        }

        $row = DB::table('job_general_assembly as j')
            ->leftJoin('client_accounts as ca', 'ca.client_account_id', '=', 'j.client_account_id')
            ->where('j.job_id', $jobId)
            ->first([
                'j.reference',
                'j.job_reference_no',
                'j.client_code',
                'j.job_status',
                'ca.client_account_name',
            ]);
        if (! $row) {
            return null;
        }

        return self::jobSummary(
            RolePermission::GENERIC_ASSESSMENT_NAME,
            (string) ($row->client_account_name ?? ''),
            (string) ($row->client_code ?? ''),
            (string) ($row->job_reference_no ?? ''),
            (string) ($row->reference ?? ''),
            (string) ($row->job_status ?? '')
        );
    }

    private static function findBphFamilyJob(int $jobId): ?object
    {
        foreach ([
            'job_bph' => null,
            'job_amt' => 'A&M',
            'job_fyrs' => 'FYRS ENERGYWISE',
            'job_csp' => 'CSP',
            'job_nh' => 'NH',
            'job_lc_home_builder' => 'LC Home Builder',
            'job_leading_energy' => 'Leading Energy',
        ] as $table => $module) {
            if (! Schema::hasTable($table)) {
                continue;
            }
            $row = DB::table($table)->where('id', $jobId)->first();
            if (! $row) {
                continue;
            }
            if ($module === null) {
                $clientCode = strtoupper(trim((string) ($row->client_code ?? '')));
                $module = match ($clientCode) {
                    'BLUINQ01' => 'BluInq',
                    'AMT01' => 'A&M',
                    'FYRS01' => 'FYRS ENERGYWISE',
                    default => 'BPH',
                };
            }

            $reference = trim((string) ($row->job_number ?? ''));
            if ($reference === '') {
                $reference = trim((string) ($row->reference ?? ''));
            }

            return self::jobSummary(
                $module,
                (string) ($row->client_name ?? ''),
                (string) ($row->client_code ?? ''),
                $reference,
                (string) ($row->reference ?? ''),
                (string) ($row->status ?? '')
            );
        }

        return null;
    }

    private static function jobSummary(string $module, string $clientName, string $clientCode, string $primaryReference, string $fallbackReference, string $status): object
    {
        $client = trim($clientName);
        if ($client === '') {
            $client = trim($clientCode);
        }
        $reference = trim($primaryReference);
        if ($reference === '') {
            $reference = trim($fallbackReference);
        }

        return (object) [
            'module' => $module,
            'client' => $client !== '' ? $client : '—',
            'reference' => $reference !== '' ? $reference : '—',
            'status' => trim($status),
        ];
    }
}
