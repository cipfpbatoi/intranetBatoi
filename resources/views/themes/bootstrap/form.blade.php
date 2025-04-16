<x-form.dynamic-model-form
        :model="$elemento"
        :method="$method"
        :fillable="$fillable"
        :defaults="$default"
        :formulario="$formulario"
 >
    @isset($afterView)
        @include($afterView,['formulario' => $formulario])
    @endisset
</x-form.dynamic-model-form>
