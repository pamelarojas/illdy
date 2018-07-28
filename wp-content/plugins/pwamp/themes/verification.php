<?php
if ( !defined( 'ABSPATH' ) ) exit;  // Exit if accessed directly.

class PWAMP_Verification
{
	private $theme_list = array(
		'twentyfifteen' => true,
		'twentyseventeen' => true,
		'twentysixteen' => true
	);


	public function __construct()
	{
	}

	public function __destruct()
	{
	}


	public function verify_theme($data)
	{
		if ( !isset($data['theme']) || !is_string($data['theme']) )
		{
			return;
		}

		$theme = $data['theme'];

		if ( empty($theme) )
		{
			return;
		}

		if ( !empty($this->theme_list[$theme]) )
		{
			return 'yes';
		}
		else
		{
			return 'no';
		}
	}
}
