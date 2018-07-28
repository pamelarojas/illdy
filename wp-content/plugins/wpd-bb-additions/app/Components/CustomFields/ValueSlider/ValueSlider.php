<?php

namespace WPD\BBAdditions\Components\CustomFields\ValueSlider;

use FLBuilderModel;
use WPD\BBAdditions\Plugin;

/**
 * Class ValueSlider
 *
 * @package WPD\BBAdditions\Components\CustomFields\ValueSlider
 */
class ValueSlider
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
			if (class_exists('FLBuilderModel') && FLBuilderModel::is_builder_active()) {
				$fl_builder_handle = defined('WP_DEBUG') && WP_DEBUG ? 'fl-builder' : 'fl-builder-min';

				wp_enqueue_script('wpd-value-slider', Plugin::url('app/Components/CustomFields/ValueSlider/resources/dist/js/field.js'), [
					'jquery',
					$fl_builder_handle
				], null, true);
				wp_enqueue_style('wpd-value-slider', Plugin::url('app/Components/CustomFields/ValueSlider/resources/dist/css/field.css'));
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
		add_action('fl_builder_control_wpd-value-slider', function ($name, $value, $field, $settings) {
			$value = $value ?: 0;

			ob_start(); ?>

            <span class="numerical-value-outer"><span
                        class="numerical-value"><?php echo $value; ?></span> <?php echo isset($field[ 'unit' ]) ? $field[ 'unit' ] : ''; ?></span>

            <input type="range"
                   name="<?php echo $name; ?>"
                   value="<?php echo $value; ?>"
                   class="<?php echo $name . '__input'; ?> wpd-value-slider__input"
                   min="<?php echo isset($field[ 'min' ]) ? $field[ 'min' ] : '0'; ?>"
                   max="<?php echo isset($field[ 'max' ]) ? $field[ 'max' ] : '100'; ?>"
                   step="<?php echo isset($field[ 'step' ]) ? $field[ 'step' ] : '1'; ?>"
            >

			<?php echo ob_get_clean();
		}, 1, 4);
	}
}
