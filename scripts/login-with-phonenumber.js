var lwp_refreshIntervalId;
jQuery(document).ready(function ($) {

    $(document).on('click', '.lwp_login_overlay, .close', function (e) {
        e.preventDefault();
        $('form#lwp_login, form#lwp_login_email, form#lwp_activate').fadeOut(500, function () {
            $('.lwp_login_overlay').remove();
        });
        return false;
    });

    // Show the login/signup popup on click

    $('body').on('click', '#show_login , .show_login', function (e) {
        e.preventDefault();
        var sticky = $(this).attr('data-sticky');
        if (sticky && sticky === '1')
            $('body').append('<div class="lwp_login_overlay"></div>');
        // if ($(this).attr('id') == 'show_login')
        if ($('form#lwp_login').length > 0) {
            $('form#lwp_login').fadeIn(0);
        }
        if ($('form#lwp_login_email').length > 0) {
            $('form#lwp_login_email').fadeIn(0);
            $('form#lwp_login').fadeOut(0);

        }
        // else
        //     $('form#register').fadeIn(500);
        e.preventDefault();
    });
    $('body').on('click', '.auth_with_phoneNumber', function (e) {
        e.preventDefault();

        // if ($(this).attr('id') == 'show_login')
        $('#lwp_login_email').fadeOut(10);
        $('#lwp_login').fadeIn(500);
        // else
        //     $('form#register').fadeIn(500);
        e.preventDefault();
    });
    $('body').on('click', '.forgot_password:not(.firebase) , .lwp_didnt_r_c:not(.firebase)', function (e) {
        e.preventDefault();
        if (!$(this).valid()) return false;
        // if (typeof firebaseConfig !== 'undefined') return false;
        let method = $('input[name="otp-method"]:checked').val();
        if (method === 'firebase') {
            console.log('method is firebase, return false')

            return false;
        }
        $('p.status', this).show().text(idehweb_lwp.loadingmessage);
        var action = 'lwp_forgot_password';
        // var username = $('.lwp_username').val();
        // username = username.replace(/^0+/, '');
        var username = $('[name="lwp_username"]').val();
        username = username.replace(/^[0\+]+/, '');

        var lwp_country_codes = $('#lwp_country_codes').val();
        username = lwp_country_codes + username;
        var email = $('.lwp_email').val();
        $('#lwp_login_email').fadeOut(10);
        $('#lwp_login').fadeOut(10);
        $('#lwp_enter_password').fadeOut(10);

        $('#lwp_activate').fadeIn(500);
        window.lwp_runTimer();
        $.ajax({
            dataType: 'json',
            url: idehweb_lwp.ajaxurl,
            data: {
                'action': action,
                'phone_number': username,
                'email': email,
                'ID': idehweb_lwp.UserId,
                'method': method,
            },
            success: function (data) {


            }
        });
    });
    $('body').on('click', '.auth_with_email', function (e) {

        // if ($(this).attr('id') == 'show_login')
        $('#lwp_login').fadeOut(10);
        $('#lwp_login_email').fadeIn(500);
        // else
        //     $('form#register').fadeIn(500);
        e.preventDefault();
    });
    $('#show_login').click();
    // Perform AJAX login/register on form submit
    $('body').on('click', 'input[name="otp-method"]', function (e) {
        if(e.target.value){
            let value=e.target.value;
            $('.ajax-auth.lwp-login-form-i').attr('class','ajax-auth lwp-login-form-i '+value);
            $('.submit_button.forgot_password').attr('class','submit_button forgot_password '+value);
            $('.submit_button.lwp_didnt_r_c').attr('class','submit_button lwp_didnt_r_c '+value);
            $('.ajax-auth.lwp-register-form-i').attr('class','ajax-auth lwp-register-form-i '+value);
        }
    })
    $('body').on('submit', 'form.ajax-auth.lwp-login-form-i:not(.firebase)', function (e) {
        // $('form#lwp_login').on('submit', function (e) {
        console.log('click on submit...')

        e.preventDefault();

        let method = $('input[name="otp-method"]:checked').val();
        if (method === 'firebase') {
            console.log('method is firebase, return false')

            return false;
        }
        console.log('method', method)

        if (!$(this).valid()) {
            var reason = '';
            if ($('[name="lwp_accept_terms"]').length && !$('[name="lwp_accept_terms"]').valid()) {
                reason = 'You must agree to the terms of service!'
            } else {
                reason = 'Phone number is required';
            }
            $('p.status', this).show().text(reason);
            console.log('not valid...')
            return false;
        }
        // if (typeof firebaseConfig !== 'undefined') {
        //     console.log('it is firebase')
        //     return false;
        // }
        $('p.status', this).show().text(idehweb_lwp.loadingmessage);
        var action = 'lwp_ajax_login';
        // var username = $('.lwp_username').val();
        // username = username.replace(/^0+/, '');
        var username = $('[name="lwp_username"]').val();
        username = username.replace(/^[0\+]+/, '');
        var lwp_country_codes = $('#lwp_country_codes').val();
        console.log('lwp_country_codes', lwp_country_codes);

        // if (!lwp_country_codes) {
        //     lwp_country_codes = $('.iti__country-list li:first-child').attr('data-dial-code');
        // }
        // console.log('lwp_country_codes', lwp_country_codes);

        username = lwp_country_codes + username;
        var ctrl = $(this);
        $.ajax({
            // type: 'GET',
            dataType: 'json',
            url: idehweb_lwp.ajaxurl,
            data: {
                'action': action,
                'username': username,
                'method': method
                // 'password': password,
                // 'email': email,
                // 'security': security
            },
            success: function (data) {
                console.log('return response...')

                $('p.status', ctrl).text(data.message);
                if (data.success == true) {
                    $('#lwp_login_email').fadeOut(10);
                    $('#lwp_login').fadeOut(10);
                    idehweb_lwp.UserId = data.ID;
                    $('.lwp_line').css('display', 'block');
                    $('.lwp_bottom_activation').css('display', 'block');
                    $('.lwp_bottom_activation .lwp_change_el').css('display', 'none');
                    $('.lwp_bottom_activation .lwp_change_pn').css('display', 'block');

                    if (data.authWithPass) {
                        if (data.showPass) {
                            $('#lwp_enter_password').fadeIn(500);
                            console.log('xdwcef');

                        } else {
                            $('#lwp_activate').fadeIn(500);
                            window.lwp_runTimer();
                            console.log('xdwcffffef');

                        }
                    } else {
                        $('#lwp_activate').fadeIn(500);
                        window.lwp_runTimer();
                        console.log('xdwcef543');


                    }
                    //     document.location.href = idehweb_lwp.redirecturl;
                }
            }
        });
    });
    $('body').on('submit', 'form#lwp_login_email', function (e) {
        // if (!$(this).valid()) return false;
        if (!$(this).valid()) {
            var reason = '';
            if ($('[name="lwp_accept_terms_email"]').length && !$('[name="lwp_accept_terms_email"]').valid()) {
                reason = 'You must agree to the terms of service!'
            } else {
                reason = 'Email is required';
            }
            $('p.status', this).show().text(reason);
            return false;
        }
        $('p.status', this).show().text(idehweb_lwp.loadingmessage);
        var action = 'lwp_ajax_login_with_email';
        var email = $('.lwp_email').val();

        // security = $('form#lwp_login .lwp_scode').val();
        var ctrl = $(this);
        $.ajax({
            // type: 'GET',
            dataType: 'json',
            url: idehweb_lwp.ajaxurl,
            data: {
                'action': action,
                'email': email
            },
            success: function (data) {

                $('p.status', ctrl).text(data.message);
                if (data.success == true) {
                    $('#lwp_login_email').fadeOut(10);
                    $('#lwp_login').fadeOut(10);
                    idehweb_lwp.UserId = data.ID;
                    $('.lwp_line').css('display', 'none');
                    $('.lwp_bottom_activation').css('display', 'block');
                    $('.lwp_bottom_activation .lwp_change_pn').css('display', 'none');
                    $('.lwp_bottom_activation .lwp_change_el').css('display', 'block');
                    if (data.authWithPass) {

                        if (data.showPass) {
                            $('#lwp_enter_password').fadeIn(500);

                        } else {
                            $('#lwp_activate').fadeIn(500);

                        }
                    } else {
                        $('#lwp_activate').fadeIn(500);

                    }
                    //     document.location.href = idehweb_lwp.redirecturl;
                }
            }
        });
        e.preventDefault();
    });


    $('body').on('submit', 'form.ajax-auth.lwp-register-form-i:not(.firebase)', function (e) {
        e.preventDefault();
        if (!$(this).valid()) return false;
        // if (typeof firebaseConfig !== 'undefined') return false;
        let method = $('input[name="otp-method"]:checked').val();
        // if (method === 'firebase') {
        //     console.log('method is firebase, return false')
        //
        //     return false;
        // }

        $('p.status', this).show().text(idehweb_lwp.loadingmessage);
        var action = 'lwp_ajax_register';
        var security = $('.lwp_scode').val();
        var obj = {
            'action': action,
            'secod': security,
            'method': method,
        };
        $('#lwp_login').fadeOut(10);
        $('#lwp_login_email').fadeOut(10);
        // $('#lwp_activate').fadeOut(500);
        // var phone_number = $('.lwp_username').val();
        var phone_number = $('[name="lwp_username"]').val();
        if (phone_number) {
            var lwp_country_codes = $('#lwp_country_codes').val();
            // phone_number = phone_number.replace(/^0+/, '');
            phone_number = phone_number.replace(/^[0\+]+/, '');
            phone_number = lwp_country_codes + phone_number;
            obj['phone_number'] = phone_number;
        }
        var email = $('.lwp_email').val();
        if (email) {
            obj['email'] = email;
        }

        var ctrl = $(this);

        $.ajax({
            // type: 'GET',
            dataType: 'json',
            url: idehweb_lwp.ajaxurl,
            data: obj,
            success: function (data) {
                if (data.authWithPass) {

                    if (!data.updatedPass) {
                        $('#lwp_activate').fadeOut(500);
                        $('#lwp_update_password').fadeIn(500);

                    } else {
                        $('p.status', ctrl).text(data.message);
                        if (data.success)
                            document.location.href = idehweb_lwp.redirecturl;

                    }
                } else {
                    $('p.status', ctrl).text(data.message);
                    if (data.success)
                        document.location.href = idehweb_lwp.redirecturl;
                }

                // console.log('');
                // if (data.loggedin == true && idehweb_lwp.redirecturl) {
                //     location.replace(idehweb_lwp.redirecturl);
                // }
            }
        });
    });


    $('body').on('submit', 'form#lwp_update_password', function (e) {
        e.preventDefault();

        if (!$(this).valid()) return false;


        $('p.status', this).show().text(idehweb_lwp.loadingmessage);
        var action = 'lwp_update_password_action';
        var lwp_up_password = $('.lwp_up_password').val();
        var obj = {
            'action': action,
            'password': lwp_up_password,
        };
        // $('#lwp_login').fadeOut(10);
        // $('#lwp_login_email').fadeOut(10);
        // $('#lwp_activate').fadeOut(500);
        // var phone_number = $('.lwp_username').val();
        // if(phone_number){
        //     obj['phone_number']=phone_number;
        // }
        // var email = $('.lwp_email').val();
        // if(email){
        //     obj['email']=email;
        // }
        //
        var ctrl = $(this);

        $.ajax({
            // type: 'GET',
            dataType: 'json',
            url: idehweb_lwp.ajaxurl,
            data: obj,
            success: function (data) {
                // if(data.updatedPass){
                //     $('#lwp_activate').fadeOut(500);
                //     $('#lwp_update_password').fadeIn(500);
                //
                // }else{
                $('p.status', ctrl).text(data.message);
                if (data.success)
                    document.location.href = idehweb_lwp.redirecturl;
                //
                // }
                // console.log('');
                // if (data.loggedin == true && idehweb_lwp.redirecturl) {
                //     location.replace(idehweb_lwp.redirecturl);
                // }
            }
        });
    });
    $('body').on('submit', 'form#lwp_enter_password', function (e) {
        console.log('act 2', idehweb_lwp.UserId);
        if (!$(this).valid()) return false;

        $('p.status', this).show().text(idehweb_lwp.loadingmessage);
        var action = 'lwp_enter_password_action';
        var lwp_up_password = $('.lwp_auth_password').val();
        var lwp_email = $('.lwp_email').val();
        // var lwp_username = $('.lwp_username').val();
        // lwp_username = lwp_username.replace(/^0+/, '');

        var lwp_username = $('[name="lwp_username"]').val();
        lwp_username = lwp_username.replace(/^[0\+]+/, '');
        var lwp_country_codes = $('#lwp_country_codes').val();
        lwp_username = lwp_country_codes + lwp_username;
        var obj = {
            'action': action,
            'password': lwp_up_password,
            'ID': idehweb_lwp.UserId,
            'email': lwp_email,
            'phoneNumber': lwp_username

        };
        var ctrl = $(this);

        $.ajax({
            // type: 'GET',
            dataType: 'json',
            url: idehweb_lwp.ajaxurl,
            data: obj,
            success: function (data) {
                $('p.status', ctrl).text(data.message);
                if (data.success)
                    document.location.href = idehweb_lwp.redirecturl;
            }
        });
        e.preventDefault();
    });


    $('body').on('click', '.lwp_change_pn', function (e) {
        e.preventDefault();
        clearInterval(lwp_refreshIntervalId);
        $('.lwp_didnt_r_c').removeClass('lwp_disable');
        $('.lwp_timer').empty();
        $('#lwp_activate').fadeOut(10);
        $('#lwp_enter_password').fadeOut(10);
        $('.ajax-auth .status').hide().empty();
        $('.lwp_didnt_r_c').addClass('lwp_none');
        $('[name="lwp_username"]').val('');
        $('#lwp_login_email').fadeOut(0);

        $('#lwp_login').fadeIn(500);

    });
    $('body').on('click', '.lwp_change_el', function (e) {
        e.preventDefault();
        clearInterval(lwp_refreshIntervalId);
        $('.lwp_didnt_r_c').removeClass('lwp_disable');
        $('.lwp_timer').empty();
        $('#lwp_activate').fadeOut(10);
        $('#lwp_enter_password').fadeOut(10);
        $('.ajax-auth .status').hide().empty();
        $('.lwp_didnt_r_c').addClass('lwp_none');
        $('.lwp_username').val('');
        $('#lwp_login').fadeOut(0);

        $('#lwp_login_email').fadeIn(500);

    });


    if (jQuery("#lwp_login").length)
        jQuery("#lwp_login").validate();

    window.lwp_runTimer = function () {
        if (idehweb_lwp.timer && (idehweb_lwp.timer == '1' || idehweb_lwp.timer == 1)) {
            var lwp_start = idehweb_lwp.timer_count || 60;
            lwp_refreshIntervalId = setInterval(function () {
                if (lwp_start >= 0)
                    $('.lwp_timer').text(lwp_start--);
                else {
                    clearInterval(lwp_refreshIntervalId);
                    $('.lwp_didnt_r_c').removeClass('lwp_disable');
                    $('.lwp_timer').empty();
                }
            }, 1000);
        }
    }
});