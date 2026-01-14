<?php

use App\Http\Controllers\BranchController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BranchUserController;
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

    Route::resource('users', App\Http\Controllers\UserController::class);
    // Activate/Deactivate User
    Route::patch('users/{user}/activate', [UserController::class, 'activate'])
        ->name('users.activate');
    Route::patch('users/{user}/deactivate', [UserController::class, 'deactivate'])
        ->name('users.deactivate');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
