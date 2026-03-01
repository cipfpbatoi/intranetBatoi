'use strict';

var id;

$(function () {
    var id = $("#fct_id").text();
    $('input.fa-user').on("click", function(event){
        if (!confirm("Vas a canviar cotutoria d'esta FCT.\n" +
            "Aquell al que has assignat podrà contactar amb este centre de treball encara que no tinga alumnes assignats.\n" +
            "El cotutor actual deixarà de vore esta fct sinó te cap alumne assignat")) {
            event.preventDefault();
        }
    });
    $('a.fa-unlink').on("click", function(event){
        if (!confirm("Vas a deslligar la FCT del SAO. L'hauràs de tornar a importar. Estas segur?")) {
            event.preventDefault();
        }
    });
    $(".alumnat").on("click",function(event){
        event.preventDefault();
        $(this).attr("data-toggle","modal").attr("data-target", "#dialogo_alumno").attr("href","");
        $.ajax({
            url: '/fct/' + id + '/alFct',
            method: 'GET',
            headers: apiAuthOptions().headers,
            data: apiAuthOptions().data,
            success: function(response) {
                let fctAl = response.data;  // Suposant que la resposta té una propietat 'alumnes'
                let select = $("#alumnoFct");
                select.empty();  // Buida el select abans de carregar els nous alumnes

                fctAl.forEach(function(alumne) {
                    let option = $("<option></option>").attr("value", alumne.id).text(alumne.nombre);
                    select.append(option);
                });

                // Un cop els alumnes estan carregats, mostra el modal
                $("#dialogo_alumno").modal("show");
            },
            error: function(error) {
                console.error("Error en obtenir els alumnes:", error);
            }
        });
    });
    $("#formDialogo_alumno").on("submit", function(event){
        event.preventDefault();
        let alumnoFctSelect = this.alumnoFct;
        let alumnoFctId = alumnoFctSelect.value;
        let alumnoFctName = $(alumnoFctSelect).find("option:selected").text();

        let auth = apiAuthOptions({
            explicacion: this.explicacion.value
        });

        $.ajax({
            method: "POST",
            url: "/fct/" + this.alumnoFct.value + "/alFct",
            headers: auth.headers,
            data: auth.data
        }).then(function (result) {
            let contacte = result.data;

            let date = new Date(contacte.created_at);
            let formattedDate = date.getFullYear() + '-' +
                String(date.getMonth() + 1).padStart(2, '0') + '-' +
                String(date.getDate()).padStart(2, '0') + ' ' +
                String(date.getHours()).padStart(2, '0') + ':' +
                String(date.getMinutes()).padStart(2, '0') + ':' +
                String(date.getSeconds()).padStart(2, '0');

            let newListItem = $('<li></li>').append(
                $('<div></div>').addClass('message_wrapper').append(
                    $('<h5></h5>').html(
                        `<em class="fa fa-calendar user-profile-icon"></em> ${formattedDate}
                    <em class="fa fa-exclamation"></em>${contacte.document}
                    <em class="fa fa-user user-profile-icon"></em> ${alumnoFctName}
                    ${contacte.comentari ? `<br/>${contacte.comentari}` : ''}`
                    )
                )
            );

            // Afegeix el nou element a la llista
            $("#ul_llist").append(newListItem);
            $("#dialogo_alumno").modal('hide');

        }, function () {
            console.log("Només es pot un per dia");
            $("#dialogo_alumno").modal('hide');
        });
    });
});
