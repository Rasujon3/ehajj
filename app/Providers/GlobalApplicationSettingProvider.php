<?php

namespace App\Providers;

use App\Modules\Settings\Models\HomeContent;
use App\Modules\Settings\Models\Logo;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;

class GlobalApplicationSettingProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        /* cache logo info for one month */
        view()->share('related', HomeContent::where('type','related')->where('status',1)->orderby('order', 'ASC')->get());
        view()->share('resource', HomeContent::where('type','resource')->where('status',1)->orderby('order', 'ASC')->get());
        view()->share('others', HomeContent::where('type','others')->where('status',1)->orderby('order', 'ASC')->get());
        return Cache::remember('logo-info', 60 * 60 * 24 * 30, function () {
            $logoInfo = Logo::orderBy('id', 'DESC')->first([
                'logo',
                'title',
                'manage_by',
                'help_link'
            ]);



            if($logoInfo  == null){
                $logoInfo = (object)[];
                $logoInfo->logo = 'assets/images/no_image.png';
            } else {
                $is_logo_exists = file_exists(public_path() . '/' . $logoInfo->logo);
                if ((config('app.APP_ENV') != 'local' && $is_logo_exists != true)) {
                    $logoInfo->logo = 'assets/images/no_image.png';
                }
            }
            return $logoInfo;
        });


    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
