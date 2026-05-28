<!-- Modal Nou -->
<x-modal name="A3A" title='Selecciona elements' action="/signatura/A3/send"
         message='{{ __("messages.buttons.confirmar")}}'>
        <table id="tableA3"></table>
</x-modal>
{{ Html::script("/js/common/api-auth.js", ['defer' => true]) }}
{{ Html::script("/js/selDoc.js", ['defer' => true]) }}
{{ Html::script("/js/taulaCheck.js", ['defer' => true]) }}
