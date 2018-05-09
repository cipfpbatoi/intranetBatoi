'use strict';


var app = new Vue({
    el: '#gestion',
    data: {
        dia: '',
        horario: [],
        idProfesor: $('#dni').text(),
        token: $("#_token").text()
    },
//    components: {
//      Datepicker
//    },
    methods: {
        elige: function () {
            var diaF = toEnglish(this.dia);
            axios.get('/api/itaca/' + diaF + '/' + this.idProfesor + '?api_token=' + this.token).then((response) => {
                this.horario = response.data.data;
            }, (error) => {
                console.log(error);
            });
        },
        confirmar: function () {
            let req = {
                url: '/api/itaca?api_token=' + this.token,
                method: 'POST',
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