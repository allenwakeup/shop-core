<?php

namespace Goodcatch\Modules\Core\Jobs;

use Goodcatch\Modules\Core\Model\Admin\ScheduleLog;
use Goodcatch\Modules\Laravel\Jobs\Middleware\RateLimited;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class ConfigurableJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const JOB                               = 'job';
    const CONFIG                            = 'config';

    /**
     * time unit name
     */
    const NOW                               = 'NOW';
    const TODAY                             = 'TODAY';
    const START_OF_TODAY                    = 'START_OF_TODAY';
    const END_OF_TODAY                      = 'END_OF_TODAY';
    const YESTERDAY                         = 'YESTERDAY';
    const START_OF_YESTERDAY                = 'START_OF_YESTERDAY';
    const END_OF_YESTERDAY                  = 'END_OF_YESTERDAY';
    const START_OF_WEEK                     = 'START_OF_WEEK';
    const END_OF_WEEK                       = 'END_OF_WEEK';
    const START_OF_MONTH                    = 'START_OF_MONTH';
    const START_OF_LAST_2_MONTHS            = 'START_OF_LAST_2_MONTHS';
    const START_OF_LAST_3_MONTHS            = 'START_OF_LAST_3_MONTHS';
    const START_OF_LAST_6_MONTHS            = 'START_OF_LAST_6_MONTHS';
    const START_OF_LAST_12_MONTHS           = 'START_OF_LAST_12_MONTHS';
    const END_OF_MONTH                      = 'END_OF_MONTH';
    const START_OF_NEXT_2_MONTHS            = 'START_OF_NEXT_2_MONTHS';
    const START_OF_NEXT_3_MONTHS            = 'START_OF_NEXT_3_MONTHS';
    const START_OF_NEXT_6_MONTHS            = 'START_OF_NEXT_6_MONTHS';
    const START_OF_NEXT_12_MONTHS           = 'START_OF_NEXT_12_MONTHS';
    const START_OF_YEAR                     = 'START_OF_YEAR';
    const START_OF_LAST_YEAR                = 'START_OF_LAST_YEAR';
    const END_OF_YEAR                       = 'END_OF_YEAR';


    /**
     * the value is in following options
     * @see START_OF_TODAY
     * @see START_OF_YESTERDAY
     * @see START_OF_WEEK
     * @see START_OF_MONTH
     * @see START_OF_YEAR
     *
     * @var $run_if_previous_failure string auto set configuration
     */
    public $run_if_previous_failure;

    public $scheduleLogId = 0;
    public $scheduleLogName = '';
    public $schedule_output = [];
    public $schedule_failed = false;

    /**
     * @var Carbon ???????????????????????????
     */
    public $time_elapsed;

    /**
     * ????????????????????????????????????
     *
     * @var bool
     */
    public $deleteWhenMissingModels = false;

    /**
     * ????????????????????????????????? (????????????)???
     *
     * @var int
     */
    public $timeout = 120;

    /**
     * ????????????????????????????????????
     *
     * @var int
     */
    public $tries = 5;


    // protected $retry_until = 5;

    /*
     * ????????????????????????
     *
     * @return \DateTime
     */
    /*
    public function retryUntil()
    {
        return now ()->addSeconds ($this->retry_until);
    }
    */

    protected $payload;

    /**
     * Create a new job instance.
     *
     * @param $payload array job data including job configuration
     */
    public function __construct ($payload)
    {
        $this->payload = $payload;

        $this->time_elapsed = Carbon::now ()->toDateTimeString ();

        $this->init ();
    }

    public function middleware()
    {
        return [new RateLimited];
    }


    /**
     * Initiate properties
     */
    protected function init ()
    {
        if (Arr::has ($this->payload, self::JOB . '.' . self::CONFIG))
        {
            $job_conf = Arr::get ($this->payload, self::JOB . '.' . self::CONFIG);

            foreach ($job_conf as $conf => $value)
            {
                // Log::debug ('set ' . $conf . ' to ' . $value);

                $this->{$conf} = $value;
            }
        }
    }

    /**
     * ?????????????????????
     *
     * @param array|null $data
     */
    protected function writeSysLogs (array $data)
    {
        dispatch (new WriteScheduleLog ($data))->onQueue('low');
    }


    /**
     * Execute the job.
     * ??????????????????????????????????????????
     * ?????????????????????????????????????????????
     *
     * @return void
     */
    public function handle ()
    {

    }

    protected function hasPreviousFailure ()
    {
        $expected = false;
        if (! empty ($this->run_if_previous_failure))
        {
            $run_if_previous_failure = $this->transDT ($this->run_if_previous_failure);
            $schedule_logs = ScheduleLog::query ()
                ->where ('schedule_id', $this->scheduleLogId)
                ->whereDate ('created_at', '>=', $run_if_previous_failure)
                ->whereDate ('created_at', '<', Carbon::now ()->toDateTimeString ())
                ->orderBy('created_at', 'desc')
                ->get ();
            if ($schedule_logs->count () === 0 || ($schedule_logs->count () > 0 && $schedule_logs->first ()->ua === 'true'))
            {
                if ($schedule_logs->count () === 0)
                {
                    Log::info ("{$run_if_previous_failure} ??????????????????");

                    $this->jLog ("{$run_if_previous_failure} ??????????????????");
                } else {

                    $previous_failure_at = $schedule_logs->first ()->created_at;

                    Log::info ('{$run_if_previous_failure}????????????????????????????????????????????????', [$previous_failure_at]);

                    $this->jLog ("{$run_if_previous_failure}???????????????????????????????????????????????? {$previous_failure_at}");
                }

                $expected = true;
            }
        }
        return $expected;
    }

    /**
     * ??????????????????
     *
     * @param $message string ?????????
     * @param bool $failed ??????????????????
     */
    protected function jLog ($message, $failed = false)
    {
        $now = Carbon::now ();

        try {
            $time_elapsed = Carbon::parse ($this->time_elapsed);
        } catch (\Exception $e)
        {
            $time_elapsed = $now;
        }

        $this->schedule_output [] = $message . ' ??? ' . $now->diffForHumans ($time_elapsed);

        $this->time_elapsed = $now->toDateTimeString ();

        if (! $this->schedule_failed && $failed)
        {
            $this->schedule_output [] = '????????????';

            $this->schedule_failed = $failed;
            $this->writeLogs ($this->schedule_output);
        }
    }

    /**
     * ???????????????????????????
     *
     * @param  Exception  $exception
     * @return void
     */
    public function failed (Exception $exception)
    {
        Log::error ('Exception queue job: ' . $exception->getMessage (), $exception->getTrace ());
        // ???????????????????????????????????????????????????

        // ?????????????????????
        $this->schedule_failed = true;
        $this->writeLogs (['????????????', $exception->getMessage ()]);
    }

    /**
     *
     * @param $dt_str
     * @param $format
     * @return string
     */
    protected function transDT ($dt_str, $format = null)
    {
        $result = null;
        switch ($dt_str)
        {
            case self::NOW:
                $result = Carbon::now ();
                break;
            case self::TODAY:
                $result = Carbon::today ();
                break;
            case self::START_OF_TODAY:
                $result = Carbon::today ()->startOfDay ();
                break;
            case self::END_OF_TODAY:
                $result = Carbon::today ()->endOfDay ();
                break;
            case self::YESTERDAY:
                $result = Carbon::yesterday ();
                break;
            case self::START_OF_YESTERDAY:
                $result = Carbon::yesterday ()->startOfDay ();
                break;
            case self::END_OF_YESTERDAY:
                $result = Carbon::yesterday ()->endOfDay ();
                break;
            case self::START_OF_WEEK:
                $result = Carbon::now ()->startOfWeek ();
                break;
            case self::END_OF_WEEK:
                $result = Carbon::now ()->endOfWeek ();
                break;
            case self::START_OF_MONTH:
                $result = Carbon::now ()->startOfMonth ();
                break;
            case self::START_OF_LAST_2_MONTHS:
                $result = Carbon::now ()->startOfMonth ()->addMonths (-2)->startOfMonth ();
                break;
            case self::START_OF_LAST_3_MONTHS:
                $result = Carbon::now ()->startOfMonth ()->addMonths (-3)->startOfMonth ();
                break;
            case self::START_OF_LAST_6_MONTHS:
                $result = Carbon::now ()->startOfMonth ()->addMonths (-6)->startOfMonth ();
                break;
            case self::START_OF_LAST_12_MONTHS:
                $result = Carbon::now ()->startOfMonth ()->addMonths (-12)->startOfMonth ();
                break;
            case self::END_OF_MONTH:
                $result = Carbon::now ()->endOfMonth ();
                break;
            case self::START_OF_NEXT_2_MONTHS:
                $result = Carbon::now ()->startOfMonth ()->addMonths (2)->startOfMonth ();
                break;
            case self::START_OF_NEXT_3_MONTHS:
                $result = Carbon::now ()->startOfMonth ()->addMonths (3)->startOfMonth ();
                break;
            case self::START_OF_NEXT_6_MONTHS:
                $result = Carbon::now ()->startOfMonth ()->addMonths (6)->startOfMonth ();
                break;
            case self::START_OF_NEXT_12_MONTHS:
                $result = Carbon::now ()->startOfMonth ()->addMonths (12)->startOfMonth ();
                break;
            case self::START_OF_YEAR:
                $result = Carbon::now ()->startOfYear ();
                break;
            case self::START_OF_LAST_YEAR:
                $result = Carbon::now ()->startOfYear ()->addYears (-1)->startOfYear ();
                break;
            case self::END_OF_YEAR:
                $result = Carbon::now ()->endOfYear ();
                break;
        }
        if (is_null ($result))
        {
            $result = $dt_str;
        } else {
            if (is_null ($format))
            {
                $result = $result->toDateTimeString ();
            } else {
                $result = $result->format ($format);
            }
        }
        return $result;
    }


    /**
     * ?????????????????????
     *
     * @param array|null $message
     */
    protected function writeLogs (array $message = null)
    {
        $now = Carbon::now ();
        $this->writeSysLogs ([
            'schedule_id' => $this->scheduleLogId,
            'content' => implode ('; ', is_null ($message) ? $this->schedule_output : $message),
            'status' => $this->schedule_failed ? ScheduleLog::STATUS_SUCCESS : ScheduleLog::STATUS_FAILED,
            'updated_at' => $now,
            'created_at' => $now
        ]);
    }
}
