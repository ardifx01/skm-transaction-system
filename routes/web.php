<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;    
use App\Http\Controllers\RoleController;    
use App\Http\Controllers\PolicyController;    
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PDFController;




Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/main', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// user management routes
Route::middleware(['auth'])->group(function () {

    // 🔹 Export policy (report)
    Route::get('/policies/{policy}/export-excel', [PolicyController::class, 'exportExcel'])
        ->name('policies.export-excel')
        ->middleware('can:policies-export');

    // 🔹 Users
    Route::resource('users', UserController::class)->middleware('can:users-view');

    // 🔹 Roles
    Route::resource('roles', RoleController::class)->middleware('can:roles-view');

    // 🔹 Policies
    Route::resource('policies', PolicyController::class)->middleware('can:policies-view');

    Route::post('/policies/{policy}/verify', [PolicyController::class, 'verify'])
        ->name('policies.verify')
        ->middleware('can:policies-verify');

    Route::post('/policies/{policy}/confirm-payment', [PolicyController::class, 'confirmPayment'])
        ->name('policies.confirm-payment')
        ->middleware('can:policies-confirm-payment');

    Route::post('/policies/{policy}/upload-payment', [PolicyController::class, 'uploadPayment'])
        ->name('policies.upload-payment')
        ->middleware('can:policies-upload-payment');

    // 🔹 Set price
    Route::post('/policies/{policy}/set-price', [PolicyController::class, 'setPrice'])
        ->name('policies.set-price')
        ->middleware('can:policies-set-price');

    Route::get('/policies/{policy}/pdf', [PolicyController::class, 'exportPdf'])->name('generate-pdf'); 
        
});

// Profile
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit')
        ->middleware('can:profile-view');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update')
        ->middleware('can:profile-edit');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy')
        ->middleware('can:profile-edit');
});

require __DIR__.'/auth.php';
