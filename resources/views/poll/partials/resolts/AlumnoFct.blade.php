@extends('layouts.intranet')
@section('css')
    <title>Resultat Enquesta {{$poll->title}}</title>
@endsection
@section('content')
    <!-- page content -->
    <div class="x_content">
        @php //dd($myVotes); @endphp
        <h2>Enquesta valoració alumnes</h2>
        <table style="border: #00aeef 1px solid">
            <thead>
            <tr>
                <td>Enquesta</td>
                @foreach ($options as $item) <th>{{$item->question}} </th> @endforeach
            </tr>
            </thead>
            @foreach ($myVotes as $fct => $fctVotes)
                        <tr>
                            <td>{{Intranet\Entities\Fct::find($fct)->Colaboracion->Empresa}}</td>
                            @foreach ($fctVotes as $option)

                                    <td> {{ $option }} </td>

                            @endforeach
                        </tr>
            @endforeach
            </table>
    </div>
    <!-- /page content -->
@endsection
@section('titulo')
   Resultat enquesta {{$poll->title}}
@endsection
@section('scripts')

@endsection

