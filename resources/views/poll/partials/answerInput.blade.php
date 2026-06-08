@if ($option->isNumericType())
    <div class="demo">
        <input type="text" class="js-range-slider" name="{{ $fieldName }}" value=""
               data-min="0"
               data-max="{{ $option->scala }}"
               data-from="0"
               data-
               />
    </div>
    <div class="demo">
        <span id="{{ $fieldName }}" class="btn btn-danger btn-sm">No Avaluat</span>
    </div>
@elseif ($option->isSelectType())
    <div class="demo">
        <select name="{{ $fieldName }}" class="form-control">
            <option value="">Selecciona una opció</option>
            @foreach ($option->choice_values as $choice)
                <option value="{{ $choice }}">{{ $choice }}</option>
            @endforeach
        </select>
    </div>
@else
    <div class="demo">
        <textarea name="{{ $fieldName }}" rows="3" cols="150"></textarea>
    </div>
@endif
