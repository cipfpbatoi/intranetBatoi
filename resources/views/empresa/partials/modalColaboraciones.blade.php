<!-- Modal -->
@php($ciclos = \Intranet\Entities\Ciclo::where('departamento',authUser()->departamento)->get())
<x-modal
        name="AddColaboration"
        title='Afegir Colaboració'
        action="/colaboracion/create"
        message='{{ trans("messages.buttons.confirmar")}}'
>
    <input type="hidden" id="id" value="" />
    <div class="form-group row">
        <label class="control-label" for="idCiclo">Cicle</label>
        <select id='idCiclo' name='idCiclo' class="form-control">
                @foreach ($ciclos as $cicle)
                    <option
                            value='{{ $cicle->id }}'
                            {{ old("idCiclo") == $cicle->id  ? "selected" : ($cicle->id == $ciclo ? 'selected' :  '')}}
                    >
                        {!! $cicle->ciclo !!}
                    </option>
                @endforeach
        </select>
    </div>
    <div class="form-group row">
        <label class="control-label" for="idCentro">Centre</label>
        <select id='idCentro' name='idCentro' class="form-control">
            @foreach ($elemento->centros as $centro)
                <option value='{{ $centro->id }}' {{ old("idCentro") == $centro->id ? "selected":""}}>
                    {!! $centro->nombre !!} ({!! $centro->direccion !!})
                </option>
            @endforeach
        </select>
    </div>
    <div class="form-group row">
        <label class="control-label" for="contacto">@lang("validation.attributes.contacto")</label>
        <input type='text' id='contacto_id' name='contacto'
               placeholder="@lang("validation.attributes.contacto")" value="{{ old('contacto') }}"
               class="form-control">
    </div>
    <div class="form-group row">
        <label class="control-label" for="telefono">@lang("validation.attributes.telef1")</label>
        <input id="telefono" type="text" name="telefono" placeholder="{{trans("validation.attributes.telef1")}}"
                       value="{{ old('telefono') }}" class="form-control"/>
    </div>
    <div class="form-group row">
        <label class="control-label" for="email">@lang("validation.attributes.email")</label>
        <input id="email" type="text" name="email" placeholder="@lang("validation.attributes.email")"
                       value="{{ old('email') }}" class="form-control"/>
    </div>
    <div class="form-group row">
        <label class="control-label" for="contacto">@lang("validation.attributes.tutor")</label>
        <input id="tutor" type="text" name="tutor" placeholer="@lang("validation.attributes.tutor")"
                       class="form-control" value='{{authUser()->nombre}} {{authUser()->apellido1}}'/>
    </div>
    <div class="form-group row">
        <label class="control-label" for="contacto">@lang("validation.attributes.puestos")</label>
        <input id="puestos" type="text" name="puestos" placeholder="@lang("validation.attributes.puestos")*"
                       value="{{ old('puestos') }}" class="form-control"/>
    </div>
</x-modal>
