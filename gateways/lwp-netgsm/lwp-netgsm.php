<?php

class lwp_netgsm
{
    function __construct()
    {
        add_action('idehweb_custom_fields', array(&$this, 'admin_init'));
        add_filter('lwp_add_to_default_gateways', array(&$this, 'lwp_add_to_default_gateways'));
        add_action('lwp_send_sms_netgsm', array(&$this, 'lwp_send_sms_netgsm'), 10, 2);
    }

    function lwp_add_to_default_gateways($args = [])
    {
        if (!is_array($args)) {
            $args = [];
        }
        array_push($args, ["value" => "netgsm", "label" => __("netgsm", 'login-with-phone-number')]);
        return $args;
    }

    function admin_init()
    {
        add_settings_field('idehweb_netgsm_api_key', __('Enter netgsm API Key', 'login-with-phone-number'), array(&$this, 'setting_idehweb_api_key'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel lwp-gateways related_to_netgsm']);
        add_settings_field('idehweb_netgsm_api_secret', __('Enter netgsm API Secret', 'login-with-phone-number'), array(&$this, 'setting_idehweb_api_secret'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel lwp-gateways related_to_netgsm']);
        add_settings_field('idehweb_netgsm_from', __('Enter netgsm Sender Name or Number', 'login-with-phone-number'), array(&$this, 'setting_idehweb_from'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel lwp-gateways related_to_netgsm']);
        add_settings_field('idehweb_netgsm_message', __('Enter Message', 'login-with-phone-number'), array(&$this, 'setting_idehweb_message'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel lwp-gateways related_to_netgsm']);

        register_setting('idehweb-lwp', 'idehweb_netgsm_api_key');
        register_setting('idehweb-lwp', 'idehweb_netgsm_api_secret');
        register_setting('idehweb-lwp', 'idehweb_netgsm_from');
        register_setting('idehweb-lwp', 'idehweb_netgsm_message');
    }

    function lwp_send_sms_netgsm($phone_number, $code)
    {
        $options = get_option('idehweb_lwp_settings');
        $apiKey = isset($options['idehweb_netgsm_api_key']) ? sanitize_text_field($options['idehweb_netgsm_api_key']) : '';
        $apiSecret = isset($options['idehweb_netgsm_api_secret']) ? sanitize_text_field($options['idehweb_netgsm_api_secret']) : '';
        $from = isset($options['idehweb_netgsm_from']) ? sanitize_text_field($options['idehweb_netgsm_from']) : '';
        $message = isset($options['idehweb_netgsm_message']) ? sanitize_text_field($options['idehweb_netgsm_message']) : '';

        $message = str_replace('${code}', sanitize_text_field($code), $message);

        $url = 'https://rest.netgsm.com/sms/json';  // API URL for sending SMS
        $params = [
            'api_key' => $apiKey,
            'api_secret' => $apiSecret,
            'from' => $from,
            'to' => $phone_number,
            'text' => $message
        ];

        // Make POST request
        $response = wp_safe_remote_post($url, [
            'timeout' => 60,
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded'
            ],
            'body' => http_build_query($params)  // Using http_build_query to format the body
        ]);

        $body = wp_remote_retrieve_body($response);
        // Optionally handle response here (e.g., log errors, process success)
    }

    function setting_idehweb_api_key()
    {
        $options = get_option('idehweb_lwp_settings');
        $apiKey = isset($options['idehweb_netgsm_api_key']) ? esc_attr($options['idehweb_netgsm_api_key']) : '';
        echo '<input type="text" name="idehweb_lwp_settings[idehweb_netgsm_api_key]" class="regular-text" value="' . $apiKey . '" /> ';
    }

    function setting_idehweb_api_secret()
    {
        $options = get_option('idehweb_lwp_settings');
        $apiSecret = isset($options['idehweb_netgsm_api_secret']) ? esc_attr($options['idehweb_netgsm_api_secret']) : '';
        echo '<input type="text" name="idehweb_lwp_settings[idehweb_netgsm_api_secret]" class="regular-text" value="' . $apiSecret . '" /> ';
    }

    function setting_idehweb_from()
    {
        $options = get_option('idehweb_lwp_settings');
        $from = isset($options['idehweb_netgsm_from']) ? esc_attr($options['idehweb_netgsm_from']) : '';
        echo '<input type="text" name="idehweb_lwp_settings[idehweb_netgsm_from]" class="regular-text" value="' . $from . '" /> ';
    }

    function setting_idehweb_message()
    {
        $options = get_option('idehweb_lwp_settings');
        $message = isset($options['idehweb_netgsm_message']) ? esc_attr($options['idehweb_netgsm_message']) : '';
        echo '<input type="text" name="idehweb_lwp_settings[idehweb_netgsm_message]" class="regular-text" value="' . $message . '" /> ';
    }
}

global $lwp_netgsm;
$lwp_netgsm = new lwp_netgsm();

