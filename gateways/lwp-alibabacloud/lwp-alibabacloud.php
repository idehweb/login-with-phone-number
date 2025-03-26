<?php

class lwp_alibaba_sms
{
    function __construct()
    {
        add_action('idehweb_custom_fields', array(&$this, 'admin_init'));
        add_filter('lwp_add_to_default_gateways', array(&$this, 'lwp_add_to_default_gateways'));
        add_action('lwp_send_sms_alibaba', array(&$this, 'lwp_send_sms_alibaba'), 10, 2);
    }

    function lwp_add_to_default_gateways($args = [])
    {
        if (!is_array($args)) {
            $args = [];
        }
        array_push($args, ["value" => "alibaba_sms", "label" => __("Alibaba SMS", 'login-with-phone-number')]);
        return $args;
    }

    function admin_init()
    {
        add_settings_field('idehweb_alibaba_apikey', __('Enter Alibaba Access Key ID', 'login-with-phone-number'), array(&$this, 'setting_idehweb_apikey'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel lwp-gateways related_to_alibaba_sms']);
        add_settings_field('idehweb_alibaba_secret', __('Enter Alibaba Access Key Secret', 'login-with-phone-number'), array(&$this, 'setting_idehweb_secret'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel lwp-gateways related_to_alibaba_sms']);
    }

    function generate_signature($params, $accessKeySecret)
    {
        ksort($params);
        $queryString = http_build_query($params);
        $stringToSign = "POST&%2F&" . rawurlencode($queryString);
        $signature = base64_encode(hash_hmac('sha1', $stringToSign, $accessKeySecret . "&", true));
        return $signature;
    }

    function lwp_send_sms_alibaba($phone_number, $message)
    {
        $options = get_option('idehweb_lwp_settings');
        $accessKeyId = sanitize_text_field($options['idehweb_alibaba_apikey']);
        $accessKeySecret = sanitize_text_field($options['idehweb_alibaba_secret']);

        if (empty($accessKeyId) || empty($accessKeySecret)) {
            error_log('Alibaba SMS: Missing required configuration values.');
            return;
        }

        $params = [
            'AccessKeyId' => $accessKeyId,
            'Action' => 'SendMessageToGlobe',
            'To' => $phone_number,
            'Message' => $message,
            'Format' => 'JSON',
            'SignatureMethod' => 'HMAC-SHA1',
            'SignatureNonce' => uniqid(),
            'SignatureVersion' => '1.0',
            'Timestamp' => gmdate('Y-m-d\TH:i:s\Z'),
            'Version' => '2018-05-01'
        ];

        $params['Signature'] = $this->generate_signature($params, $accessKeySecret);

        $response = wp_safe_remote_post("https://dysmsapi.ap-southeast-1.aliyuncs.com", [
            'body' => http_build_query($params),
            'timeout' => 60,
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded'
            ],
        ]);

        $body = wp_remote_retrieve_body($response);
        $response_code = wp_remote_retrieve_response_code($response);
        if ($response_code == 200) {
            error_log('Alibaba SMS sent successfully: ' . $body);
        } else {
            error_log('Alibaba SMS failed: ' . $body);
        }
    }

    function setting_idehweb_apikey()
    {
        $options = get_option('idehweb_lwp_settings');
        echo '<input type="text" name="idehweb_lwp_settings[idehweb_alibaba_apikey]" class="regular-text" value="' . esc_attr($options['idehweb_alibaba_apikey']) . '" />';
        echo '<p class="description">' . __('Enter Alibaba Access Key ID', 'login-with-phone-number') . '</p>';
    }

    function setting_idehweb_secret()
    {
        $options = get_option('idehweb_lwp_settings');
        echo '<input type="text" name="idehweb_lwp_settings[idehweb_alibaba_secret]" class="regular-text" value="' . esc_attr($options['idehweb_alibaba_secret']) . '" />';
        echo '<p class="description">' . __('Enter Alibaba Access Key Secret', 'login-with-phone-number') . '</p>';
    }
}

global $lwp_alibaba_sms;
$lwp_alibaba_sms = new lwp_alibaba_sms();

function lwp_alibaba_sms()
{
    // This function can be further expanded if necessary
}
?>
