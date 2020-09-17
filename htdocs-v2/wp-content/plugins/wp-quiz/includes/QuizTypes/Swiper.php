<?php
/**
 * Swiper quiz type
 *
 * @package WPQuizPro
 */

namespace WPQuiz\QuizTypes;

use WPQuiz\QuizType;

/**
 * Class Swiper
 */
class Swiper extends QuizType {

	/**
	 * Quiz type icon class.
	 *
	 * @var string
	 */
	protected $icon = 'dashicons dashicons-tickets-alt';

	/**
	 * Swiper constructor.
	 */
	public function __construct() {
		$this->name = 'swiper';
		$this->desc = __( 'Swiper quizzes are fun to play, easy to create, and are great to capture and compare results from a group of people.', 'wp-quiz' );
	}
}
