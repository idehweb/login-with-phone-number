<?php

class lwp_farazsms
{
    function __construct()
    {
        add_action('idehweb_custom_fields', array(&$this, 'admin_init'));
        add_filter('lwp_add_to_default_gateways', array(&$this, 'lwp_add_to_default_gateways'));
        add_action('lwp_send_sms_farazsms', array(&$this, 'lwp_send_sms_farazsms'), 10, 2);
    }

    function lwp_add_to_default_gateways($args = [])
    {
        if (!is_array($args)) {
            $args = [];
        }
        array_push($args, ["value" => "farazsms", "label" => __("farazsms", 'login-with-phone-number')]);
        return $args;
    }

    function admin_init()
    {
//        add_settings_field('idehweb_farazsms_username', __('Enter farazsms username', 'login-with-phone-number'), array(&$this, 'setting_idehweb_username'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel lwp-gateways related_to_farazsms']);
//        add_settings_field('idehweb_farazsms_password', __('Enter farazsms password', 'login-with-phone-number'), array(&$this, 'setting_idehweb_password'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel lwp-gateways related_to_farazsms']);
        add_settings_field('idehweb_farazsms_token', __('Enter farazsms token', 'login-with-phone-number'), array(&$this, 'setting_idehweb_token'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel lwp-gateways related_to_farazsms']);
        add_settings_field('idehweb_farazsms_from', __('Enter farazsms from', 'login-with-phone-number'), array(&$this, 'setting_idehweb_from'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel lwp-gateways related_to_farazsms']);
        add_settings_field('idehweb_farazsms_pattern_code', __('Enter pattern code for farazsms', 'login-with-phone-number'), array(&$this, 'setting_idehweb_pattern_code'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel lwp-gateways related_to_farazsms']);
        add_settings_field('idehweb_farazsms_variable', __('Enter variable', 'login-with-phone-number'), array(&$this, 'setting_idehweb_variable'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel lwp-gateways related_to_farazsms']);
        add_settings_field('idehweb_farazsms_value', __('Enter value', 'login-with-phone-number'), array(&$this, 'setting_idehweb_value'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel lwp-gateways related_to_farazsms']);
    }

    function lwp_send_sms_farazsms($phone_number, $code)
    {
        $options = get_option('idehweb_lwp_settings');
//        $username = isset($options['idehweb_farazsms_username']) ? sanitize_text_field($options['idehweb_farazsms_username']) : '';
//        $password = isset($options['idehweb_farazsms_password']) ? sanitize_text_field($options['idehweb_farazsms_password']) : '';
        $token = isset($options['idehweb_farazsms_token']) ? sanitize_text_field($options['idehweb_farazsms_token']) : '';
        $from = isset($options['idehweb_farazsms_from']) ? sanitize_text_field($options['idehweb_farazsms_from']) : '';
        $pattern_code = isset($options['idehweb_farazsms_pattern_code']) ? sanitize_text_field($options['idehweb_farazsms_pattern_code']) : '';
        $variable = isset($options['idehweb_farazsms_variable']) ? sanitize_text_field($options['idehweb_farazsms_variable']) : '';
        $value = isset($options['idehweb_farazsms_value']) ? sanitize_text_field($options['idehweb_farazsms_value']) : '';

        $value = $this->lwp_replace_strings($value, $phone_number, $code);

        $to = $phone_number;
        $var = [];
        $var[$variable] = $value;
        $body = [
            'code' => $pattern_code,
            'sender' => $from,
            'recipient' => $to,
            'variable' => $var
        ];
        $response = wp_safe_remote_post("https://api2.ippanel.com/api/v1/sms/pattern/normal/send", [
            'timeout' => 60,
            'redirection' => 1,
            'blocking' => true,
            'headers' => array('Content-Type' => 'application/json', 'apikey' => $token),
            'body' => wp_json_encode($body)
        ]);

        $body = wp_remote_retrieve_body($response);
//        print_r($body);
//        die();
    }

    public function lwp_replace_strings($string, $phone, $code)
    {
        $string = str_replace('${phone_number}', sanitize_text_field($phone), $string);
        $string = str_replace('${code}', sanitize_text_field($code), $string);

        return $string;
    }

//    function setting_idehweb_username()
//    {
//        $options = get_option('idehweb_lwp_settings');
//        $username = isset($options['idehweb_farazsms_username']) ? esc_attr($options['idehweb_farazsms_username']) : '';
//        echo '<input type="text" name="idehweb_lwp_settings[idehweb_farazsms_username]" class="regular-text" value="' . $username . '" /> ';
//        echo '<p class="description">' . __('Enter farazsms username', 'login-with-phone-number') . '</p>';
//    }
//
//    function setting_idehweb_password()
//    {
//        $options = get_option('idehweb_lwp_settings');
//        $password = isset($options['idehweb_farazsms_password']) ? esc_attr($options['idehweb_farazsms_password']) : '';
//        echo '<input type="password" name="idehweb_lwp_settings[idehweb_farazsms_password]" class="regular-text" value="' . $password . '" /> ';
//        echo '<p class="description">' . __('Enter farazsms password', 'login-with-phone-number') . '</p>';
//    }

    function setting_idehweb_token()
    {
        $options = get_option('idehweb_lwp_settings');
        $token = isset($options['idehweb_farazsms_token']) ? esc_attr($options['idehweb_farazsms_token']) : '';
        echo '<input type="token" name="idehweb_lwp_settings[idehweb_farazsms_token]" class="regular-text" value="' . $token . '" /> ';
        echo '<p class="description">' . __('Enter farazsms token', 'login-with-phone-number') . '</p>';
//        echo '<a href="https://idehweb.ir/%d9%88%d8%b1%d9%88%d8%af-%d8%a8%d8%a7-%d8%af%d8%b1%da%af%d8%a7%d9%87-%d9%81%d8%b1%d8%a7%d8%b2-%d8%a7%d8%b3-%d8%a7%d9%85-%d8%a7%d8%b3-%d8%af%d8%b1-%d9%be%d9%84%d8%a7%da%af%db%8c%d9%86-%d9%88%d8%b1/" target="_blank" type="link">farazsms documentation</a>';

    }

    function setting_idehweb_from()
    {
        $options = get_option('idehweb_lwp_settings');
        $from = isset($options['idehweb_farazsms_from']) ? esc_attr($options['idehweb_farazsms_from']) : '';
        echo '<input type="text" name="idehweb_lwp_settings[idehweb_farazsms_from]" class="regular-text" value="' . $from . '" /> ';
        echo '<p class="description">' . __('Enter farazsms from (sender)', 'login-with-phone-number') . '</p>';
    }

    function setting_idehweb_pattern_code()
    {
        $options = get_option('idehweb_lwp_settings');
        $pattern_code = isset($options['idehweb_farazsms_pattern_code']) ? esc_attr($options['idehweb_farazsms_pattern_code']) : '';
        echo '<input type="text" name="idehweb_lwp_settings[idehweb_farazsms_pattern_code]" class="regular-text" value="' . $pattern_code . '" /> ';
        echo '<p class="description">' . __('Enter the pattern code for the SMS service', 'login-with-phone-number') . '</p>';
    }

    function setting_idehweb_variable()
    {
        $options = get_option('idehweb_lwp_settings');
        $variable = isset($options['idehweb_farazsms_variable']) ? esc_attr($options['idehweb_farazsms_variable']) : '';
        echo '<input type="text" name="idehweb_lwp_settings[idehweb_farazsms_variable]" class="regular-text" value="' . $variable . '" /> ';
        echo '<p class="description">' . __('Enter the variable for the SMS service', 'login-with-phone-number') . '</p>';
    }

    function setting_idehweb_value()
    {
        $options = get_option('idehweb_lwp_settings');
        $value = isset($options['idehweb_farazsms_value']) ? esc_attr($options['idehweb_farazsms_value']) : '';
        echo '<input type="text" name="idehweb_lwp_settings[idehweb_farazsms_value]" class="regular-text" value="' . $value . '" /> ';
        echo '<p class="description">' . __('Enter the value for the SMS service', 'login-with-phone-number') . '</p>';
        echo '<a href="' . esc_url('https://idehweb.ir/%d9%88%d8%b1%d9%88%d8%af-%d8%a8%d8%a7-%d8%af%d8%b1%da%af%d8%a7%d9%87-%d9%81%d8%b1%d8%a7%d8%b2-%d8%a7%d8%b3-%d8%a7%d9%85-%d8%a7%d8%b3-%d8%af%d8%b1-%d9%be%d9%84%d8%a7%da%af%db%8c%d9%86-%d9%88%d8%b1/') . '" target="_blank">' . __('farazsms documentation', 'text-domain') . '</a>';

    }

}

global $lwp_farazsms;
$lwp_farazsms = new lwp_farazsms();
