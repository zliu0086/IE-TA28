<?php
/**
 * Subscription module
 *
 * @package WPQuiz
 */

namespace WPQuiz\Modules\Subscription;

use WPQuiz\Module;
use WPQuiz\Modules\Subscription\Admin\LeadsPage;
use WPQuiz\Modules\Subscription\MailServices\Manager;

/**
 * Class Subscription
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class Subscription extends Module {

	/**
	 * Module ID.
	 *
	 * @var string
	 */
	protected $id = 'stats';

	/**
	 * Initializes module.
	 */
	public function init() {
		$class_names = $this->get_mail_services_classes();
		foreach ( $class_names as $class_name ) {
			Manager::add( new $class_name() );
		}
	}

	/**
	 * Gets mail_services classes.
	 *
	 * @return array
	 */
	protected function get_mail_services_classes() {
		/**
		 * Allows adding new mail servicess.
		 *
		 * @since 2.0.0
		 *
		 * @param array $classes An array of MailService classes.
		 */
		return apply_filters(
			'wp_quiz_mail_service_classes',
			array(
				'\\WPQuiz\\Modules\\Subscription\\MailServices\\Mailchimp',
				'\\WPQuiz\\Modules\\Subscription\\MailServices\\GetResponse',
				'\\WPQuiz\\Modules\\Subscription\\MailServices\\AWeber',
				'\\WPQuiz\\Modules\\Subscription\\MailServices\\ConvertKit',
			)
		);
	}
}
