@php($currentValue = old($fieldName, $prefilledAnswers[$fieldName] ?? ''))

@if ($option->isNumericType())
    <div class="demo">
        <input type="text" class="js-range-slider" name="{{ $fieldName }}" value="{{ $currentValue !== '' ? $currentValue : 0 }}"
               data-min="0"
               data-max="{{ $option->scala }}"
               data-from="{{ $currentValue !== '' ? $currentValue : 0 }}" />
    </div>
    <div class="demo">
        <span id="{{ $fieldName }}" class="btn btn-danger btn-sm">No Avaluat</span>
    </div>
@elseif ($option->isSelectType())
    <div class="demo">
        <select name="{{ $fieldName }}" class="form-control">
            <option value="">Selecciona una opció</option>
            @foreach ($option->choice_values as $choice)
                <option value="{{ $choice }}" @selected((string) $currentValue === (string) $choice)>{{ $choice }}</option>
            @endforeach
        </select>
    </div>
@else
    <div class="demo">
        <textarea name="{{ $fieldName }}" rows="3" cols="150">{{ $currentValue }}</textarea>
    </div>
@endif
