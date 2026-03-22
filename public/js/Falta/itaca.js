'use strict';

function apiAuthOptions(extraData) {
    var legacyToken = $.trim($("#_token").text());
    var bearerToken = $.trim($('meta[name="user-bearer-token"]').attr('content') || "");
    var data = extraData || {};
    var headers = {};

    if (bearerToken) {
        headers.Authorization = "Bearer " + bearerToken;
    }
    if (legacyToken) {
        data.api_token = legacyToken;
    }

    return { headers: headers, data: data };
}

var app = new Vue({
    el: '#gestion',
    data: {
        dia: '',
        horario: [],
        idProfesor: $('#dni').text()
    },
//    components: {
//      Datepicker
//    },
    methods: {
        elige: function () {
            var diaF = toEnglish(this.dia);
            var auth = apiAuthOptions();
            axios.get('/api/itaca/' + diaF + '/' + this.idProfesor, {
                headers: auth.headers,
                params: auth.data
            }).then((response) => {
                this.horario = response.data.data;
            }, (error) => {
                console.log(error);
            });
        },
        confirmar: function () {
            var auth = apiAuthOptions();
            let req = {
                url: '/api/itaca',
                method: 'POST',
                headers: auth.headers,
                params: auth.data,
                data: this.horario
            };
            axios(req).then(response => {
                console.log(response.data.content);
                alert('Guardat amb exit');
            }, (error) => {
                console.log(error);
            });
        }
    }
});


function diaSemana(date) {
    var dias_semana = ["D", "L", "M", "X", "J", "V", "S"];
    var fechaSel = new Date(date)
    return dias_semana[fechaSel.getDay()];
}
function toEnglish(date) {
    let arrFecha = date.split('-');
    arrFecha = arrFecha.map(dato => (dato.length == 1) ? "0" + dato : dato);
    return arrFecha[2] + '-' + arrFecha[1] + '-' + arrFecha[0];
}
