<?php
    // enqueue style and script
    wp_enqueue_style(
        'hdq_admin_style',
        plugin_dir_url(__FILE__) . './css/hdq_style.css',
        array(),
        HDQ_PLUGIN_VERSION
    );
    wp_enqueue_script(
        'hdq_admin_script',
        plugins_url('./js/hdq_script.js?', __FILE__),
        array('jquery'),
        HDQ_PLUGIN_VERSION,
        true
    );

    $buildQuiz = true;

    if (!is_singular()) {
        // if we are on a category, search, or home blog page
        // replace quiz with direct link to post or page
        hdq_print_quiz_in_loop();
        $buildQuiz = false;
    } else {
        if (function_exists("is_amp_endpoint")) {
            if (is_amp_endpoint()) {
                hdq_print_quiz_in_loop();
                $buildQuiz = false;
            }
        }
    }


    if ($buildQuiz === true) {
        $quiz_ID = intval($quiz); // quiz ID from shortcode

        // get quiz name
        $quiz_name = get_term($quiz_ID, "quiz");
        if ($quiz_name == null) {
            echo 'This quiz no longer exists';
            return;
        }
        $quiz_name = $quiz_name->name;

        $quiz_settings = get_hdq_quiz($quiz_ID);

        // get question order for query
        $question_order = "menu_order"; // default
        if (
            $quiz_settings["randomize_questions"]["value"][0] === "yes" ||
            $quiz_settings["pool_of_questions"]["value"] > 0
        ) {
            $question_order = "rand";
        }

        $per_page = -1; // show all questions by default
        $paginate = false;
        if ($quiz_settings["wp_paginate"]["value"] > 0) {
            if ($quiz_settings["pool_of_questions"]["value"] > 0) {
                return;
            } else {
                $paginate = true;
                $question_order = "menu_order";
                $per_page = $quiz_settings["wp_paginate"]["value"];
            }
        }

        if ($quiz_settings["pool_of_questions"]["value"] > 0) {
            $per_page = $quiz_settings["pool_of_questions"]["value"];
        }

        // if we should display ads
        $use_adcode = false;
        $hdq_adcode = get_option("hd_qu_adcode");
        if ($hdq_adcode != "" && $hdq_adcode != null) {
            $hdq_adcode = stripcslashes(urldecode($hdq_adcode));
            $use_adcode = true;
        }

        // Get the page or post featured image
        // (try to send to facebook for sharing results)
        $hdq_featured_image = "";
        if (has_post_thumbnail()) {
            $hdq_featured_image = wp_get_attachment_url(get_post_thumbnail_id(get_the_ID()), 'full');
        }

        $hdq_twitter_handle = sanitize_text_field(get_option("hd_qu_tw"));
        if ($hdq_twitter_handle == "" || $hdq_twitter_handle == null) {
            $hdq_twitter_handle = "harmonic_design";
        }

        $jPaginate = false;
        // create object for localized script
        $hdq_local_vars = new \stdClass();
        $hdq_local_vars->hdq_quiz_id = $quiz_ID;
        $hdq_local_vars->hdq_timer = $quiz_settings["quiz_timer"]["value"];
        $hdq_local_vars->hdq_timer_question = $quiz_settings["quiz_timer_question"]["value"][0];
        $hdq_local_vars->hdq_show_results = $quiz_settings["show_results"]["value"][0];
        $hdq_local_vars->hdq_results_correct = $quiz_settings["show_results_correct"]["value"][0];
        $hdq_local_vars->hdq_show_extra_text = $quiz_settings["show_extra_text"]["value"][0];
        $hdq_local_vars->hdq_show_results_now = $quiz_settings["show_results_now"]["value"][0];
        $hdq_local_vars->hdq_stop_answer_reselect = $quiz_settings["stop_answer_reselect"]["value"][0];
        $hdq_local_vars->hdq_pass_percent = $quiz_settings["quiz_pass_percentage"]["value"];
        $hdq_local_vars->hdq_share_results = $quiz_settings["share_results"]["value"][0];
        $hdq_local_vars->hdq_quiz_permalink = get_the_permalink();
        $hdq_local_vars->hdq_twitter_handle = $hdq_twitter_handle;
        $hdq_local_vars->hdq_quiz_name = $quiz_name;
        $hdq_local_vars->hdq_ajax = admin_url('admin-ajax.php');
        $hdq_local_vars->hdq_featured_image = $hdq_featured_image;
        $hdq_local_vars->hdq_use_ads = $use_adcode;
        $hdq_local_vars->hdq_submit = array();
        do_action("hdq_submit", $hdq_local_vars); // add functions to quiz complete

        $hdq_local_vars = json_encode($hdq_local_vars);
        wp_localize_script('hdq_admin_script', 'hdq_local_vars', $hdq_local_vars); ?>

<div class = "hdq_quiz_wrapper" id = "hdq_<?php echo $quiz_ID; ?>">
    <div class = "hdq_before">
		<?php do_action("hdq_before", $quiz_ID); ?>
	</div>
	
	<?php
            hdq_print_quiz_start($quiz_settings["quiz_timer"]["value"], $use_adcode); ?>
	<div class = "hdq_quiz" <?php if ($quiz_settings["quiz_timer"]["value"] > 3 && $use_adcode !== true) {
                echo 'style = "display:none;"';
            } ?>>
		<?php
            if ($quiz_settings["results_position"]["value"] != "below") {
                hdq_get_results($quiz_settings);
            }

        // Query through questions
        wp_reset_postdata();
        wp_reset_query();
        global $post;
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
        
        // WP_Query arguments
        $args = array(
                'post_type' => array('post_type_questionna'),
                'tax_query' => array(
                    array(
                        'taxonomy' => 'quiz',
                        'terms' => $quiz_ID,
                    ),
                ),
                'pagination' => $paginate, // true or false
                'posts_per_page' => $per_page, // also used for the pool of questions
                'paged' => $paged,
                'orderby' => $question_order, // defaults to menu_order
                'order' => 'ASC',
            );
        
        $query = new WP_Query($args);
        $i = 0; // question counter;
        
        // figure out the starting question number (for WP Pagination)
        $questionNumber = 0;
        if ($per_page >= 1 && $paged > 1) {
            $questionNumber = ($paged * $per_page) - $per_page + 1;
        }
        
        // The Loop
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $i++;
                $question_ID = get_the_ID();
                $question = get_hdq_question($question_ID);
                    
                if ($question["paginate"]["value"][0] === "yes") {
                    $jPaginate = true;
                    hdq_print_jPaginate($quiz_ID);
                }
                echo '<div class = "hdq_question" data-type = "'.$question["question_type"]["value"].'" id = "hdq_question_'.$question_ID.'">';
                    
                hdq_print_question_featured_image($question);
                    
                // deal with randomized answer order here,
                // so that you don't have to in your custom question type functions
                $ans_cor = hdq_get_question_answers($question["answers"]["value"], $question["selected"]["value"], $quiz_settings["randomize_answers"]["value"][0]);
                $question["answers"]["value"] = $ans_cor;
                
                if ($question["question_type"]["value"] === "multiple_choice_text") {
                    hdq_multiple_choice_text($question_ID, $i, $question, $quiz_settings);
                } elseif ($question["question_type"]["value"] === "multiple_choice_image") {
                    hdq_multiple_choice_image($question_ID, $i, $question, $quiz_settings);
                } elseif ($question["question_type"]["value"] === "text_based") {
                    hdq_text_based($question_ID, $i, $question, $quiz_settings);
                } elseif ($question["question_type"]["value"] === "title") {
                    $i = $i - 1; // don't count this as a question
                    hdq_title($question_ID, $i, $question, $quiz_settings);
                } else {
                    // TODO: Allow custom question types to be hookable
                    echo "Question type not found";
                }
                hdq_print_question_extra_text($question);
                echo '</div>';
                    
                if ($use_adcode) {
                    if ($i % 5 == 0 && $i != 0) {
                        echo $hdq_adcode;
                    }
                }
            }
        }
        
        wp_reset_postdata();
        
        
        if ($query->max_num_pages > 1 || $per_page != "-1") {
            if (isset($_GET['currentScore'])) {
                echo '<input type = "hidden" id = "hdq_current_score" value = "' . intval($_GET['currentScore']) . '"/>';
            }
            if (isset($_GET['totalQuestions'])) {
                echo '<input type = "hidden" id = "hdq_total_questions" value = "' . intval($_GET['totalQuestions']) . '"/>';
            }

            if ($quiz_settings["pool_of_questions"]["value"] == 0 || $quiz_settings["pool_of_questions"]["value"] == "") {
                if ($query->max_num_pages != $paged) {
                    hdq_print_next($quiz_ID, $paged);
                }
                    
                if ($query->max_num_pages == $paged) {
                    hdq_print_finish($quiz_ID, $jPaginate);
                }
            } else {
                hdq_print_finish($quiz_ID, $jPaginate);
            }
        } else {
            hdq_print_finish($quiz_ID, $jPaginate);
        }
        
        if ($quiz_settings["results_position"]["value"] == "below") {
            hdq_get_results($quiz_settings);
        } ?>		
	</div>
    <div class = "hdq_after">
    	<?php do_action("hdq_after", $quiz_ID); ?>
	</div>
    <div class = "hdq_loading_bar"></div>
</div>
<?php
    }
?>