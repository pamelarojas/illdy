<?php

namespace WPD\BBAdditions\Components\CustomFields\GooglePlacesAutocomplete;

use FLBuilderModel;
use WPD\BBAdditions\Plugin;

/**
 * Class GooglePlacesAutocomplete
 *
 * @package WPD\BBAdditions\Components\CustomFields\GooglePlacesAutocomplete
 */
class GooglePlacesAutocomplete
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
		$this->setup();
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
		if (!self::$instance) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Add whitelabel filters
	 *
	 * @since   1.0.0
	 *
	 * @return  void
	 */
	protected function setup()
	{
		$this->registerAssets();
		$this->registerField();
	}

	/**
	 * Register custom form
	 *
	 * @since   1.0.0
	 */
	protected function registerAssets()
	{
		add_action('wp_enqueue_scripts', function () {
			if (class_exists('FLBuilderModel') && FLBuilderModel::is_builder_active() && FLBuilderModel::get_admin_settings_option('_wpd_google_places_api_key')) {
				$fl_builder_handle = defined('WP_DEBUG') && WP_DEBUG ? 'fl-builder' : 'fl-builder-min';

				wp_enqueue_script('google-maps-places-api', '//maps.googleapis.com/maps/api/js?key=' . FLBuilderModel::get_admin_settings_option('_wpd_google_places_api_key') . '&libraries=places', [], null, true);
				wp_enqueue_script('wpd-google-places-autocomplete', Plugin::url('app/Components/CustomFields/GooglePlacesAutocomplete/resources/dist/js/field.js'), [
					'google-maps-places-api',
					$fl_builder_handle
				], null, true);
				wp_enqueue_style('wpd-google-places-autocomplete', Plugin::url('app/Components/CustomFields/GooglePlacesAutocomplete/resources/dist/css/field.css'));
			}
		});
	}

	/**
	 * Register field
	 *
	 * @since   1.0.0
	 */
	protected function registerField()
	{
		add_action('fl_builder_control_wpd-google-places-autocomplete', function ($name, $value, $field, $settings) {
			if (!FLBuilderModel::get_admin_settings_option('_wpd_google_places_api_key')) {
				echo __('Please set your Google Map API keys in <a style="color: blue;" href="' . Plugin::$config->admin_page_uri . '" target="_blank">' . 'WPD Settings' . '</a> and then re-add the module.', Plugin::$config->plugin_text_domain);

				return;
			}

			ob_start(); ?>

            <input type="text" name="<?php echo $name; ?>" value="<?php echo $value; ?>"
                   placeholder="<?php echo esc_attr__('Type address or postal code here...', Plugin::$config->plugin_text_domain); ?>"
                   class="text-full wpd-google-places-autocomplete__input" id="<?php echo $name . '__input'; ?>">

			<?php echo ob_get_clean();
		}, 1, 4);
	}
}
