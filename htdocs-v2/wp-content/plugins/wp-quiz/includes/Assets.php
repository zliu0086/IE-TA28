<?php
/**
 * Class Assets
 *
 * @package WPQuiz
 */

namespace WPQuiz;

/**
 * Class Assets
 */
class Assets {

	/**
	 * Initializes.
	 */
	public function init() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
	}

	/**
	 * Enqueues assets.
	 */
	public function enqueue() {
		$quiz_types = QuizTypeManager::get_all( true );

		// Register.
		wp_register_style( 'animate', wp_quiz()->assets() . 'css/animate.css', array(), '3.6.0' );

		wp_register_script( 'jquery.serialize-object', wp_quiz()->admin_assets() . 'js/jquery.serialize-object.js', array( 'jquery' ), '2.5.0', true ); // WordPress has jquery-serialize-object script in core.

		wp_register_style( 'wp-quiz', wp_quiz()->assets() . 'css/wp-quiz.css', array(), wp_quiz()->version );
		wp_register_script( 'wp-quiz', wp_quiz()->assets() . 'js/wp-quiz.js', array( 'wp-util', 'jquery' ), wp_quiz()->version, true );

		wp_register_style( 'wp-quiz-rtl', wp_quiz()->assets() . 'css/wp-quiz-rtl.css', array(), wp_quiz()->version );

		foreach ( $quiz_types as $quiz_type ) {
			wp_register_script( 'wp-quiz-' . $quiz_type->get_name(), wp_quiz()->assets() . "js/quiz-types/{$quiz_type->get_name()}.js", array( 'wp-quiz' ), wp_quiz()->version, true );
		}

		// Localize scripts.
		wp_localize_script(
			'wp-quiz',
			'wpQuiz',
			array(
				'restUrl'    => rest_url(),
				'restNonce'  => wp_create_nonce( 'wp_rest' ),
				'settings'   => Helper::get_option(),
				'stripeName' => get_bloginfo( 'name' ),
				'i18n'       => array(
					'correct'               => esc_html__( 'Correct!', 'wp-quiz' ),
					'wrong'                 => esc_html__( 'Wrong!', 'wp-quiz' ),
					'resultScore'           => esc_html__( 'You got %%score%% out of %%total%%', 'wp-quiz' ),
					'captionTriviaFB'       => esc_html__( 'I got %%score%% out of %%total%%, and you?', 'wp-quiz' ),
					'youVoted'              => esc_html__( 'You voted', 'wp-quiz' ),
					'emptyNameEmail'        => esc_html__( 'Please enter your name and email address!', 'wp-quiz' ),
					'errorCaptureEmail'     => esc_html__( 'Error submitting details, please try again', 'wp-quiz' ),
					'errorLoadProfile'      => esc_html__( 'Can not load profile data', 'wp-quiz' ),
					'errorLoadAvatar'       => esc_html__( 'Can not load profile avatar', 'wp-quiz' ),
					'errorNoFriends'        => esc_html__( 'You do not have any friends which use this app', 'wp-quiz' ),
					// translators: Facebook name.
					'continueAs'            => esc_html__( 'Continue as %s', 'wp-quiz' ),
					'emptyUsernamePassword' => esc_html__( 'Username or password must not be empty', 'wp-quiz' ),
					'emptyRequiredFields'   => esc_html__( 'You must fill all required fields', 'wp-quiz' ),
					'passwordsNotMatch'     => esc_html__( 'Password does not match', 'wp-quiz' ),
				),
			)
		);

		if ( 'on' !== Helper::get_option( 'css_footer' ) ) {
			$this->enqueue_styles();
		}

		do_action( 'wp_quiz_register_public_styles' );
	}

	/**
	 * Enqueues styles.
	 */
	public function enqueue_styles() {
		wp_enqueue_style( 'animate' );
		wp_enqueue_style( 'wp-quiz' );

		if ( is_rtl() ) {
			wp_enqueue_style( 'wp-quiz-rtl' );
		}
	}
}
