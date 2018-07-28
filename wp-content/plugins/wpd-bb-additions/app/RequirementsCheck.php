<?php

namespace WPD\BBAdditions;

use WPD\Toolset\Utilities\AdminNotice;

/**
 * Class RequirementsCheck
 *
 * @package WPD\BBAdditions
 */
final class RequirementsCheck
{

	/**
	 * Add admin notices
	 *
	 * @param $config
	 */
	public static function addAdminNotice($config)
	{
		// WP version notice
		if (version_compare(get_bloginfo('version'), $config->plugin_wp_minimum_version, '<')) {
			AdminNotice::add(wpautop(sprintf(__('%s requires at least WordPress %s+. You are running WordPress %s. Please upgrade and try again.', $config->plugin_text_domain), $config->plugin_name, $config->plugin_wp_minimum_version, get_bloginfo('version'))), 'error');
		}

		// PHP version notice
		if (version_compare(PHP_VERSION, $config->plugin_php_minimum_version, '<')) {
			AdminNotice::add(wpautop(sprintf(__('%s requires at least PHP %s+. You are running PHP %s. Please upgrade and try again.', $config->plugin_text_domain), $config->plugin_name, $config->plugin_php_minimum_version, PHP_VERSION)), 'error');
		}

		// BeaverBuilder availability check
		if (!class_exists('FLBuilder')) {
			// Beaver Builder not active
			AdminNotice::add(wpautop(sprintf(__('%s requires <a href="https://www.wpbeaverbuilder.com/" target="_blank">Beaver Builder Plugin</a>. Please install and activate it before continuing.', $config->plugin_text_domain), $config->plugin_name)), 'error');
		}
		else if (version_compare(FL_BUILDER_VERSION, Plugin::$config->plugin_bb_minimum_version, '<')) {
			// BB Plugin our of date
			AdminNotice::add(wpautop(sprintf(__('%s requires at least Beaver Builder %s+. You are running Beaver Builder %s. Please upgrade and try again.', Plugin::$config->plugin_text_domain), $config->plugin_name, $config->plugin_bb_minimum_version, FL_BUILDER_VERSION)), 'error');
		}
	}

	/**
	 * Check if installation have min requirements.
	 *
	 * @param $config
	 *
	 * @return bool
	 */
	public static function isCompatible($config)
	{
		$pass = true;

		// WP Version Check
		if (version_compare(get_bloginfo('version'), $config->plugin_wp_minimum_version, '<')) {
			$pass = false;
		}

		// PHP Check
		if (version_compare(PHP_VERSION, $config->plugin_php_minimum_version, '<')) {
			$pass = false;
		}

		// BB Check
		if (!class_exists('\FLBuilder') || (class_exists('\FLBuilder') && version_compare(FL_BUILDER_VERSION, $config->plugin_bb_minimum_version, '<'))) {
			$pass = false;
		}

		return $pass;
	}
}
