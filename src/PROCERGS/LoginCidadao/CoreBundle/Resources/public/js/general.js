$(function() {

    $('.navbar .btn-login').on('click', function(e) {
        e.preventDefault();
        window.open('login_teste', '', "width=400,height=400");
        return false;
    });

    // add bootstrap classes to forms
    $('.form-group input, .form-group select').not('.form-control').addClass('form-control');
    $('.form-group .input-error').not(':empty').parent('.form-group').addClass('has-error');

    // maks inputs
    $('#fos_user_registration_form_cep').mask('00000-000');

    $('#fos_user_registration_form_cpf').mask('000.000.000-00');


    // switch display application list
    $('.btn-app-display').on('click', function() {
        if ( !$(this).hasClass('active') ) {
            $('.btn-app-display').removeClass('active');
            $(this).addClass('active');

            $('#applications').css({'margin-top': '-30px', 'opacity' : 0 });
            var button = $(this);
            setTimeout(function () {
                switch (button.data("display")) {
                    case 'list':
                        $('#applications').removeClass('icon').addClass('list');
                        break;
                    case 'icon':
                        $('#applications').removeClass('list').addClass('icon');
                        break;
                }
                $('#applications').css({'margin-top' : 0, 'opacity' : 1});
            }, 800);
        }
    });

    // close for msg-popup - error, alert, warning
    $('.msg-popup').on('click', '.btn', function() {
        $('.msg-popup').fadeOut();
    });

    $('#profile'). on('click', '.edit', function(e) {
        e.preventDefault();
        $('#profile input').attr('readonly', false);
        $('#profile .info').addClass('open');
    });

    $('#profile'). on('click', '.toggle', function(e) {
        e.preventDefault();
        $(this).toggleClass('open');
        $('#profile .info').toggleClass('open');
    });


});