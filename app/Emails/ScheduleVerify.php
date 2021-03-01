<?php

namespace Goodcatch\Modules\Core\Emails;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ScheduleVerify extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $verification;

    /**
     * Create a new message instance.
     *
     * @param array $verification
     */
    public function __construct(array $verification)
    {
        $this->verification = $verification;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from (config('mail.from.address'), config('mail.from.name')
            /*[
                'address' => 'department@xxx.com',
                'name' => '部门-' . config('app.name')
            ]*/
        )
            ->subject(Carbon::now ()->toDateString () . '队列任务状态检查')
            ->with ($this->verification)
            ->markdown ('core::emails.verify.schedule')
        ;
    }
}
