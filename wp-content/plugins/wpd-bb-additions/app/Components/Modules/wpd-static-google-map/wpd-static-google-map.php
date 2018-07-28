<?php

namespace WPD\BBAdditions\Components\Modules\StaticMap;

use WPD\BBAdditions\Plugin;
use WPD\Toolset\Utilities\Color;
use WPD\BBAdditions\Components\Modules\Base;

/**
 * Class WPDStaticGoogleMap
 *
 * @package WPD\BBAdditions\Modules
 */
class WPDStaticGoogleMap extends Base
{

	/**
	 * WPDStaticGoogleMap constructor.
	 */
	public function __construct()
	{
		parent::__construct([
			'name'            => __('Static Google Map Embed', Plugin::$config->plugin_text_domain),
			'description'     => __('A static (image) Google map.', Plugin::$config->plugin_text_domain),
			'category'        => __('WPD Modules', Plugin::$config->plugin_text_domain),
			'group'           => __('WPD Modules', Plugin::$config->plugin_text_domain),
			'partial_refresh' => false,
			'slug'            => 'wpd-static-google-map'
		]);
	}

	/**
	 * Overriding the default Beaver Builder method which doesn't
	 * work yet
	 *
	 * @since 1.9.2
	 *
	 * @param null $icon
	 *
	 * @return bool|String
	 */
	public function get_icon($icon = null)
	{
		return file_exists(dirname(__FILE__) . '/icon.svg')
			? file_get_contents(dirname(__FILE__) . '/icon.svg')
			: $this->get_default_icon();
	}

	/**
	 * @param $settings
	 *
	 * @return mixed
	 */
	public function update($settings)
	{
		$styleSetMessage = 'Custom map style active. To reset, delete this text and save the module, or paste another style';

		if (isset($settings->map_style) && $styleSetMessage != $settings->map_style) {
			if (!is_array($settings->map_style)) {
				$settings->map_style = null;
			}
			else {
				$settings->map_style_array = $settings->map_style;
				$settings->map_style       = $styleSetMessage;
			}
		}

		if (empty($settings->map_style)) {
			$settings->map_style       = null;
			$settings->map_style_array = null;
		}

		if (!empty($settings->map_location)) {
			$settings->map_location_encoded = urlencode($settings->map_location);
			$settings->external_map_url     = '//www.google.com/maps/place/' . $settings->map_location_encoded;
		}

		return $settings;
	}

	/**
	 * @return string
	 */
	public function getGoogleMapUrlParameters()
	{
		$url_query = [
			'center' => isset($this->settings->map_location)
				? apply_filters('wpd/bb-additions/static-google-map/center-map-location', $this->settings->map_location)
				: '',
			'scale'  => 2,
			'zoom'   => isset($this->settings->map_zoom)
				? $this->settings->map_zoom
				: '15',
			'size'   => isset($this->settings->map_size)
				? $this->settings->map_size
				: '640x640',
			'format' => isset($this->settings->map_image_format)
				? $this->settings->map_image_format
				: 'jpg',
			'key'    => \FLBuilderModel::get_admin_settings_option('_wpd_google_static_map_api_key'),
		];

		return http_build_query($url_query);
	}

	/**
	 * @return bool|null|string
	 */
	public function getGoogleMapMarkersUrl()
	{
		if (empty($this->settings->markers)) {
			return null;
		}

		unset($marker_styles);
		$marker_styles = [];

		$i = 0;

		foreach ($this->settings->markers as $marker) {
			$marker_styles[ $i ] = [
				'size'    => isset($marker->marker_size) ? $marker->marker_size : 'normal',
				'markers' => [
					isset($marker->marker_location) ? $marker->marker_location : '',
				],
			];

			if (is_object($marker)) {
				if ('custom_image' == $marker->marker_type && isset($marker->marker_custom_image_src)) {
					$marker_styles[ $i ][ 'icon' ] = $marker->marker_custom_image_src;
				}
				else {
					$marker_styles[ $i ][ 'color' ] = isset($marker->marker_color) ? '0x' . $marker->marker_color : '';
				}
			}

			$i ++;
		}

		$marker_configs = [];
		$marker_url     = '';

		if (!empty($marker_styles)) {
			foreach ($marker_styles as $marker_style) {
				$marker_style_locations = implode('|', $marker_style[ 'markers' ]); // Gather the encoded URL locations for this specific marker style
				unset($marker_style[ 'markers' ]); // Remove this from the array as it doesn't conform to same format as other properties

				$marker_properties_formatted = [];

				foreach ($marker_style as $property => $value) {
					$marker_properties_formatted[] = $property . ':' . $value;
				}

				$marker_configs[] = implode('|', $marker_properties_formatted) . '|' . $marker_style_locations;
			}
		}

		if (!empty($marker_configs)) {
			foreach ($marker_configs as $marker_config) {
				$marker_url .= '&markers=' . urlencode($marker_config);
			}
		}

		if (isset($marker_url)) {
			return $marker_url;
		}

		return false;
	}

	/**
	 * @return array|null
	 */
	public function getJsonDecodedGoogleStaticMapStyles()
	{
		if (empty($this->settings->map_style_array)) {
			return null;
		}

		$styles = [];
		$i      = 0;

		foreach ($this->settings->map_style_array as $style) {
			if (isset($style->featureType)) {
				$styles[ $i ][ 'feature' ] = $style->featureType;
			}

			if (isset($style->elementType)) {
				$styles[ $i ][ 'element' ] = $style->elementType;
			}

			if (!empty($style->stylers)) {
				foreach ($style->stylers as $style) {
					$style = (array) $style;

					foreach ($style as $styleProperty => $styleValue) {
						$styles[ $i ][ $styleProperty ] = $styleValue;
					}
				}
			}

			$i ++;
		}

		return $styles;
	}

	/**
	 * @return null|string
	 */
	public function getGoogleStaticMapStylesUrl()
	{
		if (is_null($this->getJsonDecodedGoogleStaticMapStyles())) {
			return null;
		}

		$style_url = null;

		foreach ($this->getJsonDecodedGoogleStaticMapStyles() as $style) {

			$style_url  .= '&style=';
			$styleIndex = 0;
			$styleCount = count($style);

			foreach ($style as $styleProperty => $styleValue) {
				$styleValue = Color::isHexColor($styleValue) ? '0x' . str_replace('#', '', $styleValue) : $styleValue;

				$style_url .= $styleProperty . ':' . $styleValue;

				if ($styleIndex ++ < $styleCount - 1) {
					$style_url .= '|';
				}
			}
		}

		return $style_url;
	}

	/**
	 * @return string
	 */
	public function getGoogleStaticMapUrl()
	{
		return '//maps.googleapis.com/maps/api/staticmap?' . $this->getGoogleMapUrlParameters() . $this->getGoogleMapMarkersUrl() . $this->getGoogleStaticMapStylesUrl();
	}

	/**
	 * @return array
	 */
	public static function getGoogleMapZoomLevels()
	{
		$count = [];

		for ($i = 1; $i < 21; $i++) {
			$count[] = $i;
		}

		return $count;
	}
}

\FLBuilder::register_module( __NAMESPACE__ . '\WPDStaticGoogleMap', [
	'general' => [
		'title'    => __('General', Plugin::$config->plugin_text_domain),
		'sections' => [
			'map'     => [
				'title'  => __('Map', Plugin::$config->plugin_text_domain),
				'fields' => [
					'map_location'     => [
						'type'  => 'wpd-google-places-autocomplete',
						'label' => __('Center location', Plugin::$config->plugin_text_domain),
					],
					'map_zoom'         => [
						'type'    => 'select',
						'label'   => __('Zoom level', Plugin::$config->plugin_text_domain),
						'default' => '15',
						'options' => WPDStaticGoogleMap::getGoogleMapZoomLevels(),
					],
					'map_size'         => [
						'type'    => 'select',
						'label'   => __('Map size', Plugin::$config->plugin_text_domain),
						'default' => '640x640',
						'options' => [
							'640x640' => __('Square', Plugin::$config->plugin_text_domain),
							'640x320' => __('Horizontal Rectangle', Plugin::$config->plugin_text_domain),
							'320x640' => __('Vertical Rectangle', Plugin::$config->plugin_text_domain),
						],
					],
					'map_image_format' => [
						'type'    => 'select',
						'label'   => __('Map image format', Plugin::$config->plugin_text_domain),
						'default' => 'jpg',
						'options' => [
							'jpg' => __('JPEG', Plugin::$config->plugin_text_domain),
							'gif' => __('GIF', Plugin::$config->plugin_text_domain),
							'PNG' => __('PNG', Plugin::$config->plugin_text_domain),
						]
					],
					'map_style'        => [
						'label'       => __('Map style', Plugin::$config->plugin_text_domain),
						'type'        => 'textarea',
						'description' => __('Visit <a href="https://snazzymaps.com" target="_blank">SnazzyMaps</a> or <a href="http://www.mapstylr.com/"" target="_blank">MapStylr</a> to choose a style', Plugin::$config->plugin_text_domain),
					],
				],
			],
			'markers' => [
				'title'  => __('Markers', Plugin::$config->plugin_text_domain),
				'fields' => [
					'markers' => [
						'type'         => 'form',
						'label'        => __('Marker', Plugin::$config->plugin_text_domain),
						'form'         => 'wpd_google_map_marker',
						'preview_text' => __('Marker', Plugin::$config->plugin_text_domain),
						'multiple'     => true,
					],
				],
			],
		],
	],
]);
