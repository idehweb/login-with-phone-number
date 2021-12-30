<?php
/**
 * LWP_Raygansms_Api class.
 *
 * The class names need to defined in format: LWP_%s_Api, where %s means Api name, e.g. LWP_Raygansms_Api
 * The class methods need to end with api key name
 *
 * @package Login with phone number
 */

/**
 * Class LWP_Raygansms_Api class.
 */
class LWP_Raygansms_Api
{

    /**
     * Api Key
     *
     * @var string
     */
    public $username;
    public $password;
    public $ph;


    /**
     * LWP_Handle_Messaging constructor.
     */
    public function __construct()
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_raygansms_username'])) $options['idehweb_raygansms_username'] = '';
        if (!isset($options['idehweb_raygansms_password'])) $options['idehweb_raygansms_password'] = '';
        if (!isset($options['idehweb_raygansms_phonenumber'])) $options['idehweb_raygansms_phonenumber'] = '';
        $this->username = $options['idehweb_raygansms_username'];
        $this->password = $options['idehweb_raygansms_password'];
        $this->ph = $options['idehweb_raygansms_phonenumber'];
//        $this->api_key   = trim( $plugin_settings['api_key'] );
    }

    public function lwp_send_sms($phone, $text)
    {

        $url = 'https://raygansms.com/SendMessageWithUrl.ashx?Username='.$this->username.'&Password='.$this->password.'&PhoneNumber='.$this->ph.'&MessageBody='.$text.'&RecNumber='.$phone.'&Smsclass=1';
        //        $auth = base64_encode( $this->sid . ':' . $this->token );
        $response = wp_safe_remote_get(
            $url,
            array(
//				'method'      => 'GET',
                'timeout' => 60,
                'redirection' => 5,
//				'httpversion' => '1.1',
                'blocking' => true,
                'headers' => array(),
//                'headers' => [
//                    'Authorization' => "Basic $auth"
//                ],
//				'body'        => array(
//					'Body'      => $text,
//					'From'          => $this->number,
//					'To' => $phone
//				),
                'cookies' => array(),
            )
        );

        $user_message = '';
        $dev_message = array();
        $res_param = array();

        if (is_wp_error($response)) {
            $dev_message = $response->get_error_message();
            $success = false;
        } else {

            $decoded_response = (array)json_decode($response['body']);
            $res_success = isset($decoded_response['success']) ? $decoded_response['success'] : false;
            $error_code = isset($decoded_response['error_code']) ? $decoded_response['error_code'] : '';

            if ($res_success) {
                $success = true;
            } elseif ('60033' === $error_code) {
                $success = false;
                $user_message = __('Phone number is invalid', 'orion-login');
            } elseif ('60001' === $error_code) {
                $success = false;
                $user_message = __('Invalid API key', 'orion-login');
            } elseif ('60082' === $error_code) {
                $success = false;
                $user_message = __('Cannot send SMS to landline phone numbers', 'orion-login');
            } else {
                $success = false;
                $user_message = __('Api error', 'orion-login');
            }
        }

        return array(
            'success' => $success,
            'userMessage' => $user_message,
            'devMessage' => $dev_message,
            'resParam' => $res_param,
        );
    }


}
