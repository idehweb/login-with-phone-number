<?php

class lwp_trustsignal
{
    function __construct()
    {
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
        add_settings_field('idehweb_trustsignal_token', __('Enter trustsignal API token', 'login-with-phone-number'), [$this, 'setting_trustsignal_token'], 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel lwp-gateways related_to_trustsignal']);
        add_settings_field('idehweb_trustsignal_route', __('Enter route', 'login-with-phone-number'), [$this, 'setting_trustsignal_route'], 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel lwp-gateways related_to_trustsignal']);
        add_settings_field('idehweb_trustsignal_senderid', __('Enter sender id', 'login-with-phone-number'), [$this, 'setting_trustsignal_senderid'], 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel lwp-gateways related_to_trustsignal']);
        add_settings_field('idehweb_trustsignal_templateid', __('Enter template id', 'login-with-phone-number'), [$this, 'setting_trustsignal_templateid'], 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel lwp-gateways related_to_trustsignal']);
        add_settings_field('idehweb_trustsignal_message', __('Enter text (use ${code} for OTP code)', 'login-with-phone-number'), [$this, 'setting_trustsignal_message'], 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel lwp-gateways related_to_trustsignal']);
    }

    public function lwp_replace_strings($string, $phone, $code, $message = '')
    {
        $string = str_replace('${phone_number}', $phone, $string);
        $string = str_replace('${code}', $code, $string);
        $string = str_replace('${message}', $message, $string);
        return $string;
    }

    function lwp_send_sms_trustsignal($phone_number, $code)
    {
        $options = get_option('idehweb_lwp_settings');

        $token = isset($options['idehweb_trustsignal_token']) ? $options['idehweb_trustsignal_token'] : '';
        $route = isset($options['idehweb_trustsignal_route']) ? $options['idehweb_trustsignal_route'] : '';
        $senderid = isset($options['idehweb_trustsignal_senderid']) ? $options['idehweb_trustsignal_senderid'] : '';
        $templateid = isset($options['idehweb_trustsignal_templateid']) ? $options['idehweb_trustsignal_templateid'] : '';
        $message = isset($options['idehweb_trustsignal_message']) ? $options['idehweb_trustsignal_message'] : '';

        $message = $this->lwp_replace_strings($message, '', $code);
        $phone_number = substr($phone_number, 2);

        $url = "https://api.trustsignal.io/v1/sms?api_key=" . $token;

        $d = array(
            "to" => [(int)$phone_number],
            "message" => $message,
            "route" => $route,
            "sender_id" => $senderid,
            "template_id" => $templateid
        );

        $response = wp_remote_post($url, [
            'timeout' => 60,
            'redirection' => 1,
            'body' => json_encode($d),
            'data_format' => 'body',
        ]);

        if (is_wp_error($response)) {
            error_log('trustsignal API Error: ' . $response->get_error_message());
            return false;
        }

        return true;
    }

    function setting_trustsignal_token()
    {
        $options = get_option('idehweb_lwp_settings');
        $value = isset($options['idehweb_trustsignal_token']) ? esc_attr($options['idehweb_trustsignal_token']) : '';
        echo '<input type="text" name="idehweb_lwp_settings[idehweb_trustsignal_token]" class="regular-text" value="' . $value . '" />';
        echo '<p class="description">' . __('Enter trustsignal API token', 'login-with-phone-number') . '</p>';
    }

    function setting_trustsignal_route()
    {
        $options = get_option('idehweb_lwp_settings');
        $value = isset($options['idehweb_trustsignal_route']) ? esc_attr($options['idehweb_trustsignal_route']) : '';
        echo '<input type="text" name="idehweb_lwp_settings[idehweb_trustsignal_route]" class="regular-text" value="' . $value . '" />';
        echo '<p class="description">' . __('Enter route', 'login-with-phone-number') . '</p>';
    }

    function setting_trustsignal_senderid()
    {
        $options = get_option('idehweb_lwp_settings');
        $value = isset($options['idehweb_trustsignal_senderid']) ? esc_attr($options['idehweb_trustsignal_senderid']) : '';
        echo '<input type="text" name="idehweb_lwp_settings[idehweb_trustsignal_senderid]" class="regular-text" value="' . $value . '" />';
        echo '<p class="description">' . __('enter sender id', 'login-with-phone-number') . '</p>';
    }

    function setting_trustsignal_templateid()
    {
        $options = get_option('idehweb_lwp_settings');
        $value = isset($options['idehweb_trustsignal_templateid']) ? esc_attr($options['idehweb_trustsignal_templateid']) : '';
        echo '<input type="text" name="idehweb_lwp_settings[idehweb_trustsignal_templateid]" class="regular-text" value="' . $value . '" />';
        echo '<p class="description">' . __('enter template id', 'login-with-phone-number') . '</p>';
    }

    function setting_trustsignal_message()
    {
        $options = get_option('idehweb_lwp_settings');
        $value = isset($options['idehweb_trustsignal_message']) ? esc_textarea($options['idehweb_trustsignal_message']) : '';
        echo '<textarea name="idehweb_lwp_settings[idehweb_trustsignal_message]" class="regular-text">' . $value . '</textarea>';
        echo '<p class="description">' . __('enter message, use ${code}', 'login-with-phone-number') . '</p>';
    }
}

global $lwp_trustsignal;
$lwp_trustsignal = new lwp_trustsignal();