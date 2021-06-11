<?php

namespace Goodcatch\Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConfigsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sql = <<<EOL
INSERT INTO `configs` (`name`, `key`, `value`, `type`, `group`, `remark`, `created_at`, `updated_at`) VALUES
('Schedule类型', 'CORE_DICT_SCHEDULE_JOBS', '数据交换:App\\\\Modules\\\\Core\\\\Jobs\\\\ExchangeData,时间段数据交换:App\\\\Modules\\\\Core\\\\Jobs\\\\ExchangePeriodData,数据校验:App\\\\Modules\\\\Core\\\\Jobs\\\\DoubleCheckData', '1', '模块：基础数据', 'Module Core 基础数据模块计划任务模板列表', '2020-05-06 10:19:01', '2020-05-06 10:19:01'),
('Relation模型关联关系', 'CORE_DICT_MODEL_RELATIONS', '多对多（多态）:morphToMany,一对多（多态）:morphTo,一对一 (多态):morphOne,一对多（多态）:morphMany,远程一对一:hasOneThrough,一对一:hasOne,远程一对多:hasManyThrough,一对多:hasMany,多对多:belongsToMany,一对多 (反向):belongsTo', '1', '模块：基础数据', 'Module Core 动态设置模型关系', '2020-05-06 10:19:01', '2020-05-06 10:19:01'),
('数据路径同步表名前缀', 'DATA_EXCHANGE_DATA_ROUTE_PREFIX', 'sync_', '1', '模块：基础数据', 'Module Core 设置数据路径output表名的前缀 默认值 sync_', '2020-06-27 10:19:01', '2020-06-27 10:19:01');
EOL;
        //DB::unprepared($sql);
    }
}
