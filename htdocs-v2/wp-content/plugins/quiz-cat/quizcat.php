<?php
/*
	Plugin Name: Quiz Cat Free
	Plugin URI: https://fatcatapps.com/quiz-cat
	Description: Provides an easy way to create and administer quizes
	Text Domain: quiz-cat
	Domain Path: /languages
	Author: Fatcat Apps
	Author URI: https://fatcatapps.com/
	License: GPLv2
	Version: 1.8.0
*/


// BASIC SECURITY
defined( 'ABSPATH' ) or die( 'Unauthorized Access!' );



if ( !defined ('FCA_QC_PLUGIN_DIR') ) {
	
	// DEFINE SOME USEFUL CONSTANTS
	define( 'FCA_QC_DEBUG', FALSE );
	define( 'FCA_QC_PLUGIN_VER', '1.8.0' );
	define( 'FCA_QC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
	define( 'FCA_QC_PLUGINS_URL', plugins_url( '', __FILE__ ) );
	define( 'FCA_QC_PLUGINS_BASENAME', plugin_basename(__FILE__) );
	define( 'FCA_QC_PLUGIN_FILE', __FILE__ );
	define( 'FCA_QC_PLUGIN_PACKAGE', 'Free' ); //DONT CHANGE THIS, IT WONT ADD FEATURES, ONLY BREAKS UPDATER AND LICENSE
		
	include_once( FCA_QC_PLUGIN_DIR . '/includes/functions.php' );
	include_once( FCA_QC_PLUGIN_DIR . '/includes/post-type.php' );
	include_once( FCA_QC_PLUGIN_DIR . '/includes/quiz/quiz.php' );
	include_once( FCA_QC_PLUGIN_DIR . '/includes/editor/editor.php' );	
	include_once( FCA_QC_PLUGIN_DIR . '/includes/block.php' );
	
	if ( file_exists ( FCA_QC_PLUGIN_DIR . '/includes/editor/sidebar.php' ) ) {
		include_once( FCA_QC_PLUGIN_DIR . '/includes/editor/sidebar.php' );
	}	
	if ( file_exists ( FCA_QC_PLUGIN_DIR . '/includes/premium/premium.php' ) ) {
		include_once( FCA_QC_PLUGIN_DIR . '/includes/premium/premium.php' );
	}
	if ( file_exists ( FCA_QC_PLUGIN_DIR . '/includes/premium/optins.php' ) ) {
		include_once( FCA_QC_PLUGIN_DIR . '/includes/premium/optins.php' );
	}
	if ( file_exists ( FCA_QC_PLUGIN_DIR . '/includes/premium/licensing.php' ) ) {
		include_once( FCA_QC_PLUGIN_DIR . '/includes/premium/licensing.php' );
	}	
	if ( file_exists ( FCA_QC_PLUGIN_DIR . '/includes/premium/db.php' ) ) {
		include_once( FCA_QC_PLUGIN_DIR . '/includes/premium/db.php' );
	}
	if ( file_exists ( FCA_QC_PLUGIN_DIR . '/includes/stats/stats.php' ) ) {
		include_once( FCA_QC_PLUGIN_DIR . '/includes/stats/stats.php' );
	}
	if ( file_exists ( FCA_QC_PLUGIN_DIR . '/includes/upgrade.php' ) ) {
		include_once( FCA_QC_PLUGIN_DIR . '/includes/upgrade.php' );
	}	
	
	//FILTERABLE FRONT-END STRINGS
	$global_quiz_text_strings = array (
		'no_quiz_found' => __('No Quiz found', 'quiz-cat'),
		'correct' => __('Correct!', 'quiz-cat'),
		'wrong' => __('Wrong!', 'quiz-cat'),
		'your_answer' => __('Your answer:', 'quiz-cat'),
		'correct_answer' => __('Correct answer:', 'quiz-cat'),
		'question' => __('Question', 'quiz-cat'),
		'next' =>  __('Next', 'quiz-cat'),
		'you_got' =>  __('You got', 'quiz-cat'),
		'out_of' => __('out of', 'quiz-cat'),
		'your_answers' =>  __('Your Answers', 'quiz-cat'),
		'start_quiz' => __('Start Quiz', 'quiz-cat'),
		'retake_quiz' => __('Retake Quiz', 'quiz-cat'),
		'share_results' => __('SHARE YOUR RESULTS', 'quiz-cat'),
		'i_got' => __('I got', 'quiz-cat'),
		'skip_this_step' => __('Skip this step', 'quiz-cat'),
		'your_name' => __('Your Name', 'quiz-cat'),
		'your_email' => __('Your Email', 'quiz-cat'),
		'share'  => __('Share', 'quiz-cat'),
		'tweet'  =>  __('Tweet', 'quiz-cat'),
		'pin'  =>  __('Pin', 'quiz-cat'),
		'email'  =>  __('Email', 'quiz-cat') 
	);
	
	//ACTIVATION HOOK
	function fca_qc_activation() {
	
		$meta_version = get_option ( 'fca_qc_meta_version' );
		
		if ( function_exists( 'fca_qc_convert_csv') && version_compare( $meta_version, '1.5.0', '<' ) ) {
			//convert CSV from old format to new
			fca_qc_convert_csv();
					
		}
	}
	register_activation_hook( FCA_QC_PLUGIN_FILE, 'fca_qc_activation' );

	function fca_qc_add_plugin_action_links( $links ) {
		
		$support_url = FCA_QC_PLUGIN_PACKAGE === 'Free' ? 'https://wordpress.org/support/plugin/quiz-cat' : 'https://fatcatapps.com/support';
		
		$new_links = array(
			'support' => "<a target='_blank' href='$support_url' >" . __('Support', 'quiz-cat' ) . '</a>'
		);
		
		$links = array_merge( $new_links, $links );

		return $links;
		
	}
	add_filter( 'plugin_action_links_' . FCA_QC_PLUGINS_BASENAME, 'fca_qc_add_plugin_action_links' );

	/* Localization */
	function fca_qc_load_localization() {
		load_plugin_textdomain( 'quiz-cat', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}
	add_action( 'init', 'fca_qc_load_localization' );
	
	//DEACTIVATION SURVEY
	function fca_qc_admin_deactivation_survey( $hook ) {
		if ( $hook === 'plugins.php' ) {
			
			ob_start(); ?>
			
			<div id="fca-deactivate" style="position: fixed; left: 232px; top: 191px; border: 1px solid #979797; background-color: white; z-index: 9999; padding: 12px; max-width: 669px;">
				<p style="font-size: 14px; font-weight: bold; border-bottom: 1px solid #979797; padding-bottom: 8px; margin-top: 0;"><?php _e( 'Sorry to see you go', 'quiz-cat' ) ?></p>
				<p><?php _e( 'Hi, this is David, the creator of Quiz Cat. Thanks so much for giving my plugin a try. I’m sorry that you didn’t love it.', 'quiz-cat' ) ?>
				</p>
				<p><?php _e( 'I have a quick question that I hope you’ll answer to help us make Quiz Cat better: what made you deactivate?', 'quiz-cat' ) ?>
				</p>
				<p><?php _e( 'You can leave me a message below. I’d really appreciate it.', 'quiz-cat' ) ?>
				</p>
				
				<p><textarea style='width: 100%;' id='fca-qc-deactivate-textarea' placeholder='<?php _e( 'What made you deactivate?', 'quiz-cat' ) ?>'></textarea></p>
				
				<div style='float: right;' id='fca-deactivate-nav'>
					<button style='margin-right: 5px;' type='button' class='button button-secondary' id='fca-qc-deactivate-skip'><?php _e( 'Skip', 'quiz-cat' ) ?></button>
					<button type='button' class='button button-primary' id='fca-qc-deactivate-send'><?php _e( 'Send Feedback', 'quiz-cat' ) ?></button>
				</div>
			
			</div>
			
			<?php
				
			$html = ob_get_clean();
			
			$data = array(
				'html' => $html,
				'nonce' => wp_create_nonce( 'fca_qc_uninstall_nonce' ),
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
			);
						
			wp_enqueue_script('fca_qc_deactivation_js', FCA_QC_PLUGINS_URL . '/includes/deactivation.min.js', false, FCA_QC_PLUGIN_VER, true );
			wp_localize_script( 'fca_qc_deactivation_js', "fca_qc", $data );
		}
		
		
	}	
	add_action( 'admin_enqueue_scripts', 'fca_qc_admin_deactivation_survey' );
}