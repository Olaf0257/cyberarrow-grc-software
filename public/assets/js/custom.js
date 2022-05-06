$(document).ready(function() {
    //Navigation bar
    //$('.navigation-menu li').removeClass('last-elements');

    if($('[data-toggle="select2"]').length > 0){
        $('[data-toggle="select2"]').select2();
    }

    // reset password
    $('#recoverpw-form').submit(function() {
        var email = $('#emailaddress').val();
        var resetpw = 1;
        if (email == '') {
            $('#email-group .msg').html('Email cannot be blank.');
            $('#email-group .msg').addClass('text-danger');
            $('#email-group label').addClass('text-danger');
            $('#email-group input[type=email]').css('border-color', 'red');
        } else {
            $.ajax({
                url: "form-action.php",
                type: "post",
                data: { email, resetpw },
                beforeSend: function() {
                    $(this).text('Sending');
                },
                success: function(data) {
                    if (data == 0) {
                        $('#email-group .msg').html('This email could not be found.');
                        $('#email-group .msg').addClass('text-danger');
                        $('#email-group label').addClass('text-danger');
                        $('#email-group input[type=email]').css('border-color', 'red');
                    } else {
                        $('#email-group .msg').html('');
                        $('#email-group .msg').removeClass('text-danger');
                        $('#email-group label').removeClass('text-danger');
                        $('#email-group input[type=email]').css('border-color', '');
                        window.location.href = "pages-confirm-mail.html";
                    }
                    $(this).text('Reset Password');
                }
            })
        }
        return false;
    })

    // Collapsible radio
    $('.collapsible-radio').each(function() {
        $(this).click(function() {
            var target = $(this).attr('href');
            if ($(this).is(":checked")) {
                $('.radio-collapse').not(target).hide();
                $('.radio-collapse').not(target).addClass('hide');
                $('.radio-collapse.hide').find('input').prop('required', false);
                $(target).fadeIn();
                $(target).find('input').prop('required', true);
            }
        })
    })

    // Time zone
    $('#timezone').timezones();

    // Chartist
    // new Chartist.Line('.ct-chart', {
    //     labels: ['9/19/2019', '9/21/2019', '9/22/2019', '9/23/2019', '9/24/2019', '9/25/2019', '9/26/2019'],
    //     series: [
    //     [0, 5, 10, 80, 90, 0, 0],
    //     [1, 50, 60, 20, 10, 0, 5]
    //     ]
    // });

    // Intl Tel

    function singleInputValidate(selector, input, msg){
        selector.html(msg);
        selector.addClass('text-danger');
        input.css('border-color', 'red');
    }
    
})
