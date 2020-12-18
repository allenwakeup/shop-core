<?php

namespace Goodcatch\Modules\Core\Jobs;

use Goodcatch\Modules\LightCms\Jobs\AbsRateLimitedGroup;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class ConfigurableJob extends AbsRateLimitedGroup
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
    public $scheduleLogType = 1;
    public $schedule_output = [];
    public $schedule_failed = false;

    /**
     * @var Carbon 任务生命周期时间点
     */
    public $time_elapsed;

    /**
     * 如果模型缺失即删除任务。
     *
     * @var bool
     */
    public $deleteWhenMissingModels = false;

    /**
     * 任务可以执行的最大秒数 (超时时间)。
     *
     * @var int
     */
    public $timeout = 120;

    /**
     * 任务可以尝试的最大次数。
     *
     * @var int
     */
    public $tries = 5;


    // protected $retry_until = 5;

    /*
     * 定义任务超时时间
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
     * Execute the job.
     * 监控料品是否更新，并采取措施
     * 例如与远端数据同步或者数据校验
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
            $schedule_logs = \App\Model\Admin\Log::query ()
                ->where ('user_id', $this->scheduleLogId)
                ->where ('type', $this->scheduleLogType)
                ->whereDate ('created_at', '>=', $run_if_previous_failure)
                ->whereDate ('created_at', '<', Carbon::now ()->toDateTimeString ())
                ->orderBy('created_at', 'desc')
                ->get ();
            if ($schedule_logs->count () === 0 || ($schedule_logs->count () > 0 && $schedule_logs->first ()->ua === 'true'))
            {
                if ($schedule_logs->count () === 0)
                {
                    Log::info ("{$run_if_previous_failure} 以来首次执行");

                    $this->jLog ("{$run_if_previous_failure} 以来首次执行");
                } else {

                    $previous_failure_at = $schedule_logs->first ()->created_at;

                    Log::info ('{$run_if_previous_failure}之后的最后一次任务执行失败的时间', [$previous_failure_at]);

                    $this->jLog ("{$run_if_previous_failure}之后的最后一次任务执行失败的时间 {$previous_failure_at}");
                }

                $expected = true;
            }
        }
        return $expected;
    }

    /**
     * 记录系统日志
     *
     * @param $message string 日志行
     * @param bool $failed 是否任务失败
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

        $this->schedule_output [] = $message . ' ， ' . $now->diffForHumans ($time_elapsed);

        $this->time_elapsed = $now->toDateTimeString ();

        if (! $this->schedule_failed && $failed)
        {
            $this->schedule_output [] = '任务失败';

            $this->schedule_failed = $failed;
            $this->writeLogs ($this->schedule_output);
        }
    }

    /**
     * 任务失败的处理过程
     *
     * @param  Exception  $exception
     * @return void
     */
    public function failed (Exception $exception)
    {
        Log::error ('Exception queue job: ' . $exception->getMessage (), $exception->getTrace ());
        // 给用户发送任务失败的通知，等等……

        // 这里写系统日志
        $this->schedule_failed = true;
        $this->writeLogs (['任务失败', $exception->getMessage ()]);
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
}
