<div class="x_content">
    <form action='/fichar' method="POST">
        {{ csrf_field() }}
        <input type="text" id='codigo' name='codigo' autofocus />
        <input type="submit" value='Ficha' />
    </form>
</div>
<div class="x_content">
    {!! \Intranet\Services\UI\AppAlert::render() !!}
</div>