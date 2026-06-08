<x-layouts.app :title="$message">
    <x-modal name="dialogo" title='{{$message}}'
             message='SI'  cancel="NO" dismiss='0' >
        @include('intranet.partials.components.showFields',['fields' => $element->showConfirm()])
    </x-modal>
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var modalElement = document.getElementById('dialogo');
                if (!modalElement) {
                    return;
                }

                if (window.bootstrap && window.bootstrap.Modal) {
                    window.bootstrap.Modal.getOrCreateInstance(modalElement).show();
                }

                var confirmButton = modalElement.querySelector('button.btn.btn-primary');
                if (confirmButton) {
                    confirmButton.addEventListener('click', function (event) {
                        event.preventDefault();
                        window.location.href = '{{ $route }}';
                    });
                }

                var cancelButton = modalElement.querySelector('button.btn.btn-secondary');
                if (cancelButton) {
                    cancelButton.addEventListener('click', function (event) {
                        event.preventDefault();
                        window.location.href = '{{ $back }}';
                    });
                }
            });
        </script>
    @endpush
</x-layouts.app>
