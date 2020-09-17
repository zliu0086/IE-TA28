<?php
/**
 * Admin REST routes
 *
 * @package WPQuiz
 */

namespace WPQuiz\REST;

use WP_REST_Response;
use WP_REST_Server;
use WP_REST_Request;
use WPQuiz\Admin\AdminPages\Support;
use WPQuiz\Importer;

/**
 * Class Admin
 */
class Admin extends REST {

	/**
	 * REST base.
	 *
	 * @var string
	 */
	protected $rest_base = 'admin/';

	/**
	 * Checks if user can use REST request.
	 *
	 * @return bool
	 */
	public function permission_callback() {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Registers REST routes.
	 */
	public function register_routes() {
		register_rest_route(
			self::REST_NAMESPACE,
			'environment-info',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_environment_info' ),
					'permission_callback' => array( $this, 'permission_callback' ),
				),
			)
		);

		register_rest_route(
			self::REST_NAMESPACE,
			$this->rest_base . 'import-quizzes',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'import_quizzes' ),
					'permission_callback' => array( $this, 'permission_callback' ),
				),
			)
		);

		register_rest_route(
			self::REST_NAMESPACE,
			$this->rest_base . 'import-quizzes-progress',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'import_quizzes_progress' ),
					'permission_callback' => array( $this, 'permission_callback' ),
				),
			)
		);
	}

	/**
	 * REST gets environment info.
	 *
	 * @param WP_REST_Request $request REST request.
	 * @return mixed|WP_REST_Response
	 */
	public function get_environment_info( WP_REST_Request $request ) {
		$support_page = new Support();
		return rest_ensure_response( $support_page->debug_data_output() );
	}

	/**
	 * REST imports quizzes.
	 *
	 * @param WP_REST_Request $request REST request.
	 * @return mixed|WP_REST_Response
	 */
	public function import_quizzes( WP_REST_Request $request ) {
		$importer        = new Importer();
		$download_images = $request->get_param( 'download_images' );
		$force_new       = $request->get_param( 'force_new' );
		$importer->set_download_images( $download_images && 'false' !== $download_images );
		$importer->set_author( get_current_user_id() );
		$importer->import_quizzes( $request->get_param( 'quizzes' ), $force_new && 'false' !== $force_new );
		return true;
	}

	/**
	 * REST gets import quizzes progress.
	 *
	 * @param WP_REST_Request $request REST request.
	 * @return mixed|WP_REST_Response
	 */
	public function import_quizzes_progress( WP_REST_Request $request ) {
		return rest_ensure_response(
			array(
				'remain' => wp_quiz()->import_process->get_remain(),
			)
		);
	}
}
