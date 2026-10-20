<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::define('admin-hr', fn(User $user) => $user->isAdminHr());
        Gate::define('direktur', fn(User $user) => $user->isDirektur() || $user->isAdminHr());
        Gate::define('hod', fn(User $user) => $user->isHod() || $user->isAdminHr());
        Gate::define('supervisor', fn(User $user) => $user->isSupervisor() || $user->isAdminHr());
        Gate::define('operator', fn(User $user) => $user->isOperator() || $user->isAdminHr());
    }
}
