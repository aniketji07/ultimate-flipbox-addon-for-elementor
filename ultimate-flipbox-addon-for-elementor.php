<?php
/*
* Plugin Name: Flipbox Addon for Elementor
* Plugin URI: https://github.com/Aniketji07/ultimate-flipbox-addon-for-elementor
* Description: Flipbox Addon for Elementor: Create interactive flipboxes with 5 animation styles, 4 preset styles, and customizable vertical grid and horizontal carousel layouts.
* Version: 2.0.1
* Author: aniketji007
* Author URI: https://github.com/aniketji007/
* Text Domain: ultimate-flipbox-addon-for-elementor
* License: GPL2
* Elementor tested up to:3.28.1
* Elementor Pro tested up to:3.28.1
*/

namespace UFAE;

if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

use UFAE\Includes\Ufae_Register;
use UFAE\Includes\Ufae_Ajax_Handler;
use UFAE\Admin\Feedback\UFAE_Feedback_Form;
use UFAE\Admin\Review\UFAE_Review_Form;

// Define constants only if they aren't already defined.
defined('UFAE_FILE') || define('UFAE_FILE', __FILE__);
defined('UFAE_VERSION') || define('UFAE_VERSION', '2.0.1');
defined('UFAE_DIR') || define('UFAE_DIR', plugin_dir_path(UFAE_FILE));
defined('UFAE_URL') || define('UFAE_URL', plugin_dir_url(UFAE_FILE));

// Ensure the class doesn't already exist.
if (! class_exists('Ultimate_Flipbox_Addon_For_Elementor')) {
	/**
	 * Ultimate_Flipbox_Addon_For_Elementor class handles the initialization and functionality of the Ultimate Flipbox Addon for Elementor plugin.
	 * It utilizes a singleton pattern for instantiation and registers hooks for plugin activation, deactivation, and file inclusion.
	 */
	class Ultimate_Flipbox_Addon_For_Elementor
	{

		/**
		 * The single instance of the Ultimate_Flipbox_Addon_For_Elementor class.
		 *
		 * @var Ultimate_Flipbox_Addon_For_Elementor|null The single instance of the class.
		 */
		private static $instance = null;

		/**
		 * Singleton pattern for class instantiation.
		 * Ensures only one instance of the class is created.
		 *
		 * @return Ultimate_Flipbox_Addon_For_Elementor The single instance of the class.
		 */
		public static function init()
		{
			if (self::$instance === null) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Constructor registers activation and deactivation hooks for the plugin.
		 * It also hooks into the 'plugins_loaded' action to include necessary files.
		 */
		public function __construct()
		{
			static $autoloade_status = false;

			if (! $autoloade_status) {
				$autoloade_status = spl_autoload_register([$this, 'autoload_files']);
			}

			// $this->register_widgets();

			$this->ufae_review();
			register_activation_hook(UFAE_FILE, array($this, 'plugin_activated'));
			register_deactivation_hook(UFAE_FILE, array($this, 'plugin_deactivated'));
			add_action( 'plugins_loaded', array( $this, 'required_classes' ) );
			add_action('admin_init', array($this, 'ufae_required_plugins'));
			add_action('init', array($this, 'ufafe_load_textdomain'));
		}

		private function autoload_files($class_name)
		{
			if (0 !== strpos($class_name, __NAMESPACE__)) {
				return;
			}

			$has_class_alias = isset($this->classes_aliases[$class_name]);

			// Backward Compatibility: Save old class name for set an alias after the new class is loaded
			if ($has_class_alias) {
				$class_alias_name = $this->classes_aliases[$class_name];
				$class_to_load = $class_alias_name;
			} else {
				$class_to_load = $class_name;
			}

			if (! class_exists($class_to_load)) {
				$filename = strtolower(
					preg_replace(
						['/^' . __NAMESPACE__ . '\\\/', '/([a-z])([A-Z])/', '/_/', '/\\\/'],
						['', '$1-$2', '-', DIRECTORY_SEPARATOR],
						$class_to_load
					)
				);

				$parts = explode('\\', $filename);

				if (count($parts) > 1) {
					$last_index = count($parts) - 1;
					$parts[$last_index] = 'class-' . $parts[$last_index];
				}

				$filename = implode('\\', $parts);

				
				$filename = trailingslashit(UFAE_DIR) . $filename . '.php';
				
				if (is_readable($filename)) {
					require_once $filename;
				}
			}

			if ($has_class_alias) {
				class_alias($class_alias_name, $class_name);
			}
		}

		private function register_widgets()
		{
			$initial_files = [
				'UfaeRegister'
			];

			foreach ($initial_files as $file_name) {
				$file_name = preg_replace('/(?<!^)([A-Z])/', '_$1', $file_name);

				$class_name = __NAMESPACE__ . '\Includes\\' . $file_name;

				if (class_exists($class_name)) {
					// Initialize the module by calling its singleton instance.
					$class_name::init();
				} else {
					// Optional: Log or debug if the module class isn't found.
					error_log('Module class not found or not active: ' . $class_name);
				}
			}
		}

		/**
		 * Initializes the review form functionality.
		 */
		public function ufae_review()
		{
			if (is_admin()) {
				$already_rated = get_option('ufae-already-reviewd', false);
				if (! $already_rated && class_exists('UFAE\Admin\Review\UFAE_Review_Form')) {
					UFAE_Review_Form::instance();
				}
			}
		}

		/**
		 * Checks if required plugins are active and displays an admin notice if not.
		 *
		 * This method verifies if the Elementor plugin is active. If it is not,
		 * an admin notice is displayed to inform the user that the Ultimate Flipbox
		 * Addon for Elementor requires Elementor to function properly.
		 *
		 * @return void
		 */
		public function ufae_required_plugins()
		{
			if (! is_plugin_active('elementor/elementor.php')) {
				add_action(
					'admin_notices',
					function () {
						echo '<div class="notice notice-error is-dismissible"><p>Flipbox Addon for Elementor requires Elementor to be installed and active. Please <a href="' . esc_url('https://wordpress.org/plugins/elementor/') . '" target="_blank">install Elementor</a> to use this plugin.</p></div>';
					}
				);
			}
		}

		/**
		 * Loads the translation files for the plugin.
		 * This function is hooked into the 'init' action to ensure the text domain is loaded early.
		 */
		public function ufafe_load_textdomain()
		{
			load_plugin_textdomain('ultimate-flipbox-addon-for-elementor', false, dirname(plugin_basename(__FILE__)) . '/languages');
		}

		/**
		 * Includes necessary files for the plugin based on the current context.
		 */
		public function required_classes()
		{
			// Include the class for registering plugin functionality
			if (is_admin()) {
				// Include the class for handling AJAX requests in the admin area
				if(class_exists('UFAE\Includes\Ufae_Ajax_Handler')){
					Ufae_Ajax_Handler::init();
				}

				// Include the class for handling feedback form data in the admin area
				if(class_exists('UFAE\Admin\Feedback\UFAE_Feedback_Form')){
					UFAE_Feedback_Form::get_instance();
				}
			}

			// Initialize the plugin registration
			if(class_exists('UFAE\Includes\Ufae_Register')){
				Ufae_Register::init();
			}
		}

		/**
		 * Placeholder for activation logic.
		 * This method is called when the plugin is activated.
		 * It updates options for installation date and plugin version.
		 */
		public function plugin_activated()
		{
			// Installation data
			update_option('ufafe_installation_date', gmdate('Y-m-d H:i:s'));
			// Plugin version
			update_option('ufafe_version', UFAE_VERSION);
		}

		/**
		 * Placeholder for deactivation logic.
		 * This method is called when the plugin is deactivated.
		 * It can be used to perform cleanup or other deactivation tasks.
		 */
		public function plugin_deactivated() {}
	}

	// Initialize the plugin.
	$UFAE = Ultimate_Flipbox_Addon_For_Elementor::init();
}
