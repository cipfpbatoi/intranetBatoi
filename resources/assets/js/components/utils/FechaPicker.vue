<template>
  <date-picker :value="value" @change="updateValue($event)" :date="startTime" :option="option" :limit="limit"></date-picker>
</template>

<script>
import myDatepicker from 'vue-datepicker'

    export default {
        components: {
          'date-picker': myDatepicker,
        },
        props: ['value', 'fechaIni', 'fechaFin'],
        data() {
          return {
            // Datepicker 
            startTime: {
              time: ''
            },
            endtime: {
              time: ''
            },
       
            option: {
              type: 'day',
              week: ['Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab', 'Dom'],
              month: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Juni', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
              format: 'YYYY-MM-DD',
              placeholder: 'Introduce la fecha',
              inputStyle: {
                'display': 'inline-block',
                'padding': '6px',
                'line-height': '22px',
                'font-size': '16px',
                'border': '2px solid #fff',
                'box-shadow': '0 1px 3px 0 rgba(0, 0, 0, 0.2)',
                'border-radius': '2px',
                'color': '#5F5F5F'
              },
              color: {
                header: '#ccc',
                headerText: '#f00'
              },
              buttons: {
                ok: 'Ok',
                cancel: 'Cancel'
              },
              overlayOpacity: 0.5, // 0.5 as default 
              dismissible: true // as true as default 
            },
            timeoption: {
              type: 'min',
              week: ['Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su'],
              month: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
              format: 'YYYY-MM-DD HH:mm'
            },
            multiOption: {
              type: 'multi-day',
              week: ['Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su'],
              month: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
              format:"YYYY-MM-DD HH:mm"
            },
            limit: [
              {
                type: 'weekday',
                available: [1, 2, 3, 4, 5]
              },
              {
                type: 'fromto',
                from: this.fechaIni,
                to: this.fechaFin,
              }
            ],
          }
        },
        mounted() {
          console.log('val '+this.value)
          console.log('Fin '+this.fechaFin)
        },
        methods: {
          updateValue(value) {
            this.$emit('input', value);
          },
        }
      }  
</script>