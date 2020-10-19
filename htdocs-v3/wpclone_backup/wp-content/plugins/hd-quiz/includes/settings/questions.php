<?php
if (hdq_user_permission()) {
    hdq_load_quiz_tabs();
}

function hdq_load_quiz_tabs()
{
    $quizID = 0;
    if (isset($_POST['quiz'])) {
        $quizID = intval($_POST['quiz']);
    }
    
    $quiz = get_term($quizID, "quiz");
    $fields = get_hdq_quiz($quizID); ?>
	<a href = "<?php echo get_admin_url(); ?>admin.php?page=hdq_quizzes" title = "view all quizzes">&laquo; back to quizzes</a>
    <div id="header">
		
        <h1 id="heading_title">
            <?php echo $quiz->name; ?>
        </h1>
        <div id="header_actions">
			<div role = "button" title = "Add a new question" class="hdq_button2" data-id="<?php echo $quizID; ?>" id="hdq_add_question">
				<span class="dashicons dashicons-plus"></span> ADD NEW QUESTION
			</div>			
            <div role = "button" title = "Save quiz settings" id="save" data-id="save-settings" class="hdq_button" title="save settings"><span class="dashicons dashicons-sticky"></span> SAVE QUIZ</div>
        </div>
    </div>

<p>
	Quiz Shortcode: <code>[HDquiz quiz = "<?php echo $quizID; ?>"]</code> <br/><small>You can copy / paste that shortcode onto any post or page to display this quiz or use the built-in Gutenberg block.</small>
</p>
<p>
	Add a new question to this quiz, or select a question below to edit it. You can also drag-and-drop to re-order the questions <span class="hdq_tooltip">
    ?
    <span class="hdq_tooltip_content">
        <span>Just remember to save the quiz after reordering</span>
    </span>
</span>.
</p>

	<div id = "hdq_quiz_tabs">
		<div id = "hdq_quiz_tabs_labels">
			<div data-id = "hdq_questions_list" class = "hdq_quiz_tab hdq_quiz_tab_active">
				QUESTIONS
			</div>
			<div data-id = "hdq_settings_page" class = "hdq_quiz_tab">
				QUIZ SETTINGS
			</div>			
		</div>
	<div id = "hdq_quiz_tabs_content">	

	<div id="hdq_questions_list" class="content">
<?php
// WP_Query arguments
$args = array(
    'post_type' => array('post_type_questionna'),
    'tax_query' => array(
        array(
            'taxonomy' => 'quiz',
            'terms' => $quizID,
        ),
    ),
    'nopaging' => true,
    'posts_per_page' => '-1',
    'order' => 'ASC',
    'orderby' => 'menu_order',
);

    // The Query
    $query = new WP_Query($args);
    $menu_number = 0;

    // The Loop
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $menu_number = $menu_number + 1;
            $title = get_the_title();
            if (function_exists("mb_strimwidth")) {
                $title = mb_strimwidth($title, 0, 70, "...");
            }
            echo '<div role = "button" class = "hdq_quiz_item hdq_quiz_question" data-id = "' . get_the_ID() . '" data-quiz-id = "' . $quizID. '"><span class = "hdq_quiz_item_drag" title = "drag and drop to reorder questions">≡</span>' . $menu_number . ". " . $title . '</div>';
        }
    } else {
        echo '<p>Newly added questions will appear here</p>';
    }

    // Restore original Post Data
    wp_reset_postdata()

?>		
	</div>
    <div id="hdq_settings_page" class="content">
        <div id="content_tabs">
            <div id="tab_nav_wrapper">
				<div id="hdq_logo">
					<span class="hdq_logo_tooltip"><img src="<?php echo plugins_url('../images/hd-logo.png', __FILE__); ?>">
						<span class="hdq_logo_tooltip_content">
                        <span><strong>HD Quiz</strong> is developed by Harmonic Design. Check out the addons page to see how you can extend HD Quiz even further.</span>
                    	</span>
					</span>
				</div>
                <div id="tab_nav">
                    <?php hdq_print_quiz_tabs(); ?>
                </div>
            </div>
            <div id="tab_content">
				<input type= "hidden" class = "hderp_input" id = "quiz_id" style = "display:none" data-required = "true" data-type = "integer" value = "<?php echo $quizID; ?>"/>
                <?php hdq_print_quiz_tab_content($fields); ?>
            </div>
        </div>
    </div>
</div></div>
<?php
}
