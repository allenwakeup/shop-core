<?php


namespace Goodcatch\Modules\Core\Observers;

use Goodcatch\Modules\Core\Model\Admin\Connection;
use Goodcatch\Modules\Core\Model\Admin\DataRoute;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;


/**
 * 更新缓存中的数据路径
 *
 * Class DataRouteObserver
 * @package Goodcatch\Modules\Core\Observers
 */
class DataRouteObserver
{

    const TABLE_SUFFIX_PIVOT = '_pivot';
    const TABLE_COLUMN_PIVOT_ID = 'pivot_id';
    const TABLE_COLUMN_PIVOT = 'pivot';

    // creating, created, updating, updated, saving
    // saved, deleting, deleted, restoring, restored
    public function created (DataRoute $data_route)
    {
        $this->createTables ($data_route);
    }

    public function updated (DataRoute $data_route)
    {
        $this->createTables ($data_route);
    }

    public function deleting (DataRoute $data_route)
    {
        $schema = $this->getSchemaWithConn ($data_route);

        $schema->dropIfExists ($data_route->output);

        $schema->dropIfExists ($data_route->output . self::TABLE_SUFFIX_PIVOT);
    }

    private function getSchemaWithConn (DataRoute $data_route)
    {
        if (isset ($data_route)) {
            $connection = Connection::find ($data_route->connection_id);
            if (isset ($connection)
                && Arr::has(config('database.connections', []), $connection->connectionName)) {
                return Schema::connection ($connection->connectionName);
            }
        }
        return Schema::connection (null);
    }

    private function createTables (DataRoute $data_route)
    {
        if (! empty ($data_route->output))
        {
            $schema = $this->getSchemaWithConn ($data_route);

            if (!$schema->hasTable($data_route->output)) {
                $schema->create($data_route->output, function (Blueprint $blueprint) use ($data_route) {
                    $blueprint->string($data_route->table_from, '50')->nullable(false)->comment($data_route->from . ' ID');
                    $blueprint->string(self::TABLE_COLUMN_PIVOT_ID, '50')->nullable(false)->comment('中间名称');
                    $blueprint->unique([$data_route->table_from, self::TABLE_COLUMN_PIVOT_ID]);
                });
            }
            if (!$schema->hasTable($data_route->output . self::TABLE_COLUMN_PIVOT_ID)) {
                $schema->create($data_route->output . self::TABLE_SUFFIX_PIVOT, function (Blueprint $blueprint) use ($data_route) {
                    $blueprint->string(self::TABLE_COLUMN_PIVOT, '50')->nullable(false)->comment('中间名称');
                    $blueprint->string($data_route->table_to, '50')->nullable(false)->comment($data_route->to . ' ID');
                    $blueprint->unique([self::TABLE_COLUMN_PIVOT, $data_route->table_to]);
                });
            }
        }
    }

}