<?php

namespace App\Providers;

use App\Core\Contracts\OrderConfirmationNumberGenerator;
use App\Core\Contracts\TicketCodeGenerator;
use App\Core\RandomOrderConfirmationNumberGenerator;
use App\Core\RandomTicketCodeGenerator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(TicketCodeGenerator::class, RandomTicketCodeGenerator::class);
        $this->app->bind(OrderConfirmationNumberGenerator::class, RandomOrderConfirmationNumberGenerator::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
