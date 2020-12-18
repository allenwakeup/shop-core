<?php


namespace Goodcatch\Modules\Core\Model\Relations;

use Illuminate\Database\Eloquent\Relations\MorphToMany as Relation;
use Illuminate\Support\Arr;

class MorphToMany extends Relation
{
    /**
     * Overwrite from Illuminate\Database\Eloquent\Relations\BelongsToMany
     *
     *
     * Set the join clause for the relation query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder|null  $query
     * @return $this
     */
    protected function performJoin($query = null)
    {
        $query = $query ?: $this->query;

        // We need to join to the intermediate table on the related model's primary
        // key column with the intermediate table's foreign key for the related
        // model instance. Then we can set the "where" for the parent models.
        $baseTable = $this->related->getTable();

        $method = '';
        if (Arr::has(config ('core.data_mapping.defined'), $this->getMorphClass())) {
            $model_mappings = Arr::get (config ('core.data_mapping.defined'), $this->getMorphClass (), []);
            $method = Arr::get ($model_mappings, '_map_'.$baseTable . '.relationship', '');
        }

        $key = $baseTable.'.'.$this->relatedKey;

        switch ($method) {
            case 'morphToMany' :
                $query->join($this->table, function ($join) use ($key, $method, $baseTable) {
                    $join->on ($key, '=', $this->getQualifiedRelatedPivotKeyName())
                        ->where (config ('core.data_mapping.' . $method . '.right', 'right') . '_type', $baseTable);
                });
                break;
            default:
                $query->join($this->table, $key, '=', $this->getQualifiedRelatedPivotKeyName());
                break;
        }


        return $this;
    }
}