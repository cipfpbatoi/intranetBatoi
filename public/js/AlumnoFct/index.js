'use strict';

$(function () {
    var path = window.location.pathname || '';
    if (path.indexOf('/avalFct') !== -1) {
        var storageKey = 'alumnofct_aval_tab';
        var storedTab = localStorage.getItem(storageKey);
        var $tabLinks = $('a[data-toggle="tab"]');

        function updateActaButton(activeTab) {
            var $buttons = $('.fct-acta-btn');
            if (!$buttons.length) {
                return;
            }
            $buttons.hide();
            if (activeTab === '#tab_LOE') {
                $('#tab_LOE').find('.fct-acta-btn').show();
            } else if (activeTab === '#tab_LOGSE') {
                $('#tab_LOGSE').find('.fct-acta-btn').show();
            }
        }

        if (storedTab) {
            var $storedLink = $('a[href="' + storedTab + '"]');
            if ($storedLink.length) {
                $storedLink.tab('show');
                updateActaButton(storedTab);
            } else {
                updateActaButton($('.nav-tabs li.active a[data-toggle="tab"]').attr('href'));
            }
        } else {
            updateActaButton($('.nav-tabs li.active a[data-toggle="tab"]').attr('href'));
        }

        $tabLinks.on('shown.bs.tab', function (event) {
            var href = $(event.target).attr('href');
            if (href) {
                localStorage.setItem(storageKey, href);
                updateActaButton(href);
            }
        });
    }

    $(".download").on("click", function (event) {
        event.preventDefault();
        $(this).attr("data-toggle", "modal").attr("data-target", "#password").attr("href", "");
    });


    $("#seleccion .submit").click(function() {
        event.preventDefault();
        $("#checkall").prop('checked',false);
        $("#formSeleccion" ).submit();
    });

    $('#mostraDiv').change(function() {
        if($(this).is(':checked')) {
            $('#divSignatura').show();
        } else {
            $('#divSignatura').hide();
        }
    });


    $("#password .submit").click(function() {
        localStorage.setItem("cur_modal", '#password');
        event.preventDefault();
        $('#password').modal('hide');
        $("#formPassword" ).submit();
        $(this).attr("data-toggle", "modal").attr("data-target", "#loading").attr("href", "");
    });
});
