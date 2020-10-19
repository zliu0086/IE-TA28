<?php
/**
 * Facebook quiz type
 *
 * @package WPQuizPro
 */

namespace WPQuiz\QuizTypes;

use WPQuiz\QuizType;

/**
 * Class FBQuiz
 */
class FBQuiz extends QuizType {

	/**
	 * Quiz type icon class.
	 *
	 * @var string
	 */
	protected $icon = 'dashicons dashicons-facebook';

	/**
	 * FBQuiz constructor.
	 */
	public function __construct() {
		$this->name = 'fb_quiz';
		$this->desc = __( 'Create incredibly engaging quizzes which require very less effort on the user\'s part and always gets great engagement and shares.', 'wp-quiz' );
	}

	/**
	 * Gets quiz type title.
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'Facebook quiz', 'wp-quiz' );
	}
}
