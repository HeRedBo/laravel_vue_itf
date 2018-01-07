<?php

namespace App\Listeners;

use App\Events\AdminLogger;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class AdminLoggerListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param AdminLogger $event
     * @return void
     * @throws \Exception
     */
    public function handle(AdminLogger $event)
    {
        try 
        {
            $model = $event->model;
            $model->user_id = auth('admin')->user()->id;
            $model->catalog = $event->catalog;
            $model->url     = url()->current();
            $model->intro   = $event->intro;
            $model->venue_id = $event->venue_id;
            $model->ip       = getClientIp();
            $model->created_at = time();
            $model->save();   
        } 
        catch (\Exception $e) 
        {
            logResult("[管理员日志监听数据报错失败]".$e->__toString(),'error');
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }
}
