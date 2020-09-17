<?php
/**
 * Quiz retake button template
 *
 * @package WPQuiz
 * @version 2.0.2
 *
 * @var \WPQuiz\Quiz $quiz
 */

?>
<!-- retake button -->
<div class="wq-retake-quiz wq_retakeQuizCtr">
	<button class="wq-retake-quiz-btn wq_retakeQuizBtn" type="button">
		<i class="wq-icon wq-icon-undo"></i>&nbsp; <?php esc_html_e( 'Play Again!', 'wp-quiz' ); ?>
	</button>
</div>
<!-- // retake button -->
