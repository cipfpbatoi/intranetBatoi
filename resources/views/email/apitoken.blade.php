@extends('layouts.email')
@section('body')
<table style='text-align: center'>
    <tr>
        <th>Comunicació Important per fitxatge per mòbil</th>
    </tr>
</table>
<div class="container" >
    <p>Nou enllaç per a fitxar.</p>
    <a href="{{config('contacto.host.web')}}/api/doficha?dni={{$elemento->dni}}&api_token={{$elemento->api_token}}">
     {{config('contacto.host.web')}}/api/doficha?dni={{$elemento->dni}}&api_token={{$elemento->api_token}}   
    </a>
</div>
@endsection