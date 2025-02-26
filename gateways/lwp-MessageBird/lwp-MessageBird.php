<?php
class lwp_messagebird
{
    function __construct()
    {
        add_action('idehweb_custom_fields', array(&$this, 'admin_init'));
        add_filter('lwp_add_to_default_gateways', array(&$this, 'lwp_add_to_default_gateways'));
        add_action('lwp_send_sms_messagebird', array(&$this, 'lwp_send_sms_messagebird'), 10, 2);
    }

    function lwp_add_to_default_gateways($args = [])
    {
        if (!is_array($args)) {
            $args = [];
        }
        array_push($args, ["value" => "messagebird", "label" => __("messagebird", 'login-with-phone-number')]);
        return $args;
    }

    function admin_init()
    {
        add_settings_field('idehweb_messagebird_api_key', __('Enter messagebird API Key', 'login-with-phone-number'), array(&$this, 'setting_idehweb_api_key'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel lwp-gateways related_to_messagebird']);
        add_settings_field('idehweb_messagebird_from', __('Enter messagebird Sender Name', 'login-with-phone-number'), array(&$this, 'setting_idehweb_from'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel lwp-gateways related_to_messagebird']);
        add_settings_field('idehweb_messagebird_message', __('Enter Message', 'login-with-phone-number'), array(&$this, 'setting_idehweb_message'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel lwp-gateways related_to_messagebird']);

        register_setting('idehweb-lwp', 'idehweb_messagebird_api_key');
        register_setting('idehweb-lwp', 'idehweb_messagebird_from');
        register_setting('idehweb-lwp', 'idehweb_messagebird_message');
    }

    function lwp_send_sms_messagebird($phone_number, $code)
    {
        $options = get_option('idehweb_lwp_settings');

        $apiKey = isset($options['idehweb_messagebird_api_key']) ? sanitize_text_field($options['idehweb_messagebird_api_key']) : '';
        $from = isset($options['idehweb_messagebird_from']) ? sanitize_text_field($options['idehweb_messagebird_from']) : '';
        $message = isset($options['idehweb_messagebird_message']) ? sanitize_text_field($options['idehweb_messagebird_message']) : '';

        $message = str_replace('${code}', sanitize_text_field($code), $message);

        $body = [
            'originator' => $from,
            'recipients' => [$phone_number],
            'body' => $message
        ];

        $response = wp_safe_remote_post("https://rest.messagebird.com/messages", [
            'timeout' => 60,
            'headers' => array(
                'Content-Type' => 'application/json',
                'Authorization' => 'AccessKey ' . $apiKey,
            ),
            'body' => wp_json_encode($body)
        ]);

        $body = wp_remote_retrieve_body($response);
    }

    function setting_idehweb_api_key()
    {
        $options = get_option('idehweb_lwp_settings');
        $apiKey = isset($options['idehweb_messagebird_api_key']) ? esc_attr($options['idehweb_messagebird_api_key']) : '';
        echo '<input type="text" name="idehweb_lwp_settings[idehweb_messagebird_api_key]" class="regular-text" value="' . $apiKey . '" /> ';
    }

    function setting_idehweb_from()
    {
        $options = get_option('idehweb_lwp_settings');
        $from = isset($options['idehweb_messagebird_from']) ? esc_attr($options['idehweb_messagebird_from']) : '';
        echo '<input type="text" name="idehweb_lwp_settings[idehweb_messagebird_from]" class="regular-text" value="' . $from . '" /> ';
    }

    function setting_idehweb_message()
    {
        $options = get_option('idehweb_lwp_settings');
        $message = isset($options['idehweb_messagebird_message']) ? esc_attr($options['idehweb_messagebird_message']) : '';
        echo '<input type="text" name="idehweb_lwp_settings[idehweb_messagebird_message]" class="regular-text" value="' . $message . '" /> ';
    }
}

global $lwp_messagebird;
$lwp_messagebird = new lwp_messagebird();