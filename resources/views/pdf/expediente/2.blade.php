@extends('layouts.pdf')
@section('content')
    @foreach ($todos as $elemento)
    <div class="page">
        @include('pdf.partials.cabecera')
        <br/><br/><br/>
        <div class="container" style="width:95%;clear:right;text-align: justify">
            <p><strong>COMUNICAT DE LA PÈRDUA DE L'AVALUACIÓ CONTINUA D'UN MÒDUL</strong></p>
            <p style="text-indent: 30px">Com a professor/a del grup <strong>{{$elemento->Alumno->Grupo->first()->nombre }}</strong> del curs {{ Curso() }} 
                COMUNIQUE la pèrdua de l’avaluació contínua en el procés d’ensenyament-aprenentatge del mòdul <strong>{{$elemento->Modulo->vliteral }}</strong>. 
            </p>
            <p tyle="text-indent: 30px"><strong>{{$elemento->explicacion}}</strong></p>
            <p tyle="text-indent: 30px">L’aplicació del procés d’avaluació contínua de l’alumnat requerix la seua assistència regular a les classes
                i activitats programades.  Per a això, en règim presencial, serà necessària almenys el 85% de les clases i activitats previstes 
                en cada mòdul (Normativa : Orden  79/2010, de 27 de agosto, de la Conselleria de  Educación i Normes d’inici de curs, RESOLUCIÓN de 19 de julio de 2017).</p>
            <p tyle="text-indent: 30px">La dita circumstància és acreditada i certificada pel cap d’estudis a partir dels parts de faltes d’assistència comunicades pel professorat
                que impartix docència i que l’alumne/a o tutor/a legal pot comprovar des de l’aplicació “web familia” de la Conselleria d’Educació. Aquesta situació es comunicarà de forma fidedigna a:</p>
            <p>
                @if ($elemento->Alumno->sexo == 'H')L'alumne  
                @else L'alumna  
                @endif
                {{$elemento->Alumno->FullName}}
                @php  $nac = new Jenssegers\Date\Date($elemento->Alumno->fecha_nac) @endphp
                @if ($nac->age < 18 )
                    i pares/mares o tutor/a legal de  
                    @if ($elemento->Alumno->sexo == 'H') l'alumne 
                    @else l'alumna 
                    @endif
                @endif
            </p>
        </div>
        <div class="container" style="width:90%;">
            <br/><br/>
            <br/><br/><br/>
            <p>{{config('contacto.poblacion')}},a {{$datosInforme}} </p>
            <br/><br/><br/><br/><br/>
            <div style="width:33%; float:left; ">
                <p><strong>EL/LA PROFESSOR/A:</strong></p>
                <br/><br/><br/>
                <p>Signat</p>
            </div>
            <div style="width:33%; float:left; ">
                <p><strong>L'ALUMNE:</strong></p>
                <br/><br/><br/>
                <p>Signat</p>
            </div>
            <div style="width:33%; float:left;color:grey ">
                <p><strong>VIST-I-PLAU CAP D'ESTUDIS DE FP: </strong> Rebut</p>
                <br/><br/>
                <p>Signat</p>
            </div>
        </div>
        <hr/>
        @if ($nac->age < 18 )
        <div class="container" style="width:90%;">
            <p>En/Na __________________________________________________ com a pare, mare o tutor/a legal de l’alumne/a (menor d’edat)
            _________________________________________________ quede assabentat de la pèrdua de l’avaluació contínua, amb data ___________.</p>	
										EL PARE, MARE O TUTOR/A LEGAL.
        </div>
        @endif
    </div>
    @endforeach
@endsection
