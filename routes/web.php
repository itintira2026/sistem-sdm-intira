<?php

use App\Http\Controllers\BranchController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BranchUserController;
use App\Http\Controllers\Payroll\GajihPokokController;
use App\Http\Controllers\Payroll\PayrollController;
use App\Http\Controllers\Payroll\PotonganController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
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

    Route::get('/gaji', [PayrollController::class, 'index'])->name('gaji.index');
    Route::get('/gaji-pokok', [GajihPokokController::class, 'index'])->name('gaji-pokok.index');
    // Route::get('/gaji-pokok/{branch}/detail', [GajihPokokController::class, 'detail'])->name('gaji-pokok.detail');
    Route::get('/gaji-pokok/detail/show', [GajihPokokController::class, 'show'])->name('gaji-pokok.show');
    //    Route::get('/gaji-pokok/{gajihPokok}', [GajiPokokController::class, 'show'])
    //      ->name('gaji-pokok.show');
    Route::get('/gaji-pokok/{branch}/detail', [GajihPokokController::class, 'detail'])->name('gaji-pokok.detail');
    Route::delete('/gaji-pokok/{gajiPokok}', [GajihPokokController::class, 'destroy'])->name('gaji-pokok.destroy');

    Route::get('/potongan/{branch}/detail', [PotonganController::class, 'index'])->name('potongan.index');
    Route::get('/potongan/{branch}/create', [PotonganController::class, 'create'])->name('potongan.create');
    Route::post('/potongan/{branch}/create', [PotonganController::class, 'store'])->name('potongan.store');
    Route::delete('/potongan/{potongan}', [PotonganController::class, 'destroy'])->name('potongan.destroy');

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

    // Import routes for branches
    Route::get('/branches/import', [App\Http\Controllers\BranchImportController::class, 'create'])
        ->name('branches.import.create')
        ->middleware('auth');

    Route::post('/branches/import', [App\Http\Controllers\BranchImportController::class, 'store'])
        ->name('branches.import.store')
        ->middleware('auth');

    Route::get('/branches/import/template', [App\Http\Controllers\BranchImportController::class, 'template'])
        ->name('branches.import.template')
        ->middleware('auth');

    Route::post('/import/users', [App\Http\Controllers\UserImportController::class, 'import'])
        ->name('users.import.import')
        ->middleware('auth');
});

require __DIR__ . '/auth.php';
