<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Support\Locale;

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
        Locale::boot([
            'aliases' => [
                'en' => 'en_US.utf8',
                'fr' => 'fr_FR.utf8',
            ],
            'iso_formats' => [
                'en' => [
                    Locale::TYPE_DATETIME => 'dddd, MMMM D, YYYY - h:mm:ss A', // Default is "dddd, MMMM D, YYYY h:mm A"
                ],
                'fr' => [
                    Locale::TYPE_DATETIME => 'dddd D MMMM YYYY - HH:mm:ss', // Default is "dddd D MMMM YYYY HH:mm"
                ],
            ],
        ]);
    }
}
