<?php

namespace WPD\Toolset\Utilities;

use WPD\Toolset\Package;

final class Options extends Package
{
	/**
	 * Helper to get the path to an option
	 *
	 * @since 1.0.0
	 *
	 * @param $section
	 * @param $key
	 *
	 * @return string
	 */
	public static function getOptionPath( $section, $key )
	{
		return self::$config->wp_options[ $section ][ 'prefix' ] . self::$config->wp_options[ $section ][ 'options' ][ $key ][ 'name' ];
	}

	/**
	 * Return the default of an option
	 *
	 * @param $section
	 * @param $key
	 *
	 * @return mixed
	 */
	public static function getOptionDefault( $section, $key )
	{
		return self::$config->wp_options[ $section ][ 'options' ][ $key ][ 'default' ];
	}

	/**
	 * Get the option using prefix
	 *
	 * @param string $option
	 *
	 * @return string
	 */
	public static function getPrefixedOptionPath( $option )
	{
		return self::$config->wp_options[ 'settings' ][ 'prefix' ] . $option;
	}
}