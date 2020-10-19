<?php
/**
 * Database class
 *
 * @package WPQuiz
 */

namespace WPQuiz;

/**
 * Class DB
 */
class Install {

	/**
	 * Current plugin db version.
	 *
	 * @var string
	 */
	protected $db_version = '2.0.0.8';

	/**
	 * WPDB object.
	 *
	 * @var \wpdb
	 */
	protected $wpdb;

	/**
	 * List of migrate versions and methods.
	 *
	 * @var array
	 */
	protected $migrate_versions = array();

	/**
	 * DB constructor.
	 */
	public function __construct() {
		global $wpdb;
		$this->wpdb             = $wpdb;
		$this->migrate_versions = array(
			'2.0.0.8' => array( $this, 'migrate_2_0_0' ),
		);
	}

	/**
	 * Installs database.
	 */
	public function install() {
		$old_db_version = get_option( 'wp_quiz_db_version' );
		if ( version_compare( $old_db_version, $this->db_version ) >= 0 ) {
			// You are running latest db version.
			return;
		}

		$this->migrate( $old_db_version );

		update_option( 'wp_quiz_db_version', $this->db_version );
	}

	/**
	 * Creates tables.
	 */
	protected function create_tables() {
		$charset_collate = $this->wpdb->get_charset_collate();

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		// Play data table.
		$sql = "CREATE TABLE {$this->wpdb->prefix}wp_quiz_play_data (
			id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			player_id BIGINT(20) UNSIGNED NOT NULL,
			quiz_id BIGINT(20) UNSIGNED NOT NULL,
			played_at DATETIME NOT NULL,
			correct_answered SMALLINT(5) UNSIGNED NULL,
			result VARCHAR(255) NOT NULL,
			quiz_type VARCHAR(30) NOT NULL,
			quiz_data TEXT NOT NULL,
			answered_data TEXT NOT NULL,
			PRIMARY KEY  (id),
			KEY player_id (player_id),
			KEY quiz_id (quiz_id)
		) {$charset_collate};";

		/**
		 * Allows changing plays table schema.
		 *
		 * @since 2.0.0
		 *
		 * @param string $sql Table schema. See {@see dbDelta()}.
		 */
		$sql = apply_filters( 'wp_quiz_play_data_table_schema', $sql );
		dbDelta( $sql );
	}

	/**
	 * Migrates from the old version.
	 *
	 * @param string $old_db_version Old db version.
	 */
	protected function migrate( $old_db_version ) {
		$this->create_tables();

		// For those use 1.x.x version.
		$old_version = get_option( 'wp_quiz_version' );
		if ( $old_version ) {
			$old_db_version = $old_version;
		}

		if ( ! $old_db_version ) {
			// First time using plugin.
			return;
		}

		foreach ( $this->migrate_versions as $version => $method ) {
			if ( version_compare( $old_db_version, $version ) < 0 ) {
				call_user_func( $method );
				$old_db_version = $version;
			}
		}
	}

	/**
	 * Checks if table exists.
	 *
	 * @param string $table_name Table name.
	 * @return bool
	 */
	protected function check_table_exists( $table_name ) {
		return $this->wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" ) === $table_name; // WPCS: unprepared SQL ok.
	}

	/**
	 * Migrates data to version 2.0.0.
	 */
	protected function migrate_2_0_0() {
		$this->migrate_options_2_0_0();
	}

	/**
	 * Migrates options to version 2.0.0.
	 */
	protected function migrate_options_2_0_0() {
		$options = get_option( 'wp_quiz_default_settings' );
		if ( ! empty( $options['analytics'] ) && is_array( $options['analytics'] ) ) {
			foreach ( $options['analytics'] as $key => $value ) {
				$options[ 'ga_' . $key ] = $value;
			}
			unset( $options['analytics'] );
		}

		if ( ! empty( $options['defaults'] ) && is_array( $options['defaults'] ) ) {
			foreach ( $options as $key => $value ) {
				if ( in_array( $key, array( 'restart_questions', 'promote_plugin', 'auto_scroll', 'share_meta' ), true ) ) {
					$value = intval( $value ) ? 'on' : 'off';
				}
				$options[ $key ] = $value;
			}
			unset( $options['defaults'] );
		}

		// Add dummy value for new options.
		$new_options = array(
			'subscribe_box_user_consent',
			'subscribe_box_user_consent_desc',
		);

		$quiz_types = QuizTypeManager::get_all( true );
		foreach ( $quiz_types as $key => $quiz_type ) {
			$new_options[] = 'enable_' . $key;
		}

		$defaults = Helper::get_default_options();
		foreach ( $new_options as $key ) {
			if ( isset( $options[ $key ] ) || ! isset( $defaults[ $key ] ) ) {
				continue;
			}
			$options[ $key ] = $defaults[ $key ];
		}

		update_option( 'wp_quiz_default_settings', $options );
		unset( $options );
	}
}
