@php
    $selectedRoles = array_values(array_diff(rolesUser($formulario->getElemento()->rol), [1]));
    $allRoles = array_filter(config('roles.lor', []), static fn ($name, $id) => (int) $id !== 1, ARRAY_FILTER_USE_BOTH);
    $isAlumnoProfile = $formulario->getElemento() instanceof \Intranet\Entities\Alumno;
    $canManageRoles = userIsNameAllow('direccion') || userIsNameAllow('administrador');
@endphp

@if ($isAlumnoProfile)
    <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12">DA</label>
        <div class="col-md-6 col-sm-6 col-xs-12" style="display:flex;align-items:center;min-height:34px;">
            <input type="hidden" name="DA" value="0">
            <label style="display:flex;align-items:center;margin:0;font-weight:normal;">
                <input type="checkbox" name="DA" value="1" {{ $formulario->getElemento()->DA ? 'checked' : '' }} style="margin:0;">
            </label>
        </div>
    </div>
@endif

@if ($canManageRoles)
    <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12">Rols</label>
        <div class="col-md-6 col-sm-6 col-xs-12">
            <input type="hidden" name="rol[]" value="1">
            <div style="display:flex;flex-wrap:wrap;gap:8px 16px;padding-top:7px;">
                @foreach ($allRoles as $roleId => $roleName)
                    <label class="checkbox-inline" style="margin:0;display:flex;align-items:center;font-weight:normal;">
                        <input
                            type="checkbox"
                            name="rol[]"
                            value="{{ $roleId }}"
                            {{ in_array((int) $roleId, $selectedRoles) ? 'checked' : '' }}>
                        {{ __('messages.rol.' . $roleName) }}
                    </label>
                @endforeach
            </div>
        </div>
    </div>
@endif
