<?php

namespace Intranet\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Arr;

class ValidationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
       /**
         * composite_unique validation rule
         *
         * Usage:
         *
         * When creating:
         *
         *      'column1' => 'composite_unique:table,column1,column2'
         *      'column1' => 'composite_unique:table,column1,column2,column3'
         *
         * When Updating, use a number or an expression with an equal sign as the
         * last parameter
         *
         *      'column1' => 'composite_unique:table,column1,column2,10'
         *      'column1' => 'composite_unique:table,column1,column2,primaryField = 10'
         *
         * IMPORTANT: note that the current column should be added again in the rule, this 
         * permits the attribute name being validated to be different than the database column
         */
        $this->app[ 'validator' ]->extend( 'composite_unique',
            function ( $attribute, $value, $parameters, $validator ) {
                // Custom validation logic

                // data being validated
                $data = $validator->getData();

                // remove whitespaces
                $parameters = array_map( 'trim', $parameters );

                // remove first parameter and assume it is the table name
                $table = array_shift( $parameters );

                // remove last parameter to check for except condition
                $lastParameter = array_pop( $parameters );

                // start building the query
                $query = \DB::table( $table )->select( \DB::raw( 1 ) );

                // add the field being validated as a condition
                // IMPORTANT: skipping it for improved consistency, see
                // note in the function's comment
                // $query->where( $attribute, $value );

                // iterates over the parameters and add as where clauses
                while ($field = array_shift( $parameters )) {
                    $query->where( $field, Arr::get( $data, $field ) );
                }

                // check $lastParameter for except condition. Uses a regular
                // expression to check if $lastParameter contains only numbers
                // or an equal sign
                if (preg_match( '/^(?:\d+|.+?=.+)$/', $lastParameter )) {
                    // is except condition

                    if (preg_match( '/^\d+$/', $lastParameter )) {
                        // only numbers, assume primary key is called 'id' rewrite $lastParameter
                        $lastParameter = sprintf( '%s.id = %s', $table, $lastParameter );
                    }

                    // negate condition
                    $exceptField = sprintf( '(NOT %s)', $lastParameter );

                    $query->whereRaw( $exceptField );
                } else {
                    // is not except condition, add as a normal where
                    $query->where( $lastParameter, Arr::get( $data, $lastParameter ) );
                }

                // get the result from DB
                $result = $query->first();

                return empty( $result ); // true if no result was found
            } );
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
