<?php

use App\Http\Controllers\AccountClientsController;
use App\Http\Controllers\AccountSettingsController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AmtJobController;
use App\Http\Controllers\FyrsJobController;
use App\Http\Controllers\BluinqJobController;
use App\Http\Controllers\BphJobController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CheckerController;
use App\Http\Controllers\ClientAccountController;
use App\Http\Controllers\ClientEmailAmtController;
use App\Http\Controllers\ClientEmailBphController;
use App\Http\Controllers\ComplianceController;
use App\Http\Controllers\CspJobController;
use App\Models\User;
use App\Http\Controllers\DashboardHolidayController;
use App\Http\Controllers\EmailConfigController;
use App\Http\Controllers\JobRequestController;
use App\Http\Controllers\LbsJobController;
use App\Http\Controllers\LcHomeBuilderJobController;
use App\Http\Controllers\LeadingEnergyJobController;
use App\Http\Controllers\NhJobController;
use App\Http\Controllers\NotificationSettingsController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PriorityController;
use App\Http\Controllers\SlackConfigController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\UserAccountController;
use App\Models\ClientEmailBph;
use App\Support\LoginReturnPath;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\ReportsController;

Route::get('/', function () {
    $lbsPublicHost = trim((string) config('app.lbs_public_form_domain', ''));
    if ($lbsPublicHost !== '' && strcasecmp($lbsPublicHost, request()->getHost()) === 0) {
        return redirect()->route('lbs.public.add');
    }

    $returnTo = request()->query('return_to');
    $decodedReturn = null;
    if (is_string($returnTo) && $returnTo !== '') {
        $decodedReturn = rawurldecode($returnTo);
    }

    if (session()->has('user_id')) {
        if (is_string($decodedReturn) && LoginReturnPath::isAllowed($decodedReturn)) {
            return redirect()->to(LoginReturnPath::absoluteUrlFor(request(), $decodedReturn));
        }

        return redirect()->route('dashboard');
    }

    if (is_string($decodedReturn) && $decodedReturn !== '' && LoginReturnPath::isAllowed($decodedReturn)) {
        session([
            'url.intended' => LoginReturnPath::absoluteUrlFor(request(), $decodedReturn),
        ]);
    }

    return view('app');
});

Route::get('/login', function () {
    return redirect('/');
})->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/branding/logo-light', function () {
    if (!Storage::disk('public')->exists('logo-light.png')) {
        abort(404);
    }

    return response()->file(Storage::disk('public')->path('logo-light.png'));
})->name('branding.logo_light');

$registerPublicLbsRoutes = function () {
    Route::get('/lbs/add-new', [LbsJobController::class, 'publicAddForm'])->name('lbs.public.add');
    Route::post('/lbs/add-new', [LbsJobController::class, 'store'])->name('lbs.public.store');
    Route::post('/lbs/add-new/job/{id}/send-slack', [LbsJobController::class, 'sendJobSlackNotification'])->name('lbs.public.job.sendSlack');
    Route::post('/lbs/add-new/job/{id}/send-submission-email', [LbsJobController::class, 'sendJobSubmissionEmail'])->name('lbs.public.job.sendSubmissionEmail');
};

$lbsPublicFormDomain = trim((string) config('app.lbs_public_form_domain', ''));
if ($lbsPublicFormDomain !== '') {
    Route::domain($lbsPublicFormDomain)->group($registerPublicLbsRoutes);
} else {
    $registerPublicLbsRoutes();
}

Route::middleware(['auth.session', 'check.permission'])->group(function () {
    Route::get('/dashboard/unauthorized', function () {
        return view('unauthorized', ['sidebar_active' => 'unauthorized']);
    })->name('unauthorized');

    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard/holidays/{year}', DashboardHolidayController::class)->name('dashboard.holidays');
    Route::get('/dashboard/lbs/add', [LbsJobController::class, 'addForm'])->name('lbs.add');
    Route::post('/dashboard/lbs', [LbsJobController::class, 'store'])->name('lbs.store');
    Route::post('/dashboard/lbs/job/{id}/send-slack', [LbsJobController::class, 'sendJobSlackNotification'])->name('lbs.job.sendSlack');
    Route::post('/dashboard/lbs/job/{id}/send-submission-email', [LbsJobController::class, 'sendJobSubmissionEmail'])->name('lbs.job.sendSubmissionEmail');
    Route::get('/dashboard/lbs/list', [LbsJobController::class, 'index'])->name('lbs.list');
    Route::get('/dashboard/lbs/list/tables', [LbsJobController::class, 'indexTablesFragment'])->name('lbs.list.tablesFragment');
    Route::get('/dashboard/lbs/job/{id}', [LbsJobController::class, 'show'])->name('lbs.job.view');
    Route::post('/dashboard/lbs/job/{id}/accept-form', [LbsJobController::class, 'acceptFormJob'])->name('lbs.job.acceptForm');
    Route::put('/dashboard/lbs/job/{id}', [LbsJobController::class, 'update'])->name('lbs.job.update');
    Route::post('/dashboard/lbs/job/{id}/files', [LbsJobController::class, 'uploadFiles'])->name('lbs.job.uploadFiles');
    Route::post('/dashboard/lbs/job/{id}/file/delete', [LbsJobController::class, 'deleteFile'])->name('lbs.job.deleteFile');
    Route::post('/dashboard/lbs/job/{id}/archive', [LbsJobController::class, 'archiveJob'])->name('lbs.job.archive');
    Route::get('/dashboard/lbs/job/{id}/restore', [LbsJobController::class, 'restoreJob'])->name('lbs.job.restore');
    Route::get('/dashboard/lbs/job/{id}/file/{file}', [LbsJobController::class, 'downloadFile'])->name('lbs.job.file');
    Route::post('/dashboard/lbs/job/{id}/checker-uploads', [LbsJobController::class, 'uploadCheckerFiles'])->name('lbs.job.checkerUploads');
    Route::post('/dashboard/lbs/job/{id}/run-comment', [LbsJobController::class, 'addRunComment'])->name('lbs.job.runComment');
    Route::post('/dashboard/lbs/job/{id}/comment', [LbsJobController::class, 'addJobComment'])->name('lbs.job.comment');
    Route::get('/dashboard/lbs/completed', [LbsJobController::class, 'completed'])->name('lbs.completed');
    Route::get('/dashboard/lbs/mailbox', [LbsJobController::class, 'mailbox'])->name('lbs.mailbox');
    Route::get('/dashboard/lbs/job/{id}/email-preview', [LbsJobController::class, 'emailPreview'])->name('lbs.job.emailPreview');
    Route::post('/dashboard/lbs/job/{id}/send-mailbox-email', [LbsJobController::class, 'sendMailboxEmail'])->name('lbs.job.sendMailboxEmail');
    Route::get('/dashboard/lbs/review', [LbsJobController::class, 'review'])->name('lbs.review');
    Route::get('/dashboard/lbs/trash', [LbsJobController::class, 'trash'])->name('lbs.trash');
    Route::get('/dashboard/bph/add', [BphJobController::class, 'addForm'])->name('bph.add');
    Route::get('/dashboard/bph/list', [BphJobController::class, 'list'])->name('bph.list');
    Route::get('/dashboard/bph/completed', [BphJobController::class, 'completed'])->name('bph.completed');
    Route::get('/dashboard/bph/review', [BphJobController::class, 'review'])->name('bph.review');
    Route::get('/dashboard/bph/mailbox', [BphJobController::class, 'mailbox'])->name('bph.mailbox');
    Route::get('/dashboard/bph/job/{id}/email-preview', [BphJobController::class, 'emailPreview'])->name('bph.job.emailPreview');
    Route::post('/dashboard/bph/job/{id}/send-mailbox-email', [BphJobController::class, 'sendMailboxEmail'])->name('bph.job.sendMailboxEmail');
    Route::get('/dashboard/bph/trash', [BphJobController::class, 'trash'])->name('bph.trash');
    Route::post('/dashboard/bph/store', [BphJobController::class, 'store'])->name('bph.store');
    Route::get('/dashboard/bph/job/{id}', [BphJobController::class, 'show'])->name('bph.view');
    Route::put('/dashboard/bph/job/{id}', [BphJobController::class, 'update'])->name('bph.update');
    Route::post('/dashboard/bph/job/{id}/files', [BphJobController::class, 'uploadFiles'])->name('bph.job.uploadFiles');
    Route::post('/dashboard/bph/job/{id}/file/delete', [BphJobController::class, 'deleteFile'])->name('bph.job.deleteFile');
    Route::get('/dashboard/bph/job/{id}/file/{file}', [BphJobController::class, 'downloadFile'])->name('bph.job.file');
    Route::get('/dashboard/bph/job/{id}/merge-file', [BphJobController::class, 'downloadMergeFile'])->name('bph.job.mergeFile');
    Route::get('/dashboard/bph/job/{id}/print/compliance-summary', [BphJobController::class, 'printComplianceSummary'])->name('bph.job.printCompliance');
    Route::post('/dashboard/bph/job/{id}/checker-uploads', [BphJobController::class, 'uploadCheckerFiles'])->name('bph.job.checkerUploads');
    Route::post('/dashboard/bph/job/{id}/run-comment', [BphJobController::class, 'addRunComment'])->name('bph.job.runComment');
    Route::post('/dashboard/bph/job/{id}/comment', [BphJobController::class, 'addJobComment'])->name('bph.job.comment');
    Route::post('/dashboard/bph/job/{id}/archive', [BphJobController::class, 'archiveJob'])->name('bph.job.archive');
    Route::post('/dashboard/bph/job/{id}/send-slack', [BphJobController::class, 'sendSlackNotification'])->name('bph.job.sendSlack');
    Route::post('/dashboard/bph/job/{id}/send-submission-email', [BphJobController::class, 'sendSubmissionEmail'])->name('bph.job.sendSubmissionEmail');

    Route::get('/dashboard/bph-client-email', [ClientEmailBphController::class, 'index'])->name('bph_client_email.index');
    Route::get('/dashboard/bph-client-email/create', [ClientEmailBphController::class, 'create'])->name('bph_client_email.create');
    Route::post('/dashboard/bph-client-email', [ClientEmailBphController::class, 'store'])->name('bph_client_email.store');
    Route::get('/dashboard/bph-client-email/{client_email_bph}/edit', [ClientEmailBphController::class, 'edit'])->name('bph_client_email.edit');
    Route::put('/dashboard/bph-client-email/{client_email_bph}', [ClientEmailBphController::class, 'update'])->name('bph_client_email.update');
    Route::delete('/dashboard/bph-client-email/{client_email_bph}', [ClientEmailBphController::class, 'destroy'])->name('bph_client_email.destroy');

    Route::get('/dashboard/amt-client-email', [ClientEmailAmtController::class, 'index'])->name('amt_client_email.index');
    Route::get('/dashboard/amt-client-email/create', [ClientEmailAmtController::class, 'create'])->name('amt_client_email.create');
    Route::post('/dashboard/amt-client-email', [ClientEmailAmtController::class, 'store'])->name('amt_client_email.store');
    Route::get('/dashboard/amt-client-email/{client_email_amt}/edit', [ClientEmailAmtController::class, 'edit'])->name('amt_client_email.edit');
    Route::put('/dashboard/amt-client-email/{client_email_amt}', [ClientEmailAmtController::class, 'update'])->name('amt_client_email.update');
    Route::delete('/dashboard/amt-client-email/{client_email_amt}', [ClientEmailAmtController::class, 'destroy'])->name('amt_client_email.destroy');

    Route::get('/dashboard/csp/add', function () {
        $bphClientEmails = ClientEmailBph::orderBy('email')->get(['id', 'email']);

        return view('csp.add', [
            'sidebar_active' => 'csp.add',
            'bphClientEmails' => $bphClientEmails,
            'assignmentUsers' => User::assignmentUsersForSelect(),
        ]);
    })->name('csp.add');

    Route::post('/dashboard/csp/store', [CspJobController::class, 'store'])->name('csp.store');
    Route::get('/dashboard/csp/job/{id}', [CspJobController::class, 'show'])->name('csp.view');
    Route::put('/dashboard/csp/job/{id}', [CspJobController::class, 'update'])->name('csp.update');
    Route::get('/dashboard/csp/job/{id}/email-preview', [CspJobController::class, 'emailPreview'])->name('csp.job.emailPreview');
    Route::post('/dashboard/csp/job/{id}/send-mailbox-email', [CspJobController::class, 'sendMailboxEmail'])->name('csp.job.sendMailboxEmail');
    Route::get('/dashboard/csp/job/{id}/print/compliance-summary', [CspJobController::class, 'printComplianceSummary'])->name('csp.job.printCompliance');

    Route::get('/dashboard/csp/list', function () {
        return view('csp.list', ['sidebar_active' => 'csp.list']);
    })->name('csp.list');
    Route::get('/dashboard/csp/completed', function () {
        return view('csp.completed', ['sidebar_active' => 'csp.completed']);
    })->name('csp.completed');
    Route::get('/dashboard/csp/review', function () {
        return view('csp.review', ['sidebar_active' => 'csp.review']);
    })->name('csp.review');
    Route::get('/dashboard/csp/mailbox', [CspJobController::class, 'mailbox'])->name('csp.mailbox');
    Route::get('/dashboard/csp/trash', function () {
        return view('csp.trash', ['sidebar_active' => 'csp.trash']);
    })->name('csp.trash');

    Route::get('/dashboard/bluinq/add', [BluinqJobController::class, 'addForm'])->name('bluinq.add');
    Route::post('/dashboard/bluinq/store', [BluinqJobController::class, 'store'])->name('bluinq.store');
    Route::post('/dashboard/bluinq/job/{id}/send-slack', [BluinqJobController::class, 'sendSlackNotification'])->name('bluinq.job.sendSlack');
    Route::post('/dashboard/bluinq/job/{id}/send-submission-email', [BluinqJobController::class, 'sendSubmissionEmail'])->name('bluinq.job.sendSubmissionEmail');
    Route::get('/dashboard/bluinq/job/{id}/email-preview', [BluinqJobController::class, 'emailPreview'])->name('bluinq.job.emailPreview');
    Route::post('/dashboard/bluinq/job/{id}/send-mailbox-email', [BluinqJobController::class, 'sendMailboxEmail'])->name('bluinq.job.sendMailboxEmail');
    Route::get('/dashboard/bluinq/job/{id}', [BluinqJobController::class, 'show'])->name('bluinq.view');
    Route::put('/dashboard/bluinq/job/{id}', [BluinqJobController::class, 'update'])->name('bluinq.update');
    Route::get('/dashboard/bluinq/mailbox', [BluinqJobController::class, 'mailbox'])->name('bluinq.mailbox');
    Route::get('/dashboard/bluinq/list', [BluinqJobController::class, 'list'])->name('bluinq.list');
    Route::get('/dashboard/bluinq/completed', [BluinqJobController::class, 'completed'])->name('bluinq.completed');
    Route::get('/dashboard/bluinq/review', [BluinqJobController::class, 'review'])->name('bluinq.review');
    Route::get('/dashboard/bluinq/trash', fn () => view('bluinq.trash', ['sidebar_active' => 'bluinq.trash']))->name('bluinq.trash');

    Route::get('/dashboard/amt/add', [AmtJobController::class, 'addForm'])->name('amt.add');
    Route::post('/dashboard/amt/store', [AmtJobController::class, 'store'])->name('amt.store');
    Route::post('/dashboard/amt/job/{id}/send-slack', [AmtJobController::class, 'sendSlackNotification'])->name('amt.job.sendSlack');
    Route::post('/dashboard/amt/job/{id}/send-submission-email', [AmtJobController::class, 'sendSubmissionEmail'])->name('amt.job.sendSubmissionEmail');
    Route::get('/dashboard/amt/job/{id}/email-preview', [AmtJobController::class, 'emailPreview'])->name('amt.job.emailPreview');
    Route::post('/dashboard/amt/job/{id}/send-mailbox-email', [AmtJobController::class, 'sendMailboxEmail'])->name('amt.job.sendMailboxEmail');
    Route::get('/dashboard/amt/job/{id}', [AmtJobController::class, 'show'])->name('amt.view');
    Route::put('/dashboard/amt/job/{id}', [AmtJobController::class, 'update'])->name('amt.update');
    Route::post('/dashboard/amt/job/{id}/files', [AmtJobController::class, 'uploadFiles'])->name('amt.job.uploadFiles');
    Route::post('/dashboard/amt/job/{id}/file/delete', [AmtJobController::class, 'deleteFile'])->name('amt.job.deleteFile');
    Route::get('/dashboard/amt/job/{id}/file/{file}', [AmtJobController::class, 'downloadFile'])->name('amt.job.file');
    Route::get('/dashboard/amt/job/{id}/merge-file', [AmtJobController::class, 'downloadMergeFile'])->name('amt.job.mergeFile');
    Route::get('/dashboard/amt/job/{id}/print/compliance-summary', [AmtJobController::class, 'printComplianceSummary'])->name('amt.job.printCompliance');
    Route::post('/dashboard/amt/job/{id}/checker-uploads', [AmtJobController::class, 'uploadCheckerFiles'])->name('amt.job.checkerUploads');
    Route::post('/dashboard/amt/job/{id}/run-comment', [AmtJobController::class, 'addRunComment'])->name('amt.job.runComment');
    Route::post('/dashboard/amt/job/{id}/comment', [AmtJobController::class, 'addJobComment'])->name('amt.job.comment');
    Route::post('/dashboard/amt/job/{id}/archive', [AmtJobController::class, 'archiveJob'])->name('amt.job.archive');
    Route::get('/dashboard/amt/mailbox', [AmtJobController::class, 'mailbox'])->name('amt.mailbox');
    Route::get('/dashboard/amt/rfq', [AmtJobController::class, 'rfq'])->name('amt.rfq');
    Route::get('/dashboard/amt/purchase', [AmtJobController::class, 'purchase'])->name('amt.purchase');
    Route::get('/dashboard/amt/list', [AmtJobController::class, 'list'])->name('amt.list');
    Route::get('/dashboard/amt/completed', [AmtJobController::class, 'completed'])->name('amt.completed');
    Route::get('/dashboard/amt/review', [AmtJobController::class, 'review'])->name('amt.review');
    Route::get('/dashboard/amt/trash', fn () => view('amt.trash', ['sidebar_active' => 'amt.trash']))->name('amt.trash');

    Route::get('/dashboard/fyrs/add', [FyrsJobController::class, 'addForm'])->name('fyrs.add');
    Route::post('/dashboard/fyrs/store', [FyrsJobController::class, 'store'])->name('fyrs.store');
    Route::post('/dashboard/fyrs/job/{id}/send-slack', [FyrsJobController::class, 'sendSlackNotification'])->name('fyrs.job.sendSlack');
    Route::post('/dashboard/fyrs/job/{id}/send-submission-email', [FyrsJobController::class, 'sendSubmissionEmail'])->name('fyrs.job.sendSubmissionEmail');
    Route::get('/dashboard/fyrs/job/{id}/email-preview', [FyrsJobController::class, 'emailPreview'])->name('fyrs.job.emailPreview');
    Route::post('/dashboard/fyrs/job/{id}/send-mailbox-email', [FyrsJobController::class, 'sendMailboxEmail'])->name('fyrs.job.sendMailboxEmail');
    Route::get('/dashboard/fyrs/job/{id}', [FyrsJobController::class, 'show'])->name('fyrs.view');
    Route::put('/dashboard/fyrs/job/{id}', [FyrsJobController::class, 'update'])->name('fyrs.update');
    Route::post('/dashboard/fyrs/job/{id}/files', [FyrsJobController::class, 'uploadFiles'])->name('fyrs.job.uploadFiles');
    Route::post('/dashboard/fyrs/job/{id}/file/delete', [FyrsJobController::class, 'deleteFile'])->name('fyrs.job.deleteFile');
    Route::get('/dashboard/fyrs/job/{id}/file/{file}', [FyrsJobController::class, 'downloadFile'])->name('fyrs.job.file');
    Route::get('/dashboard/fyrs/job/{id}/merge-file', [FyrsJobController::class, 'downloadMergeFile'])->name('fyrs.job.mergeFile');
    Route::get('/dashboard/fyrs/job/{id}/print/compliance-summary', [FyrsJobController::class, 'printComplianceSummary'])->name('fyrs.job.printCompliance');
    Route::post('/dashboard/fyrs/job/{id}/checker-uploads', [FyrsJobController::class, 'uploadCheckerFiles'])->name('fyrs.job.checkerUploads');
    Route::post('/dashboard/fyrs/job/{id}/run-comment', [FyrsJobController::class, 'addRunComment'])->name('fyrs.job.runComment');
    Route::post('/dashboard/fyrs/job/{id}/comment', [FyrsJobController::class, 'addJobComment'])->name('fyrs.job.comment');
    Route::post('/dashboard/fyrs/job/{id}/archive', [FyrsJobController::class, 'archiveJob'])->name('fyrs.job.archive');
    Route::get('/dashboard/fyrs/mailbox', [FyrsJobController::class, 'mailbox'])->name('fyrs.mailbox');
    Route::get('/dashboard/fyrs/list', [FyrsJobController::class, 'list'])->name('fyrs.list');
    Route::get('/dashboard/fyrs/completed', [FyrsJobController::class, 'completed'])->name('fyrs.completed');
    Route::get('/dashboard/fyrs/review', [FyrsJobController::class, 'review'])->name('fyrs.review');
    Route::get('/dashboard/fyrs/trash', fn () => view('fyrs.trash', ['sidebar_active' => 'fyrs.trash']))->name('fyrs.trash');

    Route::get('/dashboard/nh/add', [NhJobController::class, 'addForm'])->name('nh.add');
    Route::post('/dashboard/nh/store', [NhJobController::class, 'store'])->name('nh.store');
    Route::put('/dashboard/nh/job/{id}', [NhJobController::class, 'update'])->name('nh.update');
    Route::post('/dashboard/nh/job/{id}/send-slack', [NhJobController::class, 'sendSlackNotification'])->name('nh.job.sendSlack');
    Route::post('/dashboard/nh/job/{id}/send-submission-email', [NhJobController::class, 'sendSubmissionEmail'])->name('nh.job.sendSubmissionEmail');
    Route::get('/dashboard/nh/list', fn () => view('nh.list', ['sidebar_active' => 'nh.list']))->name('nh.list');
    Route::get('/dashboard/nh/completed', fn () => view('nh.completed', ['sidebar_active' => 'nh.completed']))->name('nh.completed');
    Route::get('/dashboard/nh/review', fn () => view('nh.review', ['sidebar_active' => 'nh.review']))->name('nh.review');
    Route::get('/dashboard/nh/mailbox', [NhJobController::class, 'mailbox'])->name('nh.mailbox');
    Route::get('/dashboard/nh/job/{id}/email-preview', [NhJobController::class, 'emailPreview'])->name('nh.job.emailPreview');
    Route::post('/dashboard/nh/job/{id}/send-mailbox-email', [NhJobController::class, 'sendMailboxEmail'])->name('nh.job.sendMailboxEmail');
    Route::get('/dashboard/nh/job/{id}/print/compliance-summary', [NhJobController::class, 'printComplianceSummary'])->name('nh.job.printCompliance');
    Route::get('/dashboard/nh/trash', fn () => view('nh.trash', ['sidebar_active' => 'nh.trash']))->name('nh.trash');

    Route::get('/dashboard/lc-home-builder/add', [LcHomeBuilderJobController::class, 'addForm'])->name('lc_home_builder.add');
    Route::post('/dashboard/lc-home-builder/store', [LcHomeBuilderJobController::class, 'store'])->name('lc_home_builder.store');
    Route::post('/dashboard/lc-home-builder/job/{id}/send-slack', [LcHomeBuilderJobController::class, 'sendSlackNotification'])->name('lc_home_builder.job.sendSlack');
    Route::post('/dashboard/lc-home-builder/job/{id}/send-submission-email', [LcHomeBuilderJobController::class, 'sendSubmissionEmail'])->name('lc_home_builder.job.sendSubmissionEmail');
    Route::get('/dashboard/lc-home-builder/mailbox', [LcHomeBuilderJobController::class, 'mailbox'])->name('lc_home_builder.mailbox');
    Route::get('/dashboard/lc-home-builder/job/{id}/email-preview', [LcHomeBuilderJobController::class, 'emailPreview'])->name('lc_home_builder.job.emailPreview');
    Route::post('/dashboard/lc-home-builder/job/{id}/send-mailbox-email', [LcHomeBuilderJobController::class, 'sendMailboxEmail'])->name('lc_home_builder.job.sendMailboxEmail');
    Route::get('/dashboard/lc-home-builder/list', [LcHomeBuilderJobController::class, 'list'])->name('lc_home_builder.list');
    Route::put('/dashboard/lc-home-builder/job/{id}', [LcHomeBuilderJobController::class, 'update'])->name('lc_home_builder.update');
    Route::get('/dashboard/lc-home-builder/completed', fn () => view('lc_home_builder.completed', ['sidebar_active' => 'lc_home_builder.completed']))->name('lc_home_builder.completed');
    Route::get('/dashboard/lc-home-builder/review', fn () => view('lc_home_builder.review', ['sidebar_active' => 'lc_home_builder.review']))->name('lc_home_builder.review');
    Route::get('/dashboard/lc-home-builder/trash', fn () => view('lc_home_builder.trash', ['sidebar_active' => 'lc_home_builder.trash']))->name('lc_home_builder.trash');

    Route::get('/dashboard/efficient-living/add', [LbsJobController::class, 'efficientLivingAddForm'])->name('efficient_living.add');
    Route::post('/dashboard/efficient-living', [LbsJobController::class, 'store'])->name('efficient_living.store');
    Route::post('/dashboard/efficient-living/job/{id}/send-slack', [LbsJobController::class, 'sendJobSlackNotification'])->name('efficient_living.job.sendSlack');
    Route::post('/dashboard/efficient-living/job/{id}/send-submission-email', [LbsJobController::class, 'sendJobSubmissionEmail'])->name('efficient_living.job.sendSubmissionEmail');
    Route::get('/dashboard/efficient-living/job/{id}', [LbsJobController::class, 'efficientLivingShow'])->name('efficient_living.job.view');
    Route::put('/dashboard/efficient-living/job/{id}', [LbsJobController::class, 'update'])->name('efficient_living.job.update');
    Route::get('/dashboard/efficient-living/job/{id}/restore', [LbsJobController::class, 'restoreJob'])->name('efficient_living.job.restore');
    Route::get('/dashboard/efficient-living/job/{id}/email-preview', [LbsJobController::class, 'emailPreview'])->name('efficient_living.job.emailPreview');
    Route::post('/dashboard/efficient-living/job/{id}/send-mailbox-email', [LbsJobController::class, 'sendMailboxEmail'])->name('efficient_living.job.sendMailboxEmail');
    Route::get('/dashboard/efficient-living/list', [LbsJobController::class, 'efficientLivingList'])->name('efficient_living.list');
    Route::get('/dashboard/efficient-living/completed', [LbsJobController::class, 'efficientLivingCompleted'])->name('efficient_living.completed');
    Route::get('/dashboard/efficient-living/review', [LbsJobController::class, 'efficientLivingReview'])->name('efficient_living.review');
    Route::get('/dashboard/efficient-living/mailbox', [LbsJobController::class, 'efficientLivingMailbox'])->name('efficient_living.mailbox');
    Route::get('/dashboard/efficient-living/trash', [LbsJobController::class, 'efficientLivingTrash'])->name('efficient_living.trash');

    Route::get('/dashboard/leading-energy/add', [LeadingEnergyJobController::class, 'addForm'])->name('leading_energy.add');
    Route::post('/dashboard/leading-energy/store', [LeadingEnergyJobController::class, 'store'])->name('leading_energy.store');
    Route::post('/dashboard/leading-energy/job/{id}/send-slack', [LeadingEnergyJobController::class, 'sendSlackNotification'])->name('leading_energy.job.sendSlack');
    Route::post('/dashboard/leading-energy/job/{id}/send-submission-email', [LeadingEnergyJobController::class, 'sendSubmissionEmail'])->name('leading_energy.job.sendSubmissionEmail');
    Route::get('/dashboard/leading-energy/list', [LeadingEnergyJobController::class, 'list'])->name('leading_energy.list');
    Route::get('/dashboard/leading-energy/job/{id}', [LeadingEnergyJobController::class, 'show'])->name('leading_energy.view');
    Route::put('/dashboard/leading-energy/job/{id}', [LeadingEnergyJobController::class, 'update'])->name('leading_energy.update');
    Route::get('/dashboard/leading-energy/mailbox', [LeadingEnergyJobController::class, 'mailbox'])->name('leading_energy.mailbox');
    Route::get('/dashboard/leading-energy/job/{id}/email-preview', [LeadingEnergyJobController::class, 'emailPreview'])->name('leading_energy.job.emailPreview');
    Route::post('/dashboard/leading-energy/job/{id}/send-mailbox-email', [LeadingEnergyJobController::class, 'sendMailboxEmail'])->name('leading_energy.job.sendMailboxEmail');
    Route::get('/dashboard/leading-energy/completed', [LeadingEnergyJobController::class, 'completed'])->name('leading_energy.completed');
    Route::get('/dashboard/leading-energy/review', [LeadingEnergyJobController::class, 'review'])->name('leading_energy.review');
    Route::get('/dashboard/leading-energy/trash', [LeadingEnergyJobController::class, 'trash'])->name('leading_energy.trash');

    Route::get('/dashboard/reports', [ReportsController::class, 'index'])->name('reports');

    Route::get('/dashboard/settings/email-config', [EmailConfigController::class, 'index'])->name('settings.email_config');
    Route::post('/dashboard/settings/email-config', [EmailConfigController::class, 'store'])->name('settings.email_config.store');
    Route::post('/dashboard/settings/email-config/toggle', [EmailConfigController::class, 'toggleActive'])->name('settings.email_config.toggle');
    Route::get('/dashboard/settings/slack-config', [SlackConfigController::class, 'index'])->name('settings.slack_config');
    Route::post('/dashboard/settings/slack-config', [SlackConfigController::class, 'store'])->name('settings.slack_config.store');
    Route::post('/dashboard/settings/slack-config/toggle', [SlackConfigController::class, 'togglePurpose'])->name('settings.slack_config.toggle');
    Route::get('/dashboard/settings/notifications', [NotificationSettingsController::class, 'index'])->name('settings.notifications');
    Route::get('/dashboard/settings/permission', [PermissionController::class, 'index'])->name('settings.permission');
    Route::post('/dashboard/settings/permission', [PermissionController::class, 'store'])->name('settings.permission.store');

    Route::get('/dashboard/compliance', [ComplianceController::class, 'index'])->name('compliance.index');
    Route::get('/dashboard/compliance/create', [ComplianceController::class, 'create'])->name('compliance.create');
    Route::post('/dashboard/compliance', [ComplianceController::class, 'store'])->name('compliance.store');
    Route::get('/dashboard/compliance/{compliance}/edit', [ComplianceController::class, 'edit'])->name('compliance.edit');
    Route::put('/dashboard/compliance/{compliance}', [ComplianceController::class, 'update'])->name('compliance.update');
    Route::delete('/dashboard/compliance/{compliance}', [ComplianceController::class, 'destroy'])->name('compliance.destroy');

    Route::get('/dashboard/priority', [PriorityController::class, 'index'])->name('priority.index');
    Route::get('/dashboard/priority/create', [PriorityController::class, 'create'])->name('priority.create');
    Route::post('/dashboard/priority', [PriorityController::class, 'store'])->name('priority.store');
    Route::get('/dashboard/priority/{priority}/edit', [PriorityController::class, 'edit'])->name('priority.edit');
    Route::put('/dashboard/priority/{priority}', [PriorityController::class, 'update'])->name('priority.update');
    Route::delete('/dashboard/priority/{priority}', [PriorityController::class, 'destroy'])->name('priority.destroy');

    Route::get('/dashboard/branch', [BranchController::class, 'index'])->name('branch.index');
    Route::get('/dashboard/branch/create', [BranchController::class, 'create'])->name('branch.create');
    Route::post('/dashboard/branch', [BranchController::class, 'store'])->name('branch.store');
    Route::get('/dashboard/branch/{branch}/edit', [BranchController::class, 'edit'])->name('branch.edit');
    Route::put('/dashboard/branch/{branch}', [BranchController::class, 'update'])->name('branch.update');
    Route::delete('/dashboard/branch/{branch}', [BranchController::class, 'destroy'])->name('branch.destroy');
    Route::get('/dashboard/branch/archive', [BranchController::class, 'archive'])->name('branch.archive');
    Route::post('/dashboard/branch/{branch}/restore', [BranchController::class, 'restore'])->name('branch.restore');

    Route::get('/dashboard/status', [StatusController::class, 'index'])->name('status.index');
    Route::get('/dashboard/status/create', [StatusController::class, 'create'])->name('status.create');
    Route::post('/dashboard/status', [StatusController::class, 'store'])->name('status.store');
    Route::get('/dashboard/status/{status}/edit', [StatusController::class, 'edit'])->name('status.edit');
    Route::put('/dashboard/status/{status}', [StatusController::class, 'update'])->name('status.update');
    Route::delete('/dashboard/status/{status}', [StatusController::class, 'destroy'])->name('status.destroy');

    Route::get('/dashboard/job-request', [JobRequestController::class, 'index'])->name('job_request.index');
    Route::get('/dashboard/job-request/create', [JobRequestController::class, 'create'])->name('job_request.create');
    Route::post('/dashboard/job-request', [JobRequestController::class, 'store'])->name('job_request.store');
    Route::get('/dashboard/job-request/{job_request}/edit', [JobRequestController::class, 'edit'])->name('job_request.edit');
    Route::put('/dashboard/job-request/{job_request}', [JobRequestController::class, 'update'])->name('job_request.update');
    Route::delete('/dashboard/job-request/{job_request}', [JobRequestController::class, 'destroy'])->name('job_request.destroy');

    Route::get('/dashboard/client', [ClientAccountController::class, 'index'])->name('client.index');
    Route::get('/dashboard/client/create', [ClientAccountController::class, 'create'])->name('client.create');
    Route::post('/dashboard/client', [ClientAccountController::class, 'store'])->name('client.store');
    Route::get('/dashboard/client/{client_account}/edit', [ClientAccountController::class, 'edit'])->name('client.edit');
    Route::put('/dashboard/client/{client_account}', [ClientAccountController::class, 'update'])->name('client.update');
    Route::delete('/dashboard/client/{client_account}', [ClientAccountController::class, 'destroy'])->name('client.destroy');

    Route::get('/dashboard/accounts/users', [UserAccountController::class, 'index'])->name('users.index');
    Route::get('/dashboard/accounts/users/create', [UserAccountController::class, 'create'])->name('users.create');
    Route::post('/dashboard/accounts/users', [UserAccountController::class, 'store'])->name('users.store');
    Route::get('/dashboard/accounts/users/{user}/edit', [UserAccountController::class, 'edit'])->name('users.edit');
    Route::put('/dashboard/accounts/users/{user}', [UserAccountController::class, 'update'])->name('users.update');
    Route::delete('/dashboard/accounts/users/{user}', [UserAccountController::class, 'destroy'])->name('users.destroy');

    Route::get('/dashboard/accounts/staff', [StaffController::class, 'index'])->name('staff.index');
    Route::get('/dashboard/accounts/staff/create', [StaffController::class, 'create'])->name('staff.create');
    Route::post('/dashboard/accounts/staff', [StaffController::class, 'store'])->name('staff.store');
    Route::get('/dashboard/accounts/staff/{staff}/edit', [StaffController::class, 'edit'])->name('staff.edit');
    Route::put('/dashboard/accounts/staff/{staff}', [StaffController::class, 'update'])->name('staff.update');
    Route::delete('/dashboard/accounts/staff/{staff}', [StaffController::class, 'destroy'])->name('staff.destroy');

    Route::get('/dashboard/accounts/checker', [CheckerController::class, 'index'])->name('checker.index');
    Route::get('/dashboard/accounts/checker/create', [CheckerController::class, 'create'])->name('checker.create');
    Route::post('/dashboard/accounts/checker', [CheckerController::class, 'store'])->name('checker.store');
    Route::get('/dashboard/accounts/checker/{checker}/edit', [CheckerController::class, 'edit'])->name('checker.edit');
    Route::put('/dashboard/accounts/checker/{checker}', [CheckerController::class, 'update'])->name('checker.update');
    Route::delete('/dashboard/accounts/checker/{checker}', [CheckerController::class, 'destroy'])->name('checker.destroy');

    Route::get('/dashboard/accounts/users/archive', [UserAccountController::class, 'archive'])->name('users.archive');
    Route::post('/dashboard/accounts/users/{user}/restore', [UserAccountController::class, 'restore'])->name('users.restore');

    Route::get('/dashboard/accounts/clients', [AccountClientsController::class, 'index'])->name('accounts.clients.index');
    Route::get('/dashboard/accounts/clients/create', [AccountClientsController::class, 'create'])->name('accounts.clients.create');
    Route::post('/dashboard/accounts/clients', [AccountClientsController::class, 'store'])->name('accounts.clients.store');
    Route::get('/dashboard/accounts/clients/{client}/edit', [AccountClientsController::class, 'edit'])->name('accounts.clients.edit');
    Route::put('/dashboard/accounts/clients/{client}', [AccountClientsController::class, 'update'])->name('accounts.clients.update');
    Route::delete('/dashboard/accounts/clients/{client}', [AccountClientsController::class, 'destroy'])->name('accounts.clients.destroy');

    // My account settings (per logged-in user)
    Route::get('/dashboard/account/settings', [AccountSettingsController::class, 'edit'])->name('account.settings.edit');
    Route::post('/dashboard/account/settings', [AccountSettingsController::class, 'update'])->name('account.settings.update');
    Route::get('/dashboard/account/profile-image', [AccountSettingsController::class, 'profileImage'])->name('account.settings.image');

    Route::get('/dashboard/announcement', [AnnouncementController::class, 'index'])->name('announcement.index');
    Route::get('/dashboard/announcement/create', [AnnouncementController::class, 'create'])->name('announcement.create');
    Route::post('/dashboard/announcement', [AnnouncementController::class, 'store'])->name('announcement.store');
    Route::get('/dashboard/announcement/{announcement}/edit', [AnnouncementController::class, 'edit'])->name('announcement.edit');
    Route::put('/dashboard/announcement/{announcement}', [AnnouncementController::class, 'update'])->name('announcement.update');
    Route::delete('/dashboard/announcement/{announcement}', [AnnouncementController::class, 'destroy'])->name('announcement.destroy');
});
