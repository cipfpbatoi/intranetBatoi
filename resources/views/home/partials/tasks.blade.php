<ul class="messages">
    <li>
        @foreach ($tasks as $task)
            @if ($task->informativa)
                <img src="/img/informacion.jpeg" class="avatar" alt="Avatar"/>
            @else
                <img src="/img/task.png" class="avatar" alt="Avatar">
            @endif
            <div class="message_date" title="venciment">
                    <h4 class="date text-info">{{day($task->vencimiento)}}</h4>
                    <p class="month">{{month($task->vencimiento)}}</p>
            </div>
            <div class="message_wrapper">
                <h4 class="heading"><a href="/storage/{{$task->fichero}}" title="Més Informació">{{$task->descripcion}}&nbsp;&nbsp;&nbsp;&nbsp;
                        <i class="fa fa-clipboard"></i></a> &nbsp;&nbsp;&nbsp;&nbsp;
                    @if ($task->valid)
                        <a href="/task/{{$task->id}}/check" title="Tasca revisada"><i class="fa fa-check-square-o"></i></a>
                    @else
                        <a href="/task/{{$task->id}}/check" title="Tasca revisada"><i class="fa fa-square-o"></i></a>
                    @endif
                </h4>
                <br />
            </div>
        @endforeach
    </li>
</ul>