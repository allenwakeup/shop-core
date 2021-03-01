<?php

namespace Goodcatch\Modules\Core\Http\Requests;

use Goodcatch\Modules\Laravel\Http\Requests\CommonFormRequest as FormRequest;

class BaseRequest extends FormRequest
{


    /**
     * Get the validator instance for the request.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function getValidatorInstance ()
    {
        $validator = parent::getValidatorInstance ();
        $validator->addCustomAttributes (__ ('core::validation.attributes'));
        $validator->setCustomMessages (__ ('core::validation.custom'));
        $validator->addCustomValues (__ ('core::validation.values'));
        return $validator;
    }


}
