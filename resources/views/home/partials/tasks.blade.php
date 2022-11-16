<ul class="messages">
    @foreach ($tasks as $task)
        <li>
            @if (!$task->valid || $task->vencimiento > hoy())
                @if ($task->vencimiento <= hoy())
                    <img src="/img/warning.png" class="avatar" alt="Avatar"/>
                @else
                    @if ($task->informativa)
                        <img src="/img/informacion.jpeg" class="avatar" alt="Avatar"/>
                    @else
                        <img src="/img/task.png" class="avatar" alt="Avatar">
                    @endif
                @endif
                <div class="message_date" title="venciment">
                    <h4 class="date text-info">{{day($task->vencimiento)}}</h4>
                    <p class="month">{{month($task->vencimiento)}}</p>
                </div>

                <div class="message_wrapper">
                    <h4 class="heading">
                        @if ($task->fichero)
                            <a href="/storage/{{$task->fichero}}" title="Més Informació">{{$task->descripcion}}&nbsp;&nbsp;&nbsp;&nbsp;
                                <em class="fa fa-clipboard"></em></a> &nbsp;
                        @else
                            <a href="{{$task->enlace}}" title="Més Informació">{{$task->descripcion}}&nbsp;&nbsp;&nbsp;&nbsp;
                                <em class="fa fa-clipboard"></em></a> &nbsp;
                        @endif &nbsp;&nbsp;&nbsp;
                        @if ($task->valid)
                            <a href="/task/{{$task->id}}/check" title="Tasca revisada"><em
                                        class="fa fa-check-square-o"></em></a>
                        @else
                            <a href="/task/{{$task->id}}/check" title="Tasca revisada"><em class="fa fa-square-o"></em></a>
                        @endif
                    </h4>
                    <br/>
                </div>
            @endif
        </li>
    @endforeach
</ul>