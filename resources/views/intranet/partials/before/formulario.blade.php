<div class="x_content">
    <form action='/fichar' method="POST">
        {{ csrf_field() }}
        <input type="text" id='codigo' name='codigo' autofocus />
        <input type="submit" value='Ficha' />
    </form>
</div>
<div class="x_content" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
    <label for="turnoFiltro" style="margin:0;"><strong>Veure:</strong></label>
    <select id="turnoFiltro" class="form-control" style="max-width:220px;display:inline-block;">
        <option value="todos">Todos</option>
        <option value="manana">Mañana (inici fins 13:45)</option>
        <option value="tarde">Tarde (inici despres 13:45)</option>
    </select>
    <button type="button" id="turnoFiltroReset" class="btn btn-default btn-sm">Netejar</button>
</div>
<div class="x_content">
    {!! \Intranet\Services\UI\AppAlert::render() !!}
</div>
