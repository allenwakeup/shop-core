<?php

namespace Goodcatch\Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AreaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sql = <<<EOL
INSERT INTO `core_areas` VALUES
(1, '430321000000', '湘潭总部', '总部', '总部', '总部', '总部就在湘潭', '2019-02-28 12:50:35','2019-03-07 16:52:51'),
(2, '430321000000', '湘潭旧址', '旧址', '旧址', '旧址', '旧址就在湘潭市', '2019-02-28 12:50:35','2019-03-07 16:52:51')
;
EOL;
       DB::unprepared($sql);
    }
}
