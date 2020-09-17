<?php
/**
 * GetResponse mail service
 *
 * @package WPQuiz
 */

namespace WPQuiz\Modules\Subscription\MailServices;

use Exception;
use WPQuiz\Helper;

/**
 * Class GetResponse
 */
class GetResponse extends MailService {

	/**
	 * Class GetResponse constructor.
	 */
	public function __construct() {
		$this->name  = 'getresponse';
		$this->title = __( 'GetResponse', 'wp-quiz' );
		parent::__construct();
	}

	/**
	 * Registers options.
	 *
	 * @param \CMB2 $cmb CMB2 object.
	 */
	public function register_options( \CMB2 $cmb ) {}

	/**
	 * Subscribes email.
	 *
	 * @param string $email Email address.
	 * @param string $name Subscriber name.
	 *
	 * @return mixed|false Return data base on API response or `false` on failure.
	 */
	public function subscribe( $email, $name ) {}
}
