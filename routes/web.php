<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;    
use App\Http\Controllers\RoleController;    
use App\Http\Controllers\PolicyController;    
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/main', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


// user management routes
Route::middleware(['auth'])->group(function () {
    Route::resource('users', UserController::class)->middleware('can:view-users');
    Route::resource('roles', RoleController::class)->middleware('can:view-roles');
    Route::resource('policies', PolicyController::class)->middleware('can:view-polis');

    Route::post('/policies/{policy}/verify', [PolicyController::class, 'verify'])->name('policies.verify');
    Route::post('/policies/{policy}/confirm-payment', [PolicyController::class, 'confirmPayment'])->name('policies.confirm-payment');
    Route::post('/policies/{policy}/send-mail', [PolicyController::class, 'sendMail'])
        ->name('policies.send-mail')
        ->middleware('can:send-polis-mail');
});




// end user management routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
