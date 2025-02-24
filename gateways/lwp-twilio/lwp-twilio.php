<?php

class lwp_twilio
{
    function __construct()
    {
        add_filter('lwp_add_to_default_gateways', array(&$this, 'lwp_add_to_default_gateways'));
//        add_action('admin_init', [$this, 'admin_init']);
        add_action('idehweb_custom_fields', array(&$this, 'admin_init'));
        add_action('lwp_send_sms_twilio', array(&$this, 'lwp_send_sms_twilio'), 10, 2);
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
        add_settings_field('idehweb_twilio_sid', __('Enter Twilio SID', 'login-with-phone-number'), array(&$this, 'setting_twilio_sid'), 'idehweb-lwp', 'idehweb-lwp',['label_for' => '', 'class' => 'ilwplabel lwp-gateways related_to_twilio']);
        add_settings_field('idehweb_twilio_token', __('Enter Twilio Token', 'login-with-phone-number'), array(&$this, 'setting_twilio_token'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel lwp-gateways related_to_twilio']);
        add_settings_field('idehweb_twilio_from', __('Enter Twilio From', 'login-with-phone-number'), array(&$this, 'setting_twilio_from'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel lwp-gateways related_to_twilio']);
        wp_nonce_field('Twilio_nonce_action', 'Twilio_nonce');
    }

    function lwp_send_sms_twilio($phone_number, $code)
    {
        if (!preg_match('/^\+?\d{10,15}$/', $phone_number)) {
            die('Invalid phone number.');
        }

        if (!isset($_POST['Twilio_nonce']) || !wp_verify_nonce($_POST['Twilio_nonce'], 'Twilio_nonce_action')) {
            die('Security check failed.');
        }

        $options = get_option('idehweb_lwp_settings');
        $sid = isset($options['idehweb_twilio_sid']) ? sanitize_text_field($options['idehweb_twilio_sid']) : '';
        $token = isset($options['idehweb_twilio_token']) ? sanitize_text_field($options['idehweb_twilio_token']) : '';
        $from = isset($options['idehweb_twilio_form']) ? sanitize_text_field($options['idehweb_twilio_form']) : '';
        $to = $phone_number;

        if (empty($sid) || empty($token) || empty($from) || empty($to)) {
            error_log('Twilio API Error: Missing required fields.');
            return;
        }

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
//
//        if (is_wp_error($response)) {
//            error_log('Twilio API Error: ' . $response->get_error_message());
//            return;
//        }
//
//        $response_code = wp_remote_retrieve_response_code($response);
//        if ($response_code !== 201) {
//            error_log('Twilio API Response Error: ' . wp_remote_retrieve_body($response));
//        }
    }

    function setting_twilio_sid()
    {
        $options = get_option('idehweb_lwp_settings');
        $sid = $options['idehweb_twilio_sid'] ?? '';
        echo '<input type="text" name="idehweb_lwp_settings[idehweb_twilio_sid]" class="regular-text" value="' . $sid . '" />';
        echo '<p class="description">' . __('Enter the Twilio Account SID.', 'login-with-phone-number') . '</p>';
    }

    function setting_twilio_token()
    {
        $options = get_option('idehweb_lwp_settings');
        $token = $options['idehweb_twilio_token'] ?? '';
        echo '<input type="password" name="idehweb_lwp_settings[idehweb_twilio_token]" class="regular-text" value="' . $token . '" />';
        echo '<p class="description">' . __('Enter the Twilio Token for authentication.', 'login-with-phone-number') . '</p>';
    }

    function setting_twilio_from()
    {
        $options = get_option('idehweb_lwp_settings');
        $from = $options['idehweb_twilio_from'] ?? '';
        echo '<input type="text" name="idehweb_lwp_settings[idehweb_twilio_from]" class="regular-text" value="' . $from . '" />';
        echo '<p class="description">' . __('Enter the sender ID for Twilio messages.', 'login-with-phone-number') . '</p>';
    }
}

global $lwp_twilio;
$lwp_twilio = new lwp_twilio();
