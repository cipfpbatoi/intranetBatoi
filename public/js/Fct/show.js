$(function () {
    $('a.fa-user').on("click", function(){
        if (!confirm("Vas a assignar-te la cotutoria d'esta FCT.\n" +
            "Podràs contactar amb este centre de treball encara que no tingues alumnes assignats.\n" +
            "El cotutor actual deixarà de vore esta fct sinó te cap alumne assignat")) {
            event.preventDefault();
        }
    });
});