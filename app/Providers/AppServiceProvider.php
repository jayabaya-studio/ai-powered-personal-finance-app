<?php
// File: app/Providers/AppServiceProvider.php

namespace App\Providers;

use App\Models\Family;
use App\Models\Account;
use App\Models\Transaction;
use App\Policies\FamilyPolicy;
use App\Policies\AccountPolicy;
use App\Policies\TransactionPolicy;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Contracts\Auth\Authenticatable; // Pastikan ini diimpor!

class AppServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Family::class => FamilyPolicy::class,
        Account::class => AccountPolicy::class,
        Transaction::class => TransactionPolicy::class,
        // ... tambahkan policy lain di sini
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Pastikan Anda mengimpor Illuminate\Contracts\Auth\Authenticatable
        // dan menggunakannya sebagai type hint untuk $user di sini.
        Gate::before(function (Authenticatable $user, string $ability, array $args = []) {
            // Contoh logika: jika pengguna adalah 'super_admin', izinkan semua aksi
            if (isset($user->role) && $user->role === 'super_admin') {
                return true;
            }
        });

        // ... kode lain di metode boot() Anda
    }
}
