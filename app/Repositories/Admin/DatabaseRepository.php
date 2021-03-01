<?php
/**
 * @author  Allen <ali@goodcatch.cn>
 */

namespace Goodcatch\Modules\Core\Repositories\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DatabaseRepository extends BaseRepository
{
    public static function listTable ($perPage, $page, $keyword = null)
    {
        $data = \collect (DB::select ('select * from information_schema.tables where  table_type = \'BASE TABLE\''))
            ->reduce (function ($arr, $table) use ($keyword) {

                $item = [
                    'id' => $table->TABLE_NAME,
                    'value' => $table->TABLE_NAME,
                    'name' => $table->TABLE_NAME,
                    'title' => $table->TABLE_NAME,
                    'rows' => $table->TABLE_ROWS,
                    'schema' => $table->TABLE_SCHEMA,
                ];

                if (! empty ($keyword))
                {
                    if (Str::contains ($table->TABLE_NAME, $keyword))
                    {
                        $arr->add ($item);
                    }
                } else {
                    $arr->add ($item);
                }

                return $arr;
                }, \collect ([]));

        return [
            'code' => 0,
            'msg' => '',
            'count' => $data->count (),
            'data' => $data->slice ($page * $perPage, $perPage)->values ()->all (),
        ];
    }
    public static function listColumn ($perPage, $page, $table, $keyword = null)
    {
        $data = \collect (DB::select ("SHOW COLUMNS FROM `{$table}`;"))
        ->filter (function ($item) use ($keyword) {
            if (! empty ($keyword))
            {
                if (Str::contains($item->Field, $keyword))
                {
                    return $item;
                }
            } else {
                return $item;
            }

        });

        return [
            'code' => 0,
            'msg' => '',
            'count' => $data->count (),
            'data' => $data->slice ($page * $perPage, $perPage)->values ()->all (),
        ];
    }
}
