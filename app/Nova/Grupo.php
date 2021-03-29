<?php

namespace Intranet\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\HasOne;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Http\Requests\NovaRequest;

class Grupo extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \Intranet\Entities\Grupo::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'nombre';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'nombre','tutor'
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            Text::make('codigo')->sortable()->rules('required')->
            creationRules('unique:grupos,codigo','max:5')->hideWhenUpdating(),
            Text::make(__('validation.attributes.nombre'),'nombre')
                ->sortable()
                ->rules('required', 'max:45'),
            Text::make(__('validation.attributes.turno'),'turno')
                ->sortable()
                ->rules('required', 'max:1')
                ->hideFromIndex(),
            HasOne::make('Profesor','Tutor'),
            BelongsTo::make('Ciclo'),
            Select::make(__('validation.attributes.tipo'),'tipo')->options(config('auxiliares.tipoEstudio'))->displayUsingLabels(),
            Text::make('curso')
                ->sortable()
                ->rules( 'integer','max:3')
                ->hideFromIndex(),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }
}
