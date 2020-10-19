<?php
/**
 * Personality quiz
 *
 * @package WPQuiz
 */

namespace WPQuiz\QuizTypes;

use WPQuiz\PlayDataTracking\PlayData;
use WPQuiz\Quiz;
use WPQuiz\Template;

/**
 * Class Personality
 */
class Personality extends Trivia {

	/**
	 * Quiz type icon class.
	 *
	 * @var string
	 */
	protected $icon = 'dashicons dashicons-admin-users';

	/**
	 * Personality constructor.
	 */
	public function __construct() {
		$this->name = 'personality';
		$this->desc = __( 'Ask a series of questions to your user and reveal something about them even they didn\'t know about.', 'wp-quiz' );
	}

	/**
	 * Gets default data for answer.
	 *
	 * @return array
	 */
	public function get_default_answer() {
		return array(
			'title'   => '',
			'image'   => '',
			'imageId' => '',
			'results' => array(),
		);
	}

	/**
	 * Gets backend quiz classes.
	 *
	 * @return array
	 */
	protected function backend_quiz_classes() {
		return array(
			'wp-quiz-backend',
			'wp-quiz-trivia-backend',
			"wp-quiz-{$this->name}-backend",
		);
	}

	/**
	 * Enqueues backend scripts.
	 */
	public function enqueue_backend_scripts() {
		wp_enqueue_script( 'wp-quiz-admin-trivia' );
		parent::enqueue_backend_scripts();
	}

	/**
	 * Prints backend content.
	 *
	 * @param Quiz $quiz Quiz object.
	 */
	protected function backend_content( Quiz $quiz ) {
		Template::notice( __( 'Please add results before adding questions', 'wp-quiz' ), 'info inline', true, true );
		$this->backend_results_list( $quiz );
		$this->backend_questions_list( $quiz );
		$this->backend_js_templates( $quiz );
	}

	/**
	 * Prints backend js templates.
	 *
	 * @param Quiz $quiz Quiz object.
	 */
	protected function backend_js_templates( Quiz $quiz ) {
		parent::backend_js_templates( $quiz );
		?>
		<script type="text/html" id="tmpl-wp-quiz-<?php echo esc_attr( $this->name ); ?>-answer-result-tpl">
			<li class="wp-quiz-answer-result" data-id="{{ data.result.id }}">
				<span class="wp-quiz-answer-result-title">{{ data.result.title }}</span>
				<input type="number" min="0" step="1" class="tiny-text wp-quiz-answer-result-point" name="{{ data.baseName }}[{{ data.result.id }}]" value="{{ data.result.point || 0 }}">
			</li>
		</script>
		<?php
	}

	/**
	 * Prints answer js template for backend.
	 *
	 * @param Quiz $quiz Quiz object.
	 */
	protected function backend_answer_js_template( Quiz $quiz ) {
		?>
		<p>
			<textarea class="widefat wp-quiz-answer-title" name="{{ baseName }}[title]" placeholder="<?php esc_attr_e( 'Answer text', 'wp-quiz' ); ?>" rows="1" data-autoresize>{{ data.answer.title }}</textarea>
		</p>

		<div class="wp-quiz-answer-image wp-quiz-image-upload style-overlay {{ data.answer.image ? '' : 'no-image' }}" data-edit-text="{{ data.i18n.editImage }}" data-upload-text="{{ data.i18n.uploadImage }}">
			<div class="wp-quiz-image-upload-preview">
				<# if (data.answer.image) { #>
				<img src="{{ data.answer.image }}" alt="">
				<# } #>
			</div><!-- End .wp-quiz-image-upload-preview -->

			<button type="button" class="wp-quiz-image-upload-btn">
				{{ data.answer.image ? data.i18n.editImage : data.i18n.uploadImage }}
			</button>

			<?php $this->backend_remove_image_btn( __( 'Remove', 'wp-quiz' ) ); ?>

			<input type="hidden" class="wp-quiz-image-upload-url" name="{{ baseName }}[image]" value="{{ data.answer.image }}">
			<input type="hidden" class="wp-quiz-image-upload-id" name="{{ baseName }}[imageId]" value="{{ data.answer.imageId }}">
		</div><!-- End .wp-quiz-image-upload -->

		<p><strong><?php esc_html_e( 'Associate results:', 'wp-quiz' ); ?></strong></p>
		<ul class="wp-quiz-answer-results" data-answer-results="{{ JSON.stringify( data.answer.results ) }}" data-base-name="{{ baseName }}[results]"></ul>

		<?php $this->backend_remove_answer_btn(); ?>
		<?php
	}

	/**
	 * Prints result js template for backend.
	 *
	 * @param Quiz $quiz Quiz object.
	 */
	protected function backend_result_js_template( Quiz $quiz ) {
		?>
		<p>
			<textarea class="widefat wp-quiz-result-title" name="{{ baseName }}[title]" placeholder="<?php esc_attr_e( 'Result title', 'wp-quiz' ); ?>" rows="1" data-autoresize>{{ data.result.title }}</textarea>
		</p>

		<div class="wp-quiz-result-image wp-quiz-image-upload style-overlay {{ data.result.image ? '' : 'no-image' }}" data-edit-text="{{ data.i18n.editImage }}" data-upload-text="{{ data.i18n.uploadImage }}">
			<div class="wp-quiz-image-upload-preview">
				<# if (data.result.image) { #>
				<img src="{{ data.result.image }}" alt="">
				<# } #>
			</div><!-- End .wp-quiz-image-upload-preview -->

			<button type="button" class="wp-quiz-image-upload-btn">
				{{ data.result.image ? data.i18n.editImage : data.i18n.uploadImage }}
			</button>

			<?php $this->backend_remove_image_btn( __( 'Remove', 'wp-quiz' ) ); ?>

			<input type="hidden" class="wp-quiz-image-upload-url" name="{{ baseName }}[image]" value="{{ data.result.image }}">
			<input type="hidden" class="wp-quiz-image-upload-id" name="{{ baseName }}[imageId]" value="{{ data.result.imageId }}">
		</div><!-- End .wp-quiz-image-upload -->

		<p>
			<textarea class="widefat wp-quiz-result-desc" name="{{ baseName }}[desc]" placeholder="<?php esc_attr_e( 'Description', 'wp-quiz' ); ?>">{{ data.result.desc }}</textarea>
		</p>

		<p>
			<input type="url" class="widefat wp-quiz-result-redirect-url" name="{{ baseName }}[redirect_url]" placeholder="<?php esc_attr_e( 'Redirect URL(optional)', 'wp-quiz' ); ?>" value="{{ data.result.redirect_url }}">
		</p>

		<?php $this->backend_remove_result_btn(); ?>
		<?php
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
		$quiz_data = isset( $player_data['quiz_data'] ) ? $player_data['quiz_data'] : json_decode( $quiz->get_post()->post_content, true );
		$answered  = isset( $player_data['answered'] ) ? $player_data['answered'] : array();
		$result_id = isset( $player_data['result_id'] ) ? $player_data['result_id'] : '';

		return array(
			'quiz_id'       => $quiz->get_id(),
			'result'        => $result_id,
			'quiz_type'     => $this->name,
			'quiz_data'     => $quiz_data,
			'answered_data' => $answered,
		);
	}
}
