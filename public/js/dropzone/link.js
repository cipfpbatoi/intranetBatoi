function apiAuthOptions(extraData) {
    var bearerTokenMeta = document.querySelector('meta[name="user-bearer-token"]');
    var bearerToken = ((bearerTokenMeta && bearerTokenMeta.getAttribute('content')) || "").trim();
    var data = extraData || {};
    var headers = {};

    if (bearerToken) {
        headers.Authorization = "Bearer " + bearerToken;
    }

    return { headers: headers, data: data };
}

function fetchJson(url, options) {
    return fetch(url, options).then(function (response) {
        if (!response.ok) {
            return response.text().then(function (text) {
                throw new Error(text || ("Error HTTP " + response.status));
            });
        }

        return response.json();
    });
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
        'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') || {}).content
    },

    init: function() {
        var auth = apiAuthOptions();
        var expedienteInput = document.getElementById('id');
        var modeloInput = document.getElementById('modelo');
        var expediente = expedienteInput ? expedienteInput.value : '';
        var modelo = modeloInput ? modeloInput.value : '';
        var myDropzone = this;

        if (auth.headers.Authorization) {
            this.options.headers.Authorization = auth.headers.Authorization;
        }

        this.on("complete", function(file) {
            var removeLinks = document.querySelectorAll('.dz-remove');
            var errorMessages = document.querySelectorAll('.dz-error-message');

            removeLinks.forEach(function (link) {
                link.innerHTML = "<div><span class='fa fa-trash text-danger' style='font-size: 1.5em'></span></div>";
            });

            errorMessages.forEach(function (messageNode) {
                messageNode.innerHTML = "";
            });
        });

        var attachedUrl = '/api/getAttached/' + modelo + '/' + expediente;
        if (Object.keys(auth.data).length > 0) {
            attachedUrl += '?' + new URLSearchParams(auth.data).toString();
        }

        fetchJson(attachedUrl, {
            method: 'GET',
            headers: auth.headers,
        }).then(function (data) {
            (data.data || []).forEach(function (mockFile) {
                    myDropzone.emit("addedfile", mockFile);
                    if (!mockFile.referencesTo) {
                        var previewName = mockFile.file ? mockFile.file : mockFile.name;
                        myDropzone.createThumbnailFromUrl(
                            mockFile,
                            '/storage/adjuntos/' + modelo + '/' + expediente + '/' + previewName
                        );
                    }
                    myDropzone.emit("success", mockFile);
                    myDropzone.files.push(mockFile);
                    myDropzone.emit("complete", mockFile);
                });
        }).catch(function (error) {
            alert("No s'han pogut carregar els adjunts: " + error.message);
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
                    a.addEventListener('click', function () {
                        if (file.referencesTo.indexOf('foremp.edu.gva.es') !== -1) {
                            alert("Si no s'obri el document, inicia sessió en el SAO en una altra pestanya i torna-ho a provar.");
                        }
                    });
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
            var removeUrl = '/api/removeAttached/' + modelo + '/' + expediente + '/' + file.name;
            if (Object.keys(auth.data).length > 0) {
                removeUrl += '?' + new URLSearchParams(auth.data).toString();
            }

            fetchJson(removeUrl, {
                method: 'GET',
                headers: auth.headers,
            }).catch(function (error) {
                alert("No s'ha pogut esborrar: " + error.message);
                myDropzone.emit("addedfile", file);
                myDropzone.createThumbnailFromUrl(file,'/storage/adjuntos/'+modelo+'/'+expediente+'/'+file.name);
                myDropzone.emit("success", file);
                myDropzone.files.push(file);
                myDropzone.emit("complete", file);
            });

        })
    },
};

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.message').forEach(function (messageLink) {
        messageLink.addEventListener('click', function (event) {
            event.preventDefault();
            var parent = messageLink.parentElement;
            var message = parent ? parent.getAttribute('title') : '';
            if (confirm(message)) {
                location.href = messageLink.getAttribute('href');
            }
        });
    });
});
