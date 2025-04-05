<x-layouts.app  title="Panell de Control">
    <x-user-profile :usuario="$usuario" />
    <div class="col-md-9 col-sm-9 col-xs-12">
        <x-user-tabs :tabs="[
            ['title' => 'messages.generic.nextActivities', 'view' => 'home.partials.activities', 'data' => ['actividades' => $actividades]],
            ['title' => 'messages.generic.timeTable', 'view' => 'home.partials.horario.grupo', 'data' => ['horario' => $horario]],
            ['title' => 'messages.generic.calendari', 'view' => 'home.partials.calendari' ],
        ]" />
    </div>
</x-layouts.app>


