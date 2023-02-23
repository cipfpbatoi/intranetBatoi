Dropzone.options.myDropzone = {
    autoProcessQueue: true,
    uploadMultiple: true,
    maxFilesize: 10,
    parallelUploads: 5,
    addRemoveLinks: true,
    timeout: 50000,
    method: 'POST',
    url: '/api/attachFile',
    acceptedFiles: 'application/pdf,application/vnd.oasis.opendocument.text',
    previewsContainer: ".dropzone-previews",
    disablePreviews: true,
    dictRemoveFileConfirmation: "Vas a esborrar el fitxer",
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },

    init: function() {
        var expediente = $('#id').attr('value');
        var modelo = $('#modelo').attr('value');
        myDropzone = this;

        this.on("complete", function(file) {
            $(".dz-remove").html(
                "<div>" +
                "<span class='fa fa-trash text-danger' style='font-size: 1.5em'></span>" +
                "</div>");
            $(".dz-error-message").html("");
        });

        $.ajax({
            url: '/api/getAttached/'+modelo+'/'+expediente,
            type: 'GET',
            dataType: 'json',
            data: {api_token: $("#_token").text()},
            success: function(data){
                $.each(data.data, function (key, mockFile) {
                    myDropzone.emit("addedfile", mockFile);
                    myDropzone.createThumbnailFromUrl(mockFile,'/storage/adjuntos/'+modelo+'/'+expediente+'/'+mockFile.name);
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
                myDropzone.processQueue();
                var a = document.createElement('a');
                a.setAttribute('style','float:right');
                if (file.referencesTo) {
                    a.setAttribute('href',file.referencesTo);
                } else {
                    if (file.file) {
                        a.setAttribute('href', '/storage/adjuntos/' + modelo + '/' + expediente + '/' + file.file);
                    } else {
                        a.setAttribute('href', '/readFileByName/' + file.name);
                    }
                }
                a.setAttribute('target', "_blank");
                a.innerHTML = "<em class='fa fa-download'></em>";
                file.previewTemplate.appendChild(a);
            }
        });
        this.on("maxfilesexceeded", function(file) {
            this.removeFile(file);
            alert("No pots pujar més de cinc fitxers");
        });
        this.on("error", function(file,message) {
            alert(message);
        });
        this.on("success",
            myDropzone.processQueue.bind(myDropzone)
        );
        this.on("removedfile", function(file){
                $.ajax({
                    url: '/api/removeAttached/' + modelo + '/' + expediente + '/' + file.name,
                    type: 'GET',
                    dataType: 'json',
                    data: {api_token: $("#_token").text()},
                    error: function(data){
                        alert("No s'ha pogut esborrar: "+data.responseText);
                        myDropzone.emit("addedfile", file);
                        myDropzone.createThumbnailFromUrl(file,'/storage/adjuntos/'+modelo+'/'+expediente+'/'+file.name);
                        myDropzone.emit("success", file);
                        myDropzone.files.push(file);
                        myDropzone.emit("complete", file);
                    }
                });

        })
    },
};

$(function () {
    $('.message').on("click", function(event){
        event.preventDefault();
        let message = $(this).parent().attr('title');
        if (confirm(message)) {
            location.href = $(this).attr('href');
        }
    });
});
