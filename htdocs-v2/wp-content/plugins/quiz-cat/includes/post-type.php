<?php

////////////////////////////
// SET UP POST TYPE
////////////////////////////

//REGISTER CPT
function fca_qc_register_post_type() {
	
	$labels = array(
		'name' => _x('Quizzes','quiz-cat'),
		'singular_name' => _x('Quiz','quiz-cat'),
		'add_new' => _x('Add New','quiz-cat'),
		'all_items' => _x('All Quizzes','quiz-cat'),
		'add_new_item' => _x('Add New Quiz','quiz-cat'),
		'edit_item' => _x('Edit Quiz','quiz-cat'),
		'new_item' => _x('New Quiz','quiz-cat'),
		'view_item' => _x('View Quiz','quiz-cat'),
		'search_items' => _x('Search Quizzes','quiz-cat'),
		'not_found' => _x('Quiz not found','quiz-cat'),
		'not_found_in_trash' => _x('No Quizzes found in trash','quiz-cat'),
		'parent_item_colon' => _x('Parent Quiz:','quiz-cat'),
		'menu_name' => _x('Quiz Cat','quiz-cat')
	);
		
	$args = array(
		'labels' => $labels,
		'description' => "",
		'public' => false,
		'exclude_from_search' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'show_in_nav_menus' => false,
		'show_in_menu' => true,
		'show_in_admin_bar' => true,
		'menu_position' => 117,
		'menu_icon' => FCA_QC_PLUGINS_URL . '/assets/icon.png',
		'capability_type' => 'post',
		'hierarchical' => false,
		'supports' => array('title'),
		'has_archive' => false,
		'rewrite' => false,
		'query_var' => true,
		'can_export' => true
	);
	
	register_post_type( 'fca_qc_quiz', $args );
}
add_action ( 'init', 'fca_qc_register_post_type' );

//CHANGE CUSTOM 'UPDATED' MESSAGES FOR OUR CPT
function fca_qc_post_updated_messages( $messages ){
	
	$post = get_post();
	
	$messages['fca_qc_quiz'] = array(
		0  => '', // Unused. Messages start at index 1.
		1  => __( 'Quiz updated.','quiz-cat'),
		2  => __( 'Quiz updated.','quiz-cat'),
		3  => __( 'Quiz deleted.','quiz-cat'),
		4  => __( 'Quiz updated.','quiz-cat'),
		/* translators: %s: date and time of the revision */
		5  => isset( $_GET['revision'] ) ? sprintf( __( 'Quiz restored to revision from %s','quiz-cat'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		6  => __( 'Quiz published.' ,'quiz-cat'),
		7  => __( 'Quiz saved.' ,'quiz-cat'),
		8  => __( 'Quiz submitted.' ,'quiz-cat'),
		9  => sprintf(
			__( 'Quiz scheduled for: <strong>%1$s</strong>.','quiz-cat'),
			// translators: Publish box date format, see http://php.net/date
			date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) )
		),
		10 => __( 'Quiz draft updated.' ,'quiz-cat'),
	);

	return $messages;
}
add_filter('post_updated_messages', 'fca_qc_post_updated_messages' );

//Customize CPT table columns
function fca_qc_add_new_post_table_columns($columns) {
	$new_columns = array();
	$new_columns['cb'] = '<input type="checkbox" />';
	$new_columns['title'] = _x('Quiz Name', 'column name', 'quiz-cat');
	$new_columns['shortcode'] = __('Shortcode', 'quiz-cat');
	$new_columns['date'] = _x('Date', 'column name', 'quiz-cat');
 
	return $new_columns;
}
add_filter('manage_edit-fca_qc_quiz_columns', 'fca_qc_add_new_post_table_columns', 10, 1 );

function fca_qc_manage_post_table_columns($column_name, $id) {
	switch ($column_name) {
		case 'shortcode':
			echo '<input type="text" style="max-width: 100%" readonly="readonly" onclick="this.select()" value="[quiz-cat id=&quot;'. $id . '&quot;]"/>';
				break;
	 
		default:
		break;
	} // end switch
}
add_action('manage_fca_qc_quiz_posts_custom_column', 'fca_qc_manage_post_table_columns', 10, 2);

function fca_qc_remove_screen_options_tab ( $show_screen, $screen ) {
	if ( $screen->id == 'fca_qc_quiz' ) {
		return false;
	}
	return $show_screen;
}	
add_filter('screen_options_show_screen', 'fca_qc_remove_screen_options_tab', 10, 2);

function fca_qc_clone_quiz() {
	global $wpdb;
	
	$nonce = empty( $_GET['fca_sp_clone_nonce'] ) ? '' : sanitize_text_field( $_GET['fca_sp_clone_nonce'] );
	$to_dupe =  empty( $_GET['fca_sp_clone_id'] ) ? '' : intVal( $_GET['fca_sp_clone_id'] );
	
	if ( wp_verify_nonce( $nonce, 'fca_sp_clone_nonce' ) == false ) {		
		wp_die('Invalid nonce.  Please log in again.');
	}

	$post = get_post( $to_dupe );
	
	if ( empty( $post ) ) {
		wp_die( 'Post not found.' );
	}

	$post_id = $post->ID;

	$args = array(
		'post_title'     => $post->post_title . ' ' . __( 'Copy', 'quiz-cat' ),
		'comment_status' => $post->comment_status,
		'ping_status'    => $post->ping_status,
		'post_author'    => $post->post_author,
		'post_content'   => $post->post_content,
		'post_excerpt'   => $post->post_excerpt,
		'post_name'      => $post->post_name,
		'post_parent'    => $post->post_parent,
		'post_password'  => $post->post_password,
		'post_status'    => 'publish',
		'post_type'      => $post->post_type,
		'to_ping'        => $post->to_ping,
		'menu_order'     => $post->menu_order
	);

	$new_post_id = wp_insert_post( $args );
	
	//DUPLICATE POST META
	$post_meta_infos = $wpdb->get_results( "SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$post_id" );
	
	if ( count( $post_meta_infos ) != 0 ) {
		$sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
		foreach ( $post_meta_infos as $meta_info ) {
			$meta_key = $meta_info->meta_key;
			if( $meta_key == '_wp_old_slug' ) continue;
			$meta_value = addslashes( $meta_info->meta_value );
			$sql_query_sel []= "SELECT $new_post_id, '$meta_key', '$meta_value'";
		}
		$sql_query .= implode( " UNION ALL ", $sql_query_sel);
		$wpdb->query( $sql_query );
	}
	
	wp_redirect( admin_url( "post.php?action=edit&post=$new_post_id" ) );
	exit;

}
add_action( 'admin_action_fca_qc_clone_quiz', 'fca_qc_clone_quiz' );

function fca_qc_add_clone_post_link( $actions, $post ) {
	$post_type = empty( $post->post_type ) ? '' : $post->post_type;
	
	if ( $post_type === 'fca_qc_quiz' ) {
		$url = add_query_arg( array(
				'action' => 'fca_qc_clone_quiz',
				'fca_sp_clone_nonce' => wp_create_nonce( 'fca_sp_clone_nonce' ),
				'fca_sp_clone_id' => $post->ID,
			),
			admin_url( "admin.php" )
		);		
		$actions['duplicate'] = "<a href='$url'>" . __('Copy', 'quiz-cat' ) . "</a>";
	}
	return $actions;
}
 
add_filter( 'post_row_actions', 'fca_qc_add_clone_post_link', 10, 2 );