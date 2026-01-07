@php
    $fields = method_exists($elemento, 'getVisible') && !empty($elemento->getVisible())
        ? $elemento->getVisible()
        : ($elemento->getFillable() ?? []);
@endphp

<x-layouts.app :title="__('models.' . $modelo . '.show')">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
            <div class="x_title">
                <h2>{{ __('models.' . $modelo . '.show') }}</h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <ul class="to_do">
                    @forelse($fields as $campo)
                        <li>
                            <p>
                                <strong>@lang('validation.attributes.' . $campo):</strong>
                                {{ data_get($elemento, $campo) }}
                            </p>
                        </li>
                    @empty
                        <li class="text-muted">@lang('messages.generic.nodata')</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</x-layouts.app>
