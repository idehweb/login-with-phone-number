<?php
class lwp_msg91
{
    function __construct()
    {
        add_action('idehweb_custom_fields', [$this, 'admin_init']);
        add_filter('lwp_add_to_default_gateways', [$this, 'lwp_add_to_default_gateways']);
        add_action('lwp_send_sms_msg91', [$this, 'lwp_send_sms_msg91'], 10, 2);
    }

    function lwp_add_to_default_gateways($args = [])
    {
        if (!is_array($args)) {
            $args = [];
        }
        $args[] = ["value" => "msg91", "label" => __("MSG91", 'login-with-phone-number')];
        return $args;
    }

    function admin_init()
    {
        add_settings_field('idehweb_msg91_authkey', __('Enter MSG91 Authkey', 'login-with-phone-number'), array(&$this, 'setting_msg91_authkey'), 'idehweb-lwp', 'idehweb-lwp',['label_for' => '', 'class' => 'ilwplabel lwp-gateways related_to_msg91']);
        add_settings_field('idehweb_msg91_template_id', __('Enter MSG91 Template id', 'login-with-phone-number'), array(&$this, 'setting_msg91_template_id'), 'idehweb-lwp', 'idehweb-lwp',['label_for' => '', 'class' => 'ilwplabel lwp-gateways related_to_msg91']);
    }

    function lwp_send_sms_msg91($phone_number, $message)
    {
        $options = get_option('idehweb_lwp_settings');
        $authkey = isset($options['idehweb_msg91_authkey']) ? sanitize_text_field($options['idehweb_msg91_authkey']) : '';
        $template_id = isset($options['idehweb_msg91_template_id']) ? sanitize_text_field($options['idehweb_msg91_template_id']) : '';
//        $country = isset($options['idehweb_msg91_country']) ? sanitize_text_field($options['idehweb_msg91_country']) : '91'; // Default to India
//        $route = isset($options['idehweb_msg91_route']) ? sanitize_text_field($options['idehweb_msg91_route']) : '4'; // Default to transactional route
//        $unicode = isset($options['idehweb_msg91_unicode']) ? $options['idehweb_msg91_unicode'] : false; // Default to false

        $to = $phone_number;

        if (empty($authkey) || empty($template_id) || empty($to)) {
            error_log('MSG91 API Error: Missing required fields.');
            return;
        }

        $url = 'https://control.msg91.com/api/v5/flow';

        $body = [
            'template_id' => $template_id,
            'short_url' => '0',
            'short_url_expiry' => '',
            'realTimeResponse' => '1',
            'recipients' => [
                [
                    'mobiles' => $to,
                    'var' => $message
                ]
            ]
        ];

        $args = [
            'headers' => [
                'accept'        => 'application/json',
                'authkey'       => $authkey,
                'content-type'  => 'application/json',
            ],
            'body'        => wp_json_encode($body),
            'data_format' => 'body',
            'timeout'     => 30,
        ];

        $response = wp_safe_remote_post($url, $args);


        if (is_wp_error($response)) {
            error_log('MSG91 API Error: ' . $response->get_error_message());
            return;
        }

        $response_code = wp_remote_retrieve_response_code($response);
        if ($response_code !== 200) {
            error_log('MSG91 API Response Error: ' . wp_remote_retrieve_body($response));
        }

    }

    function setting_msg91_authkey()
    {
        $options = get_option('idehweb_lwp_settings');
        $authkey = isset($options['idehweb_msg91_authkey']) ? esc_attr($options['idehweb_msg91_authkey']) : '';
        echo '<input type="text" name="idehweb_lwp_settings[idehweb_msg91_authkey]" class="regular-text" value="' . $authkey . '" />';
        echo '<p class="description">' . __('Enter the MSG91 Authkey.', 'login-with-phone-number') . '</p>';
    }

    function setting_msg91_template_id()
    {
        $options = get_option('idehweb_lwp_settings');
        $template_id = isset($options['idehweb_msg91_template_id']) ? esc_attr($options['idehweb_msg91_template_id']) : '';
        echo '<input type="text" name="idehweb_lwp_settings[idehweb_msg91_template_id]" class="regular-text" value="' . $template_id . '" />';
        echo '<p class="description">' . __('Enter the Template ID for MSG91 messages.', 'login-with-phone-number') . '</p>';
    }


}

global $lwp_msg91;
$lwp_msg91 = new lwp_msg91();