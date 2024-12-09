<?php

namespace App\Providers;

use URL;
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
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (config('app.env') != 'local') {
            \URL::forceScheme('https');
        }
        $this->app['validator']->extend('bd_nid', function ($attribute, $value, $parameters) {
            $length = strlen($value);
            if (in_array($length, [10, 13, 17])) {
                return true;
            }
            return false;
        });
        $this->app['validator']->extend('bd_mobile', function ($attribute, $value, $parameters) {
            if (!preg_match('/(^(\+8801|8801|01|008801|1))[1|3-9]{1}(\d){8}$/', $value)) {
                return false;
            }
            return true;
        });


        $this->app['validator']->extend('alphaSpace', function ($attribute, $value, $parameters) {
            // echo '<pre>';print_r($value);exit;
            if (!preg_match('/^[a-zA-Z ]*$/', $value)) {
                return false;
            }
            return true;
        });

        $this->app['validator']->extend('alphaComma',function($attribute,$value,$parameters){
            if(preg_match("/^([A-Z],)+[A-Z]$/", $value) == 1) {  // allow starting with A-Z then comma then any repeated with A-Z then comma, and ending with A-Z
                return true;
            }
            return false;

        });

        $this->app['validator']->extend('alphaSpaceArray', function ($attribute, $value, $parameters) {
            foreach ($value as $v) {
                if (!preg_match('/^[a-zA-Z ]*$/', $v)) {
                    return false;
                }
            }
            return true;
        });

        $this->app['validator']->extend('numberDotArray', function ($attribute, $value, $parameters) {
            foreach ($value as $v) {
                if (!preg_match('/^\d*(?:\.{1}\d+)?$/', $v)) {
                    return false;
                }
            }
            return true;
        });

        $this->app['validator']->extend('numericArray', function ($attribute, $value, $parameters)
        {
            foreach ($value as $v) {
                if (!is_int($v)) {
                    return false;
                }
            }
            return true;
        });

        $this->app['validator']->extend('requiredArray', function ($attribute, $value, $parameters)
        {
            foreach ($value as $v) {
                if(empty($v)){
                    return false;
                }
            }
            return true;
        });

    }
}
