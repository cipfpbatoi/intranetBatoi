<div class="panel">
    <a class="panel-heading"
       role="tab"
       id="headingTwo"
       data-toggle="collapse"
       data-parent="#accordion"
       href="#collapseTwo"
       aria-expanded="true"
       aria-controls="collapseTwo"
    >
        <h4 class="panel-title"><i class="fa fa-bars"></i> @lang("models.Reunion.ordenes")</h4>
    </a>
    <div id="collapseTwo" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingTwo">
        <div class="panel-body">
            <table class="table table-striped table-condensed" name='ordenreunion'>
                <tr>
                    <th style="width: 5%">@lang("validation.attributes.orden")</th>
                    <th style="width: 40%">@lang("validation.attributes.punto")</th>
                    <th style="width: 40%">@lang("validation.attributes.resumen")</th>
                    <th style="width: 10%">@lang("validation.attributes.operaciones")</th>
                </tr>
                @foreach ($ordenes as $orden)
                <tr class="lineaGrupo" id='{{ $orden->id }}'>
                    <td><span class='none'  name='orden'>{!! $orden->orden !!}</span></td>
                    <td>
                        @if ($formulario->getElemento()->modificable)
                            <span class='input'  name='descripcion'>{!! $orden->descripcion !!}</span>
                        @else
                            <span class='none'  name='descripcion'>{!! $orden->descripcion !!}</span>
                       @endif
                    </td>
                    <td><span class='textarea' name='resumen'>{!! $orden->resumen !!}</span></td>
                    <td><span class='botones'>
                            @if ($formulario->getElemento()->modificable)
                                <a href="/reunion/{!!$formulario->getElemento()->id!!}/borrarOrden/{!! $orden->id !!}"
                                   class="delGrupo"
                                >
                                    {!! Html::image(
                                            'img/delete.png',
                                            trans("messages.buttons.delete"),
                                            array('class' => 'iconopequeno','title'=>trans("messages.buttons.delete"))
                                            )
                                    !!}
                                </a>
                            @endif
                            <a href="#" class="editGrupo">
                                {!! Html::image(
                                    'img/edit.png',
                                    trans("messages.buttons.edit"),
                                    array('class' => 'iconopequeno','title'=>trans("messages.buttons.edit"))
                                    )
                                !!}
                            </a>
                        </span>
                    </td>
                </tr>
                @endforeach
                @if ($formulario->getElemento()->modificable)
                <form method="POST" class="agua" action="/reunion/{!!$formulario->getElemento()->id!!}/nuevoOrden">
                    {{ csrf_field() }}
                    <input type='hidden' name='idReunion' value="{!!$formulario->getElemento()->id!!}">
                    <tr>
                        <td><input type='text' required name='orden' class="form-control"></td>
                        <td><input type='text' required name='descripcion' class="form-control"></td>
                        <td><textarea  rows="1"  name='resumen'class="form-control" ></textarea></td>
                        <td>
                            <input id="submit"
                                   class="boton"
                                   type="submit"
                                   value="@lang("messages.generic.anadir") @lang("models.modelos.OrdenReunion") "
                            >
                        </td>
                    </tr>
                </form>
                @endif
            </table>
        </div>
    </div>
</div>

