<x-layouts.app  title="Panell de Control">
    <div class="col-md-11 col-sm-11 col-xs-12">
        <x-user-tabs :tabs="[
            ['title' => 'messages.generic.nextActivities', 'view' => 'home.partials.activities', 'data' => ['actividades' => $actividades]],
            ['title' => 'messages.generic.timeTable', 'view' => 'home.partials.horario.grupo', 'data' => ['horario' => $horario]],
            ['title' => 'messages.generic.calendari', 'view' => 'home.partials.calendari' ],
        ]" />
    </div>
</x-layouts.app>


