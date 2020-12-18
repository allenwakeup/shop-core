<?php

namespace Goodcatch\Modules\Core\Jobs;

use Goodcatch\Modules\Core\Model\Admin\Connection;
use Goodcatch\Modules\Core\Model\Admin\Eloquent;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Class SyncDataMappingData
 * Synchronize mapping data
 *
 * @package App\Jobs
 */
class SyncDataMappingData extends ExchangeData
{
    protected $sync;

    protected $left_id;
    protected $detach = false;

    protected $left_table_name;
    protected $right_table_name;

    protected $left_table;
    protected $right_table;

    protected $source_table;
    protected $table;
    protected $table_pivot;

    protected $title;
    protected $relationship;

    protected $parent_key;
    protected $related_key;

    protected $data_route;

    protected $data_route_table_from;
    protected $data_route_table_to;

    protected $target_connection;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct (array $sync)
    {
        $this->sync = $sync;

        $this->title = Arr::get ($this->sync, 'title', '');
        $this->relationship = Arr::get ($this->sync, 'relationship', '');

        // 选择的左表ID值
        $this->left_id = Arr::get ($this->sync, 'left_id', '');
        $this->detach = Arr::get ($this->sync, 'detach', false);

        $this->left_table_name = Arr::get ($this->sync, 'left', '');
        $this->right_table_name = Arr::get ($this->sync, 'right', '');

        $this->left_table = Str::lower (Arr::get ($this->sync, 'left_table', ''));
        $this->right_table = Str::lower (Arr::get ($this->sync, 'right_table', ''));

        $this->parent_key = Str::lower (Arr::get ($this->sync, 'parent_key', ''));
        $this->related_key = Str::lower (Arr::get ($this->sync, 'related_key', ''));

        $this->data_route = Arr::get ($this->sync, 'data_route', []);
        $this->data_route_table_from = Arr::get ($this->data_route, 'table_from', '');
        $this->data_route_table_to = Arr::get ($this->data_route, 'table_to', '');

        $this->table = Str::lower (Arr::get ($this->data_route, 'output', ''));
        $this->source_table = Str::lower (Arr::get ($this->sync, 'table', config ('core.data_mapping.' . $this->relationship . '.table', '')));
        $this->table_pivot = $this->table . '_pivot';

        $this->target_connection = Str::lower (Arr::get ($this->sync, 'connection'));



        parent::__construct ([
            'job' => [
                'config' => [
                    'deleteWhenMissingModels' => false,
                    'queue' => 'default',
                    'connection' => 'redis',
                    'timeout' => 360,
                    'tries' => 1,
                    'retry_until' => 5
                ]
            ]
        ]);
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

        if (strcmp ($this->left_table, $this->data_route_table_from) === 0 && strcmp ($this->right_table, $this->data_route_table_to) === 0)
        {
            // 从原表取出映射数据插入目标表
            $this->syncOutput ();
            // 从原表取出映射数据插入目标中间表
            $this->syncOutputPivot ();
        } else if (strcmp ($this->left_table, $this->data_route_table_from)  === 0)
        {
            // 从原表取出映射数据插入目标表
            $this->syncOutput ();
        } else if (strcmp ($this->right_table, $this->data_route_table_to)  === 0)
        {
            // 从原表取出映射数据插入目标中间表
            $this->syncOutputPivot ();
        }



    }

    /**
     * With help of this method, it builds different eloquent model for querying
     *
     * @param $inputOrOutput
     * @param string $directory
     * @return Eloquent
     */
    protected function makeQueryModel ($inputOrOutput, $directory = Connection::TYPE_SRC, $model = null)
    {
        $model = isset ($model) ? $model : new Eloquent ();


        if ($directory === Connection::TYPE_DST)
        {
            $connection_name = $directory . '_' . Arr::get ($inputOrOutput, self::CONNECTION, $model->getConnection ());

            if (! $this->checkConnection ($connection_name))
            {
                Log::warning ('no connection found for name ' . $connection_name);
                return null;
            }

            $model->setConnection ($connection_name);
        }

        $model->unguard ();
        return $model->setTable (Arr::get ($inputOrOutput, self::TABLE, $model->getTable ()))
            ->setKeyName (Arr::get ($inputOrOutput, self::PRIMARY_KEY, $model->getKeyName ()))
            ->setKeyType (Arr::get ($inputOrOutput, self::KEY_TYPE, $model->getKeyType ()))
            ->setIncrementing (Arr::get ($inputOrOutput, self::INCREMENTING, $model->getIncrementing ()))
            ->setUsesTimestamps (Arr::get ($inputOrOutput, self::USES_TIMESTAMPS, $model->usesTimestamps ()));
    }

    protected function syncOutput ()
    {
        // 重制分页
        $this->resetPage ();

        $data_mapping_config = config('core.data_mapping.' . $this->relationship, []);

        $config_left = Arr::get($data_mapping_config, 'left', 'left');
        $config_right = Arr::get($data_mapping_config, 'right', 'right');

        $config_left_id = Arr::get($data_mapping_config, 'left_id', 'left_id');
        $config_right_id = Arr::get($data_mapping_config, 'right_id', 'right_id');

        // 自动同步
        $this->loadDataSourceConfig([
            'source' => [
                'name' => '映射的输出表「' . $this->table . '」获取数据来源「' . $this->title . '」',
                'table' => $this->source_table,
                'primary_key' => $config_left_id,
                'key_type' => 'string',
                'incrementing' => false,
                'uses_timestamps' => false,
                'conditions' => [
                    'select' => implode (',', [
                        "{$this->source_table}.{$config_left_id}",
                        (strcmp ($this->right_table, $this->data_route_table_to) === 0)
                            ? "concat({$this->source_table}.{$config_left}_type, '.', {$this->source_table}.{$config_left_id}) as pivot_id"
                            : "concat({$this->source_table}.{$config_right}_type, '.', {$this->source_table}.{$config_right_id}) as pivot_id"
                    ]),
                    'group' => (
                        (strcmp ($this->right_table, $this->data_route_table_to) === 0)
                            ? [
                                "{$this->source_table}.{$config_left_id}",
                                "{$this->source_table}.{$config_left}_type"
                            ]
                            : [
                                "{$this->source_table}.{$config_left_id}",
                                "{$this->source_table}.{$config_right}_type",
                                "{$this->source_table}.{$config_right_id}",
                            ]
                    ),
                    'where' => [
                        [$this->source_table . '.' . $config_left . '_type', '=', $this->left_table],
                        [$this->source_table . '.' . $config_right . '_type', '=', $this->right_table],
                        [$this->source_table . '.' . $config_left_id, '=', $this->left_id]
                    ],
                    'join' => [
                        [$this->left_table, $config_left_id, '=', $this->parent_key]
                    ]
                ],
                'fields_mapping' => [
                    [
                        $config_left_id,
                        $this->left_table,
                    ],
                    [
                        'pivot_id',
                        'pivot_id'
                    ]
                ]
            ],
            'target' => [
                'name' => '获取「' . $this->title . '」数据并写入输出表「' . $this->table . '」',
                'connection' => $this->target_connection,
                'table' => $this->table,
                'primary_key' => $this->data_route_table_from,
                'key_type' => 'string',
                'incrementing' => false,
                'uses_timestamps' => false,
                'delete' => true,
                'conditions' => [
                    'where' =>[ [$this->data_route_table_from, '=', "{$this->data_route_table_from}." . $this->left_id] ]
                ],
                'unique' => [
                    [
                        $this->left_table,
                        $this->left_table,
                    ],
                    [
                        'pivot_id',
                        'pivot_id'
                    ]
                ]
            ]
        ]);

        if ($this->detach)
        {
            DB::transaction (function () {

                Log::info('清理删除的关联');

                $delete_res = $this->makeQueryModel($this->target, Connection::TYPE_DST)
                    ->newModelQuery()
                    ->where(function ($query) {
                        $query->where ($this->left_table, $this->left_id);
                        if (strcmp ($this->right_table, $this->data_route_table_to) === 0)
                        {
                            $query->where ('pivot_id', $this->left_table . '.' . $this->left_id);
                        } else {
                            $query->where ('pivot_id', 'like', $this->right_table . '.%');
                        }
                    })->delete();

                Log::info('已清理 ' . $delete_res);

            });
        }


        $this->exchange ();
    }

    protected function syncOutputPivot ()
    {

        // 重制分页
        $this->resetPage ();

        $data_mapping_config = config('core.data_mapping.' . $this->relationship, []);

        $config_left = Arr::get($data_mapping_config, 'left', 'left');
        $config_right = Arr::get($data_mapping_config, 'right', 'right');

        $config_left_id = Arr::get($data_mapping_config, 'left_id', 'left_id');
        $config_right_id = Arr::get($data_mapping_config, 'right_id', 'right_id');

        // 自动同步
        $this->loadDataSourceConfig([
            'source' => [
                'name' => '映射的输出表「' . $this->table_pivot . '」获取数据来源「' . $this->title . '」',
                'table' => $this->source_table,
                'primary_key' => $config_left_id,
                'key_type' => 'string',
                'incrementing' => false,
                'uses_timestamps' => false,
                'conditions' => [
                    'select' => implode (',', [
                        "{$this->source_table}.{$config_right_id}",
                        "concat({$this->source_table}.{$config_left}_type, '.', {$this->source_table}.{$config_left_id}) as pivot"
                    ]),
                    'group' => [
                        "{$this->source_table}.{$config_left_id}",
                        "{$this->source_table}.{$config_left}_type",
                        "{$this->source_table}.{$config_right_id}",
                    ],
                    'where' => [
                        [$this->source_table . '.' . $config_left . '_type', '=', $this->left_table],
                        [$this->source_table . '.' . $config_right . '_type', '=', $this->right_table],
                        [$this->source_table . '.' . $config_left_id, '=', $this->left_id]
                    ],
                    'join' => [
                        [$this->left_table, $config_left_id, '=', $this->parent_key]
                    ]
                ],
                'fields_mapping' => [
                    [
                        $config_right_id,
                        $this->right_table,
                    ],
                    [
                        'pivot',
                        'pivot',
                    ]
                ]
            ],
            'target' => [
                'name' => '获取「' . $this->title . '」数据并写入输出表「' . $this->table_pivot . '」',
                'table' => $this->table_pivot,
                'connection' => $this->target_connection,
                'primary_key' => $this->data_route_table_to,
                'key_type' => 'string',
                'incrementing' => false,
                'uses_timestamps' => false,
                'delete' => true,
                'conditions' => [
                    'where' => [ ['pivot', '=', "{$this->data_route_table_from}." . $this->left_id] ]
                ],
                'unique' => [
                    [
                        'pivot',
                        'pivot'
                    ],
                    [
                        $this->right_table,
                        $this->right_table
                    ]
                ]
            ]
        ]);


        if ($this->detach)
        {
            DB::transaction (function () {

                Log::info('清理删除的关联');

                $delete_res = $this->makeQueryModel($this->target, Connection::TYPE_DST)
                    ->newModelQuery()
                    ->where(function ($query) {
                        if (strcmp ($this->right_table, $this->data_route_table_to) === 0)
                        {
                            $query->where ('pivot', $this->left_table . '.' . $this->left_id);
                        } else {
                            $query->where ('pivot', 'like', $this->left_table . '.%');
                        }
                    })->delete();

                Log::info('已清理 ' . $delete_res);

            });
        }

        $this->exchange ();
    }

    /**
     * 重置分页设置
     */
    protected function resetPage ()
    {
        $this->page = 1;
    }

}
