@foreach ($panel->getElementos($pestana) as $elemento)
<div class="col-md-4 col-sm-4 col-xs-12 profile_details">
    <div id="{{$elemento->id}}" class="well profile_view">
        <div class="col-sm-12">
            <h4 class="brief">
                <em class="fa fa-wrench"></em>
                <strong> id.{{$elemento->id}}</strong>
                {{$elemento->material}} {{ $elemento->descripcion }}.
            </h4>
            @if (!empty($elemento->observaciones))
                <h5><em class="fa fa-comment-o"></em> {{$elemento->observaciones}}</h5>
            @endif
            @if (!empty($elemento->imagen))
                <div class="incidencia-imatge" style="margin-top: 8px;">
                    <a href="{{ Storage::url($elemento->imagen) }}"
                       class="js-incidencia-image"
                       data-image="{{ Storage::url($elemento->imagen) }}"
                       aria-label="Veure imatge ampliada">
                        <img src="{{ Storage::url($elemento->imagen) }}"
                             alt="Imatge incidència"
                             class="incidencia-thumb"
                             loading="lazy">
                    </a>
                </div>
            @endif
            <div class="left col-xs-12">
                <h5> <em class="fa fa-tag"></em> {{$elemento->Xespacio}}</h5>
                <h5><em class="fa fa-tag"></em> {{$elemento->Tipos->literal}}</h5>
                <ul class="list-unstyled">
                        <li><em class="fa fa-user"></em>
                            {{$elemento->Creador->nombre}} {{$elemento->Creador->apellido1}}
                        </li>
                        <li><em class="fa fa-group"></em>
                            @if (isset($elemento->Responsables->nombre))
                                {{$elemento->Responsables->nombre}} {{$elemento->Responsables->apellido1}}
                            @else
                                No assignat
                            @endif
                        </li>
                </ul>
                @if (isset($elemento->solucion))
                    <h5><em class="fa fa-lightbulb-o"></em>{{$elemento->solucion}}</h5>
                @endif
            </div>
        </div>
        <div class="col-xs-12 bottom text-center">
            <div class="col-xs-12 col-sm-6 emphasis">
                <p class="ratings">
                    {{$elemento->fecha}}<br/>
                    @if (isset($elemento->orden))
                    <a href="/mantenimiento/ordentrabajo/{{$elemento->orden}}/anexo" class="btn btn-primary btn-xs">
                        @lang("validation.attributes.orden") {{$elemento->orden}}
                    </a>
                    @endif
                </p>
                
            </div>
            <div class="col-xs-12 col-sm-6 emphasis">
                <x-botones :panel="$panel" tipo="profile" :elemento="$elemento ?? null" /><br/>
             </div>
        </div>
    </div>
</div>
@endforeach

@once
    <style>
        .incidencia-thumb {
            width: 160px;
            height: 120px;
            object-fit: cover;
            border-radius: 4px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.15);
        }
        .incidencia-thumb:hover {
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.2);
        }
    </style>

    <div class="modal fade" id="incidenciaImageModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tancar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Imatge incidència</h4>
                </div>
                <div class="modal-body text-center">
                    <img id="incidenciaImageModalImg" src="" alt="Imatge incidència ampliada" style="max-width: 100%; height: auto;">
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.js-incidencia-image').forEach(function (link) {
                link.addEventListener('click', function (event) {
                    event.preventDefault();
                    var src = this.getAttribute('data-image');
                    var img = document.getElementById('incidenciaImageModalImg');
                    if (img) {
                        img.src = src;
                    }
                    if (window.jQuery && window.jQuery.fn && window.jQuery.fn.modal) {
                        window.jQuery('#incidenciaImageModal').modal('show');
                    } else {
                        window.open(src, '_blank', 'noopener');
                    }
                });
            });
        });
    </script>
@endonce
