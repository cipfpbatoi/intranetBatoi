<div class="x_content">
    <form action='/fichar' method="POST">
        {{ csrf_field() }}
        <input type="text" id='codigo' name='codigo' autofocus />
        <input type="submit" value='Ficha' />
    </form>
</div>
<div class="x_content" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
    <label for="turnoFiltro" style="margin:0;"><strong>Veure:</strong></label>
    <label style="margin:0;display:inline-flex;align-items:center;gap:6px;">
        <input type="checkbox" id="turnoFiltroManana" />
        <span>Mañana (inici fins 13:45)</span>
    </label>
    <label style="margin:0;display:inline-flex;align-items:center;gap:6px;">
        <input type="checkbox" id="turnoFiltroTarde" />
        <span>Tarde (inici despres 13:45)</span>
    </label>
    <button type="button" id="turnoFiltroReset" class="btn btn-default btn-sm">Netejar</button>
</div>
<div class="x_content">
    {!! \Intranet\Services\UI\AppAlert::render() !!}
</div>
