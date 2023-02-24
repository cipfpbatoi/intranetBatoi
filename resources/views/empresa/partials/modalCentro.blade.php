<!-- Modal Nou -->
<x-modal
        name="AddCenter"
        title='Afegir Centre Treball'
        action="/centro/create"
        message='{{ trans("messages.buttons.confirmar")}}'
>
    <input type="hidden" id="idCentro" value="" />
    <div class="form-group item has-feedback row">
        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="nombre">
            @lang("validation.attributes.nombre")
        </label>
        <div class="col-md-8 col-sm-8 col-xs-12">
            <input type='text'
                   name='nombre'
                   id="nombreCentro"
                   placeholder='@lang("validation.attributes.nombre") *'
                   value="{{ old('nombre') }}"
                   class='form-control '
                   style="@if ($errors->has('nombre')) background-color:#ebcaca @endif"
            />
        </div>
    </div>
    <div class="form-group item has-feedback row">
        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="direccion">
            @lang("validation.attributes.direccion"):</label>
        <div class="col-md-8 col-sm-8 col-xs-12">
            <input type='text'
                   name='direccion'
                   id="direccionCentro"
                   placeholder='@lang("validation.attributes.direccion") *'
                   value="{{ old('direccion') }}"
                   class='form-control'
                   style="@if ($errors->has('direccion')) background-color:#ebcaca @endif"
            />
        </div>
    </div>
    <div class="form-group item has-feedback row">
        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="localidad">
            @lang("validation.attributes.localidad"):
        </label>
        <div class="col-md-8 col-sm-8 col-xs-12">
            <input type='text'
                   name='localidad'
                   id="localidadCentro"
                   placeholder='@lang("validation.attributes.localidad") *'
                   value="{{ old('localidad') }}"
                   class='form-control'
                   style="@if ($errors->has('localidad')) background-color:#ebcaca @endif"
            />
        </div>
        <input type='hidden' name='idEmpresa' value="{!!$elemento->id!!}">
    </div>
    <div class="form-group item has-feedback row">
        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="horarios">Horaris:</label>
        <div class="col-md-8 col-sm-8 col-xs-12">
            <input type='text'
                   name='horarios'
                   id="horariosCentro"
                   placeholder='horaris'
                   value="{{ old('horarios') }}"
                   class='form-control' />
        </div>
    </div>
    <div class="form-group item has-feedback row">
        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="codiPostal">Codi Postal:</label>
        <div class="col-md-8 col-sm-8 col-xs-12">
            <input type='text'
                   name='codiPostal'
                   id="codiPostalCentro"
                   placeholder='Codi Postal'
                   value="{{ old('codiPostal') }}"
                   class='form-control' />
        </div>
    </div>
    <div class="form-group item has-feedback row">
        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="idioma">Idioma:</label>
        <div class="col-md-8 col-sm-8 col-xs-12">
            <select name='idioma' class='form-control' id="idiomaCentro" >
                <option value="" selected="selected">Idioma</option>
                <option value="">-Selecciona-</option>
                <option value="es">Español</option>
                <option value="ca">Valencià</option>
                <option value="en">English</option>
            </select>
        </div>
    </div>
    <div class="form-group item has-feedback row">
        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="observaciones">
            @lang("validation.attributes.observaciones"):</label>
        <div class="col-md-8 col-sm-8 col-xs-12">
            <textarea rows='4' name='observaciones' id="observacionesCentro" class='form-control'>
                {{ old('observaciones')??'' }}
            </textarea>
        </div>
    </div>

</x-modal>
