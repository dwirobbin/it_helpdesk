<?php

use App\Http\Controllers\AppSettingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RespondController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TicketChatController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login')->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');
    Route::delete('/profile/avatar', [ProfileController::class, 'deleteAvatar'])->name('profile.avatar.delete');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('/positions')->name('positions.')->controller(PositionController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::put('/{position:slug}', 'update')->name('update');
        Route::delete('/{position:slug}', 'destroy')->name('destroy');
        Route::get('/{position:slug}/edit', 'edit')->name('edit');
    });

    Route::prefix('/departments')->name('departments.')->controller(DepartmentController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::put('/{department:slug}', 'update')->name('update');
        Route::delete('/{department:slug}', 'destroy')->name('destroy');
        Route::get('/{department:slug}/edit', 'edit')->name('edit');
    });

    Route::prefix('/employees')->name('employees.')->controller(EmployeeController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::get('/create', 'create')->name('create');
        Route::put('/{employee}', 'update')->name('update');
        Route::delete('/{employee}', 'destroy')->name('destroy');
        Route::get('/{employee}/edit', 'edit')->name('edit');
        Route::patch('/{employee}/change-account-status', 'changeAccountStatus')->name('change-account-status');
    });

    Route::prefix('/tickets')->name('tickets.')->controller(TicketController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::get('/{ticket:slug}', 'show')->name('show');
        Route::post('/{ticket:slug}', 'update')->name('update');
        Route::delete('/{ticket:slug}', 'destroy')->name('destroy');
        Route::get('/{ticket:slug}/edit', 'edit')->name('edit');
        Route::patch('/{ticket:slug}/confirm', 'confirm')->name('confirm');
        Route::patch('/{ticket:slug}/solved', 'solved')->name('solved');
    });

    Route::prefix('/responds')->name('responds.')->controller(RespondController::class)->group(function () {
        Route::post('/', 'store')->name('store');
        Route::get('/create', 'create')->name('create');
        Route::post('/{respond}', 'update')->name('update');
        Route::get('/{respond}/edit', 'edit')->name('edit');
    });

    Route::prefix('/reports')->name('reports.')->controller(ReportController::class)->group(function () {
        Route::get('/tickets', 'indexTicketReport')->name('tickets.index');
        Route::post('/tickets', 'createTicketReport')->name('tickets.create');
    });

    Route::prefix('/ticket-chats')->name('ticket-chats.')->controller(TicketChatController::class)->group(function () {
        Route::get('/', 'create')->name('create');
        Route::post('/', 'store')->name('store');
    });

    Route::prefix('/settings')->name('settings.')->controller(SettingController::class)->group(function () {
        Route::get('/', 'edit')->name('edit');
        Route::post('/app-information', 'updateAppInformation')->name('app-information.update');
        Route::post('/company-information', 'updateCompanyInformation')->name('company-information.update');
    });
});

require __DIR__ . '/auth.php';
