<?php

namespace Goodcatch\Modules\Core\Model\Concerns;

use Goodcatch\Modules\Core\Model\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait HasMappingAttributes
{

    /**
     * change morph class to what ever u want
     *
     * @var $morphClass
     */
    protected $morphClass;

    /**
     * table from left
     *
     * @var $dataMapTable
     */
    protected $dataMapTable;

    /**
     * table from left
     *
     * @var $dataMapPayload
     */
    protected $dataMapPayload;

    /**
     * setup data map configuration
     *
     * @param array $options
     * @return HasMappingAttributes
     */
    public function setDataMap (array $options)
    {
        if (! empty ($options))
        {
            $this->dataMapPayload = Arr::get ($options, 'payload', []);

        }
        return $this;
    }

    /**
     * Get a relationship.
     *
     * @param string $key
     * @return mixed
     */
    public function getRelationValue ($key)
    {
        if (!$this->relationLoaded ($key)) {

            if ($this->hasDataMap ()) {

                $options = $this->getDataMap ();

                if (Arr::has ($options, $key))
                {
                    $this->setDataMap ($this->getDataMap ());

                    $relation = $this->getMappingRelation ($key);

                    if (isset ($relation)) {

                        tap ($relation->getResults (), function ($results) use ($key) {
                            $this->setRelation ($key, $results);
                        });

                        return $this->relations [$key];
                    }
                }
            }
        }
        parent::getRelationValue($key);
    }

    /**
     * Get the class name for polymorphic relations.
     *
     * @return string
     */
    public function getMorphClass ()
    {
        return $this->dataMapTable ?? $this->morphClass;
    }

    /**
     * Create a new model instance for a related model.
     *
     * @param  string  $class
     * @return mixed
     */
    protected function newRelatedInstance ($class)
    {
        if ($this->hasDataMap ())
        {
            if (Arr::has ($this->getDataMap (), $this->getDataMapKey ($class)))
            {
                $instance = (new static)->setDataMapTable ($class);
                tap ($instance, function ($instance) {
                    $instance->unguard ();
                });
                return $instance;
            }
        }
        return parent::newRelatedInstance ($class);
    }

    /**
     * Get a relationship.
     *
     * @param string $key
     * @return mixed
     */
    private function getMappingRelation ($key)
    {

        if (Arr::has(config ('core.data_mapping.defined'), $this->getTable ())) {
            $model_mappings = Arr::get (config ('core.data_mapping.defined'), $this->getTable (), []);

            $args = Arr::get ($model_mappings, $key . '.args', []);

            $method = Arr::get ($model_mappings, $key . '.relationship', 'morphToMany');


            return $this->forwardCallTo ($this, $method, $args);


        }
    }

    /**
     * Instantiate a new MorphToMany relationship.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Database\Eloquent\Model  $parent
     * @param  string  $name
     * @param  string  $table
     * @param  string  $foreignPivotKey
     * @param  string  $relatedPivotKey
     * @param  string  $parentKey
     * @param  string  $relatedKey
     * @param  string|null  $relationName
     * @param  bool  $inverse
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    protected function newMorphToMany(Builder $query, Model $parent, $name, $table, $foreignPivotKey,
                                      $relatedPivotKey, $parentKey, $relatedKey,
                                      $relationName = null, $inverse = false)
    {
        return new MorphToMany($query, $parent, $name, $table, $foreignPivotKey, $relatedPivotKey, $parentKey, $relatedKey,
            $relationName, $inverse);
    }

    /**
     * Get a relationship.
     *
     * @param $key
     * @return mixed
     */
    public function getDataMapping ($key)
    {
        return $this->getMappingRelation ($this->getDataMapKey ($key));
    }


    public function getDataMapTable ()
    {
        return $this->dataMapTable;
    }

    public function setDataMapTable ($dataMapTable)
    {
        $this->dataMapTable = $dataMapTable;
        if ($this->getTable () === Str::snake(Str::pluralStudly(class_basename($this))))
        {
            $this->table = $this->dataMapTable;
        }
        return $this;
    }

    private function hasDataMap ()
    {
        return Arr::has (config ('core.data_mapping.defined', []), $this->getDataMapTable ());
    }

    private function getDataMap ()
    {
        return Arr::get (config ('core.data_mapping.defined', []), $this->getDataMapTable ());
    }

    private function getDataMapKey ($key)
    {
        return '_map_' . $key;
    }
}
