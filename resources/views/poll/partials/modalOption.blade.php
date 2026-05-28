<!-- Modal -->
<div class="modal fade" id="AddOption" tabindex="-1" role="dialog" aria-labelledby="AddOptionTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="AddOptionTitle">@lang("models.modelos.Option")</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" class="agua" action="/option/create">
                <div class="modal-body">
                    @csrf
                    <input type='text' name='question' placeholder='@lang("validation.attributes.question")' value="{{ old('question') }}" class='form-control' />
                    <input type='text' name='scala' placeholder='@lang("validation.attributes.scala") *' value="{{ old('scala') }}" class='form-control' />
                    <input type='hidden' name='ppoll_id' value="{!!$elemento->id!!}">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <input id="submit" class="btn btn-primary" type="submit" value="@lang('messages.generic.anadir') @lang('models.modelos.Option')"  />
                </div>
            </form>    
        </div>
    </div>
</div>
