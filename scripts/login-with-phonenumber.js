jQuery(document).ready(function ($) {
    // Display form from link inside a popup
    // $('#pop_login').live('click', function (e) {
    //     formtoFadeIn = $('form#lwp_login');
    //     // formToFadeOut = $('form#lwp_login');
    //     // formtoFadeIn = $('form#register');
    //
    //     formToFadeOut.fadeOut(500, function () {
    //         formtoFadeIn.fadeIn();
    //     })
    //     return false;
    // });
    // Close popup
    // $(document).on('click', '.login_overlay, .close', function () {
    //     $('form#lwp_login, form#register').fadeOut(500, function () {
    //         $('.login_overlay').remove();
    //     });
    //     return false;
    // });

    // Show the login/signup popup on click

    $('body').on('click','#show_login', function (e) {
        $('body').prepend('<div class="login_overlay"></div>');
        // if ($(this).attr('id') == 'show_login')
        $('form#lwp_login').fadeIn(500);
        // else
        //     $('form#register').fadeIn(500);
        e.preventDefault();
    });
    $('#show_login').click();
    // Perform AJAX login/register on form submit
    $('form#lwp_login').on('submit', function (e) {
        if (!$(this).valid()) return false;
        console.log('hete');
        $('p.status', this).show().text(idehweb_lwp.loadingmessage);
        var action = 'lwp_ajax_login';
        var username = $('.lwp_username').val();
        $('#lwp_login').fadeOut(10);
        $('#lwp_activate').fadeIn(500);
        // security = $('form#lwp_login .lwp_scode').val();
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
                // $('p.status', ctrl).text(data.message);
                // if (data.loggedin == true) {
                //     document.location.href = idehweb_lwp.redirecturl;
                // }
            }
        });
        e.preventDefault();
    });


    $('form#lwp_activate').on('submit', function (e) {
        if (!$(this).valid()) return false;
        console.log('hete');
        $('p.status', this).show().text(idehweb_lwp.loadingmessage);
        var action = 'lwp_ajax_register';
        var username = $('.lwp_username').val();
        $('#lwp_login').fadeOut(10);
        $('#lwp_activate').fadeIn(500);
        var security = $('.lwp_scode').val();
        var ctrl = $(this);
        $.ajax({
            // type: 'GET',
            dataType: 'json',
            url: idehweb_lwp.ajaxurl,
            data: {
                'action': action,
                'phone_number': username,
                'secod': security,
                // 'password': password,
                // 'email': email,
                // 'security': security
            },
            success: function (data) {
                $('p.status', ctrl).text(data.message);
                if (data.loggedin == true) {
                    location.reload();
                }
            }
        });
        e.preventDefault();
    });



  if (jQuery("#lwp_login").length)
        jQuery("#lwp_login").validate();
});