<?php

namespace WPD\BBAdditions\Components\Enhancements\ModuleAnimations;

use WPD\BBAdditions\Plugin;

/**
 * Class ModuleAnimations
 *
 * @package WPD\BBAdditions\Components\Enhancements\ModuleAnimations
 */
class ModuleAnimations
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
		add_filter('fl_builder_register_settings_form', [__CLASS__, 'addSettingsForm'], 10, 2);
		add_action('init', [__CLASS__, 'setupFrontendAssets']);
	}

	/**
	 * Add new style options on modules
	 *
	 * @since   1.0.0
	 *
	 * @param       $form
	 * @param mixed $id
	 *
	 * @return array $form
	 */
	public static function addSettingsForm($form, $id)
	{
		if ('module_advanced' === $id) {

			// Add new animations
			$new_module_animations = [
				'roll-left'       => __('Roll Left', Plugin::$config->plugin_text_domain),
				'roll-right'      => __('Roll Right', Plugin::$config->plugin_text_domain),
				'bounce-in'       => __('Bounce In', Plugin::$config->plugin_text_domain),
				'bounce-in-down'  => __('Bounce In Down', Plugin::$config->plugin_text_domain),
				'bounce-in-up'    => __('Bounce In Up', Plugin::$config->plugin_text_domain),
				'bounce-in-left'  => __('Bounce In Left', Plugin::$config->plugin_text_domain),
				'bounce-in-right' => __('Bounce In Right', Plugin::$config->plugin_text_domain),
				'fade-in-down'    => __('Fade In Down', Plugin::$config->plugin_text_domain),
				'fade-in-up'      => __('Fade In Up', Plugin::$config->plugin_text_domain),
				'fade-in-left'    => __('Fade In Left', Plugin::$config->plugin_text_domain),
				'fade-in-right'   => __('Fade In Right', Plugin::$config->plugin_text_domain),
				'flip-in-x'       => __('Flip In X', Plugin::$config->plugin_text_domain),
				'flip-in-y'       => __('Flip In Y', Plugin::$config->plugin_text_domain),
				'lightspeed-in'   => __('Lightspeed In', Plugin::$config->plugin_text_domain),
				'pulse'           => __('Pulse', Plugin::$config->plugin_text_domain),
				'flash'           => __('Flash', Plugin::$config->plugin_text_domain),
				'shake'           => __('Shake', Plugin::$config->plugin_text_domain),
				'tada'            => __('Tada', Plugin::$config->plugin_text_domain),
				'wiggle'          => __('Wiggle', Plugin::$config->plugin_text_domain),
				'wobble'          => __('Wobble', Plugin::$config->plugin_text_domain),
			];

			foreach ($new_module_animations as $animation => $animation_description) {
				$form[ 'sections' ][ 'animation' ][ 'fields' ][ 'animation' ][ 'options' ][ $animation ] = $animation_description;
			}

		}

		return $form;
	}

	/**
	 *
	 * Add custom CSS to main stylesheet
	 *
	 * @since   1.0.0
	 *
	 * @return string $css
	 */
	public static function renderCustomCss($css, $nodes, $global_settings)
	{
		if (file_exists(Plugin::$config->plugin_theme_override_dir . 'Enhancements/ModuleAnimations/css/style.css')) {
			$css_file = Plugin::$config->plugin_theme_override_dir . 'Enhancements/ModuleAnimations/css/style.css';
		}
		else {
			$css_file = __DIR__ . '/resources/dist/css/style.css';
		}

		$css_file = apply_filters('wpd/bb-additions/module-animations-css-file', $css_file);

		if (file_exists($css_file)) {
			$css .= file_get_contents($css_file);
		}

		return $css;
	}

	/**
	 * Setup assets for frontend
	 *
	 * @since 2.0.4
	 */
	public static function setupFrontendAssets()
	{
		if (!\FLBuilderModel::is_builder_active()) {
			add_filter('fl_builder_render_css', [__CLASS__, 'renderCustomCss'], 10, 3);
		}
	}
}

