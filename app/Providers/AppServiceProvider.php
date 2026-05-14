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
        URL::forceScheme('https');
        WithdrawalData::observe(WithdrawalObserver::class);
        User::observe(UserObserver::class);

        Relation::morphMap([
            'Vendor' => Vendor::class,
            'Finance' => FinanceMaster::class,
        ]);

        // Meilisearch Filterable Attributes
        if (config('scout.driver') === 'meilisearch') {
            try {
                $client = new \Meilisearch\Client(config('scout.meilisearch.host'), config('scout.meilisearch.key'));
                $client->index('customer_data')->updateFilterableAttributes(['is_active']);
            } catch (\Exception $e) {
                // Silently fail if Meilisearch is not reachable
            }
        }
    }
}
