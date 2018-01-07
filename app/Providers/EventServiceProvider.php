<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\Event' => [
            'App\Listeners\EventListener',
        ],

        /**
         * 管理员日志信息
         */
        'App\Events\AdminLogger' => [
            'App\Listeners\AdminLoggerListener',
        ],

        // sql日志事件
        'Illuminate\Database\Events\QueryExecuted' => [
            'App\Listeners\QueryListener'
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
