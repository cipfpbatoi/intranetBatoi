<?php

namespace Intranet\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Http\Requests\NovaRequest;

class Ciclo extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \Intranet\Entities\Ciclo::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'ciclo';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'ciclo','vliteral','cliteral'
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
            ID::make(__('ID'), 'id')->sortable(),
            Text::make(__('validation.attributes.ciclo'),'ciclo')
                ->sortable()
                ->rules('required', 'max:50'),
            Text::make(__('validation.attributes.vliteral'),'vliteral')
                ->sortable()
                ->rules('required', 'max:100')
                ->hideFromIndex(),
            Text::make(__('validation.attributes.cliteral'),'cliteral')
                ->sortable()
                ->rules('required', 'max:100')
                ->hideFromIndex(),
            BelongsTo::make('Departamento','Departament'),
            Select::make(__('validation.attributes.tipo'),'tipo')->searchable()->options(config('auxiliares.tipoEstudio'))->displayUsingLabels(),
            Text::make('normativa')
                ->sortable()
                ->rules( 'max:10')
                ->hideFromIndex(),
            Text::make('titol')
                ->rules( 'max:100')
                ->hideFromIndex(),
            Text::make('rd')
                ->rules( 'max:100')
                ->hideFromIndex(),
            Text::make('rd2')
                ->rules( 'max:100')
                ->hideFromIndex(),
            Text::make('horasFct')
                ->hideFromIndex(),
            Text::make('acronim')
                ->rules( 'max:10')
                ->hideFromIndex(),
            Text::make('llocTreball')
                ->rules( 'max:100')
                ->hideFromIndex(),
            Date::make('dataSignaturaDual')
                ->nullable()
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
