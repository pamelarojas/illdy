<?php

namespace WPD\Toolset\Features;

final class WhiteLabel {

	/**
	 * @var
	 */
	protected static $instance = null;

	/**
	 * @var
	 */
	private $plugin_class;

	/**
	 * Plugin init
	 *
	 * @since   1.0.0
	 *
	 * @param string $plugin_class
	 */
	public function __construct($plugin_class)
	{
		if (!in_array('WPD\Toolset\Traits\PluginPropertyGetters', class_uses($plugin_class))) {
			return;
		}

		$this->plugin_class = $plugin_class;
		$this->registerHooks();
	}

	/**
	 * Run hooks
	 *
	 * @since   1.0.0
	 *
	 * @return  void
	 */
	private function registerHooks()
	{
		add_action('all_plugins', [$this, 'filterPluginsPageInfo']);
		add_action('gettext', [$this, 'filterPluginUpdateText']);
	}

	/**
	 * Plugins page info
	 *
	 * @since   1.0.0
	 *
	 * @param   array $plugins The list of plugins
	 *
	 * @return  array
	 */
	public function filterPluginsPageInfo( $plugins ) {
		if ( isset( $plugins[ call_user_func([$this->plugin_class, 'getBasename']) ] ) ) {
			$plugins[ call_user_func([$this->plugin_class, 'getBasename']) ][ 'Name' ]        = call_user_func([$this->plugin_class, 'getName']);
			$plugins[ call_user_func([$this->plugin_class, 'getBasename']) ][ 'Title' ]       = call_user_func([$this->plugin_class, 'getName']);
			$plugins[ call_user_func([$this->plugin_class, 'getBasename']) ][ 'Description' ] = call_user_func([$this->plugin_class, 'getDescription']);
			$plugins[ call_user_func([$this->plugin_class, 'getBasename']) ][ 'Author' ]      = call_user_func([$this->plugin_class, 'getAuthor']);;
			$plugins[ call_user_func([$this->plugin_class, 'getBasename']) ][ 'AuthorName' ]  = call_user_func([$this->plugin_class, 'getAuthor']);;
			$plugins[ call_user_func([$this->plugin_class, 'getBasename']) ][ 'AuthorURI' ]   = call_user_func([$this->plugin_class, 'getAuthorUri']);;
			$plugins[ call_user_func([$this->plugin_class, 'getBasename']) ][ 'PluginURI' ]   = call_user_func([$this->plugin_class, 'getWebsite']);;
		}

		return $plugins;
	}

	/**
	 * Plugins update info
	 *
	 * @since   1.0.0
	 *
	 * @param   string $text
	 *
	 * @return  string
	 */
	public function filterPluginUpdateText($text) {
		global $pagenow;

		if ( is_admin() && 'update-core.php' == $pagenow && false !== strpos( $text, call_user_func([$this->plugin_class, 'getName'], [false]) ) ) {
			$text = call_user_func([$this->plugin_class, 'getName']);
		}

		return $text;
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