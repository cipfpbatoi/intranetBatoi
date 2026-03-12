'use strict';

(function () {
    function getInfoAndOption(trigger) {
        var option = 'el centro de trabajo';
        var list = trigger.closest('ul');
        var infoNode = trigger.closest('li') ? trigger.closest('li').querySelector('.info') : null;
        var info = infoNode ? infoNode.textContent : '';

        if (list && list.classList.contains('colaboracion')) {
            option = 'la colaboración entre';
        } else if (list && list.classList.contains('fct')) {
            option = 'la fct de ';
        } else {
            var anchor = trigger.closest('a');
            if (anchor && anchor.classList.contains('instructor')) {
                option = 'el instructor';
                var nombreNode = trigger.closest('h4.text-info') ? trigger.closest('h4.text-info').querySelector('.nombre') : null;
                info = nombreNode ? nombreNode.textContent : info;
            }
        }

        return { option: option, info: info };
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.addEventListener('click', function (event) {
            var trashIcon = event.target.closest('.fa-trash');
            if (trashIcon) {
                var data = getInfoAndOption(trashIcon);
                if (!window.confirm('Vas a borrar ' + data.option + ': ' + data.info)) {
                    event.preventDefault();
                }
                return;
            }

            var deleteButton = event.target.closest('#Borrar');
            if (deleteButton) {
                if (!window.confirm('!! Vas a borrar la empresa, los centros y todas sus colaboraciones !!')) {
                    event.preventDefault();
                }
            }
        });
    });
})();
