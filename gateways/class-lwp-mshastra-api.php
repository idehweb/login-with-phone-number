<?php
/**
 * LWP_Mshastra_Api class.
 *
 * The class names need to defined in format: LWP_%s_Api, where %s means Api name, e.g. LWP_Mshastra_Api
 * The class methods need to end with api key name
 *
 * @package Login with phone number
 */

/**
 * Class LWP_Mshastra_Api class.
 */
class LWP_Mshastra_Api
{

    /**
     * Api Key
     *
     * @var string
     */
    public $user;
    public $pwd;
    public $senderid;

    /**
     * LWP_Handle_Messaging constructor.
     */
    public function __construct()
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_mshastra_user'])) $options['idehweb_mshastra_user'] = '';
        if (!isset($options['idehweb_mshastra_pwd'])) $options['idehweb_mshastra_pwd'] = '';
        if (!isset($options['idehweb_mshastra_senderid'])) $options['idehweb_mshastra_senderid'] = '';
        $this->user = $options['idehweb_mshastra_user'];
        $this->pwd = $options['idehweb_mshastra_pwd'];
        $this->senderid = $options['idehweb_mshastra_senderid'];
    }

    public function lwp_send_sms($phone, $text)
    {

        $url = "https://mshastra.com/sendurlcomma.aspx?user=".$this->user."&pwd=".$this->pwd."&senderid=".$this->senderid."&mobileno=" . $phone . "&msgtext=" . $text . "&priority=High&CountryCode=ALL";
        $response = wp_safe_remote_get(
            $url,
            array(
//                'method' => 'GET',
                'timeout' => 60,
                'redirection' => 5,
//				'httpversion' => '1.1',
                'blocking' => true,
                'headers' => array(),
//                'headers' => [
//                    'Authorization' => "Basic $auth"
//                ],
                'body' => array(),
                'cookies' => array(),
            )
        );

        $user_message = '';
        $dev_message = array();
        $res_param = array();
        $decoded_response = '';
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
            'response' => $response
        );

    }


}
