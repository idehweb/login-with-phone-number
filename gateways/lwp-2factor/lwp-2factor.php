<?php
class lwp_2factor
{
    function __construct()
    {
        add_action('idehweb_custom_fields', array(&$this, 'admin_init'));
        add_filter('lwp_add_to_default_gateways', array(&$this, 'lwp_add_to_default_gateways'));
        add_action('lwp_send_sms_2factor', array(&$this, 'lwp_send_sms_2factor'), 10, 2);
    }

    function lwp_add_to_default_gateways($args = [])
    {
        if (!is_array($args)) {
            $args = [];
        }
        array_push($args, ["value" => "2factor", "label" => __("2Factor", 'login-with-phone-number')]);
        return $args;
    }

    function admin_init()
    {
        add_settings_field('idehweb_2factor_api_key', __('Enter 2Factor API Key', 'login-with-phone-number'), array(&$this, 'setting_idehweb_api_key'), 'idehweb-lwp', 'idehweb-lwp', ['class' => 'ilwplabel lwp-gateways related_to_2factor']);
        add_settings_field('idehweb_2factor_template', __('Enter 2Factor OTP Template', 'login-with-phone-number'), array(&$this, 'setting_idehweb_template'), 'idehweb-lwp', 'idehweb-lwp', ['class' => 'ilwplabel lwp-gateways related_to_2factor']);
        add_settings_field('idehweb_otp_value', __('Enter otp_value', 'login-with-phone-number'), array(&$this, 'setting_idehweb_otp_value'), 'idehweb-lwp', 'idehweb-lwp', ['class' => 'ilwplabel lwp-gateways related_to_2factor']);
    }

    function lwp_send_sms_2factor($phone_number, $code)
    {
        $options = get_option('idehweb_lwp_settings');
        $api_key = isset($options['idehweb_2factor_api_key']) ? sanitize_text_field($options['idehweb_2factor_api_key']) : '';
        $otp_template = isset($options['idehweb_2factor_template']) ? sanitize_text_field($options['idehweb_2factor_template']) : '';
        $otp_value = isset($options['idehweb_2factor_otp_value']) ? sanitize_text_field($options['idehweb_2factor_otp_value']) : '';

        $url = "https://2factor.in/API/V1/" . urlencode($api_key) . "/SMS/" . urlencode($phone_number) . "/" . urlencode($otp_value) . "/" . urlencode($otp_template);

        $response = wp_safe_remote_get($url, [
            'timeout'     => 60,
            'redirection' => 1,
            'blocking'    => true,
            'headers' => []
        ]);
    }

    function setting_idehweb_api_key()
    {
        $options = get_option('idehweb_lwp_settings');
        $api_key = isset($options['idehweb_2factor_api_key']) ? esc_attr($options['idehweb_2factor_api_key']) : '';
        echo '<input type="text" name="idehweb_lwp_settings[idehweb_2factor_api_key]" class="regular-text" value="' . $api_key . '" />';
        echo '<p class="description">' . __('Enter your 2Factor API Key', 'login-with-phone-number') . '</p>';
    }

    function setting_idehweb_template()
    {
        $options = get_option('idehweb_lwp_settings');
        $otp_template = isset($options['idehweb_2factor_template']) ? esc_attr($options['idehweb_2factor_template']) : '';
        echo '<input type="text" name="idehweb_lwp_settings[idehweb_2factor_template]" class="regular-text" value="' . $otp_template . '" />';
        echo '<p class="description">' . __('Enter your 2Factor OTP Template Name', 'login-with-phone-number') . '</p>';
    }
    function setting_idehweb_otp_value()
    {
        $options = get_option('idehweb_lwp_settings');
        $otp_value = isset($options['idehweb_2factor_otp_value']) ? esc_attr($options['idehweb_2factor_otp_value']) : '';
        echo '<input type="text" name="idehweb_lwp_settings[idehweb_2factor_otp_value]" class="regular-text" value="' . $otp_value . '" />';
        echo '<p class="description">' . __('Enter your otp_value (character OTP value to be sent to the user)', 'login-with-phone-number') . '</p>';
    }
}

global $lwp_2factor;
$lwp_2factor = new lwp_2factor();
