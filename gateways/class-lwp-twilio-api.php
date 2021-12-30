<?php
/**
 * LWP_Twilio_Api class.
 *
 * The class names need to defined in format: LWP_%s_Api, where %s means Api name, e.g. LWP_Twilio_Api
 * The class methods need to end with api key name
 *
 * @package Login with phone number
 */

/**
 * Class LWP_Twilio_Api class.
 */
class LWP_Twilio_Api {

	/**
	 * Api Key
	 *
	 * @var string
	 */
	public $sid;
	public $api_key;
	public $token;
	public $number;

	/**
	 * LWP_Handle_Messaging constructor.
	 */
	public function __construct() {
        $options = get_option( 'idehweb_lwp_settings' );
        if (!isset($options['idehweb_twilio_account_sid'])) $options['idehweb_twilio_account_sid'] = '';
        if (!isset($options['idehweb_twilio_auth_token'])) $options['idehweb_twilio_auth_token'] = '';
        if (!isset($options['idehweb_twilio_phone_number'])) $options['idehweb_twilio_phone_number'] = '';
        $this->sid=$options['idehweb_twilio_account_sid'];
        $this->token=$options['idehweb_twilio_auth_token'];
        $this->number=$options['idehweb_twilio_phone_number'];
//        $this->api_key   = trim( $plugin_settings['api_key'] );
	}

	/**
	 * Send the OTP via Twilio api
	 *
	 * @param {int} $phone Phone number is without country code.
	 * @param {int} $country_code Country Code is without plus sign.
	 *
	 * @return {array} Returns result
	 */
	public function lwp_send_otp( $phone, $country_code ) {

		$url      = 'https://api.authy.com/protected/json/phones/verification/start';
		$response = wp_remote_post(
			$url,
			array(
				'method'      => 'POST',
				'timeout'     => 30,
				'redirection' => 10,
				'httpversion' => '1.1',
				'blocking'    => true,
				'headers'     => array(),
				'body'        => array(
					'api_key'      => $this->api_key,
					'via'          => 'sms',
					'phone_number' => $phone,
					'country_code' => $country_code,
				),
				'cookies'     => array(),
			)
		);

		$user_message = '';
		$dev_message  = array();
		$res_param    = array();

		if ( is_wp_error( $response ) ) {
			$dev_message = $response->get_error_message();
			$success     = false;
		} else {

			$decoded_response = (array) json_decode( $response['body'] );
			$res_success      = isset( $decoded_response['success'] ) ? $decoded_response['success'] : false;
			$error_code       = isset( $decoded_response['error_code'] ) ? $decoded_response['error_code'] : '';

			if ( $res_success ) {
				$success = true;
			} elseif ( '60033' === $error_code ) {
				$success      = false;
				$user_message = __( 'Phone number is invalid', 'orion-login' );
			} elseif ( '60001' === $error_code ) {
				$success      = false;
				$user_message = __( 'Invalid API key', 'orion-login' );
			} elseif ( '60082' === $error_code ) {
				$success      = false;
				$user_message = __( 'Cannot send SMS to landline phone numbers', 'orion-login' );
			} else {
				$success      = false;
				$user_message = __( 'Api error', 'orion-login' );
			}
		}

		return array(
			'success'     => $success,
			'userMessage' => $user_message,
			'devMessage'  => $dev_message,
			'resParam'    => $res_param,
		);
	}
	public function lwp_send_sms( $phone, $text ) {

		$url      = 'https://api.twilio.com/2010-04-01/Accounts/'.$this->sid.'/Messages.json';
        $auth = base64_encode( $this->sid . ':' . $this->token );
		$response = wp_safe_remote_post(
			$url,
			array(
				'method'      => 'POST',
				'timeout'     => 60,
				'redirection' => 10,
//				'httpversion' => '1.1',
				'blocking'    => true,
//				'headers'     => array(),
                'headers' => [
                    'Authorization' => "Basic $auth"
                ],
				'body'        => array(
					'Body'      => $text,
					'From'          => $this->number,
					'To' => $phone
				),
				'cookies'     => array(),
			)
		);

		$user_message = '';
		$dev_message  = array();
		$res_param    = array();

		if ( is_wp_error( $response ) ) {
			$dev_message = $response->get_error_message();
			$success     = false;
		} else {

			$decoded_response = (array) json_decode( $response['body'] );
			$res_success      = isset( $decoded_response['success'] ) ? $decoded_response['success'] : false;
			$error_code       = isset( $decoded_response['error_code'] ) ? $decoded_response['error_code'] : '';

			if ( $res_success ) {
				$success = true;
			} elseif ( '60033' === $error_code ) {
				$success      = false;
				$user_message = __( 'Phone number is invalid', 'orion-login' );
			} elseif ( '60001' === $error_code ) {
				$success      = false;
				$user_message = __( 'Invalid API key', 'orion-login' );
			} elseif ( '60082' === $error_code ) {
				$success      = false;
				$user_message = __( 'Cannot send SMS to landline phone numbers', 'orion-login' );
			} else {
				$success      = false;
				$user_message = __( 'Api error', 'orion-login' );
			}
		}

		return array(
			'success'     => $success,
			'userMessage' => $user_message,
			'devMessage'  => $dev_message,
			'resParam'    => $res_param,
		);
	}

	/**
	 * Verifies otp
	 *
	 * @param {Int}    $phone Phone.
	 * @param {Int}    $country_code Country Code.
	 * @param {String} $otp OTP.
	 * @param {Array}  $res_param Response Parameter.
	 *
	 * @return array
	 */
	public function lwp_verify_otp( $phone, $country_code, $otp, $res_param ) {

		$url      = 'https://api.authy.com/protected/json/phones/verification/check';
		$response = wp_remote_post(
			$url,
			array(
				'method'      => 'GET',
				'timeout'     => 30,
				'redirection' => 10,
				'httpversion' => '1.1',
				'blocking'    => true,
				'headers'     => array(
					'X-Authy-Api-Key' => $this->api_key,
				),
				'body'        => array(
					'phone_number'      => $phone,
					'country_code'      => $country_code,
					'verification_code' => $otp,
				),
				'cookies'     => array(),
			)
		);

		$invalid_otp   = false;
		$error_message = '';
		$dev_message   = '';

		if ( is_wp_error( $response ) ) {

			$error_message = $response->get_error_message();
			$success       = false;

		} else {
			$decoded_response = (array) json_decode( $response['body'] );

			$res_success = isset( $decoded_response['success'] ) ? $decoded_response['success'] : false;
			$error_code  = isset( $decoded_response['error_code'] ) ? $decoded_response['error_code'] : '';

			if ( $res_success ) {
				$success = true;
			} elseif ( '60022' === $error_code ) {
				$success     = false;
				$invalid_otp = true;
			} elseif ( '60023' === $error_code ) {
				$success       = false;
				$invalid_otp   = false;
				$error_message = __( 'Phone number already verified', 'orion-login' );
			} else {
				$success       = false;
				$error_message = __( 'Api error', 'orion-login' );
				$dev_message   = $decoded_response;
			}
		}

		return array(
			'success'      => $success,
			'invalidOtp'   => $invalid_otp,
			'errorMessage' => $error_message,
			'devMessage'   => $dev_message,
		);

	}

}
