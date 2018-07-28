<?php
if ( !defined( 'ABSPATH' ) ) exit;  // Exit if accessed directly.

class PWAMP_ThemeCom
{
	public function __construct()
	{
	}

	public function __destruct()
	{
	}


	private function retrofit_media(&$amp_style, $viewport_width)
	{
		if ( $viewport_width == 0 )
		{
			return;
		}

		preg_match_all('~@media\b([^{]+)\{([\s\S]+?\})\s*\}~', $amp_style, $matches);
		foreach ( $matches[1] as $key => $value )
		{
			unset($min_width);
			if ( preg_match('~min-width:\s?(\d+)px~i', $value, $matches2) )
			{
				$min_width = intval($matches2[1]);
			}
			elseif ( preg_match('~min-width:\s?(\d+(\.\d+)?)em~i', $value, $matches2) )
			{
				$min_width = intval($matches2[1]) * 16;
			}

			unset($max_width);
			if ( preg_match('~max-width:\s?(\d+)px~i', $value, $matches2) )
			{
				$max_width = intval($matches2[1]);
			}
			elseif ( preg_match('~max-width:\s?(\d+(\.\d+)?)em~i', $value, $matches2) )
			{
				$max_width = intval($matches2[1]) * 16;
			}

			$value = str_replace(array('(', ')', '.'), array('\(', '\)', '\.'), $value);
			if ( isset($min_width) && isset($max_width) )
			{
				if ( $viewport_width < $min_width || $viewport_width > $max_width )
				{
					$amp_style = preg_replace('~@media\b' . $value . '\{([\s\S]+?\})\s*\}~', '', $amp_style, 1);
				}
				else
				{
					$amp_style = preg_replace('~@media\b' . $value . '\{([\s\S]+?\})\s*\}~', $matches[2][$key], $amp_style, 1);
				}
			}
			elseif ( isset($min_width) )
			{
				if ( $viewport_width < $min_width )
				{
					$amp_style = preg_replace('~@media\b' . $value . '\{([\s\S]+?\})\s*\}~', '', $amp_style, 1);
				}
				else
				{
					$amp_style = preg_replace('~@media\b' . $value . '\{([\s\S]+?\})\s*\}~', $matches[2][$key], $amp_style, 1);
				}
			}
			elseif ( isset($max_width) )
			{
				if ( $viewport_width > $max_width )
				{
					$amp_style = preg_replace('~@media\b' . $value . '\{([\s\S]+?\})\s*\}~', '', $amp_style, 1);
				}
				else
				{
					$amp_style = preg_replace('~@media\b' . $value . '\{([\s\S]+?\})\s*\}~', $matches[2][$key], $amp_style, 1);
				}
			}
			else
			{
				$amp_style = preg_replace('~@media\b' . $value . '\{([\s\S]+?\})\s*\}~', '', $amp_style, 1);
			}
		}
	}


	protected function minify_css($style, $id = '')
	{
		$css = !empty($id) ? $id . '{' . $style . '}' : $style;

		$css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
		$css = preg_replace('~[\s\t]*!important;?~i', ';', $css);
		$css = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $css);
		$css = str_replace(array(' {', ': ', ', ', '; ', ';}'), array('{', ':', ',', ';', '}'), $css);

		if ( preg_match('~{}$~im', $css) )
		{
			return;
		}

		return $css;
	}


	protected function transcode_html(&$page, &$amp_style, $home_url, $permalink)
	{
		// Remove HTML comments.
		$page = preg_replace('~<!--.*-->~isU', '', $page);

		$page = preg_replace('~^<!DOCTYPE\b([^>]+)>$~im', '<!doctype${1}>', $page, 1);

		// The attribute 'onclick' may not appear in tag 'a'.
		$page = preg_replace('~<a\b([^>]*)onclick=\'[^\']+\'([^>]*)>~i', '<a${1}${2}>', $page);

		// The tag 'audio' may only appear as a descendant of tag 'noscript'. Did you mean 'amp-audio'?
		$page = preg_replace('~<audio\b~i', '<amp-audio', $page);

		$serviceworker = '<amp-install-serviceworker
	src="' . $home_url . '/' . ( $permalink == 'pretty' ? '' : '?' ) . 'pwamp-sw-js"
	data-iframe-src="' . $home_url . '/' . ( $permalink == 'pretty' ? '' : '?' ) . 'pwamp-sw-html"
	layout="nodisplay">
</amp-install-serviceworker>' . "\n";
		$serviceworker .= '<amp-pixel src="' . $home_url . '/?pwamp-viewport-width=VIEWPORT_WIDTH" layout="nodisplay"></amp-pixel>' . "\n";
		$serviceworker .= '</body>';
		$page = preg_replace('~^</body>~im', $serviceworker, $page, 1);

		// The attribute 'action' may not appear in tag 'FORM [method=POST]'.
		$page = preg_replace('~<form action=([^>]+)>~i', '<form action-xhr=${1}>', $page);

		// The mandatory attribute 'target' is missing in tag 'FORM [method=GET]'.
		$page = preg_replace('~<form\b([^>]+)>~i', '<form${1} target="_top">', $page);

		$page = preg_replace('~^[\s\t]*</head>$~im', '</head>', $page, 1);

		$page = preg_replace('~^<html\b([^>]+)>$~im', '<html amp${1}>', $page, 1);

		$page = preg_replace('~hhttps://~i', 'https://', $page);

		// The tag 'iframe' may only appear as a descendant of tag 'noscript'. Did you mean 'amp-iframe'?
		$page = preg_replace('~<iframe\b~i', '<amp-iframe', $page);

		// The tag 'img' may only appear as a descendant of tag 'noscript'. Did you mean 'amp-img'?
		$pattern = '~<img\b(([^>]*) width=(["|\'])(\d+)\3([^>]*))/?>~iU';
		if ( preg_match_all($pattern, $page, $matches) )
		{
			foreach ( $matches[4] as $key => $value )
			{
				if ( !preg_match('~height=(["|\'])\d+\1~i', $matches[1][$key]) )
				{
					$width = $value;
					$height = intval($width * 3 / 4);

					if ( !preg_match('~layout=(["|\'])responsive\1~i', $matches[1][$key]) )
					{
						$page = preg_replace($pattern, '<amp-img' . $matches[2][$key] . ' width="' . $width . '" height="' . $height . '"' . $matches[5][$key] . 'layout="responsive" />', $page, 1);
					}
					else
					{
						$page = preg_replace($pattern, '<amp-img' . $matches[2][$key] . ' width="' . $width . '" height="' . $height . '"' . $matches[5][$key] . '/>', $page, 1);
					}
				}
				else
				{
					if ( !preg_match('~layout=(["|\'])responsive\1~i', $matches[1][$key]) )
					{
						$page = preg_replace($pattern, '<amp-img' . $matches[1][$key] . 'layout="responsive" />', $page, 1);
					}
					else
					{
						$page = preg_replace($pattern, '<amp-img' . $matches[1][$key] . '/>', $page, 1);
					}
				}
			}
		}

		// The tag 'link rel=canonical' appears more than once in the document.
		$page = preg_replace('~^<link rel="canonical" href="[^"]+" />$~im', '', $page, 1);

		// The attribute 'href' in tag 'link rel=stylesheet for fonts' is set to the invalid value...
		$page = preg_replace('~^<link rel=\'stylesheet\' id=\'[^\']+\' \s?href=\'[^\']+\' type=\'text/css\' media=\'all\' />$~im', '', $page);

		$page = preg_replace('~^[\s\t]*<link\b([^>]+)>$~im', '<link${1}>', $page);

		$page = preg_replace('~^[\s\t]*<meta\b([^>]+)>$~im', '<meta${1}>', $page);

		// Only AMP runtime 'script' tags are allowed, and only in the document head.
		$page = preg_replace('~<script\b[^>]*>.*</script>~isU', '', $page);

		// The mandatory attribute 'amp-custom' is missing in tag 'style amp-custom'.
		$pattern = '~<style\b[^>]*>(.*)</style>~isU';
		if ( preg_match_all($pattern, $page, $matches) )
		{
			foreach ( $matches[1] as $key => $value )
			{
				$amp_style .= $this->minify_css($value);

				$page = preg_replace($pattern, '', $page, 1);
			}
		}

		// The inline 'style' attribute is not allowed in AMP documents. Use 'style amp-custom' tag instead.
		$pattern = '~<([^>]+) style=(["|\'])([^\2]*)\2([^>]*)>~iU';
		if ( preg_match_all($pattern, $page, $matches) )
		{
			foreach ( $matches[3] as $key => $value )
			{
				$class = 'pwamp-class-' . $key;
				$amp_style .= $this->minify_css($value, '.' . $class);

				$pattern2 = '~ class="([^"]*)"~i';
				if ( preg_match($pattern2, $matches[1][$key], $matches2) )
				{
					if ( !empty($matches2[1]) )
					{
						$class = $matches2[1] . ' ' . $class;
					}
					$matches[1][$key] = preg_replace($pattern2, ' class="' . $class . '"', $matches[1][$key]);

					$page = preg_replace($pattern, '<' . $matches[1][$key] . $matches[4][$key] . '>', $page, 1);
				}
				elseif ( preg_match($pattern2, $matches[4][$key], $matches2) )
				{
					if ( !empty($matches2[1]) )
					{
						$class = $matches2[1] . ' ' . $class;
					}
					$matches[4][$key] = preg_replace($pattern2, ' class="' . $class . '"', $matches[4][$key]);

					$page = preg_replace($pattern, '<' . $matches[1][$key] . $matches[4][$key] . '>', $page, 1);
				}
				else
				{
					$page = preg_replace($pattern, '<' . $matches[1][$key] . ' class="' . $class . '"' . $matches[4][$key] . '>', $page, 1);
				}
			}
		}

		$page = preg_replace('~^[\s\t]*<title>(.*)</title>$~im', '<title>${1}</title>', $page, 1);
	}

	protected function transcode_head(&$page, &$amp_style, $home_url, $permalink, $canonical_url, $viewport_width)
	{
		// Remove blank lines.
		$page = preg_replace('~(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+~', "\n", $page);


		$this->retrofit_media($amp_style, $viewport_width);


		$amp_header = '<link rel="manifest" href="' . $home_url . '/' . ( $permalink == 'pretty' ? '' : '?' ) . 'manifest.webmanifest">' . "\n";

		// The property 'minimum-scale' is missing from attribute 'content' in tag 'meta name=viewport'.
		$amp_header .= '<meta name="viewport" content="width=device-width, minimum-scale=1, initial-scale=1">' . "\n";

		$amp_header .= '<meta name="theme-color" content="#ffffff">' . "\n";

		// The mandatory tag 'amphtml engine v0.js script' is missing or incorrect.
		$amp_header .= '<script async src="https://cdn.ampproject.org/v0.js"></script>' . "\n";

		// The mandatory tag 'link rel=canonical' is missing or incorrect.
		$amp_header .= '<link rel="canonical" href="' . $canonical_url . '" />' . "\n";

		// The mandatory tag 'noscript enclosure for boilerplate' is missing or incorrect.
		$amp_header .= '<style amp-boilerplate>body{-webkit-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-moz-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-ms-animation:-amp-start 8s steps(1,end) 0s 1 normal both;animation:-amp-start 8s steps(1,end) 0s 1 normal both}@-webkit-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-moz-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-ms-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-o-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}</style><noscript><style amp-boilerplate>body{-webkit-animation:none;-moz-animation:none;-ms-animation:none;animation:none}</style></noscript>' . "\n";

		// Import amp-audio component.
		$amp_header .= '<script async custom-element="amp-audio" src="https://cdn.ampproject.org/v0/amp-audio-0.1.js"></script>' . "\n";

		// Import amp-fit-text component.
		$amp_header .= '<script async custom-element="amp-fit-text" src="https://cdn.ampproject.org/v0/amp-fit-text-0.1.js"></script>' . "\n";

		// The tag 'FORM [method=GET]' requires including the 'amp-form' extension JavaScript.
		$amp_header .= '<script async custom-element="amp-form" src="https://cdn.ampproject.org/v0/amp-form-0.1.js"></script>' . "\n";

		// Import amp-iframe component.
		$amp_header .= '<script async custom-element="amp-iframe" src="https://cdn.ampproject.org/v0/amp-iframe-0.1.js"></script>' . "\n";

		// Import amp-install-serviceworker component.
		$amp_header .= '<script async custom-element="amp-install-serviceworker" src="https://cdn.ampproject.org/v0/amp-install-serviceworker-0.1.js"></script>' . "\n";

		// Import amp-sidebar component.
		$amp_header .= '<script async custom-element="amp-sidebar" src="https://cdn.ampproject.org/v0/amp-sidebar-0.1.js"></script>' . "\n";

		// amp-custom style
		$amp_header .= '<style amp-custom>' . "\n";
		$amp_header .= $amp_style . "\n";
		$amp_header .= '</style>';

		$page = preg_replace('~^[\s\t]*<meta name="viewport" content="width=device-width[^"]*">$~im', $amp_header, $page, 1);
	}
}
