@php
    $selectedRoles = rolesUser($formulario->getElemento()->rol);
    $allRoles = config('roles.lor', []);
@endphp

@if (authUser()->rol % 17 === 0)
    <input type="hidden" name="DA" value="0">
    <label>
        <input type="checkbox" name="DA" value="1" {{ $formulario->getElemento()->DA ? 'checked' : '' }}>
        DA
    </label>
@endif

@foreach ($allRoles as $roleId => $roleName)
    <label class="checkbox-inline">
        <input
            type="checkbox"
            name="rol[]"
            value="{{ $roleId }}"
            {{ in_array((int) $roleId, $selectedRoles) ? 'checked' : '' }}>
        {{ $roleName }}
    </label>
@endforeach
