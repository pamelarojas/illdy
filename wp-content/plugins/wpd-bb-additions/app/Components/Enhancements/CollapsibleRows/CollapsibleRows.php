<?php

namespace WPD\BBAdditions\Components\Enhancements\CollapsibleRows;

use WPD\BBAdditions\Plugin;
use WPD\BBAdditions\Utils\General;

/**
 * Class CollapsibleRows
 *
 * @package WPD\BBAdditions\Enhancements
 */
class CollapsibleRows
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
		if (!self::$instance) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 *
	 */
	protected function registerHooks()
	{
		add_filter('fl_builder_register_settings_form', [__CLASS__, 'addSettingsForm'], 10, 2);
		add_filter('fl_builder_after_render_row_bg', [__CLASS__, 'renderRowBg'], 10, 2);
		add_filter('fl_builder_before_render_row', [__CLASS__, 'renderBeforeRow'], 10, 2);
		add_filter('fl_builder_row_attributes', [__CLASS__, 'addModuleClassAttributes'], 10, 2);
		add_filter('fl_builder_render_css', [__CLASS__, 'renderCustomCss'], 10, 3);
		add_action('init', [__CLASS__, 'setupFrontendAssets']);
	}

	/**
	 * Enqueue assets from CDN
	 */
	public static function registerExternalAssets()
	{
		wp_enqueue_script('js-cookie', '//cdnjs.cloudflare.com/ajax/libs/js-cookie/2.2.0/js.cookie.min.js', ['jquery'], null, false);
	}

	/**
	 * Close icon on rows
	 *
	 * @param $form
	 * @param $id
	 *
	 * @return array $form
	 */
	public static function addSettingsForm($form, $id)
	{
		if ('row' === $id) {
			$form[ 'tabs' ][ 'wpd' ][ 'sections' ][ 'close_row' ] = [
				'title'  => __('Close Row', Plugin::$config->plugin_text_domain),
				'fields' => [
					'close_row_icon'            => [
						'type'        => 'icon',
						'label'       => __('Close Icon', Plugin::$config->plugin_text_domain),
						'show_remove' => true
					],
					'close_row_icon_size'       => [
						'type'        => 'unit',
						'label'       => __('Close Icon Size', Plugin::$config->plugin_text_domain),
						'description' => 'px',
						'responsive'  => 'true'
					],
					'close_row_icon_color'      => [
						'type'       => 'color',
						'label'      => __('Close Icon Color', Plugin::$config->plugin_text_domain),
						'show_reset' => 'true'
					],
					'close_row_icon_position'   => [
						'type'    => 'select',
						'label'   => __('Close Icon Position', Plugin::$config->plugin_text_domain),
						'options' => [
							'top_right'    => __('Top Right', Plugin::$config->plugin_text_domain),
							'middle_right' => __('Middle Right', Plugin::$config->plugin_text_domain),
							'bottom_right' => __('Bottom Right', Plugin::$config->plugin_text_domain),
						],
						'default' => 'middle_right'
					],
					'close_row_speed'           => [
						'type'        => 'unit',
						'label'       => __('Close Speed', Plugin::$config->plugin_text_domain),
						'default'     => '500',
						'description' => 'ms'
					],
					'set_close_row_icon_cookie' => [
						'type'    => 'select',
						'label'   => __('Set Cookie?', Plugin::$config->plugin_text_domain),
						'help'    => __('By enabling this, if a visitor closes this row, it won\'t reappear after a page refresh', Plugin::$config->plugin_text_domain),
						'options' => [
							'false' => __('No', Plugin::$config->plugin_text_domain),
							'true'  => __('Yes', Plugin::$config->plugin_text_domain),
						],
						'default' => 'false',
						'toggle'  => [
							'true' => [
								'fields' => ['close_row_cookie_duration']
							]
						]
					],
					'close_row_cookie_duration' => [
						'type'        => 'unit',
						'label'       => __('Cookie Duration', Plugin::$config->plugin_text_domain),
						'description' => __('days', Plugin::$config->plugin_text_domain),
						'default'     => '30'
					]
				]
			];
		}

		return $form;
	}

	/**
	 * @param $row
	 */
	public static function renderRowBg($row)
	{
		if (isset($row->settings->close_row_icon) && !empty($row->settings->close_row_icon)) {
			echo '<a href="#" class="row-close-icon"><i class="' . $row->settings->close_row_icon . '"></i></a>';
		}
	}

	/**
	 * @param $row
	 * @param $groups
	 */
	public static function renderBeforeRow($row, $groups)
	{
		if (isset($row->settings->close_row_icon) && !empty($row->settings->close_row_icon)) {
			General::enqueueIconStyles($row->settings->close_row_icon);
		}
	}

	/**
	 * @param $attrs
	 * @param $row
	 *
	 * @return mixed
	 */
	public static function addModuleClassAttributes($attrs, $row)
	{
	    if (\FLBuilderModel::is_builder_active()) {
	        return $attrs;
        }

		if (isset($_COOKIE[ 'row_' . $row->node . '__closed' ]) && 'yes' == $_COOKIE[ 'row_' . $row->node . '__closed' ]) {
			$attrs[ 'class' ][] = 'wpd-hide';
		}

		return $attrs;
	}

	/**
	 * @param $css
	 * @param $nodes
	 * @param $global_settings
	 *
	 * @return string
	 */
	public static function renderCustomCss($css, $nodes, $global_settings)
	{
		foreach ($nodes[ 'rows' ] as $row => $row_object) {
			if (isset($row_object->settings->close_row_icon) && !empty($row_object->settings->close_row_icon)) :
				$icon_top_position = '50%';
				$icon_vertical_transform = '-50%';

				if (isset($row_object->settings->close_row_icon_position) && 'top_right' == $row_object->settings->close_row_icon_position) {
					$icon_top_position       = '0';
					$icon_vertical_transform = '0';
				}

				if (isset($row_object->settings->close_row_icon_position) && 'bottom_right' == $row_object->settings->close_row_icon_position) {
					$icon_top_position       = '100%';
					$icon_vertical_transform = '-100%';
				}

				// @formatter:off

				ob_start(); ?>

                .fl-node-<?php echo $row; ?> .row-close-icon {
                    position: absolute;
                    top: <?php echo $icon_top_position; ?>;
                    right: 1rem;
                    transform: translateY(<?php echo $icon_vertical_transform; ?>);
                    line-height: 0;
				    <?php echo isset( $row_object->settings->close_row_icon_size ) && isset( $row_object->settings->close_row_icon_size ) ? 'font-size: ' . $row_object->settings->close_row_icon_size . 'px;' : ''; ?>
				    <?php echo isset( $row_object->settings->close_row_icon_color ) && isset( $row_object->settings->close_row_icon_color ) ? 'color: #' . $row_object->settings->close_row_icon_color . ';' : ''; ?>
                }

				<?php if ( isset( $row_object->settings->close_row_icon_size_medium ) && isset( $row_object->settings->close_row_icon_size_medium ) ) : ?>
                    @media (max-width: <?php echo $global_settings->medium_breakpoint; ?>px) {
                        .fl-node-<?php echo $row; ?> .row-close-icon {
                            font-size: <?php echo $row_object->settings->close_row_icon_size_medium; ?>px;
                        }
                    }
			    <?php endif; ?>

				<?php if ( isset( $row_object->settings->close_row_icon_size_responsive ) && isset( $row_object->settings->close_row_icon_size_responsive ) ) : ?>
                    @media (max-width: <?php echo $global_settings->responsive_breakpoint; ?>px) {
                        .fl-node-<?php echo $row; ?> .row-close-icon {
                            font-size: <?php echo $row_object->settings->close_row_icon_size_responsive; ?>px;
                        }
                    }
			    <?php endif; ?>

                .wpd-hide.fl-node-<?php echo $row; ?> {
                    display: none;
                }

				<?php $css .= ob_get_clean();

				// @formatter:off
			endif;
		}
		return $css;
	}


	/**
	 * @param $js
	 * @param $nodes
	 * @param $global_settings
	 *
	 * @return string
	 */
	public static function renderCustomJs( $js, $nodes, $global_settings )
	{
		foreach ( $nodes[ 'rows' ] as $row => $row_object ) {
			if ( isset( $row_object->settings->close_row_icon ) && ! empty( $row_object->settings->close_row_icon ) ) :
				// @formatter:off

				ob_start(); ?>

                ( function($) {

                    $('.fl-node-<?php echo $row; ?> .row-close-icon').on('click', function(e) {
                        e.preventDefault();

				        <?php if ( isset( $row_object->settings->set_close_row_icon_cookie ) && 'true' == $row_object->settings->set_close_row_icon_cookie ) : ?>
                            Cookies.set('row_<?php echo $row; ?>__closed', 'yes', { expires: <?php echo $row_object->settings->close_row_cookie_duration; ?> });
			            <?php endif; ?>

                        $(this).closest('.fl-row').slideUp(<?php echo isset( $row_object->settings->close_row_speed ) ? $row_object->settings->close_row_speed : 500; ?>, function() {
                            FLBuilderLayout._initModuleAnimations();
                        });
                    });
                })(jQuery);

				<?php $js .= ob_get_clean();

				// @formatter:on
			endif;
		}

		return $js;
	}

	/**
	 * Setup assets for frontend
	 *
	 * @since 2.0.4
	 */
	public static function setupFrontendAssets()
	{
		if (!\FLBuilderModel::is_builder_active()) {
			add_action('wp_enqueue_scripts', [__CLASS__, 'registerExternalAssets']);
			add_filter('fl_builder_render_js', [__CLASS__, 'renderCustomJs'], 10, 3);
		}
	}
}
