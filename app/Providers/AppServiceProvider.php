<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use  App\Services\FileManager\BaseManager;
use  App\Services\FileManager\QiniuManager;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
         \Carbon\Carbon::setLocale('zh');
        Schema::defaultStringLength(191);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
       $this->app->register(RepositoryServiceProvider::class);
       $this->app->singleton('uploader', function($app) {
           $config = config('filesystems.default','public');
           if($config == 'qiniu')
           {
               return new QiniuManager();
           }
           else
           {
               return new BaseManager();
           }
       });
    }
}
