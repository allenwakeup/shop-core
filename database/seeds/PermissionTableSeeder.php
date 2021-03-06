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
                        'name' => '主数据',
                        'children' => [
                            [
                                'name' => '地区管理',
                                'link' => $this->getSeedsModuleApiUri(self::MODULE_ALIAS,'areas'),
                            ],
                        ]
                    ],
                    [
                        'name' => '数据源管理',
                        'children' => [
                            [
                                'name' => '数据库信息',
                                'link' => $this->getSeedsModuleApiUri(self::MODULE_ALIAS,'databases'),
                            ],
                            [
                                'name' => '数据源',
                                'link' => $this->getSeedsModuleApiUri(self::MODULE_ALIAS,'datasources'),
                            ],
                            [
                                'name' => '连接管理',
                                'link' => $this->getSeedsModuleApiUri(self::MODULE_ALIAS,'connections'),
                            ],
                            [
                                'name' => '数据路径',
                                'link' => $this->getSeedsModuleApiUri(self::MODULE_ALIAS,'data_routes'),
                            ],
                            [
                                'name' => '数据映射',
                                'link' => $this->getSeedsModuleApiUri(self::MODULE_ALIAS,'data_maps'),
                            ]
                        ]
                    ],
                    [
                        'name' => '服务管理',
                        'children' => [
                            [
                                'name' => '计划与任务',
                                'link' => $this->getSeedsModuleApiUri(self::MODULE_ALIAS,'schedules'),
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    public function getSeedsPermissionGroups (){
        return [
            // 主数据
            // 地区管理
            $this->getSeedsModuleMenuGroupName(self::MODULE_ALIAS, '地区管理') => [
                self::MODULE_ALIAS . '.areas'
            ],
            // 数据源管理
            // 数据库信息
            $this->getSeedsModuleMenuGroupName(self::MODULE_ALIAS, '数据库信息') => [
                self::MODULE_ALIAS . '.databases' => [
                    'index' => ['name' => '列表', 'content' => '列表展示']
                ]
            ],
            //数据源
            $this->getSeedsModuleMenuGroupName(self::MODULE_ALIAS, '数据源管理') => [
                self::MODULE_ALIAS . '.datasources'
            ],
            // 连接管理
            $this->getSeedsModuleMenuGroupName(self::MODULE_ALIAS, '连接管理') => [
                self::MODULE_ALIAS . '.connections' => array_merge($this->api_actions, [
                    'test' => ['name' => '测试', 'content' => '测试连接']
                ])
            ],
            // 数据路径
            $this->getSeedsModuleMenuGroupName(self::MODULE_ALIAS, '数据路径') => [
                self::MODULE_ALIAS . '.data_routes'
            ],
            // 数据映射
            $this->getSeedsModuleMenuGroupName(self::MODULE_ALIAS, '数据映射') => [
                self::MODULE_ALIAS . '.data_maps'
            ],
            // 服务管理
            // 计划与任务
            $this->getSeedsModuleMenuGroupName(self::MODULE_ALIAS, '计划与任务') => [
                self::MODULE_ALIAS . '.schedules' => array_merge($this->api_actions, [
                    'logs' => ['name' => '日志列表', 'content' => '任务执行日志展示']
                ])
            ]

        ];
    }
}
