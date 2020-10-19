<?php
/**
 * List quiz type
 *
 * @package WPQuizPro
 */

namespace WPQuiz\QuizTypes;

use WPQuiz\QuizType;

/**
 * Class ListQuiz
 */
class ListQuiz extends QuizType {

	/**
	 * Quiz type icon class.
	 *
	 * @var string
	 */
	protected $icon = 'dashicons dashicons-format-gallery';

	/**
	 * Flip constructor.
	 */
	public function __construct() {
		$this->name = 'listquiz';
		$this->desc = __( 'Combine the power of listicles and quizzes to generate some incredible engagement from your users and great results for yourself.', 'wp-quiz' );
	}

	/**
	 * Gets quiz type title.
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'List quiz', 'wp-quiz' );
	}
}
