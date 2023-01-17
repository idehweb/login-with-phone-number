jQuery(document).ready(function ($) {

    if (firebase)
        if (!firebase.apps.length) {
            firebase.initializeApp(firebaseConfig);
        } else {
            firebase.app(); // if already initialized, use that one
        }

    $('form#lwp_login').on('submit', function (e) {
        console.log('act 1');
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
                if (data && data.ID)
                    idehweb_lwp.UserId = data.ID;

                // $('p.status', ctrl).text(data.message);
                $('.lwp_bottom_activation').css('display', 'block');
                $('.lwp_bottom_activation .lwp_change_pn').css('display', 'block');
                $('.lwp_bottom_activation .lwp_change_el').css('display', 'none');
                if (data.success == true) {
                    if (data.authWithPass && data.showPass) {
                        $('#lwp_login_email').fadeOut(10);
                        $('#lwp_login').fadeOut(10);
                        $('#lwp_enter_password').fadeIn(500);

                    } else {
                        $('p.status').html('running recaptcha...');

                        runFBase("+" + username);


                    }
                    //     document.location.href = idehweb_lwp.redirecturl;
                } else {
                    $('p.status').html(data.message);
                }
            }
        });
        e.preventDefault();


        // firebaseAuth.confirm( otpForm.getOtpValue() ).then(function (result) {
        //
        //     firebase.auth().currentUser.getIdToken( false ).then(function(idToken) {
        //         otpForm.verifyOTP( { firebase_idToken: idToken } );
        //     })
        //
        // }).catch(function (error) {
        //     // User couldn't sign in (bad verification code?)
        //     otpForm.verifyOTP( { firebase_error: JSON.stringify( error ) } );
        // });
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
        if (window.confirmationResult && window.confirmationResult.verificationId) {
            obj['verificationId'] = window.confirmationResult.verificationId;
        }
        var ctrl = $(this);
        console.log('code entered:', obj['secod']);
        // return confirmationResult.confirm(obj['secod']).then((result) => {
        //     console.log('result',result);
        // User signed in successfully.
        // const user = result.user;
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
            }
        });
        // }).catch((error) => {
        //     console.log('error',error);
        //     // User couldn't sign in (bad verification code?)
        //     // ...
        // });

        e.preventDefault();
    });


    $('body').on('click', '.forgot_password , .lwp_didnt_r_c', function (e) {
        console.log('act 2');

        if (!$(this).valid()) return false;

        $('p.status', this).show().text(idehweb_lwp.loadingmessage);
        var action = 'lwp_forgot_password';
        var username = $('.lwp_username').val();
        username = username.replace(/^0+/, '');

        var lwp_country_codes = $('#lwp_country_codes').val();
        username = lwp_country_codes + username;
        var email = $('.lwp_email').val();
        // $('#lwp_login').fadeOut(10);
        $('#lwp_enter_password').fadeOut(10);
        $('#lwp_login').fadeIn(500);
        $.ajax({
            dataType: 'json',
            url: idehweb_lwp.ajaxurl,
            data: {
                'action': action,
                'phone_number': username,
                'email': email,
                'ID': idehweb_lwp.UserId,
            },
            success: function (data) {

                runFBase("+" + username);
            }
        });
        e.preventDefault();
    });

    function runFBase(phoneNumber) {

        if (!window.recaptchaVerifier) {
            $('<div id="lwp-firebase-recaptcha"></div>').insertBefore('.submit_button.auth_phoneNumber');
            //Firebase
            window.recaptchaVerifier = new firebase.auth.RecaptchaVerifier('lwp-firebase-recaptcha', {
                'size': 'invisible',
                'callback': function (response) {
                    console.log('recaptchaVerifier response');
                }
            });
        }
        const appVerifier = window.recaptchaVerifier;
        // console.log('phoneNumber', phoneNumber);
        firebase.auth().signInWithPhoneNumber(phoneNumber, appVerifier)
            .then(function (confirmationResult) {
                // console.log('confirmationResult', confirmationResult);
                // console.log('verificationId', confirmationResult.verificationId);
                window.confirmationResult = confirmationResult;
                $('#lwp_login_email').fadeOut(10);
                $('#lwp_login').fadeOut(10);
                $('#lwp_activate').fadeIn(500);
                window.lwp_runTimer();
                // SMS sent. Prompt user to type the code from the message, then sign the
                // user in with confirmationResult.confirm(code).
                // console.log('confirmationResult', confirmationResult);
                // return confirmationResult.confirm(testVerificationCode)
                // phoneForm.otpFormHandler.firebaseAuth = confirmationResult;
                // phoneForm.onRequestOTP(response);
            }).catch(function (error) {

            // Error; SMS not sent
            // response.otp_sent 	= 0;
            // response.notice 	= error.message ? parse_notice( error.message ) : xoo_ml_phone_localize.notices.try_later;
            // phoneForm.onRequestOTP(response);

            window.recaptchaVerifier.render().then(function (widgetId) {
                grecaptcha.reset(widgetId);
            });
            console.log(error);

        });
    }
})