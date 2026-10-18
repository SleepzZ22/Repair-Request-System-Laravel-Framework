<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TicketController;

/*
|--------------------------------------------------------------------------
| Web Routes - IT ServiceDesk (ระบบแจ้งซ่อมอุปกรณ์ไอที)
|--------------------------------------------------------------------------
*/

// Authentication & Role Switcher
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/auth/quick/{id}', [AuthController::class, 'quickLogin'])->name('auth.quickLogin');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Root Route - Smart redirect based on Role
Route::get('/', function () {
    $user = Auth::user() ?? \App\Models\User::where('role', 'admin')->first();
    if ($user->isAdmin()) {
        return redirect()->route('admin.dashboard');
    } elseif ($user->isTechnician()) {
        return redirect()->route('technician.dashboard');
    } else {
        return redirect()->route('tickets.myTickets');
    }
})->name('dashboard');

// 👑 ADMIN ROUTES
Route::prefix('admin')->group(function () {
    Route::get('/dashboard', [TicketController::class, 'adminDashboard'])->name('admin.dashboard');
    Route::post('/tickets/{id}/assign', [TicketController::class, 'assignTicket'])->name('admin.assign');
});

// 🛠️ TECHNICIAN ROUTES (ช่าง 4 คน)
Route::prefix('technician')->group(function () {
    Route::get('/dashboard', [TicketController::class, 'technicianDashboard'])->name('technician.dashboard');
    Route::post('/tickets/{id}/claim', [TicketController::class, 'claimTicket'])->name('technician.claim');
});

// 👤 USER ROUTES (ผู้ใช้งานทั่วไป)
Route::get('/my-tickets', [TicketController::class, 'myTickets'])->name('tickets.myTickets');

// 📋 GENERAL TICKET MANAGEMENT
Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
Route::get('/tickets/create', [TicketController::class, 'create'])->name('tickets.create');
Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
Route::get('/tickets/track', [TicketController::class, 'track'])->name('tickets.track');
Route::get('/tickets/{id}', [TicketController::class, 'show'])->name('tickets.show');
Route::patch('/tickets/{id}/status', [TicketController::class, 'updateStatus'])->name('tickets.updateStatus');
