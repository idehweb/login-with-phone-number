<?php
/**
 * LWP_Taqnyat_Api class.
 *
 * The class names need to defined in format: LWP_%s_Api, where %s means Api name, e.g. LWP_Taqnyat_Api
 * The class methods need to end with api key name
 *
 * @package Login with phone number
 */

/**
 * Class LWP_Taqnyat_Api class.
 */
class LWP_Taqnyat_Api
{

    /**
     * Api Key
     *
     * @var string
     */
    public $sendernumber;
    public $apiKey;

    /**
     * LWP_Handle_Messaging constructor.
     */
    public function __construct()
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_taqnyat_sendernumber'])) $options['idehweb_taqnyat_sendernumber'] = '';
        if (!isset($options['idehweb_taqnyat_api_key'])) $options['idehweb_taqnyat_api_key'] = '';
        $this->sendernumber = $options['idehweb_taqnyat_sendernumber'];
        $this->apiKey = $options['idehweb_taqnyat_api_key'];
    }

    public function lwp_send_sms($phone, $text)
    {

        $url = 'https://api.taqnyat.sa/v1/messages';
//        $auth = base64_encode( $this->sid . ':' . $this->token );
        $response = wp_safe_remote_post(
            $url,
            array(
                'method' => 'POST',
                'timeout' => 60,
                'redirection' => 5,
//				'httpversion' => '1.1',
                'blocking' => true,
//                'headers' => array(),
                'headers' => [
                    'Authorization' => "Bearer $this->apiKey"
                ],
                'body' => array(
                    'sender' => $this->sendernumber,
                    'body' => $text,
                    'recipients' => [$phone]
                ),
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
            '$response'=>$decoded_response
        );
    }


}
