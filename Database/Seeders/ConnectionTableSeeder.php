<?php

namespace Goodcatch\Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ConnectionTableSeeder extends Seeder
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
INSERT INTO `core_connections` VALUES
(1,3,'sqlserver_db_in','','','','sqlsrv','',1433,'','','','','','','utf8','','','',0,'','','','','','','SRC','mssql',0,1,'2020-06-16 16:03:26','2020-06-16 16:03:26'),
(2,1,'mysql_in','','','','mysql','mysql',3306,'','','','','','','utf8mb4','utf8mb4_unicode_ci','','',0,'','','','','','','SRC','docker',0,1,'2020-06-16 16:43:13','2020-06-16 16:43:13'),
(3,1,'mysql_out','','','','mysql','mysql',3306,'','','','','','','utf8mb4','utf8mb4_unicode_ci','','',0,'','','','','','','DST','docker',0,1,'2020-06-16 16:43:13','2020-06-16 16:43:13');
EOL;
        DB::unprepared($sql);
    }
}
