<?php

namespace WPD\BBAdditions\Components\Enhancements\RowEffectOnScroll;

use FLBuilderModel;
use WPD\BBAdditions\Plugin;

/**
 * Class RowEffectOnScroll
 *
 * @package WPD\BBAdditions\Enhancements
 */
class RowEffectOnScroll
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
		add_filter('fl_builder_register_settings_form', [__CLASS__, 'addSettingsForm'], 10, 2);
		add_filter('fl_builder_row_attributes', [__CLASS__, 'addModuleClassAttributes'], 10, 2);
		add_action('wp_enqueue_scripts', [__CLASS__, 'enqueueScripts']);
	}

	/**
	 * Add new style options on modules
	 *
	 * @since 1.0.0
	 *
	 * @param $form
	 * @param $id
	 *
	 * @return array $form
	 */
	public static function addSettingsForm($form, $id)
	{
		if ('row' === $id) {
			$form[ 'tabs' ][ 'wpd' ][ 'sections' ][ 'row_effects' ] = [
				'title'  => __('Row Effects', Plugin::$config->plugin_text_domain),
				'fields' => [
					'row_effect_on_scroll' => [
						'label'   => __('Apply row effect when you scroll', Plugin::$config->plugin_text_domain),
						'type'    => 'select',
						'options' => [
							'false'                   => __('Disabled', Plugin::$config->plugin_text_domain),
							'fade_out_on_scroll_down' => __('Fade out on scroll down', Plugin::$config->plugin_text_domain),
							'fade_in_on_scroll_down'  => __('Fade in on scroll down', Plugin::$config->plugin_text_domain),
						],
						'default' => 'false',
						'toggle'  => [
							'false'                   => [],
							'fade_out_on_scroll_down' => [
								'fields' => ['row_effect_bg_color'],
							],
							'fade_in_on_scroll_down'  => []
						]
					],
				],
			];
		}

		return $form;
	}

	/**
	 * @param $attrs
	 * @param $row
	 *
	 * @return mixed
	 */
	public static function addModuleClassAttributes($attrs, $row)
	{
		if (isset($row->settings->row_effect_on_scroll)) {
			if ('fade_out_on_scroll_down' == $row->settings->row_effect_on_scroll) {
				$attrs[ 'class' ][] = 'wpd-fade-out-on-scroll-down';
			}

			if ('fade_in_on_scroll_down' == $row->settings->row_effect_on_scroll) {
				$attrs[ 'class' ][] = 'wpd-fade-in-on-scroll-down';
			}
		}

		return $attrs;

	}

	/**
	 * Enqueue scripts
	 *
	 * @since   1.0.0
	 */
	public static function enqueueScripts()
	{
		if (FLBuilderModel::is_builder_active()) {
			return;
		}

		$script = Plugin::url('app/Components/Enhancements/RowEffectOnScroll/resources/dist/js/scripts.js');
		wp_enqueue_script('wpd-row-effect-on-scroll', $script, ['jquery']);
	}
}
