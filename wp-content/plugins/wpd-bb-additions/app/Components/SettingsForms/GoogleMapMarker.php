<?php

namespace WPD\BBAdditions\Components\SettingsForms;

use FLBuilder;
use WPD\BBAdditions\Plugin;

/**
 * Class GoogleMapMarker
 *
 * @package WPD\BBAdditions\Components\SettingsForms
 */
class GoogleMapMarker {

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
		FLBuilder::register_settings_form('wpd_google_map_marker', $this->getForm());
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
		return [
			'title' => __( 'Google Map marker', Plugin::$config->plugin_text_domain ),
			'tabs'  => [
				'general' => [
					'title'    => __( 'General', Plugin::$config->plugin_text_domain ),
					'sections' => [
						'marker_location' => [
							'title'  => __( 'Marker location', Plugin::$config->plugin_text_domain ),
							'fields' => [
								'marker_location' => [
									'type'  => 'wpd-google-places-autocomplete',
									'label' => __( 'Marker Location', Plugin::$config->plugin_text_domain ),
								],
							],
						],
						'marker_style'    => [
							'title'  => __( 'Marker style', Plugin::$config->plugin_text_domain ),
							'fields' => [
								'marker_type'         => [
									'type'    => 'select',
									'label'   => __( 'Marker type', Plugin::$config->plugin_text_domain ),
									'default' => 'pin',
									'options' => [
										'pin'          => __( 'Pin', Plugin::$config->plugin_text_domain ),
										'custom_image' => __( 'Custom image', Plugin::$config->plugin_text_domain ),
									],
									'toggle'  => [
										'pin'          => [
											'fields' => [ 'marker_color' ],
										],
										'custom_image' => [
											'fields' => [ 'marker_custom_image' ],
										],
									]
								],
								'marker_color'        => [
									'type'  => 'color',
									'label' => __( 'Marker colour', Plugin::$config->plugin_text_domain ),
								],
								'marker_custom_image' => [
									'type'  => 'photo',
									'label' => __( 'Custom marker image', Plugin::$config->plugin_text_domain ),
									'help'  => __( 'Must be a maximum of 64x64px in size, and GIF, PNG or JPG format', Plugin::$config->plugin_text_domain ),
								],
							],
						],
					]
				]
			]
		];
	}
}
