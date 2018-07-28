<?php

namespace WPD\Toolset\Utilities;

use Adbar\Dot;
use WPD\Toolset\Package;

final class Collections extends Package
{
	/**
	 * Returns an array value using a dot selector
	 *
	 * e.g getArrayByDot( $array, 'key.subKey' )
	 *
	 * @since   1.0.0
	 *
	 * @param   array       $array   Item to extract element from
	 * @param   string      $path    Array key or object property
	 * @param   null|string $default $default
	 *
	 * @return mixed A value from the array or object
	 */
	public function getArrayByDot($array, $path, $default = null)
	{
		if (!static::isAccessible($array)) {
			return $default;
		}

		if (is_null($path)) {
			return $array;
		}

		if (static::keyExists($array, $path)) {
			if (is_array($array)) {
				return $array[ $path ];
			}
			else if (is_object($array)) {
				return $array->$path;
			}
		}

		foreach (explode('.', $path) as $segment) {
			if (static::isAccessible($array) && static::keyExists($array, $segment)) {
				if (is_array($array)) {
					$array = $array[ $segment ];
				}
				else if (is_object($array)) {
					$array = $array->$segment;
				}
			}
			else {
				return $default;
			}
		}

		return $array;
	}

	/**
	 * @param $value
	 *
	 * @return bool
	 */
	public function isAccessible($value)
	{
		return is_array($value) || is_object($value) || $value instanceof \ArrayAccess;
	}

	/**
	 * @param $array
	 * @param $key
	 *
	 * @return bool
	 */
	public function keyExists($array, $key)
	{
		if ($array instanceof \ArrayAccess) {
			return $array->offsetExists($key);
		}

		if (is_object($array)) {
			return property_exists($array, $key);
		}

		return array_key_exists($key, $array);
	}

	/**
	 * Organize array of items by some item field
	 *
	 * @since   1.0.0
	 *
	 * @param   array $array       An array to sort
	 * @param   mixed $keyOrGetter A key or method name
	 *
	 * @return  array                   A sorted array
	 */
	public function organizeArrayByKey($array, $keyOrGetter)
	{
		$res   = [];
		$first = reset($array);

		if ($first) {
			if (is_object($first) && method_exists($first, $keyOrGetter)) {
				foreach ($array as $item) {
					$key         = call_user_func([$item, $keyOrGetter]);
					$res[ $key ] = $item;
				}
			}
			else {
				foreach ($array as $item) {
					$key         = self::getItem($item, $keyOrGetter);
					$res[ $key ] = $item;
				}
			}
		}

		return $res;
	}

	/**
	 * Returns object's property or array's element by key
	 * in case of absence returns default value
	 *
	 * @since   1.0.0
	 *
	 * @param   array|object $data         Item to extract element from
	 * @param   string       $key          Array key or object property
	 * @param   mixed        $defaultValue Default value if value isn't set
	 *
	 * @return  mixed                   A value from the array or object
	 */
	public function getItem($data, $key, $defaultValue = "")
	{
		$value = $defaultValue;

		if (is_object($data) && isset($data->$key)) {
			$value = $data->$key;
		}

		if (is_array($data) && isset($data[ $key ])) {
			$value = $data[ $key ];
		}

		return $value;
	}

	/**
	 * Get all array keys, and output them as a single string
	 *
	 * @since   1.0.0
	 *
	 * @param   $array array            The original array
	 *
	 * @return  string                  A string of keys
	 */
	public function implodeArrayKeys($array)
	{
		$imploded_string = null;

		foreach ($array as $key => $value) {
			$imploded_string .= $key . ' ';
		}

		return $imploded_string;
	}

	/**
	 * Inserts a new key/value before the key in the array.
	 *
	 * @param string $key       The key to insert before.
	 * @param array  $array     An array to insert in to.
	 * @param string $new_key   The key to insert.
	 * @param mixed  $new_value An value to insert.
	 *
	 * @return array|bool
	 */
	public function arrayInsertBefore($key, array &$array, $new_key, $new_value)
	{
		if (array_key_exists($key, $array)) {
			$new = [];

			foreach ($array as $k => $value) {
				if ($k === $key) {
					$new[ $new_key ] = $new_value;
				}
				$new[ $k ] = $value;
			}

			return $new;
		}

		return false;
	}

	/**
	 * Inserts a new key/value after the key in the array.
	 *
	 * @param string $key       The key to insert after.
	 * @param array  $array     An array to insert in to.
	 * @param string $new_key   The key to insert.
	 * @param mixed  $new_value An value to insert.
	 *
	 * @return array|bool
	 */
	public function arrayInsertAfter($key, array &$array, $new_key, $new_value)
	{
		if (array_key_exists($key, $array)) {
			$new = [];

			foreach ($array as $k => $value) {
				$new[ $k ] = $value;
				if ($k === $key) {
					$new[ $new_key ] = $new_value;
				}
			}

			return $new;
		}

		return false;
	}

	/**
	 * Get 2nd level array keys
	 *
	 * [
	 *      0 => [
	 *          'array_key_1' => [],
	 *          'array_key_2' => []
	 *      ],
	 *      1 => [
	 *          'array_key_3' => [],
	 *          'array_key_4' => []
	 *      ],
	 * ]
	 *
	 * @param array $array
	 *
	 * @return array|bool
	 */
	public function getL2Keys($array)
	{
		$result = [];

		foreach ($array as $sub) {
			$result = array_merge($result, $sub);
		}

		return array_keys($result);
	}

	/**
	 * Set an array item to a given value using "dot" notation.
	 *
	 * @param   array  $array
	 * @param   string $path
	 * @param   mixed  $value
	 *
	 * @return  array
	 */
	public function setArrayByDot(&$array, $path, $value)
	{
		$modified_array = new Dot();

		$modified_array->setReference($array);

		$modified_array->set($path, $value);

		return (array) $modified_array;
	}

	/**
	 * Remove 1 or more specified items from an array by key(s)
	 *
	 * @since   1.0.0
	 *
	 * @param   $array array            The original array
	 * @param   $keys  array             An array of keys to remove
	 *
	 * @return  array                   The original array, less the removed indexes
	 */
	public function removeItemByKey($array, $keys = [])
	{
		if (is_array($keys)) {
			foreach ($keys as $key) {
				unset($array[ $key ]);
			}
		}

		return $array;
	}

	/**
	 * Helper to get multiple given values from array
	 *
	 * @since 1.0.0
	 *
	 * @param array $array
	 * @param array $included_properties
	 *
	 * @return array
	 */
	public function pluckManyFromArray($array, $included_properties)
	{
		if (!is_array($array)
		    || !count($array)
		    || !is_array($included_properties)
		    || !count($included_properties)) {
			return [];
		}

		$new_array = [];

		foreach ($array as $key => $value) {
			if (in_array($key, $included_properties)) {
				$new_array[ $key ] = $value;
			}
		}

		return $new_array;
	}

	/**
	 * Helper to rename array keys.
	 *
	 * @since 1.0.0
	 *
	 * @param array $properties
	 * @param array $mappings Transformations to make inside $properties
	 *
	 * @return array
	 */
	public function transformArrayKeys($properties, $mappings)
	{
		if (!is_array($properties)
		    || !count($properties)
		    || !is_array($mappings)
		    || !count($mappings)) {
			return $properties;
		}

		foreach ($mappings as $old_property_name => $new_property_name) {
			if (isset($properties[ $old_property_name ])) {
				$properties[ $new_property_name ] = $properties[ $old_property_name ];
				unset($properties[ $old_property_name ]);
			}
		}

		return $properties;
	}
}