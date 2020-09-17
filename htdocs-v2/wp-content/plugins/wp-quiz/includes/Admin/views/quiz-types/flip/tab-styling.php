<?php
/**
 * Flip quiz styling options
 *
 * @package WPQuiz
 * @var CMB2 $cmb
 */

$cmb->add_field(
	array(
		'type'    => 'radio_inline',
		'name'    => __( 'Choose skin', 'wp-quiz' ),
		'id'      => 'wp_quiz_skin',
		'options' => array(
			'traditional' => __( 'Traditional skin', 'wp-quiz' ),
			'flat'        => __( 'Modern flat skin', 'wp-quiz' ),
		),
		'default' => 'traditional',
	)
);

$cmb->add_field(
	array(
		'type'    => 'colorpicker',
		'name'    => __( 'Questions font color', 'wp-quiz' ),
		'id'      => 'wp_quiz_font_color',
		'default' => '#444',
	)
);

$cmb->add_field(
	array(
		'type'    => 'select',
		'name'    => __( 'Quiz size', 'wp-quiz' ),
		'id'      => 'wp_quiz_size',
		'options' => array(
			'full'   => __( 'Full Width (responsive)', 'wp-quiz' ),
			'custom' => __( 'Custom', 'wp-quiz' ),
		),
	)
);

$cmb->add_field(
	array(
		'type' => 'text',
		'name' => __( 'Custom width', 'wp-quiz' ),
		'id'   => 'wp_quiz_custom_width',
		'dep'  => array(
			array( 'wp_quiz_size', 'custom' ),
		),
	)
);

$cmb->add_field(
	array(
		'type' => 'text',
		'name' => __( 'Custom height', 'wp-quiz' ),
		'id'   => 'wp_quiz_custom_height',
		'dep'  => array(
			array( 'wp_quiz_size', 'custom' ),
		),
	)
);

/**
 * Fires after registering flip styling tab.
 *
 * @since 2.0.0
 *
 * @param CMB2 $cmb CMB2 object.
 */
do_action( 'wp_quiz_flip_styling_tab', $cmb );
