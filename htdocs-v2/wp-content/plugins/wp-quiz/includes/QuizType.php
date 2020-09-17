<?php
/**
 * Base quiz type
 *
 * @package WPQuizPro
 */

namespace WPQuiz;

use WPQuiz\PlayDataTracking\PlayData;
use WPQuiz\Traits\QuizTypeBackend;
use WPQuiz\Traits\QuizTypeFrontend;

/**
 * Class QuizType
 */
class QuizType {

	use QuizTypeBackend;
	use QuizTypeFrontend;

	/**
	 * Quiz type name.
	 *
	 * @var string
	 */
	protected $name = '';

	/**
	 * Quiz description.
	 *
	 * @var string
	 */
	protected $desc = '';

	/**
	 * Default enable this quiz type in settings.
	 *
	 * @var bool
	 */
	protected $default_enabled = true;

	/**
	 * Has results or not.
	 *
	 * @var bool
	 */
	protected $has_results = true;

	/**
	 * Has answers or not.
	 *
	 * @var bool
	 */
	protected $has_answers = true;

	/**
	 * Quiz type icon class.
	 *
	 * @var string
	 */
	protected $icon = 'dashicons dashicons-editor-help';

	/**
	 * Processed questions.
	 *
	 * @var array
	 */
	protected $processed_questions = array();

	/**
	 * Gets quiz type name.
	 *
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * Gets quiz type title.
	 *
	 * @return string
	 */
	public function get_title() {
		return ucfirst( $this->name );
	}

	/**
	 * Shows quiz type title.
	 *
	 * @param string $prefix Prefix.
	 * @param string $suffix Suffix.
	 * @param bool   $echo   Show the output or just return it.
	 * @return string
	 */
	public function show_title( $prefix = '', $suffix = '', $echo = true ) {
		$output = $prefix . $this->get_title() . $suffix;
		if ( ! $echo ) {
			return $output;
		}
		echo wp_kses_post( $output );
	}

	/**
	 * Gets quiz type description.
	 *
	 * @return string
	 */
	public function get_desc() {
		return $this->desc;
	}

	/**
	 * Shows quiz type description.
	 *
	 * @param string $prefix Prefix.
	 * @param string $suffix Suffix.
	 * @param bool   $echo   Show the output or just return it.
	 * @return string
	 */
	public function show_desc( $prefix = '', $suffix = '', $echo = true ) {
		$output = $prefix . $this->desc . $suffix;
		if ( ! $echo ) {
			return $output;
		}
		echo wp_kses_post( $output );
	}

	/**
	 * Gets icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return $this->icon;
	}

	/**
	 * Checks if this type is enabled.
	 *
	 * @return bool
	 */
	public function is_enabled() {
		return 'on' === Helper::get_option( 'enable_' . $this->name );
	}

	/**
	 * Checks if is active screen.
	 *
	 * @return bool
	 */
	protected function is_edit_screen() {
		return get_current_screen()->id === PostTypeQuiz::get_name();
	}

	/**
	 * Gets quiz default settings.
	 *
	 * @return array
	 */
	public function get_default_settings() {
		return array();
	}

	/**
	 * Gets default data for question.
	 *
	 * @return array
	 */
	public function get_default_question() {
		return array(
			'id'      => '',
			'title'   => '',
			'desc'    => '',
			'answers' => array(),
		);
	}

	/**
	 * Gets default data for answer.
	 *
	 * @return array
	 */
	public function get_default_answer() {
		return array(
			'id'      => '',
			'title'   => '',
			'image'   => '',
			'imageId' => '',
		);
	}

	/**
	 * Gets default data for result.
	 *
	 * @return array
	 */
	public function get_default_result() {
		return array(
			'id'      => '',
			'title'   => '',
			'image'   => '',
			'imageId' => '',
		);
	}

	/**
	 * Gets questions.
	 *
	 * @param Quiz $quiz Quiz object.
	 * @return array
	 */
	protected function get_questions( Quiz $quiz ) {
		return $quiz->get_questions();
	}

	/**
	 * Gets results.
	 *
	 * @param Quiz $quiz Quiz object.
	 * @return array
	 */
	protected function get_results( Quiz $quiz ) {
		return $quiz->get_results();
	}

	/**
	 * Gets settings.
	 *
	 * @param Quiz $quiz Quiz object.
	 * @return array
	 */
	protected function get_settings( Quiz $quiz ) {
		return $quiz->get_settings();
	}

	/**
	 * Gets processed questions.
	 *
	 * @param Quiz $quiz Quiz object.
	 * @return array
	 */
	protected function get_processed_questions( Quiz $quiz ) {
		if ( isset( $this->processed_questions[ $quiz->get_id() ] ) ) {
			return $this->processed_questions[ $quiz->get_id() ];
		}

		$questions = $this->get_questions( $quiz );

		// Questions order for list quiz.
		$orderby = $quiz->get_setting( 'question_orderby' );
		if ( 'random' === $orderby ) {
			shuffle( $questions );
		} elseif ( 'votes' === $orderby ) {
			uasort( $questions, array( $this, 'sort_questions_by_votes_callback' ) );
		}

		$index = 0;
		foreach ( $questions as &$question ) {
			$question          = wp_parse_args( $question, $this->get_default_question() );
			$question['index'] = $index;
			$this->add_question_extra_data( $question );
			$index++;
		}

		$this->processed_questions[ $quiz->get_id() ] = $questions;

		return $questions;
	}

	/**
	 * Sort questions by votes callback.
	 *
	 * @param array $a Question A.
	 * @param array $b Question B.
	 * @return int
	 */
	public function sort_questions_by_votes_callback( $a, $b ) {
		$a['votesUp']   = isset( $a['votesUp'] ) ? intval( $a['votesUp'] ) : 0;
		$a['votesDown'] = isset( $a['votesDown'] ) ? intval( $a['votesDown'] ) : 0;
		$b['votesUp']   = isset( $b['votesUp'] ) ? intval( $b['votesUp'] ) : 0;
		$b['votesDown'] = isset( $b['votesDown'] ) ? intval( $b['votesDown'] ) : 0;

		$a_point = $a['votesUp'] - $a['votesDown'];
		$b_point = $b['votesUp'] - $b['votesDown'];
		if ( $a_point === $b_point ) {
			return 1;
		}
		return $a_point > $b_point ? -1 : 1;
	}

	/**
	 * Gets player data to insert into DB.
	 *
	 * @param Quiz  $quiz        Quiz object.
	 * @param array $player_data Player data get from REST request.
	 * @return array|false       Return an array with keys corresponding to players table columns.
	 *                           Return `false` if do not want to track player data on this quiz type.
	 */
	public function get_inserting_player_data( Quiz $quiz, array $player_data ) {
		$insert_data = array(
			'user_id' => is_user_logged_in() ? get_current_user_id() : null,
			'user_ip' => Helper::get_current_ip(),
			'type'    => is_user_logged_in() ? 'user' : 'guest',
		);

		/**
		 * Allows changing player insert data.
		 *
		 * @since 2.0.0
		 *
		 * @param array $insert_data Player insert data.
		 * @param array $player_data Unprocessed player data from REST request.
		 * @param Quiz  $quiz        Quiz object.
		 */
		return apply_filters( 'wp_quiz_player_insert_data', $insert_data, $player_data, $quiz );
	}

	/**
	 * Gets play data to insert into DB.
	 *
	 * @param Quiz  $quiz        Quiz object.
	 * @param array $player_data Player data get from REST request.
	 * @return array|false       Return an array with keys corresponding to plays table columns.
	 *                           Return `false` if do not want to track player data on this quiz type.
	 */
	public function get_inserting_play_data( Quiz $quiz, array $player_data ) {
		_doing_it_wrong( __METHOD__, esc_html__( 'This method must be overridden in quiz type classes', 'wp-quiz' ), '2.0.0' );
		return false;
	}

	/**
	 * Shows play data detail.
	 *
	 * @param PlayData $play_data Play data.
	 * @param bool     $no_result Not show the result.
	 */
	public function show_tracking_data( PlayData $play_data, $no_result = false ) {}

	/**
	 * Gets quiz result email output.
	 *
	 * @param Quiz     $quiz      Quiz object.
	 * @param PlayData $play_data Play data.
	 * @return string
	 */
	public function quiz_result_email( Quiz $quiz, PlayData $play_data ) {
		return '';
	}
}
