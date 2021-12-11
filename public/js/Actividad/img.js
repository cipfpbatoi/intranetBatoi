Dropzone.options.dropzone =
    {
        maxFiles: 3,
        maxFilesize: 4,
        acceptedFiles: ".jpeg,.jpg,.png,.gif",
        addRemoveLinks: true,
        timeout: 50000,
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        init:function() {

            // Get images
            var myDropzone = this;
            var actividad = $('#idctividad').attr('value');
            $.ajax({
                url: 'actividad/'+actividad+'/getFiles',
                type: 'GET',
                dataType: 'json',
                success: function(data){
                    //console.log(data);
                    $.each(data, function (key, value) {

                        var file = {name: value.name, size: value.size};
                        myDropzone.options.addedfile.call(myDropzone, file);
                        myDropzone.options.thumbnail.call(myDropzone, file,'/storage/Extraescolars/');
                        myDropzone.emit("complete", file);
                    });
                }
            });
        },
        removedfile: function(file)
        {
            if (this.options.dictRemoveFile) {
                return Dropzone.confirm("Are You Sure to "+this.options.dictRemoveFile, function() {
                    if(file.previewElement.id != ""){
                        var name = file.previewElement.id;
                    }else{
                        var name = file.name;
                    }
                    //console.log(name);
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: 'PUT',
                        url: delete_url,
                        data: {filename: name},
                        success: function (data){
                            alert(data.success +" File has been successfully removed!");
                        },
                        error: function(e) {
                            console.log(e);
                        }});
                    var fileRef;
                    return (fileRef = file.previewElement) != null ?
                        fileRef.parentNode.removeChild(file.previewElement) : void 0;
                });
            }
        },

        success: function(file, response)
        {
            file.previewElement.id = response.success;
            //console.log(file);
            // set new images names in dropzone’s preview box.
            var olddatadzname = file.previewElement.querySelector("[data-dz-name]");
            file.previewElement.querySelector("img").alt = response.success;
            olddatadzname.innerHTML = response.success;
        },
        error: function(file, response)
        {
            if($.type(response) === "string")
                var message = response; //dropzone sends it's own error messages in string
            else
                var message = response.message;
            file.previewElement.classList.add("dz-error");
            _ref = file.previewElement.querySelectorAll("[data-dz-errormessage]");
            _results = [];
            for (_i = 0, _len = _ref.length; _i < _len; _i++) {
                node = _ref[_i];
                _results.push(node.textContent = message);
            }
            return _results;
        }

    };