<ul class="messages">
    @foreach ($tasks as $task)
        @if (!$task->valid || fechaInglesa($task->vencimiento) > hoy())
            <x-llist image="{{$task->image}}" date="{{$task->vencimiento}}">
                <a href="{{$task->link}}" title="Més Informació">
                    {{$task->descripcion}}&nbsp;&nbsp;&nbsp;&nbsp;
                    <em class="fa fa-clipboard"></em>
                </a>
                <a href="{{ route('task.check', ['id' => $task->id]) }}" title="Tasca revisada">
                    @if ($task->valid)
                        <em class="fa fa-check-square-o"></em>
                    @else
                        <em class="fa fa-square-o"></em>
                    @endif
                </a>&nbsp;
            </x-llist>
        @endif
    @endforeach
</ul>
