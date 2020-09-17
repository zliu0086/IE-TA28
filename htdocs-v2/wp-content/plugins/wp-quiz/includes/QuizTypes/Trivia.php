<?php
/**
 * Trivia quiz
 *
 * @package WPQuiz
 */

namespace WPQuiz\QuizTypes;

use WPQuiz\PlayDataTracking\PlayData;
use WPQuiz\PostTypeQuiz;
use WPQuiz\QuizType;
use WPQuiz\Quiz;
use WPQuiz\Helper;

/**
 * Class Trivia
 */
class Trivia extends QuizType {

	/**
	 * Trivia constructor.
	 */
	public function __construct() {
		$this->name = 'trivia';
		$this->desc = __( 'Create Trivia Quizzes to test your users\' knowledge about a subject and then surprise them with the results.', 'wp-quiz' );
	}

	/**
	 * Gets default settings.
	 *
	 * @return array
	 */
	public function get_default_settings() {
		return array(
			'question_layout'  => 'single',
			'skin'             => 'flat',
			'bar_color'        => '#00c479',
			'font_color'       => '#444',
			'background_color' => '#ecf0f1',
			'animation_in'     => 'fadeIn',
			'animation_out'    => 'fadeOut',
			'show_ads'         => false,
		);
	}

	/**
	 * Gets default data for question.
	 *
	 * @return array
	 */
	public function get_default_question() {
		return array(
			'id'                 => '',
			'title'              => '',
			'desc'               => '',
			'hint'               => '',
			'mediaType'          => 'image',
			'image'              => '',
			'imageId'            => '',
			'video'              => '',
			'imagePlaceholder'   => '',
			'imagePlaceholderId' => '',
			'imageCredit'        => '',
			'answerType'         => 'text',
			'answers'            => array(),
		);
	}

	/**
	 * Gets default data for answer.
	 *
	 * @return array
	 */
	public function get_default_answer() {
		return array(
			'id'        => '',
			'title'     => '',
			'image'     => '',
			'imageId'   => '',
			'isCorrect' => '',
		);
	}

	/**
	 * Gets default data for result.
	 *
	 * @return array
	 */
	public function get_default_result() {
		return array(
			'id'           => '',
			'title'        => '',
			'image'        => '',
			'imageId'      => '',
			'min'          => 0,
			'max'          => 1,
			'desc'         => '',
			'redirect_url' => '',
		);
	}

	/**
	 * Prints question js template for backend.
	 *
	 * @param Quiz $quiz Quiz object.
	 */
	protected function backend_question_js_template( Quiz $quiz ) {
		/**
		 * Template variables:
		 *
		 * @type Object question
		 * @type String baseName
		 * @type Number index
		 * @type Object i18n
		 */
		?>
		<input type="hidden" class="wp-quiz-question-media-type" name="{{ baseName }}[mediaType]" value="{{ data.question.mediaType }}">
		<input type="hidden" class="wp-quiz-question-answer-type" name="{{ baseName }}[answerType]" value="{{ data.question.answerType }}">

		<div class="wp-quiz-question-heading">
			<div class="wp-quiz-question-number">{{ data.index + 1 }}</div>
			<div class="wp-quiz-question-types">
				<button type="button" title="<?php esc_attr_e( 'Image question', 'wp-quiz' ); ?>" class="wp-quiz-set-question-type-btn" data-type="image">
					<span class="dashicons dashicons-format-image"></span>
				</button>

				<button type="button" title="<?php esc_html_e( 'Video question', 'wp-quiz' ); ?>" class="wp-quiz-set-question-type-btn" data-type="video">
					<span class="dashicons dashicons-format-video"></span>
				</button>

				<?php $this->backend_remove_question_btn(); ?>
			</div>
		</div><!-- End .wp-quiz-question-heading -->

		<div class="wp-quiz-question-content">
			<div class="wp-quiz-question-image">

				<div class="wp-quiz-image-upload style-overlay {{ data.question.image ? '' : 'no-image' }}" data-edit-text="{{ data.i18n.editImage }}" data-upload-text="{{ data.i18n.uploadImage }}">
					<div class="wp-quiz-image-upload-preview">
						<# if (data.question.image) { #>
						<img src="{{ data.question.image }}" alt="">
						<# } #>
					</div><!-- End .wp-quiz-image-upload-preview -->

					<button type="button" class="wp-quiz-image-upload-btn">
						{{ data.question.image ? data.i18n.editImage : data.i18n.uploadImage }}
					</button>

					<?php $this->backend_remove_image_btn( __( 'Remove', 'wp-quiz' ) ); ?>

					<input type="text" class="wp-quiz-image-upload-credit" name="{{ baseName }}[imageCredit]" placeholder="<?php esc_attr_e( 'Credit', 'wp-quiz' ); ?>" value="{{ data.question.imageCredit }}">

					<input type="hidden" class="wp-quiz-image-upload-url" name="{{ baseName }}[image]" value="{{ data.question.image }}">
					<input type="hidden" class="wp-quiz-image-upload-id" name="{{ baseName }}[imageId]" value="{{ data.question.imageId }}">
				</div><!-- End .wp-quiz-image-upload -->

			</div><!-- End .wp-quiz-question-image -->

			<div class="wp-quiz-question-video">
				<div class="wp-quiz-video-upload {{ data.question.video ? '' : 'no-video' }}">
					<div class="wp-quiz-video-upload-error"></div>
					<div class="wp-quiz-video-upload-preview"></div>

					<div class="wp-quiz-video-upload-url-wrapper">
						<input type="url" class="wp-quiz-video-upload-url" placeholder="<?php esc_attr_e( 'Video URL', 'wp-quiz' ); ?>" name="{{ baseName }}[video]" value="{{ data.question.video }}">
						<button type="button" class="button button-large wp-quiz-upload-video-btn"><?php esc_html_e( 'Upload video', 'wp-quiz' ); ?></button>
						<!-- <button type="button" class="button button-large wp-quiz-load-video-preview-btn"><?php esc_html_e( 'Preview', 'wp-quiz' ); ?></button> -->
					</div>

					<div class="wp-quiz-image-upload">
						<div class="wp-quiz-image-upload-url-wrapper">
							<input type="url" class="wp-quiz-image-upload-url" name="{{ baseName }}[imagePlaceholder]" value="{{ data.question.imagePlaceholder }}" placeholder="<?php esc_attr_e( 'Video image placeholder', 'wp-quiz' ); ?>">
							<button type="button" class="button button-large wp-quiz-image-upload-btn"><?php esc_html_e( 'Upload image', 'wp-quiz' ); ?></button>
						</div>
						<input type="hidden" class="wp-quiz-image-upload-id" name="{{ baseName }}[imagePlaceholderId]" value="{{ data.question.imagePlaceholderId }}">
					</div><!-- End .wp-quiz-image-upload -->

				</div><!-- End .question-video -->
			</div><!-- End .wp-quiz-question-video -->

			<p>
				<textarea class="widefat wp-quiz-question-title" name="{{ baseName }}[title]" placeholder="<?php esc_attr_e( 'Question text?', 'wp-quiz' ); ?>" rows="1" data-autoresize>{{ data.question.title }}</textarea>
			</p>

			<p>
				<textarea class="widefat wp-quiz-question-desc" rows="4" name="{{ baseName }}[desc]" placeholder="<?php esc_attr_e( 'Answer Explanation', 'wp-quiz' ); ?>">{{ data.question.desc }}</textarea>
			</p>

			<p>
				<label for="wp-quiz-question-hint-{{ data.question.id }}"><?php esc_html_e( 'Question hint (optional)', 'wp-quiz' ); ?></label>
				<textarea id="wp-quiz-question-hint-{{ data.question.id }}" class="widefat wp-quiz-question-hint" rows="4" name="{{ baseName }}[hint]" style="width: 100%;">{{ data.question.hint }}</textarea>
			</p>

			<?php $this->backend_answers_list( $quiz ); ?>
		</div><!-- End .wp-quiz-question-content -->
		<?php
	}

	/**
	 * Prints backend answers list template for backend.
	 *
	 * @param Quiz $quiz Quiz object.
	 */
	protected function backend_answers_list( Quiz $quiz ) {
		?>
		<div
			class="wp-quiz-answers wp-quiz-<?php echo esc_attr( $this->name ); ?>-answers"
			data-base-name="{{ baseName }}[answers]"
			data-type="{{ data.question.answerType }}"
		>
			<div class="wp-quiz-answers-heading">
				<h3><?php esc_html_e( 'Answers', 'wp-quiz' ); ?></h3>

				<div class="wp-quiz-answer-type-btns">
					<button type="button" title="<?php esc_attr_e( 'Text answers', 'wp-quiz' ); ?>" class="wp-quiz-set-answer-type-btn" data-type="text">
						<span class="dashicons dashicons-format-aside"></span>
					</button>
					<button type="button" title="<?php esc_attr_e( 'Image answers', 'wp-quiz' ); ?>" class="wp-quiz-set-answer-type-btn" data-type="image">
						<span class="dashicons dashicons-format-image"></span>
					</button>
				</div>
			</div>

			<div class="wp-quiz-answers-list"></div><!-- End .wp-quiz-answers-list -->
			<?php $this->backend_add_answer_btn(); ?>
		</div><!-- End .wp-quiz-questions -->
		<?php
	}

	/**
	 * Prints answer js template for backend.
	 *
	 * @param Quiz $quiz Quiz object.
	 */
	protected function backend_answer_js_template( Quiz $quiz ) {
		?>
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

		<p>
			<textarea class="widefat wp-quiz-answer-title" name="{{ baseName }}[title]" placeholder="<?php esc_attr_e( 'Answer text', 'wp-quiz' ); ?>" rows="1" data-autoresize>{{ data.answer.title }}</textarea>
		</p>

		<p>
			<label><input type="checkbox" class="wp-quiz-answer-correct-checkbox" name="{{ baseName }}[isCorrect]" value="1" <# if (parseInt(data.answer.isCorrect)) { #>checked<# } #>> <?php esc_html_e( 'Correct answer', 'wp-quiz' ); ?></label>
		</p>

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

		<div class="wp-quiz-result-range">
			<label>
				<span><?php esc_html_e( 'Min', 'wp-quiz' ); ?></span>
				<input type="number" min="0" step="1" class="small-text" name="{{ baseName }}[min]" value="{{ data.result.min }}">
			</label>
			<label>
				<span><?php esc_html_e( 'Max', 'wp-quiz' ); ?></span>
				<input type="number" min="0" step="1" class="small-text" name="{{ baseName }}[max]" value="{{ data.result.max }}">
			</label>
		</div><!-- End .wp-quiz-result-range -->

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
	 * Adds extra data for question.
	 *
	 * @param array $question Question data.
	 */
	public function add_question_extra_data( array &$question ) {
		$question['totalCorrects'] = $this->get_total_corrects( $question );

		// Check answer type for old version.
		if ( empty( $question['answerType'] ) ) {
			$answer_type = 'text';
			foreach ( $question['answers'] as $answer ) {
				if ( ! empty( $answer['image'] ) ) {
					$answer_type = 'image';
					break;
				}
			}
			$question['answerType'] = $answer_type;
		}
	}

	/**
	 * Gets number of correct answers.
	 *
	 * @param array $question Question data.
	 * @return int
	 */
	protected function get_total_corrects( array $question ) {
		$corrects_count = 0;
		if ( empty( $question['answers'] ) || ! is_array( $question['answers'] ) ) {
			return $corrects_count;
		}
		foreach ( $question['answers'] as $answer ) {
			if ( ! empty( $answer['isCorrect'] ) ) {
				$corrects_count++;
			}
		}
		return $corrects_count;
	}

	/**
	 * Gets js data.
	 *
	 * @param Quiz $quiz Quiz object.
	 * @return array
	 */
	protected function get_js_data( Quiz $quiz ) {
		$data = parent::get_js_data( $quiz );
		if ( is_singular( PostTypeQuiz::get_name() ) && ! empty( $_GET['wqtid'] ) ) { // WPCS: csrf ok.
			$player = Helper::get_player( $_GET['wqtid'] ); // WPCS: csrf, sanitization ok.
			if ( $player ) {
				if ( $player['answer_data'] ) {
					$player['answer_data'] = json_decode( $player['answer_data'] );
				}
				if ( $player['result_data'] ) {
					$player['result_data'] = json_decode( $player['result_data'] );
				}
				$data['answered'] = $player;
			}
		}

		return $data;
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
		$corrects  = isset( $player_data['corrects'] ) ? $player_data['corrects'] : 0;
		$result_id = isset( $player_data['result_id'] ) ? $player_data['result_id'] : '';
		return array(
			'quiz_id'          => $quiz->get_id(),
			'correct_answered' => $corrects,
			'result'           => $result_id,
			'quiz_type'        => $this->name,
			'quiz_data'        => $quiz_data,
			'answered_data'    => $answered,
		);
	}
}
