<?php

use App\Http\Controllers\Admin\UsersController as AdminUsersController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\AnnouncementsController;
use App\Http\Controllers\CertificatesController;
use App\Http\Controllers\Community\CertificateRequestsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProblemReportsController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\ResidentsController;
use App\Http\Controllers\RequestsController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:5,1');
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])->middleware('throttle:5,1');
    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->middleware('throttle:3,1')->name('password.email');
    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->middleware('throttle:5,1')->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::get('/account/pending-verification', function (\Illuminate\Http\Request $request) {
        abort_unless($request->user()->hasRole('user'), 403);

        if ($request->user()->is_verified) {
            return to_route('dashboard');
        }

        return view('auth.pending-verification');
    })->name('verification.pending');

    Route::get('/dashboard', DashboardController::class)->middleware('verified.account')->name('dashboard');
    Route::get('/settings', [SettingsController::class, 'edit'])->middleware('verified.account')->name('settings.edit');
    Route::put('/settings', [SettingsController::class, 'update'])->middleware('verified.account')->name('settings.update');
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::middleware(['role:user', 'verified.account'])->prefix('my')->name('my.')->group(function () {
        Route::get('/certificates', [CertificateRequestsController::class, 'index'])->name('certificates.index');
        Route::get('/certificates/create', [CertificateRequestsController::class, 'create'])->name('certificates.create');
        Route::post('/certificates', [CertificateRequestsController::class, 'store'])->name('certificates.store');
        Route::get('/problem-reports', [ProblemReportsController::class, 'mine'])->name('problems.index');
        Route::get('/problem-reports/create', [ProblemReportsController::class, 'create'])->name('problems.create');
        Route::post('/problem-reports', [ProblemReportsController::class, 'store'])->name('problems.store');
    });

    Route::middleware('role:staff,admin')->group(function () {
        Route::resource('residents', ResidentsController::class)->except(['destroy']);
        Route::get('/certificates', [CertificatesController::class, 'index'])->name('certificates.index');
        Route::get('/requests', [RequestsController::class, 'index'])->name('requests.index');
        Route::get('/requests/create', [RequestsController::class, 'create'])->name('requests.create');
        Route::post('/requests', [RequestsController::class, 'store'])->name('requests.store');
        Route::get('/requests/{serviceRequest}/review', [RequestsController::class, 'edit'])->name('requests.review');
        Route::get('/requests/{serviceRequest}/certificate', [RequestsController::class, 'certificate'])->name('requests.certificate');
        Route::patch('/requests/{serviceRequest}/status', [RequestsController::class, 'updateStatus'])->name('requests.status');
        Route::resource('announcements', AnnouncementsController::class)->except(['show', 'destroy']);
        Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');
        Route::get('/problem-reports', [ProblemReportsController::class, 'index'])->name('problem-reports.index');
        Route::patch('/problem-reports/{problemReport}/status', [ProblemReportsController::class, 'updateStatus'])->name('problem-reports.status');
    });

    Route::middleware('role:admin')->group(function () {
        Route::delete('/residents/{resident}', [ResidentsController::class, 'destroy'])->name('residents.destroy');
        Route::post('/residents/{residentId}/restore', [ResidentsController::class, 'restore'])->whereNumber('residentId')->name('residents.restore');
        Route::delete('/requests/{serviceRequest}', [RequestsController::class, 'destroy'])->name('requests.destroy');
        Route::post('/requests/{serviceRequestId}/restore', [RequestsController::class, 'restore'])->whereNumber('serviceRequestId')->name('requests.restore');
        Route::delete('/announcements/{announcement}', [AnnouncementsController::class, 'destroy'])->name('announcements.destroy');
        Route::delete('/problem-reports/{problemReport}', [ProblemReportsController::class, 'destroy'])->name('problem-reports.destroy');
        Route::post('/problem-reports/{problemReportId}/restore', [ProblemReportsController::class, 'restore'])->whereNumber('problemReportId')->name('problem-reports.restore');

        Route::prefix('admin')->name('admin.')->group(function () {
            Route::resource('users', AdminUsersController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
            Route::patch('/users/{user}/verify', [AdminUsersController::class, 'verify'])->name('users.verify');
            Route::post('/users/{userId}/restore', [AdminUsersController::class, 'restore'])->whereNumber('userId')->name('users.restore');
        });
    });
});
