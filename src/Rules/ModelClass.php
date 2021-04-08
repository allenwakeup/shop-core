<?php

namespace Goodcatch\Modules\Core\Rules;

use Illuminate\Contracts\Validation\Rule;

class ModelClass implements Rule
{
    /**
     * @var string $key indicates that identification for one record
     */
    protected $key;

    /**
     * @var string $date the date that pass to client during Api communication
     */
    protected $date;

    /**
     * Create a new rule instance.
     */
    public function __construct ()
    {

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
        $file = app_path (
            str_replace ('\\',
                DIRECTORY_SEPARATOR,
                str_replace ('App', '', $value)) . '.php'
        );
        return file_exists ($file);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message ()
    {
        return '类不存在';
    }
}
