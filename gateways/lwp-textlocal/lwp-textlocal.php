<?php

class lwp_textlocal
{
    function __construct()
    {
        add_action('idehweb_custom_fields', array(&$this, 'admin_init'));
        add_filter('lwp_add_to_default_gateways', array(&$this, 'lwp_add_to_default_gateways'));
        add_action('lwp_send_sms_textlocal', array(&$this, 'lwp_send_sms_textlocal'), 10, 2);
    }

    function lwp_add_to_default_gateways($args = [])
    {
        if (!is_array($args)) {
            $args = [];
        }
        array_push($args, ["value" => "textlocal", "label" => __("Textlocal", 'login-with-phone-number')]);

        return $args;
    }

    function admin_init()
    {
        add_settings_field('idehweb_textlocal_apikey', __('Enter Textlocal API key', 'login-with-phone-number'), array(&$this, 'setting_idehweb_apikey'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel lwp-gateways related_to_textlocal']);
        add_settings_field('idehweb_textlocal_sender', __('Enter Textlocal sender', 'login-with-phone-number'), array(&$this, 'setting_idehweb_sender'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel lwp-gateways related_to_textlocal']);
        add_settings_field('idehweb_textlocal_message', __('Enter Textlocal message', 'login-with-phone-number'), array(&$this, 'setting_idehweb_message'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel lwp-gateways related_to_textlocal']);
    }

    function settings_validate($input)
    {
        // Add any validation rules here if necessary
        return $input;
    }

    function lwp_send_sms_textlocal($phone_number, $code)
    {
        $options = get_option('idehweb_lwp_settings');
        $apikey = isset($options['idehweb_textlocal_apikey']) ? sanitize_text_field($options['idehweb_textlocal_apikey']) : '';
        $sender = isset($options['idehweb_textlocal_sender']) ? sanitize_text_field($options['idehweb_textlocal_sender']) : '';
        $message = isset($options['idehweb_textlocal_message']) ? sanitize_textarea_field($options['idehweb_textlocal_message']) : '';

        $message = $this->lwp_replace_strings($message, $phone_number, $code);
        $message = urlencode(rawurlencode($message));
        $numbers = sanitize_text_field($phone_number);

        $url = esc_url("https://api.textlocal.in/send/?apikey=$apikey&sender=$sender&numbers=$numbers&message=$message");

        $response = wp_safe_remote_get($url, [
            'timeout' => 60,
            'redirection' => 1,
            'headers' => []
        ]);

        $body = wp_remote_retrieve_body($response);
    }

    public function lwp_replace_strings($string, $phone, $code, $message = '')
    {
        $string = str_replace('${phone_number}', sanitize_text_field($phone), $string);
        $string = str_replace('${code}', sanitize_text_field($code), $string);
        $string = str_replace('${message}', sanitize_text_field($message), $string);

        return $string;
    }

    function setting_idehweb_apikey()
    {
        $options = get_option('idehweb_lwp_settings');
        $apikey = isset($options['idehweb_textlocal_apikey']) ? esc_attr($options['idehweb_textlocal_apikey']) : '';
        echo '<input type="text" name="idehweb_lwp_settings[idehweb_textlocal_apikey]" class="regular-text" value="' . $apikey . '" />';
        echo '<p class="description">' . __('Enter Textlocal API key', 'login-with-phone-number') . '</p>';
    }

    function setting_idehweb_sender()
    {
        $options = get_option('idehweb_lwp_settings');
        $sender = isset($options['idehweb_textlocal_sender']) ? esc_attr($options['idehweb_textlocal_sender']) : '';
        echo '<input type="text" name="idehweb_lwp_settings[idehweb_textlocal_sender]" class="regular-text" value="' . $sender . '" />';
        echo '<p class="description">' . __('Enter Textlocal sender', 'login-with-phone-number') . '</p>';
    }

    function setting_idehweb_message()
    {
        $options = get_option('idehweb_lwp_settings');
        $message = isset($options['idehweb_textlocal_message']) ? sanitize_textarea_field($options['idehweb_textlocal_message']) : '';
        echo '<textarea name="idehweb_lwp_settings[idehweb_textlocal_message]" class="regular-text">' . esc_textarea($message) . '</textarea>';
        echo '<p class="description">' . __('Enter message, use ${code} for the verification code', 'login-with-phone-number') . '</p>';
    }
}

global $lwp_textlocal;
$lwp_textlocal = new lwp_textlocal();

/**
 * Template Tag
 */
function lwp_textlocal()
{
    // This function can be further expanded if necessary
}
