<!-- Modal Nou -->
<x-modal name="AddCenter" title='Afegir Centre Treball' action="/centro/create"
         message='{{ trans("messages.buttons.confirmar")}}'>
    <div class="form-group row">
        <label class="control-label" for="nombre">@lang("validation.attributes.nombre")</label>
        <input type='text'
               name='nombre'
               placeholder='@lang("validation.attributes.nombre") *'
               value="{{ old('nombre') }}"
               class='form-control' />
    </div>
    <div class="form-group row">
        <label class="control-label" for="nombre">@lang("validation.attributes.direccion"):</label>
        <input type='text'
               name='direccion'
               placeholder='@lang("validation.attributes.direccion") *'
               value="{{ old('direccion') }}"
               class='form-control' />
    </div>
    <div class="form-group row">
        <label class="control-label" for="nombre">@lang("validation.attributes.localidad"):</label>
        <input type='text'
               name='localidad'
               placeholder='@lang("validation.attributes.localidad") *'
               value="{{ old('localidad') }}"
               class='form-control' />
        <input type='hidden' name='idEmpresa' value="{!!$elemento->id!!}">
    </div>
    <div class="form-group row">
        <label class="control-label" for="nombre">Horaris:</label>
        <input type='text'
               name='horarios'
               placeholder='horaris'
               value="{{ old('horarios') }}"
               class='form-control' />
    </div>
    <div class="form-group row">
        <label class="control-label" for="nombre">@lang("validation.attributes.observaciones"):</label>
        <input type='textarea'
               name='observaciones'
               placeholder='@lang("validation.attributes.observaciones")'
               value="{{ old('observaciones') }}"
               class='form-control' />
    </div>
</x-modal>
