<!-- Modal Nou -->
<x-modal name="seleccion" title='Selecciona elements' action="/{{ strtolower($panel->getModel())}}/selecciona"
         message='{{ trans("messages.buttons.confirmar")}}'>
        <strong>Selecciona Document:</strong>
        <select name="informe" id="informe">
                <option value="">--</option>
                <option value="pg0301">@lang("models.Fct.pg0301")</option>
                <option value="pr0401">@lang("models.Fct.pr0401")</option>
                <option value="pr0402">@lang("models.Fct.pr0402")</option>
                <option value="autTutor">@lang("models.Fct.autTutor")</option>
                <option value="autDireccio">@lang("models.Fct.autDireccio")</option>
                <option value="autAlumnat">@lang("models.Fct.autAlumnat")</option>
                <option value="A1">@lang("models.Fct.an1")</option>
                <option value="A2">@lang("models.Fct.an2")</option>
                <option value="A5">@lang("models.Fct.an5")</option>
        </select>
        <input type="checkbox" name="zip" id="zip" /> Zip
        <input type="checkbox" name="mostraDiv" id="mostraDiv" /> Signatura Digital
        <hr/>
        <table id="tableSeleccion"></table>
        <div id="divSignatura" style="display:none;">
                @if(file_exists(authUser()->pathCertificate))
                        <div style="border: 1px solid black;background-color:#ddd" >
                                <h3 style="text-align: center">Signatura Digital</h3>
                                <label class="control-label" for="password">Introduir Password Intranet:</label>
                                <input type="password" id="decrypt" name="decrypt" class="form-control"/>
                                <label class="control-label" for="password">Introduir Password Certificat:</label>
                                <input type="password" id="cert" name="cert" class="form-control"/>
                        </div>
                @endif
        </div>
</x-modal>
{{ Html::script("/js/extended.js") }}
{{ Html::script("/js/taulaCheck.js") }}
