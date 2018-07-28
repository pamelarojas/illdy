<?php

namespace WPD\BBAdditions;

use WPD\BBAdditions\Components\AdminSettings;
use WPD\BBAdditions\Components\CustomFields\GooglePlacesAutocomplete\GooglePlacesAutocomplete;
use WPD\BBAdditions\Components\CustomFields\ValueSlider\ValueSlider;
use WPD\BBAdditions\Components\Enhancements\MatchHeight\MatchHeight;
use WPD\BBAdditions\Components\Enhancements\ModuleAnimationPreviews\ModuleAnimationPreviews;
use WPD\BBAdditions\Components\Enhancements\ModuleAnimationSpeed\ModuleAnimationSpeed;
use WPD\BBAdditions\Components\SettingsForms\Button;
use WPD\BBAdditions\Components\SettingsForms\GoogleMapMarker;
use WPD\BBAdditions\Components\Enhancements\PolaroidPhoto\PolaroidPhoto;
use WPD\BBAdditions\Components\Enhancements\ModuleAnimations\ModuleAnimations;
use WPD\BBAdditions\Components\Enhancements\RowEffectOnScroll\RowEffectOnScroll;
use WPD\BBAdditions\Components\Enhancements\CollapsibleRows\CollapsibleRows;
use WPD\BBAdditions\Components\Enhancements\FeatureElement\FeatureElement;
use WPD\BBAdditions\Setup\Nodes\Row;
use WPD\Toolset\Utilities\Options;
use WPD\Toolset\Traits\PluginPropertyGetters;
use WPD\BBAdditions\Utils\General;
use WPD\Toolset\Features\WhiteLabel;

/**
 * Class Plugin
 *
 * @package WPD\BBAdditions
 */
class Plugin
{
	use PluginPropertyGetters;

	/**
	 * Plugin config
	 *
	 * @since   1.0.0
	 */
	public static $config;

	/**
	 * Plugin name extracted from the path
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	protected static $basename = '';

	/**
	 * Base plugin root dir
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	protected static $rootDir = '';

	/**
	 * Main plugin file
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	protected static $rootFile = '';

	/**
	 * Plugin slug
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	protected static $slug = '';

	/**
	 * Singleton instance
	 *
	 * @since   1.0.0
	 *
	 * @var     self null
	 */
	protected static $instance = null;

	/**
	 * Configurable Config
	 *
	 * @since   2.0.3
	 *
	 * @var     self null
	 */
	public static $configurableConfig = [];

	/**
	 * Plugin constructor.
	 *
	 * @since   1.0.0
	 */
	protected function __construct()
	{
		self::setConfig();
		self::registerModules();
		self::registerComponents();

		add_action('init', [__CLASS__, 'doPluginActivationSteps'], 0);
		add_action('init', [__CLASS__, 'loadTextDomain']);
		$this->doWhiteLabel();
	}

	/**
	 * Get singleton instance
	 *
	 * @since   1.0.0
	 *
	 * @param   string $rootFile The entry point file
	 *
	 * @return  mixed Plugin Instance of the plugin
	 */
	public static function getInstance($rootFile = '')
	{
		self::$rootFile = $rootFile;
		self::$rootDir  = realpath(dirname($rootFile));
		self::$basename = plugin_basename($rootFile);
		self::$slug     = dirname(self::$basename);

		/**
		 * Check the requirements of the plugin are met
		 */
		if (!RequirementsCheck::isCompatible(self::getConfig())) {
			RequirementsCheck::addAdminNotice(self::getConfig());

			return false;
		}

		/**
		 * New up an instance of Plugin
		 */
		if (!self::$instance) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Set plugin configuration
	 *
	 * @since   1.0.0
	 *
	 * @return  void
	 */
	public static function setConfig()
	{
		self::$config = self::getConfig();
	}

	/**
	 * Get plugin configuration
	 *
	 * @since   1.0.0
	 *
	 * @return  object
	 */
	public static function getConfig()
	{
		$config = include(realpath(dirname(self::$rootFile)) . '/config/config.php');

		return $config;
	}

	/**
	 * Load the text domain
	 *
	 * @since   1.0.0
	 *
	 * @return  void
	 */
	public static function loadTextDomain()
	{
		load_plugin_textdomain(self::$config->plugin_text_domain, false, self::$rootDir . '/languages');
	}

	/**
	 * Register filters and actions hooks
	 *
	 * @since   1.0.0
	 */
	public static function registerComponents()
	{
		AdminSettings::getInstance();

		// Beaver Builder node modifications
		Row::getInstance();

		// Enhancements
		if (!General::isEnhancementInactive('collapsible-rows')) {
			CollapsibleRows::getInstance();
		}

		if (!General::isEnhancementInactive('feature-element')) {
			FeatureElement::getInstance();
		}

		if (!General::isEnhancementInactive('module-animations')) {
			ModuleAnimations::getInstance();
		}

		if (!General::isEnhancementInactive('module-animation-previews')) {
			ModuleAnimationPreviews::getInstance();
		}

		if (!General::isEnhancementInactive('module-animation-speed')) {
			ModuleAnimationSpeed::getInstance();
		}

		if (!General::isEnhancementInactive('polaroid-photo')) {
			PolaroidPhoto::getInstance();
		}

		if (!General::isEnhancementInactive('row-effect-on-scroll')) {
			RowEffectOnScroll::getInstance();
		}

		if (!General::isEnhancementInactive('match-height')) {
			MatchHeight::getInstance();
		}

		// Fields
		GooglePlacesAutocomplete::getInstance();
		ValueSlider::getInstance();

		add_action('init', function () {
			Button::getInstance();
			GoogleMapMarker::getInstance();
		});
	}

	/**
	 * Register BB modules
	 *
	 * @since   2.0.1
	 *
	 * @return  void
	 */
	public static function registerModules()
	{
		add_action('init', function () {
			require_once 'Components/Modules/wpd-optimised-video/wpd-optimised-video.php';
			require_once 'Components/Modules/wpd-static-google-map/wpd-static-google-map.php';
		}, 11);
	}

	/**
	 * Register data with default values
	 *
	 * Fired only after activation of plugin
	 *
	 * @since   1.0.0
	 *
	 * @return  void
	 */
	public static function doPluginActivationSteps()
	{
		if (get_option(basename(self::$rootFile, '.php') . '-activated')) {
			delete_option(basename(self::$rootFile, '.php') . '-activated');

			self::registerOptions();

			if (is_admin()) {
				self::redirectAdminUserToPluginSettings();
			}
		}
	}

	/**
	 * Register options with default values
	 *
	 * @since   1.0.0
	 *
	 * @return  void
	 */
	public static function registerOptions()
	{
		foreach (Plugin::$config->wp_options as $option_section => $option_section_data) {
			foreach ($option_section_data[ 'options' ] as $option_key => $option_data) {
				$option = Options::getOptionPath($option_section, $option_key);
				$value  = Options::getOptionDefault($option_section, $option_key);

				add_option($option, $value);
			}
		}
	}

	/**
	 * Redirects user to plugin settings
	 *
	 * @since   1.0.0
	 *
	 * @return  void
	 */
	public static function redirectAdminUserToPluginSettings()
	{
		wp_redirect(Plugin::$config->admin_page_uri);
	}

	/**
	 * Get main plugin filename
	 *
	 * @since   1.0.0
	 *
	 * @return  string The filename
	 */
	public static function filename()
	{
		return self::$rootFile;
	}

	/**
	 * Returns root dir path
	 *
	 * @since   1.0.0
	 *
	 * @param   string $relPath Directory path to item, assuming the
	 *                          root is the current plugin dir
	 *
	 * @return  string The complete directory path
	 */
	public static function path($relPath = '')
	{
		return self::$rootDir . '/' . $relPath;
	}

	/**
	 * Returns root dir url
	 *
	 * @since   1.0.0
	 *
	 * @param   string $relPath Directory path to item, assuming the
	 *                          root is the current plugin dir
	 *
	 * @return  string The URL to the item
	 */
	public static function url($relPath = '')
	{
		return plugins_url($relPath, dirname(__FILE__));
	}

	/**
	 * Returns root dir of dist directory
	 *
	 * @since   1.0.0
	 *
	 * @param   string $path Directory path to item
	 *
	 * @return  string The path to a dist item
	 */
	public static function assetDistDir($path = null)
	{
		return self::path('resources/dist/' . $path);
	}

	/**
	 * Returns root dir of dist directory URL
	 *
	 * @since   1.0.0
	 *
	 * @param   string $path Directory path to item
	 *
	 * @return  string The URL to the dist path item
	 */
	public static function assetDistUri($path = null)
	{
		return self::url('resources/dist/' . $path);
	}

	/**
	 * Instantiate White Label Micro Module
	 * to allow user to white label the plugin
	 *
	 * Here is an example of how you can modify the plugin name:
	 *
	 * add_filter( 'wpd/bb-additions/config', function( $config ) {
	 *      $config->plugin_menu_name = 'My Plugin';
	 *      $config->plugin_description = 'My Plugin Description';
	 *
	 *      return $config;
	 * }, 10, 1 );
	 *
	 * @since 2.0.1
	 */
	public function doWhiteLabel()
	{
		new WhiteLabel(__CLASS__);
	}
}
