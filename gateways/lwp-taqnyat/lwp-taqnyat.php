<?php
//require_once __DIR__ . '/vendor/autoload.php';
class lwp_taqnyat
{
    function __construct()
    {
        add_action('idehweb_custom_fields', array(&$this, 'admin_init'));
        add_filter('lwp_add_to_default_gateways', array(&$this, 'lwp_add_to_default_gateways'));
        add_action('lwp_send_sms_taqnyat', array(&$this, 'lwp_send_sms_taqnyat'), 10, 2);
    }

    function lwp_add_to_default_gateways($args = [])
    {
        if (!is_array($args)) {
            $args = [];
        }
        array_push($args, ["value" => "taqnyat", "label" => __("Taqnyat", 'login-with-phone-number')]);
        return $args;
    }

    function admin_init()
    {
        add_settings_field('idehweb_taqnyat_token', __('Enter taqnyat token', 'login-with-phone-number'), array(&$this, 'setting_idehweb_token'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel lwp-gateways related_to_taqnyat']);
        add_settings_field('idehweb_taqnyat_from', __('Enter taqnyat from', 'login-with-phone-number'), array(&$this, 'setting_idehweb_from'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel lwp-gateways related_to_taqnyat']);
    }

    function lwp_send_sms_taqnyat($phone_number, $code)
    {
        $options = get_option('idehweb_lwp_settings');
        $token = isset($options['idehweb_taqnyat_token']) ? sanitize_text_field($options['idehweb_taqnyat_token']) : '';
        $from = isset($options['idehweb_taqnyat_from']) ? sanitize_text_field($options['idehweb_taqnyat_from']) : '';
        $message_body = $this->lwp_replace_strings('Your verification code is: ${code}', $phone_number, $code);
        $url = "https://api.taqnyat.sa/v1/messages";

        $data = [
            'recipients'=> [ $phone_number],
            'body'=> $message_body,
            'sender'=> $from,
        ];

        $response = wp_remote_post($url, [

            'body' => json_encode($data),
            'headers'=> [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer' . $token,
            ],
            'timeout'=> 60,
        ]);

//        if (!is_wp_error($response)) {
//            error_log('Message sent successfully: ' . print_r($response, true));
//        }
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
        $token = isset($options['idehweb_taqnyat_token']) ? esc_attr($options['idehweb_taqnyat_token']) : '';
        echo '<input type="text" name="idehweb_lwp_settings[idehweb_taqnyat_token]" class="regular-text" value="' . $token . '" /> ';
        echo '<p class="description">' . __('Enter taqnyat token', 'login-with-phone-number') . '</p>';
    }

    function setting_idehweb_from()
    {
        $options = get_option('idehweb_lwp_settings');
        $from = isset($options['idehweb_taqnyat_from']) ? esc_attr($options['idehweb_taqnyat_from']) : '';
        echo '<input type="text" name="idehweb_lwp_settings[idehweb_taqnyat_from]" class="regular-text" value="' . $from . '" /> ';
        echo '<p class="description">' . __('Enter taqnyat from (sender)', 'login-with-phone-number') . '</p>';
    }
}

global $lwp_taqnyat;
$lwp_taqnyat = new lwp_taqnyat();

?>
