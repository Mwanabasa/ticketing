<?php

use App\Http\Controllers\AdminAuditLogController;
use App\Http\Controllers\AdminCategoryController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminKnowledgeBaseController;
use App\Http\Controllers\AdminReportController;
use App\Http\Controllers\AdminTicketController;
use App\Http\Controllers\AdminTicketTemplateController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\KnowledgeBaseController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\StudentTicketController;
use App\Http\Controllers\TimeEntryController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');

Route::get('/knowledge-base', [KnowledgeBaseController::class, 'index'])->name('knowledge-base.index');
Route::get('/knowledge-base/{article}', [KnowledgeBaseController::class, 'show'])->name('knowledge-base.show');

// ── Auth ──────────────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function (): void {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:5,1');
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->middleware('throttle:5,1');

    Route::get('/forgot-password', [PasswordResetController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'store'])->middleware('throttle:3,1')->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->middleware('throttle:3,1')->name('password.update');
});

Route::post('/logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// ── Email verification ────────────────────────────────────────────────────────
Route::middleware('auth')->group(function (): void {
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        $user = $request->user();
        return redirect($user->isStaff() ? route('admin.dashboard') : route('student.dashboard'))
            ->with('status', 'Email verified successfully!');
    })->middleware('signed')->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('status', 'Verification link sent!');
    })->middleware('throttle:3,1')->name('verification.send');
});

// ── Student ───────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:student'])->group(function (): void {
    Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('student.dashboard');
    Route::get('/tickets', [StudentTicketController::class, 'index'])->name('student.tickets.index');
    Route::get('/tickets/create', [StudentTicketController::class, 'create'])->name('student.tickets.create');
    Route::post('/tickets', [StudentTicketController::class, 'store'])->name('student.tickets.store');
    Route::get('/tickets/{ticket}', [StudentTicketController::class, 'show'])->name('student.tickets.show');
    Route::post('/tickets/{ticket}/replies', [StudentTicketController::class, 'reply'])->name('student.tickets.replies.store');
    Route::post('/tickets/{ticket}/rate', [StudentTicketController::class, 'rate'])->name('student.tickets.rate');
    Route::post('/tickets/{ticket}/rate', [StudentTicketController::class, 'rate'])->name('student.tickets.rate');
});

// ── Notifications (both roles) ────────────────────────────────────────────────
Route::middleware(['auth'])->group(function (): void {
    Route::get('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
});

// ── Admin / Staff ─────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:staff'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Tickets
    Route::get('/tickets', [AdminTicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/{ticket}', [AdminTicketController::class, 'show'])->name('tickets.show');
    Route::patch('/tickets/{ticket}', [AdminTicketController::class, 'update'])->name('tickets.update');
    Route::post('/tickets/{ticket}/replies', [AdminTicketController::class, 'reply'])->name('tickets.replies.store');
    Route::post('/tickets/bulk', [AdminTicketController::class, 'bulkUpdate'])->name('tickets.bulk');
    Route::post('/tickets/merge', [AdminTicketController::class, 'merge'])->name('tickets.merge');

    // Time entries
    Route::get('/tickets/{ticket}/time-entries', [TimeEntryController::class, 'index'])->name('time-entries.index');
    Route::get('/tickets/{ticket}/time-entries/create', [TimeEntryController::class, 'create'])->name('time-entries.create');
    Route::post('/tickets/{ticket}/time-entries', [TimeEntryController::class, 'store'])->name('time-entries.store');
    Route::delete('/time-entries/{timeEntry}', [TimeEntryController::class, 'destroy'])->name('time-entries.destroy');

    // Templates
    Route::get('/templates', [AdminTicketTemplateController::class, 'index'])->name('templates.index');
    Route::get('/templates/create', [AdminTicketTemplateController::class, 'create'])->name('templates.create');
    Route::post('/templates', [AdminTicketTemplateController::class, 'store'])->name('templates.store');
    Route::get('/templates/{template}/edit', [AdminTicketTemplateController::class, 'edit'])->name('templates.edit');
    Route::patch('/templates/{template}', [AdminTicketTemplateController::class, 'update'])->name('templates.update');
    Route::delete('/templates/{template}', [AdminTicketTemplateController::class, 'destroy'])->name('templates.destroy');

    // Reports
    Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');

    // Knowledge base
    Route::get('/knowledge-base', [AdminKnowledgeBaseController::class, 'index'])->name('knowledge-base.index');
    Route::get('/knowledge-base/create', [AdminKnowledgeBaseController::class, 'create'])->name('knowledge-base.create');
    Route::post('/knowledge-base', [AdminKnowledgeBaseController::class, 'store'])->name('knowledge-base.store');
    Route::get('/knowledge-base/{article}/edit', [AdminKnowledgeBaseController::class, 'edit'])->name('knowledge-base.edit');
    Route::patch('/knowledge-base/{article}', [AdminKnowledgeBaseController::class, 'update'])->name('knowledge-base.update');
    Route::delete('/knowledge-base/{article}', [AdminKnowledgeBaseController::class, 'destroy'])->name('knowledge-base.destroy');

    // Categories
    Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [AdminCategoryController::class, 'store'])->name('categories.store');
    Route::patch('/categories/{category}', [AdminCategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');

    // Users
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
    Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
    Route::patch('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
    Route::patch('/users/{user}/reset-password', [AdminUserController::class, 'resetPassword'])->name('users.reset-password');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

    // Audit logs
    Route::get('/audit-logs', [AdminAuditLogController::class, 'index'])->name('audit-logs.index');
});
