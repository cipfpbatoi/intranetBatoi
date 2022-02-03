
Dropzone.options.myDropzone = {
    autoProcessQueue: true,
    uploadMultiple: true,
    maxFilezise: 10,
    parallelUploads: 5,
    addRemoveLinks: true,
    timeout: 50000,
    method: 'POST',
    acceptedFiles: 'application/pdf,application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    previewsContainer: ".dropzone-previews",
    createImageThumbnails: true,
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },

    init: function() {
        var expediente = $('#id').attr('value');
        myDropzone = this;

        this.on("complete", function(file) {
            $(".dz-remove").html(
                "<div>" +
                "<span class='fa fa-trash text-danger' style='font-size: 1.5em'></span>" +
                "</div>");
            $(".dz-error-message").html("");
        });

        $.ajax({
            url: '/api/expediente/'+expediente+'/getFiles',
            type: 'GET',
            dataType: 'json',
            data: {api_token: $("#_token").text()},
            success: function(data){
                $.each(data.data, function (key, mockFile) {
                    myDropzone.emit("addedfile", mockFile);
                    myDropzone.createThumbnailFromUrl(mockFile,'/storage/Expedientes/'+expediente+'/'+mockFile.name);
                    myDropzone.emit("success", mockFile);
                    myDropzone.files.push(mockFile);
                    myDropzone.emit("complete", mockFile);
                });
            },
        });
        this.on("addedfile", function (file) {
            if (file.size == 0) {
                this.removeFile(file);
                return;
            } else {
                var a = document.createElement('a');
                a.setAttribute('style','float:right');
                a.setAttribute('href', '/storage/Expedientes/'+expediente+'/'+file.name);
                a.setAttribute('target', "_blank");
                a.innerHTML = "<em class='fa fa-download'></em>";
                file.previewTemplate.appendChild(a);
                myDropzone.processQueue();
            }
        });
        this.on("maxfilesexceeded", function(file) {
            this.removeFile(file);
            alert("No pots pujar més de cinc fitxers");
        });
        this.on("success",
            myDropzone.processQueue.bind(myDropzone)
        );
        this.on("removedfile", function(file){
            $.ajax({
                    url: '/api/expediente/'+expediente+'/removefile/'+file.name,
                    type: 'GET',
                    dataType: 'json',
                    data: {api_token: $("#_token").text()},
            });
        })
    },
};




