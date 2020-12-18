<?php

namespace Goodcatch\Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * 最新国标行政区规划最低到居委会这一级别，行政区代码代码也变长，不包含港澳台信息，按需所需不同的版本
     * 见 https://github.com/wecatch/china_regions/releases
     *
     * @return void
     */
    public function run ()
    {
        $province_sql = file_get_contents (module_generated_path ('core', 'seeder', 'province.sql'));
        DB::unprepared ($province_sql);
        $city_sql = file_get_contents (module_generated_path ('core', 'seeder', 'city.sql'));
        DB::unprepared ($city_sql);
        $county_sql = file_get_contents (module_generated_path ('core', 'seeder', 'county.sql'));
        DB::unprepared ($county_sql);
        $town_sql = file_get_contents (module_generated_path ('core', 'seeder', 'town.sql'));
        DB::unprepared ($town_sql);
    }
}
