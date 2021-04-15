<?php
/**
 * @author  Allen <ali@goodcatch.cn>
 */

namespace Goodcatch\Modules\Core\Console;

use Goodcatch\Modules\Core\Model\Admin\Schedule as MySchedule;
use Illuminate\Console\Scheduling\CallbackEvent;
use Illuminate\Console\Scheduling;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class Kernel
{

    private $schedule;

    private $container;

    public function __construct (Application $container, Schedule $schedule)
    {
        $this->container = $container;
        $this->schedule = $schedule;
    }

    public function schedule ()
    {

        foreach (MySchedule::ofEnabled ()->ofOrdered ()->get () as $idx => $my_schedule)
        {
            // Log::info ('try schedule ' . $my_schedule->id);

            $callbackEvent = $this->initSchedule ($my_schedule);
            if (isset ($callbackEvent))
            {
                $this->attachSchedule ($callbackEvent, $my_schedule);
                // Log::info ('scheduled ' . $my_schedule->id);
            }
        }
    }

    /**
     * configure schedule with settings, then schedule it
     *
     * @param $event
     * @param MySchedule $my_schedule
     */
    private function attachSchedule ($event, MySchedule $my_schedule)
    {
        if ($my_schedule->overlapping === MySchedule::OVER_LAPPING_DISABLE)
        {
            $event->withoutOverlapping ();
        }

        $event->cron ($my_schedule->cron);

        if (! empty ($my_schedule->ping_before) && Str::startsWith ($my_schedule->ping_before, 'http'))
        {
            $event->pingBefore ($my_schedule->ping_before);
        }
        if (! empty ($my_schedule->ping_success) && Str::startsWith ($my_schedule->ping_before, 'http'))
        {
            $event->pingOnSuccess ($my_schedule->ping_success);
        }
        if (! empty ($my_schedule->ping_failure) && Str::startsWith ($my_schedule->ping_before, 'http'))
        {
            $event->pingOnFailure ($my_schedule->ping_failure);
        }
        if ($my_schedule->one_server === MySchedule::ONE_SERVER_ENABLE)
        {
            $event->onOneServer ();
        }
        if ($my_schedule->background === MySchedule::BACKGROUND_ENABLE)
        {
            $event->runInBackground ();
        }
        if ($my_schedule->maintenance === MySchedule::MAINTENANCE_ENABLE)
        {
            $event->evenInMaintenanceMode ();
        }
        if ($my_schedule->once === MySchedule::ONCE_ENABLE)
        {
            $event->before (function () use ($my_schedule) {
                // 执行前关闭标志，下次不自动执行，直到标志重新设置
                $my_schedule->status = MySchedule::STATUS_DISABLE;
                $my_schedule->save ();
            });
        }

        $output = storage_path ('logs' . DIRECTORY_SEPARATOR . 'schedules');
        if (! is_dir ($output))
        {
            mkdir ($output);
        }

        $event->appendOutputTo ($output
            . DIRECTORY_SEPARATOR
            . $my_schedule->id
            . '-'
            . date("Ymd", time ())
            . '.log'
        );
    }

    /**
     * initiate schedule in different ways
     *
     * @param $schedule
     * @return CallbackEvent|Scheduling\Event|null
     */
    private function initSchedule (MySchedule $schedule)
    {
        $my_schedule = null;
        switch ($schedule->schedule_type) {
            case MySchedule::TYPE_COMMAND:
                $my_schedule = $this->schedule->command ($schedule->input);
                break;
            case MySchedule::TYPE_EXEC:
                $my_schedule = $this->schedule->exec ($schedule->input);
                break;
            case MySchedule::TYPE_JOB:
                $instance = $this->getJobInstance ($schedule->input, $schedule);
                if (isset ($instance))
                {
                    if (is_null ($schedule->queue))
                    {
                        $my_schedule = $this->schedule->job ($instance);
                    } else {
                        Log::info ('scheduled job on overwrite queue ' . $schedule->queue);
                        $my_schedule = $this->schedule->job ($instance, $schedule->queue);
                    }
                }
                break;
        }
        return $my_schedule;
    }

    /**
     * Get initial schedule job instance
     *
     * @param $clazz
     * @param $schedule
     * @return mixed
     */
    private function getJobInstance ($clazz, $schedule)
    {
        $instance = null;
        try {
            $instance = new $clazz ($schedule->payload);
        } catch (\Exception $e)
        {
            Log::error ($e->getMessage ());
        }
        return $instance;
    }
}
