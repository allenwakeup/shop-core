<?php

namespace Goodcatch\Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ScheduleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
$sql = <<<EOL
INSERT INTO `core_schedules` (id, name, input, cron, description, schedule_type, overlapping, one_server, background, maintenance, once, `group`, `order`, `status`, created_at, updated_at) VALUES
(1,'「系统」刷新系统配置缓存','config:cache','* * * * *','Flush cache, DO NOT change it!',1,0,0,0,0,1,'系统',0,0,'2020-06-16 16:03:26','2020-06-16 16:03:26'),
(2,'「系统」重启队列','queue:restart','* * * * *','Restart queue, DO NOT change it!',1,0,0,0,0,1,'系统',0,0,'2020-06-16 16:03:26','2020-06-16 16:03:26');
EOL;
        DB::unprepared($sql);
    }
}
