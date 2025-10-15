<div class="col-lg-8 col-md-10 col-sm-12 mx-auto">
    <h3 class="mb-3 text-purple">Bústia violeta</h3>

    @if (session()->has('ok'))
        <div class="alert alert-success">{{ session('ok') }}</div>
    @endif

    <form wire:submit.prevent="submit">
        <div class="form-group mb-2">
            <label>Categoria</label>
            <select class="form-control" wire:model.defer="categoria">
                <option value="">— Selecciona —</option>
                <option value="assetjament">Assetjament</option>
                <option value="igualtat">Igualtat</option>
                <option value="altres">Altres</option>
            </select>
            @error('categoria') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="form-group mb-2">
            <label>Missatge</label>
            <textarea class="form-control" rows="6" wire:model.defer="mensaje"></textarea>
            @error('mensaje') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="form-check mb-3">
            <input id="anonimo" type="checkbox" class="form-check-input" wire:model="anonimo">
            <label for="anonimo" class="form-check-label">Enviar de manera anònima</label>
        </div>

        <div class="form-group mb-3">
            <label>Adjunt (opcional)</label>
            <input type="file" class="form-control" wire:model="adjunto">
            @error('adjunto') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <button class="btn btn-primary">Enviar</button>
    </form>

    <p class="text-muted mt-3">
         S’usa un hash intern del DNI per evitar abusos i detecció de duplicats.
    </p>
</div>
