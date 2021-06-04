<?php

namespace Intranet\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\HasOne;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\File;
use Laravel\Nova\Http\Requests\NovaRequest;

class Task extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \Intranet\Entities\Task::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id','descripcion','vencimiento'
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
            Text::make(__('validation.attributes.descripcion'),'descripcion')
                ->sortable()
                ->rules('required', 'max:100'),
            Date::make(__('validation.attributes.vencimiento'),'vencimiento')
                ->rules('required','after:today'),
            Text::make(__('validation.attributes.enlace'),'enlace')
                ->sortable()
                ->rules( 'max:200')
                ->hideFromIndex(),
            File::make('fichero')->disk('public')->path('/Eventos')->prunable()
                ->if(['enlace'], fn($value) => $value['enlace'] === '')
                ->if(['enlace'], "_value.enlace === ''"),
            Boolean::make(__('validation.attributes.informativa'),'informativa'),
            Boolean::make(__('validation.attributes.activa'),'activa'),
            Select::make(__('validation.attributes.destinatario'),'destinatario')->options(config('roles.lor'))->displayUsingLabels(),
            Select::make(__('validation.attributes.accion'),'action')->options(config('roles.actions'))->displayUsingLabels()
                ->if(['informativa'], fn($value) => $value['informativa'] === 'false')
                ->if(['informativa'], "_value.informativa === 'false'"),
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
