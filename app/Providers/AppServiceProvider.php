<?php

namespace App\Providers;

use App\Models\Family;
use App\Models\Account;
use App\Models\Transaction;
use App\Policies\FamilyPolicy;
use App\Policies\AccountPolicy;
use App\Policies\TransactionPolicy;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Contracts\Auth\Authenticatable;

class AppServiceProvider extends ServiceProvider
{
    protected $policies = [
        Family::class => FamilyPolicy::class,
        Account::class => AccountPolicy::class,
        Transaction::class => TransactionPolicy::class,
    ];

    public function register(): void
    {
    }

    public function boot(): void
    {
        Gate::before(function (Authenticatable $user, string $ability, array $args = []) {
            if (isset($user->role) && $user->role === 'super_admin') {
                return true;
            }
        });

    }
}
