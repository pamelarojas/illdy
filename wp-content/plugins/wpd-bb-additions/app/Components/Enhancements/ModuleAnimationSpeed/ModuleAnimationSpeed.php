<?php

namespace WPD\BBAdditions\Components\Enhancements\ModuleAnimationSpeed;

use WPD\BBAdditions\Plugin;

/**
 * Class ModuleAnimationSpeed
 *
 * @package WPD\BBAdditions\Components\Enhancements\ModuleAnimationSpeed
 */
class ModuleAnimationSpeed
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
		add_filter('fl_builder_render_css', [__CLASS__, 'renderCustomCss'], 10, 4);
		add_action('wp_enqueue_scripts', [__CLASS__, 'enqueueScripts']);
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
			$form[ 'sections' ][ 'animation' ][ 'fields' ][ 'wpd_animation_duration' ] = [
				'type'        => 'text',
				'label'       => __('Animation Duration', Plugin::$config->plugin_text_domain),
				'help'        => __('Control the speed of the animation', Plugin::$config->plugin_text_domain),
				'placeholder' => '0.0',
				'description' => __('seconds', Plugin::$config->plugin_text_domain),
				'maxlength'   => '4',
				'size'        => '5',
				'preview'     => [
					'type' => 'none',
				],
			];
		}

		return $form;
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
	 * @param $global
	 *
	 * @return string $css
	 */
	public static function renderCustomCss($css, $nodes, $global_settings, $global)
	{
		foreach ($nodes[ 'modules' ] as $node) {
			if (!isset($node->settings->wpd_animation_duration)) {
				continue;
			}

			// @formatter:off
	        ob_start(); ?>

	        .fl-module.fl-animated.fl-node-<?= $node->node; ?> {
	            animation-duration: <?= $node->settings->wpd_animation_duration; ?>s;
	        }

	        <?php $css .= ob_get_clean();
	        // @formatter:on
		}

		return $css;
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

		$script = Plugin::url('app/Components/Enhancements/ModuleAnimationSpeed/resources/dist/js/scripts.js');
		wp_enqueue_script('wpd-module-animation-speed', $script, ['jquery']);
	}
}
