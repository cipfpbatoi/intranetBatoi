 {!! Field::$type($name, $value, $params) !!}

@if (!empty($params['disabled']) && $params['disabled'] === 'disabled')
    {!! Field::hidden($name, null, []) !!}
@endif