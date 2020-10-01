<?php
/*
Plugin Name: Login with phone number
Plugin URI: http://idehweb.com/login-with-phone-number
Description: Login with phone number - sending sms - activate user by phone number - limit pages to login - register and login with ajax - modal
Version: 1.1.11
Author: Hamid Alinia - idehweb
Author URI: http://idehweb.com
Text Domain: login-with-phone-number
Domain Path: /languages
*/

class idehwebLwp
{
    public $textdomain = 'login-with-phone-number';

    function __construct()
    {
        add_action('init', array(&$this, 'idehweb_lwp_textdomain'));
        add_action('admin_init', array(&$this, 'admin_init'));
        add_action('admin_menu', array(&$this, 'admin_menu'));
        add_action('wp_enqueue_scripts', array(&$this, 'enqueue_scripts'));
        add_action('wp_ajax_idehweb_lwp_auth_customer', array(&$this, 'idehweb_lwp_auth_customer'));
        add_action('wp_ajax_idehweb_lwp_activate_customer', array(&$this, 'idehweb_lwp_activate_customer'));
        add_action('wp_ajax_idehweb_lwp_check_credit', array(&$this, 'idehweb_lwp_check_credit'));
        add_action('wp_ajax_idehweb_lwp_get_shop', array(&$this, 'idehweb_lwp_get_shop'));
        add_action('wp_ajax_lwp_ajax_login', array(&$this, 'lwp_ajax_login'));
        add_action('wp_ajax_lwp_update_password_action', array(&$this, 'lwp_update_password_action'));
        add_action('wp_ajax_lwp_enter_password_action', array(&$this, 'lwp_enter_password_action'));
        add_action('wp_ajax_lwp_ajax_login_with_email', array(&$this, 'lwp_ajax_login_with_email'));
        add_action('wp_ajax_lwp_ajax_register', array(&$this, 'lwp_ajax_register'));
        add_action('wp_ajax_lwp_forgot_password', array(&$this, 'lwp_forgot_password'));
        add_action('wp_ajax_nopriv_lwp_ajax_login', array(&$this, 'lwp_ajax_login'));
        add_action('wp_ajax_nopriv_lwp_ajax_login_with_email', array(&$this, 'lwp_ajax_login_with_email'));
        add_action('wp_ajax_nopriv_lwp_ajax_register', array(&$this, 'lwp_ajax_register'));
        add_action('wp_ajax_nopriv_lwp_update_password_action', array(&$this, 'lwp_update_password_action'));
        add_action('wp_ajax_nopriv_lwp_enter_password_action', array(&$this, 'lwp_enter_password_action'));
        add_action('wp_ajax_nopriv_lwp_forgot_password', array(&$this, 'lwp_forgot_password'));
        add_action('activated_plugin', array(&$this, 'lwp_activation_redirect'));
//        add_action('admin_bar_menu', array(&$this, 'credit_adminbar'), 100);
//        add_action('login_enqueue_scripts', array(&$this, 'admin_custom_css'));


        add_filter('manage_users_columns', array(&$this, 'new_modify_user_table'));
        add_filter('manage_users_custom_column', array(&$this, 'new_modify_user_table_row'), 10, 3);


        add_shortcode('idehweb_lwp', array(&$this, 'shortcode'));

    }

    function lwp_activation_redirect($plugin)
    {
        if ($plugin == plugin_basename(__FILE__)) {
            exit(wp_redirect(admin_url('admin.php?page=idehweb-lwp')));
        }
    }

    function idehweb_lwp_textdomain()
    {
        $idehweb_lwp_lang_dir = dirname(plugin_basename(__FILE__)) . '/languages/';
        $idehweb_lwp_lang_dir = apply_filters('idehweb_lwp_languages_directory', $idehweb_lwp_lang_dir);

        load_plugin_textdomain($this->textdomain, false, $idehweb_lwp_lang_dir);


    }

    function admin_init()
    {
        $options = get_option('idehweb_lwp_settings');
        register_setting('idehweb-lwp', 'idehweb_lwp_settings', array(&$this, 'settings_validate'));
        add_settings_section('idehweb-lwp', '', array(&$this, 'section_intro'), 'idehweb-lwp');

        add_settings_field('idehweb_sms_login', __('Enable phone number login', $this->textdomain), array(&$this, 'setting_idehweb_sms_login'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel']);

        $ghgfd = '';
        if ($options['idehweb_phone_number'] && $options['idehweb_token']) {
            $ghgfd = ' none';
        }
//        add_settings_field('idehweb_phone_number_ccode', __('Enter your Country Code', $this->textdomain), array(&$this, 'setting_idehweb_phone_number'), 'idehweb-lwp', 'idehweb-lwp', ['class' => 'ilwplabel lwp_phone_number_label related_to_login' . $ghgfd]);
        add_settings_field('idehweb_phone_number', __('Enter your phone number', $this->textdomain), array(&$this, 'setting_idehweb_phone_number'), 'idehweb-lwp', 'idehweb-lwp', ['class' => 'ilwplabel lwp_phone_number_label related_to_login' . $ghgfd]);
//        if (!isset($options['idehweb_phone_number'])) $options['idehweb_phone_number'] = '';
        add_settings_field('idehweb_token', __('Enter api key', $this->textdomain), array(&$this, 'setting_idehweb_token'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel alwaysDisplayNone']);
        if ($options['idehweb_phone_number'] && $options['idehweb_token']) {

            add_settings_field('idehweb_country_codes', __('Country code accepted in front', $this->textdomain), array(&$this, 'setting_country_code'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel related_to_login']);
            add_settings_field('idehweb_sms_shop', __('Buy credit here', $this->textdomain), array(&$this, 'setting_buy_credit'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel related_to_login']);
        }
        //        $display = 'inherit';

//            $display = 'none';

        add_settings_field('idehweb_email_login', __('Enable email login', $this->textdomain), array(&$this, 'setting_idehweb_email_login'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel']);
        add_settings_field('idehweb_password_login', __('Enable password login', $this->textdomain), array(&$this, 'setting_idehweb_password_login'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel']);
        add_settings_field('idehweb_redirect_url', __('Enter redirect url', $this->textdomain), array(&$this, 'setting_idehweb_url_redirect'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel']);
        add_settings_field('idehweb_position_form', __('Enable fix position', $this->textdomain), array(&$this, 'idehweb_position_form'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel']);
        add_settings_field('instructions', __('Shortcode and Template Tag', $this->textdomain), array(&$this, 'setting_instructions'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel']);
//        }
//        add_settings_section('idehweb-lwp', '', array(&$this, 'section_intro'), 'idehweb-lwp');

    }

    function admin_menu()
    {

        $icon_url = 'dashicons-smartphone';
        $page_hook = add_menu_page(
            __('login setting', $this->textdomain),
            __('login setting', $this->textdomain),
            'manage_options',
            'idehweb-lwp',
            array(&$this, 'settings_page'),
            $icon_url
        );
        add_action('admin_print_styles-' . $page_hook, array(&$this, 'admin_custom_css'));
//        wp_enqueue_script('idehweb-lwp-admin-chosen', plugins_url('/scripts/chosen.jquery.js', __FILE__), array('jquery'), true, true);

    }

    function admin_custom_css()
    {
        wp_enqueue_style('idehweb-lwp-admin', plugins_url('/styles/lwp-admin.css', __FILE__));
//        wp_enqueue_style('idehweb-lwp-admin-chosen-style', plugins_url('/styles/chosen.min.css', __FILE__));


    }

    function settings_page()
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_phone_number'])) $options['idehweb_phone_number'] = '';

        ?>
        <div class="wrap">
            <div id="icon-themes" class="icon32"></div>
            <h2><?php _e('idehwebLwp Settings', $this->textdomain); ?></h2>
            <?php if (isset($_GET['settings-updated']) && $_GET['settings-updated']) {

                ?>
                <div id="setting-error-settings_updated" class="updated settings-error">
                    <p><strong><?php _e('Settings saved.', $this->textdomain); ?></strong></p>
                </div>
            <?php } ?>
            <form action="options.php" method="post" id="iuytfrdghj">
                <?php settings_fields('idehweb-lwp'); ?>
                <?php do_settings_sections('idehweb-lwp'); ?>

                <p class="submit">
                    <span id="wkdugchgwfchevg3r4r"></span>
                </p>
                <p class="submit">
                    <span id="oihdfvygehv"></span>
                </p>
                <p class="submit">
                    <?php

                    //                    if (!$options['idehweb_phone_number']){
                    //
                    ?>

                    <?php

                    //                    }else{
                    ?>
                    <input type="submit" class="button-primary"
                           value="<?php _e('Save Changes', $this->textdomain); ?>"/></p>
                <!--            --><?php //}
                ?>
            </form>
            <script type="text/javascript">window.$crisp=[];window.CRISP_WEBSITE_ID="99fd6613-af76-4745-80b6-8931ec5e0daa";(function(){d=document;s=d.createElement("script");s.src="https://client.crisp.chat/l.js";s.async=1;d.getElementsByTagName("head")[0].appendChild(s);})();</script>

            <script>
                <?php

                ?>
                jQuery(function ($) {

                    $(window).load(function () {

                        $('.loiuyt').click();
                        $('.refreshShop').click();
                        // $("#idehweb_country_codes").chosen();
                        // if ($('#idehweb_phone_number_ccode').is(':visible'))
                        //     $("#idehweb_phone_number_ccode").chosen();

                    });

                    var edf = $('#idehweb_lwp_settings_idehweb_sms_login');
                    var idehweb_body = $('body');
                    var dfg = $('.related_to_login');

                    if (edf.is(':checked')) {
                        console.log('is checked!');
                        dfg.css('display', 'table-row');
                        // $("#idehweb_phone_number_ccode").chosen();


                    } else {
                        console.log('is not checked!');

                        dfg.css('display', 'none');
                    }
                    $('#idehweb_lwp_settings_idehweb_sms_login').change(
                        function () {
                            if (this.checked && this.value == '1') {
                                console.log('change is checked!');

                                dfg.css('display', 'table-row');
                                // $("#idehweb_phone_number_ccode").chosen();

                            } else {
                                console.log('change is not checked!');

                                dfg.css('display', 'none');
                            }
                        });
                    idehweb_body.on('click', '.loiuyt',
                        function () {

                            $.ajax({
                                type: "GET",
                                url: ajaxurl,
                                data: {action: 'idehweb_lwp_check_credit'}
                            }).done(function (msg) {
                                var arr = JSON.parse(msg);
                                console.log(arr);
                                $('.creditor .cp').html('<?php _e('Your Credit:', $this->textdomain) ?>' + ' ' + arr['credit'])


                            });

                        });
                    idehweb_body.on('click', '.refreshShop',
                        function () {
                            var lwp_token = $('#lwp_token').val();
                            if (lwp_token) {
                                $.ajax({
                                    type: "GET",
                                    url: ajaxurl,
                                    data: {action: 'idehweb_lwp_get_shop'}
                                }).done(function (msg) {
                                    if (msg) {
                                        var arr = JSON.parse(msg);
                                        if (arr && arr.products) {
                                            $('.chargeAccount').empty();
                                            for (var j = 0; j < arr.products.length; j++) {
                                                $('.chargeAccount').append('<div class="col-lg-2 col-md-4 col-sm-6">' +
                                                    '<div class="lwp-produ-wrap">' +
                                                    '<div class="lwp-shop-title">' +
                                                    arr.products[j].title + ' ' +
                                                    '</div>' +
                                                    '<div class="lwp-shop-price">' +
                                                    arr.products[j].price +
                                                    '</div>' +
                                                    '<div class="lwp-shop-buy">' +
                                                    '<a target="_blank" href="' + arr.products[j].buy + lwp_token + '/' + arr.products[j].ID + '">' + '<?php _e("Buy", $this->textdomain); ?>' + '</a>' +
                                                    '</div>' +
                                                    '</div>' +
                                                    '</div>'
                                                )

                                            }
                                        }
                                    }

                                });
                            }

                        });
                    idehweb_body.on('click', '.auth',
                        function () {
                            var lwp_phone_number = $('#lwp_phone_number').val();
                            var idehweb_phone_number_ccode = $('#idehweb_phone_number_ccode').val();
                            // alert(idehweb_phone_number_ccode);
                            // return;
                            if (lwp_phone_number) {
                                $('.lwp_phone_number_label th').html('enter code messaged to you!');
                                $('#lwp_phone_number').css('display', 'none');
                                $('#lwp_secod').css('display', 'inherit');
                                $('.i34').css('display', 'inline-block');
                                $('.i35').css('display', 'none');
                                $('.idehweb_phone_number_ccode_wrap').css('display', 'none');
                                // $('#lwp_secod').html('enter code messaged to you!');
                                lwp_phone_number = idehweb_phone_number_ccode + lwp_phone_number;
                                $.ajax({
                                    type: "GET",
                                    url: ajaxurl,
                                    data: {
                                        action: 'idehweb_lwp_auth_customer',
                                        phone_number: lwp_phone_number,
                                        country_code: idehweb_phone_number_ccode
                                    }
                                }).done(function (msg) {
                                    if (msg) {
                                        var arr = JSON.parse(msg);
                                        console.log(arr);
                                    }
                                    // $('form#iuytfrdghj').submit();

                                });

                            }
                        });
                    idehweb_body.on('click', '.lwpchangePhoneNumber',
                        function (e) {
                            e.preventDefault();
                            $('.lwp_phone_number_label').removeClass('none');
                            $('#lwp_phone_number').focus();
                            // $("#idehweb_phone_number_ccode").chosen();

                        });
                    idehweb_body.on('click', '.lwp_more_help', function () {
                        createTutorial();
                    });
                    idehweb_body.on('click', '.lwp_close , .lwp_button', function (e) {
                        e.preventDefault();
                        $('.lwp_modal').remove();
                        $('.lwp_modal_overlay').remove();
                        localStorage.setItem('ldwtutshow', 1);
                    });
                    idehweb_body.on('click', '.activate',
                        function () {

                            var lwp_phone_number = $('#lwp_phone_number').val();
                            var lwp_secod = $('#lwp_secod').val();
                            var idehweb_phone_number_ccode = $('#idehweb_phone_number_ccode').val();

                            if (lwp_phone_number && lwp_secod && idehweb_phone_number_ccode) {
                                lwp_phone_number = idehweb_phone_number_ccode + lwp_phone_number;
                                $.ajax({
                                    type: "GET",
                                    url: ajaxurl,
                                    data: {
                                        action: 'idehweb_lwp_activate_customer', phone_number: lwp_phone_number,
                                        secod: lwp_secod
                                    }
                                }).done(function (msg) {
                                    if (msg) {
                                        var arr = JSON.parse(msg);
                                        console.log(arr);
                                        if (arr['token']) {
                                            $('#lwp_token').val(arr['token']);
                                            $('form#iuytfrdghj').submit();
                                        }
                                    }
                                });

                            }
                        });
                    var ldwtutshow = localStorage.getItem('ldwtutshow');
                    if (ldwtutshow === null) {
                        // localStorage.setItem('ldwtutshow', 1);
                        // Show popup here
                        // $('#myModal').modal('show');
                        console.log('set here');
                        createTutorial();
                    }

                    function createTutorial() {
                        var wrap = $('.wrap');
                        wrap.prepend('<div class="lwp_modal_overlay"></div>')
                            .prepend('<div class="lwp_modal">' +
                                '<div class="lwp_modal_header">' +
                                '<div class="lwp_l"></div>' +
                                '<div class="lwp_r"><button class="lwp_close">x</button></div>' +
                                '</div>' +
                                '<div class="lwp_modal_body">' +
                                '<ul>' +
                                '<li>'+'<?php _e("1. create a page and name it login or register or what ever", $this->textdomain) ?>'+'</li>' +
                                '<li>'+'<?php _e("2. copy this shortcode <code>[idehweb_lwp]</code> and paste in the page you created at step 1", $this->textdomain) ?>'+'</li>' +
                                '<li>'+'<?php _e("3. now, that is your login page. check your login page with other device or browser that you are not logged in!", $this->textdomain) ?>'+
                                '</li>' +
                                '<li>' +
                                '<?php _e("for more information visit: ", $this->textdomain) ?>'+'<a target="_blank" href="https://idehweb.com/login-with-phone-number/?lang=en">Idehweb</a>'+
                                '</li>' +
                                '</ul>' +
                                '</div>' +
                                '<div class="lwp_modal_footer">' +
                                '<button class="lwp_button"><?php _e("got it ", $this->textdomain) ?></button>' +
                                '</div>' +
                                '</div>');

                    }
                });
            </script>
        </div>
        <?php
    }

    function section_intro()
    {
        ?>
        <!--<div class="lwp_sections">-->
        <!---->
        <!--</div>-->
        <?php

    }

    function setting_idehweb_email_login()
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_email_login'])) $options['idehweb_email_login'] = '1';
        $display = 'inherit';
        if (!isset($options['idehweb_phone_number'])) $options['idehweb_phone_number'] = '';
        if (!$options['idehweb_phone_number']) {
            $display = 'none';
        }
        echo '<input  type="hidden" name="idehweb_lwp_settings[idehweb_email_login]" value="0" />
		<label><input type="checkbox" name="idehweb_lwp_settings[idehweb_email_login]" value="1"' . (($options['idehweb_email_login']) ? ' checked="checked"' : '') . ' />' . __('I want user login with email', $this->textdomain) . '</label>';

    }

    function setting_idehweb_sms_login()
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_sms_login'])) $options['idehweb_sms_login'] = '0';
        $display = 'inherit';
        if (!isset($options['idehweb_phone_number'])) $options['idehweb_phone_number'] = '';
        if (!$options['idehweb_phone_number']) {
            $display = 'none';
        }
        echo '<input  type="hidden" name="idehweb_lwp_settings[idehweb_sms_login]" value="0" />
		<label><input type="checkbox" id="idehweb_lwp_settings_idehweb_sms_login" name="idehweb_lwp_settings[idehweb_sms_login]" value="1"' . (($options['idehweb_sms_login']) ? ' checked="checked"' : '') . ' />' . __('I want user login with phone number', $this->textdomain) . '</label>';

    }

    function setting_idehweb_password_login()
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_password_login'])) $options['idehweb_password_login'] = '1';
        $display = 'inherit';
        if (!isset($options['idehweb_phone_number'])) $options['idehweb_phone_number'] = '';
        if (!$options['idehweb_phone_number']) {
            $display = 'none';
        }
        echo '<input type="hidden" name="idehweb_lwp_settings[idehweb_password_login]" value="0" />
		<label><input type="checkbox" name="idehweb_lwp_settings[idehweb_password_login]" value="1"' . (($options['idehweb_password_login']) ? ' checked="checked"' : '') . ' />' . __('I want user login with password too', $this->textdomain) . '</label>';

    }

    function idehweb_position_form()
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_position_form'])) $options['idehweb_position_form'] = '0';

        echo '<input type="hidden" name="idehweb_lwp_settings[idehweb_position_form]" value="0" />
		<label><input type="checkbox" name="idehweb_lwp_settings[idehweb_position_form]" value="1"' . (($options['idehweb_position_form']) ? ' checked="checked"' : '') . ' />' . __('I want form shows on page in fix position', $this->textdomain) . '</label>';

    }

    function credit_adminbar()
    {
        global $wp_admin_bar, $melipayamak;
        if (!is_super_admin() || !is_admin_bar_showing())
            return;

        $credit = '0';
//        $wp_admin_bar -> add_menu(array('id' => 'melipayamak', 'title' => __('sms credit: ',$this->textdomain).'<span class="lwpcreditupdate">'.$credit.'</span>', 'href' => get_bloginfo('url') . '/wp-admin/admin.php?page=melipayamak'));

//        $wp_admin_bar->add_menu(array('id' => 'lwpcreditbar', 'title' => '<span class="ab-icon dashicons dashicons-smartphone"></span><span class="lwpcreditupdate">' . $credit . '</span>', 'href' => get_bloginfo('url') . '/wp-admin/admin.php?page=idehweb-lwp'));
        ?>
        <!--        <script>-->
        <!---->
        <!--            jQuery(function ($) {-->
        <!--                $(window).load(function () {-->
        <!--                    $.ajax({-->
        <!--                        type: "GET",-->
        <!--                        url: ajaxurl,-->
        <!--                        data: {action: 'idehweb_lwp_check_credit'}-->
        <!--                    }).done(function (msg) {-->
        <!--                        var arr = JSON.parse(msg);-->
        <!--                        console.log(arr);-->
        <!--                        $('.lwpcreditupdate').html(arr['credit'])-->
        <!---->
        <!---->
        <!--                    });-->
        <!--                });-->
        <!--            });-->
        <!--        </script>-->
        <?php
        //        $balance = $melipayamak -> credit;
//        if ($balance && $melipayamak -> is_ready) {
//            $balance = number_format($balance);
//            $wp_admin_bar -> add_menu(array('parent' => 'melipayamak', 'title' => 'موجودی حساب: ' . $balance . ' پیامک', 'href' => get_bloginfo('url') . '/wp-admin/admin.php?page=melipayamak_setting'));
//        }
//        $t = 'اعضای خبرنامه: ' . number_format(intval($melipayamak -> count)) . ' نفر';
//        $wp_admin_bar -> add_menu(array('parent' => 'melipayamak', 'title' => $t, 'href' => get_bloginfo('url') . '/wp-admin/admin.php?page=melipayamak_phonebook'));
//        $wp_admin_bar -> add_menu(array('parent' => 'melipayamak', 'title' => 'مشاهده پیام ها', 'href' => get_bloginfo('url') . '/wp-admin/admin.php?page=melipayamak_smessages'));
//        $wp_admin_bar -> add_menu(array('parent' => 'melipayamak', 'title' => 'ملی پیامک', 'href' => 'http://melipayamak.com'));
    }

    function setting_idehweb_phone_number()
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_phone_number'])) $options['idehweb_phone_number'] = '';
        if (!isset($options['idehweb_phone_number_ccode'])) $options['idehweb_phone_number_ccode'] = '';
        ?>
        <div class="idehweb_phone_number_ccode_wrap">
            <select name="idehweb_lwp_settings[idehweb_phone_number_ccode]" id="idehweb_phone_number_ccode"
                    data-placeholder="<?php _e('Choose a country...', $this->textdomain); ?>">
                <?php
                $country_codes = $this->get_country_code_options();

                foreach ($country_codes as $country) {
                    echo '<option value="' . $country["value"] . '" ' . (($options['idehweb_phone_number_ccode'] == $country["value"]) ? ' selected="selected"' : '') . ' >+' . $country['value'] . ' - ' . $country["code"] . '</option>';
                }
                ?>
            </select>
            <?php
            echo '<input placeholder="Ex: 9120539945" type="text" name="idehweb_lwp_settings[idehweb_phone_number]" id="lwp_phone_number" class="regular-text" value="' . esc_attr($options['idehweb_phone_number']) . '" />';
            ?>
        </div>
        <?php
        echo '<input type="text" name="idehweb_lwp_settings[idehweb_secod]" id="lwp_secod" class="regular-text" style="display:none" value="" placeholder="_ _ _ _ _ _" />';
        ?>
        <button type="button" class="button-primary auth i35"
                value="<?php _e('Authenticate', $this->textdomain); ?>"><?php _e('activate sms login', $this->textdomain); ?></button>
        <button type="button" class="button-primary activate i34" style="display: none"
                value="<?php _e('Activate', $this->textdomain); ?>"><?php _e('activate account', $this->textdomain); ?></button>

        <?php
    }

    function setting_idehweb_token()
    {
        $options = get_option('idehweb_lwp_settings');
        $display = 'inherit';
        if (!isset($options['idehweb_token'])) $options['idehweb_token'] = '';
        if (!isset($options['idehweb_phone_number'])) $options['idehweb_phone_number'] = '';
        if (!$options['idehweb_phone_number']) {
            $display = 'none';
        }
        echo '<input id="lwp_token" type="text" name="idehweb_lwp_settings[idehweb_token]" class="regular-text" value="' . esc_attr($options['idehweb_token']) . '" />
		<p class="description">' . __('enter api key', $this->textdomain) . '</p>';

    }

    function setting_idehweb_url_redirect()
    {
        $options = get_option('idehweb_lwp_settings');
        $display = 'inherit';
        if (!isset($options['idehweb_redirect_url'])) $options['idehweb_redirect_url'] = '';
        if (!isset($options['idehweb_phone_number'])) $options['idehweb_phone_number'] = '';
        if (!$options['idehweb_phone_number']) {
            $display = 'none';
        }
        echo '<input id="lwp_token" type="text" name="idehweb_lwp_settings[idehweb_redirect_url]" class="regular-text" value="' . esc_attr($options['idehweb_redirect_url']) . '" />
		<p class="description">' . __('enter redirect url', $this->textdomain) . '</p>';

    }

    function get_country_code_options()
    {

        $json_countries = '[["Afghanistan (‫افغانستان‬‎)", "af", "93"], ["Albania (Shqipëri)", "al", "355"], ["Algeria (‫الجزائر‬‎)", "dz", "213"], ["American Samoa", "as", "1684"], ["Andorra", "ad", "376"], ["Angola", "ao", "244"], ["Anguilla", "ai", "1264"], ["Antigua and Barbuda", "ag", "1268"], ["Argentina", "ar", "54"], ["Armenia (Հայաստան)", "am", "374"], ["Aruba", "aw", "297"], ["Australia", "au", "61", 0], ["Austria (Österreich)", "at", "43"], ["Azerbaijan (Azərbaycan)", "az", "994"], ["Bahamas", "bs", "1242"], ["Bahrain (‫البحرين‬‎)", "bh", "973"], ["Bangladesh (বাংলাদেশ)", "bd", "880"], ["Barbados", "bb", "1246"], ["Belarus (Беларусь)", "by", "375"], ["Belgium (België)", "be", "32"], ["Belize", "bz", "501"], ["Benin (Bénin)", "bj", "229"], ["Bermuda", "bm", "1441"], ["Bhutan (འབྲུག)", "bt", "975"], ["Bolivia", "bo", "591"], ["Bosnia and Herzegovina (Босна и Херцеговина)", "ba", "387"], ["Botswana", "bw", "267"], ["Brazil (Brasil)", "br", "55"], ["British Indian Ocean Territory", "io", "246"], ["British Virgin Islands", "vg", "1284"], ["Brunei", "bn", "673"], ["Bulgaria (България)", "bg", "359"], ["Burkina Faso", "bf", "226"], ["Burundi (Uburundi)", "bi", "257"], ["Cambodia (កម្ពុជា)", "kh", "855"], ["Cameroon (Cameroun)", "cm", "237"], ["Canada", "ca", "1", 1, ["204", "226", "236", "249", "250", "289", "306", "343", "365", "387", "403", "416", "418", "431", "437", "438", "450", "506", "514", "519", "548", "579", "581", "587", "604", "613", "639", "647", "672", "705", "709", "742", "778", "780", "782", "807", "819", "825", "867", "873", "902", "905"]], ["Cape Verde (Kabu Verdi)", "cv", "238"], ["Caribbean Netherlands", "bq", "599", 1], ["Cayman Islands", "ky", "1345"], ["Central African Republic (République centrafricaine)", "cf", "236"], ["Chad (Tchad)", "td", "235"], ["Chile", "cl", "56"], ["China (中国)", "cn", "86"], ["Christmas Island", "cx", "61", 2], ["Cocos (Keeling) Islands", "cc", "61", 1], ["Colombia", "co", "57"], ["Comoros (‫جزر القمر‬‎)", "km", "269"], ["Congo (DRC) (Jamhuri ya Kidemokrasia ya Kongo)", "cd", "243"], ["Congo (Republic) (Congo-Brazzaville)", "cg", "242"], ["Cook Islands", "ck", "682"], ["Costa Rica", "cr", "506"], ["Côte d’Ivoire", "ci", "225"], ["Croatia (Hrvatska)", "hr", "385"], ["Cuba", "cu", "53"], ["Curaçao", "cw", "599", 0], ["Cyprus (Κύπρος)", "cy", "357"], ["Czech Republic (Česká republika)", "cz", "420"], ["Denmark (Danmark)", "dk", "45"], ["Djibouti", "dj", "253"], ["Dominica", "dm", "1767"], ["Dominican Republic (República Dominicana)", "do", "1", 2, ["809", "829", "849"]], ["Ecuador", "ec", "593"], ["Egypt (‫مصر‬‎)", "eg", "20"], ["El Salvador", "sv", "503"], ["Equatorial Guinea (Guinea Ecuatorial)", "gq", "240"], ["Eritrea", "er", "291"], ["Estonia (Eesti)", "ee", "372"], ["Ethiopia", "et", "251"], ["Falkland Islands (Islas Malvinas)", "fk", "500"], ["Faroe Islands (Føroyar)", "fo", "298"], ["Fiji", "fj", "679"], ["Finland (Suomi)", "fi", "358", 0], ["France", "fr", "33"], ["French Guiana (Guyane française)", "gf", "594"], ["French Polynesia (Polynésie française)", "pf", "689"], ["Gabon", "ga", "241"], ["Gambia", "gm", "220"], ["Georgia (საქართველო)", "ge", "995"], ["Germany (Deutschland)", "de", "49"], ["Ghana (Gaana)", "gh", "233"], ["Gibraltar", "gi", "350"], ["Greece (Ελλάδα)", "gr", "30"], ["Greenland (Kalaallit Nunaat)", "gl", "299"], ["Grenada", "gd", "1473"], ["Guadeloupe", "gp", "590", 0], ["Guam", "gu", "1671"], ["Guatemala", "gt", "502"], ["Guernsey", "gg", "44", 1], ["Guinea (Guinée)", "gn", "224"], ["Guinea-Bissau (Guiné Bissau)", "gw", "245"], ["Guyana", "gy", "592"], ["Haiti", "ht", "509"], ["Honduras", "hn", "504"], ["Hong Kong (香港)", "hk", "852"], ["Hungary (Magyarország)", "hu", "36"], ["Iceland (Ísland)", "is", "354"], ["India (भारत)", "in", "91"], ["Indonesia", "id", "62"], ["Iran (‫ایران‬‎)", "ir", "98"], ["Iraq (‫العراق‬‎)", "iq", "964"], ["Ireland", "ie", "353"], ["Isle of Man", "im", "44", 2], ["Israel (‫ישראל‬‎)", "il", "972"], ["Italy (Italia)", "it", "39", 0], ["Jamaica", "jm", "1", 4, ["876", "658"]], ["Japan (日本)", "jp", "81"], ["Jersey", "je", "44", 3], ["Jordan (‫الأردن‬‎)", "jo", "962"], ["Kazakhstan (Казахстан)", "kz", "7", 1], ["Kenya", "ke", "254"], ["Kiribati", "ki", "686"], ["Kosovo", "xk", "383"], ["Kuwait (‫الكويت‬‎)", "kw", "965"], ["Kyrgyzstan (Кыргызстан)", "kg", "996"], ["Laos (ລາວ)", "la", "856"], ["Latvia (Latvija)", "lv", "371"], ["Lebanon (‫لبنان‬‎)", "lb", "961"], ["Lesotho", "ls", "266"], ["Liberia", "lr", "231"], ["Libya (‫ليبيا‬‎)", "ly", "218"], ["Liechtenstein", "li", "423"], ["Lithuania (Lietuva)", "lt", "370"], ["Luxembourg", "lu", "352"], ["Macau (澳門)", "mo", "853"], ["Macedonia (FYROM) (Македонија)", "mk", "389"], ["Madagascar (Madagasikara)", "mg", "261"], ["Malawi", "mw", "265"], ["Malaysia", "my", "60"], ["Maldives", "mv", "960"], ["Mali", "ml", "223"], ["Malta", "mt", "356"], ["Marshall Islands", "mh", "692"], ["Martinique", "mq", "596"], ["Mauritania (‫موريتانيا‬‎)", "mr", "222"], ["Mauritius (Moris)", "mu", "230"], ["Mayotte", "yt", "262", 1], ["Mexico (México)", "mx", "52"], ["Micronesia", "fm", "691"], ["Moldova (Republica Moldova)", "md", "373"], ["Monaco", "mc", "377"], ["Mongolia (Монгол)", "mn", "976"], ["Montenegro (Crna Gora)", "me", "382"], ["Montserrat", "ms", "1664"], ["Morocco (‫المغرب‬‎)", "ma", "212", 0], ["Mozambique (Moçambique)", "mz", "258"], ["Myanmar (Burma) (မြန်မာ)", "mm", "95"], ["Namibia (Namibië)", "na", "264"], ["Nauru", "nr", "674"], ["Nepal (नेपाल)", "np", "977"], ["Netherlands (Nederland)", "nl", "31"], ["New Caledonia (Nouvelle-Calédonie)", "nc", "687"], ["New Zealand", "nz", "64"], ["Nicaragua", "ni", "505"], ["Niger (Nijar)", "ne", "227"], ["Nigeria", "ng", "234"], ["Niue", "nu", "683"], ["Norfolk Island", "nf", "672"], ["North Korea (조선 민주주의 인민 공화국)", "kp", "850"], ["Northern Mariana Islands", "mp", "1670"], ["Norway (Norge)", "no", "47", 0], ["Oman (‫عُمان‬‎)", "om", "968"], ["Pakistan (‫پاکستان‬‎)", "pk", "92"], ["Palau", "pw", "680"], ["Palestine (‫فلسطين‬‎)", "ps", "970"], ["Panama (Panamá)", "pa", "507"], ["Papua New Guinea", "pg", "675"], ["Paraguay", "py", "595"], ["Peru (Perú)", "pe", "51"], ["Philippines", "ph", "63"], ["Poland (Polska)", "pl", "48"], ["Portugal", "pt", "351"], ["Puerto Rico", "pr", "1", 3, ["787", "939"]], ["Qatar (‫قطر‬‎)", "qa", "974"], ["Réunion (La Réunion)", "re", "262", 0], ["Romania (România)", "ro", "40"], ["Russia (Россия)", "ru", "7", 0], ["Rwanda", "rw", "250"], ["Saint Barthélemy", "bl", "590", 1], ["Saint Helena", "sh", "290"], ["Saint Kitts and Nevis", "kn", "1869"], ["Saint Lucia", "lc", "1758"], ["Saint Martin (Saint-Martin (partie française))", "mf", "590", 2], ["Saint Pierre and Miquelon (Saint-Pierre-et-Miquelon)", "pm", "508"], ["Saint Vincent and the Grenadines", "vc", "1784"], ["Samoa", "ws", "685"], ["San Marino", "sm", "378"], ["São Tomé and Príncipe (São Tomé e Príncipe)", "st", "239"], ["Saudi Arabia (‫المملكة العربية السعودية‬‎)", "sa", "966"], ["Senegal (Sénégal)", "sn", "221"], ["Serbia (Србија)", "rs", "381"], ["Seychelles", "sc", "248"], ["Sierra Leone", "sl", "232"], ["Singapore", "sg", "65"], ["Sint Maarten", "sx", "1721"], ["Slovakia (Slovensko)", "sk", "421"], ["Slovenia (Slovenija)", "si", "386"], ["Solomon Islands", "sb", "677"], ["Somalia (Soomaaliya)", "so", "252"], ["South Africa", "za", "27"], ["South Korea (대한민국)", "kr", "82"], ["South Sudan (‫جنوب السودان‬‎)", "ss", "211"], ["Spain (España)", "es", "34"], ["Sri Lanka (ශ්‍රී ලංකාව)", "lk", "94"], ["Sudan (‫السودان‬‎)", "sd", "249"], ["Suriname", "sr", "597"], ["Svalbard and Jan Mayen", "sj", "47", 1], ["Swaziland", "sz", "268"], ["Sweden (Sverige)", "se", "46"], ["Switzerland (Schweiz)", "ch", "41"], ["Syria (‫سوريا‬‎)", "sy", "963"], ["Taiwan (台灣)", "tw", "886"], ["Tajikistan", "tj", "992"], ["Tanzania", "tz", "255"], ["Thailand (ไทย)", "th", "66"], ["Timor-Leste", "tl", "670"], ["Togo", "tg", "228"], ["Tokelau", "tk", "690"], ["Tonga", "to", "676"], ["Trinidad and Tobago", "tt", "1868"], ["Tunisia (‫تونس‬‎)", "tn", "216"], ["Turkey (Türkiye)", "tr", "90"], ["Turkmenistan", "tm", "993"], ["Turks and Caicos Islands", "tc", "1649"], ["Tuvalu", "tv", "688"], ["U.S. Virgin Islands", "vi", "1340"], ["Uganda", "ug", "256"], ["Ukraine (Україна)", "ua", "380"], ["United Arab Emirates (‫الإمارات العربية المتحدة‬‎)", "ae", "971"], ["United Kingdom", "gb", "44", 0], ["United States", "us", "1", 0], ["Uruguay", "uy", "598"], ["Uzbekistan (Oʻzbekiston)", "uz", "998"], ["Vanuatu", "vu", "678"], ["Vatican City (Città del Vaticano)", "va", "39", 1], ["Venezuela", "ve", "58"], ["Vietnam (Việt Nam)", "vn", "84"], ["Wallis and Futuna (Wallis-et-Futuna)", "wf", "681"], ["Western Sahara (‫الصحراء الغربية‬‎)", "eh", "212", 1], ["Yemen (‫اليمن‬‎)", "ye", "967"], ["Zambia", "zm", "260"], ["Zimbabwe", "zw", "263"], ["Åland Islands", "ax", "358", 1]]';
        $countries = json_decode($json_countries);
        $retrun_array = array();

        foreach ($countries as $country) {
            $option = array(
                'label' => $country[0] . ' [+' . $country[2] . ']',
                'value' => $country[2],
                'code' => $country[1],
                'is_placeholder' => false,
            );
            array_push($retrun_array, $option);
        }

        return $retrun_array;
    }

    function setting_instructions()
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_phone_number'])) $options['idehweb_phone_number'] = '';
        $display = 'inherit';
        if (!$options['idehweb_phone_number']) {
            $display = 'none';
        }
        echo '<div> <p>' . __('make a page and name it login, put the shortcode inside it, now you have a login page!', $this->textdomain) . '</p>
		<p><code>[idehweb_lwp]</code></p>
		<p>' . __('To use idehwebLwp manually in your theme template use the following PHP code:', $this->textdomain) . '</p>
		<p><code>&lt;?php if( function_exists(\'idehweb_lwp\') ) idehweb_lwp(); ?&gt;</code></p>
		<p><a href="#" class="lwp_more_help">' . __('Need more help?', $this->textdomain) . '</a></p>
		</div>';
    }

    function setting_country_code()
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_country_codes'])) $options['idehweb_country_codes'] = [];
        $country_codes = $this->get_country_code_options();
//        print_r($options['idehweb_country_codes']);

        ?>
        <select name="idehweb_lwp_settings[idehweb_country_codes][]" id="idehweb_country_codes" multiple>
            <?php
            foreach ($country_codes as $country) {
                $rr = in_array($country["value"], $options['idehweb_country_codes']);
                echo '<option value="' . $country["value"] . '" ' . ($rr ? ' selected="selected"' : '') . '>' . $country['label'] . '</option>';
            }
            ?>
        </select>
        <?php

    }

    function setting_buy_credit()
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_phone_number'])) $options['idehweb_phone_number'] = '';
        if (!isset($options['idehweb_phone_number_ccode'])) $options['idehweb_phone_number_ccode'] = '';
        $display = 'inherit';
        if (!$options['idehweb_phone_number']) {
            $display = 'none';
        }
        ?>

        <div class="creditor">
            <button type="button" class="button-primary loiuyt"
                    value="<?php _e('Check credit', $this->textdomain); ?>"><?php _e('Check credit', $this->textdomain); ?></button>
            <span class="cp"></span>

            <button type="button" class="button-primary refreshShop"
                    value="<?php _e('Refresh', $this->textdomain); ?>"><?php _e('Refresh', $this->textdomain); ?></button>
            <span class="df">
                <?php echo _e('phone number:', $this->textdomain) . '+' . $options['idehweb_phone_number_ccode'] . ' ' . $options['idehweb_phone_number']; ?>
                <a href="#" class="lwpchangePhoneNumber">
<?php _e('change', $this->textdomain);
?>
            </a>
            </span>
        </div>


        <div class="chargeAccount">

        </div>
        <?php
    }

    function settings_validate($input)
    {

        return $input;
    }

    function enqueue_scripts()
    {
        wp_enqueue_style('idehweb-lwp', plugins_url('/styles/login-with-phonenumber.css', __FILE__));

        wp_enqueue_script('idehweb-lwp-validate-script', plugins_url('/scripts/jquery.validate.js', __FILE__), array('jquery'));
        wp_enqueue_script('idehweb-lwp', plugins_url('/scripts/login-with-phonenumber.js', __FILE__), array('jquery'));
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_redirect_url'])) $options['idehweb_redirect_url'] = home_url();

        wp_localize_script('idehweb-lwp', 'idehweb_lwp', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'redirecturl' => $options['idehweb_redirect_url'],
            'loadingmessage' => __('please wait...', $this->textdomain),
        ));
    }

    function shortcode($atts)
    {

        extract(shortcode_atts(array(
            'redirect_url' => ''
        ), $atts));
        ob_start();
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_sms_login'])) $options['idehweb_sms_login'] = '0';
        if (!isset($options['idehweb_email_login'])) $options['idehweb_email_login'] = '1';
        if (!isset($options['idehweb_password_login'])) $options['idehweb_password_login'] = '1';
        if (!isset($options['idehweb_redirect_url'])) $options['idehweb_redirect_url'] = '';
        if (!isset($options['idehweb_country_codes'])) $options['idehweb_country_codes'] = [];
        if (!isset($options['idehweb_position_form'])) $options['idehweb_position_form'] = '0';
        $class = '';
        if ($options['idehweb_position_form'] == '1') {
            $class = 'sticky';
        }
        $is_user_logged_in = is_user_logged_in();
        if (!$is_user_logged_in) {
            ?>
            <a id="show_login" class="show_login"
               style="display: none"
               data-sticky="<?php echo esc_attr($options['idehweb_position_form']); ?>"><?php echo __('login', $this->textdomain); ?></a>
            <div class="lwp_forms_login <?php echo esc_attr($class); ?>">
                <?php
                if ($options['idehweb_sms_login']) {
                    ?>
                    <form id="lwp_login" class="ajax-auth" action="login" method="post">

                        <h1><?php echo __('Login / register', $this->textdomain); ?></h1>
                        <p class="status"></p>
                        <?php wp_nonce_field('ajax-login-nonce', 'security'); ?>
                        <label for="lwp_username"><?php echo __('Phone number', $this->textdomain); ?></label>
                        <?php
                        //                    $country_codes = $this->get_country_code_options();
                        ?>
                        <div class="lwp_country_codes_wrap">
                            <select id="lwp_country_codes">
                                <?php
                                foreach ($options['idehweb_country_codes'] as $country) {
//                            $rr=in_array($country["value"],$options['idehweb_country_codes']);
                                    echo '<option value="' . $country . '">+' . $country . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <input type="number" class="required lwp_username" name="lwp_username"
                               placeholder="<?php echo __('9*********', $this->textdomain); ?>">

                        <button class="submit_button auth_phoneNumber" type="submit">
                            <?php echo __('Submit', $this->textdomain); ?>
                        </button>
                        <?php
                        if ($options['idehweb_email_login']) {
                            ?>
                            <button class="submit_button auth_with_email" type="button">
                                <?php echo __('Login with email', $this->textdomain); ?>
                            </button>
                        <?php } ?>
                        <a class="close" href="">(x)</a>
                    </form>
                <?php } ?>
                <?php
                if ($options['idehweb_email_login']) {
                    ?>
                    <form id="lwp_login_email" class="ajax-auth" action="loginemail" method="post">

                        <h1><?php echo __('Login / register', $this->textdomain); ?></h1>
                        <p class="status"></p>
                        <?php wp_nonce_field('ajax-login-nonce', 'security'); ?>
                        <label for="lwp_email"><?php echo __('Your email:', $this->textdomain); ?></label>
                        <input type="email" class="required lwp_email" name="lwp_email"
                               placeholder="<?php echo __('Please enter your email', $this->textdomain); ?>">

                        <button class="submit_button auth_email" type="submit">
                            <?php echo __('Submit', $this->textdomain); ?>
                        </button>
                        <?php
                        if ($options['idehweb_sms_login']) {
                            ?>
                            <button class="submit_button auth_with_phoneNumber" type="button">
                                <?php echo __('Login with phone number', $this->textdomain); ?>
                            </button>
                        <?php } ?>
                        <a class="close" href="">(x)</a>
                    </form>
                <?php } ?>

                <form id="lwp_activate" class="ajax-auth" action="activate" method="post">
                    <h1><?php echo __('Activation', $this->textdomain); ?></h1>
                    <p class="status"></p>
                    <?php wp_nonce_field('ajax-login-nonce', 'security'); ?>

                    <label for="lwp_scode"><?php echo __('Security code', $this->textdomain); ?></label>
                    <input type="text" class="required lwp_scode" name="lwp_scode" placeholder="ـ ـ ـ ـ ـ ـ">

                    <button class="submit_button auth_secCode">
                        <?php echo __('Activate', $this->textdomain); ?>
                    </button>


                    <a class="close" href="">(x)</a>
                </form>

                <?php
                if ($options['idehweb_password_login']) {
                    ?>
                    <form id="lwp_update_password" class="ajax-auth" action="update_password" method="post">

                        <h1><?php echo __('Update password', $this->textdomain); ?></h1>
                        <p class="status"></p>
                        <?php wp_nonce_field('ajax-login-nonce', 'security'); ?>
                        <label for="lwp_email"><?php echo __('Enter new password:', $this->textdomain); ?></label>
                        <input type="password" class="required lwp_up_password" name="lwp_up_password"
                               placeholder="<?php echo __('Please choose a password', $this->textdomain); ?>">

                        <button class="submit_button auth_email" type="submit">
                            <?php echo __('Update', $this->textdomain); ?>
                        </button>
                        <a class="close" href="">(x)</a>
                    </form>
                    <form id="lwp_enter_password" class="ajax-auth" action="enter_password" method="post">

                        <h1><?php echo __('Enter password', $this->textdomain); ?></h1>
                        <p class="status"></p>
                        <?php wp_nonce_field('ajax-login-nonce', 'security'); ?>
                        <label for="lwp_email"><?php echo __('Your password:', $this->textdomain); ?></label>
                        <input type="password" class="required lwp_auth_password" name="lwp_auth_password"
                               placeholder="<?php echo __('Please enter your password', $this->textdomain); ?>">

                        <button class="submit_button login_with_pass" type="submit">
                            <?php echo __('Login', $this->textdomain); ?>
                        </button>
                        <button class="submit_button forgot_password" type="button">
                            <?php echo __('Forgot password', $this->textdomain); ?>
                        </button>
                        <!--                    --><?php
                        //                    if ($options['idehweb_sms_login']) {
                        //                        ?>
                        <!--                        <button class="submit_button auth_with_phoneNumber" type="button">-->
                        <!--                            --><?php //echo __('Login with phone number', $this->textdomain); ?>
                        <!--                        </button>-->
                        <!--                    --><?php //} ?>
                        <a class="close" href="">(x)</a>
                    </form>
                <?php } ?>
            </div>
            <?php
        } else {
            if ($options['idehweb_redirect_url'])
                wp_redirect($options['idehweb_redirect_url']);
        }
        return ob_get_clean();
    }

    function phone_number_exist($phone_number)
    {
        $args = array(
            'meta_query' => array(
                array(
                    'key' => 'phone_number',
                    'value' => $phone_number,
                    'compare' => '='
                )
            )
        );

        $member_arr = get_users($args);
        if ($member_arr && $member_arr[0])
            return $member_arr[0]->ID;
        else
            return 0;

    }

    function lwp_ajax_login()
    {
        $usesrname = sanitize_text_field($_GET['username']);
        if (preg_replace('/^(\-){0,1}[0-9]+(\.[0-9]+){0,1}/', '', $usesrname) == "") {
            $phone_number = ltrim($usesrname, '0');
            $phone_number = substr($phone_number, 0, 15);
//echo $phone_number;
//die();
            if (strlen($phone_number) < 11) {
                echo json_encode([
                    'success' => false,
                    'phone_number' => $phone_number,
                    'message' => __('phone number is wrong!', $this->textdomain)
                ]);
                die();
            }
            $username_exists = $this->phone_number_exist($phone_number);
            $userRegisteredNow = false;
            if (!$username_exists) {
                $info = array();
                $info['user_login'] = $info['user_nicename'] = $info['nickname'] = $info['display_name'] = $this->generate_username();
                $info['user_url'] = sanitize_text_field($_GET['website']);
                $user_register = wp_insert_user($info);
                if (is_wp_error($user_register)) {
                    $error = $user_register->get_error_codes();

                    if (in_array('empty_user_login', $error)) {
                        echo json_encode([
                            'success' => false,
                            'phone_number' => $phone_number,
                            'message' => __($user_register->get_error_message('empty_user_login'))
                        ]);
                        die();
                    } elseif (in_array('existing_user_login', $error)) {
                        echo json_encode([
                            'success' => false,
                            'phone_number' => $phone_number,
                            'message' => __('This username is already registered.', $this->textdomain)
                        ]);
                        die();
                    } elseif (in_array('existing_user_email', $error)) {
                        echo json_encode([
                            'success' => false,
                            'phone_number' => $phone_number,
                            'message' => __('This email address is already registered.', $this->textdomain)
                        ]);
                        die();
                    }
                    die();
                } else {
                    add_user_meta($user_register, 'phone_number', sanitize_user($phone_number));
                    $userRegisteredNow = true;
                    add_user_meta($user_register, 'updatedPass', 0);
                    $username_exists = $user_register;

                }


            }
            $showPass = false;
            if (!$userRegisteredNow) {
                $showPass = true;
            } else {
                $this->lwp_generate_token($username_exists, $phone_number);
            }

            $options = get_option('idehweb_lwp_settings');
            if (!isset($options['idehweb_password_login'])) $options['idehweb_password_login'] = '1';
            $options['idehweb_password_login'] = (bool)(int)$options['idehweb_password_login'];
            if (!$options['idehweb_password_login']) {
                $this->lwp_generate_token($username_exists, $phone_number);

            }
            echo json_encode([
                'success' => true,
                'ID' => $username_exists,
                'phone_number' => $phone_number,
                'showPass' => $showPass,
                'authWithPass' => (bool)(int)$options['idehweb_password_login'],
                'message' => __('Sms sent successfully!', $this->textdomain)
            ]);
            die();

        } else {
            echo json_encode([
                'success' => false,
                'phone_number' => $usesrname,
                'message' => __('phone number is wrong!', $this->textdomain)
            ]);
            die();
        }
    }

    function lwp_forgot_password()
    {

        if ($_GET['email'] != '' && $_GET['ID']) {
            $this->lwp_generate_token($_GET['ID'], $_GET['email'], true);

        }
        if ($_GET['phone_number'] != '' && $_GET['ID'] != '') {
            $this->lwp_generate_token($_GET['ID'], $_GET['phone_number']);

//
        }
        update_user_meta($_GET['ID'], 'updatedPass', '0');

        echo json_encode([
            'success' => true,
            'ID' => $_GET['ID'],
            'message' => __('Update password', $this->textdomain)
        ]);
//
        die();
//        }
    }

    function lwp_enter_password_action()
    {
        if ($_GET['email'] != '') {
            $user = get_user_by('email', $_GET['email']);

        }
        if ($_GET['ID'] != '') {
            $user = get_user_by('ID', $_GET['ID']);

        }
        $creds = array(
            'user_login' => $user->user_login,
            'user_password' => $_GET['password'],
            'remember' => true
        );

        $user = wp_signon($creds, false);

        if (is_wp_error($user)) {
            echo json_encode([
                'success' => false,
                'ID' => $user->ID,
//                'IDENTERED'=>$_GET['ID'],
//                'EMAILENTERED'=>$_GET['email'],
//                'PASSWORD'=>$_GET['password'],

                'err' => $user->get_error_message(),
                'message' => __('Password is incorrect!', $this->textdomain)
            ]);
            die();
        } else {

            echo json_encode([
                'success' => true,
                'ID' => $user->ID,
                'message' => __('Redirecting...', $this->textdomain)
            ]);

            die();
        }
    }

    function lwp_update_password_action()
    {
        $user = wp_get_current_user();
        if ($user) {
            wp_update_user([
                'ID' => $user->ID,
                'user_pass' => $_GET['password']
            ]);
            update_user_meta($user->ID, 'updatedPass', 1);
            wp_clear_auth_cookie();
            wp_set_current_user($user->ID); // Set the current user detail
            wp_set_auth_cookie($user->ID); // Set auth details in cookie
            echo json_encode([
                'success' => true,
                'message' => __('Password set successfully! redirecting...', $this->textdomain)
            ]);

            die();
        } else {

            echo json_encode([
                'success' => false,
                'message' => __('User not found', $this->textdomain)
            ]);

            die();
        }
    }

    function lwp_ajax_login_with_email()

    {
        $email = sanitize_text_field($_GET['email']);
        $userRegisteredNow = false;
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email_exists = email_exists($email);
            if (!$email_exists) {
                $info = array();
                $info['user_email'] = $info['user_nicename'] = $info['nickname'] = $info['display_name'] = sanitize_user($email);
                $info['user_url'] = sanitize_text_field($_GET['website']);
                $info['user_login'] = $this->generate_username();
                $user_register = wp_insert_user($info);
                if (is_wp_error($user_register)) {
                    $error = $user_register->get_error_codes();

                    echo json_encode([
                        'success' => false,
                        'email' => $email,
                        '$email_exists' => $email_exists,
                        '$error' => $error,
                        'message' => __('This email address is already registered.', $this->textdomain)
                    ]);

                    die();
                } else {
                    $userRegisteredNow = true;
                    add_user_meta($user_register, 'updatedPass', 0);
                    $email_exists = $user_register;
                }


            }
//            $user = get_user_by('ID', $email_exists);
//            $password = $user->data->user_pass;
            $showPass = false;
            if (!$userRegisteredNow) {
                $showPass = true;
            } else {
                $wp_mail = $this->lwp_generate_token($email_exists, $email, true);
            }
            $options = get_option('idehweb_lwp_settings');
            if (!isset($options['idehweb_password_login'])) $options['idehweb_password_login'] = '1';
            $options['idehweb_password_login'] = (bool)(int)$options['idehweb_password_login'];
            if (!$options['idehweb_password_login']) {
                $wp_mail = $this->lwp_generate_token($email_exists, $email, true);


            }
            echo json_encode([
                'success' => true,
                'ID' => $email_exists,
//                '$wp_mail' => $wp_mail,
//                '$user' => $user,
                'showPass' => $showPass,
                'authWithPass' => (bool)(int)$options['idehweb_password_login'],

                'email' => $email,
                'message' => __('Email sent successfully!', $this->textdomain)
            ]);
            die();

        } else {
            echo json_encode([
                'success' => false,
                'email' => $email,
                'message' => __('email is wrong!', $this->textdomain)
            ]);
            die();
        }
    }

    function lwp_generate_token($user_id, $contact, $send_email = false)
    {
        $six_digit_random_number = mt_rand(100000, 999999);
        update_user_meta($user_id, 'activation_code', $six_digit_random_number);
        if ($send_email) {
            $wp_mail = wp_mail($contact, 'activation code', __('your activation code: ', $this->textdomain) . $six_digit_random_number);
            return $wp_mail;
        } else {
            $this->send_sms($contact, $six_digit_random_number);
        }
    }

    function generate_username()
    {
        $ulogin = 'user';

        // make user_login unique so WP will not return error
        $check = username_exists($ulogin);
        if (!empty($check)) {
            $suffix = 2;
            while (!empty($check)) {
                $alt_ulogin = $ulogin . '-' . $suffix;
                $check = username_exists($alt_ulogin);
                $suffix++;
            }
            $ulogin = $alt_ulogin;
        }

        return $ulogin;
    }

    function send_sms($phone_number, $code)
    {
        $options = get_option('idehweb_lwp_settings');

        $smsUrl = "https://idehweb.com/wp-json/sendsms/" . $options['idehweb_token'] . "/" . $phone_number . "/" . $code;

        $response = wp_remote_get($smsUrl);
        wp_remote_retrieve_body($response);

    }

    function lwp_ajax_register()
    {
        if (isset($_GET['phone_number'])) {
            $phoneNumber = sanitize_text_field($_GET['phone_number']);
            if (preg_replace('/^(\-){0,1}[0-9]+(\.[0-9]+){0,1}/', '', $phoneNumber) == "") {
                $phone_number = ltrim($phoneNumber, '0');
                $phone_number = substr($phone_number, 0, 15);

                if ($phone_number < 11) {
                    echo json_encode([
                        'success' => false,
                        'phone_number' => $phone_number,
                        'message' => __('phone number is wrong!', $this->textdomain)
                    ]);
                    die();
                }
            }
            $username_exists = $this->phone_number_exist($phone_number);
        } else if (isset($_GET['email'])) {
            $username_exists = email_exists($_GET['email']);
        } else {
            echo json_encode([
                'success' => false,
                'message' => __('phone number is wrong!', $this->textdomain)
            ]);
            die();
        }
        if ($username_exists) {
            $activation_code = get_user_meta($username_exists, 'activation_code', true);
            $secod = sanitize_text_field($_GET['secod']);
            if ($activation_code == $secod) {
                // First get the user details
                $user = get_user_by('ID', $username_exists);

                if (!is_wp_error($user)) {
                    wp_clear_auth_cookie();
                    wp_set_current_user($user->ID); // Set the current user detail
                    wp_set_auth_cookie($user->ID); // Set auth details in cookie
                    update_user_meta($username_exists, 'activation_code', '');
                    $options = get_option('idehweb_lwp_settings');
                    if (!isset($options['idehweb_password_login'])) $options['idehweb_password_login'] = '1';
                    $options['idehweb_password_login'] = (bool)(int)$options['idehweb_password_login'];
                    $updatedPass = (bool)(int)get_user_meta($username_exists, 'updatedPass', true);

                    echo json_encode(array('success' => true, 'loggedin' => true, 'message' => __('loading...', $this->textdomain), 'updatedPass' => $updatedPass, 'authWithPass' => $options['idehweb_password_login']));

                } else {
                    echo json_encode(array('success' => false, 'loggedin' => false, 'message' => __('wrong', $this->textdomain)));

                }

                die();

            } else {
                echo json_encode([
                    'success' => false,
                    'phone_number' => $phone_number,
                    'message' => __('entered code is wrong!', $this->textdomain)
                ]);
                die();

            }

        } else {

            echo json_encode([
                'success' => false,
                'phone_number' => $phone_number,
                'message' => __('user does not exist!', $this->textdomain)
            ]);
            die();

        }

//        echo json_encode([
//            'success' => false,
//            'phone_number' => $phoneNumber,
//            'message' => __('phone number is not correct!', $this->textdomain)
//        ]);
//        die();

    }

    function auth_user_login($user_login, $password, $login)
    {
        $info = array();
        $info['user_login'] = $user_login;
        $info['user_password'] = $password;
        $info['remember'] = true;

        // From false to '' since v 4.9
        $user_signon = wp_signon($info, '');
        if (is_wp_error($user_signon)) {
            echo json_encode(array('loggedin' => false, 'message' => __('Wrong username or password.', $this->textdomain)));
        } else {
            wp_set_current_user($user_signon->ID);
            echo json_encode(array('loggedin' => true, 'message' => __($login . ' successful, redirecting...', $this->textdomain)));
        }

        die();
    }

    function idehweb_lwp_auth_customer()
    {
        $options = get_option('idehweb_lwp_settings');

        if (!isset($options['idehweb_phone_number'])) $options['idehweb_phone_number'] = '';
        $phone_number = sanitize_text_field($_GET['phone_number']);
        $country_code = sanitize_text_field($_GET['country_code']);
        $url = get_site_url();
        $response = wp_remote_get("https://idehweb.com/wp-json/customer/$phone_number/?website=" . $url . "&country_code=".$country_code);
        $body = wp_remote_retrieve_body($response);
        echo $body;
        die();
    }

    function idehweb_lwp_check_credit()
    {
        $options = get_option('idehweb_lwp_settings');

        if (!isset($options['idehweb_token'])) $options['idehweb_token'] = '';
        $idehweb_token = $options['idehweb_token'];
        $url = "https://idehweb.com/wp-json/check-credit/$idehweb_token";
        $response = wp_remote_get($url);

        $body = wp_remote_retrieve_body($response);

        echo $body;


        die();
    }

    function idehweb_lwp_get_shop()
    {
        $url = "https://idehweb.com/wp-json/all-products/0";
        $response = wp_remote_get($url);

        $body = wp_remote_retrieve_body($response);


        echo $body;


        die();
    }

    function idehweb_lwp_activate_customer()
    {
        $phone_number = sanitize_text_field($_GET['phone_number']);
        $secod = sanitize_text_field($_GET['secod']);

        $response = wp_remote_get("https://idehweb.com/wp-json/activate/$phone_number/$secod");
        $body = wp_remote_retrieve_body($response);

        echo $body;


        die();
    }

    function new_modify_user_table($column)
    {
        $column['phone_number'] = __('Phone number', $this->textdomain);
        $column['activation_code'] = __('Activation code', $this->textdomain);

        return $column;
    }


    function new_modify_user_table_row($val, $column_name, $user_id)
    {
        switch ($column_name) {
            case 'phone_number' :
                return get_the_author_meta('phone_number', $user_id);
            case 'activation_code' :
                return get_the_author_meta('activation_code', $user_id);
            default:
        }
        return $val;
    }

}

global $idehweb_lwp;
$idehweb_lwp = new idehwebLwp();

/**
 * Template Tag
 */
function idehweb_lwp()
{

}



