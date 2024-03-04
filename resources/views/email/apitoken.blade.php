@extends('layouts.email')
@section('body')
@php 
$programaciones = Intranet\Entities\Programacion::misProgramaciones($elemento->dni)->get();
@endphp
<table style='text-align: center'>
    <tr>
        <th>Dades Professor </th>
    </tr>
</table>
<div class="container" >
    <ul>
        <li>Codi 4 dígits:  <b>{{$elemento->codigo}}</b></li>
        <li>Enllaç per a fitxatge per mòbil:
            <a href="{{config('contacto.host.web')}}/api/doficha?dni={{$elemento->dni}}&api_token={{$elemento->api_token}}">
            {{config('contacto.host.web')}}/api/doficha?dni={{$elemento->dni}}&api_token={{$elemento->api_token}}   
            </a> 
        </li>
        <li>Les teues programacions:
            <ul>
                @forelse ($programaciones as $programacion)
                <li>
                    <a href="{{$programacion->ModuloCiclo->enlace}}">
                        Mòdul: {{$programacion->ModuloCiclo->Modulo->literal}} (<b>{{$programacion->ModuloCiclo->Ciclo->literal}}</b>)
                    </a>
                </li>
                @empty
                    <li>No tens programacions</li>
                @endforelse
            </ul>
        </li>
        @if (config('contacto.host.externo'))
            <li>Enllaç per a connectar-se des de fora del centre:
                <a href="{{config('contacto.host.externo')}}?api_token={{$elemento->api_token}}">{{config('contacto.host.externo')}}?api_token={{$elemento->api_token}}</a>
            </li>
            <li>Enllaç per a obrir aules:
                <a href="{{config('contacto.host.web')}}/api/aula?dni={{$elemento->dni}}&api_token={{$elemento->api_token}}">{{config('contacto.host.web')}}/api/aula?dni={{$elemento->dni}}&api_token={{$elemento->api_token}}</a>
            </li>
        @else
            <li>{{config('contacto.host.externo')}}</li>
        @endif
    </ul>
    <p></p>
    
</div>
@endsection