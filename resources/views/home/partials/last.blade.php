<table class="data table table-striped no-margin">
    <thead>
        <tr>
            <th>{{ trans('validation.attributes.tabla')}}</th>
            <th>{{ trans('validation.attributes.accion')}}</th>
            <th class="hidden-phone">{{ trans('validation.attributes.id')}}</th>
            <th>{{ trans('validation.attributes.data') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($activities as $activity)
        <tr>
            <td>{{ substr($activity->model_class,18) }}</td>
            <td>{{ trans('messages.generic.'.$activity->action) }}</td>
            <td class="hidden-phone">{{ $activity->model_id }}</td>
            <td>{{ $activity->updated_at }}</td>
        </tr>
        @endforeach
    </tbody>
</table>