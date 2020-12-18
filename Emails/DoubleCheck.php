<?php

namespace Goodcatch\Modules\Core\Emails;

use Goodcatch\Modules\Core\Exports\CollectionsExport;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Arr;
use Maatwebsite\Excel\Facades\Excel;

class DoubleCheck extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    const COLLAPSE_HUGE_DATA                        = 'details';
    const COLLAPSE_HUGE_DATA_TABLE_ONLY             = 'details_table_only';
    const COLLAPSE_HUGE_DATA_1                      = 'attachment_1';
    const COLLAPSE_HUGE_DATA_2                      = 'attachment_2';
    const COLLAPSE_HUGE_DATA_3                      = 'attachment_3';

    public $result;

    /**
     * Create a new message instance.
     *
     * @param array $data
     *
     * @return void
     */
    public function __construct (array $data)
    {
        $this->result = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        $message = $this->from (
            config('mail.from.address'),
            config('mail.from.name')
        /*[
            'address' => 'department@xxx.com',
            'name' => '部门-' . config('app.name')
        ]*/
        )
            ->subject(Carbon::now ()->toDateString () . ' ' . Arr::get ($this->result, 'title', '来源与目标数据检查'))
            ->with ($this->result)
            ->markdown ('core::emails.verify.double')
        ;

        $collapse_data  = Arr::get ($this->result, self::COLLAPSE_HUGE_DATA, []);

        if (! empty ($collapse_data) && is_array ($collapse_data) && sizeof ($collapse_data) > 200)
        {
            $message->attachData(
                Excel::raw(new CollectionsExport ($collapse_data), \Maatwebsite\Excel\Excel::XLSX),
                Arr::get ($this->result, 'title', '来源与目标数据检查') . '.xlsx',
                [
                    'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                ]
            );
        }

        $collapse_data  = Arr::get ($this->result, self::COLLAPSE_HUGE_DATA_TABLE_ONLY, []);

        if (! empty ($collapse_data) && is_array ($collapse_data) && sizeof ($collapse_data) > 200)
        {
            $message->attachData(
                Excel::raw(new CollectionsExport ($collapse_data), \Maatwebsite\Excel\Excel::XLSX),
                Arr::get ($this->result, 'title', '数据表格') . '.xlsx',
                [
                    'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                ]
            );
        }

        $collapse_data  = Arr::get ($this->result, self::COLLAPSE_HUGE_DATA_1, []);
        if (! empty ($collapse_data) && is_array ($collapse_data) && sizeof ($collapse_data) > 0)
        {
            $message->attachData(
                Excel::raw(new CollectionsExport ($collapse_data), \Maatwebsite\Excel\Excel::XLSX),
                '附件【1】.xlsx',
                [
                    'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                ]
            );
        }

        $collapse_data  = Arr::get ($this->result, self::COLLAPSE_HUGE_DATA_2, []);
        if (! empty ($collapse_data) && is_array ($collapse_data) && sizeof ($collapse_data) > 0)
        {
            $message->attachData(
                Excel::raw(new CollectionsExport ($collapse_data), \Maatwebsite\Excel\Excel::XLSX),
                '附件【2】.xlsx',
                [
                    'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                ]
            );
        }


        $collapse_data  = Arr::get ($this->result, self::COLLAPSE_HUGE_DATA_3, []);
        if (! empty ($collapse_data) && is_array ($collapse_data) && sizeof ($collapse_data) > 0)
        {
            $message->attachData(
                Excel::raw(new CollectionsExport ($collapse_data), \Maatwebsite\Excel\Excel::XLSX),
                '附件【3】.xlsx',
                [
                    'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                ]
            );
        }

    }
}
