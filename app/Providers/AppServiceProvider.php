<?php

namespace App\Providers;

use App\Models\EmailConfig;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     * Apply saved email config so Mail:: sends using SMTP and default From.
     */
    public function boot(): void
    {
        Paginator::defaultView('vendor.pagination.dashboard');
        Paginator::defaultSimpleView('vendor.pagination.simple-dashboard');

        // Share LBS sidebar counts (Allocated / For Review) across all pages
        try {
            View::composer('layouts.partials.sidebar', function ($view) {
                $counts = DB::table('jobs')
                    ->where('reference', 'like', 'JOBS%')
                    ->selectRaw("
                        SUM(CASE WHEN job_status = 'Allocated' THEN 1 ELSE 0 END) AS allocated_count,
                        SUM(CASE WHEN job_status = 'For Review' THEN 1 ELSE 0 END) AS review_count
                    ")
                    ->first();

                $view->with('lbs_list_count', (int) ($counts->allocated_count ?? 0));
                $view->with('lbs_review_count', (int) ($counts->review_count ?? 0));
                $view->with('lbs_mailbox_count', 0);
            });
        } catch (\Throwable) {
            // Fail silently; sidebar will use default fallback values
        }

        // Apply dynamic email configuration if available
        try {
            $config = EmailConfig::where('is_active', true)->first();
        } catch (\Throwable) {
            return;
        }

        if (!$config) {
            return;
        }

        Config::set('mail.default', 'smtp');
        Config::set('mail.mailers.smtp.host', $config->smtp_host);
        Config::set('mail.mailers.smtp.port', (int) $config->smtp_port);
        Config::set('mail.mailers.smtp.username', $config->smtp_username);
        Config::set('mail.mailers.smtp.password', $config->getDecryptedPassword());
        Config::set('mail.mailers.smtp.encryption', $config->encryption ?: null);

        if ($config->from_email) {
            Config::set('mail.from.address', $config->from_email);
            Config::set('mail.from.name', $config->from_name ?? config('mail.from.name'));
        }
    }
}
