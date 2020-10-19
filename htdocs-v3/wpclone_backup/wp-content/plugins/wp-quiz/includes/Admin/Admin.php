<?php
/**
 * Plugin admin class
 *
 * @package WPQuiz
 */

namespace WPQuiz\Admin;

use WPQuiz\Admin\AdminPages\ImportExport;
use WPQuiz\Admin\AdminPages\NewQuiz;
use WPQuiz\Admin\AdminPages\Settings;
use WPQuiz\Admin\AdminPages\Support;
use WPQuiz\Admin\CMB2Custom\CustomFields;
use WPQuiz\Admin\MetaBoxes\QuizMetaBox;
use WPQuiz\Admin\MetaBoxes\QuizShortcodeMetaBox;

/**
 * Class Admin
 */
class Admin {

	/**
	 * Initializes admin functions.
	 */
	public function init() {
		$this->includes();

		// CMB2 custom fields.
		( new CustomFields() )->register();

		// Meta boxes.
		( new QuizMetaBox() )->init();
		( new QuizShortcodeMetaBox() )->register();

		// Settings pages.
		( new NewQuiz() )->init();

		( new Settings() )->init();
		( new ImportExport() )->init();

		$assets = new Assets();
		$assets->init();

		$editor_button = new EditorButtons();
		$editor_button->init();

		$this->hooks();
	}

	/**
	 * Adds hooks.
	 */
	protected function hooks() {
		add_filter( 'cmb2_localized_data', array( $this, 'cmb2_change_codemirror_defaults' ) );
	}

	/**
	 * Includes files.
	 */
	protected function includes() {
		require_once wp_quiz()->plugin_dir() . 'vendor/cmb2/cmb2/init.php';
		require_once wp_quiz()->libraries_dir() . 'cmb-field-select2/cmb-field-select2.php';
	}

	/**
	 * Changes codemirror defaults.
	 *
	 * @param array $l10n_data Codemirror defaults.
	 * @return mixed
	 */
	public function cmb2_change_codemirror_defaults( $l10n_data ) {
		$l10n_data['defaults']['code_editor']['codemirror']['direction'] = is_rtl() ? 'rtl' : 'ltr';
		return $l10n_data;
	}
}
