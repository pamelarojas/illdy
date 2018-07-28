<?php

namespace WPD\BBAdditions\Components\Modules;

use WPD\BBAdditions\Plugin;

/**
 * Class Base
 *
 * @package WPD\BBAdditions\Components\Modules
 */
class Base extends \FLBuilderModule
{

	/**
	 * Base constructor.
	 *
	 * @param array $params
	 */
	public function __construct($params = [])
	{
		parent::__construct([
			'name'            => $params[ 'name' ],
			'description'     => $params[ 'description' ],
			'category'        => $params[ 'category' ],
			'partial_refresh' => $params[ 'partial_refresh' ],
			'dir'             => Plugin::path('app/Components/Modules/' . $params[ 'slug' ] . '/'),
			'url'             => Plugin::url('app/Components/Modules/' . $params[ 'slug' ] . '/'),
			'slug'            => $params[ 'slug' ],
		]);
	}

	/**
	 * @param null|string $appended_string Append a string to the CSS class, joined by a hyphen
	 *
	 * @return string Module class name
	 */
	function getModuleClass($appended_string = null)
	{
		return isset($appended_string) ? $this->slug . '-' . $appended_string : $this->slug;
	}
}
