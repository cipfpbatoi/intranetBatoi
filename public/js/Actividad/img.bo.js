
    Dropzone.options.myDropzone = {
        url: '/actividad/50/fileupload',
        autoProcessQueue: false,
        uploadMultiple: true,
        maxFilezise: 256,
        filesizeBase: 1024,
        maxFiles: 3,
        //addRemoveLinks: true,
        method: 'PUT',
        timeout: 50000,
        dictCancelUpload: "Pujada Cancelada",
        dictFileTooBig: 'Fitxer massa gran',
        dictFallbackMessage: 'Navegador no admet',
        parallelUploads: 3,
        acceptedFiles: 'image/*',
        
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },

        init: function() {
            var submitBtn = document.querySelector("#submit");
            myDropzone = this;

            submitBtn.addEventListener("click", function(e){
                e.preventDefault();
                e.stopPropagation();
                if (myDropzone.getQueuedFiles().length > 0) {
                    myDropzone.processQueue();
                } else {
                    myDropzone.uploadFiles([]); //send empty
                }
            });
            this.on("maxfilesexceeded", function() {
                alert("No pots pujar més de tres fitxers");
            });


            this.on("complete", function(file) {
                myDropzone.removeFile(file);
            });

        },

    };


