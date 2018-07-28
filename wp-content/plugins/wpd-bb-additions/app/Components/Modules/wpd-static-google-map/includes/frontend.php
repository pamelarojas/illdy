<?php

use WPD\BBAdditions\Plugin;

if (!FLBuilderModel::get_admin_settings_option('_wpd_google_static_map_api_key')) {
	echo __('Please set your Google Map API keys in <a style="color: blue;" href="' . Plugin::$config->admin_page_uri . '" target="_blank">' . 'WPD Settings' . '</a> and then re-add the module to the page.', Plugin::$config->plugin_text_domain);

	return;
}

?>

<div class="wpd-static-google-map">
    <a href="<?php echo isset($settings->external_map_url) ? $settings->external_map_url : ''; ?>" target="_blank"
       rel="nofollow">
        <img src="<?php echo $module->getGoogleStaticMapUrl(); ?>"
             alt="<?php echo sprintf(__('%1$s Location', Plugin::$config->plugin_text_domain), get_bloginfo('name')); ?>">
    </a>
</div>
