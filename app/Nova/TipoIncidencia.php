<?php

namespace Intranet\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Number;

class TipoIncidencia extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \Intranet\Entities\TipoIncidencia::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'nom';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'nombre','nom'
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
            Number::make('id')->sortable()->rules('required')
            ->creationRules('unique:tipoincidencias,id','max:250')->hideWhenUpdating(),
            Text::make(__('validation.attributes.nombre'),'nombre')
                ->sortable()
                ->rules('required', 'max:30'),
            Text::make(__('validation.attributes.nombre'),'nom')
                ->sortable()
                ->rules('required', 'max:30'),
            BelongsTo::make('Profesor','Responsable'),
            Select::make(__('validation.attributes.tipo'),'tipus')->searchable()->options(config('auxiliares.tipoIncidencia'))->displayUsingLabels(),
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
