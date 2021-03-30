<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\CastMember;
use App\Models\Category;
use App\Models\Genre;
use App\Observers\SyncModelObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        Category::observe(SyncModelObserver::class);
        Genre::observe(SyncModelObserver::class);
        CastMember::observe(SyncModelObserver::class);

        // pendende execultar
        // php artisan vendor:publish --provider="Bschmitt\Amqp\LumenServiceProvider"
        // aguardando resposta suporte curso

    }
}
