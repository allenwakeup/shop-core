<?php

namespace Goodcatch\Modules\Core\Console;

use Goodcatch\Modules\Core\Model\Admin\Schedule;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;

class ScheduleVerifyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'core:verify-schedule {email : message to emails} {--date= : 指定日期}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '通过对比计划任务状态检查执行情况';


    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    public function handle ()
    {

        $opt_date = $this->option ('date');

        $address = load_dictionary (trim($this->argument('email')));

        $date = Carbon::now ()->subHours (24)->addMinutes (1);

        if (isset ($opt_date) && ! is_null($opt_date) && ! empty($opt_date))
        {
            $date = Carbon::parse ($opt_date);

            $this->info ("specific day $opt_date");
        }

        $schedules = Schedule::ofEnabled ()->ofJob ()->ofOrdered ()->with (['logs' => function ($query) use ($date) {
            $query->where ('type', 1)->whereDate ('created_at', $date)->orderBy ('created_at', 'desc');
        }])->get ();

        if ($schedules->count () > 0)
        {
            $logs_status = $schedules->reduce (function ($arr, $schedule) {
                $arr [$schedule->name] = ['true' => 0, 'false' => 0];
                if (! is_null ($schedule) && ! is_null ($schedule->logs))
                {
                    $arr [$schedule->name] = array_merge (
                        Arr::get ($arr, $schedule->name, []),
                        $schedule->logs->countBy (function ($log) {
                            return $log->ua;
                        })->all ()
                    );
                }
                return $arr;
            }, []);

            $message = (new \Goodcatch\Modules\Core\Emails\ScheduleVerify ([
                'run_at' => Carbon::now ()->toDateTimeString (),
                'schedules' => $schedules->all (),
                'logs_status' => $logs_status
            ]))->onQueue('emails');
            Mail::to(\collect($address)->pluck('code')->all())
                ->queue ($message);
        }

    }
}
