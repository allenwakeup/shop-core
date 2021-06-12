<?php

namespace Goodcatch\Modules\Core\Database\Seeders;

use Goodcatch\Modules\Qwshop\Traits\PermissionSeedsTrait;
use Illuminate\Database\Seeder;

class PermissionTableSeeder extends Seeder
{
    use PermissionSeedsTrait;

    const MODULE_NAME = '核心模块';
    const MODULE_ALIAS = 'core';

    public function getSeedsMenus (){
        return [
            [
                'name' => self::MODULE_NAME,
                'children' => [
                    [
                        'name' => '地区管理',
                        'link' => $this->getSeedsModuleApiUri(self::MODULE_ALIAS,'areas'),
                    ]
                ]
            ]
        ];
    }

    public function getSeedsPermissionGroups (){
        return [
            $this->getSeedsModuleMenuGroupName(self::MODULE_ALIAS, '地区管理') => [
                'areas'
            ]
        ];
    }
}
