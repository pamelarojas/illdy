<?php

namespace WPD\BBAdditions\Utils;

use FLBuilderModel;

/**
 * Class General
 *
 * @package WPD\BBAdditions\Utils
 */
class General
{

	/**
	 * Enqueue the stylesheet for an icon.
	 *
	 * @since  1.4.6
	 * @access private
	 *
	 * @param string $icon The icon CSS classname.
	 *
	 * @return void
	 */
	public static function enqueueIconStyles($icon)
	{
		// Is this a core icon?
		if (stristr($icon, 'fa-')) {
			wp_enqueue_style('font-awesome');
		}
		else if (stristr($icon, 'fi-')) {
			wp_enqueue_style('foundation-icons');
		}
		else if (stristr($icon, 'dashicon')) {
			wp_enqueue_style('dashicons');
		}
		// It must be a custom icon.
		else {
			$sets = \FLBuilderIcons::get_sets();

			foreach ($sets as $key => $data) {
				if (in_array($icon, $data[ 'icons' ])) {
					self::enqueueIconStylesByKey($key);
				}
			}
		}
	}

	/**
	 * Enqueue the stylesheet for an icon set by key.
	 *
	 * @since  1.4.6
	 * @access private
	 *
	 * @param string $key The icon set key.
	 *
	 * @return void
	 */
	public static function enqueueIconStylesByKey($key)
	{
		if (apply_filters('fl_builder_enqueue_custom_styles_by_key', true, $key)) {
			$sets = \FLBuilderIcons::get_sets();

			if (isset($sets[ $key ])) {
				$set = $sets[ $key ];

				if ('icomoon' == $set[ 'type' ]) {
					wp_enqueue_style($key, $set[ 'stylesheet' ], [], FL_BUILDER_VERSION);
				}
				if ('fontello' == $set[ 'type' ]) {
					wp_enqueue_style($key, $set[ 'stylesheet' ], [], FL_BUILDER_VERSION);
				}
			}
		}
	}

	/**
	 * @param        $slug
	 * @param null   $prefix
	 * @param string $prefix_delimiter
	 *
	 * @return string
	 */
	public static function getAbbreviationFromSlug($slug, $prefix = null, $prefix_delimiter = '-')
	{
		$slug_array = explode('-', $slug);

		$abbv_array = array_map(function ($item) {
			return $item[ 0 ];
		}, $slug_array);

		$abbv = implode($abbv_array);

		if ($prefix) {
			return $prefix . $prefix_delimiter . $abbv;
		}

		return $abbv;
	}

	/**
	 * @param      $enhancementKey
	 * @param bool $checkbox
	 *
	 * @return bool|string
	 */
	public static function isEnhancementInactive($enhancementKey, $checkbox = false)
	{
		$inactiveEnhancements = FLBuilderModel::get_admin_settings_option('_wpd_inactive_enhancements')
			? FLBuilderModel::get_admin_settings_option( '_wpd_inactive_enhancements' )
			: [];

		if($checkbox) {
			return in_array( $enhancementKey, $inactiveEnhancements ) ? 'checked' : '';
		}

		return in_array( $enhancementKey, $inactiveEnhancements ) ? true : false;
	}
}
