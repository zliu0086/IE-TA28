<!-- Quizzes Admin Page -->

<div id = "hdq_loading">
	<div id = "hdq_loading_inner">
		<!-- not sure if I like this -->
		<label>●</label>
		<label>●</label>
		<label>●</label>
		<label>●</label>
		<label>●</label>
		<label>●</label>
	</div>
</div>

<div id="main">
	<?php
	
		$wasUpgrade = sanitize_text_field(get_option("hdq_remove_data_upgrade_notice"));
	
		if($wasUpgrade === "yes"){	
			$taxonomy = 'quiz';
			$term_args = array(
				'hide_empty' => false,
				'orderby' => 'name',
				'order' => 'ASC',
			);
			$tax_terms = get_terms($taxonomy, $term_args);

			if (!empty($tax_terms) && !is_wp_error($tax_terms)) {            
				$quiz_id = $tax_terms[0]->term_id;
				$term_meta = get_option("taxonomy_term_$quiz_id");
				if (isset($term_meta) && $term_meta != null && $term_meta != "") {
					?>
						<div class = 'notice notice-success is-dismissible'>
							<p>Thank you for upgrading HD Quiz to v<?php echo HDQ_PLUGIN_VERSION;?>. <strong style = "font-weight: bold">This version requires updating your quiz and question data to be compatible with this version.</strong></p>
							<p>If you have less than 60 questions, this should have already been done automaticaly. However, if you have more than 60 questions, or if you are experiencing any issues with your quiz settings, please run the manual upgrade tool located <a href = "<?php echo get_admin_url(null, "?page=hdq_tools_data_upgrade"); ?>">here</a>.</p>
							<p>
								<span class = "hdq_marker">If you still have issues <strong style = "font-weight: bold">do not panik</strong></span>. None of your quiz data has been removed or changed, and you can always <a href = "https://wordpress.org/plugins/hd-quiz/advanced/" target = "_blank">revert to a previous version of HD Quiz</a>, and of course, you can always <a href = "http://harmonicdesign.ca/hd-quiz/" target = "_blank">contact me for help and support</a>.
							</p>
							<p style = "text-align:center">							
								<button class = "button" id = "hdq_update_data_notice" title = "dismiss this notification">
									OK, I understand - hide this notification
								</button>
							</p>
						</div>
					<?php
				}
			}
		}
	?>
    <div id="header">
        <h1 id="heading_title">
            HD Quiz - Quizzes
        </h1>
        <div id="header_actions">
			<a title = "delete or rename quizzes" href = "./edit-tags.php?taxonomy=quiz&post_type=post_type_questionna" class = "hdq_button_warning"><span class="dashicons dashicons-trash"></span> DELETE QUIZZES</a>
			<a title = "add to multiple quizzes, or delete multiple questions" href = "./edit.php?post_type=post_type_questionna" class = "hdq_button_warning">BULK MODIFY QUESTIONS</a>
		</div>
    </div>
    <div id="hdq_quizzes_page" class="content">
		<div id = "hdq_quiz_create_wrapper">
			<input type = "text" id = "hdq_new_quiz_name" class="input_enter" placeholder = "add new quiz"/>
			<p>Add a new quiz, or select a quiz below to add / edit questions, or change quiz settings.</p>		
		</div>
		<div id = "hdq_list_quizzes">
			<?php
            $taxonomy = 'quiz';
            $term_args = array(
                'hide_empty' => false,
                'orderby' => 'name',
                'order' => 'ASC',
            );
            $tax_terms = get_terms($taxonomy, $term_args);

            if (!empty($tax_terms) && !is_wp_error($tax_terms)) {
                foreach ($tax_terms as $tax_terms) {
                    ?>
							<div role = "button" class = "hdq_quiz_item hdq_quiz_term" data-name = "<?php echo $tax_terms->name; ?>" data-id = "<?php echo $tax_terms->term_id; ?>">
								<?php
                                    if (function_exists('mb_strimwidth')) {
                                        echo mb_strimwidth($tax_terms->name, 0, 50, "...");
                                    } else {
                                        echo $tax_terms->name;
                                    } ?> 
								<code>[HDquiz quiz = "<?php echo $tax_terms->term_id; ?>"]</code>
							</div>
			<?php
                }
            }
            ?>
		</div>
	</div>
	
	<p>If you have any questions or need support, please do not hesitate to ask on the <a href="https://wordpress.org/support/plugin/hd-quiz">official&nbsp;WordPress support page</a> or on our own&nbsp;<a href="http://harmonicdesign.ca/hd-quiz/" target="_blank" rel="noopener">support page at Harmonic Design</a>.</p>
	<p>
		<strong>This latest update (v1.8) was a major change to how quiz and question data is saved and used by HD Quiz</strong>. Because of this, you may have issues converting your quizzes/questions over to the new format. If you have issues, please use the <a href = "<?php echo get_admin_url(null, "?page=hdq_tools_data_upgrade"); ?>">upgrade tool</a> <span class="hdq_tooltip">
    ?
    <span class="hdq_tooltip_content">
        <span>The upgrade should have been automatic if you have less than 80 questions on your site</span>
    </span>
</span> or <a href = "http://harmonicdesign.ca/hd-quiz/" target = "_blank">contact me here</a>.</p>
	
<div class="hdq_highlight" id = "hd_patreon">
				<div id = "hd_patreon_icon">
					<img src = "<?php echo plugin_dir_url(__FILE__); ?>../images/hd_patreon.png" alt = "Donate"/>
				</div>
				<p>
					HD Quiz is a 100% free plugin developed in my spare time, and as such, I get paid in nothing but good will and positive reviews. If you are enjoying HD Quiz and would like to show your support, please consider contributing to my <a href="https://www.patreon.com/harmonic_design" target="_blank">patreon page</a> to help continued development. Every little bit helps, and I am fuelled by ☕.
				</p>
			</div>	
	
</div>

<div style = "display:none;">
<?php
// load editor so that tinymce is loaded
wp_editor("", "hdq_enqued_editor", array('textarea_name' => 'hdq_enqued_editor', 'teeny' => true, 'media_buttons' => false, 'textarea_rows' => 3, 'quicktags' => false));
// scripts and styles needed to use WP uploader
wp_enqueue_media();
?>
</div>