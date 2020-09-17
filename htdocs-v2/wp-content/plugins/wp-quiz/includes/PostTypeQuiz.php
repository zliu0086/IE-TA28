<?php
/**
 * Post type Quiz
 *
 * @package WPQuiz
 */

namespace WPQuiz;

use WP_Post;
use WP_Screen;

/**
 * Class PostTypeQuiz
 */
class PostTypeQuiz {

	/**
	 * Gets quiz post type name.
	 *
	 * @return string
	 */
	public static function get_name() {
		return apply_filters( 'wp_quiz_post_type_name', 'wp_quiz' );
	}

	/**
	 * Gets quiz object from post.
	 *
	 * @param WP_Post|int $post Post object or post ID.
	 * @return Quiz|false
	 */
	public static function get_quiz( $post ) {
		if ( $post instanceof Quiz ) {
			return $post;
		}
		$post = get_post( $post );
		if ( ! $post ) {
			return false;
		}
		if ( self::get_name() !== $post->post_type ) {
			return false;
		}
		return new Quiz( $post );
	}

	/**
	 * Initializes.
	 */
	public function init() {
		add_action( 'init', array( $this, 'register' ) );
		add_filter( 'default_hidden_meta_boxes', array( $this, 'hide_meta_boxes' ), 10, 2 );
		add_filter( 'rank_math/frontend/description', array( $this, 'meta_desc' ) );

		if ( is_admin() ) {
			$post_type = self::get_name();
			add_filter( "manage_{$post_type}_posts_columns", array( $this, 'manage_columns' ) );
			add_action( "manage_{$post_type}_posts_custom_column", array( $this, 'manage_column_data' ), 10, 2 );
		}
	}

	/**
	 * Registers post types.
	 */
	public function register() {
		$slug = Helper::get_option( 'quiz_slug' );

		/**
		 * Allow changing quiz post type labels.
		 *
		 * @since 2.0.0
		 *
		 * @param array $labels Post type labels.
		 */
		$labels = apply_filters(
			'wp_quiz_post_type_labels',
			array(
				'name'               => __( 'Quizzes', 'wp-quiz' ),
				'singular_name'      => __( 'Quiz', 'wp-quiz' ),
				'add_new'            => _x( 'Add New Quiz', 'wp-quiz', 'wp-quiz' ),
				'add_new_item'       => __( 'Add New Quiz', 'wp-quiz' ),
				'edit_item'          => __( 'Edit Quiz', 'wp-quiz' ),
				'new_item'           => __( 'New Quiz', 'wp-quiz' ),
				'view_item'          => __( 'View Quiz', 'wp-quiz' ),
				'search_items'       => __( 'Search Quizzes', 'wp-quiz' ),
				'not_found'          => __( 'No Quizzes found', 'wp-quiz' ),
				'not_found_in_trash' => __( 'No Quizzes found in Trash', 'wp-quiz' ),
				'parent_item_colon'  => __( 'Parent Quiz:', 'wp-quiz' ),
				'menu_name'          => __( 'WP Quiz', 'wp-quiz' ),
				'all_items'          => __( 'All Quizzes', 'wp-quiz' ),
			)
		);

		/**
		 * Allow changing quiz post type args.
		 *
		 * @since 2.0.0
		 *
		 * @param array $args Post type args.
		 */
		$args = apply_filters(
			'wp_quiz_post_type_args',
			array(
				'labels'              => $labels,
				'hierarchical'        => false,
				'description'         => '',
				'taxonomies'          => array(),
				'public'              => true,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'show_in_admin_bar'   => true,
				'menu_position'       => null,
				'menu_icon'           => 'dashicons-editor-help',
				'show_in_nav_menus'   => true,
				'publicly_queryable'  => true,
				'exclude_from_search' => false,
				'has_archive'         => true,
				'query_var'           => true,
				'can_export'          => true,
				'rewrite'             => array(
					/**
					 * Allow changing quiz slug.
					 *
					 * @since 2.0.0
					 *
					 * @param string $slug Quiz slug.
					 */
					'slug' => apply_filters( 'wp_quiz_slug', $slug ),
				),
				'capability_type'     => 'post',
				'supports'            => apply_filters(
					'wp_quiz_post_type_supports',
					array(
						'title',
						'author',
						'thumbnail',
						'excerpt',
						'comments',
					)
				),
			)
		);

		register_post_type( self::get_name(), $args );

		if ( get_option( 'wp_quiz_old_slug' ) !== $slug ) {
			flush_rewrite_rules();
			update_option( 'wp_quiz_old_slug', $slug );
		}
	}

	/**
	 * Hides quiz meta boxes by default.
	 *
	 * @param array     $hidden List of hidden meta boxes.
	 * @param WP_Screen $screen WP Screen object.
	 * @return array
	 */
	public function hide_meta_boxes( $hidden, $screen ) {
		if ( self::get_name() === $screen->post_type ) {
			$hidden = array( 'postexcerpt', 'commentstatusdiv', 'commentsdiv', 'slugdiv', 'authordiv' );
		}
		return $hidden;
	}

	/**
	 * Manages quiz columns.
	 *
	 * @param array $columns Quiz columns.
	 * @return array
	 */
	public function manage_columns( $columns ) {
		$new_columns              = array();
		$new_columns['cb']        = '<input type="checkbox" />';
		$new_columns['title']     = esc_html__( 'Quiz Name', 'wp-quiz' );
		$new_columns['shortcode'] = esc_html__( 'Shortcode', 'wp-quiz' );
		$new_columns['type']      = esc_html__( 'Quiz type', 'wp-quiz' );
		$new_columns['date']      = esc_html__( 'Date', 'wp-quiz' );

		return $new_columns;
	}

	/**
	 * Manages column data.
	 *
	 * @param string $column  Column ID.
	 * @param int    $post_id Post ID.
	 */
	public function manage_column_data( $column, $post_id ) {
		$quiz = self::get_quiz( $post_id );

		switch ( $column ) {

			case 'shortcode':
				echo '<div class="field"><input type="text" readonly value="' . esc_html( '[wp_quiz id=&quot;' . $quiz->get_id() . '&quot;]' ) . '" onClick="this.select();" style="width:100%;"></div>';
				break;

			case 'type':
				if ( $quiz->get_quiz_type() ) {
					echo esc_html( $quiz->get_quiz_type()->get_title() );
				}
				break;
		}
	}

	/**
	 * Changes the meta description content.
	 *
	 * @param string $description Meta description content.
	 * @return string
	 */
	public function meta_desc( $description ) {
		if ( is_singular( self::get_name() ) ) {
			if ( 0 === strpos( $description, '{"' ) ) {
				return '';
			}
		}

		return $description;
	}
}
