<?php
// ./app/Validator/MyValidator.php

namespace Intranet\Validator;

use Illuminate\Validation\Validator;

class MyValidator extends Validator
{
    // Laravel uses this convention to look for validation rules, this function will be triggered 
    // for composite_unique
    public function validateCompositeUnique( $attribute, $value, $parameters )
    {
        // by extending you can use protected methods like this one
        $this->requireParameterCount( 2, $parameters, 'composite_unique' );

        // same logic from my last response
        // ...
    }

    // you can add other validations to this class, the next one will validate a another_rule validation
    public function validateAnotherRule( $attribute, $value, $parameters )
    {
        // custom logic here
    }
}