<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\AccountController;
use App\Http\Controllers\User\CategoryController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\TransactionController;
use App\Http\Controllers\User\BudgetController;
use App\Http\Controllers\User\GoalController;
use App\Http\Controllers\User\FamilyController;
use App\Http\Controllers\User\UserCardController;
use App\Http\Controllers\User\RecurringTransactionController;
use App\Http\Controllers\User\NotificationController;
use App\Http\Controllers\User\AIController;

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
	Route::get('/dashboard/expense-chart-data', [DashboardController::class, 'expenseChartData'])->name('dashboard.expense-chart');
    Route::get('/dashboard/monthly-summary-chart', [DashboardController::class, 'getMonthlySummaryChartData'])->name('dashboard.monthly-summary-chart');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('accounts', AccountController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('transactions', TransactionController::class)->except(['create', 'show']);
	Route::resource('budgets', BudgetController::class)->only(['index', 'store']);
    Route::resource('goals', GoalController::class);
    Route::resource('cards', UserCardController::class)->except(['create', 'show', 'edit']);
    Route::resource('recurring-transactions', RecurringTransactionController::class)->except(['create', 'show', 'edit']);

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
	
    Route::resource('families', FamilyController::class);
    Route::post('/families/clear-current', [FamilyController::class, 'clearCurrent'])->name('families.clear-current');

    Route::prefix('families/{family}')->name('families.')->group(function () {
        Route::post('/members/invite', [FamilyController::class, 'inviteMember'])->name('invite-member');
        Route::delete('/members/{member}', [FamilyController::class, 'removeMember'])->name('remove-member');
        Route::post('/set-current', [FamilyController::class, 'setCurrent'])->name('set-current');
        
        Route::get('/joint-accounts/create', [FamilyController::class, 'createJointAccountForm'])->name('joint-accounts.create');
        Route::post('/joint-accounts', [FamilyController::class, 'storeJointAccount'])->name('joint-accounts.store');
    });

    Route::post('/ai-assistant/analyze', [AIController::class, 'analyze'])->name('ai.analyze');

});

require __DIR__.'/auth.php';
