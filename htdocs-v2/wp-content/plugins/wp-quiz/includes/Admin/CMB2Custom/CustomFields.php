<?php
/**
 * New fields for CMB2
 *
 * @package WPQuiz
 */

namespace WPQuiz\Admin\CMB2Custom;

use CMB2_Field;
use CMB2_Types;
use WPQuiz\PostTypeQuiz;
use WPQuiz\QuizType;
use WPQuiz\QuizTypeManager;

/**
 * Class CustomFields
 */
class CustomFields {

	/**
	 * Registers custom fields.
	 */
	public function register() {
		add_filter( 'cmb2_render_class_select_optgroup', array( $this, 'class_select_optgroup' ) );

		if ( ! has_action( 'cmb2_render_switch' ) ) {
			add_action( 'cmb2_render_switch', array( $this, 'render_switch' ), 10, 5 );
		}

		add_action( 'cmb2_render_quiz_content', array( $this, 'render_quiz_content' ), 10, 5 );

		add_action( 'cmb2_render_aweber', array( $this, 'render_aweber' ), 10, 5 );
	}

	/**
	 * Registers render class for select_optgroup type.
	 *
	 * @return string
	 */
	public function class_select_optgroup() {
		return '\\WPQuiz\\Admin\\CMB2Custom\\TypeSelectOptgroup';
	}

	/**
	 * Render switch field.
	 *
	 * @param CMB2_Field $field             The passed in `CMB2_Field` object.
	 * @param mixed      $escaped_value     The value of this field escaped.
	 *                                      It defaults to `sanitize_text_field`.
	 *                                      If you need the unescaped value, you can access it
	 *                                      via `$field->value()`.
	 * @param int        $object_id         The ID of the current object.
	 * @param string     $object_type       The type of object you are working with.
	 *                                      Most commonly, `post` (this applies to all post-types),
	 *                                      but could also be `comment`, `user` or `options-page`.
	 * @param object     $field_type_object This `CMB2_Types` object.
	 */
	public function render_switch( CMB2_Field $field, $escaped_value, $object_id, $object_type, $field_type_object ) {
		$field->args['options'] = array(
			'off' => esc_html( $field->get_string( 'off', __( 'Off', 'wp-quiz' ) ) ),
			'on'  => esc_html( $field->get_string( 'on', __( 'On', 'wp-quiz' ) ) ),
		);
		$field->set_options();

		echo $field_type_object->radio_inline(); // WPCS: xss ok.
	}

	/**
	 * Render quiz content field.
	 *
	 * @param CMB2_Field $field             The passed in `CMB2_Field` object.
	 * @param mixed      $escaped_value     The value of this field escaped.
	 *                                      It defaults to `sanitize_text_field`.
	 *                                      If you need the unescaped value, you can access it
	 *                                      via `$field->value()`.
	 * @param int        $object_id         The ID of the current object.
	 * @param string     $object_type       The type of object you are working with.
	 *                                      Most commonly, `post` (this applies to all post-types),
	 *                                      but could also be `comment`, `user` or `options-page`.
	 * @param object     $field_type_object This `CMB2_Types` object.
	 */
	public function render_quiz_content( CMB2_Field $field, $escaped_value, $object_id, $object_type, $field_type_object ) {
		if ( 'post' !== $object_type ) {
			return;
		}
		$quiz_type = $field->args( 'quiz_type' );
		if ( ! $quiz_type instanceof QuizType ) {
			$quiz_type = QuizTypeManager::get( $quiz_type );
		}
		if ( ! $quiz_type ) {
			return;
		}
		$quiz = PostTypeQuiz::get_quiz( $object_id );
		if ( ! $quiz ) {
			return;
		}
		$quiz->set_quiz_type( $quiz_type );
		printf( '<div id="%1$s" class="wp-quiz-content-settings">', esc_attr( $field->prop( 'id' ) ) );
		$field->args( 'quiz_type' )->backend( $quiz );
		printf( '</div><!-- End #%s -->', esc_attr( $field->prop( 'id' ) ) );
		$quiz_type->enqueue_backend_scripts();
	}

	/**
	 * Render aweber field.
	 *
	 * @param CMB2_Field $field             The passed in `CMB2_Field` object.
	 * @param mixed      $escaped_value     The value of this field escaped.
	 *                                      It defaults to `sanitize_text_field`.
	 *                                      If you need the unescaped value, you can access it
	 *                                      via `$field->value()`.
	 * @param int        $object_id         The ID of the current object.
	 * @param string     $object_type       The type of object you are working with.
	 *                                      Most commonly, `post` (this applies to all post-types),
	 *                                      but could also be `comment`, `user` or `options-page`.
	 * @param CMB2_Types $field_type_object This `CMB2_Types` object.
	 */
	public function render_aweber( CMB2_Field $field, $escaped_value, $object_id, $object_type, $field_type_object ) {
		$value = wp_parse_args(
			$escaped_value,
			array(
				'consumer_key'    => '',
				'consumer_secret' => '',
				'access_key'      => '',
				'access_secret'   => '',
				'account_id'      => '',
				'listid'          => '',
			)
		);

		$is_connected = true;
		foreach ( array( 'consumer_key', 'consumer_secret', 'access_key', 'access_secret', 'account_id' ) as $key ) {
			echo $field_type_object->input(
				array(
					'name'  => $field_type_object->_name( "[{$key}]" ),
					'id'    => $field_type_object->_id( '_' . $key ),
					'value' => $value[ $key ],
					'type'  => 'hidden',
					'class' => 'aweber-' . $key,
				)
			); // WPCS: xss ok.
			if ( ! $value[ $key ] ) {
				$is_connected = false;
			}
		}

		printf(
			'<div class="aweber-wrapper %1$s" data-option-id="%2$s">',
			$is_connected ? 'connected' : 'unconnected',
			$field->args( 'id' )
		); // WPCS: xss ok.

		echo '<div class="aweber-app-id-step">';
		echo $field_type_object->title(
			array(
				'name' => __( 'Enter your app ID then click Get auth code button.', 'wp-quiz' ),
			)
		); // WPCS: xss ok.
		echo '<p><input type="text" class="aweber-app-id" placeholder="' . esc_attr__( 'App ID', 'wp-quiz' ) . '"></p>';
		printf(
			'<p><a href="#" target="_blank" class="button button-primary aweber-get-auth-code-button">%s</a></p>',
			esc_html__( 'Get auth code', 'wp-quiz' )
		);
		echo '</div><!-- End .aweber-app-id-step -->';

		echo '<div class="aweber-auth-code-step">';
		echo $field_type_object->title(
			array(
				'name' => __( 'Copy and paste the authorization code you see after log in to your AWeber account.', 'wp-quiz' ),
			)
		); // WPCS: xss ok.
		echo '<p><input type="password" class="aweber-auth-code regular-text" placeholder="' . esc_attr__( 'Auth code', 'wp-quiz' ) . '"></p>';
		echo '<p><button type="button" class="button button-primary aweber-auth-button">' . esc_html__( 'Authorize', 'wp-quiz' ) . '</button></p>';
		echo '</div><!-- End .aweber-auth-code-step -->';

		echo '<div class="aweber-list-id-step">';
		$desc = sprintf(
			// translators: disconnect button.
			__( 'Your AWeber Account is connected. %s to disconnect.', 'wp-quiz' ),
			sprintf(
				'<a href="#" class="aweber-disconnect-button">%s</a>',
				__( 'Click here', 'wp-quiz' )
			)
		);
		echo '<p>' . wp_kses_post( $desc ) . '</p>';
		$id = $field_type_object->_id( '_listid' );
		echo '<p><label for="' . esc_attr( $id ) . '"><strong>' . esc_html__( 'List ID', 'wp-quiz' ) . '</strong></label></p>';
		echo '<p>';
		echo $field_type_object->input(
			array(
				'name'  => $field_type_object->_name( '[listid]' ),
				'id'    => $id,
				'type'  => 'text',
				'value' => $value['listid'],
			)
		); // WPCS: xss ok.
		echo '</p>';
		echo '</div><!-- End .aweber-list-id-step -->';

		echo '</div><!-- End .aweber-wrapper -->';
	}
}
