<?php

namespace WPD\Toolset\Utilities;

use WPD\Toolset\Package;
use Mexitek\PHPColors\Color as PhpColor;

final class Color extends Package
{
	/**
	 * Check if string is a hex colour
	 *
	 * @since   1.0.0
	 *
	 * @param   $string string                  A string
	 *
	 * @return  bool                            True if is hex
	 */
	public static function isHexColor($string)
	{
		return preg_match('/^#(?:[0-9a-fA-F]{3}){1,2}$/', $string);
	}

	/**
	 * Return a hex colour if 'rgba' isn't found in a colour string
	 *
	 * @since   1.0.0
	 *
	 * @param   $color string                   A color which could be a hex or rgba value
	 *
	 * @return  string                          A hex or rgba value
	 */
	public static function getRgbaOrHex($color)
	{
		$color = false === strpos($color, 'rgba') ? '#' . $color : $color;

		return $color;
	}

	/**
	 * Hex to RGB(A)
	 *
	 * @since   1.0.0
	 *
	 * @param   $color   string                 Hex value
	 * @param   $opacity int                    Opacity (0 - 1)
	 * @param   $wrap    bool                   Wrap the value in rgba()
	 *
	 * @return  string                          RGBA value
	 */
	public static function hexToRgba($color, $opacity = 0, $wrap = false)
	{
		if ('#' === $color[ 0 ]) {
			$color = substr($color, 1);
		}

		if (6 === strlen($color)) {
			list($r, $g, $b) = [
				$color[ 0 ] . $color[ 1 ],
				$color[ 2 ] . $color[ 3 ],
				$color[ 4 ] . $color[ 5 ]
			];
		}
		elseif (3 === strlen($color)) {
			list($r, $g, $b) = [
				$color[ 0 ] . $color[ 0 ],
				$color[ 1 ] . $color[ 1 ],
				$color[ 2 ] . $color[ 2 ]
			];
		}
		else {
			return false;
		}

		$r = hexdec($r);
		$g = hexdec($g);
		$b = hexdec($b);

		$rgba = $r . ',' . $g . ',' . $b . ',' . $opacity;

		if ($wrap) {
			$rgba = 'rgba(' . $rgba . ')';
		}

		return $rgba;
	}

	public static function removeAlphaFromRgba($color)
	{
		if (false !== strpos($color, 'rgba')) {
			$color_array = explode(',', $color);

			unset($color_array[ 3 ]);

			$color = implode(', ', $color_array) . ')';
			$color = str_replace('rgba', 'rgb', $color);
		}

		return $color;
	}

	/**
	 * Get Darkened color
	 *
	 * @param $color
	 * @param $amount
	 *
	 * @return string
	 * @throws Exception
	 */
	public function getDarkenedColor($color, $amount)
	{
		$color = (new PhpColor($color))->darken($amount);

		return $color;
	}

	/**
	 * Get Lightened color
	 *
	 * @param $color
	 * @param $amount
	 *
	 * @return string
	 * @throws Exception
	 */
	public function getLightenedColor($color, $amount)
	{
		$color = (new PhpColor($color))->lighten($amount);

		return $color;
	}
}
