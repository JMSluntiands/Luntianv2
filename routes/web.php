<?php

use App\Http\Controllers\AccountClientsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CheckerController;
use App\Http\Controllers\ClientAccountController;
use App\Http\Controllers\ComplianceController;
use App\Http\Controllers\EmailConfigController;
use App\Http\Controllers\JobRequestController;
use App\Http\Controllers\PriorityController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\UserAccountController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\LbsJobController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\AccountSettingsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (session()->has('user_id')) {
        return redirect()->route('dashboard');
    }
    return view('app');
});

Route::get('/login', function () {
    return redirect('/');
})->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth.session')->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard/lbs/add', [LbsJobController::class, 'addForm'])->name('lbs.add');
    Route::post('/dashboard/lbs', [LbsJobController::class, 'store'])->name('lbs.store');
    Route::get('/dashboard/lbs/list', [LbsJobController::class, 'index'])->name('lbs.list');
    Route::get('/dashboard/lbs/job/{id}', [LbsJobController::class, 'show'])->name('lbs.job.view');
    Route::put('/dashboard/lbs/job/{id}', [LbsJobController::class, 'update'])->name('lbs.job.update');
    Route::post('/dashboard/lbs/job/{id}/files', [LbsJobController::class, 'uploadFiles'])->name('lbs.job.uploadFiles');
    Route::post('/dashboard/lbs/job/{id}/file/delete', [LbsJobController::class, 'deleteFile'])->name('lbs.job.deleteFile');
    Route::get('/dashboard/lbs/job/{id}/file/{file}', [LbsJobController::class, 'downloadFile'])->name('lbs.job.file');
    Route::post('/dashboard/lbs/job/{id}/checker-uploads', [LbsJobController::class, 'uploadCheckerFiles'])->name('lbs.job.checkerUploads');
    Route::post('/dashboard/lbs/job/{id}/run-comment', [LbsJobController::class, 'addRunComment'])->name('lbs.job.runComment');
    Route::post('/dashboard/lbs/job/{id}/comment', [LbsJobController::class, 'addJobComment'])->name('lbs.job.comment');
    Route::get('/dashboard/lbs/completed', function () {
        return view('lbs.completed', ['sidebar_active' => 'lbs.completed']);
    })->name('lbs.completed');
    Route::get('/dashboard/lbs/mailbox', [LbsJobController::class, 'mailbox'])->name('lbs.mailbox');
    Route::get('/dashboard/lbs/job/{id}/email-preview', [LbsJobController::class, 'emailPreview'])->name('lbs.job.emailPreview');
    Route::post('/dashboard/lbs/job/{id}/send-mailbox-email', [LbsJobController::class, 'sendMailboxEmail'])->name('lbs.job.sendMailboxEmail');
    Route::get('/dashboard/lbs/review', [LbsJobController::class, 'review'])->name('lbs.review');
    Route::get('/dashboard/lbs/trash', function () {
        return view('lbs.trash', ['sidebar_active' => 'lbs.trash']);
    })->name('lbs.trash');
    Route::get('/dashboard/bph/add', function () {
        return view('bph.add', ['sidebar_active' => 'bph.add']);
    })->name('bph.add');
    Route::get('/dashboard/bph/list', function () {
        return view('bph.list', ['sidebar_active' => 'bph.list']);
    })->name('bph.list');
    Route::get('/dashboard/csp/add', function () {
        return view('csp.add', ['sidebar_active' => 'csp.add']);
    })->name('csp.add');
    Route::get('/dashboard/csp/list', function () {
        return view('csp.list', ['sidebar_active' => 'csp.list']);
    })->name('csp.list');
    Route::get('/dashboard/csp/completed', function () {
        return view('csp.completed', ['sidebar_active' => 'csp.completed']);
    })->name('csp.completed');
    Route::get('/dashboard/csp/review', function () {
        return view('csp.review', ['sidebar_active' => 'csp.review']);
    })->name('csp.review');
    Route::get('/dashboard/csp/trash', function () {
        return view('csp.trash', ['sidebar_active' => 'csp.trash']);
    })->name('csp.trash');

    Route::get('/dashboard/bluinq/add', fn () => view('bluinq.add', ['sidebar_active' => 'bluinq.add']))->name('bluinq.add');
    Route::get('/dashboard/bluinq/list', fn () => view('bluinq.list', ['sidebar_active' => 'bluinq.list']))->name('bluinq.list');
    Route::get('/dashboard/bluinq/completed', fn () => view('bluinq.completed', ['sidebar_active' => 'bluinq.completed']))->name('bluinq.completed');
    Route::get('/dashboard/bluinq/review', fn () => view('bluinq.review', ['sidebar_active' => 'bluinq.review']))->name('bluinq.review');
    Route::get('/dashboard/bluinq/trash', fn () => view('bluinq.trash', ['sidebar_active' => 'bluinq.trash']))->name('bluinq.trash');

    Route::get('/dashboard/nh/add', fn () => view('nh.add', ['sidebar_active' => 'nh.add']))->name('nh.add');
    Route::get('/dashboard/nh/list', fn () => view('nh.list', ['sidebar_active' => 'nh.list']))->name('nh.list');
    Route::get('/dashboard/nh/completed', fn () => view('nh.completed', ['sidebar_active' => 'nh.completed']))->name('nh.completed');
    Route::get('/dashboard/nh/review', fn () => view('nh.review', ['sidebar_active' => 'nh.review']))->name('nh.review');
    Route::get('/dashboard/nh/trash', fn () => view('nh.trash', ['sidebar_active' => 'nh.trash']))->name('nh.trash');

    Route::get('/dashboard/lc-home-builder/add', fn () => view('lc_home_builder.add', ['sidebar_active' => 'lc_home_builder.add']))->name('lc_home_builder.add');
    Route::get('/dashboard/lc-home-builder/list', fn () => view('lc_home_builder.list', ['sidebar_active' => 'lc_home_builder.list']))->name('lc_home_builder.list');
    Route::get('/dashboard/lc-home-builder/completed', fn () => view('lc_home_builder.completed', ['sidebar_active' => 'lc_home_builder.completed']))->name('lc_home_builder.completed');
    Route::get('/dashboard/lc-home-builder/review', fn () => view('lc_home_builder.review', ['sidebar_active' => 'lc_home_builder.review']))->name('lc_home_builder.review');
    Route::get('/dashboard/lc-home-builder/trash', fn () => view('lc_home_builder.trash', ['sidebar_active' => 'lc_home_builder.trash']))->name('lc_home_builder.trash');

    Route::get('/dashboard/efficient-living/add', fn () => view('efficient_living.add', ['sidebar_active' => 'efficient_living.add']))->name('efficient_living.add');
    Route::get('/dashboard/efficient-living/list', fn () => view('efficient_living.list', ['sidebar_active' => 'efficient_living.list']))->name('efficient_living.list');
    Route::get('/dashboard/efficient-living/completed', fn () => view('efficient_living.completed', ['sidebar_active' => 'efficient_living.completed']))->name('efficient_living.completed');
    Route::get('/dashboard/efficient-living/review', fn () => view('efficient_living.review', ['sidebar_active' => 'efficient_living.review']))->name('efficient_living.review');
    Route::get('/dashboard/efficient-living/trash', fn () => view('efficient_living.trash', ['sidebar_active' => 'efficient_living.trash']))->name('efficient_living.trash');

    Route::get('/dashboard/leading-energy/add', fn () => view('leading_energy.add', ['sidebar_active' => 'leading_energy.add']))->name('leading_energy.add');
    Route::get('/dashboard/leading-energy/list', fn () => view('leading_energy.list', ['sidebar_active' => 'leading_energy.list']))->name('leading_energy.list');
    Route::get('/dashboard/leading-energy/completed', fn () => view('leading_energy.completed', ['sidebar_active' => 'leading_energy.completed']))->name('leading_energy.completed');
    Route::get('/dashboard/leading-energy/review', fn () => view('leading_energy.review', ['sidebar_active' => 'leading_energy.review']))->name('leading_energy.review');
    Route::get('/dashboard/leading-energy/trash', fn () => view('leading_energy.trash', ['sidebar_active' => 'leading_energy.trash']))->name('leading_energy.trash');

    Route::get('/dashboard/reports', function () {
        return view('reports.index', ['sidebar_active' => 'reports']);
    })->name('reports');

    Route::get('/dashboard/settings/email-config', [EmailConfigController::class, 'index'])->name('settings.email_config');
    Route::post('/dashboard/settings/email-config', [EmailConfigController::class, 'store'])->name('settings.email_config.store');

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
});
