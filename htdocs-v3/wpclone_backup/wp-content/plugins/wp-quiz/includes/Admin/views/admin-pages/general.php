<?php
/**
 * General settings tab
 *
 * @package WPQuiz
 * @var CMB2 $cmb
 */

use WPQuiz\Helper;

$default_options = Helper::get_default_options();

$currencies       = Helper::get_currencies();
$currency_options = array();

foreach ( $currencies as $key => $currency ) {
	$currency_options[ $key ] = $currency['title'];
}

$cmb->add_field(
	array(
		'id'      => 'quiz_slug',
		'name'    => __( 'Quiz archive slug', 'wp-quiz' ),
		'desc'    => __( 'If you are planning to use this quiz CPT in the theme, then you can change the slug according to your needs.', 'wp-quiz' ),
		'type'    => 'text',
		// Using function because `wp_quiz` is a callable.
		'default_cb' => function() use ( $default_options ) {
			return $default_options['quiz_slug'];
		},
	)
);

$cmb->add_field(
	array(
		'id'      => 'record_guest_method',
		'name'    => __( 'Record guest user by', 'wp-quiz' ) . '<span class="pro-text">' . esc_html__( 'Pro feature', 'wp-quiz' ) . '</span>',
		'desc'    => __( 'Use this option to group the played quizzes for guest users.', 'wp-quiz' ),
		'type'    => 'radio_inline',
		'options' => array(
			'ip'     => __( 'IP address', 'wp-quiz' ),
			'cookie' => __( 'Browser cookie', 'wp-quiz' ),
		),
		'default' => 'off',
		'classes' => 'wp-quiz-pro-feature',
	)
);

$cmb->add_field(
	array(
		'id'      => 'allow_user_multi_votes',
		'name'    => __( 'Allow multiple votes per user', 'wp-quiz' ) . '<span class="pro-text">' . esc_html__( 'Pro feature', 'wp-quiz' ) . '</span>',
		'desc'    => __( 'This option is for List Quiz only. If voting option is used.', 'wp-quiz' ),
		'type'    => 'switch',
		'default' => 'off',
		'classes' => 'wp-quiz-pro-feature',
	)
);

$cmb->add_field(
	array(
		'id'      => 'fb_app_id',
		'type'    => 'text',
		'name'    => __( 'Facebook App ID', 'wp-quiz' ),
		'desc'    => sprintf(
			// translators: Facebook App ID tutorial link.
			esc_html__( 'This is required option if you are using the Facebook Share button. %s', 'wp-quiz' ),
			'<a href="https://youtu.be/OE-iLSHLFZ8?t=415" target="_blank">' . esc_html__( 'Tutorial', 'wp-quiz' ) . '</a>'
		),
		'default' => $default_options['fb_app_id'],
	)
);

$cmb->add_field(
	array(
		'id'         => 'stripe_api_key',
		'type'       => 'text',
		'name'       => __( 'Stripe API key', 'wp-quiz' ) . '<span class="pro-text">' . esc_html__( 'Pro feature', 'wp-quiz' ) . '</span>',
		'desc'       => sprintf(
			// translators: API key link.
			esc_html__( 'Stripe Payments is used to enable the Pay to Play feature, %s to get Stripe API key', 'wp-quiz' ),
			'<a href="https://dashboard.stripe.com/account/apikeys" target="_blank">' . esc_html__( 'Click here', 'wp-quiz' ) . '</a>'
		),
		'attributes' => array(
			'type' => 'password',
		),
		'classes'    => 'wp-quiz-pro-feature',
	)
);

$cmb->add_field(
	array(
		'id'      => 'currency',
		'type'    => 'select',
		'name'    => __( 'Currency', 'wp-quiz' ) . '<span class="pro-text">' . esc_html__( 'Pro feature', 'wp-quiz' ) . '</span>',
		'desc'    => __( 'Set up the currency for the Stripe Payment.', 'wp-quiz' ),
		'options' => $currency_options,
		'default' => '',
		'classes' => 'wp-quiz-pro-feature',
	)
);

$desc = ! Helper::get_option( 'mts_username' ) ?
	sprintf(
		'<a href="https://mythemeshop.com/#signup" target="_blank">%1$s</a>%2$s',
		esc_html__( 'Signup', 'wp-quiz' ),
		esc_html__( ' and get your referral ID (username) if you don\'t have it already!', 'wp-quiz' )
	) :
	sprintf(
		'%1$s<a href="https://mythemeshop.com/go/aff/member/stats" target="_blank">%2$s</a>',
		esc_html__( 'Check your affiliate earning by following ', 'wp-quiz' ),
		esc_html__( 'this link', 'wp-quiz' )
	);

$cmb->add_field(
	array(
		'id'      => 'mts_username',
		'type'    => 'text',
		'name'    => __( 'MyThemeShop username', 'wp-quiz' ),
		'desc'    => $desc,
		'default' => $default_options['mts_username'],
	)
);

$cmb->add_field(
	array(
		'id'      => 'share_meta',
		'type'    => 'switch',
		'name'    => __( 'Enable Open Graph & Twitter Cards meta tags', 'wp-quiz' ) . '<span class="pro-text">' . esc_html__( 'Pro feature', 'wp-quiz' ) . '</span>',
		'desc'    => __( 'If enabled, when user shares the quiz result on Facebook or Twitter, details of final results will be used(example: Result image, text etc).', 'wp-quiz' ),
		'default' => 'off',
		'classes' => 'wp-quiz-pro-feature',
	)
);

$cmb->add_field(
	array(
		'id'      => 'players_tracking',
		'type'    => 'switch',
		'name'    => __( 'Enable players tracking', 'wp-quiz' ) . '<span class="pro-text">' . esc_html__( 'Pro feature', 'wp-quiz' ) . '</span>',
		'desc'    => __( 'You can track advanced player statistics using this option.', 'wp-quiz' ),
		'default' => 'off',
		'classes' => 'wp-quiz-pro-feature',
	)
);

$cmb->add_field(
	array(
		'id'      => 'email_result_to_admin',
		'type'    => 'switch',
		'name'    => __( 'Send email result to admin', 'wp-quiz' ) . '<span class="pro-text">' . esc_html__( 'Pro feature', 'wp-quiz' ) . '</span>',
		'desc'    => __( 'You can recive emails regarding each played quiz and their result.', 'wp-quiz' ),
		'default' => 'off',
		'classes' => 'wp-quiz-pro-feature',
	)
);

$cmb->add_field(
	array(
		'id'      => 'css_footer',
		'type'    => 'switch',
		'name'    => __( 'Enqueue CSS files in footer', 'wp-quiz' ) . '<span class="pro-text">' . esc_html__( 'Pro feature', 'wp-quiz' ) . '</span>',
		'desc'    => __( 'Will improve overall site score. Please use this option with caution as this can also break styling in the frontend.', 'wp-quiz' ),
		'default' => 'off',
		'classes' => 'wp-quiz-pro-feature',
	)
);
