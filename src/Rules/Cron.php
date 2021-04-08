<?php

namespace Goodcatch\Modules\Core\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;

class Cron implements Rule
{
    /**
     * @var string $key indicates that identification for one record
     */
    protected $key;

    /**
     * @var string $date the date that pass to client during Api communication
     */
    protected $date;

    /**
     * Create a new rule instance.
     */
    public function __construct ()
    {

    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes ($attribute, $value)
    {
        if (isset ($value) && ! empty ($value) && ! Str::contains($value, ['-', ',']))
        {
            $sections = explode (' ', trim ($value));
            if (count ($sections) === 5 && $sections !== '* * * * *')
            {
                list ($minute, $hour, $day, $week, $month) = $sections;

                $minute = Str::replaceFirst ('*/', '', $minute);
                $hour = Str::replaceFirst ('*/', '', $hour);
                $day = Str::replaceFirst ('*/', '', $day);
                $week = Str::replaceFirst ('*/', '', $week);
                $month = Str::replaceFirst ('*/', '', $month);

                if (($minute === '*' || (((int) $minute >= 0 && (int) $minute <= 59)))
                    && ($hour === '*' || ((int) $hour >= 0 && (int) $hour <= 23))
                    && ($day === '*' || ((int) $day >= 0 && (int) $day <= 30))
                    && ($week === '*' || ((int) $week >= 1 && (int) $week <= 7))
                    && ($month === '*' || ((int) $month >= 1 && (int) $month <= 12))) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message ()
    {
        return '定时器校验失败. 格式 * * * * *  允许范围 0-59 0-23 0-30 1-7 1-12';
    }
}
