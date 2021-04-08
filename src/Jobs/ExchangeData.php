<?php

namespace Goodcatch\Modules\Core\Jobs;

use Goodcatch\Modules\Core\Model\Admin\Connection;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\ForwardsCalls;
use Illuminate\Validation\Validator;

class ExchangeData extends ConfigurableJob
{

    use ForwardsCalls;

    /**
     * Constants for job payload configuration
     */
    const TARGET                            = 'target';
    const SOURCE                            = 'source';

    const JOB_NAME                          = 'name';
    const TYPE                              = 'type';
    const CONNECTION                        = 'connection';
    const TABLE                             = 'table';
    const PRIMARY_KEY                       = 'primary_key';
    const KEY_TYPE                          = 'key_type';
    const INCREMENTING                      = 'incrementing';
    const USES_TIMESTAMPS                   = 'uses_timestamps';
    const CONDITIONS                        = 'conditions';
    const CONDITION_SELECT                  = 'select';
    const CONDITION_GROUP                   = 'group';
    const CONDITION_JOIN                    = 'join';
    const CONDITION_LEFT_JOIN               = 'leftJoin';
    const CONDITION_RIGHT_JOIN              = 'rightJoin';
    const CONDITION_WHERE                   = 'where';
    const CONDITION_VALUE                   = 'value';
    const CONDITION_WHERE_NOT_NULL          = 'whereNotNull';
    const CONDITION_WHERE_NULL              = 'whereNull';
    const CONDITION_WHERE_METHOD            = 'method';
    const CONDITION_WHERE_DATE_FORMATTER    = 'format';
    const FIELDS_MAPPING                    = 'fields_mapping';
    const UNIQUE                            = 'unique';
    const DATA_CLEAN                        = 'clean';
    const DATA_CLEAN_WHERE                  = self::CONDITION_WHERE;
    const DATA_CLEAN_CASE                   = 'case';
    const DATA_CLEAN_CASE_WHEN              = 'when';
    const DATA_CLEAN_CASE_WHEN_RULES        = 'rules';
    const DATA_CLEAN_CASE_WHEN_THEN         = 'then';
    const DATA_CLEAN_CASE_ELSE              = 'else';
    const DATA_CLEAN_MAX                    = 'max';
    const DELETE                            = 'delete';





    /**
     * @var $source array 配置数据同步的数据源来源
     */
    protected $source;

    /**
     * @var $target array 配置数据同步的数据源目标
     */
    protected $target;

    /**
     * @var $validator Validator
     */
    protected $validator;

    protected $page = 1;

    protected $per_page = 10000;

    /**
     * @var bool 只清理一次
     */
    protected $deleted = false;

    /**
     * @var int $clean_max variable for cleaning
     */
    protected $clean_max = -1;


    /**
     * Create a new job instance.
     *
     * @param $payload array job data including job configuration
     */
    public function __construct ($payload)
    {
        parent::__construct ($payload);

        // Log::debug ('payload ', $payload);

        $this->loadDataSourceConfig ($payload);
    }

    /**
     * configuration for input and output
     *
     * @param $payload
     * @return $this
     */
    protected function loadDataSourceConfig ($payload)
    {
        $this->source = Arr::get ($payload, self::SOURCE, []);
        $this->target = Arr::get ($payload, self::TARGET, []);
        return $this;
    }

    /**
     * 数据交换
     * 'source'=>[
     *      'name'=>'do input data table exchange'
     *      'connection'=>'mysql',
     *      'table'=>'client',
     *      'primary_key'=>'id',
     *      'key_type'=>'int',
     *      'incrementing'=>true,
     *      'uses_timestamps'=>true,
     *
     *      'conditions'=>[
     *          'group'=>['name'],
     *          'select'=>['client.id', 'client.name', 'join_table.id as join_id'],
     *          'join'=>[
     *              ['join_table', 'client.id', '=', 'join_table.id']
     *          ],
     *          'where'=>[
     *              ['status', '=', '1']
     *          ]
     *      ],
     *      'fields_mapping'=>[
     *          ['name', 'UserName', 'trim|substr:0,2']
     *      ],
     *      'unique'=>[
     *          ['name', 'trim|substr'],
     *          ['department', 'trim|substr'],
     *          'md5',
     *      ]
     * ]
     * 'target'=>[
     *      'name'=>'do output data table exchange'
     *      'connection'=>'mysql',
     *      'table'=>'client',
     *      'primary_key'=>'id',
     *      'key_type'=>'int',
     *      'incrementing'=>true,
     *      'uses_timestamps'=>true,
     *      'delete' => false,
     *
     *      'unique'=>[
     *          ['unique:name-department', 'id'],
     *      ]
     * ]
     *
     */
    public function handle ()
    {
        $this->beforeExchange ();

        // 写入日志
        $this->writeLogs ();

    }

    protected function beforeExchange ()
    {
        if (empty ($this->run_if_previous_failure))
        {
            return $this->exchange ();
        } else {

            if ($this->hasPreviousFailure ())
            {
                return $this->exchange ();
            } else {

                Log::info ('在指定的时间内，任务执行记录中最后一次的状态是失败才执行', [$this->run_if_previous_failure]);

                $this->jLog ("在指定的时间（{$this->run_if_previous_failure}）内，任务执行记录中最后一次的状态是失败才执行");

            }
        }
        return false;
    }

    /**
     * do data exchange
     *
     * @return bool
     */
    protected function exchange ()
    {
        Log::info ('数据交换', [get_class ($this), Arr::get ($this->source, self::JOB_NAME, '未知名称')]);

        if (! isset ($this->source) || ! isset ($this->target))
        {
            $this->jLog ("缺少数据源设置，可能是没有设置", true);

            Log::info ('required configurations for both source and target');

            return false;
        }

        // 根据设置构建指定的模型用于查询
        $source = $this->makeQueryModel ($this->source);
        $test_target = $this->makeQueryModel ($this->target, Connection::TYPE_DST);
        if (! isset ($source) || ! isset ($test_target))
        {
            Log::info ('required connections for both source and target');

            $this->jLog ("缺少数据源的连接，可能是没有设置，或者被更改了", true);

            return false;
        }

        // 根据设置中的条件使用查询构建器查询数据
        $source_data = $this->load ($source, $this->source);

        $source_data_size = $source_data->count ();

        Log::info ("source size", [$source_data_size]);

        $this->jLog ("来源数据量 {$source_data_size}");


        if ($source_data_size > 0)
        {

            $unique_method = null;
            // 根据设置构建唯一键
            $source_unique = \collect (Arr::get ($this->source, self::UNIQUE, []))
                ->reduce (function ($arr, $unique) use (&$unique_method) {

                    if (is_array ($unique))
                    {
                        $unique_len = count ($unique);
                        $arr_len = count ($arr);
                        if ($unique_len === 1)
                        {
                            $arr [] = [$unique [0], $arr_len];
                        } else if ($unique_len === 2)
                        {
                            $arr [] = [$unique [0], $arr_len, $unique [1]];
                        }
                    } else if (! empty ($unique)) {
                        $unique_method = $unique; // 如果有，设置唯一键的生成方法
                    }

                    return $arr;
                }, []);

            Log::debug ('>>>输入表唯一键设置', [$source_unique]);


            // 读取输出表唯一键的设置
            $target_unique = Arr::get ($this->target, self::UNIQUE, []);

            Log::debug ('>>>输出表唯一键设置', [$target_unique]);

            // unique:field1-field2
            $source_unique_arr_key = empty ($source_unique) ?
                '' : 'unique:' . implode ('-',
                    \collect ($source_unique)
                        ->reduce (function ($arr, $unique) {
                            $arr [] = $unique [0];
                            return $arr;
                        }, []
                        ));

            Log::debug ('>>>输出表唯一键', [$source_unique_arr_key]);

            // 读取输入表字段映射
            $source_mapping = Arr::get ($this->source, self::FIELDS_MAPPING, []);

            // 读取源表数据，根据映射，构建中间数据，包含唯一键数据
            $source_data_transform = $source_data
                ->map (function ($item) use ($source_mapping, $source_unique, $unique_method, $source_unique_arr_key) {
                    $model_data = $item->toArray ();
                    $source_trans_data = $this->transform ($model_data, $source_mapping);
                    $unique_data = implode ('', \collect ($this->transform ($model_data, $source_unique))->values ()->all ());

                    if (isset ($unique_method) && $this->allowedUniqueMethod ($unique_method))
                    {
                        Log::debug ('>>>输出表唯一键的值 before', [$unique_data]);
                        $unique_data = call_user_func ($unique_method, $unique_data);
                        Log::debug ('>>>输出表唯一键的值 after', [$unique_data]);
                    }
                    if (! empty ($source_unique_arr_key))
                    {
                        $source_trans_data [$source_unique_arr_key] = $unique_data;
                    }
                    return $source_trans_data;
                });

            unset ($source_data);

            Log::debug ('>>>输入表构建数据', [count ($source_data_transform)]);

            // 构建待写入输出表的操作数据
            // 对于每一个输入表行数据，如果有设置唯一键，构建两个操作值；如果没有唯一键，只构建一个操作值
            $source_data_transaction = $source_data_transform
                ->reduce (function ($arr, $updateOrCreate) use ($target_unique) {

                    if (empty ($target_unique))
                    {
                        $arr [] = [$updateOrCreate];
                    } else if (is_array ($target_unique)) {
                        // 构建两个操作值，第一个将用于检查唯一键，存在则更新，不存在则插入新的数据
                        $target_unique_data = \collect ($target_unique)
                            ->reduce (function ($arr, $t_uq) use (&$updateOrCreate) {
                                if (is_array ($t_uq))
                                {
                                    list($source_key, $target_key) = array_merge ($t_uq, [null, null]);
                                    $target_value = Arr::get ($updateOrCreate, $source_key);
                                    Arr::forget ($updateOrCreate, $source_key);
                                    $arr [$target_key] = $target_value;
                                }
                                return $arr;
                            }, []);

                        $arr [] = [$target_unique_data, array_diff_key ($updateOrCreate, $target_unique_data)];

                        // Log::debug ('操作数据', [$arr [count ($arr) - 1]]);
                    }
                    return $arr;
                }, []);

            Log::debug ('>>>输入表构建操作数据', [count ($source_data_transaction)]);

            $result = 0;
            // 使用事务控制对数据进行的写入
            DB::transaction (function () use (&$result, $source_data_transaction) {

                $count_source_data_transaction = count ($source_data_transaction);

                $this->jLog ("进入事务控制，数据包数量：$count_source_data_transaction");

                if (Arr::get ($this->target, self::DELETE, false))
                {
                    Log::info ('写入前先清理');

                    if (! $this->deleted)
                    {
                        $delete_res = $this->makeQueryModel ($this->target, Connection::TYPE_DST)
                            ->newModelQuery ()
                            ->where (function ($query) {
                                $this->buildQuery ($query, self::CONDITION_WHERE, Arr::get ($this->target, self::CONDITIONS . '.' . self::CONDITION_WHERE, []));
                            })->delete ();

                        $this->deleted = true;

                        Log::info ('已清理', [$delete_res]);

                        $this->jLog ("写入前清理了 $delete_res 条数据");

                    }


                }

                $count_clean = 0;

                foreach ($source_data_transaction as $idx => $args)
                {
                    $target = $this->makeQueryModel ($this->target, Connection::TYPE_DST);

                    $target_queryable = $target->newModelQuery ();

                    $instance = null;
                    $count_args = count ($args);
                    // 两个操作值，先查询后更新或插入
                    if ($count_args > 1)
                    {
                        // 查询
                        if (! is_null ($instance = $target_queryable->where ($args [0])->first ()))
                        {
                            $instance = $this->makeQueryModel ($this->target, Connection::TYPE_DST, $instance);
                            $fill_data = $args [1];
                        } else {
                            $instance = $target;
                            $fill_data = $args [0] + $args [1];
                        }
                    } else if ($count_args === 1) {
                        $instance = $target;
                        $fill_data = $args [0];
                    }
                    // 执行操作
                    if (isset ($instance))
                    {
                        // Log::debug ('更新数据', [$count_args, $result, $instance->toArray (), $fill_data]);

                        if ($instance->fill ($fill_data)->save ())
                        {
                            $result ++;

                            if (! empty (Arr::get ($this->target, self::DATA_CLEAN, [])))
                            {
                                $clean_instance = $target_queryable->where ($fill_data)->first ();

                                if (! is_null ($clean_instance))
                                {
                                    $clean_data = $this->clean ($clean_instance, Arr::get ($this->target, self::DATA_CLEAN, []));

                                    if (! empty ($clean_data))
                                    {
                                        if ($clean_instance->fill ($clean_data)->save ())
                                        {
                                            $count_clean ++;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                if ($count_clean > 0)
                {
                    Log::info ('当前清洗数据', [$count_clean]);

                    $this->jLog ("清洗了 {$count_clean} 条数据");
                }
            }, 2);

            unset ($source_data_transaction);

            Log::info ('当前已同步数据', [$result]);

            $this->jLog ("已同步 {$result} 条数据");

            // 继续处理下一页
            if ($this->exchange ())
            {
                Log::info ('[done]');
            }
        }

        return true;
    }

    /**
     * query data with given configuration
     *
     * @param $model \Illuminate\Database\Eloquent\Model
     * @param $database
     * @return mixed
     */
    protected function load (\Illuminate\Database\Eloquent\Model $model, array $database)
    {
        $conditions = Arr::get ($database, self::CONDITIONS, []);
        $query = $model->newModelQuery ();
        if (! empty ($conditions))
        {
            $this->buildQueryJoin ($query, Arr::get ($conditions, self::CONDITION_JOIN, []));
            $this->buildQueryJoin ($query, Arr::get ($conditions, self::CONDITION_LEFT_JOIN, []), 'leftJoin');
            $this->buildQueryJoin ($query, Arr::get ($conditions, self::CONDITION_RIGHT_JOIN, []), 'rightJoin');
            $this->buildQuery ($query, self::CONDITION_WHERE, Arr::get ($conditions, self::CONDITION_WHERE, []));
            $this->buildQuery ($query, self::CONDITION_WHERE_NULL, Arr::get ($conditions, self::CONDITION_WHERE_NULL, []));
            $this->buildQuery ($query, self::CONDITION_WHERE_NOT_NULL, Arr::get ($conditions, self::CONDITION_WHERE_NOT_NULL, []));
            $group = Arr::get ($conditions, self::CONDITION_GROUP, []);
            $select = Arr::get ($conditions, self::CONDITION_SELECT, []);
            if (empty ($select))
            {
                if (! empty ($group))
                {
                    $query->select ($group)->groupBy ($group);
                }
            } else {

                if (is_array ($select))
                {
                    $query->select ($select);
                } else {
                    $query->select (DB::raw($select));
                }

                if (! empty ($group))
                {
                    if (is_array ($group))
                    {
                        $query->groupBy ($group);
                    } else {
                        $query->groupBy (DB::raw ($group));
                    }
                }
            }
        }
        return $this->pagination ($query)->get ();
    }

    /**
     * handle pagination
     *
     * Note: Please Don't Use OFFSET and LIMIT from huge number of records For Pagination in the future
     * Note: Use where condition and limit instead
     *
     * @param $query \Illuminate\Database\Eloquent\Builder
     * @return mixed
     */
    protected function pagination ($query)
    {
        Log::info ("pagination {$this->page} / {$this->per_page}");

        $this->jLog ("分页设置（{$this->page} / {$this->per_page}）");

        return $query->forPage ($this->page ++, $this->per_page);
    }

    /**
     * do column mapping
     *
     * @param array $orig_data
     * @param array $mappings
     * @return array
     */
    protected function transform (array $orig_data, array $mappings)
    {
        $data = [];
        foreach ($mappings as $idx => $mapping)
        {
            if (is_array ($mapping))
            {
                list($source, $target, $convert) = array_merge ($mapping, [null, null, null]);
                $data [$target] = Arr::get ($orig_data, $source);
                if (! empty ($convert))
                {
                    foreach (explode ('|', $convert) as $method)
                    {
                        $data [$target] = transform_in_rules ($method, $data [$target], $data);
                    }
                }
            }
        }
        return sizeof ($data) === 0 ? $orig_data : $data;
    }

    /**
     * do data cleaning during target exchanging.
     * Just like CASE WHEN
     * configuration E.g.
     * 'field_name'=> [
     *      'where' => ['field name', 'str:upper|append:123'],
     *      'where' => 4,
     *      'where' => 'value',
     *      'case' => [
     *          'when' => [
     *              [
     *                  'rules' => [
     *                      'field name' => 'between:30,40'
     *                  ],
     *                  'then' => 1
     *              ],
     *              [
     *                  'rules' => [
     *                      'field name' => 'between:60,80'
     *                  ],
     *                  'then' => 1
     *              ],
     *          ],
     *          'else' => ['field name', 'str:upper|append:123'],
     *          'else' => '3',
     *      ],
     *  ]
     *
     */
    protected function clean (\Illuminate\Database\Eloquent\Model $eloquent, array $options)
    {
        $res = [];
        if (isset ($eloquent) && ! empty ($options))
        {
            $data = $eloquent->toArray ();

            foreach ($options as $field => $case_when)
            {
                if (!empty ($case_when) && is_array ($case_when))
                {
                    $where = Arr::get ($case_when, self::DATA_CLEAN_WHERE, []);

                    if (! empty ($where))
                    {
                        if (is_array ($where))
                        {
                            list($target, $convert) = array_merge ($where, [null, null]);
                            if (! empty ($convert))
                            {

                                $where = $data [$target];
                                foreach (explode ('|', $convert) as $method)
                                {
                                    $where = transform_in_rules ($method, $where);
                                }
                            }
                        }
                    }

                    // 跳过清洗
                    if (strcmp ("{$data [$field]}", "{$where}") !== 0)
                    {
                        continue;
                    }

                    $case = Arr::get ($case_when, self::DATA_CLEAN_CASE, []);

                    if (! empty ($case) && is_array ($case))
                    {
                        $when = Arr::get ($case, self::DATA_CLEAN_CASE_WHEN, []);

                        $is_else = false;
                        if (! empty ($when) && is_array ($when))
                        {
                            if (is_null ($this->validator))
                            {
                                try {
                                    $this->getValidatorInstance ();
                                } catch (BindingResolutionException $exception) {
                                    Log::error ("Validator not found!");
                                    break;
                                }
                            }

                            // 如果满足when的条件，取then的值
                            foreach ($when as $w_idx => $case_then)
                            {
                                if (! empty ($case_then) && is_array ($case_then))
                                {
                                    $this->validator->setRules (Arr::get ($case_then, self::DATA_CLEAN_CASE_WHEN_RULES, $this->validator->getRules ()));
                                    $this->validator->setData ($data);

                                    if ($is_else = $this->validator->passes ())
                                    {
                                        $res [$field] = Arr::get ($case_then, self::DATA_CLEAN_CASE_WHEN_THEN, $data [$field]);
                                        break;
                                    }
                                }
                            }

                        }

                        // 其他条件的值
                        if (! $is_else)
                        {
                            $else = Arr::get ($case, self::DATA_CLEAN_CASE_ELSE, []);

                            if (! empty ($else))
                            {
                                if (is_array ($else))
                                {
                                    list($target, $convert) = array_merge ($else, [null, null]);
                                    if (! empty ($target))
                                    {
                                        $else = $data [$target];
                                        if (! empty ($convert)) {
                                            foreach (explode('|', $convert) as $method) {
                                                $else = transform_in_rules($method, $else);
                                            }
                                        }
                                    }
                                }
                                $res [$field] = $else;
                            }
                        }
                    }

                    $max = Arr::get ($case_when, self::DATA_CLEAN_MAX, 0);

                    if ($max > 0)
                    {
                        if ($this->clean_max < 0)
                        {
                            $this->clean_max = $eloquent->newModelQuery ()->max ($field);
                        }

                        $res [$field] = $this->clean_max = $this->clean_max + $max;
                    }
                }
            }
        }
        return $res;
    }

    /**
     * generate criteria based on configuration
     *
     * @param $query
     * @param $method
     * @param $options
     * @return mixed
     */
    protected function buildQuery ($query, $method, $options)
    {
        foreach ($options as $condition)
        {
            if (is_array ($condition))
            {

                foreach ($condition as $idx => $arg)
                {
                    if (is_array ($arg))
                    {
                        $method = Arr::get ($arg, self::CONDITION_WHERE_METHOD, 'whereDate');
                        $condition [$idx] = $this->transDT (
                            Arr::get ($arg, self::CONDITION_VALUE),
                            Arr::get ($arg, self::CONDITION_WHERE_DATE_FORMATTER, 'Y-m-d H:i:s')
                        );
                    }
                }

                $this->forwardCallTo ($query, $method, $condition);
            }
        }
        return $this;

    }

    /**
     * generate criteria based on configuration
     *
     * @param $query
     * @param $options
     * @param $opera
     * @return mixed
     */
    protected function buildQueryJoin ($query, array $options, $opera = 'join')
    {
        if (count ($options) > 0)
        {
            $model_table = $query->getModel ()->getTable ();

            foreach ($options as $idx => $conditions)
            {
                if (is_array ($conditions))
                {
                    list($table, $left, $operator, $right) = array_merge ($conditions, [null, null, null, null]);
                    if (! empty ($table) && ! empty ($left) && ! empty ($operator) && ! empty ($right))
                    {
                        $left_field = Str::contains ($left, '.') ? $left : $model_table . '.' . $left;
                        $right_field = Str::contains ($right, '.') ? $right : $table . '.' . $right;
                        $this->forwardCallTo ($query, $opera, [$table, $left_field, $operator, $right_field]);
                    }
                }
            }
        }
        return $this;
    }


    /**
     * With help of this method, it builds different eloquent model for querying
     *
     * @param $inputOrOutput
     * @param string $directory
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function makeQueryModel ($inputOrOutput, $directory = Connection::TYPE_SRC, $model = null)
    {
        $type = 'Goodcatch\\Modules\\Core\\Model\\Admin\\' . Arr::get ($inputOrOutput, self::TYPE, 'Eloquent');

        $model = isset ($model) ? $model : new $type;

        $connection_name = $directory . '_' . Arr::get ($inputOrOutput, self::CONNECTION, $model->getConnection ());
        if (! $this->checkConnection ($connection_name))
        {
            Log::warning ('no connection found for name ' . $connection_name);
            return null;
        }

        $model->unguard ();
        return $model->setConnection ($connection_name)
            ->setTable (Arr::get ($inputOrOutput, self::TABLE, $model->getTable ()))
            ->setKeyName (Arr::get ($inputOrOutput, self::PRIMARY_KEY, $model->getKeyName ()))
            ->setKeyType (Arr::get ($inputOrOutput, self::KEY_TYPE, $model->getKeyType ()))
            ->setIncrementing (Arr::get ($inputOrOutput, self::INCREMENTING, $model->getIncrementing ()))
            ->setUsesTimestamps (Arr::get ($inputOrOutput, self::USES_TIMESTAMPS, $model->usesTimestamps ()));
    }

    /**
     * allow methods to generate unique id
     *
     * @param $method
     * @return bool
     */
    protected function allowedUniqueMethod ($method)
    {
        return in_array ($method, ['md5']);
    }


    /**
     * check out if there is connection name available
     *
     * @param $name
     * @return bool
     */
    protected function checkConnection ($name)
    {
        $connections = Cache::get (config ('modules.cache.key') . '.core.connections', []);

        return count ($connections) > 0 && \collect ($connections)->values ()->filter (function ($conn) use ($name) {
                return $name === $conn ['type'] . '_' . $conn ['name'];
            })->count ();

    }

    /**
     * Get the validator instance for the request.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function getValidatorInstance ()
    {
        if ($this->validator) {
            return $this->validator;
        }

        $this->validator = app ('validator')->make ([], [], [], []);

        return $this->validator;
    }

}
