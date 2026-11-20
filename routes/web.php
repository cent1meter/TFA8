<?php
use App\Http\Controllers\PageController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::middleware('auth')->group(function (): void {
    Route::view('/account/security', 'auth.security', ['title' => 'Account Security'])->name('account.security');
    Route::redirect('/dashboard', '/customers')->name('dashboard');
    Route::resource('customers', CustomerController::class);
    Route::resource('users', UserController::class);
});
require __DIR__.'/auth.php';
