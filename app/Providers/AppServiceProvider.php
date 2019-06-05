<?php

namespace App\Providers;

use App\Models\Core\AppUser;
use App\Observers\AppUserObserver;
use Illuminate\Support\ServiceProvider;
use App\Models\Core\Message;
use App\Observers\MessageObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Message::observe(MessageObserver::class);
        AppUser::observe(AppUserObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
