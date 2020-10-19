<?php
    // question type template
    // default multiple choice text

    // print question title
    hdq_print_question_title($question_number, $question);
    // print out answers
    $answers = $question["answers"]["value"];
    
    for ($i = 0; $i < count($answers); $i++) {
        if ($answers[$i]["answer"] != "" && $answers[$i]["answer"] != null) {
            $selected = 0;
            if ($answers[$i]["correct"]) {
                $selected = 1;
            } ?>
			<div class = "hdq_row">
				<label class="hdq_label_answer" data-type = "radio" data-id = "hdq_question_<?php echo $question_ID; ?>" for="hdq_option_<?php echo $i . '_' . $question_ID; ?>">
					<div class="hdq-options-check">
						<input type="checkbox" data-id = "<?php echo $question_ID; ?>" class="hdq_option hdq_check_input" data-type = "radio" value="<?php echo $selected; ?>" name="hdq_option_<?php echo $i . '_' . $question_ID; ?>" id="hdq_option_<?php echo $i . '_' . $question_ID; ?>">
						<label for="hdq_option_<?php echo $i . '_' . $question_ID; ?>"></label>						
					</div>
				<?php echo $answers[$i]["answer"]; ?>
				</label>
			</div>
	<?php
        }
    }
