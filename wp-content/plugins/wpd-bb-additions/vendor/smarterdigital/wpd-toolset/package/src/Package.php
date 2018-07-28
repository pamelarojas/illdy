<?php

namespace WPD\Toolset;

class Package
{
	/**
	 * @var array
	 */
	protected static $config = ['plugin_text_domain' => 'wpd-toolset'];

	/**
	 * @param array $config
	 */
	public function __construct(array $config = [])
	{
		$this->setConfig($config);
		$this->setup();
	}

	/**
	 * @param array $config
	 */
	protected function setup(array $config = []) {

	}

	/**
	 *
	 */
	public function getConfig()
	{
		return (object) self::$config;
	}

	/**
	 * @param array $config
	 */
	protected function setConfig(array $config = [])
	{
		$default      = (array) self::$config;
		self::$config = (object) array_merge($default, $config);
	}

	/**
	 * Magic method to check the loaded object property status.
	 *
	 * @param  string $key
	 *
	 * @return bool
	 */
//	public function __isset($key)
//	{
//		if (null === $this->__get($key)) {
//			return false;
//		}
//
//		return true;
//	}

	/**
	 * Magic method to get the loaded object property.
	 *
	 * @param  string $prop
	 *
	 * @return mixed
	 */
//	public function __get($prop)
//	{
//		if ($prop === 'config') {
//			if (isset(self::$prop) && is_array(self::$prop)) {
//				return (object) self::$prop;
//			}
//
//			return self::$prop;
//		}
//
//		return null;
//	}

	/**
	 * Temporary downgrade
	 *
	 * @return string
	 */
	public static function get_class() {
		return __CLASS__;
	}
}
