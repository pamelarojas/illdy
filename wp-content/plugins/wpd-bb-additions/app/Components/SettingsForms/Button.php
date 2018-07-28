<?php

namespace WPD\BBAdditions\Components\SettingsForms;

use FLBuilderModel;
use WPD\BBAdditions\Plugin;

/**
 * Class Button
 *
 * @package WPD\BBAdditions\Components\SettingsForms
 */
class Button {

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
		$this->setup();
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
	 * Add whitelabel filters
	 *
	 * @since   1.0.0
	 *
	 * @return  void
	 */
	protected function setup()
	{
		$this->registerForm();
	}

	/**
	 * Register custom form
	 *
	 * @since   1.0.0
	 */
	protected function registerForm()
	{
		\FLBuilder::register_settings_form('wpd_bb_button_form', $this->getForm());
	}

	/**
	 * Get the form
	 *
	 * @since   1.0.0
	 *
	 * @return array
	 */
	protected function getForm()
	{
		$tabs = isset(\FLBuilderModel::$modules[ 'button' ]) ? \FLBuilderModel::$modules[ 'button' ]->form : [];
		if ( array_key_exists( 'advanced', $tabs ) ) {
			unset( $tabs[ 'advanced' ] );
		}

		return [
			'title' => __( 'Add BB Button', Plugin::$config->plugin_text_domain ),
			'tabs'  => $tabs
		];

	}
}
