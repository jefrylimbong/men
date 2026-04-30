<?php

namespace App\Providers;

use App\Models\FinanceMaster;
use App\Models\User;
use App\Models\Vendor;
use App\Models\WithdrawalData;
use App\Observers\UserObserver;
use App\Observers\WithdrawalObserver;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
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
        WithdrawalData::observe(WithdrawalObserver::class);
        User::observe(UserObserver::class);

        Relation::morphMap([
            'Vendor' => Vendor::class,
            'Finance' => FinanceMaster::class,
        ]);
    }
}
