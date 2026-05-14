<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminReportController;
use App\Http\Controllers\AdminTicketController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\StudentTicketController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
});

Route::post('/logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth', 'role:student'])->group(function (): void {
    Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('student.dashboard');
    Route::get('/tickets', [StudentTicketController::class, 'index'])->name('student.tickets.index');
    Route::get('/tickets/create', [StudentTicketController::class, 'create'])->name('student.tickets.create');
    Route::post('/tickets', [StudentTicketController::class, 'store'])->name('student.tickets.store');
    Route::get('/tickets/{ticket}', [StudentTicketController::class, 'show'])->name('student.tickets.show');
    Route::post('/tickets/{ticket}/replies', [StudentTicketController::class, 'reply'])->name('student.tickets.replies.store');
});

Route::middleware(['auth', 'role:staff'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/tickets', [AdminTicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/{ticket}', [AdminTicketController::class, 'show'])->name('tickets.show');
    Route::patch('/tickets/{ticket}', [AdminTicketController::class, 'update'])->name('tickets.update');
    Route::post('/tickets/{ticket}/replies', [AdminTicketController::class, 'reply'])->name('tickets.replies.store');
    Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
});
