<?php
/**
 * Class WPQuiz
 *
 * @package WPQuiz
 */

namespace WPQuiz;

use WPQuiz\Admin\Admin;
use WPQuiz\Modules\Subscription\Subscription;
use WPQuiz\PlayDataTracking\PlayData;
use WPQuiz\PlayDataTracking\PlayDataTracking;
use WPQuiz\Processes\ImportProcess;
use WPQuiz\Shortcodes\WPQuizListingShortcode;
use WPQuiz\Shortcodes\WPQuizProShortcode;

/**
 * Class WPQuiz
 */
class WPQuiz {

	/**
	 * REST namespace.
	 *
	 * @var string
	 */
	const REST_NAMESPACE = 'wp-quiz/v2';

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	public $version = '2.0.5';

	/**
	 * Plugin url.
	 *
	 * @var string
	 */
	private $plugin_url = '';

	/**
	 * Plugin path.
	 *
	 * @var string
	 */
	private $plugin_dir = '';

	/**
	 * Class instance.
	 *
	 * @var WPQuiz
	 */
	protected static $_instance;

	/**
	 * Import background process.
	 *
	 * @var ImportProcess
	 */
	public $import_process;

	/**
	 * Module manager.
	 *
	 * @var ModuleManager
	 */
	public $module_manager;

	/**
	 * Gets class instance.
	 *
	 * @return WPQuiz
	 */
	public static function get_instance() {
		if ( ! self::$_instance ) {
			self::$_instance = new self();
			self::$_instance->setup();
		}
		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cloning is forbidden.', 'wp-quiz' ), esc_html( $this->version ) );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Unserializing instances of this class is forbidden.', 'wp-quiz' ), esc_html( $this->version ) );
	}

	/**
	 * Class constructor.
	 */
	protected function __construct() {}

	/**
	 * Setups plugin.
	 */
	public function setup() {
		$quiz_type_classes = $this->get_quiz_type_classes();
		foreach ( $quiz_type_classes as $quiz_type_class ) {
			QuizTypeManager::add( new $quiz_type_class() );
		}

		$this->register_shortcodes();

		( new PostTypeQuiz() )->init();
		( new Assets() )->init();
		( new GDPR() )->init();
		( new ShowAnsweredQuiz() )->init();

		$play_data_tracking = new PlayDataTracking();
		$play_data_tracking->init();

		// REST init.
		new REST\REST();
		new REST\Admin();

		if ( is_admin() ) {
			( new Admin() )->init();
		}

		$this->module_manager = new ModuleManager();
		$this->module_manager->add( new Subscription() );

		$this->hooks();
	}

	/**
	 * Adds hooks.
	 */
	protected function hooks() {
		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
		add_action( 'wp_head', array( $this, 'wp_head' ), 1 );
		add_action( 'init', array( $this, 'embed_output' ) );
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
	}

	/**
	 * Activates plugin.
	 */
	public function activate() {
		$post_types = new PostTypeQuiz();
		$post_types->register();
		flush_rewrite_rules();

		$wq_upload_dir = wp_upload_dir();
		wp_mkdir_p( $wq_upload_dir['basedir'] . '/wp_quiz-import/' );
		wp_mkdir_p( $wq_upload_dir['basedir'] . '/wp_quiz-result-images/' );

		// phpcs:disable
		chmod( $wq_upload_dir['basedir'], 0755 );
		chmod( $wq_upload_dir['basedir'] . '/wp_quiz-import/', 0755 );
		chmod( $wq_upload_dir['basedir'] . '/wp_quiz-result-images/', 0755 );
		// phpcs:enable
	}

	/**
	 * Deactivates plugin.
	 */
	public function deactivate() {
		flush_rewrite_rules();
	}

	/**
	 * Loads plugin textdomain.
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'wp-quiz', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Gets quiz type classes.
	 *
	 * @return array
	 */
	protected function get_quiz_type_classes() {
		/**
		 * Allows adding new quiz types.
		 *
		 * @since 2.0.0
		 *
		 * @param array $classes An array of quiz type classes.
		 */
		return apply_filters(
			'wp_quiz_type_classes',
			array(
				'\\WPQuiz\\QuizTypes\\Trivia',
				'\\WPQuiz\\QuizTypes\\Personality',
				'\\WPQuiz\\QuizTypes\\Flip',
				'\\WPQuiz\\QuizTypes\\Swiper',
				'\\WPQuiz\\QuizTypes\\FBQuiz',
				'\\WPQuiz\\QuizTypes\\ListQuiz',
			)
		);
	}

	/**
	 * Registers shortcodes.
	 */
	protected function register_shortcodes() {
		( new WPQuizProShortcode() )->register();
		( new WPQuizListingShortcode() )->register();
	}

	/**
	 * Does something when all plugins are loaded.
	 */
	public function plugins_loaded() {
		$install = new Install();
		$install->install();
		$this->import_process = new ImportProcess();
	}

	/**
	 * Prints something inside <head> tag.
	 */
	public function wp_head() {
		// phpcs:disable
		if ( Helper::get_option( 'fb_app_id' ) ) : ?>
			<script>
				// Allow detecting when fb api is loaded.
				function Deferred() {
					var self = this;
					this.promise = new Promise( function( resolve, reject ) {
						self.reject  = reject;
						self.resolve = resolve;
					});
				}
				window.fbLoaded = new Deferred();

				window.fbAsyncInit = function() {
					FB.init({
						appId            : '<?php echo esc_js( Helper::get_option( 'fb_app_id' ) ); ?>',
						autoLogAppEvents : true,
						xfbml            : true,
						version          : 'v3.0'
					});

					window.fbLoaded.resolve();
				};

				(function(d, s, id){
					var js, fjs = d.getElementsByTagName(s)[0];
					if (d.getElementById(id)) {return;}
					js = d.createElement(s); js.id = id;
					js.src = "https://connect.facebook.net/en_US/sdk.js";
					fjs.parentNode.insertBefore(js, fjs);
				}(document, 'script', 'facebook-jssdk'));
			</script>
			<?php
		endif;
		// phpcs:enable
	}

	/**
	 * Prints embed output on embed page.
	 */
	public function embed_output() {
		if ( ! Helper::is_embed() ) {
			return;
		}
		$quiz = PostTypeQuiz::get_quiz( $_GET['wp_quiz_id'] ); // phpcs:ignore
		if ( ! $quiz ) {
			return;
		}
		Template::load_template( 'embed.php', compact( 'quiz' ) );
		die();
	}

	/**
	 * Get the plugin dir.
	 *
	 * @return string
	 */
	public function plugin_dir() {

		if ( ! $this->plugin_dir ) {
			$this->plugin_dir = trailingslashit( plugin_dir_path( WP_QUIZ_FILE ) );
		}

		return $this->plugin_dir;
	}

	/**
	 * Get the plugin url.
	 *
	 * @return string
	 */
	public function plugin_url() {

		if ( ! $this->plugin_url ) {
			$this->plugin_url = trailingslashit( plugin_dir_url( WP_QUIZ_FILE ) );
		}

		return $this->plugin_url;
	}

	/**
	 * Get plugin includes directory.
	 *
	 * @return string
	 */
	public function includes_dir() {
		return $this->plugin_dir() . 'includes/';
	}

	/**
	 * Get plugin templates directory.
	 *
	 * @return string
	 */
	public function templates_dir() {
		return $this->plugin_dir() . 'templates/';
	}

	/**
	 * Get plugin libraries directory.
	 *
	 * @return string
	 */
	public function libraries_dir() {
		return $this->includes_dir() . 'libs/';
	}

	/**
	 * Get assets url.
	 *
	 * @return string
	 */
	public function assets() {
		return $this->plugin_url() . 'assets/frontend/';
	}

	/**
	 * Get admin assets url.
	 *
	 * @return string
	 */
	public function admin_assets() {
		return $this->plugin_url() . 'assets/admin/';
	}

	/**
	 * Get plugin admin directory.
	 *
	 * @return string
	 */
	public function admin_dir() {
		return $this->plugin_dir() . 'includes/Admin/';
	}
}
