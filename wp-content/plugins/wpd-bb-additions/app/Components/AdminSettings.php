<?php

namespace WPD\BBAdditions\Components;

use FLBuilderModel;
use WPD\BBAdditions\Plugin;
use WPD\BBAdditions\Utils\General;

/**
 * Class AdminSettings
 *
 * @package WPD\BBAdditions\Components
 */
class AdminSettings
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
		$this->registerAdminNavLink();
		$this->renderAdminForm();
		$this->saveSettings();
	}

	/**
	 * Save settings when POSTed
	 *
	 * @since 1.0.0
	 */
	protected function registerAdminNavLink()
	{
		add_filter('fl_builder_admin_settings_nav_items', function ($nav_items) {
			$sorted_data = [];

			foreach ($nav_items as $key => $data) {
				$data[ 'key' ]                      = $key;
				$sorted_data[ $data[ 'priority' ] ] = $data;
			}

			ksort($sorted_data);

			$nav_items[ 'wpd-bb-additions-admin-settings' ] = [
				'title'    => __('WPD Settings', Plugin::$config->plugin_text_domain),
				'show'     => true,
				'priority' => end($sorted_data)[ 'priority' ] - 1
			];

			return $nav_items;
		}, 10, 1);
	}

	/**
	 * Save settings when POSTed
	 *
	 * @since 1.0.0
	 */
	protected function renderAdminForm()
	{
		add_action('fl_builder_admin_settings_render_forms', function () {
			?>

            <div id="fl-wpd-bb-additions-admin-settings-form" class="fl-settings-form" style="max-width: 880px;">
                <h3 class="fl-settings-form-header"><?php _e('WPD Settings', Plugin::$config->plugin_text_domain); ?></h3>

                <form id="wpd-bb-additions-admin-settings-form"
                      action="<?php \FLBuilderAdminSettings::render_form_action('wpd-bb-additions-admin-settings'); ?>"
                      method="post">

                    <div class="fl-settings-form-content">
                        <h3><?= __('Google Maps configuration', Plugin::$config->plugin_text_domain); ?></h3>
                        <input type="hidden" name="wpd-bb-additions-admin-form-updated" value="1">
                        <div class="wpd-google-places-api-key">
                            <h4><?= __('Set Google Places API key', Plugin::$config->plugin_text_domain); ?></h4>
                            <p><?= __('This is used for autocompleting addresses when you start typing them', Plugin::$config->plugin_text_domain); ?></p>
                            <input type="text" name="wpd-google-places-api-key"
                                   value="<?= FLBuilderModel::get_admin_settings_option('_wpd_google_places_api_key'); ?>"
                                   class="regular-text"/>
                            <p>
                                <em><?= sprintf(__('Click "Get a Key" from %1$s this page%2$s. See %3$sthis%2$s article for instructions.', Plugin::$config->plugin_text_domain), '<a href="https://developers.google.com/places/web-service/" target="_blank">', '</a>', '<a href="https://wpdevelopers.co.uk/knowledge-base/generating-google-maps-javascript-api-key-gif/?utm_source=wpd-bb-modules-admin-settings&utm_campaign=google-api-key&utm_medium=wpd-bb-modules" target="_blank">'); ?></em>
                            </p>
                        </div>

                        <div class="wpd-google-static-map-api-key">
                            <h4><?= __('Set Google Static Map API key', Plugin::$config->plugin_text_domain); ?></h4>
                            <p><?= __('This is used for displaying a Google map as an image, rather than an embed, which improves page load speed', Plugin::$config->plugin_text_domain); ?></p>
                            <input type="text" name="wpd-google-static-map-api-key"
                                   value="<?= FLBuilderModel::get_admin_settings_option('_wpd_google_static_map_api_key'); ?>"
                                   class="regular-text"/>
                            <p>
                                <em><?= sprintf(__('Click "Get a Key" from %1$s this page%2$s. See %3$sthis%2$s article for instructions.', Plugin::$config->plugin_text_domain), '<a href="https://developers.google.com/maps/documentation/static-maps/" target="_blank">', '</a>', '<a href="https://wpdevelopers.co.uk/knowledge-base/generating-google-maps-javascript-api-key-gif/?utm_source=wpd-bb-modules-admin-settings&utm_campaign=google-api-key&utm_medium=wpd-bb-modules" target="_blank">'); ?></em>
                            </p>
                        </div>

                        <div class="wpd-enhancements-disable-options">
                            <h4><?= __('Disable Beaver Builder Enhancements', Plugin::$config->plugin_text_domain); ?></h4>
                            <p><?= __('Enhancements are small features or modifications for Beaver Builder that are enabled by default. If you don\'t need them, simply tick the box next to the ones you want to disable below.', Plugin::$config->plugin_text_domain); ?></p>
                            <p>
                                <label>
                                    <input type="checkbox" name="inactive-wpd-enhancements[]"
                                           value="collapsible-rows" <?= General::isEnhancementInactive('collapsible-rows', true); ?> />
                                    <span><?= __('Collapsible Rows', Plugin::$config->plugin_text_domain); ?></span>
                                </label>
                            </p>
                            <p>
                                <label>
                                    <input type="checkbox" name="inactive-wpd-enhancements[]"
                                           value="feature-element" <?= General::isEnhancementInactive('feature-element', true); ?> />
                                    <span><?= __('Feature Element', Plugin::$config->plugin_text_domain); ?></span>
                                </label>
                            </p>
                            <p>
                                <label>
                                    <input type="checkbox" name="inactive-wpd-enhancements[]"
                                           value="module-animations" <?= General::isEnhancementInactive('module-animations', true); ?> />
                                    <span><?= __('Module Animations', Plugin::$config->plugin_text_domain); ?></span>
                                </label>
                            </p>
                            <p>
                                <label>
                                    <input type="checkbox" name="inactive-wpd-enhancements[]"
                                           value="module-animation-previews" <?= General::isEnhancementInactive('module-animation-previews', true); ?> />
                                    <span><?= __('Module Animation Previews', Plugin::$config->plugin_text_domain); ?></span>
                                </label>
                            </p>
                            <p>
                                <label>
                                    <input type="checkbox" name="inactive-wpd-enhancements[]"
                                           value="module-animation-speed" <?= General::isEnhancementInactive('module-animation-speed', true); ?> />
                                    <span><?= __('Module Animation Speed', Plugin::$config->plugin_text_domain); ?></span>
                                </label>
                            </p>
                            <p>
                                <label>
                                    <input type="checkbox" name="inactive-wpd-enhancements[]"
                                           value="polaroid-photo" <?= General::isEnhancementInactive('polaroid-photo', true); ?> />
                                    <span><?= __('Polaroid Photo', Plugin::$config->plugin_text_domain); ?></span>
                                </label>
                            </p>
                            <p>
                                <label>
                                    <input type="checkbox" name="inactive-wpd-enhancements[]"
                                           value="row-effect-on-scroll" <?= General::isEnhancementInactive('row-effect-on-scroll', true); ?> />
                                    <span><?= __('Row Effects On Scroll', Plugin::$config->plugin_text_domain); ?></span>
                                </label>
                            </p>
                            <p>
                                <label>
                                    <input type="checkbox" name="inactive-wpd-enhancements[]"
                                           value="match-height" <?= General::isEnhancementInactive('match-height', true); ?> />
                                    <span><?= __('Match Module Heights', Plugin::$config->plugin_text_domain); ?></span>
                                </label>
                            </p>
                        </div>
                    </div>

                    <p class="submit">
                        <input type="submit" name="update" class="button-primary"
                               value="<?php esc_attr_e('Save WPD Settings', Plugin::$config->plugin_text_domain); ?>"/>
						<?php wp_nonce_field('wpd-bb-additions-admin-settings', 'wpd-bb-additions-admin-settings-nonce'); ?>
                    </p>
                </form>

                <a href="https://wpdevelopers.co.uk/?utm_source=wpd-bb-additions-admin-settings&utm_campaign=wpd-bb-additions-admin-settings-footer&utm_medium=wpd-bb-additions"
                   target="_blank">
                    <img class="fl-wpd-logo-img" src="<?= Plugin::assetDistUri('images/wp-developers-logo.svg'); ?>"
                         alt="<?= esc_attr__('WP Developers', Plugin::$config->plugin_text_domain); ?>"
                         style="max-width: 100px; float: right;">
                </a>
            </div>

			<?php
		});
	}

	/**
	 * Save settings when POSTed
	 *
	 * @since 1.0.0
	 */
	protected function saveSettings()
	{
		add_action('fl_builder_admin_settings_save', function () {
			if (isset($_POST[ 'wpd-bb-additions-admin-settings-nonce' ]) && wp_verify_nonce($_POST[ 'wpd-bb-additions-admin-settings-nonce' ], 'wpd-bb-additions-admin-settings')) {
				if (isset($_POST[ 'wpd-bb-additions-admin-form-updated' ]) && !FLBuilderModel::get_admin_settings_option('_wpd_bb_additions_admin_form_updated')) {
					$wpd_bb_additions_settings_updated = sanitize_text_field($_POST[ 'wpd-bb-additions-admin-form-updated' ]);

					FLBuilderModel::update_admin_settings_option('_wpd_bb_additions_admin_form_updated', $wpd_bb_additions_settings_updated, false);
				}

				if (isset($_POST[ 'wpd-google-places-api-key' ])) {
					$wpd_google_places_api_key = sanitize_text_field($_POST[ 'wpd-google-places-api-key' ]);

					FLBuilderModel::update_admin_settings_option('_wpd_google_places_api_key', $wpd_google_places_api_key, false);
				}

				if (isset($_POST[ 'wpd-google-static-map-api-key' ])) {
					$wpd_google_static_map_api_key = sanitize_text_field($_POST[ 'wpd-google-static-map-api-key' ]);

					FLBuilderModel::update_admin_settings_option('_wpd_google_static_map_api_key', $wpd_google_static_map_api_key, false);
				}

				if (isset($_POST[ 'wpd-feature-overlay' ])) {
					$wpd_feature_overlay = sanitize_text_field($_POST[ 'wpd-feature-overlay' ]);

					FLBuilderModel::update_admin_settings_option('_wpd_feature_overlay', $wpd_feature_overlay, false);
				}

				$wpd_inactive_enhancements = [];
				if (isset($_POST[ 'inactive-wpd-enhancements' ])) {
					$wpd_inactive_enhancements = (array) $_POST[ 'inactive-wpd-enhancements' ];
				}

				FLBuilderModel::update_admin_settings_option('_wpd_inactive_enhancements', $wpd_inactive_enhancements, false);
			}
		});
	}
}
