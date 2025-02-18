<?php

class lwp_mshastra
{
    function __construct()
    {
        add_action('idehweb_custom_fields', array(&$this, 'admin_init'));
        add_filter('lwp_add_to_default_gateways', array(&$this, 'lwp_add_to_default_gateways'));
        add_action('lwp_send_sms_mshastra', array(&$this, 'lwp_send_sms_mshastra'), 10, 2);
    }

    function lwp_add_to_default_gateways($args = [])
    {
        if (!is_array($args)) {
            $args = [];
        }
        array_push($args, ["value" => "mshastra", "label" => __("mshastra", 'login-with-phone-number')]);
        return $args;
    }

    function admin_init()
    {
        add_settings_field('idehweb_mshastra_username', __('Enter mshastra username', 'login-with-phone-number'), array(&$this, 'setting_idehweb_username'), 'idehweb-lwp', 'idehweb-lwp', ['class' => 'ilwplabel lwp-gateways related_to_mshastra']);
        add_settings_field('idehweb_mshastra_password', __('Enter mshastra password', 'login-with-phone-number'), array(&$this, 'setting_idehweb_password'), 'idehweb-lwp', 'idehweb-lwp', ['class' => 'ilwplabel lwp-gateways related_to_mshastra']);
        add_settings_field('idehweb_mshastra_senderid', __('Enter mshastra senderid', 'login-with-phone-number'), array(&$this, 'setting_idehweb_senderid'), 'idehweb-lwp', 'idehweb-lwp', ['class' => 'ilwplabel lwp-gateways related_to_mshastra']);
        add_settings_field('idehweb_mshastra_Countrycode', __('Enter mshastra country code', 'login-with-phone-number'), array(&$this, 'setting_idehweb_Countrycode'), 'idehweb-lwp', 'idehweb-lwp', ['class' => 'ilwplabel lwp-gateways related_to_mshastra']);
        add_settings_field('idehweb_mshastra_smstype', __('Enter mshastra smstype', 'login-with-phone-number'), array(&$this, 'setting_idehweb_smstype'), 'idehweb-lwp', 'idehweb-lwp', ['class' => 'ilwplabel lwp-gateways related_to_mshastra']);
    }

    function lwp_send_sms_mshastra($phone_number, $code)
    {
        $options = get_option('idehweb_lwp_settings');
        $username = isset($options['idehweb_mshastra_username']) ? sanitize_text_field($options['idehweb_mshastra_username']) : '';
        $password = isset($options['idehweb_mshastra_password']) ? sanitize_text_field($options['idehweb_mshastra_password']) : '';
        $senderid = isset($options['idehweb_mshastra_senderid']) ? sanitize_text_field($options['idehweb_mshastra_senderid']) : '';
        $Countrycode = isset($options['idehweb_mshastra_Countrycode']) ? sanitize_text_field($options['idehweb_mshastra_Countrycode']) : '';
//        $smstype = isset($options['idehweb_mshastra_Countrycode']) ? sanitize_text_field($options['idehweb_mshastra_Countrycode']) : '';
        $full_phone_number = $Countrycode . $phone_number;
        $message_text = "Your verification code is: $code";

//        $data = [
//            'user'      => $username,
//            'pwd'       => $password,
//            'senderid'  => $senderid,
//            'mobileno'  => $full_phone_number,
//            'msgtext'   => $message_text
//        ];

        $url = esc_url_raw("http://mshastra.com/sendurlcomma.aspx?user=$username&pwd=$password&senderid=$senderid&mobileno=$full_phone_number&msgtext=" . urlencode($message_text));

        $response = wp_safe_remote_get($url, [
            'timeout'     => 60,
            'redirection' => 5,
            'blocking'    => true,
//            'body'        => $data,
        ]);

    }

    public function lwp_replace_strings($string, $phone, $code)
    {
        $string = str_replace('${phone_number}', $phone, $string);
        $string = str_replace('${code}', $code, $string);
        return $string;
    }

    function setting_idehweb_username()
    {
        $options = get_option('idehweb_lwp_settings');
        $username = isset($options['idehweb_mshastra_username']) ? esc_attr($options['idehweb_mshastra_username']) : '';
        echo '<p><input type="text" name="idehweb_lwp_settings[idehweb_mshastra_username]" class="regular-text" value="' . $username . '" /></p>';
        echo '<p class="description">' . __('Enter mshastra username', 'login-with-phone-number') . '</p>';
    }

    function setting_idehweb_password()
    {
        $options = get_option('idehweb_lwp_settings');
        $password = isset($options['idehweb_mshastra_password']) ? esc_attr($options['idehweb_mshastra_password']) : '';
        echo '<p><input type="password" name="idehweb_lwp_settings[idehweb_mshastra_password]" class="regular-text" value="' . $password . '" /></p>';
        echo '<p class="description">' . __('Enter mshastra password', 'login-with-phone-number') . '</p>';
    }

    function setting_idehweb_senderid()
    {
        $options = get_option('idehweb_lwp_settings');
        $senderid = isset($options['idehweb_mshastra_senderid']) ? esc_attr($options['idehweb_mshastra_senderid']) : '';
        echo '<p><input type="text" name="idehweb_lwp_settings[idehweb_mshastra_senderid]" class="regular-text" value="' . $senderid . '" /></p>';
        echo '<p class="description">' . __('Enter mshastra senderid', 'login-with-phone-number') . '</p>';
    }

    function setting_idehweb_Countrycode()
    {
        $options = get_option('idehweb_lwp_settings');
        $Countrycode = isset($options['idehweb_mshastra_Countrycode']) ? esc_attr($options['idehweb_mshastra_Countrycode']) : '';
        echo '<p><input type="text" name="idehweb_lwp_settings[idehweb_mshastra_Countrycode]" class="regular-text" value="' . $Countrycode . '" /></p>';
        echo '<p class="description">' . __('Enter mshastra country code', 'login-with-phone-number') . '</p>';
    }

    function setting_idehweb_smstype()
    {
        $options = get_option('idehweb_lwp_settings');
        $smstype = isset($options['idehweb_mshastra_smstype']) ? esc_attr($options['idehweb_mshastra_smstype']) : '';
        echo '<p><input type="text" name="idehweb_lwp_settings[idehweb_mshastra_smstype]" class="regular-text" value="' . $smstype . '" /></p>';
        echo '<p class="description">' . __('Enter mshastra smstype', 'login-with-phone-number') . '</p>';
    }
}

global $lwp_mshastra;
$lwp_mshastra = new lwp_mshastra();
