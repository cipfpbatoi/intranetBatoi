<!-- Modal Nou -->
<x-modal name="dialogo" title='{{ __("messages.buttons.contact")}} {{__("models.modelos.".$panel->getModel()) }}'
         message='{{ __("messages.buttons.confirmar")}}'>
    @if ($panel->getModel() === 'Colaboracion')
        <div class="form-group">
            <label class="control-label" for="contact_type">Tipus de contacte</label>
            <select id="contact_type" name="contact_type" class="form-control">
                <option value="telefonada">Telefonada</option>
                <option value="correu">Correu</option>
                <option value="visita">Visita</option>
                <option value="reunio">Reunió</option>
                <option value="seguiment">Seguiment</option>
            </select>
        </div>

        <div class="form-group">
            <label class="control-label" for="resultat">Resultat del contacte</label>
            <select id="resultat" name="resultat" class="form-control"></select>
        </div>

        <div class="form-group">
            <label class="control-label" for="observacions">Observacions</label>
            <textarea id="observacions" name="observacions" class="form-control" rows="3"></textarea>
        </div>

        <div class="form-group">
            <label class="control-label" for="proxima_accio">Pròxim pas</label>
            <input id="proxima_accio" name="proxima_accio" type="text" class="form-control" placeholder="Ex. Tornar a telefonar dimarts, enviar Annex I">
        </div>

        <div class="form-group">
            <label class="control-label" for="data_prevista">Data prevista</label>
            <input id="data_prevista" name="data_prevista" type="date" class="form-control">
        </div>

        <input id="explicacion" name="explicacion" type="hidden" value="">
    @else
        <label class="control-label" for="explicacion">@lang("messages.generic.resum"):</label>
        <textarea id="explicacion" name="explicacion" class="form-control"></textarea>
    @endif
</x-modal>
