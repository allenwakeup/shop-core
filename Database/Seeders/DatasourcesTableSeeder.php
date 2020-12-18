<?php

namespace Goodcatch\Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatasourcesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sql = <<<EOL
INSERT INTO `core_datasources` VALUES
(1, 'mysql', 'Mysql 5.7.23', 'Mysql 5.7.23', 'driver:mysql,host:127.0.0.1,port:3306,database,username,password', 'charset:utf8mb4,collation:utf8mb4_unicode_ci,prefix', 1, 1, '2020-06-01 10:44:33', '2020-06-01 10:44:33'),
(2, 'pgsql', 'PostgreSQL', 'PostgreSQL', 'driver:pgsql,host:127.0.0.1,port:5432,database,username,password', 'charset:utf8,prefix,schema:public,sslmode:prefer', 1, 1, '2020-06-01 10:44:33', '2020-06-01 10:44:33'),
(3, 'sqlsrv', 'Microsoft SQLServer', 'Microsoft SQLServer', 'driver:sqlsrv,host:localhost,port:1433,database,username,password', 'charset:utf8,prefix', 1, 1, '2020-06-01 10:44:33', '2020-06-01 10:44:33'),
(4, 'sqlite', 'Sqlite', 'Sqlite', 'driver:sqlite,database:~/sqlite.db', 'prefix', 1, 1, '2020-06-01 10:44:33', '2020-06-01 10:44:33');
EOL;
        DB::unprepared($sql);
    }
}
