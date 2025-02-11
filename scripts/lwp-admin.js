jQuery(document).ready(function ($) {
    var default_tab = window.location.hash ? window.location.hash.substring(1) : 'lwp-tab-general-settings'; // Default to 'gateway-settings'
    // $('#' + initialTab).addClass('active');


    jQuery('.ilwplabel').css('display', 'none')
    console.log("let's make all " + ".ilwplabel." + default_tab + " display table-row")
    jQuery('.ilwplabel.' + default_tab).css('display', 'table-row')


    //related to general tabs
    var edf = $('#idehweb_lwp_settings_idehweb_sms_login');
    var edf5 = $('#idehweb_lwp_settings_enable_timer_on_sending_sms');
    var related_to_phone_number_login = $('.related_to_phone_number_login');
    var related_to_entimer = $('.related_to_entimer');


    //related to gateway tabs
    // var edf3 = $('#idehweb_lwp_settings_use_custom_gateway');
    var edf4 = $('#idehweb_default_gateways');
    // var related_to_defaultgateway = $('.related_to_defaultgateway');
    var related_to_login = $('.related_to_login');

    var related_to_firebase = $('.related_to_firebase');
    var related_to_custom = $('.related_to_custom');
    var default_gateways = edf4.val();
    let the_gateways = $('.lwp-gateways')
    if (!(default_gateways instanceof Array)) {
        default_gateways = [];
    }


    //related to form tabs
    var edf6 = $('.idehweb_lwp_position_form');
    var edf7 = $('#idehweb_enable_accept_terms_and_condition');
    var related_to_position_fixed = jQuery('.related-to-position-fixed');
    var related_to_accept_terms = $('.related-to-accept-terms');


    if (default_tab == 'lwp-tab-general-settings') {


        if (edf.is(':checked')) {
            related_to_phone_number_login.css('display', 'table-row');


        } else {

            related_to_phone_number_login.css('display', 'none');
        }


        if (edf5.is(':checked')) {
            console.log('ed5 is checked!');
            related_to_entimer.css('display', 'table-row');

        } else {
            // console.log('is not checked!');
            related_to_entimer.css('display', 'none');

        }

    }

    if (default_tab == 'lwp-tab-gateway-settings') {
        if (the_gateways) {
            the_gateways.each((i, item) => {
                console.log("in", i, item.classList)
                $(item).css('display', 'none')
                if (item && item.classList && item.classList[2]) {
                    let main_class = item.classList[2];
                    let main_gateways = main_class?.split('related_to_')
                    let main_gateway_name = main_gateways[1];
                    // console.log("main_gateway_name", main_gateway_name)
// if(!available_gateways.)
                    if (default_gateways.includes(main_gateway_name)) {
                        $(".related_to_" + main_gateway_name).css("display", "table-row")
                    }
                }
            })
            // default_gateways.forEach((dg)=>{
            //
            //
            // })
        }
        if (default_gateways.includes('firebase')) {
            // console.log('is checked!');
            // $("#idehweb_phone_number_ccode").chosen();
            related_to_firebase.css('display', 'table-row');


        } else {
            // console.log('is not checked!');
            related_to_firebase.css('display', 'none');


        }


        if (default_gateways.includes('custom')) {
            // console.log('is checked!');
            // $("#idehweb_phone_number_ccode").chosen();
            related_to_custom.css('display', 'table-row');


        } else {
            // console.log('is not checked!');
            related_to_custom.css('display', 'none');


        }
        // if (edf3.is(':checked')) {
        //     console.log('edf3 is checked!');
        //     // $("#idehweb_phone_number_ccode").chosen();
        //     related_to_defaultgateway.css('display', 'table-row');
        //     $('.rltll').css('display', 'none');
        //
        //
        // } else {
        //     console.log('edf3 is not checked!');
        //
        //     // console.log('is not checked!');
        //     related_to_defaultgateway.css('display', 'none');
        //
        //
        // }
    }

    if (default_tab == 'lwp-tab-form-settings') {

        if (edf6.is(':checked')) {
            console.log('edf6 is checked.')
            related_to_position_fixed.css('display', 'table-row');

        } else {
            console.log('edf6 is not checked.', related_to_position_fixed)
            related_to_position_fixed.css('display', 'none');
        }
        if (edf7.is(':checked')) {
            console.log('edf7 is checked.')
            related_to_accept_terms.css('display', 'table-row');

        } else {
            console.log('edf7 is not checked.', related_to_accept_terms)
            related_to_accept_terms.css('display', 'none');
        }
    }
    edf4.on('change', function (e) {
        var data = $("#idehweb_default_gateways").select2('data');
        data = data.map((item) => {
            return item.id
        })
        // console.log('this.value', data);
        let available_gateways = [];
        $('.lwp-gateways').each((i, item) => {
            $(item).css('display', 'none')
            if (item && item.classList && item.classList[2]) {
                let main_class = item.classList[2];
                let main_gateways = main_class?.split('related_to_')
                let main_gateway_name = main_gateways[1];
                // console.log("main_gateway_name", main_gateway_name)
// if(!available_gateways.)
                if (data.includes(main_gateway_name)) {
                    $(".related_to_" + main_gateway_name).css("display", "table-row")
                }
            }
        })
        if (!(data instanceof Array)) {
            data = [];
        }
        // Assuming data is a string that could contain both "custom" and "firebase"
        console.log("data:", data);

// Check if data contains both "custom" and "firebase"
        if (data.includes("custom") && data.includes("firebase")) {
            console.log("data includes both custom and firebase");
            // Set the styles for both custom and firebase
            related_to_firebase.css('display', 'table-row');
            related_to_custom.css('display', 'table-row');
        }
// Check if data contains only "custom"
        else if (data.includes("custom")) {
            console.log("data includes custom");
            related_to_firebase.css('display', 'none');
            related_to_custom.css('display', 'table-row');
        }
// Check if data contains only "firebase"
        else if (data.includes("firebase")) {
            console.log("data includes firebase");
            related_to_firebase.css('display', 'table-row');
            related_to_custom.css('display', 'none');
        }
// If data doesn't contain "custom" or "firebase"
        else {
            console.log("data includes neither custom nor firebase");
            related_to_firebase.css('display', 'none');
            related_to_custom.css('display', 'none');
        }

    });

    $('body').on('change', '.idehweb_lwp_position_form',
        function () {
            console.log('hi');
            if (this.checked && this.value == '1') {
                // console.log('change is checked!');

                $('.related-to-position-fixed').css('display', 'table-row');
                // $("#idehweb_phone_number_ccode").chosen();

            } else {
                // console.log('change is not checked!');

                $('.related-to-position-fixed').css('display', 'none');
            }
        });
    edf.change(
        function () {
            if (this.checked && this.value == '1') {
                // console.log('change is checked!');

                related_to_phone_number_login.css('display', 'table-row');
                // $("#idehweb_phone_number_ccode").chosen();

            } else {
                // console.log('change is not checked!');

                related_to_phone_number_login.css('display', 'none');
            }
        });
    // edf3.change(
    //     function () {
    //         edf4.trigger('change');
    //         if (this.checked && this.value == '1') {
    //             console.log('change is checked!');
    //
    //             // $("#idehweb_phone_number_ccode").chosen();
    //             related_to_defaultgateway.css('display', 'table-row');
    //             $('.rltll').css('display', 'none');
    //
    //         } else {
    //             console.log('change is not checked!');
    //             $('.rltll').css('display', 'table-row');
    //
    //             related_to_defaultgateway.css('display', 'none');
    //
    //         }
    //     });
    edf5.change(
        function () {
            if (this.checked && this.value == '1') {
                console.log('edf5 change is checked!');

                related_to_entimer.css('display', 'table-row');

            } else {
                // console.log('change is not checked!');
                related_to_entimer.css('display', 'none');

            }
        });
    edf7.change(
        function () {
            if (this.checked && this.value == '1') {
                console.log('edf7 change change is checked!');

                // $("#idehweb_phone_number_ccode").chosen();
                related_to_accept_terms.css('display', 'table-row');

            } else {
                // console.log('change is not checked!');
                related_to_accept_terms.css('display', 'none');

            }
        });
    jQuery('.lwp-tabs-list').on('click', '.lwp-tab-item', function (e) {
        e.preventDefault();
        var lwp_data_tab = jQuery(this).attr("data-tab");
        jQuery('.ilwplabel').css('display', 'none')
        window.location.hash = lwp_data_tab;
        console.log("lwp_data_tab:", lwp_data_tab)
        if (lwp_data_tab == 'lwp-tab-general-settings') {
            jQuery('.ilwplabel.' + lwp_data_tab).css('display', 'table-row')
        }
        if (lwp_data_tab == 'lwp-tab-gateway-settings') {
            jQuery('.ilwplabel.' + lwp_data_tab).css('display', 'table-row')

            // var edf = jQuery('#idehweb_lwp_settings_idehweb_sms_login');
            // var edf3 = jQuery('#idehweb_lwp_settings_use_custom_gateway');
            // var edf4 = jQuery('#idehweb_default_gateways');
            //
            // var related_to_login = jQuery('.related_to_login');
            // var related_to_defaultgateway = jQuery('.related_to_defaultgateway');
            //
            // var related_to_firebase = jQuery('.related_to_firebase');
            // var related_to_custom = jQuery('.related_to_custom');
            //
            //
            // var default_gateways = edf4.val();
            if (!(default_gateways instanceof Array)) {
                default_gateways = [];
            }

            if (edf.is(':checked')) {
                related_to_login.css('display', 'table-row');
                // jQuery("#idehweb_phone_number_ccode").chosen();


            } else {

                related_to_login.css('display', 'none');
            }

            // if (edf3.is(':checked')) {
            //     related_to_defaultgateway.css('display', 'table-row');
            //     jQuery('.rltll').css('display', 'none');
            //
            //
            // } else {
            //     // console.log('is not checked!');
            //     related_to_defaultgateway.css('display', 'none');
            //
            //
            // }


            console.log("default_gateways", default_gateways)
            // if(default_gateways && default_gateways.length>0){
            let the_gateways = jQuery('.lwp-gateways')
            if (the_gateways) {
                the_gateways.each((i, item) => {
                    console.log("in", i, item.classList)
                    jQuery(item).css('display', 'none')
                    if (item && item.classList && item.classList[2]) {
                        let main_class = item.classList[2];
                        let main_gateways = main_class?.split('related_to_')
                        let main_gateway_name = main_gateways[1];
                        // console.log("main_gateway_name", main_gateway_name)
// if(!available_gateways.)
                        if (default_gateways.includes(main_gateway_name)) {
                            jQuery(".related_to_" + main_gateway_name).css("display", "table-row")
                        }
                    }
                })
                // default_gateways.forEach((dg)=>{
                //
                //
                // })
            }
            if (default_gateways.includes('firebase')) {
                // console.log('is checked!');
                // jQuery("#idehweb_phone_number_ccode").chosen();
                related_to_firebase.css('display', 'table-row');


            } else {
                // console.log('is not checked!');
                related_to_firebase.css('display', 'none');


            }


            if (default_gateways.includes('custom')) {
                // console.log('is checked!');
                // jQuery("#idehweb_phone_number_ccode").chosen();
                related_to_custom.css('display', 'table-row');


            } else {
                // console.log('is not checked!');
                related_to_custom.css('display', 'none');


            }


        }
        if (lwp_data_tab == 'lwp-tab-form-settings') {
            jQuery('.ilwplabel.' + lwp_data_tab).css('display', 'table-row')


            if (edf6.is(':checked')) {
                console.log('edf6 is checked.')
                related_to_position_fixed.css('display', 'table-row');

            } else {
                console.log('edf6 is not checked.')
                related_to_position_fixed.css('display', 'none');
            }


            if (edf7.is(':checked')) {
                console.log('edf7 is checked.')
                related_to_accept_terms.css('display', 'table-row');

            } else {
                console.log('edf7 is not checked.', related_to_accept_terms)
                related_to_accept_terms.css('display', 'none');
            }
        }
        if (lwp_data_tab == 'lwp-tab-installation-settings') {
            jQuery('.ilwplabel.' + lwp_data_tab).css('display', 'table-row')
        }
        console.log("lwp_data_tab", lwp_data_tab)
    });
    jQuery('input#lwp_media_media_manager').click(function (e) {

        e.preventDefault();
        var image_frame;
        if (image_frame) {
            image_frame.open();
        }
        // Define image_frame as wp.media object
        image_frame = wp.media({
            title: 'Select Media',
            multiple: false,
            library: {
                type: 'image',
            }
        });

        image_frame.on('close', function () {
            // On close, get selections and save to the hidden input
            // plus other AJAX stuff to refresh the image preview
            var selection = image_frame.state().get('selection');
            var gallery_ids = new Array();
            var my_index = 0;
            selection.each(function (attachment) {
                gallery_ids[my_index] = attachment['id'];
                my_index++;
            });
            var ids = gallery_ids.join(",");
            if (ids.length === 0) return true;//if closed withput selecting an image
            jQuery('input#lwp_media_image_id').val(ids);
            Refresh_Image(ids);
        });

        image_frame.on('open', function () {
            // On open, get the id from the hidden input
            // and select the appropiate images in the media manager
            var selection = image_frame.state().get('selection');
            var ids = jQuery('input#lwp_media_image_id').val().split(',');
            ids.forEach(function (id) {
                var attachment = wp.media.attachment(id);
                attachment.fetch();
                selection.add(attachment ? [attachment] : []);
            });

        });

        image_frame.open();
    });
    jQuery('input#lwp_media_background_manager').click(function (e) {

        e.preventDefault();
        console.log('hi');
        var image_frame;
        if (image_frame) {
            image_frame.open();
        }
        // Define image_frame as wp.media object
        image_frame = wp.media({
            title: 'Select Media',
            multiple: false,
            library: {
                type: 'image',
            }
        });

        image_frame.on('close', function () {
            // On close, get selections and save to the hidden input
            // plus other AJAX stuff to refresh the image preview
            var selection = image_frame.state().get('selection');
            var gallery_ids = new Array();
            var my_index = 0;
            selection.each(function (attachment) {
                gallery_ids[my_index] = attachment['id'];
                my_index++;
            });
            var ids = gallery_ids.join(",");
            if (ids.length === 0) return true;//if closed withput selecting an image
            jQuery('input#lwp_media_background_id').val(ids);
            Refresh_Image(ids, '#lwp_media-preview-background-image');
        });

        image_frame.on('open', function () {
            // On open, get the id from the hidden input
            // and select the appropiate images in the media manager
            var selection = image_frame.state().get('selection');
            var ids = jQuery('input#lwp_media_background_id').val().split(',');
            ids.forEach(function (id) {
                var attachment = wp.media.attachment(id);
                attachment.fetch();
                selection.add(attachment ? [attachment] : []);
            });

        });

        image_frame.open();
    });

});

// Ajax request to refresh the image preview
function Refresh_Image(the_id, id = '#lwp_media-preview-image') {
    var data = {
        action: 'lwp_media_get_image',
        id: the_id
    };

    jQuery.get(ajaxurl, data, function (response) {

        if (response.success === true) {
            jQuery(id).replaceWith(response.data.image);
        }
    });
}