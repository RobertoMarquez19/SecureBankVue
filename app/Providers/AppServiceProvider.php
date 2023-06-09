<?php

namespace App\Providers;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Testing\Fluent\Concerns\Has;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Passport::ignoreRoutes();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Validator::extend('unique_encrypted', function ($attribute, $value, $parameters, $validator) {
            $table = $parameters[0];
            $column = $parameters[1];

            $decryptedValue = \hash('sha256',$value);
            $count = DB::table($table)
                ->where($column, '=', $decryptedValue)
                ->count();

            return $count === 0;
        });
    }
}
