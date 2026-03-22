<table class="data table table-striped no-margin">
    <thead>
        <tr>
            <th>{{ __('validation.attributes.tabla')}}</th>
            <th>{{ __('validation.attributes.accion')}}</th>
            <th class="hidden-phone">{{ __('validation.attributes.id')}}</th>
            <th>{{ __('validation.attributes.data') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($activities as $activity)
        <tr>
            <td>{{ substr($activity->model_class,18) }}</td>
            <td>{{ __('messages.generic.'.$activity->action) }}</td>
            <td class="hidden-phone">{{ $activity->model_id }}</td>
            <td>{{ $activity->updated_at }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
