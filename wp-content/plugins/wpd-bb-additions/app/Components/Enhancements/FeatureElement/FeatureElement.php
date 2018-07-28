<?php

namespace WPD\BBAdditions\Components\Enhancements\FeatureElement;

use FLBuilderModel;
use WPD\BBAdditions\Plugin;
use WPD\Toolset\Utilities\Color;

/**
 * Class FeatureElement
 * Add ability to feature an element on click
 *
 * @package WPD\BBAdditions\Enhancements
 */
class FeatureElement
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
		add_action('fl_builder_after_render_module', [__CLASS__, 'renderAfterRowModule']);
		add_action('fl_builder_module_attributes', [__CLASS__, 'renderModuleAttributes'], 10, 2);
		add_action('init', [__CLASS__, 'setupFrontendAssets']);
	}

	/**
	 *
	 * @param $module
	 */
	public static function renderAfterRowModule($module)
	{
		if (isset($module->settings->enable_focus_feature) && 'on' === $module->settings->enable_focus_feature) : ?>
            <div class="wpd-feature-element__feature-overlay"
                 style="background-color: <?= Color::getRgbaOrHex($module->settings->feature_overlay_color); ?>"></div>
		<?php endif;
	}

	/**
	 * @param $form
	 * @param $slug
	 *
	 * @return mixed
	 */
	public static function addSettingsForm($form, $slug)
	{
		if ('contact-form' === $slug || 'subscribe-form' === $slug) {
			$form[ 'wpd' ] = [
				'title'    => __('WPD', Plugin::$config->plugin_text_domain),
				'sections' => [
					'enable_focus_feature' => [
						'title'  => __('Focus Feature', Plugin::$config->plugin_text_domain),
						'fields' => [
							'enable_focus_feature'  => [
								'type'        => 'select',
								'label'       => __('Enable focus feature', Plugin::$config->plugin_text_domain),
								'description' => __('When a user clicks inside a field in this module, a background overlay will be displayed with the form in front', Plugin::$config->plugin_text_domain),
								'default'     => 'off',
								'options'     => [
									'on'  => __('On', Plugin::$config->plugin_text_domain),
									'off' => __('Off', Plugin::$config->plugin_text_domain)
								],
								'toggle'      => [
									'on' => [
										'fields' => ['feature_overlay_color']
									]
								]
							],
							'feature_overlay_color' => [
								'type'       => 'color',
								'label'      => __('Set Background Colour for Feature Overlay', Plugin::$config->plugin_text_domain),
								'show_reset' => true,
								'show_alpha' => true,
							],
						],
					],
				],
			];
		}

		return $form;

	}

	/**
	 * @param $attrs
	 * @param $module
	 *
	 * @return mixed
	 */
	public static function renderModuleAttributes($attrs, $module)
	{
		if (!FLBuilderModel::is_builder_active() && isset($module->settings->enable_focus_feature)) {
			if ('on' == $module->settings->enable_focus_feature) {
				$attrs[ 'class' ][] = 'wpd-feature-module--active';
			}
		}

		return $attrs;
	}

	/**
	 * @param $css
	 * @param $nodes
	 * @param $global_settings
	 * @param $include_global
	 *
	 * @return string
	 */
	public static function renderCustomCss($css, $nodes, $global_settings, $include_global)
	{
		if ($include_global) {

			// @formatter:off

			ob_start(); ?>

            .wpd-feature-element__feature-overlay {
                position: fixed;
                display: none;
                width: 100%;
                height: 100%;
                top: 0;
                background-color: rgba(0,0,0,0.5);
                left: 0;
                z-index: 9998;
                cursor: pointer;
                overflow-x: hidden;
                transition: 0.5s;
            }

            .wpd-feature-element__feature-overlay.wpd-feature-element__feature-overlay--active {
                display: block;
            }

            .wpd-feature-module--active {
                position: relative;
                z-index: 9999;
            }

			<?php $css .= ob_get_clean();

			// @formatter:on
		}

		return $css;

	}

	/**
	 * @param $js
	 * @param $nodes
	 * @param $global_settings
	 * @param $include_global
	 *
	 * @return string
	 */
	public static function renderCustomJs($js, $nodes, $global_settings, $include_global)
	{
		if (!FLBuilderModel::is_builder_active() && $include_global) {
			foreach ($nodes[ 'modules' ] as $module => $module_object) {
				if (('contact-form' === $module_object->slug || 'subscribe-form' === $module_object->slug) && 'on' === $module_object->settings->enable_focus_feature) {
					// @formatter:off

				    ob_start(); ?>

                    ( function($) {
                        var wpdElement = $('.fl-node-<?= $module; ?>');
                        var wpdOverlay = $(wpdElement).closest('.fl-col-content').find('.wpd-feature-element__feature-overlay');
                        var wpdElOffset = wpdElement.offset().top;
                        var wpdElHeight = wpdElement.height();
                        var wpdWindowHeight = $(window).height();
                        var wpdOffset;

                        $( '.fl-node-<?= $module; ?> form input, .fl-node-<?= $module; ?> form textarea, .fl-node-<?= $module; ?> form select' ).on( 'click', function(e) {
                            if ( wpdElHeight < wpdWindowHeight ) {
                                wpdOffset = wpdElOffset - ( ( wpdWindowHeight / 2 ) - ( wpdElHeight / 2 ) );
                            }
                            else {
                                wpdOffset = wpdElOffset;
                            }

                            $('html, body').animate({
                                scrollTop: wpdOffset
                            }, 500);

                            $('body').addClass('wpd-overlay--active');
                            $(wpdOverlay).addClass('wpd-feature-element__feature-overlay--active');

                            return false;
                        });

                        $( '.fl-node-<?= $module; ?> form a.fl-button' ).on( 'click', function() {
                            if( $(wpdOverlay).hasClass('wpd-feature-element__feature-overlay--active') ) {
                                $(wpdOverlay).removeClass('wpd-feature-element__feature-overlay--active');
                                return false;
                            }
                        });

                        $( '.wpd-feature-element__feature-overlay' ).on( 'click', function() {
                            $(this).removeClass('wpd-feature-element__feature-overlay--active');
                            return false;
                        });

                    })( jQuery );

				    <?php $js .= ob_get_clean();
			    }
		    }
	    }
	    return $js;
    }

	/**
	 * Setup assets for frontend
	 *
	 * @since 2.0.4
	 */
    public static function setupFrontendAssets() {
        if (!FLBuilderModel::is_builder_active()) {
            add_filter('fl_builder_render_css', [__CLASS__, 'renderCustomCss'], 10, 4);
            add_filter('fl_builder_render_js', [__CLASS__, 'renderCustomJs'], 10, 4);
        }
    }
}



