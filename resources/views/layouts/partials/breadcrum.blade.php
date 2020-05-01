<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">Inici</a></li>
        @foreach (session('breadCrum') as $opcio)
            <li class="breadcrumb-item"><a href="#">{{$opcio}}</a></li>
        @endforeach


    </ol>
</nav>