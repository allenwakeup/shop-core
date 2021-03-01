<?php

namespace Goodcatch\Modules\Core\Http\Requests\Admin;

use Goodcatch\Modules\Core\Http\Requests\BaseRequest as FormRequest;
use Goodcatch\Modules\Core\Model\Admin\Schedule;
use Goodcatch\Modules\Core\Rules\Cron;
use Illuminate\Validation\Rule;

class ScheduleRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules ()
    {
        return [
            'name' => ['required', 'max:200', $this->uniqueOrExists (Schedule::class, 'name') . ':core_schedules'],
            'input' => 'required|max:500',
            'cron' => ['required', new Cron ()],
            'ping_before' => 'max:500',
            'ping_success' => 'max:500',
            'ping_failure' => 'max:500',
            'payload' => ['required_if:type,' . Schedule::TYPE_JOB, 'json'],
            'description' => 'max:255',
            'overlapping' => [
                Rule::in ([Schedule::OVER_LAPPING_ENABLE, Schedule::OVER_LAPPING_DISABLE])
            ],
            'one_server' => [
                Rule::in ([Schedule::ONE_SERVER_ENABLE, Schedule::ONE_SERVER_DISABLE])
            ],
            'background' => [
                Rule::in ([Schedule::BACKGROUND_ENABLE, Schedule::BACKGROUND_DISABLE])
            ],
            'maintenance' => [
                Rule::in ([Schedule::MAINTENANCE_ENABLE, Schedule::MAINTENANCE_DISABLE])
            ],
            'schedule_type' => [
                'required',
                Rule::in ([Schedule::TYPE_JOB, Schedule::TYPE_EXEC, Schedule::TYPE_COMMAND])
            ],
            'once' => [
                'required',
                Rule::in ([Schedule::ONCE_DISABLE, Schedule::ONCE_ENABLE])
            ],
            'group' => 'max:20',
            'order' => 'integer',
            'status' => [
                'required',
                Rule::in ([Schedule::STATUS_DISABLE, Schedule::STATUS_ENABLE])
            ]
        ];
    }
}
