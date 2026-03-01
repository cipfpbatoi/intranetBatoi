function apiAuthOptions(extraData) {
    var legacyToken = $.trim($("#_token").text());
    var bearerToken = $.trim($('meta[name="user-bearer-token"]').attr('content') || "");
    var data = extraData || {};
    var headers = {};

    if (bearerToken) {
        headers.Authorization = "Bearer " + bearerToken;
    }
    if (legacyToken) {
        data.api_token = legacyToken;
    }

    return { headers: headers, data: data };
}

Dropzone.options.myDropzone = {
    autoProcessQueue: true,
    uploadMultiple: true,
    maxFilesize: 10,
    parallelUploads: 5,
    addRemoveLinks: true,
    timeout: 50000,
    method: 'POST',
    url: '/api/attachFile',
    acceptedFiles: 'application/pdf,application/x-pdf,application/vnd.oasis.opendocument.text,application/zip,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,.pdf,.odt,.zip,.doc,.docx',
    previewsContainer: ".dropzone-previews",
    disablePreviews: true,
    dictRemoveFileConfirmation: "Vas a esborrar el fitxer",
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },

    init: function() {
        var auth = apiAuthOptions();
        var expediente = $('#id').attr('value');
        var modelo = $('#modelo').attr('value');
        myDropzone = this;

        if (auth.headers.Authorization) {
            this.options.headers.Authorization = auth.headers.Authorization;
        } else if (auth.data.api_token) {
            this.options.params = $.extend({}, this.options.params, { api_token: auth.data.api_token });
        }

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
            headers: auth.headers,
            data: auth.data,
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
                    headers: auth.headers,
                    data: auth.data,
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
