
Dropzone.options.myDropzone = {
    autoProcessQueue: false,
    uploadMultiple: true,
    maxFilezise: 10,
    maxFiles: 3,
    parallelUploads: 3,
    addRemoveLinks: true,
    timeout: 50000,
    method: 'POST',
    acceptedFiles: 'image/*',
    previewsContainer: ".dropzone-previews",

    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },

    init: function() {
        var submitBtn = document.querySelector("#submit");
        var actividad = $('#idActividad').attr('value');
        var serverFiles = [];
        myDropzone = this;


        $.ajax({
            url: '/api/actividad/'+actividad+'/getFiles',
            type: 'GET',
            dataType: 'json',
            success: function(data){
                //console.log(data);
                $.each(data.data, function (key, mockFile) {
                    myDropzone.emit("addedfile", mockFile);
                    myDropzone.createThumbnailFromUrl(mockFile,'/storage/Extraescolars/'+mockFile.name);
                    myDropzone.emit("success", mockFile);
                    myDropzone.files.push(mockFile);
                    myDropzone.emit("complete", mockFile);
                    serverFiles.push(mockFile);
                });
            },
        });


        submitBtn.addEventListener("click", function(e){
            if (myDropzone.getQueuedFiles().length > 0) {
                e.preventDefault();
                e.stopPropagation();
                myDropzone.processQueue();
            }  else {
                serverFiles.forEach(function(file,index){
                    var etiqueta = "#image"+(index+1);
                    $(etiqueta).val(file.name);
                })
            }
        });

        this.on("addedfile", function (file) {
            if (file.size == 0) {
                this.removeFile(file);
                return;
            }
        });

        this.on("maxfilesexceeded", function(file) {
            this.removeFile(file);
            alert("No pots pujar més de tres fitxers");
        });

        this.on("success",
            myDropzone.processQueue.bind(myDropzone)
        );

        this.on("removedfile", function(file){
            const fakeFileIndex = serverFiles.findIndex(x => x.name === file.name);
            if (fakeFileIndex !== -1) {
                serverFiles.splice(fakeFileIndex, 1);
            }
        })

        this.on("sendingmultiple", function(data, xhr, formData) {
            formData.append("desenvolupament", jQuery("#desenvolupament").val());
            formData.append("dades", jQuery("#dades").val());
            formData.append("valoracio", jQuery("#valoracio").val());
            formData.append("aspectes", jQuery("#aspectes").val())
            formData.append("recomanda", jQuery("#recomanada").val());
            formData.append("estado", jQuery("#estado").val())
            formData.append("idActividad", jQuery("#idActividad").val());
            serverFiles.forEach(function(file,index){
                formData.append("image"+(index+1),file.name);
            })
        });



    },

};




