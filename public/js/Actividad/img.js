'use strict';

Dropzone.options.dropzone = {
    maxFiles: 3,
    maxFilesize: 4,
    acceptedFiles: '.jpeg,.jpg,.png,.gif',
    addRemoveLinks: true,
    timeout: 50000,
    method: 'POST',
    headers: {
        'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') || {}).content || ''
    },
    init: function () {
        var myDropzone = this;
        var actividadInput = document.getElementById('idctividad');
        var actividad = actividadInput ? actividadInput.getAttribute('value') : '';
        if (!actividad) {
            return;
        }

        fetch('actividad/' + actividad + '/getFiles', {
            method: 'GET',
            credentials: 'same-origin'
        })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('HTTP ' + response.status);
                }
                return response.json();
            })
            .then(function (data) {
                (data || []).forEach(function (value) {
                    var file = { name: value.name, size: value.size };
                    myDropzone.options.addedfile.call(myDropzone, file);
                    myDropzone.options.thumbnail.call(myDropzone, file, '/storage/Extraescolars/');
                    myDropzone.emit('complete', file);
                });
            })
            .catch(function (error) {
                window.console.log(error);
            });
    },
    removedfile: function (file) {
        if (!this.options.dictRemoveFile) {
            return;
        }

        var self = this;
        return Dropzone.confirm('Are You Sure to ' + this.options.dictRemoveFile, function () {
            var name = file.previewElement && file.previewElement.id ? file.previewElement.id : file.name;
            var csrf = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';
            var body = new URLSearchParams({ filename: name }).toString();

            fetch(delete_url, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
                },
                body: body,
                credentials: 'same-origin'
            })
                .then(function (response) {
                    if (!response.ok) {
                        throw new Error('HTTP ' + response.status);
                    }
                    return response.json();
                })
                .then(function (data) {
                    alert(data.success + ' File has been successfully removed!');
                })
                .catch(function (error) {
                    window.console.log(error);
                });

            if (file.previewElement && file.previewElement.parentNode) {
                file.previewElement.parentNode.removeChild(file.previewElement);
            }
            return self;
        });
    },
    success: function (file, response) {
        file.previewElement.id = response.success;
        var olddatadzname = file.previewElement.querySelector('[data-dz-name]');
        file.previewElement.querySelector('img').alt = response.success;
        olddatadzname.innerHTML = response.success;
    },
    error: function (file, response) {
        var message = typeof response === 'string' ? response : response.message;
        file.previewElement.classList.add('dz-error');
        var nodes = file.previewElement.querySelectorAll('[data-dz-errormessage]');
        for (var i = 0; i < nodes.length; i += 1) {
            nodes[i].textContent = message;
        }
        return nodes;
    }
};
