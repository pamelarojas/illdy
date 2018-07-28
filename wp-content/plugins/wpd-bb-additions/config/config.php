<?php

namespace WPD\BBAdditions;

use WPD\BBAdditions\Utils\General;

return (object) [
	'plugin_slug'                => $slug = 'wpd-bb-additions',
	'plugin_text_domain'         => $text_domain = $slug,
	'plugin_menu_name'           => __('WPD BB Additions', $text_domain),
	'plugin_name'                => 'WPD Beaver Builder Additions',
	'plugin_description'         => 'A collection of useful modules, custom fields and settings for the Beaver Builder page builder.',
	'plugin_website'             => 'https://wpdevelopers.co.uk',
	'plugin_author'              => 'Doug Belchamber',
	'plugin_author_uri'          => 'https://wpdevelopers.co.uk',
	'plugin_abbv'                => $abbv = General::getAbbreviationFromSlug($slug, 'wpd', '_'),
	'plugin_version'             => '2.0.5',
	'plugin_bb_minimum_version'  => '1.9',
	'plugin_wp_minimum_version'  => '4.7',
	'plugin_php_minimum_version' => '5.4',
	'plugin_theme_override_dir'  => trailingslashit(get_stylesheet_directory() . '/' . $slug),
	'wp_options_prefix'          => $options_prefix = __NAMESPACE__ . '\\',
	'wp_options'                 => [
		'settings' => [
			'prefix'  => $options_prefix . 'Settings\\',
			'options' => [
				'enhancements' => [
					'name'    => 'Enhancements',
					'default' => [
						'enabled' => true
					],
				]
			]
		]
	],
	'admin_page_uri'             => get_admin_url() . 'options-general.php?page=fl-builder-settings#wpd-bb-additions-admin-settings',
	'transient_prefix'           => $options_prefix . 'Transients\\',
	'meta_prefix'                => '_' . $abbv . '_'
];
