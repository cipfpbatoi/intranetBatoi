<?php
namespace Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;


class FeatureTestCase extends TestCase
{
    use DatabaseTransactions;
    
    
    public function seeErrors(array $errors)
    {
        foreach ($errors as $message => $fields)
        {
            if (is_string($fields))
               $this->seeInElement('div.alert.alert-danger ul li',trans("validation.$message",array('attribute'=>trans("validation.attributes.".$fields))));
            else {
                foreach ($fields as $index => $field)
                    $fields[$index] = trans("validation.attributes.".$field);
                $this->seeInElement('div.alert.alert-danger ul li',trans("validation.$message",$fields));
            }
        }
    }
    
}

