<?php

use App\Http\Controllers\BranchController;
use App\Http\Controllers\Payroll\PotonganImportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BranchUserController;
use App\Http\Controllers\Payroll\GajihPokokImportController;
use App\Http\Controllers\Payroll\GajihPokokController;
// use App\Http\Controllers\Payroll\GajihPokokImportController;
use App\Http\Controllers\Payroll\PayrollController;
use App\Http\Controllers\Payroll\PotonganController;
use App\Http\Controllers\PresensiImportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    Route::post('/import/users', [App\Http\Controllers\UserImportController::class, 'import'])
        ->name('users.import.import')
        ->middleware('auth');

    Route::get('/branches/search', [BranchController::class, 'search'])
        ->name('branches.search');

    Route::post('/branches/import', [App\Http\Controllers\BranchImportController::class, 'store'])
        ->name('branches.import.store')
        ->middleware('auth');

    Route::resource('branches', App\Http\Controllers\BranchController::class);
    Route::patch('branches/{branch}/activate', [BranchController::class, 'activate'])
        ->name('branches.activate');

    Route::patch('branches/{branch}/deactivate', [BranchController::class, 'deactivate'])
        ->name('branches.deactivate');



    Route::get('/branches/{branch}/users', [BranchUserController::class, 'create'])
        ->name('branchesusers.create');
    Route::post('/branches/{branch}/users', [BranchUserController::class, 'store'])
        ->name('branches.users.store');
    Route::patch('/branches/{branch}/users/{user}/toggle-manager', [BranchUserController::class, 'toggleManager'])
        ->name('branches.users.toggle-manager');
    Route::delete('/branches/{branch}/users/{user}', [BranchUserController::class, 'destroy'])
        ->name('branches.users.destroy');


    Route::get('/gaji-pokok/{gajihPokok}', [GajihPokokController::class, 'show'])->name('gaji-pokok.show');

    Route::get('/payroll/{branch}/show', [PayrollController::class, 'show'])->name('gaji.show');
    Route::get('/gaji', [PayrollController::class, 'index'])->name('gaji.index');
    // Route::get('/gaji-pokok/{branch}/detail', [GajihPokokController::class, 'detail'])->name('gaji-pokok.detail');
    //    Route::get('/gaji-pokok/{gajihPokok}', [GajiPokokController::class, 'show'])
    //      ->name('gaji-pokok.show');
    Route::get('/gaji-pokok', [GajihPokokController::class, 'index'])->name('gaji-pokok.index');
    Route::get('/gaji-pokok/{branch}/create', [GajihPokokController::class, 'create'])->name('gaji-pokok.create');
    Route::post('/gaji-pokok/{branch}', [GajihPokokController::class, 'store'])->name('gaji-pokok.store');
    Route::get('/gaji-pokok/{branch}/detail', [GajihPokokController::class, 'detail'])->name('gaji-pokok.detail');
    Route::delete('/gaji-pokok/{gajiPokok}', [GajihPokokController::class, 'destroy'])->name('gaji-pokok.destroy');

    Route::post('/gaji-pokok-import', [GajihPokokImportController::class, 'store'])->name('gaji-pokok.import');
    Route::get('/gaji-pokok/template', [GajihPokokImportController::class, 'template'])->name('gaji-pokok.template');

    Route::get('/potongan/{branch}/detail', [PotonganController::class, 'index'])->name('potongan.index');
    Route::get('/potongan/{branch}/create', [PotonganController::class, 'create'])->name('potongan.create');
    Route::post('/potongan/{branch}/create', [PotonganController::class, 'store'])->name('potongan.store');
    Route::delete('/potongan/{potongan}', [PotonganController::class, 'destroy'])->name('potongan.destroy');

    Route::post('/potongan-import', [PotonganImportController::class, 'store'])->name('potongan.import');
    Route::get('/potongan/template', [PotonganImportController::class, 'template'])->name('potongan.template');

    Route::resource('users', App\Http\Controllers\UserController::class);
    // Activate/Deactivate User
    Route::patch('users/{user}/activate', [UserController::class, 'activate'])
        ->name('users.activate');
    Route::patch('users/{user}/deactivate', [UserController::class, 'deactivate'])
        ->name('users.deactivate');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('presensi', App\Http\Controllers\PresensiController::class);
    Route::post('/presensi-import', [PresensiImportController::class, 'store'])->name('presensi.import');
    Route::get('/presensi/template', [PresensiImportController::class, 'template'])->name('presensi.template');

    Route::resource('daily-contents', App\Http\Controllers\DailyContentController::class);

    // routes/web.php



    // });
});

require __DIR__ . '/auth.php';
