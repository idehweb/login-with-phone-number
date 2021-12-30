<?php
/**
 * LWP_Smsbharti_Api class.
 *
 * The class names need to defined in format: LWP_%s_Api, where %s means Api name, e.g. LWP_Smsbharti_Api
 * The class methods need to end with api key name
 *
 * @package Login with phone number
 */

/**
 * Class LWP_Smsbharti_Api class.
 */
class LWP_Smsbharti_Api
{

    /**
     * Api Key
     *
     * @var string
     */
    public $api_key;
    public $from;
    public $template_id;
    public $routeid;

    /**
     * LWP_Handle_Messaging constructor.
     */
    public function __construct()
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_smsbharti_api_key'])) $options['idehweb_smsbharti_api_key'] = '';
        if (!isset($options['idehweb_smsbharti_from'])) $options['idehweb_smsbharti_from'] = '';
        if (!isset($options['idehweb_smsbharti_template_id'])) $options['idehweb_smsbharti_template_id'] = '';
        if (!isset($options['idehweb_smsbharti_routeid'])) $options['idehweb_smsbharti_routeid'] = '';
        $this->api_key = $options['idehweb_smsbharti_api_key'];
        $this->from = $options['idehweb_smsbharti_from'];
        $this->template_id = $options['idehweb_smsbharti_template_id'];
        $this->routeid = $options['idehweb_smsbharti_routeid'];
    }

    public function lwp_send_sms($phone, $text)
    {

        $phone = ltrim($phone, '0');
        $phone = substr($phone, 0, 12);
        $phone = substr($phone, 2, 10);
        $url = "http://webmsg.smsbharti.com/app/smsapi/index.php?key=" . $this->api_key . "&campaign=0&routeid=" . $this->routeid . "&type=text&contacts=" . $phone . "&senderid=" . $this->from . "&msg=" . $text . "&template_id=" . $this->template_id;
//        $auth = base64_encode( $this->sid . ':' . $this->token );
//        return ['ytr'=>$url];
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
