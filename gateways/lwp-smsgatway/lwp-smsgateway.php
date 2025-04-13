<?php
class lwp_smsir
{
    function __construct()
    {
        add_action('idehweb_custom_fields', array(&$this, 'admin_init'));
        add_filter('lwp_add_to_default_gateways', array(&$this, 'lwp_add_to_default_gateways'));
        add_action('lwp_send_sms_smsir', array(&$this, 'lwp_send_sms_smsir'), 10, 2);
    }

    function lwp_add_to_default_gateways($args = [])
    {
        if (!is_array($args)) {
            $args = [];
        }
        array_push($args, ["value" => "smsir", "label" => __("smsir", 'login-with-phone-number')]);
        return $args;
    }

    function admin_init()
    {
        add_settings_field('idehweb_smsir_token', __('Enter sms.ir token', 'login-with-phone-number'), array(&$this, 'setting_idehweb_token'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel lwp-gateways related_to_smsir']);
        add_settings_field('idehweb_smsir_from', __('Enter sms.ir from', 'login-with-phone-number'), array(&$this, 'setting_idehweb_from'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel lwp-gateways related_to_smsir']);
        add_settings_field('idehweb_smsir_pattern_code', __('Enter pattern code for sms.ir', 'login-with-phone-number'), array(&$this, 'setting_idehweb_pattern_code'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel lwp-gateways related_to_smsir']);
    }

    function lwp_send_sms_smsir($phone_number, $code)
    {
        $options = get_option('idehweb_lwp_settings');
        $token = isset($options['idehweb_smsir_token']) ? sanitize_text_field($options['idehweb_smsir_token']) : '';
        $from = isset($options['idehweb_smsir_from']) ? sanitize_text_field($options['idehweb_smsir_from']) : '';
        $pattern_code = isset($options['idehweb_smsir_pattern_code']) ? sanitize_text_field($options['idehweb_smsir_pattern_code']) : '';

        $value = $this->lwp_replace_strings('کد شما: ${code}', $phone_number, $code);

        $to = $phone_number;
        $body = [
            'pattern_code' => $pattern_code,
            'from' => $from,
            'to' => $to,
            'body' => $value
        ];
        $response = wp_safe_remote_post("https://api.sms.ir/v1/send", [
            'timeout' => 60,
            'redirection' => 1,
            'blocking' => true,
            'headers' => array('Content-Type' => 'application/json', 'apikey' => $token),
            'body' => wp_json_encode($body)
        ]);
    }

    public function lwp_replace_strings($string, $phone, $code)
    {
        $string = str_replace('${phone_number}', sanitize_text_field($phone), $string);
        $string = str_replace('${code}', sanitize_text_field($code), $string);

        return $string;
    }

    function setting_idehweb_token()
    {
        $options = get_option('idehweb_lwp_settings');
        $token = isset($options['idehweb_smsir_token']) ? esc_attr($options['idehweb_smsir_token']) : '';
        echo '<input type="text" name="idehweb_lwp_settings[idehweb_smsir_token]" class="regular-text" value="' . $token . '" /> ';
        echo '<p class="description">' . __('Enter sms.ir token', 'login-with-phone-number') . '</p>';
    }

    function setting_idehweb_from()
    {
        $options = get_option('idehweb_lwp_settings');
        $from = isset($options['idehweb_smsir_from']) ? esc_attr($options['idehweb_smsir_from']) : '';
        echo '<input type="text" name="idehweb_lwp_settings[idehweb_smsir_from]" class="regular-text" value="' . $from . '" /> ';
        echo '<p class="description">' . __('Enter sms.ir from (sender)', 'login-with-phone-number') . '</p>';
    }

    function setting_idehweb_pattern_code()
    {
        $options = get_option('idehweb_lwp_settings');
        $pattern_code = isset($options['idehweb_smsir_pattern_code']) ? esc_attr($options['idehweb_smsir_pattern_code']) : '';
        echo '<input type="text" name="idehweb_lwp_settings[idehweb_smsir_pattern_code]" class="regular-text" value="' . $pattern_code . '" /> ';
        echo '<p class="description">' . __('Enter the pattern code for the SMS service', 'login-with-phone-number') . '</p>';
    }
}

global $lwp_smsir;
$lwp_smsir = new lwp_smsir();
