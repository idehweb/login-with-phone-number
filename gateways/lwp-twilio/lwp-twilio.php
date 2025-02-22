<?php

//require __DIR__ . '/Twilio/autoload.php';

class lwp_twilio
{
    function __construct()
    {
        add_action('admin_init', [$this, 'register_settings']);
        add_action('idehweb_custom_fields', [$this, 'admin_init']);
        add_filter('lwp_add_to_default_gateways', [$this, 'lwp_add_to_default_gateways']);
        add_action('lwp_send_sms_twilio', [$this, 'lwp_send_sms_twilio'], 10, 2);
    }

    function register_settings()
    {
        register_setting('idehweb-lwp', 'idehweb_lwp_settings', [
            'sanitize_callback' => [$this, 'sanitize_settings']
        ]);
    }

    function sanitize_settings($settings)
    {
        $sanitized_settings = [];
        $sanitized_settings['idehweb_twilio_sid'] = isset($settings['idehweb_twilio_sid']) ? sanitize_text_field($settings['idehweb_twilio_sid']) : '';
        $sanitized_settings['idehweb_twilio_token'] = isset($settings['idehweb_twilio_token']) ? sanitize_text_field($settings['idehweb_twilio_token']) : '';
        $sanitized_settings['idehweb_twilio_from'] = isset($settings['idehweb_twilio_from']) ? sanitize_text_field($settings['idehweb_twilio_from']) : '';

        return $sanitized_settings;
    }

    function lwp_add_to_default_gateways($args = [])
    {
        if (!is_array($args)) {
            $args = [];
        }
        $args[] = ["value" => "twilio", "label" => __("Twilio", 'login-with-phone-number')];
        return $args;
    }

    function admin_init()
    {
        add_settings_field('idehweb_twilio_sid', __('Enter Twilio SID', 'login-with-phone-number'), [$this, 'setting_twilio_sid'], 'idehweb-lwp', 'idehweb-lwp',['label_for' => '', 'class' => 'ilwplabel lwp-gateways related_to_twilio']);
        add_settings_field('idehweb_twilio_token', __('Enter Twilio Token', 'login-with-phone-number'), [$this, 'setting_twilio_token'], 'idehweb-lwp', 'idehweb-lwp',['label_for' => '', 'class' => 'ilwplabel lwp-gateways related_to_twilio']);
        add_settings_field('idehweb_twilio_from', __('Enter Twilio From', 'login-with-phone-number'), [$this, 'setting_twilio_from'], 'idehweb-lwp', 'idehweb-lwp',['label_for' => '', 'class' => 'ilwplabel lwp-gateways related_to_twilio']);
    }

    function lwp_send_sms_twilio($phone_number, $code)
    {
        $options = get_option('idehweb_lwp_settings');
        $sid = isset($options['idehweb_twilio_sid']) ? sanitize_text_field($options['idehweb_twilio_sid']) : '';
        $token = isset($options['idehweb_twilio_token']) ? sanitize_text_field($options['idehweb_twilio_token']) : '';
        $from = isset($options['idehweb_twilio_from']) ? sanitize_text_field($options['idehweb_twilio_from']) : '';

        $to = sanitize_text_field($phone_number);

        if (empty($sid) || empty($token) || empty($from) || empty($to)) {
            error_log('Twilio API Error: Missing required fields.');
            return;
        }

        // Ensure HTTPS protocol is used
        $url = "https://api.twilio.com/2010-04-01/Accounts/$sid/Messages.json";

        $response = wp_safe_remote_post($url, [
            'timeout' => 60,
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode("$sid:$token"),
                'method' => 'POST',
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'body' => [
                'From' => $from,
                'To' => $to,
                'Body' => $code,
            ],
        ]);

        if (is_wp_error($response)) {
            error_log('Twilio API Error: ' . $response->get_error_message());
            return;
        }

        $response_code = wp_remote_retrieve_response_code($response);
        if ($response_code !== 201) {
            error_log('Twilio API Response Error: ' . wp_remote_retrieve_body($response));
        }
    }

    function setting_twilio_sid()
    {
        $options = get_option('idehweb_lwp_settings');
        $sid = isset($options['idehweb_twilio_sid']) ? esc_attr($options['idehweb_twilio_sid']) : '';
        echo '<input type="text" name="idehweb_lwp_settings[idehweb_twilio_sid]" class="regular-text" value="' . $sid . '" />';
        echo '<p class="description">' . __('Enter the Twilio Account SID.', 'login-with-phone-number') . '</p>';
    }

    function setting_twilio_token()
    {
        $options = get_option('idehweb_lwp_settings');
        $token = isset($options['idehweb_twilio_token']) ? esc_attr($options['idehweb_twilio_token']) : '';
        echo '<input type="password" name="idehweb_lwp_settings[idehweb_twilio_token]" class="regular-text" value="' . $token . '" />';
        echo '<p class="description">' . __('Enter the Twilio Token for authentication.', 'login-with-phone-number') . '</p>';
    }

    function setting_twilio_from()
    {
        $options = get_option('idehweb_lwp_settings');
        $from = isset($options['idehweb_twilio_from']) ? esc_attr($options['idehweb_twilio_from']) : '';
        echo '<input type="text" name="idehweb_lwp_settings[idehweb_twilio_from]" class="regular-text" value="' . $from . '" />';
        echo '<p class="description">' . __('Enter the sender ID for Twilio messages.', 'login-with-phone-number') . '</p>';
    }
}

global $lwp_twilio;
$lwp_twilio = new lwp_twilio();
