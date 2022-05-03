<div class="x_content">
    <select id="periode">
        <option value="0" selected>Tots</option>
        @foreach (config('auxiliares.periodesFct') as $key => $value)
            <option value="{{$key}}">{{$value}}</option>
        @endforeach
    </select>
</div>