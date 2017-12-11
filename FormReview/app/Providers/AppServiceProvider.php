<?php

namespace App\Providers;
use Illuminate\Support\Facades\Cache;
use Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //自定义验证规则
        Validator::extend('code', function($attribute, $value, $parameters, $validator) {
            if (!empty($parameters)) {
                $code = Cache::get('code_'.$parameters[0]);
                return $value == $code;
            }
            return false;
        });
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
