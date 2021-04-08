<?php

namespace Goodcatch\Modules\Core\Jobs;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Class ExchangePeriodData
 * Synchronizing data base on start-time and end-time
 *
 * @package Goodcatch\Modules\Core\Jobs
 */
class ExchangePeriodData extends ExchangeData
{

    const PERIOD_UNIT_MIN               = 'MIN';
    const PERIOD_UNIT_HOUR              = 'HOUR';
    const PERIOD_UNIT_DAY               = 'DAY';
    const PERIOD_UNIT_WEEK              = 'WEEK';
    const PERIOD_UNIT_MONTH             = 'MONTH';
    const PERIOD_UNIT_YEAR              = 'YEAR';

    const PERIOD                        = 'period';
    const PERIOD_START                  = 'start';
    const PERIOD_END                    = 'end';
    const PERIOD_POSITIVE               = 'positive';
    const PERIOD_COUNT                  = 'count';
    const PERIOD_UNIT                   = 'unit';

    const PERIOD_CHECK_POINT            = 'check_point';

    public $period;

    public $period_check_point = [];

    private $is_out_of_period = false;

    /**
     * Create a new job instance.
     *
     * @param $payload array job data including job configuration
     */
    public function __construct ($payload)
    {
        parent::__construct ($payload);

        $this->period = Arr::get ($this->source, self::PERIOD, []);

    }

    /**
     * overwrite handle here to keep query data till the end of period
     */
    public function handle ()
    {
        if (! empty ($this->period))
        {
            while (! $this->is_out_of_period)
            {
                if (! $this->beforeExchange ())
                {
                    break;
                }
            }
        } else {
            Log::info ("未设置时间区间");

            $this->jLog ("未设置时间区间", true);
        }

        // 写入日志
        $this->writeLogs ();
    }

    /**
     * overwrite pagination for period
     *
     *
     * @param $query \Illuminate\Database\Eloquent\Builder
     * @return mixed
     */
    protected function pagination ($query)
    {
        // 默认设置 xxxx-xx-xx 00:00:00
        $def_start_of_day = Carbon::today ()->startOfDay ()->toDateTimeString ();
        // 默认设置 xxxx-xx-xx 23:59:59
        $def_end_of_day = Carbon::today ()->endOfDay ()->toDateTimeString ();

        foreach ($this->period as $field => $options)
        {
            $dt_format = Arr::get ($options, self::CONDITION_WHERE_DATE_FORMATTER);

            if (! Arr::has ($this->period_check_point, $field))
            {
                // 按字段初始化
                Arr::set ($this->period_check_point, $field, []);

                $period_start_str = $this->transDT (Arr::get ($options, self::PERIOD_START, $def_start_of_day), $dt_format);
                $period_end_str = $this->transDT (Arr::get ($options, self::PERIOD_END, $def_end_of_day), $dt_format);

                Arr::set ($this->period_check_point, $field . '.' .self::PERIOD_START, $period_start_str);
                Arr::set ($this->period_check_point, $field . '.' .self::PERIOD_END, $period_end_str);

                $is_positive = Arr::get ($options, self::PERIOD_POSITIVE, true);

                Arr::set ($this->period_check_point, $field . '.' .self::PERIOD_POSITIVE, $is_positive);

                Arr::set (
                    $this->period_check_point,
                    $field . '.' .self::PERIOD_CHECK_POINT,
                    $is_positive ? $period_start_str : $period_end_str
                );

                // count
                Arr::set (
                    $this->period_check_point,
                    $field . '.' .self::PERIOD_COUNT,
                    Arr::get ($options, self::PERIOD_COUNT, 1)
                );
                // unit
                Arr::set (
                    $this->period_check_point,
                    $field . '.' .self::PERIOD_UNIT,
                    Arr::get ($options, self::PERIOD_UNIT, self::PERIOD_UNIT_DAY)
                );
            }

            $count = Arr::get ($this->period_check_point, $field . '.' .self::PERIOD_COUNT);
            $unit = Arr::get ($this->period_check_point, $field . '.' .self::PERIOD_UNIT);
            $positive = Arr::get ($this->period_check_point, $field . '.' .self::PERIOD_POSITIVE);
            $check_point_str = Arr::get ($this->period_check_point,$field . '.' .self::PERIOD_CHECK_POINT);
            $start_str = Carbon::parse (Arr::get ($this->period_check_point, $field . '.' .self::PERIOD_START));
            $end_str = Carbon::parse (Arr::get ($this->period_check_point, $field . '.' .self::PERIOD_END));

            try {
                $check_point = Carbon::parse ($check_point_str);
                $start = Carbon::parse ($start_str);
                $end = Carbon::parse ($end_str);
            } catch (\Exception $e) {
                $now = Carbon::now ();
                $check_point = $now;
                $start = $now;
                $end = $now;
            }

            $next_check_point = $this->getNextPeriod ($check_point->clone (), $unit, $count, $positive);


            $period_start = $positive ? $check_point : $next_check_point;

            if ($next_check_point->isBetween ($start, $end))
            {
                $period_end = $positive ? $next_check_point : $check_point;
            } else {
                $period_end = $positive ? $end : $start;
            }

            if ($start->notEqualTo ($end)
                && $start->isBefore ($end)
                && $period_start->isBetween ($start, $end)
                && $period_end->isBetween ($start, $end))
            {

                $query_start = is_null ($dt_format) ? $period_start->toDateTimeString () : $period_start->format ($dt_format);
                $query_end = is_null ($dt_format) ? $period_end->toDateTimeString () : $period_end->format ($dt_format);

                Log::info ("pagination 「{$start_str} - {$end_str}」: {$field} >= {$query_start} and {$field} < {$query_end}");

                $this->jLog ("分页设置 「{$start_str} - {$end_str}」: {$field} >= {$query_start} and {$field} < {$query_end}");

                $query
                    ->where ($field, '>=', $query_start)
                    ->where ($field, '<', $query_end);

            } else {

                $query
                    ->where ($field, '>', $next_check_point->toDateTimeString ())
                    ->where ($field, '<', $next_check_point->toDateTimeString ())
                    ->limit (0);

                Log::info ("out of range 「{$period_start} - {$period_end}」");

            }

            Arr::set (
                $this->period_check_point,
                $field . '.' .self::PERIOD_CHECK_POINT,
                $next_check_point->toDateTimeString ()
            );

            // 超过时间段则停止分页
            if (! $this->is_out_of_period && (
                $start->equalTo ($end)
                || $start->isAfter ($end)
                || $period_start->equalTo ($period_end)
                || ! $period_start->isBetween ($start, $end)
                || ! $period_end->isBetween ($start, $end)
                )
            )
            {
                $this->is_out_of_period = true;
            }
        }

        return $query;
    }



    /**
     * overwrite column mapping
     *
     * @param array $orig_data
     * @param array $mappings
     * @return array
     */
    protected function transform (array $orig_data, array $mappings)
    {
        foreach ($this->period as $field => $options)
        {
            $orig_data ["__{$field}"] = Arr::get ($options,self::PERIOD_CHECK_POINT);
        }

        return parent::transform ($orig_data, $mappings);
    }


    /**
     *
     *
     * @param $carbon
     * @param $unit
     * @param int $count
     * @param bool $positive
     * @return Carbon
     */
    private function getNextPeriod (Carbon $carbon, $unit, $count = 1, $positive = true)
    {
        $method = Arr::get ([
            self::PERIOD_UNIT_MIN => 'Minute',
            self::PERIOD_UNIT_HOUR => 'Hour',
            self::PERIOD_UNIT_DAY => 'Day',
            self::PERIOD_UNIT_WEEK => 'Week',
            self::PERIOD_UNIT_MONTH => 'Month',
            self::PERIOD_UNIT_YEAR => 'Year'
        ], $unit, '');

        if (! empty ($method))
        {
            call_user_func_array (array ($carbon, ($positive ? 'add' : 'sub') . Str::plural ($method)), [$count]);
        }
        return $carbon;
    }

    /**
     * transform time indication to formatted datetime string
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
            case self::END_OF_TODAY:
                $result = Carbon::today ()->addDays (1)->startOfDay ();
                break;
            case self::END_OF_YESTERDAY:
                $result = Carbon::today ()->startOfDay ();
                break;
            case self::END_OF_WEEK:
                $result = Carbon::now ()->addWeeks (1)->startOfWeek ();
                break;
            case self::END_OF_MONTH:
                $result = Carbon::now ()->startOfMonth ()->addMonths (1)->startOfMonth ();
                break;
            case self::END_OF_YEAR:
                $result = Carbon::now ()->startOfYear ()->addYears (1)->startOfYear ();
                break;
        }
        if (is_null ($result))
        {
            $result = parent::transDT ($dt_str, $format);
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
