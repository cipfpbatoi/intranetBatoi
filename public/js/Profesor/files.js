document.getElementById('formulari').addEventListener('submit', function(e) {
    e.preventDefault(); // prevenir comportament per defecte

    var certificat = document.getElementById('certificat');

    // comprovar si s'ha carregat el fitxer que requereix la contrasenya
    if (certificat.files.length > 0){
        if (certificat.files[0].type === 'application/x-pkcs12') {
            // demanar contrasenya
            this.submit();

        } else {
            alert('El fitxer no és un certificat digital');
            return;
        }
    }
});

$(function() {
    $("#formPassword").on("submit", function(){
        $('#passwd').value($('#pass').value);
    });
});