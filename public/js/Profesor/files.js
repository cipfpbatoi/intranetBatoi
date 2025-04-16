document.getElementById('formulari').addEventListener('submit', function(e) {
    e.preventDefault(); // prevenir comportament per defecte

    var certificat = document.getElementById('certificat'); // assegura’t que coincideix amb l’ID de l’input

    if (certificat && certificat.files.length > 0) {
        if (certificat.files[0].type === 'application/x-pkcs12') {
            // demanar contrasenya
            this.submit();
        } else {
            alert('El fitxer no és un certificat digital');
        }
    } else {
        this.submit();
    }
});

$(function() {
    $("#formPassword").on("submit", function(){
        // Correcció: usem .val(...)
        $('#passwd').val( $('#pass').val() );
    });
});
