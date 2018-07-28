<?php

namespace WPD\BBAdditions;

/**
 * Plugin Name:     WPD Beaver Builder Additions
 * Plugin URI:      https://wpdevelopers.co.uk
 * Description:     A collection of useful modules, custom fields and settings for the Beaver Builder page builder.
 * Version:         2.0.5
 * Author:          Doug Belchamber
 * Author URI:      https://wpdevelopers.co.uk
 * License:         GNU General Public License v2
 * License URI:     https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:     wpd-bb-additions
 */

if (!defined('WPINC')) {
	die;
}

require_once __DIR__ . '/vendor/autoload.php';

/**
 * Run actions when plugin is activated
 *
 * @since 1.0.0
 *
 * @return void
 */
function registerHooks()
{
	register_activation_hook(__FILE__, __NAMESPACE__ . '\flushRewriteRules');
	register_activation_hook(__FILE__, __NAMESPACE__ . '\registerActivationOption');
	register_deactivation_hook(__FILE__, __NAMESPACE__ . '\flushRewriteRules');
	register_uninstall_hook(__FILE__, __NAMESPACE__ . '\flushRewriteRules');
}

/**
 * Flush rewrite rules
 *
 * @since 1.0.0
 *
 * @return void
 */
function flushRewriteRules()
{
	delete_option('rewrite_rules');
}

/**
 * Create a temporary option when plugin is activated.
 * This gets deleted on first activation
 *
 * @see   https://codex.wordpress.org/Function_Reference/register_activation_hook
 *
 * @since 1.0.0
 *
 * @return void
 */
function registerActivationOption()
{
	add_option(basename(__FILE__, '.php') . '-activated', true);
}

registerHooks();

/**
 * Boot plugin
 */
add_action('plugins_loaded', function () {
	Plugin::getInstance(__FILE__);

	/**
	 * Define constants
	 *
	 * @internal We have kept this named constant as this constant is used in
	 * WPD Beaver Popups to verify if this plugin is integrated or not.
	 *
	 * @source https://goo.gl/BWWp3c
	 */
	define( 'WPD_BB_ADDITIONS_PLUGIN_SLUG', Plugin::$config->plugin_slug );
});
