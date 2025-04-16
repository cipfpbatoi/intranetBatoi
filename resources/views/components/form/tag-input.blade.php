<div class="control-group">
    <label class="control-label col-md-3 col-sm-3 col-xs-12">
        @lang('validation.attributes.tags')
    </label>
    <div class="col-md-9 col-sm-9 col-xs-12">
        <input
                name="{{ $name }}"
                id="tags_1"
                type="text"
                class="tags form-control"
                value="{{ old($name, $value) }}"
        />
    </div>
</div>