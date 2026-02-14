@switch($type)
    @case('file')
        <x-form.file-input
                :name="$name"
                :label="$label"
                :current-file="$currentFile"
                :params="$params"
        />
        @break

    @case('tag')
        <x-form.tag-input
                :name="$name"
                :value="$value"
        />
        @break

    @default
        <x-form.generic-field
                :name="$name"
                :type="$type"
                :value="$value"
                :params="$params"
        />
@endswitch
