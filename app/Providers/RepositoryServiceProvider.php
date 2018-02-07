<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(\App\Repositories\PostRepository::class, \App\Repositories\PostRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\AdminRepository::class, \App\Repositories\AdminRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\RoleRepository::class, \App\Repositories\RoleRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\RoleRepository::class, \App\Repositories\RoleRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\PermissionRepository::class, \App\Repositories\PermissionRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\VenueRepository::class, \App\Repositories\VenueRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ClassesRepository::class, \App\Repositories\ClassesRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\CardRepository::class, \App\Repositories\CardRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\StudentRepository::class, \App\Repositories\StudentRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\StudentNumberCardRepository::class, \App\Repositories\StudentNumberCardRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\VenueBillRepository::class, \App\Repositories\VenueBillRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\VenueScheduleRepository::class, \App\Repositories\VenueScheduleRepositoryEloquent::class);
        //:end-bindings:
    }
}
