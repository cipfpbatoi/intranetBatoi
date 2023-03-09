$(function () {
    $('input.fa-user').on("click", function(){
        if (!confirm("Vas a canviar cotutoria d'esta FCT.\n" +
            "Aquell al que has assignat podrà contactar amb este centre de treball encara que no tinga alumnes assignats.\n" +
            "El cotutor actual deixarà de vore esta fct sinó te cap alumne assignat")) {
            event.preventDefault();
        }
    });
});