<?php

namespace WPD\BBAdditions\Components\Enhancements\ModuleAnimationPreviews;
use WPD\BBAdditions\Plugin;

/**
 * Class ModuleAnimationPreviews
 *
 * @package WPD\BBAdditions\Components\Enhancements\ModuleAnimationPreviews
 */
class ModuleAnimationPreviews
{

	/**
	 * Singleton instance
	 *
	 * @since   1.0.0
	 *
	 * @var     self null
	 */
	protected static $instance = null;

	/**
	 * Plugin constructor.
	 *
	 * @since   1.0.0
	 */
	protected function __construct()
	{
		$this->registerHooks();
	}

	/**
	 * Get singleton instance
	 *
	 * @since   1.0.0
	 *
	 * @return  mixed Plugin Instance of the plugin
	 */
	public static function getInstance()
	{
		/**
		 * New up an instance of Plugin
		 */
		if (!self::$instance) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * @since 1.0.0
	 */
	protected function registerHooks()
	{
		add_action('wp_enqueue_scripts', [__CLASS__, 'enqueueScripts']);
	}

	/**
	 * Enqueue scripts
	 *
	 * @since   1.0.0
	 */
	public static function enqueueScripts()
	{
		if (!\FLBuilderModel::is_builder_active()) {
			return;
		}

		$script = Plugin::url('app/Components/Enhancements/ModuleAnimationPreviews/resources/dist/js/scripts.js');
		wp_enqueue_script('wpd-module-animation-previews', $script, ['jquery']);
	}
}

