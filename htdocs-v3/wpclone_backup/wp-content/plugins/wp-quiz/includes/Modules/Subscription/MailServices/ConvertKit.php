<?php
/**
 * ConvertKit mail service
 *
 * @package WPQuiz
 */

namespace WPQuiz\Modules\Subscription\MailServices;

use CMB2;
use Exception;
use WPQuiz\Helper;

/**
 * Class ConvertKit
 */
class ConvertKit extends MailService {

	/**
	 * ConvertKit constructor.
	 */
	public function __construct() {
		$this->name  = 'convertkit';
		$this->title = __( 'ConvertKit', 'wp-quiz' );
		parent::__construct();
	}

	/**
	 * Subscribes email.
	 *
	 * @param string $email Subscriber email address.
	 * @param string $name  Subscriber name.
	 *
	 * @return array|false
	 *
	 * @throws Exception Exception.
	 */
	public function subscribe( $email, $name ) {}

	/**
	 * Registers subscription options.
	 *
	 * @param CMB2 $cmb CMB2 object.
	 */
	public function register_options( CMB2 $cmb ) {}
}
