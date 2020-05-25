$(function(){
	$('.editGrupo').on("click",editRow);
        
})

function editRow() {
        cancelEdit();
        var contenido =`<div id="alerts"></div>
                    <div class="btn-toolbar editor" data-role="editor-toolbar" data-target="#area">
                        
                    <div class="btn-group">
                      <a class="btn dropdown-toggle" data-toggle="dropdown" title="Font Size"><i class="fa fa-text-height"></i>&nbsp;<b class="caret"></b></a>
                      <ul class="dropdown-menu">
                        <li>
                          <a data-edit="fontSize 5">
                            <p style="font-size:17px">Huge</p>
                          </a>
                        </li>
                        <li>
                          <a data-edit="fontSize 3">
                            <p style="font-size:14px">Normal</p>
                          </a>
                        </li>
                        <li>
                          <a data-edit="fontSize 1">
                            <p style="font-size:11px">Small</p>
                          </a>
                        </li>
                      </ul>
                    </div>

                    <div class="btn-group">
                      <a class="btn" data-edit="bold" title="Bold (Ctrl/Cmd+B)"><i class="fa fa-bold"></i></a>
                      <a class="btn" data-edit="italic" title="Italic (Ctrl/Cmd+I)"><i class="fa fa-italic"></i></a>
                      <a class="btn" data-edit="strikethrough" title="Strikethrough"><i class="fa fa-strikethrough"></i></a>
                      <a class="btn" data-edit="underline" title="Underline (Ctrl/Cmd+U)"><i class="fa fa-underline"></i></a>
                    </div>

                    <div class="btn-group">
                      <a class="btn" data-edit="insertunorderedlist" title="Bullet list"><i class="fa fa-list-ul"></i></a>
                      <a class="btn" data-edit="insertorderedlist" title="Number list"><i class="fa fa-list-ol"></i></a>
                      <a class="btn" data-edit="outdent" title="Reduce indent (Shift+Tab)"><i class="fa fa-dedent"></i></a>
                      <a class="btn" data-edit="indent" title="Indent (Tab)"><i class="fa fa-indent"></i></a>
                    </div>

                    <div class="btn-group">
                      <a class="btn" data-edit="justifyleft" title="Align Left (Ctrl/Cmd+L)"><i class="fa fa-align-left"></i></a>
                      <a class="btn" data-edit="justifycenter" title="Center (Ctrl/Cmd+E)"><i class="fa fa-align-center"></i></a>
                      <a class="btn" data-edit="justifyright" title="Align Right (Ctrl/Cmd+R)"><i class="fa fa-align-right"></i></a>
                      <a class="btn" data-edit="justifyfull" title="Justify (Ctrl/Cmd+J)"><i class="fa fa-align-justify"></i></a>
                    </div>

                    <div class="btn-group">
                      <a class="btn dropdown-toggle" data-toggle="dropdown" title="Hyperlink"><i class="fa fa-link"></i></a>
                      <div class="dropdown-menu input-append">
                        <input class="span2" placeholder="URL" type="text" data-edit="createLink" />
                        <button class="btn" type="button">Add</button>
                      </div>
                      <a class="btn" data-edit="unlink" title="Remove Hyperlink"><i class="fa fa-cut"></i></a>
                    </div>

                    <div class="btn-group">
                      <a class="btn" data-edit="undo" title="Undo (Ctrl/Cmd+Z)"><i class="fa fa-undo"></i></a>
                      <a class="btn" data-edit="redo" title="Redo (Ctrl/Cmd+Y)"><i class="fa fa-repeat"></i></a>
                    </div>
                  </div>

                  <div id="area" class="editor-wrapper">`;
	$(this).parents('tr').children().each(function(index, celda) {
		var $span=$(celda).children();
		$span.hide();
		switch ($span.attr('class')) {
			case 'none' :
				$span.after('<span class="editando">'+$span.text()+'</span>');
				break;
			case 'input':
				$span.after('<span class="editando"><input type="text" class="form-control" name="'
					+$span.attr('name')+'" value="'+$span.text()+' maxlenght=200"></span>');
				break;
			case 'select':
				$span.after('<span class="editando"><select id="'+$span.attr('name')+'" class="form-control" name="'
					+$span.attr('name')+'"></select></span>');
				$.each(options[$span.attr('name')],(index,value) => {
					if (index == $span.text())
						$("#"+$span.attr('name')).append("<option value='"+index+"' selected>"+value+"</option>")
					else
						$("#"+$span.attr('name')).append("<option value='"+index+"'>"+value+"</option>")
				});
				break;
			case 'textarea':
				var nombre = '#'+$span.attr('name');
				$span.after('<span class="editando"><textarea name="'
	+$span.attr('name')+'" id="'+$span.attr('name')+'" style="display:none;"></textarea><div>'+contenido+$span.html()+'</div>');
				$('#area').wysiwyg();
				$('#area').focus();
				$('#area').focusout(function() {
					$(nombre).val($('#area').html());
				});
				break;
			case 'botones':
				$span.after('<span class="editando"><a href="#" class="imgButton" id="edit-ok">'
					+'<i class="fa fa-check" alt="Aceptar" title="Aceptar"></i></a>'
					+'<a href="#" class="imgButton" id="edit-cancel">'
					+'<i class="fa fa-close" alt="Cancelar" title="Cancelar"></i></a></span>');
				break;				
		}
		$('#edit-cancel').on("click", cancelEdit);
		$('#edit-ok').on("click", saveEdit);
//		celda.hide();
	})
}

function cancelEdit(ev) {
	if (ev)
		ev.preventDefault();
	$('.editando').prev().show().end().remove();
}
function saveEdit(ev) {
        var token = $("#_token").text();
 	var datos={};
 	var id=$(this).parents('tr').attr('id');
        var tabla=$(this).parents('table').attr('name');
        
	ev.preventDefault();
 	$('.editando').children().each(function(index, span) {
 		if ($(span).attr('name'))
	 		datos[$(span).attr('name')]=$(span).val();
 	});
        datos['api_token']=token;
 	$.ajax({
 		url: "/api/"+tabla+"/"+id,
 		method: "PUT",
 		data: datos
 	}).then(function(res) {
 		console.log(res);
 		if (res.message=="OK" || res.success ) {
			$('.editando').each(function(index, span) {
				$anterior=$(span).prev();
				$anterior.show();
				if ($anterior.attr('class')!='botones')
					if ($anterior.attr('class') != 'select')
						$anterior.html($(span).children().val() );
					else
						$anterior.html(options[$anterior.attr('name')][$(span).children().val()]);
			});
			cancelEdit();
 		}
 	}, function(res) {
 		console.log(res);
 	})
 }