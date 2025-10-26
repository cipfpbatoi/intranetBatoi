{{-- resources/views/livewire/bustia-violeta/form.blade.php --}}
<div class="col-lg-8 col-md-10 col-sm-12 mx-auto">
    <h3 class="mb-3">Bústia</h3>

    @if (session()->has('ok'))
        <div class="alert alert-success">{{ session('ok') }}</div>
    @endif

    <form wire:submit.prevent="confirmAndSubmit">
       <div class="form-group mb-2">
            <label>Tipus</label>
            <select class="form-control" wire:model="tipus">
                <option value="violeta">Violeta</option>
                <option value="convivencia">Convivència</option>
            </select>
        </div>
        <div class="form-group mb-2">
            <label>Finalitat</label>
            <select class="form-control" wire:model="finalitat">
                <option value="escoltar">Vull que escolteu/llegiu la meua història</option>
                <option value="visibilitzar">Vull visibilitzar </option>
                <option value="parlar">Vull parlar amb vosaltres personalment</option>
            </select>
            @error('finalitat') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        @if ($finalitat === 'parlar')
            <div class="alert alert-info py-2">
                Per poder contactar-te, l’enviament no pot ser anònim.
            </div>
        @endif 
        <div class="form-group mb-2">
            <label>Categoria</label>
            <select class="form-control" wire:model.defer="categoria">
                <option value="">— Selecciona —</option>
                @foreach($categories as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
            @error('categoria') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="form-group mb-2">
            <label>Missatge</label>
            <textarea class="form-control" rows="6" wire:model.defer="mensaje"></textarea>
            @error('mensaje') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="form-check mb-3">
            <input id="anonimo" type="checkbox" class="form-check-input"
                   wire:model="anonimo" @if($finalitat==='parlar') disabled @endif>
            <label for="anonimo" class="form-check-label">
                Enviar de manera anònima
            </label>
            @error('anonimo') <small class="text-danger d-block">{{ $message }}</small> @enderror
        </div>
 
        <button class="btn btn-primary">Enviar</button>
    </form>

    <p class="text-muted mt-3">
        Si vols ser atés/a directament per a poder canalitzar la teua necessitat i donar-te suport, és millor, que la consulta no siga anònima. Si prefereixes fer l’exposició dels fets i mantindre’t en
        l’anonimat, marca la casella corresponent.<br/>
        Recordar-te que aquesta informació sols serà llegida per la Comissió d’Igualtat i Convivència del
        CIPFP Batoi i tractada amb total confidencialitat, per preservar la teua intimitat i seguretat personal,
        no obstant això, cal que les situacions de desigualtat de gènere siguen visibilitzades per a que no es
        tornen a repetir.
    </p>
</div>

