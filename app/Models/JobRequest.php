<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class JobRequest extends Model
{
    protected $table = 'job_requests';

    public $timestamps = false;

    protected $fillable = ['client_code', 'job_request_id', 'job_request_type'];

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_code', 'client_code');
    }

    /** @return list<string> */
    public static function luntianClientCodesForQuery(): array
    {
        $codes = ['LT01', 'Luntian'];

        if (Schema::hasTable('clients')) {
            $fromDb = DB::table('clients')
                ->where(function ($q) {
                    $q->whereRaw('LOWER(TRIM(client_code)) IN (?, ?)', ['lt01', 'luntian'])
                        ->orWhereRaw('LOWER(client_name) LIKE ?', ['%luntian%']);
                })
                ->pluck('client_code')
                ->map(static fn ($c) => trim((string) $c))
                ->filter()
                ->all();
            $codes = array_merge($codes, $fromDb);
        }

        return array_values(array_unique($codes));
    }

    /** Luntian add/list: LT01, Luntian (any case), client name match, or EA_LT_* request IDs. */
    public function scopeForLuntianVertical(Builder $query): Builder
    {
        $codes = self::luntianClientCodesForQuery();

        return $query->where(function (Builder $q) use ($codes) {
            $q->whereRaw('LOWER(TRIM(client_code)) IN (?, ?)', ['lt01', 'luntian'])
                ->orWhereRaw("job_request_id LIKE 'EA\\_LT\\_%'");

            if ($codes !== []) {
                $q->orWhereIn('client_code', $codes);
            }
        });
    }

    /** Idempotent: mirror LBS01 job types as LT01 / EA_LT_* when none exist yet. */
    public static function seedLuntianTypesFromLbsIfMissing(): int
    {
        if (! Schema::hasTable('job_requests')) {
            return 0;
        }

        $existing = self::query()->forLuntianVertical()->count();
        if ($existing > 0) {
            return 0;
        }

        $lbsRows = DB::table('job_requests')->where('client_code', 'LBS01')->get();
        if ($lbsRows->isEmpty()) {
            return 0;
        }

        if (Schema::hasTable('clients')) {
            $exists = DB::table('clients')->where('client_code', 'LT01')->exists();
            if (! $exists) {
                DB::table('clients')->insert([
                    'client_code' => 'LT01',
                    'client_name' => 'Luntian Account',
                    'client_email' => 'admin@luntiands.com',
                ]);
            }
        }

        $nextId = (int) DB::table('job_requests')->max('id') + 1;
        $inserted = 0;

        foreach ($lbsRows as $row) {
            $newRequestId = str_replace('EA_LBS_', 'EA_LT_', (string) $row->job_request_id);
            if ($newRequestId === (string) $row->job_request_id) {
                continue;
            }

            $exists = DB::table('job_requests')
                ->where('client_code', 'LT01')
                ->where('job_request_id', $newRequestId)
                ->exists();

            if ($exists) {
                continue;
            }

            DB::table('job_requests')->insert([
                'id'               => $nextId++,
                'client_code'      => 'LT01',
                'job_request_id'   => $newRequestId,
                'job_request_type' => $row->job_request_type,
            ]);
            $inserted++;
        }

        return $inserted;
    }
}
