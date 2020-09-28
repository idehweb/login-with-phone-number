jQuery(document).ready(function ($) {
    var UserId = 0;
    $(document).on('click', '.lwp_login_overlay, .close', function (e) {
        e.preventDefault();
        $('form#lwp_login, form#lwp_login_email, form#lwp_activate').fadeOut(500, function () {
            $('.lwp_login_overlay').remove();
        });
        return false;
    });

    // Show the login/signup popup on click

    $('body').on('click', '#show_login , .show_login', function (e) {
        var sticky = $(this).attr('data-sticky');
        if (sticky && sticky==='1')
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
        // if ($(this).attr('id') == 'show_login')
        $('#lwp_login_email').fadeOut(10);
        $('#lwp_login').fadeIn(500);
        // else
        //     $('form#register').fadeIn(500);
        e.preventDefault();
    });
    $('body').on('click', '.forgot_password', function (e) {
        if (!$(this).valid()) return false;
        $('p.status', this).show().text(idehweb_lwp.loadingmessage);
        var action = 'lwp_forgot_password';
        var username = $('.lwp_username').val();
        username = username.replace(/^0+/, '');

        var lwp_country_codes = $('#lwp_country_codes').val();
        username = lwp_country_codes + username;
        var email = $('.lwp_email').val();
        $('#lwp_login_email').fadeOut(10);
        $('#lwp_login').fadeOut(10);
        $('#lwp_enter_password').fadeOut(10);

        $('#lwp_activate').fadeIn(500);
        $.ajax({
            dataType: 'json',
            url: idehweb_lwp.ajaxurl,
            data: {
                'action': action,
                'phone_number': username,
                'email': email,
                'ID': UserId,
            },
            success: function (data) {


            }
        });
        e.preventDefault();
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
    $('form#lwp_login').on('submit', function (e) {
        if (!$(this).valid()) return false;

        $('p.status', this).show().text(idehweb_lwp.loadingmessage);
        var action = 'lwp_ajax_login';
        var username = $('.lwp_username').val();
        username = username.replace(/^0+/, '');

        var lwp_country_codes = $('#lwp_country_codes').val();
        username = lwp_country_codes + username;
        var ctrl = $(this);
        $.ajax({
            // type: 'GET',
            dataType: 'json',
            url: idehweb_lwp.ajaxurl,
            data: {
                'action': action,
                'username': username,
                // 'password': password,
                // 'email': email,
                // 'security': security
            },
            success: function (data) {

                $('p.status', ctrl).text(data.message);
                if (data.success == true) {
                    $('#lwp_login_email').fadeOut(10);
                    $('#lwp_login').fadeOut(10);
                    UserId = data.ID;
                    if (data.authWithPass) {
                        if (data.showPass) {
                            $('#lwp_enter_password').fadeIn(500);
                            console.log('xdwcef');

                        } else {
                            $('#lwp_activate').fadeIn(500);
                            console.log('xdwcffffef');

                        }
                    } else {
                        $('#lwp_activate').fadeIn(500);
                        console.log('xdwcef543');


                    }
                    //     document.location.href = idehweb_lwp.redirecturl;
                }
            }
        });
        e.preventDefault();
    });
    $('form#lwp_login_email').on('submit', function (e) {
        if (!$(this).valid()) return false;

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
                    UserId = data.ID;
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


    $('form#lwp_activate').on('submit', function (e) {
        if (!$(this).valid()) return false;

        $('p.status', this).show().text(idehweb_lwp.loadingmessage);
        var action = 'lwp_ajax_register';
        var security = $('.lwp_scode').val();
        var obj = {
            'action': action,
            'secod': security,
        };
        $('#lwp_login').fadeOut(10);
        $('#lwp_login_email').fadeOut(10);
        // $('#lwp_activate').fadeOut(500);
        var phone_number = $('.lwp_username').val();
        if (phone_number) {
            var lwp_country_codes = $('#lwp_country_codes').val();
            phone_number = phone_number.replace(/^0+/, '');
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
        e.preventDefault();
    });


    $('form#lwp_update_password').on('submit', function (e) {
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
        e.preventDefault();
    });
    $('form#lwp_enter_password').on('submit', function (e) {
        if (!$(this).valid()) return false;

        $('p.status', this).show().text(idehweb_lwp.loadingmessage);
        var action = 'lwp_enter_password_action';
        var lwp_up_password = $('.lwp_auth_password').val();
        var lwp_email = $('.lwp_email').val();
        var lwp_username = $('.lwp_username').val();
        lwp_username = lwp_username.replace(/^0+/, '');
        var lwp_country_codes = $('#lwp_country_codes').val();
        lwp_username = lwp_country_codes + lwp_username;
        var obj = {
            'action': action,
            'password': lwp_up_password,
            'ID': UserId,
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


    if (jQuery("#lwp_login").length)
        jQuery("#lwp_login").validate();
});