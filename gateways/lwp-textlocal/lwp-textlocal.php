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
        array_push($args, ["value" => "textlocal", "label" => __("textlocal", 'lwp-textlocal')]);

        return $args;

    }

    function admin_init()
    {

        add_settings_field('idehweb_textlocal_apikey', __('Enter textlocal api key', 'lwp-textlocal'), array(&$this, 'setting_idehweb_apikey'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel lwp-gateways related_to_textlocal']);
//        add_settings_field('idehweb_textlocal_hash', __('Enter textlocal hash', 'lwp-textlocal'), array(&$this, 'setting_idehweb_hash'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel lwp-gateways related_to_textlocal']);
        add_settings_field('idehweb_textlocal_sender', __('Enter textlocal sender', 'lwp-textlocal'), array(&$this, 'setting_idehweb_sender'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel lwp-gateways related_to_textlocal']);
        add_settings_field('idehweb_textlocal_message', __('Enter message', 'lwp-textlocal'), array(&$this, 'setting_idehweb_message'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel lwp-gateways related_to_textlocal']);

    }


    function settings_validate($input)
    {

        return $input;
    }


//
    function lwp_send_sms_textlocal($phone_number, $code)
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_textlocal_apikey'])) $options['idehweb_textlocal_apikey'] = '';
        if (!isset($options['idehweb_textlocal_sender'])) $options['idehweb_textlocal_sender'] = '';
        if (!isset($options['idehweb_textlocal_message'])) $options['idehweb_textlocal_message'] = '';
        $apikey = $options['idehweb_textlocal_apikey'];
        $sender = $options['idehweb_textlocal_sender'];
        $message = $options['idehweb_textlocal_message'];
        $message=$this->lwp_replace_strings($message,'',$code);
        $message=rawurlencode($message);
        $numbers = $phone_number;
        $test = "0";
        $message = urlencode($message);
//        $data = "username=".$apikey."&hash=".$hash."&message=".$message."&sender=".$sender."&numbers=".$numbers."&test=".$test;
        $url="https://api.textlocal.in/send/?apikey=$apikey&sender=$sender&numbers=$numbers&message=$message";

        $response = wp_safe_remote_get($url, [
            'timeout' => 60,
            'redirection' => 1,
            'headers' => array(),
//            'body' => $data
        ]);

        $body = wp_remote_retrieve_body($response);
//        print_r($body);
//        die();
    }
    public function lwp_replace_strings($string, $phone, $code, $message = '')
    {
        $string = str_replace('${phone_number}', $phone, $string);
        $string = str_replace('${code}', $code, $string);
        $string = str_replace('${message}', $message, $string);

//        $string = str_replace('${text}', $text, $string);
        return $string;
    }
    function setting_idehweb_apikey()
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_textlocal_apikey'])) $options['idehweb_textlocal_apikey'] = '';
        echo '<input type="text" name="idehweb_lwp_settings[idehweb_textlocal_apikey]" class="regular-text" value="' . esc_attr($options['idehweb_textlocal_apikey']) . '" />
		<p class="description">' . __('Enter textlocal Api key', 'lwp-textlocal') . '</p>';

    }

    function setting_idehweb_sender()
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_textlocal_sender'])) $options['idehweb_textlocal_sender'] = '';
        echo '<input type="text" name="idehweb_lwp_settings[idehweb_textlocal_sender]" class="regular-text" value="' . esc_attr($options['idehweb_textlocal_sender']) . '" />
		<p class="description">' . __('Enter Enter textlocal sender', 'lwp-textlocal') . '</p>';

    }

//    function setting_idehweb_from()
//    {
//        $options = get_option('idehweb_lwp_settings');
//        if (!isset($options['lwp_textlocal_from'])) $options['lwp_textlocal_from'] = '';
//        echo '<input type="text" name="idehweb_lwp_settings[lwp_textlocal_from]" class="regular-text" value="' . esc_attr($options['lwp_textlocal_from']) . '" />
//		<p class="description">' . __('enter from number', 'lwp-textlocal') . '</p>';
//
//    }
    function setting_idehweb_message()
    {

        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_textlocal_message'])) $options['idehweb_textlocal_message'] = '';
        else $options['idehweb_textlocal_message'] = sanitize_textarea_field($options['idehweb_textlocal_message']);

        echo '<textarea name="idehweb_lwp_settings[idehweb_textlocal_message]" class="regular-text">' . esc_attr($options['idehweb_textlocal_message']) . '</textarea>
		<p class="description">' . __('enter message, use ${code}', 'login-with-phone-number') . '</p>';
    }
}

global $lwp_textlocal;
$lwp_textlocal = new lwp_textlocal();

/**
 * Template Tag
 */
function lwp_textlocal()
{

}



