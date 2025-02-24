<?php

class LWP_KavenegarSMS
{
    function __construct()
    {
        add_action('idehweb_custom_fields', array(&$this, 'admin_init'));
        add_filter('lwp_add_to_default_gateways', array(&$this, 'lwp_add_to_default_gateways'));
        add_action('lwp_send_sms_kavenegar', array(&$this, 'lwp_send_sms_kavenegar'), 10, 2);
    }

    function lwp_add_to_default_gateways($args = [])
    {
        if (!is_array($args)) {
            $args = [];
        }
        array_push($args, ["value" => "kavenegar", "label" => __("Kavenegar", 'login-with-phone-number')]);
        return $args;
    }

    function admin_init()
    {
        add_settings_field('idehweb_kavenegar_api_key', __('Enter Kavenegar API Key', 'login-with-phone-number'), array(&$this, 'setting_idehweb_api_key'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel lwp-gateways related_to_kavenegar']);
        add_settings_field('idehweb_kavenegar_template', __('Enter Kavenegar Template', 'login-with-phone-number'), array(&$this, 'setting_idehweb_template'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel lwp-gateways related_to_kavenegar']);
//        wp_nonce_field('kavenegar_nonce_action', 'kavenegar_nonce');
    }

    function lwp_send_sms_kavenegar($phone_number, $code)
    {
//        if (!preg_match('/^\+?\d{10,15}$/', $phone_number)) {
//            die('Invalid phone number.');
//        }
//
//        if (!isset($_POST['kavenegar_nonce']) || !wp_verify_nonce($_POST['kavenegar_nonce'], 'kavenegar_nonce_action')) {
//            die('Security check failed.');
//        }

        $options = get_option('idehweb_lwp_settings');
        $api_key = isset($options['idehweb_kavenegar_api_key']) ? sanitize_text_field($options['idehweb_kavenegar_api_key']) : '';
        $template = isset($options['idehweb_kavenegar_template']) ? sanitize_text_field($options['idehweb_kavenegar_template']) : '';

        $url = "https://api.kavenegar.com/v1/{$api_key}/verify/lookup.json";

        $body = [
            'receptor' => $phone_number,
            'token' => $code,
            'template' => $template
        ];

        $response = wp_safe_remote_post($url, [
            'timeout' => 60,
            'redirection' => 1,
            'blocking' => true,
            'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
            'body' => $body
        ]);
    }

    function setting_idehweb_api_key()
    {
        $options = get_option('idehweb_lwp_settings');
        $api_key = isset($options['idehweb_kavenegar_api_key']) ? esc_attr($options['idehweb_kavenegar_api_key']) : '';
        echo '<input type="text" name="idehweb_lwp_settings[idehweb_kavenegar_api_key]" class="regular-text" value="' . $api_key . '" /> ';
        echo '<p class="description">' . __('Enter Kavenegar API Key', 'login-with-phone-number') . '</p>';
    }

    function setting_idehweb_template()
    {
        $options = get_option('idehweb_lwp_settings');
        $template = isset($options['idehweb_kavenegar_template']) ? esc_attr($options['idehweb_kavenegar_template']) : '';
        echo '<input type="text" name="idehweb_lwp_settings[idehweb_kavenegar_template]" class="regular-text" value="' . $template . '" /> ';
        echo '<p class="description">' . __('Enter Kavenegar Template Name', 'login-with-phone-number') . '</p>';
        echo '<p style="color: green" class="description">**For the validation method from the Kavenegar service.</p>';
    }

}

global $lwp_kavenegar_sms;
$lwp_kavenegar_sms = new LWP_KavenegarSMS();
