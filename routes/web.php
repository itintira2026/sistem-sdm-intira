<?php

use App\Http\Controllers\BranchController;
use App\Http\Controllers\Payroll\PotonganImportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BranchUserController;
use App\Http\Controllers\Contact90Controller;
use App\Http\Controllers\DailyReportController;
use App\Http\Controllers\Payroll\GajihPokokImportController;
use App\Http\Controllers\Payroll\GajihPokokController;
use App\Http\Controllers\Payroll\PayrollController;
use App\Http\Controllers\Payroll\PotonganController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\PresensiImportController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => view('auth.login'));

Route::get('/dashboard', fn() => view('dashboard'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');
Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | PROFILE (ALL ROLES)
    |--------------------------------------------------------------------------
    */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Route::middleware('role:fo|manager|superadmin|hr|marketing')->group(function () {

    Route::middleware('role:fo|superadmin|manager')->group(function () {
        /*
        |--------------------------------------------------------------------------
        | CONTACT 90 – FO AREA
        |--------------------------------------------------------------------------
        */
        Route::prefix('contact90')->name('contact90.')->group(function () {
            Route::get('/', [Contact90Controller::class, 'index'])->name('index');
            Route::get('/create', [Contact90Controller::class, 'create'])->name('create');
            Route::post('/', [Contact90Controller::class, 'store'])->name('store');
            Route::get('/{contact90}/edit', [Contact90Controller::class, 'edit'])->name('edit');
            Route::put('/{contact90}', [Contact90Controller::class, 'update'])->name('update');
            Route::delete('/{contact90}', [Contact90Controller::class, 'destroy'])->name('destroy');
        });

        Route::prefix('daily-reports')->name('daily-reports.')->group(function () {
            // Dashboard & List Laporan
            Route::get('/', [DailyReportController::class, 'index'])->name('index');

            // Create Laporan
            Route::get('/create', [DailyReportController::class, 'create'])->name('create');
            Route::post('/', [DailyReportController::class, 'store'])->name('store');

            // Edit & Update Laporan
            Route::get('/{dailyReport}/edit', [DailyReportController::class, 'edit'])->name('edit');
            Route::put('/{dailyReport}', [DailyReportController::class, 'update'])->name('update');

            // Delete Laporan
            Route::delete('/{dailyReport}', [DailyReportController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('daily-reports-3hour-manager')->name('daily-reports.3hour-manager.')->group(function () {
            // Dashboard & List Laporan
            // Route::get('/', [DailyReportController::class, 'index'])->name('index');
            Route::get('/', fn() => view('daily-reports.3hour-manager.index'))->name('index');
            // Create Laporan
            // Route::get('/create', [DailyReportController::class, 'create'])->name('create');
            // Route::post('/', [DailyReportController::class, 'store'])->name('store');

            // Edit & Update Laporan
            // Route::get('/{dailyReport}/edit', [DailyReportController::class, 'edit'])->name('edit');
            // Route::put('/{dailyReport}', [DailyReportController::class, 'update'])->name('update');

            // Delete Laporan
            // Route::delete('/{dailyReport}', [DailyReportController::class, 'destroy'])->name('destroy');
        });
    });

    Route::middleware('role:manager|superadmin')->group(function () {
        /*
        |--------------------------------------------------------------------------
        | CONTACT 90 – MANAGER AREA
        |--------------------------------------------------------------------------
        */
        Route::prefix('contact90/manager')->name('contact90.manager.')->group(function () {
            Route::get('/dashboard', [Contact90Controller::class, 'managerDashboard'])->name('dashboard');
            Route::get('/fo-list', [Contact90Controller::class, 'managerFoList'])->name('folist');
            Route::get('/fo/{user}', [Contact90Controller::class, 'managerFoDetail'])->name('fodetail');
            Route::post('/validate/{contact90}', [Contact90Controller::class, 'validate'])->name('validate');
            Route::post('/validate-bulk', [Contact90Controller::class, 'validateBulk'])->name('validate.bulk');
        });

        Route::prefix('daily-reports/manager')->name('daily-reports.manager.')->group(function () {
            // Dashboard Manager
            Route::get('/dashboard', [DailyReportController::class, 'managerDashboard'])->name('dashboard');

            // List Laporan untuk Validasi
            Route::get('/reports', [DailyReportController::class, 'managerReportList'])->name('reports');

            // Validasi Laporan (Single)
            Route::post('/validate/{dailyReport}', [DailyReportController::class, 'validate'])->name('validate');
        });
    });

    // Route::middleware('role:superadmin')->group(function () {

    //     });

    Route::middleware('role:superadmin|marketing')->group(function () {

        /*
            |--------------------------------------------------------------------------
            | DAILY CONTENTS
            |--------------------------------------------------------------------------
            */
        Route::get('/branches/search', [BranchController::class, 'search'])->name('branches.search');
        Route::resource('daily-contents', App\Http\Controllers\DailyContentController::class);
    });

    Route::middleware('role:superadmin|hr')->group(function () {
        /*
        |--------------------------------------------------------------------------
        | MASTER DATA
        |--------------------------------------------------------------------------
        */
        Route::resource('branches', BranchController::class);
        Route::patch('branches/{branch}/activate', [BranchController::class, 'activate'])->name('branches.activate');
        Route::patch('branches/{branch}/deactivate', [BranchController::class, 'deactivate'])->name('branches.deactivate');

        Route::get('/branches/{branch}/users', [BranchUserController::class, 'create'])->name('branchesusers.create');
        Route::post('/branches/{branch}/users', [BranchUserController::class, 'store'])->name('branches.users.store');
        Route::patch('/branches/{branch}/users/{user}/toggle-manager', [BranchUserController::class, 'toggleManager'])->name('branches.users.toggle-manager');
        Route::delete('/branches/{branch}/users/{user}', [BranchUserController::class, 'destroy'])->name('branches.users.destroy');

        Route::resource('users', UserController::class);
        Route::patch('users/{user}/activate', [UserController::class, 'activate'])->name('users.activate');
        Route::patch('users/{user}/deactivate', [UserController::class, 'deactivate'])->name('users.deactivate');

        Route::post('/import/users', [App\Http\Controllers\UserImportController::class, 'import'])->name('users.import.import');
        Route::post('/branches/import', [App\Http\Controllers\BranchImportController::class, 'store'])->name('branches.import.store');

        /*
        |--------------------------------------------------------------------------
        | PRESENSI
        |--------------------------------------------------------------------------
        */
        Route::resource('presensi', PresensiController::class);
        Route::post('/presensi/{user}/izin', [PresensiController::class, 'storeIzin'])->name('presensi.izin');
        Route::post('/presensi/{user}/sakit', [PresensiController::class, 'storeSakit'])->name('presensi.sakit');
        Route::post('/presensi-import', [PresensiImportController::class, 'store'])->name('presensi.import');
        Route::get('/presensi/template', [PresensiImportController::class, 'template'])->name('presensi.template');

        /*
        |--------------------------------------------------------------------------
        | PAYROLL
        |--------------------------------------------------------------------------
        */
        Route::get('/gaji-pokok', [PayrollController::class, 'index'])->name('gaji-pokok.index');
        Route::get('/payroll/{branch}/show', [PayrollController::class, 'show'])->name('gaji.show');

        // Route::get('/gaji-pokok', [GajihPokokController::class, 'index'])->name('gaji-pokok.index');
        Route::get('/gaji-pokok/{branch}/create', [GajihPokokController::class, 'create'])->name('gaji-pokok.create');
        Route::post('/gaji-pokok/{branch}', [GajihPokokController::class, 'store'])->name('gaji-pokok.store');
        Route::get('/gaji-pokok/{branch}/detail', [GajihPokokController::class, 'detail'])->name('gaji-pokok.detail');
        Route::get('/gaji-pokok/{gajihPokok}', [GajihPokokController::class, 'show'])->name('gaji-pokok.show');
        Route::delete('/gaji-pokok/{gajiPokok}', [GajihPokokController::class, 'destroy'])->name('gaji-pokok.destroy');

        Route::post('/gaji-pokok-import', [GajihPokokImportController::class, 'store'])->name('gaji-pokok.import');
        Route::get('/gaji-pokok/template', [GajihPokokImportController::class, 'template'])->name('gaji-pokok.template');

        /*
        |--------------------------------------------------------------------------
        | POTONGAN
        |--------------------------------------------------------------------------
        */
        Route::get('/potongan/{branch}/detail', [PotonganController::class, 'index'])->name('potongan.index');
        Route::get('/potongan/{branch}/create', [PotonganController::class, 'create'])->name('potongan.create');
        Route::post('/potongan/{branch}/create', [PotonganController::class, 'store'])->name('potongan.store');
        Route::delete('/potongan/{potongan}', [PotonganController::class, 'destroy'])->name('potongan.destroy');

        Route::post('/potongan-import', [PotonganImportController::class, 'store'])->name('potongan.import');
        Route::get('/potongan/template', [PotonganImportController::class, 'template'])->name('potongan.template');
    });

    // });
});

require __DIR__ . '/auth.php';
