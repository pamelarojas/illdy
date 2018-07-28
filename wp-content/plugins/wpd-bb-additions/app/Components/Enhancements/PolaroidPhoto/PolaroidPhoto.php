<?php

namespace WPD\BBAdditions\Components\Enhancements\PolaroidPhoto;

use WPD\BBAdditions\Plugin;

/**
 * Class PolaroidPhoto
 *
 * @package WPD\BBAdditions\Components\Enhancements\PolaroidPhoto
 */
class PolaroidPhoto
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
	 * Register Hooks
	 *
	 * @since   1.0.0
	 */
	protected function registerHooks()
	{
		add_filter('fl_builder_register_settings_form', [__CLASS__, 'addSettingsForm'], 10, 2);
		add_filter('fl_builder_module_attributes', [__CLASS__, 'addModuleClassAttributes'], 10, 2);
		add_filter('fl_builder_render_css', [__CLASS__, 'renderCustomCss'], 10, 3);
	}

	/**
	 * Register custom form
	 *
	 * @since   1.0.0
	 *
	 * @param $form
	 * @param $slug
	 *
	 * @return array $form
	 */
	public static function addSettingsForm($form, $slug)
	{
		if ('photo' === $slug) {
			$form[ 'wpd' ] = [
				'title'    => __('WPD', Plugin::$config->plugin_text_domain),
				'sections' => [
					'style' => [
						'title'  => __('Style', Plugin::$config->plugin_text_domain),
						'fields' => [
							'photo_style' => [
								'type'    => 'select',
								'label'   => __('Photo Style', Plugin::$config->plugin_text_domain),
								'default' => 'regular',
								'options' => [
									'regular'  => __('Regular', Plugin::$config->plugin_text_domain),
									'polaroid' => __('Polaroid', Plugin::$config->plugin_text_domain),
								],
							],
						],
					],
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
	public static function addModuleClassAttributes($attrs, $module)
	{
		if ('photo' == $module->slug && isset($module->settings->photo_style)) {
			if ('regular' == $module->settings->photo_style) {
				$attrs[ 'class' ][] = 'photo--regular';
			}
			elseif ('polaroid' == $module->settings->photo_style) {
				$attrs[ 'class' ][] = 'photo--polaroid';
			}
		}

		return $attrs;

	}

	/**
	 *
	 * Add custom CSS to main stylesheet
	 *
	 * @since   1.0.0
	 *
	 * @param $css
	 * @param $nodes
	 * @param $global_settings
	 *
	 * @return string
	 */
	public static function renderCustomCss($css, $nodes, $global_settings)
	{
		$add_css = false;

		foreach ($nodes[ 'modules' ] as $module) {
			// Loop through the module nodes, available as the 2nd param in this filter
			if ('photo' == $module->slug && isset($module->settings->photo_style) && 'polaroid' == $module->settings->photo_style) { // Check a setting
				// Change the $add_css to true
				$add_css = true;
			}
		}

		if ($add_css) {
			// Either allow a theme override, or not
			if (file_exists(Plugin::$config->plugin_theme_override_dir . 'Enhancements/PolaroidPhoto/css/style.css')) {
				$css_file = Plugin::$config->plugin_theme_override_dir . 'Enhancements/PolaroidPhoto/css/style.css';
			}
			else {
				$css_file = __DIR__ . '/resources/dist/css/style.css';
			}

			// Or let someone else filter the file path
			$css_file = apply_filters('wpd/bb-additions/polaroid-photo-css-file', $css_file);

			// Add the contents of the file to the CSS variable (1st param of filter)
			if (file_exists($css_file)) {
				$css .= file_get_contents($css_file);
			}
		}

		// Return it
		return $css;
	}
}
