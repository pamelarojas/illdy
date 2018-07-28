<?php
/*
Plugin Name: PWAMP
Plugin URI:  https://flexplat.com/pwamp/
Description: PWAMP is a WordPress solution for both lightning fast load time of AMP pages and first load cache-enabled of PWA pages.
Version:     1.4.0
Author:      Rickey Gu
Author URI:  https://flexplat.com
Text Domain: pwamp
Domain Path: /languages
*/
if ( !defined('ABSPATH') ) exit;  // Exit if accessed directly.

require_once(plugin_dir_path(__FILE__) . 'var/cfg.php');
require_once(plugin_dir_path(__FILE__) . 'lib/detection.php');
require_once(plugin_dir_path(__FILE__) . 'themes/verification.php');

class PWAMP
{
	private $page = '';

	private $home_url = '';
	private $site_url = '';
	private $plugin_dir_url = '';
	private $plugin_dir_path = '';
	private $template_directory_uri = '';
	private $permalink_structure = '';
	private $theme = '';
	private $time = '';

	private $parts = null;
	private $themes = null;


	public function __construct()
	{
		$this->home_url = home_url();
		$this->site_url = site_url();
		$this->plugin_dir_url = plugin_dir_url(__FILE__);
		$this->plugin_dir_path = plugin_dir_path(__FILE__);
		$this->template_directory_uri = get_template_directory_uri();
		$this->permalink_structure = get_option('permalink_structure');
		$this->theme = esc_html(wp_get_theme()->get('TextDomain'));
		$this->time = time();

		$this->parts = parse_url($this->home_url);
		$this->themes = wp_get_themes();
	}

	public function __destruct()
	{
	}


	private function validate()
	{
		if ( is_admin() || $GLOBALS['pagenow'] === 'wp-login.php' )
		{
			return false;
		}

		return true;
	}


	public function get_amphtml()
	{
		$amphtml_url = $this->parts['scheme'] . '://' . $this->parts['host'] . add_query_arg('amp', 'on');
		$amphtml_url = htmlspecialchars($amphtml_url);

		echo '<link rel="amphtml" href="' . $amphtml_url . '" />' . "\n";
	}

	private function get_canonical_url()
	{
		$canonical_url = $this->parts['scheme'] . '://' . $this->parts['host'] . add_query_arg('amp', 'off');
		$canonical_url = htmlspecialchars($canonical_url);

		return $canonical_url;
	}


	private function echo_sw_js()
	{
		header('Content-Type: application/javascript', true);
		echo 'importScripts(\'.' . str_replace($this->site_url, '', $this->plugin_dir_url) . 'lib/sw-toolbox/sw-toolbox.js\');
toolbox.router.default = toolbox.cacheFirst;
self.addEventListener(\'install\', function(event) {
	console.log(\'SW: Installing service worker\');
});';

		exit();
	}

	private function echo_sw_html()
	{
		header('Content-Type: text/html; charset=utf-8', true);
		echo '<!doctype html>
<html>
<head>
<title>PWAMP: installing service worker</title>
<script type=\'text/javascript\'>
	var swsource = \'' . $this->home_url . '/' . ( !empty($this->permalink_structure) ? 'pwamp-sw-js' : '?pwamp-sw-js' ) . '\';
	if ( \'serviceWorker\' in navigator ) {
		navigator.serviceWorker.register(swsource).then(function(reg) {
			console.log(\'ServiceWorker scope: \', reg.scope);
		}).catch(function(err) {
			console.log(\'ServiceWorker registration failed: \', err);
		});
	};
</script>
</head>
<body>
</body>
</html>';

		exit();
	}

	private function echo_manifest()
	{
		header('Content-Type: application/x-web-app-manifest+json', true);
		echo '{
	"name": "' . get_bloginfo('name') . ' &#8211; ' . get_bloginfo('description') . '",
	"short_name": "' . get_bloginfo('name') . '",
	"start_url": "' . $this->home_url . '",
	"display": "standalone",
	"theme_color": "#ffffff",
	"background_color": "#ffffff",
	"icons": [{
		"src": "' . str_replace($this->site_url, '', $this->plugin_dir_url) . 'lib/manifest/pwamp-logo-512.png",
		"sizes": "512x512",
		"type": "image/png"
	}]
}';

		exit();
	}

	public function init()
	{
		$current_url = $this->parts['scheme'] . '://' . $this->parts['host'] . add_query_arg();

		if ( !empty($this->permalink_structure) )
		{
			if ( $current_url == $this->home_url . '/manifest.webmanifest' )
			{
				$this->echo_manifest();
			}
			elseif ( $current_url == $this->home_url . '/pwamp-sw-js' )
			{
				$this->echo_sw_js();
			}
			elseif ( $current_url == $this->home_url . '/pwamp-sw-html' )
			{
				$this->echo_sw_html();
			}
			elseif ( preg_match('~^' . $this->home_url . '/\?pwamp-viewport-width=(\d+)$~im', $current_url, $matches) )
			{
				$viewport_width = $matches[1];
				setcookie('pwamp_viewport_width', $viewport_width, $this->time + 60 * 60 * 24 * 365, COOKIEPATH, COOKIE_DOMAIN);
			}
		}
		else
		{
			if ( $current_url == $this->home_url . '/?manifest.webmanifest' )
			{
				$this->echo_manifest();
			}
			elseif ( $current_url == $this->home_url . '/?pwamp-sw-js' )
			{
				$this->echo_sw_js();
			}
			elseif ( $current_url == $this->home_url . '/?pwamp-sw-html' )
			{
				$this->echo_sw_html();
			}
			elseif ( preg_match('~^' . $this->home_url . '/\?pwamp-viewport-width=(\d+)$~im', $current_url, $matches) )
			{
				$viewport_width = $matches[1];
				setcookie('pwamp_viewport_width', $viewport_width, $this->time + 60 * 60 * 24 * 365, COOKIEPATH, COOKIE_DOMAIN);
			}
		}
	}


	public function stylesheet()
	{
		$stylesheet = $this->themes[PWAMP_DEFAULT_THEME]->stylesheet;

		return $stylesheet;
	}

	public function template()
	{
		$template = $this->themes[PWAMP_DEFAULT_THEME]->template;

		return $template;
	}


	private function catch_page_callback($page)
	{
		$this->page .= $page;
	}

	public function after_setup_theme()
	{
		if ( empty($_COOKIE['pwamp_message']) )
		{
			ob_start(array($this, 'catch_page_callback'));

			return;
		}


		$message = $_COOKIE['pwamp_message'];
		setcookie('pwamp_message', '', $this->time - 1, COOKIEPATH, COOKIE_DOMAIN);

		$title = '';
		if ( !empty($_COOKIE['pwamp_title']) )
		{
			$title = $_COOKIE['pwamp_title'];
			setcookie('pwamp_title', '', $this->time - 1, COOKIEPATH, COOKIE_DOMAIN);
		}

		$args = array();
		if ( !empty($_COOKIE['pwamp_args']) )
		{
			$args = json_decode(stripslashes($_COOKIE['pwamp_args']));
			setcookie('pwamp_args', '', $this->time - 1, COOKIEPATH, COOKIE_DOMAIN);
		}

		_default_wp_die_handler($message, $title, $args);
	}

	public function shutdown()
	{
		$page = $this->transcode_page();
		if ( empty($page) )
		{
			echo $this->page;

			return;
		}

		echo $page;
	}


	private function get_page_type()
	{
		global $wp_query;

		$type = 'undefined';
		if ( $wp_query->is_page )
		{
			$type = is_front_page() ? 'front' : 'page';
		}
		elseif ( $wp_query->is_home )
		{
			$type = 'home';
		}
		elseif ( $wp_query->is_single )
		{
			$type = ( $wp_query->is_attachment ) ? 'attachment' : 'single';
		}
		elseif ( $wp_query->is_category )
		{
			$type = 'category';
		}
		elseif ( $wp_query->is_tag )
		{
			$type = 'tag';
		}
		elseif ( $wp_query->is_tax )
		{
			$type = 'tax';
		}
		elseif ( $wp_query->is_archive )
		{
			if ( $wp_query->is_day )
			{
				$type = 'day';
			}
			elseif ( $wp_query->is_month )
			{
				$type = 'month';
			}
			elseif ( $wp_query->is_year )
			{
				$type = 'year';
			}
			elseif ( $wp_query->is_author )
			{
				$type = 'author';
			}
			else
			{
				$type = 'archive';
			}
		}
		elseif ( $wp_query->is_search )
		{
			$type = 'search';
		}
		elseif ( $wp_query->is_404 )
		{
			$type = 'notfound';
		}

		return $type;
	}

	private function get_device()
	{
		$data = array(
			'user_agent' => !empty($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '',
			'accept' => !empty($_SERVER['HTTP_ACCEPT']) ? $_SERVER['HTTP_ACCEPT'] : '',
			'profile' => !empty($_SERVER['HTTP_PROFILE']) ? $_SERVER['HTTP_PROFILE'] : ''
		);

		$detection = new PWAMP_Detection();

		$device = $detection->get_device($data);

		return $device;
	}

	private function verify_theme()
	{
		$data = array(
			'theme' => $this->theme
		);

		$verification = new PWAMP_Verification();

		$theme = $verification->verify_theme($data);

		return $theme;
	}

	private function transcode_page()
	{
		$page = preg_replace('~^[\s\t]*<style type="[^"]+" id="[^"]+"></style>$~im', '', $this->page);

		$data = array(
			'home_url' => $this->home_url,
			'canonical_url' => $this->get_canonical_url(),
			'permalink' => !empty($this->permalink_structure) ? 'pretty' : 'ugly',
			'theme_uri' => $this->template_directory_uri,
			'viewport_width' => !empty($_COOKIE['pwamp_viewport_width']) ? $_COOKIE['pwamp_viewport_width'] : ''
		);

		$template = $this->get_page_type();

		$library = $this->plugin_dir_path . 'themes/common.php';
		include($library);

		$file = $this->plugin_dir_path . 'themes/' . $this->theme . '.php';
		include($file);

		$app = new PWAMP_Theme();

		$app->transcode($template, $page, $data);

		return $page;
	}


	private function json_redirect($redirection)
	{
		$host_url = $this->parts['scheme'] . '://' . $this->parts['host'];

		header('Content-type: application/json');
		header('Access-Control-Allow-Credentials: true');
		header('Access-Control-Allow-Origin: *.ampproject.org');
		header('Access-Control-Expose-Headers: AMP-Redirect-To, AMP-Access-Control-Allow-Source-Origin');
		header('AMP-Access-Control-Allow-Source-Origin: ' . $host_url);
		header('AMP-Redirect-To: ' . $redirection);

		$output = [];
		echo json_encode($output);

		exit();
	}

	public function comment_post_redirect($location, $comment)
	{
		$status = 302;

		$location = wp_sanitize_redirect($location);
		$location = wp_validate_redirect($location, apply_filters('wp_safe_redirect_fallback', admin_url(), $status));

		$location = apply_filters('wp_redirect', $location, $status);
		$status = apply_filters('wp_redirect_status', $status, $location);

		$this->json_redirect($location);
	}

	public function die_handler($message, $title = '', $args = array())
	{
		if ( $title !== 'Comment Submission Failure' )
		{
			_default_wp_die_handler($message, $title, $args);

			return;
		}


		setcookie('pwamp_message', $message, $this->time + 60, COOKIEPATH, COOKIE_DOMAIN);

		if ( !empty($title) )
		{
			setcookie('pwamp_title', $title, $this->time + 60, COOKIEPATH, COOKIE_DOMAIN);
		}
		else
		{
			setcookie('pwamp_title', '', $this->time - 1, COOKIEPATH, COOKIE_DOMAIN);
		}

		if ( !empty($args) )
		{
			setcookie('pwamp_args', json_encode($args), $this->time + 60, COOKIEPATH, COOKIE_DOMAIN);
		}
		else
		{
			setcookie('pwamp_args', '', $this->time - 1, COOKIEPATH, COOKIE_DOMAIN);
		}

		$redirection = $this->site_url;
		$this->json_redirect($redirection);
	}

	public function wp_die_handler($function)
	{
		$function = array($this, 'die_handler');

		return $function;
	}


	public function main()
	{
		if ( !$this->validate() )
		{
			return;
		}


		add_action('init', array($this, 'init'));


		$redirection = !empty($_GET['amp']) ? $_GET['amp'] : '';
		$style = !empty($_COOKIE['pwamp_style']) ? $_COOKIE['pwamp_style'] : '';

		if ( !empty($redirection) )
		{
			$device = $redirection != 'on' ? 'desktop' : 'mobile';
		}
		elseif ( !empty($style) )
		{
			$device = $style != 'mobile' ? 'desktop' : 'mobile';
		}
		else
		{
			$device = $this->get_device();
			if ( empty($device) )
			{
				return;
			}

			$device = ( $device == 'desktop' || $device == 'bot' ) ? 'desktop' : 'mobile';
		}

		setcookie('pwamp_style', $device, $this->time + 60 * 60 * 24 * 30, COOKIEPATH, COOKIE_DOMAIN);

		if ( $device != 'mobile' )
		{
			add_action('wp_head', array($this, 'get_amphtml'), 0);

			return;
		}


		$theme = !empty($_COOKIE['pwamp_theme']) ? $_COOKIE['pwamp_theme'] : '';

		if ( empty($theme) || ( $theme != PWAMP_DEFAULT_THEME && $theme != $this->theme ) )
		{
			$supported = $this->verify_theme();
			if ( empty($supported) )
			{
				return;
			}

			if ( $supported != 'yes' )
			{
				if ( empty($this->themes[PWAMP_DEFAULT_THEME]) )
				{
					return;
				}

				$this->theme = PWAMP_DEFAULT_THEME;

				add_filter('stylesheet', array($this, 'stylesheet'));
				add_filter('template', array($this, 'template'));
			}

			setcookie('pwamp_theme', $this->theme, $this->time + 60 * 60 * 24, COOKIEPATH, COOKIE_DOMAIN);
		}
		elseif ( $theme == PWAMP_DEFAULT_THEME )
		{
			if ( $theme != $this->theme )
			{
				$this->theme = PWAMP_DEFAULT_THEME;

				add_filter('stylesheet', array($this, 'stylesheet'));
				add_filter('template', array($this, 'template'));
			}
		}


		add_action('after_setup_theme', array($this, 'after_setup_theme'));
		add_action('shutdown', array($this, 'shutdown'));

		add_filter('comment_post_redirect', array($this, 'comment_post_redirect'), 10, 2);
		add_filter('wp_die_handler', array($this, 'wp_die_handler'), 10, 1);

		add_filter('show_admin_bar', '__return_false');
	}
}


function pwamp_main()
{
	$pwamp = new PWAMP();

	$pwamp->main();
}
add_action('plugins_loaded', 'pwamp_main', 1);
