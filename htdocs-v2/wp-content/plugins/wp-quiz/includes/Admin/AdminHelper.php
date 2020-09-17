<?php
/**
 * Admin helpers
 *
 * @package WPQuiz
 */

namespace WPQuiz\Admin;

/**
 * Class Helpers
 */
class AdminHelper {

	/**
	 * Loads view.
	 *
	 * @param string $file_path View file path.
	 * @param array  $data      Data passed to view.
	 */
	public static function load_view( $file_path, array $data = array() ) {
		if ( ! file_exists( wp_quiz()->admin_dir() . 'views/' . $file_path ) ) {
			return;
		}
		// phpcs:ignore
		extract( $data );
		include wp_quiz()->admin_dir() . 'views/' . $file_path;
	}

	/**
	 * Gets list of animations in.
	 *
	 * @return array
	 */
	public static function get_animations_in() {
		return array(
			__( 'Attention Seekers', 'wp-quiz' )  => array(
				'bounce'     => __( 'bounce', 'wp-quiz' ),
				'flash'      => __( 'flash', 'wp-quiz' ),
				'pulse'      => __( 'pulse', 'wp-quiz' ),
				'rubberBand' => __( 'rubberBand', 'wp-quiz' ),
				'shake'      => __( 'shake', 'wp-quiz' ),
				'swing'      => __( 'swing', 'wp-quiz' ),
				'tada'       => __( 'tada', 'wp-quiz' ),
				'wobble'     => __( 'wobble', 'wp-quiz' ),
				'jello'      => __( 'jello', 'wp-quiz' ),
			),
			__( 'Bouncing Entrances', 'wp-quiz' ) => array(
				'bounceIn'      => __( 'bounceIn', 'wp-quiz' ),
				'bounceInDown'  => __( 'bounceInDown', 'wp-quiz' ),
				'bounceInLeft'  => __( 'bounceInLeft', 'wp-quiz' ),
				'bounceInRight' => __( 'bounceInRight', 'wp-quiz' ),
				'bounceInUp'    => __( 'bounceInUp', 'wp-quiz' ),
			),
			__( 'Fading Entrances', 'wp-quiz' )   => array(
				'fadeIn'         => __( 'fadeIn', 'wp-quiz' ),
				'fadeInDown'     => __( 'fadeInDown', 'wp-quiz' ),
				'fadeInDownBig'  => __( 'fadeInDownBig', 'wp-quiz' ),
				'fadeInLeft'     => __( 'fadeInLeft', 'wp-quiz' ),
				'fadeInLeftBig'  => __( 'fadeInLeftBig', 'wp-quiz' ),
				'fadeInRight'    => __( 'fadeInRight', 'wp-quiz' ),
				'fadeInRightBig' => __( 'fadeInRightBig', 'wp-quiz' ),
				'fadeInUp'       => __( 'fadeInUp', 'wp-quiz' ),
				'fadeInUpBig'    => __( 'fadeInUpBig', 'wp-quiz' ),
			),
			__( 'Flippers', 'wp-quiz' )           => array(
				'flip'    => __( 'flip', 'wp-quiz' ),
				'flipInX' => __( 'flipInX', 'wp-quiz' ),
				'flipInY' => __( 'flipInY', 'wp-quiz' ),
			),
			__( 'Lightspeed', 'wp-quiz' )         => array(
				'lightSpeedIn' => __( 'lightSpeedIn', 'wp-quiz' ),
			),
			__( 'Rotating Entrances', 'wp-quiz' ) => array(
				'rotateIn'          => __( 'rotateIn', 'wp-quiz' ),
				'rotateInDownLeft'  => __( 'rotateInDownLeft', 'wp-quiz' ),
				'rotateInDownRight' => __( 'rotateInDownRight', 'wp-quiz' ),
				'rotateInUpLeft'    => __( 'rotateInUpLeft', 'wp-quiz' ),
				'rotateInUpRight'   => __( 'rotateInUpRight', 'wp-quiz' ),
			),
			__( 'Sliding Entrances', 'wp-quiz' )  => array(
				'slideInUp'    => __( 'slideInUp', 'wp-quiz' ),
				'slideInDown'  => __( 'slideInDown', 'wp-quiz' ),
				'slideInLeft'  => __( 'slideInLeft', 'wp-quiz' ),
				'slideInRight' => __( 'slideInRight', 'wp-quiz' ),
			),
			__( 'Zoom Entrances', 'wp-quiz' )     => array(
				'zoomIn'      => __( 'zoomIn', 'wp-quiz' ),
				'zoomInDown'  => __( 'zoomInDown', 'wp-quiz' ),
				'zoomInLeft'  => __( 'zoomInLeft', 'wp-quiz' ),
				'zoomInRight' => __( 'zoomInRight', 'wp-quiz' ),
				'zoomInUp'    => __( 'zoomInUp', 'wp-quiz' ),
			),
			__( 'Specials', 'wp-quiz' )           => array(
				'jackInTheBox' => __( 'jackInTheBox', 'wp-quiz' ),
				'rollIn'       => __( 'rollIn', 'wp-quiz' ),
			),
		);
	}


	/**
	 * Gets list of animations out.
	 *
	 * @return array
	 */
	public static function get_animations_out() {
		return array(
			__( 'Attention Seekers', 'wp-quiz' ) => array(
				'bounce'     => __( 'bounce', 'wp-quiz' ),
				'flash'      => __( 'flash', 'wp-quiz' ),
				'pulse'      => __( 'pulse', 'wp-quiz' ),
				'rubberBand' => __( 'rubberBand', 'wp-quiz' ),
				'shake'      => __( 'shake', 'wp-quiz' ),
				'swing'      => __( 'swing', 'wp-quiz' ),
				'tada'       => __( 'tada', 'wp-quiz' ),
				'wobble'     => __( 'wobble', 'wp-quiz' ),
				'jello'      => __( 'jello', 'wp-quiz' ),
			),
			__( 'Bouncing Exits', 'wp-quiz' )    => array(
				'bounceOut'      => __( 'bounceOut', 'wp-quiz' ),
				'bounceOutDown'  => __( 'bounceOutDown', 'wp-quiz' ),
				'bounceOutLeft'  => __( 'bounceOutLeft', 'wp-quiz' ),
				'bounceOutRight' => __( 'bounceOutRight', 'wp-quiz' ),
				'bounceOutUp'    => __( 'bounceOutUp', 'wp-quiz' ),
			),
			__( 'Fading Exits', 'wp-quiz' )      => array(
				'fadeOut'         => __( 'fadeOut', 'wp-quiz' ),
				'fadeOutDown'     => __( 'fadeOutDown', 'wp-quiz' ),
				'fadeOutDownBig'  => __( 'fadeOutDownBig', 'wp-quiz' ),
				'fadeOutLeft'     => __( 'fadeOutLeft', 'wp-quiz' ),
				'fadeOutLeftBig'  => __( 'fadeOutLeftBig', 'wp-quiz' ),
				'fadeOutRight'    => __( 'fadeOutRight', 'wp-quiz' ),
				'fadeOutRightBig' => __( 'fadeOutRightBig', 'wp-quiz' ),
				'fadeOutUp'       => __( 'fadeOutUp', 'wp-quiz' ),
				'fadeOutUpBig'    => __( 'fadeOutUpBig', 'wp-quiz' ),
			),
			__( 'Flippers', 'wp-quiz' )          => array(
				'flip'     => __( 'flip', 'wp-quiz' ),
				'flipOutX' => __( 'flipOutX', 'wp-quiz' ),
				'flipOutY' => __( 'flipOutY', 'wp-quiz' ),
			),
			__( 'Lightspeed', 'wp-quiz' )        => array(
				'lightSpeedOut' => __( 'lightSpeedOut', 'wp-quiz' ),
			),
			__( 'Rotating Exits', 'wp-quiz' )    => array(
				'rotateOut'          => __( 'rotateOut', 'wp-quiz' ),
				'rotateOutDownLeft'  => __( 'rotateOutDownLeft', 'wp-quiz' ),
				'rotateOutDownRight' => __( 'rotateOutDownRight', 'wp-quiz' ),
				'rotateOutUpLeft'    => __( 'rotateOutUpLeft', 'wp-quiz' ),
				'rotateOutUpRight'   => __( 'rotateOutUpRight', 'wp-quiz' ),
			),
			__( 'Sliding Exits', 'wp-quiz' )     => array(
				'slideOutUp'    => __( 'slideOutUp', 'wp-quiz' ),
				'slideOutDown'  => __( 'slideOutDown', 'wp-quiz' ),
				'slideOutLeft'  => __( 'slideOutLeft', 'wp-quiz' ),
				'slideOutRight' => __( 'slideOutRight', 'wp-quiz' ),
			),
			__( 'Zoom Exits', 'wp-quiz' )        => array(
				'zoomOut'      => __( 'zoomOut', 'wp-quiz' ),
				'zoomOutDown'  => __( 'zoomOutDown', 'wp-quiz' ),
				'zoomOutLeft'  => __( 'zoomOutLeft', 'wp-quiz' ),
				'zoomOutRight' => __( 'zoomOutRight', 'wp-quiz' ),
				'zoomOutUp'    => __( 'zoomOutUp', 'wp-quiz' ),
			),
			__( 'Specials', 'wp-quiz' )          => array(
				'hinge'   => __( 'hinge', 'wp-quiz' ),
				'rollOut' => __( 'rollOut', 'wp-quiz' ),
			),
		);
	}
}
