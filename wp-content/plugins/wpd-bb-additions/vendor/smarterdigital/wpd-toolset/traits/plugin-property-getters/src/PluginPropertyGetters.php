<?php

namespace WPD\Toolset\Traits;

trait PluginPropertyGetters {
	public static function getSlug($filtered = true)
	{
		return $filtered ? apply_filters(static::$slug . '/properties/plugin_slug', static::$slug) : static::$slug;
	}

	public static function getName($filtered = true)
	{
		return $filtered ? apply_filters(static::$slug . '/properties/plugin_name', static::$config->plugin_name) : static::$config->plugin_name;
	}

	public static function getDescription($filtered = true)
	{
		return $filtered ? apply_filters(static::$slug . '/properties/plugin_description', static::$config->plugin_description) : static::$config->plugin_description;
	}

	public static function getAuthor($filtered = true)
	{
		return $filtered ? apply_filters(static::$slug . '/properties/plugin_author', static::$config->plugin_author) : static::$config->plugin_author;
	}

	public static function getAuthorUri($filtered = true)
	{
		return $filtered ? apply_filters(static::$slug . '/properties/plugin_author_uri', static::$config->plugin_author_uri) : static::$config->plugin_author_uri;
	}

	public static function getWebsite($filtered = true)
	{
		return $filtered ? apply_filters(static::$slug . '/properties/plugin_website', static::$config->plugin_website) : static::$config->plugin_website;
	}

	public static function getBasename()
	{
		return static::$basename;
	}

	/**
	 * Temporary downgrade
	 *
	 * @return string
	 */
	public static function get_class() {
		return __CLASS__;
	}
}