<?php

namespace WPD\BBAdditions\Setup\Nodes;

abstract class Node
{

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
		$this->registerHooks();
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
		static $instances = [];

		$called_class = get_called_class();

		if (!isset($instances[ $called_class ])) {
			$instances[ $called_class ] = new $called_class();
		}

		return $instances[ $called_class ];
	}

	/**
	 * Prevent cloning
	 */
	private function __clone() {}

	/**
	 * Require method
	 * @return void
	 */
	abstract protected function registerHooks();
}
