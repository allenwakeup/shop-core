<?php

namespace Goodcatch\Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DataRouteTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sql = <<<EOL
INSERT INTO `core_data_routes` (`id`, `name`, `alias`, `short`, `description`, `from`, `table_from`, `to`, `table_to`, `output`, `created_at`, `updated_at`) VALUES
(1, '数据集用户与客户', '用户与客户', '用户与客户', '用户与客户的数据集，其中包括用户对应客户，用户对应部门，用户对应客户分组，部门对应客户，分组对应客户。它们的并集就是用户与客户的数据集', '用户', 'users', '客户', 'core_clients' ,'users_clients', '2020-05-06 10:19:01', '2020-05-06 10:19:01');
EOL;
        DB::unprepared($sql);
    }
}
