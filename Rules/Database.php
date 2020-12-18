<?php

namespace Goodcatch\Modules\Core\Rules;

use Illuminate\Contracts\Validation\Rule;

class Database implements Rule
{
    /**
     * @var string $uniqueOrExists indicates that update only or create new one
     */
    protected $uniqueOrExists;


    /**
     * Create a new rule instance.
     */
    public function __construct ($uniqueOrExists)
    {

        $this->uniqueOrExists = $uniqueOrExists;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes ($attribute, $value)
    {
        $exists = array_key_exists ($value, config ('database.connections'));

        if ($this->uniqueOrExists === 'exists')
        {
            $exists = false;
        }

        return ! $exists;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message ()
    {
        return '数据库已存在';
    }
}
