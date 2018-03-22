<?php

namespace App\Jobs;

use App\Models\Admin\Admin;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Admin $user)
    {
        $this->user = $user;
    }

    /**
     * 执行队列的方法 比如发送邮件 
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $user = $this->user;
        
        // debug 
        //logResult("【消息队列发送内容】".var_export($user->toArray(), true));
         //echo 'queue';
         Log::info("【用户个人姓名】".$this->user->username);
        //return true;
        //   Mail::raw('这里填写邮件的内容',function ($message){
        //          // 发件人（你自己的邮箱和名称）
        //         $message->from('your_email@163.com', 'yourname');
        //         // 收件人的邮箱地址
        //         $message->to($this->user);
        //         // 邮件主题
        //         $message->subject('队列发送邮件');
        //     });
        // }
    }
}
