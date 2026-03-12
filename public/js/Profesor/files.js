var formulari = document.getElementById('formulari');
if (formulari) {
    formulari.addEventListener('submit', function (e) {
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
}

document.addEventListener('DOMContentLoaded', function () {
    var formPassword = document.getElementById('formPassword');
    if (!formPassword) {
        return;
    }

    formPassword.addEventListener('submit', function () {
        var passwd = document.getElementById('passwd');
        var pass = document.getElementById('pass');
        if (passwd && pass) {
            passwd.value = pass.value;
        }
    });
});
