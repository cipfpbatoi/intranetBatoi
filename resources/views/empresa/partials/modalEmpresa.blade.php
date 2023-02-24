<!-- Modal -->
<x-modal
        name="AddEnterprise"
        title='Afegir Empresa des de Centre'
        action="#"
        message='{{ trans("messages.buttons.confirmar")}}'
>
        <input type='text'
               name='cif'
               placeholder='CIF'
               value="{{ old('cif') }}"
               class='form-control' />
        <input type='text'
               name='concierto'
               placeholder='@lang("validation.attributes.concierto") *'
               value="{{ old('concierto') }}"
               class='form-control' />
        <input type='text'
               name='email'
               placeholder='@lang("validation.attributes.email") *'
               value="{{ old('email') }}"
               class='form-control' />
        <input type='text'
               name='telefono'
               placeholder='@lang("validation.attributes.telefono") *'
               value="{{ old('telefono') }}"
               class='form-control' />
</x-modal>
