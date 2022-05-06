$(document).ready(function(){

    // Login form validation
    function singleInputValidate(selector, input, msg){
        selector.html(msg);
        selector.addClass('text-danger');
        input.css('border-color', 'red');
    }
    $('#login-form').submit(function(){
        return false;
    })
    $('#login-form').submit(function(){
        var email = $('#emailaddress').val(),
            password = $('#password').val(),
            submit = 1;
        if(email == ""){
            var selector = $('#email-group .msg'),
                input = $('#email-group input[type=email]'),
                msg = "Email cannot be blank.";
            singleInputValidate(selector,input,msg);
            $('#email-group label').addClass('text-danger');
        }if(password == ""){
            var selector = $('#password-group .msg'),
                input = $('#password-group input[type=password]'),
                msg = "Password cannot be blank";
            singleInputValidate(selector,input,msg);
            $('#password-group label').addClass('text-danger');
        }else{
            // Send value
            $.ajax({
                url: "form-action.php",
                type: "post",
                data: {email,password,submit},
                success: function(data){
                    if(data=="e=0"){
                        var selector = $('#email-group .msg'),
                            input = $('#email-group input[type=email]'),
                            msg = "Incorrect email.";
                        singleInputValidate(selector,input,msg);
                        $('#email-group label').addClass('text-danger');
                        var result = 0;
                    }else if(data=="p=0"){
                        var selector = $('#password-group .msg'),
                            input = $('#password-group input[type=password]'),
                            msg = "Incorrect password.";
                        singleInputValidate(selector,input,msg);
                        $('#password-group label').addClass('text-danger');
                        var result = 0;
                    }else if(data=="ep=0"){
                        var emsg = "Incorrect email.";
                        $('#email-group .msg').html(emsg);
                        $('#email-group .msg').addClass('text-danger');
                        $('#email-group label').addClass('text-danger');
                        $('#email-group input[type=email]').css('border-color', 'red');
                        var pmsg = "Incorrect password.";
                        $('#password-group .msg').html(pmsg);
                        $('#password-group .msg').addClass('text-danger');
                        $('#password-group label').addClass('text-danger');
                        $('#password-group input[type=password]').css('border-color', 'red');
                        var result = 0;
                    }else{
                        $('#password-group .msg').html('');
                        $('#password-group .msg').removeClass('text-danger');
                        $('#email-group label').removeClass('text-danger');
                        $('#password-group input[type=password]').css('border-color', '');
                        $('#email-group .msg').html('');
                        $('#email-group .msg').removeClass('text-danger');
                        $('#password-group label').removeClass('text-danger');
                        $('#email-group input[type=email]').css('border-color', '');
                        var result = 1;
                    }
                    if(result==1)   {
                        return true;
                    }else{
                        return false;
                    }
                }
            })
        }
    })
})
