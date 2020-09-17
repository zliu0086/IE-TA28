<?php
/**
 * Subscription tab
 *
 * @package WPQuiz
 * @var CMB2 $cmb
 */

use WPQuiz\Modules\Subscription\MailServices\Manager;

$cmb->add_field(
	array(
		'id'               => 'mail_service',
		'type'             => 'select',
		'name'             => __( 'Mail Service', 'wp-quiz' ) . '<span class="pro-text">' . esc_html__( 'Pro feature', 'wp-quiz' ) . '</span>',
		'options'          => Manager::get_list_all(),
		'show_option_none' => __( 'Select Mail service', 'wp-quiz' ),
		'classes'          => 'wp-quiz-pro-feature',
	)
);

foreach ( Manager::get_all() as $service ) {
	$service->register_options( $cmb );
}
