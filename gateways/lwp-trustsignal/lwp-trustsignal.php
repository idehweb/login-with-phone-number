<?php

class lwp_trustsignal
{
    function __construct()
    {
//        add_action('admin_init', [$this, 'register_settings']);
        add_action('idehweb_custom_fields', [$this, 'admin_init']);
        add_filter('lwp_add_to_default_gateways', [$this, 'lwp_add_to_default_gateways']);
        add_action('lwp_send_sms_trustsignal', [$this, 'lwp_send_sms_trustsignal'], 10, 2);
    }
    function lwp_add_to_default_gateways($args = [])
    {
        if (!is_array($args)) {
            $args = [];
        }
        $args[] = ["value" => "trustsignal", "label" => __("trustsignal", 'login-with-phone-number')];
        return $args;
    }

    function admin_init()
    {
        add_settings_field('idehweb_trustsignal_api_key', __('Enter trustsignal api_key', 'login-with-phone-number'), [$this, 'setting_trustsignal_api_key'], 'idehweb-lwp', 'idehweb-lwp',['label_for' => '', 'class' => 'ilwplabel lwp-gateways related_to_trustsignal']);
        add_settings_field('idehweb_trustsignal_sender', __('Enter trustsignal Sender', 'login-with-phone-number'), [$this, 'setting_trustsignal_sender'], 'idehweb-lwp', 'idehweb-lwp',['label_for' => '', 'class' => 'ilwplabel lwp-gateways related_to_trustsignal']);
        add_settings_field('idehweb_trustsignal_country', __('Enter Country Code', 'login-with-phone-number'), [$this, 'setting_trustsignal_country'], 'idehweb-lwp', 'idehweb-lwp',['label_for' => '', 'class' => 'ilwplabel lwp-gateways related_to_trustsignal']);
        add_settings_field('idehweb_trustsignal_route', __('Enter Route', 'login-with-phone-number'), [$this, 'setting_trustsignal_route'], 'idehweb-lwp', 'idehweb-lwp',['label_for' => '', 'class' => 'ilwplabel lwp-gateways related_to_trustsignal']);
        add_settings_field('idehweb_trustsignal_unicode', __('Enable Unicode', 'login-with-phone-number'), [$this, 'setting_trustsignal_unicode'], 'idehweb-lwp', 'idehweb-lwp',['label_for' => '', 'class' => 'ilwplabel lwp-gateways related_to_trustsignal']);
        add_settings_field('idehweb_trustsignal_template_id', __('Enter Template ID', 'login-with-phone-number'), [$this, 'setting_trustsignal_template_id'], 'idehweb-lwp', 'idehweb-lwp',['label_for' => '', 'class' => 'ilwplabel lwp-gateways related_to_trustsignal']);
    }

    function lwp_send_sms_trustsignal($phone_number, $message)
    {
        $options = get_option('idehweb_lwp_settings');
        $api_key = isset($options['idehweb_trustsignal_api_key']) ? sanitize_text_field($options['idehweb_trustsignal_api_key']) : '';
        $sender = isset($options['idehweb_trustsignal_sender']) ? sanitize_text_field($options['idehweb_trustsignal_sender']) : '';
        $country = isset($options['idehweb_trustsignal_country']) ? sanitize_text_field($options['idehweb_trustsignal_country']) : '91'; // Default to India
        $route = isset($options['idehweb_trustsignal_route']) ? sanitize_text_field($options['idehweb_trustsignal_route']) : '4'; // Default to transactional route
        $unicode = isset($options['idehweb_trustsignal_unicode']) ? $options['idehweb_trustsignal_unicode'] : false; // Default to false
        $template_id = isset($options['idehweb_trustsignal_template_id']) ? sanitize_text_field($options['idehweb_trustsignal_template_id']) : ''; // Template ID

        $to = sanitize_text_field($phone_number);

        if (empty($api_key || empty($sender) || empty($to))) {
            error_log('trustsignal API Error: Missing required fields.');
            return;
        }

        // Ensure HTTPS protocol is used
        $url = "https://api.trustsignal.io/v1/sms?api_key";

        $body = [
            'api_key' => $api_key,
            'mobiles' => $to,
            'message' => $message,
            'sender' => $sender,
            'route' => $route,
            'country' => $country,
            'unicode' => $unicode ? 'true' : 'false',
//            'headers' => array('Content-type' =>'apli','api_key' => $api_key)
        ];

        // Add Template ID if provided
        if (!empty($template_id)) {
            $body['template_id'] = $template_id;
        }

        $response = wp_safe_remote_post($url, [
            'timeout' => 30,
            'headers' =>[
                'Content-Type' => 'application/json',
//                'api_key' => $api_key
            ],
            'body' => json_encode($body),
        ]);

        if (is_wp_error($response)) {
            error_log('trustsignal API Error: ' . $response->get_error_message());
            return;
        }

        $response_code = wp_remote_retrieve_response_code($response);
        if ($response_code !== 200) {
            error_log('trustsignal API Response Error: ' . wp_remote_retrieve_body($response));
        }
    }

    function setting_trustsignal_api_key()
    {
        $options = get_option('idehweb_lwp_settings');
        $api_key = isset($options['idehweb_trustsignal_api_key']) ? esc_attr($options['idehweb_trustsignal_api_key']) : '';
        echo '<input type="text" name="idehweb_lwp_settings[idehweb_trustsignal_api_key]" class="regular-text" value="' . $api_key . '" />';
        echo '<p class="description">' . __('Enter the trustsignal api_key.', 'login-with-phone-number') . '</p>';
    }

    function setting_trustsignal_sender()
    {
        $options = get_option('idehweb_lwp_settings');
        $sender = isset($options['idehweb_trustsignal_sender']) ? esc_attr($options['idehweb_trustsignal_sender']) : '';
        echo '<input type="text" name="idehweb_lwp_settings[idehweb_trustsignal_sender]" class="regular-text" value="' . $sender . '" />';
        echo '<p class="description">' . __('Enter the sender ID for trustsignal messages.', 'login-with-phone-number') . '</p>';
    }

    function setting_trustsignal_country()
    {
        $options = get_option('idehweb_lwp_settings');
        $country = isset($options['idehweb_trustsignal_country']) ? esc_attr($options['idehweb_trustsignal_country']) : '91';
        echo '<input type="text" name="idehweb_lwp_settings[idehweb_trustsignal_country]" class="regular-text" value="' . $country . '" />';
        echo '<p class="description">' . __('Enter the country code for the recipient phone number.', 'login-with-phone-number') . '</p>';
    }

    function setting_trustsignal_route()
    {
        $options = get_option('idehweb_lwp_settings');
        $route = isset($options['idehweb_trustsignal_route']) ? esc_attr($options['idehweb_trustsignal_route']) : '4'; // Default route for transactional
        echo '<input type="text" name="idehweb_lwp_settings[idehweb_trustsignal_route]" class="regular-text" value="' . $route . '" />';
        echo '<p class="description">' . __('Enter the message route (e.g., 1 for Promotional, 4 for Transactional).', 'login-with-phone-number') . '</p>';
    }

    function setting_trustsignal_unicode()
    {
        $options = get_option('idehweb_lwp_settings');
        $unicode = isset($options['idehweb_trustsignal_unicode']) ? esc_attr($options['idehweb_trustsignal_unicode']) : false;
        echo '<input type="checkbox" name="idehweb_lwp_settings[idehweb_trustsignal_unicode]" value="1" ' . checked($unicode, true, false) . ' />';
        echo '<p class="description">' . __('Enable Unicode for the message (e.g., for non-Latin characters).', 'login-with-phone-number') . '</p>';
    }

    function setting_trustsignal_template_id()
    {
        $options = get_option('idehweb_lwp_settings');
        $template_id = isset($options['idehweb_trustsignal_template_id']) ? esc_attr($options['idehweb_trustsignal_template_id']) : '';
        echo '<input type="text" name="idehweb_lwp_settings[idehweb_trustsignal_template_id]" class="regular-text" value="' . $template_id . '" />';
        echo '<p class="description">' . __('Enter the trustsignal Template ID.', 'login-with-phone-number') . '</p>';
    }
}

global $lwp_trustsignal;
$lwp_trustsignal = new lwp_trustsignal();
