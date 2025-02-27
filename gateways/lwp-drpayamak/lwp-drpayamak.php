<?php

class lwp_drpayamak
{
    function __construct()
    {
        add_action('idehweb_custom_fields', array(&$this, 'admin_init'));
        add_filter('lwp_add_to_default_gateways', array(&$this, 'lwp_add_to_default_gateways'));
        add_action('lwp_send_sms_drpayamak', array(&$this, 'lwp_send_sms_drpayamak'), 10, 2);
    }

    function lwp_add_to_default_gateways($args = [])
    {
        if (!is_array($args)) {
            $args = [];
        }
        array_push($args, ["value" => "drpayamak", "label" => __("drpayamak", 'login-with-phone-number')]);

        return $args;
    }

    function admin_init()
    {
        add_settings_field('idehweb_drpayamak_username', __('Enter drpayamak username', 'login-with-phone-number'), array(&$this, 'setting_idehweb_username'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel lwp-gateways related_to_drpayamak']);
        add_settings_field('idehweb_drpayamak_password', __('Enter drpayamak password', 'login-with-phone-number'), array(&$this, 'setting_idehweb_password'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel lwp-gateways related_to_drpayamak']);
        add_settings_field('idehweb_drpayamak_from', __('Enter drpayamak from', 'login-with-phone-number'), array(&$this, 'setting_idehweb_from'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel lwp-gateways related_to_drpayamak']);
        add_settings_field('idehweb_drpayamak_message', __('Enter drpayamak message', 'login-with-phone-number'), array(&$this, 'setting_idehweb_message'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel lwp-gateways related_to_drpayamak']);
    }

    function settings_validate($input)
    {
        // Add any validation rules here if necessary
        return $input;
    }

    function lwp_send_sms_drpayamak($phone_number, $code)
    {
        $options = get_option('idehweb_lwp_settings');
        $username = isset($options['idehweb_drpayamak_username']) ? sanitize_text_field($options['idehweb_drpayamak_username']) : '';
        $password = isset($options['idehweb_drpayamak_password']) ? sanitize_text_field($options['idehweb_drpayamak_password']) : '';
        $from = isset($options['idehweb_drpayamak_from']) ? sanitize_text_field($options['idehweb_drpayamak_from']) : '';
        $message = isset($options['idehweb_drpayamak_message']) ? sanitize_textarea_field($options['idehweb_drpayamak_message']) : '';

        $message = $this->lwp_replace_strings($message, $phone_number, $code);

        // Send SMS via drpayamak's new API
        $response = wp_safe_remote_post("http://rest.payamak-panel.com/api/SendSMS/SendSMS", [
            'timeout' => 60,
            'redirection' => 1,
            'blocking' => true,
            'headers' => array('Content-Type' => 'application/json'),
            'body' => wp_json_encode([
                'username' => $username,
                'password' => $password,
                'from' => $from,
                'isflash' => 'false',
                'to' => $phone_number,
                'text' => $message
            ])
        ]);

        $body = wp_remote_retrieve_body($response);
        // Handle response if needed (e.g., log errors)
    }

    public function lwp_replace_strings($string, $phone, $code, $message = '')
    {
        $string = str_replace('${phone_number}', sanitize_text_field($phone), $string);
        $string = str_replace('${code}', sanitize_text_field($code), $string);
        $string = str_replace('${message}', sanitize_text_field($message), $string);

        return $string;
    }

    function setting_idehweb_username()
    {
        $options = get_option('idehweb_lwp_settings');
        $username = isset($options['idehweb_drpayamak_username']) ? esc_attr($options['idehweb_drpayamak_username']) : '';
        echo '<input type="text" name="idehweb_lwp_settings[idehweb_drpayamak_username]" class="regular-text" value="' . $username . '" />';
        echo '<p class="description">' . __('Enter drpayamak username', 'login-with-phone-number') . '</p>';
    }

    function setting_idehweb_password()
    {
        $options = get_option('idehweb_lwp_settings');
        $password = isset($options['idehweb_drpayamak_password']) ? esc_attr($options['idehweb_drpayamak_password']) : '';
        echo '<input type="password" name="idehweb_lwp_settings[idehweb_drpayamak_password]" class="regular-text" value="' . $password . '" />';
        echo '<p class="description">' . __('Enter drpayamak password', 'login-with-phone-number') . '</p>';
    }

    function setting_idehweb_from()
    {
        $options = get_option('idehweb_lwp_settings');
        $from = isset($options['idehweb_drpayamak_from']) ? esc_attr($options['idehweb_drpayamak_from']) : '';
        echo '<input type="text" name="idehweb_lwp_settings[idehweb_drpayamak_from]" class="regular-text" value="' . $from . '" />';
        echo '<p class="description">' . __('Enter drpayamak from (sender)', 'login-with-phone-number') . '</p>';
    }

    function setting_idehweb_message()
    {
        $options = get_option('idehweb_lwp_settings');
        $message = isset($options['idehweb_drpayamak_message']) ? sanitize_textarea_field($options['idehweb_drpayamak_message']) : '';
        echo '<textarea name="idehweb_lwp_settings[idehweb_drpayamak_message]" class="regular-text">' . esc_textarea($message) . '</textarea>';
        echo '<p class="description">' . __('Enter message, use ${code} for the verification code', 'login-with-phone-number') . '</p>';
    }
}

global $lwp_drpayamak;
$lwp_drpayamak = new lwp_drpayamak();

/**
 * Template Tag
 */
function lwp_drpayamak()
{
    // This function can be further expanded if necessary
}
