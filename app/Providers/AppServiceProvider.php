<?php

namespace App\Providers;

use App\Models\EmailConfig;
use App\Services\JobCountsScope;
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
                $lbsBase = DB::table('jobs')->where('reference', 'like', 'JOBS%');
                JobCountsScope::applyJobsTableAssignment($lbsBase);
                $counts = $lbsBase
                    ->selectRaw("
                        SUM(CASE WHEN job_status = 'Allocated' THEN 1 ELSE 0 END) AS allocated_count,
                        SUM(CASE WHEN job_status = 'For Review' THEN 1 ELSE 0 END) AS review_count,
                        SUM(CASE WHEN job_status = 'For Email Confirmation' THEN 1 ELSE 0 END) AS mailbox_count
                    ")
                    ->first();

                $view->with('lbs_list_count', JobCountsScope::sidebarCountForBranchVertical('LBS', (int) ($counts->allocated_count ?? 0)));
                $view->with('lbs_review_count', JobCountsScope::sidebarCountForBranchVertical('LBS', (int) ($counts->review_count ?? 0)));
                $view->with('lbs_mailbox_count', JobCountsScope::sidebarCountForBranchVertical('LBS', (int) ($counts->mailbox_count ?? 0)));

                // Efficient Living: same logic as LBS badges, but only `jobs` rows for EL (EA_EL_*), matching list/review/mailbox queries
                $elBase = DB::table('jobs')
                    ->whereRaw("job_request_id LIKE 'EA\_EL\_%'")
                    ->where('reference', 'like', 'JOBS%');
                JobCountsScope::applyJobsTableAssignment($elBase);
                $elCounts = $elBase
                    ->selectRaw("
                        SUM(CASE WHEN job_status = 'Allocated' THEN 1 ELSE 0 END) AS allocated_count,
                        SUM(CASE WHEN job_status = 'For Review' THEN 1 ELSE 0 END) AS review_count,
                        SUM(CASE WHEN job_status = 'For Email Confirmation' THEN 1 ELSE 0 END) AS mailbox_count
                    ")
                    ->first();
                $elList = JobCountsScope::sidebarCountForBranchVertical('EFFICIENT LIVING', (int) ($elCounts->allocated_count ?? 0));
                $elReview = JobCountsScope::sidebarCountForBranchVertical('EFFICIENT LIVING', (int) ($elCounts->review_count ?? 0));
                $elMailbox = JobCountsScope::sidebarCountForBranchVertical('EFFICIENT LIVING', (int) ($elCounts->mailbox_count ?? 0));

                $view->with('efficient_living_list_count', $elList);
                $view->with('efficient_living_review_count', $elReview);
                $view->with('efficient_living_mailbox_count', $elMailbox);

                // BPH sidebar badges from `job_bph`
                $bphBase = DB::table('job_bph')
                    ->whereRaw('LOWER(TRIM(COALESCE(client_code, \'\'))) != ?', ['bluinq01']);
                JobCountsScope::applyJobBphAssignment($bphBase);
                JobCountsScope::applyJobBphBranchVerticalScope($bphBase);
                $bphCounts = $bphBase
                    ->selectRaw("
                        SUM(CASE WHEN status = 'Allocated' THEN 1 ELSE 0 END) AS allocated_count,
                        SUM(CASE WHEN status = 'For Review' THEN 1 ELSE 0 END) AS review_count,
                        SUM(CASE WHEN status = 'For Email Confirmation' THEN 1 ELSE 0 END) AS mailbox_count
                    ")
                    ->first();

                $view->with('bph_list_count', JobCountsScope::sidebarCountForBranchVertical('BPH', (int) ($bphCounts->allocated_count ?? 0)));
                $view->with('bph_review_count', JobCountsScope::sidebarCountForBranchVertical('BPH', (int) ($bphCounts->review_count ?? 0)));
                $view->with('bph_mailbox_count', JobCountsScope::sidebarCountForBranchVertical('BPH', (int) ($bphCounts->mailbox_count ?? 0)));

                $bluinqBase = DB::table('job_bph')
                    ->where('client_code', 'BLUINQ01');
                JobCountsScope::applyJobBphAssignment($bluinqBase);
                JobCountsScope::applyJobBphBranchVerticalScope($bluinqBase);
                $bluinqCounts = $bluinqBase
                    ->selectRaw("
                        SUM(CASE WHEN status = 'Allocated' THEN 1 ELSE 0 END) AS allocated_count,
                        SUM(CASE WHEN status = 'For Review' THEN 1 ELSE 0 END) AS review_count,
                        SUM(CASE WHEN status = 'For Email Confirmation' THEN 1 ELSE 0 END) AS mailbox_count
                    ")
                    ->first();

                $view->with('bluinq_list_count', JobCountsScope::sidebarCountForBranchVertical('BLUINQ', (int) ($bluinqCounts->allocated_count ?? 0)));
                $view->with('bluinq_review_count', JobCountsScope::sidebarCountForBranchVertical('BLUINQ', (int) ($bluinqCounts->review_count ?? 0)));
                $view->with('bluinq_mailbox_count', JobCountsScope::sidebarCountForBranchVertical('BLUINQ', (int) ($bluinqCounts->mailbox_count ?? 0)));

                // CSP sidebar badges from job_csp
                if (\Illuminate\Support\Facades\Schema::hasTable('job_csp')) {
                    $cspBase = DB::table('job_csp');
                    JobCountsScope::applyJobBphAssignment($cspBase);
                    $cspCounts = $cspBase
                        ->selectRaw("
                            SUM(CASE WHEN status = 'Allocated' THEN 1 ELSE 0 END) AS allocated_count,
                            SUM(CASE WHEN status = 'For Review' THEN 1 ELSE 0 END) AS review_count,
                            SUM(CASE WHEN status = 'For Email Confirmation' THEN 1 ELSE 0 END) AS mailbox_count
                        ")
                        ->first();
                    $view->with('csp_list_count', JobCountsScope::sidebarCountForBranchVertical('CSP', (int) ($cspCounts->allocated_count ?? 0)));
                    $view->with('csp_review_count', JobCountsScope::sidebarCountForBranchVertical('CSP', (int) ($cspCounts->review_count ?? 0)));
                    $view->with('csp_mailbox_count', JobCountsScope::sidebarCountForBranchVertical('CSP', (int) ($cspCounts->mailbox_count ?? 0)));
                } else {
                    $view->with('csp_list_count', 0);
                    $view->with('csp_review_count', 0);
                    $view->with('csp_mailbox_count', 0);
                }

                // NH sidebar badges from job_nh
                if (\Illuminate\Support\Facades\Schema::hasTable('job_nh')) {
                    $nhBase = DB::table('job_nh');
                    JobCountsScope::applyJobBphAssignment($nhBase);
                    $nhCounts = $nhBase
                        ->selectRaw("
                            SUM(CASE WHEN status = 'Allocated' THEN 1 ELSE 0 END) AS allocated_count,
                            SUM(CASE WHEN status = 'For Review' THEN 1 ELSE 0 END) AS review_count,
                            SUM(CASE WHEN status = 'For Email Confirmation' THEN 1 ELSE 0 END) AS mailbox_count
                        ")
                        ->first();
                    $view->with('nh_list_count', JobCountsScope::sidebarCountForBranchVertical('NH', (int) ($nhCounts->allocated_count ?? 0)));
                    $view->with('nh_review_count', JobCountsScope::sidebarCountForBranchVertical('NH', (int) ($nhCounts->review_count ?? 0)));
                    $view->with('nh_mailbox_count', JobCountsScope::sidebarCountForBranchVertical('NH', (int) ($nhCounts->mailbox_count ?? 0)));
                } else {
                    $view->with('nh_list_count', 0);
                    $view->with('nh_review_count', 0);
                    $view->with('nh_mailbox_count', 0);
                }

                // LC HOME BUILDER sidebar badges from job_lc_home_builder
                if (\Illuminate\Support\Facades\Schema::hasTable('job_lc_home_builder')) {
                    $lcBase = DB::table('job_lc_home_builder');
                    JobCountsScope::applyJobBphAssignment($lcBase);
                    $lcCounts = $lcBase
                        ->selectRaw("
                            SUM(CASE WHEN status = 'Allocated' THEN 1 ELSE 0 END) AS allocated_count,
                            SUM(CASE WHEN status = 'For Review' THEN 1 ELSE 0 END) AS review_count,
                            SUM(CASE WHEN status = 'For Email Confirmation' THEN 1 ELSE 0 END) AS mailbox_count
                        ")
                        ->first();
                    $view->with('lc_home_builder_list_count', JobCountsScope::sidebarCountForBranchVertical('LC HOME BUILDER', (int) ($lcCounts->allocated_count ?? 0)));
                    $view->with('lc_home_builder_review_count', JobCountsScope::sidebarCountForBranchVertical('LC HOME BUILDER', (int) ($lcCounts->review_count ?? 0)));
                    $view->with('lc_home_builder_mailbox_count', JobCountsScope::sidebarCountForBranchVertical('LC HOME BUILDER', (int) ($lcCounts->mailbox_count ?? 0)));
                } else {
                    $view->with('lc_home_builder_list_count', 0);
                    $view->with('lc_home_builder_review_count', 0);
                    $view->with('lc_home_builder_mailbox_count', 0);
                }

                // LEADING ENERGY sidebar badges from job_leading_energy
                if (\Illuminate\Support\Facades\Schema::hasTable('job_leading_energy')) {
                    $leBase = DB::table('job_leading_energy');
                    JobCountsScope::applyJobBphAssignment($leBase);
                    $leadingEnergyCounts = $leBase
                        ->selectRaw("
                            SUM(CASE WHEN status = 'Allocated' THEN 1 ELSE 0 END) AS allocated_count,
                            SUM(CASE WHEN status = 'For Review' THEN 1 ELSE 0 END) AS review_count,
                            SUM(CASE WHEN status = 'For Email Confirmation' THEN 1 ELSE 0 END) AS mailbox_count
                        ")
                        ->first();
                    $view->with('leading_energy_list_count', JobCountsScope::sidebarCountForBranchVertical('LEADING ENERGY', (int) ($leadingEnergyCounts->allocated_count ?? 0)));
                    $view->with('leading_energy_review_count', JobCountsScope::sidebarCountForBranchVertical('LEADING ENERGY', (int) ($leadingEnergyCounts->review_count ?? 0)));
                    $view->with('leading_energy_mailbox_count', JobCountsScope::sidebarCountForBranchVertical('LEADING ENERGY', (int) ($leadingEnergyCounts->mailbox_count ?? 0)));
                } else {
                    $view->with('leading_energy_list_count', 0);
                    $view->with('leading_energy_review_count', 0);
                    $view->with('leading_energy_mailbox_count', 0);
                }

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
