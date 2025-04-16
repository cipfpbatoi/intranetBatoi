<div class="col-md-6 col-sm-6 col-xs-12">
    <div class="x_panel">
        <div class="x_content">
            <div class="">
                <ul class="to_do">
                    @foreach($material->getVisible() as $campo)
                        <li><p> {{ ucfirst($campo) }} : {{ $material->$campo }}</p></li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
