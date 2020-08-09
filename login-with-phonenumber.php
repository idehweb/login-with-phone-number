<?php
/*
Plugin Name: Login with phone number
Plugin URI: http://idehweb.com/login-with-phonenumber
Description: Login with phone number - sending sms - activate user by phone number - limit pages to login - register and login with ajax - modal
Version: 1.0.0
Author: Hamid alinia - Idehweb
Author URI: http://idehweb.com
Text Domain: idehwebLwp
*/

class idehwebLwp
{
    public $textdomain = 'idehwebLwp';

    function __construct()
    {
        add_action('init', array(&$this, 'idehweb_lwp_textdomain'));
        add_action('admin_init', array(&$this, 'admin_init'));
        add_action('admin_menu', array(&$this, 'admin_menu'), 99);
        add_action('wp_enqueue_scripts', array(&$this, 'enqueue_scripts'));
        add_action('wp_ajax_idehweb_lwp_auth_customer', array(&$this, 'idehweb_lwp_auth_customer'));
        add_action('wp_ajax_idehweb_lwp_activate_customer', array(&$this, 'idehweb_lwp_activate_customer'));
        add_action('wp_ajax_idehweb_lwp_check_credit', array(&$this, 'idehweb_lwp_check_credit'));
        add_action('wp_ajax_idehweb_lwp_get_shop', array(&$this, 'idehweb_lwp_get_shop'));
        add_action('wp_ajax_lwp_ajax_login', array(&$this, 'lwp_ajax_login'));
        add_action('wp_ajax_lwp_ajax_register', array(&$this, 'lwp_ajax_register'));
        add_action('wp_ajax_nopriv_lwp_ajax_login', array(&$this, 'lwp_ajax_login'));
        add_action('wp_ajax_nopriv_lwp_ajax_register', array(&$this, 'lwp_ajax_register'));

        add_shortcode('idehweb_lwp', array(&$this, 'shortcode'));

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
        add_settings_field('idehweb_phone_number', __('Enter your phone number', 'idehwebLwp'), array(&$this, 'setting_idehweb_phone_number'), 'idehweb-lwp', 'idehweb-lwp', ['class' => 'lwp_phone_number_label']);
        add_settings_field('idehweb_token', __('Enter api key', 'idehwebLwp'), array(&$this, 'setting_idehweb_token'), 'idehweb-lwp', 'idehweb-lwp');

        add_settings_field('instructions', __('Shortcode and Template Tag', 'idehwebLwp'), array(&$this, 'setting_instructions'), 'idehweb-lwp', 'idehweb-lwp');
//        }
    }

    function admin_menu()
    {
        $icon_url = plugins_url('/images/favicon.png', __FILE__);
        $page_hook = add_menu_page(__('idehwebLwp Settings', 'idehwebLwp'), 'idehwebLwp', 'update_core', 'idehweb-lwp', array(&$this, 'settings_page'), $icon_url);
        add_submenu_page('idehweb-lwp', __('Settings', 'idehwebLwp'), __('idehwebLwp Settings', 'idehwebLwp'), 'update_core', 'idehweb-lwp', array(&$this, 'settings_page'));

        add_submenu_page('autobuyF', 'idehwebLwp', 'idehwebLwp', 'update_core', 'idehweb-lwp', array(&$this, 'settings_page'));
        add_action('admin_print_styles-' . $page_hook, array(&$this, 'admin_custom_css'));
    }

    function admin_custom_css()
    {
        wp_enqueue_style('idehweb-lwp-admin', plugins_url('/styles/lwp-admin.css', __FILE__));

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
                    <p><strong><?php _e('Settings saved.', 'idehwebLwp'); ?></strong></p>
                </div>
            <?php } ?>
            <form action="options.php" method="post" id="iuytfrdghj">
                <?php settings_fields('idehweb-lwp'); ?>
                <?php do_settings_sections('idehweb-lwp'); ?>
                <div class="chargeAccount">

                </div>
                <p class="submit">
                    <span id="wkdugchgwfchevg3r4r"></span>
                </p>
                <p class="submit">
                    <span id="oihdfvygehv"></span>
                </p>
                <p class="submit">
                    <?php

                    if (!$options['idehweb_phone_number']){
                        ?>
                        <button type="button" class="button-primary auth i35"
                                value="<?php _e('Authenticate', $this->textdomain); ?>"><?php _e('enter your phone number to login/register!', $this->textdomain); ?></button>
                        <button type="button" class="button-primary activate i34" style="display: none"
                                value="<?php _e('Activate', $this->textdomain); ?>"><?php _e('activate account', $this->textdomain); ?></button>

                        <?php

                    }else{ ?>
                    <button type="button" class="button-primary refreshShop"
                            value="<?php _e('Refresh shop', $this->textdomain); ?>"><?php _e('Refresh shop', $this->textdomain); ?></button>
                    <button type="button" class="button-primary loiuyt"
                            value="<?php _e('Check credit', $this->textdomain); ?>"><?php _e('Check credit', $this->textdomain); ?></button>
                    <input type="submit" class="button-primary"
                           value="<?php _e('Save Changes', $this->textdomain); ?>"/></p>
            <?php } ?>
            </form>
            <script>
                <?php

                ?>
                jQuery(function ($) {
                    $(window).load(function () {

                        $('.loiuyt').click();
                        $('.refreshShop').click();

                    });

                    $('body').on('click', '.loiuyt',
                        function () {

                            $.ajax({
                                type: "GET",
                                url: ajaxurl,
                                data: {action: 'idehweb_lwp_check_credit'}
                            }).done(function (msg) {
                                var arr = JSON.parse(msg);
                                console.log(arr);
                                $('.creditor').html('اعتبار شما: ' + arr['credit'] + ' عدد')


                            });

                        });
                    $('body').on('click', '.refreshShop',
                        function () {
                            var lwp_token = $('#lwp_token').val();
                            if (lwp_token) {
                                $.ajax({
                                    type: "GET",
                                    url: ajaxurl,
                                    data: {action: 'idehweb_lwp_get_shop'}
                                }).done(function (msg) {
                                    var arr = JSON.parse(msg);
                                    console.log(arr);
                                    if (arr && arr.products) {
                                        $('.chargeAccount').empty();
                                        for (var j = 0; j < arr.products.length; j++) {
                                            $('.chargeAccount').append('<div class="col-md-3">' +
                                                '<div class="lwp-produ-wrap">' +
                                                '<div class="lwp-shop-title">' +
                                                arr.products[j].title +
                                                '</div>' +
                                                '<div class="lwp-shop-price">' +
                                                arr.products[j].price +
                                                '</div>' +
                                                '<div class="lwp-shop-buy">' +
                                                '<a href="' + arr.products[j].buy + lwp_token + '/' + arr.products[j].ID + '">' + 'خرید' + '</a>' +
                                                '</div>' +
                                                '</div>' +
                                                '</div>'
                                            )

                                        }
                                    }


                                });
                            }

                        });
                    $('body').on('click', '.auth',
                        function () {

                            var lwp_phone_number = $('#lwp_phone_number').val();
                            if (lwp_phone_number) {
                                $('.lwp_phone_number_label th').html('enter code messaged to you!');
                                $('#lwp_phone_number').css('display', 'none');
                                $('#lwp_secod').css('display', 'inherit');
                                $('.i34').css('display', 'inherit');
                                $('.i35').css('display', 'none');
                                // $('#lwp_secod').html('enter code messaged to you!');
                                $.ajax({
                                    type: "GET",
                                    url: ajaxurl,
                                    data: {action: 'idehweb_lwp_auth_customer', phone_number: lwp_phone_number}
                                }).done(function (msg) {
                                    var arr = JSON.parse(msg);
                                    console.log(arr);
                                    // $('form#iuytfrdghj').submit();

                                });

                            }
                        });

                    $('body').on('click', '.activate',
                        function () {

                            var lwp_phone_number = $('#lwp_phone_number').val();
                            var lwp_secod = $('#lwp_secod').val();
                            if (lwp_phone_number && lwp_secod) {
                                $.ajax({
                                    type: "GET",
                                    url: ajaxurl,
                                    data: {
                                        action: 'idehweb_lwp_activate_customer', phone_number: lwp_phone_number,
                                        secod: lwp_secod
                                    }
                                }).done(function (msg) {
                                    var arr = JSON.parse(msg);
                                    console.log(arr);
                                    if (arr['token']) {
                                        $('#lwp_token').val(arr['token']);
                                        $('form#iuytfrdghj').submit();
                                    }

                                });

                            }
                        });
                });
            </script>
        </div>
        <?php
    }

    function section_intro()
    {
        ?>

        <?php

    }


    function setting_idehweb_phone_number()
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_phone_number'])) $options['idehweb_phone_number'] = '';

        echo '<input type="text" name="idehweb_lwp_settings[idehweb_phone_number]" id="lwp_phone_number" class="regular-text" value="' . esc_attr($options['idehweb_phone_number']) . '" />';
        echo '<input type="text" name="idehweb_lwp_settings[idehweb_secod]" id="lwp_secod" class="regular-text" style="display:none" value="' . esc_attr($options['idehweb_phone_number']) . '" />';
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
        echo '<input style="display:' . $display . '" id="lwp_token" type="text" name="idehweb_lwp_settings[idehweb_token]" class="regular-text" value="' . esc_attr($options['idehweb_token']) . '" />
		<p class="description">' . __('enter api key', $this->textdomain) . '</p>';

    }


    function setting_instructions()
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_phone_number'])) $options['idehweb_phone_number'] = '';
        if (!isset($options['idehweb_phone_number'])) $options['idehweb_phone_number'] = '';
        $display = 'inherit';
        if (!$options['idehweb_phone_number']) {
            $display = 'none';
        }
        echo '<div style="display: ' . $display . '"> <p>' . __('you can get support at +989120539945 & To use idehwebLwp in your posts and pages you can use the shortcode:',$this->textdomain) . '</p>
		<p><code>[idehweb_lwp]</code></p>
		<p>' . __('To use idehwebLwp manually in your theme template use the following PHP code:', $this->textdomain) . '</p>
		<p><code>&lt;?php if( function_exists(\'idehweb_lwp\') ) idehweb_lwp(); ?&gt;</code></p>
		<p class="creditor"></p>
		</div>';
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

        wp_localize_script('idehweb-lwp', 'idehweb_lwp', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'redirecturl' => home_url(),
            'loadingmessage' => __('the code has been sent to your phone number...',$this->textdomain)
        ));
    }

    function shortcode($atts)
    {

        extract(shortcode_atts(array(
            'socialmkt' => 'aweber'
        ), $atts));
        ob_start();
        $is_user_logged_in = is_user_logged_in();
        if (!$is_user_logged_in) {
            ?>
            <a id="show_login" style="display: none"><?php echo __('login',$this->textdomain); ?></a>
            <form id="lwp_login" class="ajax-auth" action="login" method="post">

                <h1><?php echo __('Login / register', $this->textdomain); ?></h1>
                <p class="status"></p>
                <?php wp_nonce_field('ajax-login-nonce', 'security'); ?>
                <label for="lwp_username"><?php echo __('Phone number', $this->textdomain); ?></label>
                <input type="text" class="required lwp_username" name="lwp_username"
                       placeholder="<?php echo __('Please enter your phone number', $this->textdomain); ?>">

                <button class="submit_button auth_phoneNumber" type="submit">
                    <?php echo __('Submit', $this->textdomain); ?>
                </button>

                <a class="close" href="">(x)</a>
            </form>

            <form id="lwp_activate" class="ajax-auth" action="activate" method="post">
                <h1><?php echo __('Activate phone number', $this->textdomain); ?></h1>
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
        }
        return ob_get_clean();
    }

    function lwp_ajax_login()

    {
        $usesrname = sanitize_text_field($_GET['username']);
        if (preg_replace('/^(\-){0,1}[0-9]+(\.[0-9]+){0,1}/', '', $usesrname) == "") {
            $phone_number = ltrim($usesrname, '0');
            $phone_number = substr($phone_number, 0, 10);

            if ($phone_number < 10) {
                echo json_encode([
                    'success' => false,
                    'phone_number' => $phone_number,
                    'message' => __('phone number is wrong!', $this->textdomain)
                ]);
                die();
            }
            $username_exists = username_exists($phone_number);

            if (!$username_exists) {
                $info = array();
                $info['user_nicename'] = $info['nickname'] = $info['display_name'] = $info['first_name'] = $info['user_login'] = sanitize_user($phone_number);
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
                    $username_exists = $user_register;
                }


            }
            $this->lwp_generate_token($username_exists, $phone_number);
            echo json_encode([
                'success' => true,
                'ID' => $username_exists,
                'phone_number' => $phone_number,
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

    function lwp_generate_token($user_id, $phone_number)
    {
        $six_digit_random_number = mt_rand(100000, 999999);
        update_user_meta($user_id, 'activation_code', $six_digit_random_number);
        $this->send_sms($phone_number, $six_digit_random_number);
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
        $phoneNumber = sanitize_text_field($_GET['phone_number']);
        if (preg_replace('/^(\-){0,1}[0-9]+(\.[0-9]+){0,1}/', '', $phoneNumber) == "") {
            $phone_number = ltrim($phoneNumber, '0');
            $phone_number = substr($phone_number, 0, 10);

            if ($phone_number < 10) {
                echo json_encode([
                    'success' => false,
                    'phone_number' => $phone_number,
                    'message' => __('phone number is wrong!', $this->textdomain)
                ]);
                die();
            }
            $username_exists = username_exists($phone_number);
            if ($username_exists) {
                $activation_code = get_user_meta($username_exists, 'activation_code', true);
                $secod = sanitize_text_field($_GET['secod']);
                if ($activation_code == $secod) {
                    // First get the user details
                    $user = get_user_by('login', $phone_number);

                    if (!is_wp_error($user)) {
                        wp_clear_auth_cookie();
                        wp_set_current_user($user->ID); // Set the current user detail
                        wp_set_auth_cookie($user->ID); // Set auth details in cookie
                        update_user_meta($username_exists, 'activation_code', '');

                        echo json_encode(array('success' => true, 'loggedin' => true, 'message' => __('loading...', $this->textdomain)));
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
        }
        echo json_encode([
            'success' => false,
            'phone_number' => $phoneNumber,
            'message' => __('phone number is not correct!', $this->textdomain)
        ]);
        die();

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
        $url = get_site_url();
        $response = wp_remote_get("https://idehweb.com/wp-json/customer/$phone_number/?website=" . $url);
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

}

global $idehweb_lwp;
$idehweb_lwp = new idehwebLwp();

/**
 * Template Tag
 */
function idehweb_lwp()
{

}



