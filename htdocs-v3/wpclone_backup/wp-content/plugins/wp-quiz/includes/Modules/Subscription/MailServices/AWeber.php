<?php
/**
 * AWeber mail service
 *
 * @package WPQuiz
 */

namespace WPQuiz\Modules\Subscription\MailServices;

use AWeberAPI;
use Exception;
use WPQuiz\Helper;

/**
 * Class AWeber
 */
class AWeber extends MailService {

	/**
	 * Class AWeber constructor.
	 */
	public function __construct() {
		$this->name  = 'aweber';
		$this->title = __( 'AWeber', 'wp-quiz' );

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
