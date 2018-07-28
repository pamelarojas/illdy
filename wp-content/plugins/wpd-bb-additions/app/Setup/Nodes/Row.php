<?php

namespace WPD\BBAdditions\Setup\Nodes;

use WPD\BBAdditions\Plugin;

class Row extends Node {

	/**
	 *
	 */
	protected function registerHooks()
	{
		add_filter('fl_builder_register_settings_form', [__CLASS__, 'modifySettingsForm'], 1, 2);
	}

	/**
	 * Add new style options on modules
	 *
	 * @since 1.0.0
	 *
	 * @param $form
	 * @param $id
	 *
	 * @return array $form
	 */
	public static function modifySettingsForm($form, $id)
	{
		if ('row' === $id) {
			$form[ 'tabs' ][ 'wpd' ] = [
				'title'    => __('WPD', Plugin::$config->plugin_text_domain),
				'sections' => []
			];
		}

		return $form;
	}
}
