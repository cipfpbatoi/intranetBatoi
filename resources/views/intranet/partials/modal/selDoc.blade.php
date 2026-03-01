<!-- Modal Nou -->
<x-modal name="A3A" title='Selecciona elements' action="/signatura/A3/send"
         message='{{ trans("messages.buttons.confirmar")}}'>
        <table id="tableA3"></table>
</x-modal>
{{ Html::script("/js/common/api-auth.js") }}
{{ Html::script("/js/selDoc.js") }}
{{ Html::script("/js/taulaCheck.js") }}
