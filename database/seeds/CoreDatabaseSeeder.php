<?php

namespace Goodcatch\Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class CoreDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        $this->call(PermissionTableSeeder::class);
        $this->call(RegionsTableSeeder::class);
        $this->call(AreaTableSeeder::class);
        $this->call(DatasourcesTableSeeder::class);
        $this->call(ConfigsTableSeeder::class);
        $this->call(ConnectionTableSeeder::class);
    }
}
