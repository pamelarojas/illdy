<?php

namespace WPD\BBAdditions\Components\Enhancements\MatchHeight;

/**
 * Class MatchHeight
 *
 * @package WPD\BBAdditions\Enhancements
 */
class MatchHeight
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
	 *
	 */
	protected function registerHooks()
	{
		add_action('wp_enqueue_scripts', [__CLASS__, 'registerExternalAssets']);
		add_filter('fl_builder_register_settings_form', [__CLASS__, 'addSettingsForm'], 10, 2);
		add_filter('fl_builder_module_attributes', [__CLASS__, 'addModuleAttributes'], 10, 2);
	}

	/**
	 * Enqueue assets from CDN
	 */
	public static function registerExternalAssets()
	{
		wp_enqueue_script('jquery-matchheight', '//cdnjs.cloudflare.com/ajax/libs/jquery.matchHeight/0.7.2/jquery.matchHeight-min.js', ['jquery'], null, true);
	}

	/**
	 * Add options to settings forms
	 *
	 * @param $form
	 * @param $id
	 *
	 * @return array $form
	 */
	public static function addSettingsForm($form, $id)
	{
		if ('module_advanced' == $id) {
			$form[ 'sections' ][ 'css_selectors' ][ 'fields' ][ 'match_height_group' ] = [
				'type'    => 'text',
				'label'   => __('Match height group', 'fl-builder'),
				'preview' => [
					'type' => 'none',
				],
			];
		}

		return $form;
	}

	/**
	 * @param $attrs
	 * @param $module
	 *
	 * @return mixed
	 */
	public static function addModuleAttributes($attrs, $module)
	{
		if (isset($module->settings->match_height_group) && !empty($module->settings->match_height_group)) {
			$attrs[ 'data-mh' ][] = sanitize_title_with_dashes($module->settings->match_height_group);
		}

		return $attrs;
	}
}
