$(function() {
    $("#evaluacion_id").change(function (event) {
        if ($("#evaluacion_id").val() != 3)
            $('#field_adquiridosNO_id').hide();
        else
            $('#field_adquiridosNO_id').show();
    });
});

function postModal() {
    var tipo = $("#evaluacion_id").val();
    if (tipo == 3) {
        $('#field_adquiridosNO_id').show();
    } else {
        $('#field_adquiridosNO_id').hide();
    }
}
