<?php

namespace WPD\BBAdditions\Utils;

use WPD\BBAdditions\Plugin;

/**
 * Class FieldGroups
 *
 * @package WPD\BBAdditions\Utils
 */
class FieldGroups
{
	/**
	 * @return array Icon fields
	 */
	public static function getIconFieldGroup()
	{
		return [
			'icon'       => [
				'type'        => 'icon',
				'label'       => __('Icon', Plugin::$config->plugin_text_domain),
				'show_remove' => true
			],
			'icon_size'  => [
				'type'        => 'unit',
				'label'       => __('Icon size', Plugin::$config->plugin_text_domain),
				'default'     => '',
				'size'        => '4',
				'description' => 'px',
				'responsive'  => true,
				'preview'     => [
					'type'     => 'css',
					'selector' => 'i',
					'property' => 'font-size',
					'unit'     => 'px'
				]
			],
			'icon_color' => [
				'type'       => 'color',
				'label'      => __('Icon colour', Plugin::$config->plugin_text_domain),
				'preview'    => [
					'type'     => 'css',
					'selector' => 'i',
					'property' => 'color'
				],
				'show_alpha' => true,
				'show_reset' => true
			]
		];
	}

	/**
	 * @return array All possible CSS filters
	 */
	public static function getAllCssFilters()
	{
		$filters = [
			'none' => __('None', Plugin::$config->plugin_text_domain),
		];

		return array_merge($filters, self::getPercentageBasedCssFilters());
	}

	/**
	 * @return array All CSS filters that use percentages for the value
	 */
	public static function getPercentageBasedCssFilters()
	{
		return [
			'grayscale' => __('Grayscale', Plugin::$config->plugin_text_domain),
			'sepia'     => __('Sepia', Plugin::$config->plugin_text_domain),
			'invert'    => __('Invert', Plugin::$config->plugin_text_domain),
		];
	}

	/**
	 * @return array Toggle rules for when you select a CSS field in the builder
	 */
	public static function getAllCssFiltersToggleRules()
	{
		$toggle_rules = [];

		return array_merge($toggle_rules, self::getPercentageBasedCssFilterToggleRules());
	}

	/**
	 * @return array Toggle rules only for percentage based CSS filters
	 */
	public static function getPercentageBasedCssFilterToggleRules()
	{
		$toggle_array = [];

		foreach (self::getPercentageBasedCssFilters() as $filter => $label) {
			$toggle_array[ $filter ] = [
				'fields' => ['css_filter_percentage']
			];
		}

		return $toggle_array;
	}

	/**
	 * @return array Array of fields for CSS filters
	 */
	public static function getCssFilterFieldGroup()
	{
		return [
			'select_css_filter'     => [
				'type'        => 'select',
				'label'       => __('Select CSS filter', Plugin::$config->plugin_text_domain),
				'description' => __('Experimental. Only modern browsers will see this effect.', Plugin::$config->plugin_text_domain),
				'options'     => self::getAllCssFilters(),
				'toggle'      => self::getAllCssFiltersToggleRules(),
				'default'     => 'none',
				'preview'     => [
					'type' => 'none'
				]
			],
			'css_filter_percentage' => [
				'type'    => 'wpd-value-slider',
				'label'   => __('Filter Amount', Plugin::$config->plugin_text_domain),
				'preview' => [
					'type' => 'none'
				]
			]
		];
	}

	/**
	 * @return array Tab containing structure fields, such as alignment
	 */
	public static function getStructureTab()
	{
		$tab[ 'structure' ] = [
			'title'    => __('Structure', Plugin::$config->plugin_text_domain),
			'sections' => [
				'typography' => [
					'title'  => __('General', Plugin::$config->plugin_text_domain),
					'fields' => self::getResponsiveAlignmentFieldGroup()
				]
			]
		];

		return $tab[ 'structure' ];
	}

	/**
	 * @param null $field_prefix
	 * @param null $field_label_prefix
	 *
	 * @return array Alignment options for responsive and desktop
	 */
	public static function getResponsiveAlignmentFieldGroup($field_prefix = null, $field_label_prefix = null)
	{
		$desktop_field          = isset($field_prefix)
			? $field_prefix . '_alignment'
			: 'alignment';
		$desktop_field_label    = isset($field_label_prefix)
			? $field_label_prefix . ' alignment'
			: 'Alignment';
		$medium_field           = isset($field_prefix)
			? $field_prefix . '_medium_alignment'
			: 'medium_alignment';
		$medium_field_label     = isset($field_label_prefix)
			? $field_label_prefix . ' medium alignment'
			: 'Medium alignment';
		$responsive_field       = isset($field_prefix)
			? $field_prefix . '_responsive_alignment'
			: 'responsive_alignment';
		$responsive_field_label = isset($field_label_prefix)
			? $field_label_prefix . ' responsive alignment'
			: 'Responsive alignment';

		return [
			$desktop_field    => [
				'type'    => 'select',
				'label'   => __($desktop_field_label, Plugin::$config->plugin_text_domain),
				'default' => 'default',
				'options' => self::getAlignmentOptions()
			],
			$medium_field     => [
				'type'    => 'select',
				'label'   => __($medium_field_label, Plugin::$config->plugin_text_domain),
				'default' => 'default',
				'options' => self::getAlignmentOptions()
			],
			$responsive_field => [
				'type'    => 'select',
				'label'   => __($responsive_field_label, Plugin::$config->plugin_text_domain),
				'default' => 'default',
				'options' => self::getAlignmentOptions()
			]
		];
	}

	/**
	 * @return array Generic typography tab of fields
	 */
	public static function getGenericTypographyTab()
	{
		$tab[ 'typography' ] = [
			'title'    => __('Typography', Plugin::$config->plugin_text_domain),
			'sections' => [
				'typography' => [
					'title'  => __('General', Plugin::$config->plugin_text_domain),
					'fields' => self::getGenericTypographyFieldGroup()
				]
			]
		];

		return $tab[ 'typography' ];
	}

	/**
	 * @return array Generic typography fields
	 */
	public static function getGenericTypographyFieldGroup()
	{
		return [
			'font'           => [
				'type'    => 'font',
				'label'   => __('Font', Plugin::$config->plugin_text_domain),
				'default' => [
					'family' => 'Default',
					'weight' => 300
				],
			],
			'font_size'      => [
				'type'        => 'unit',
				'label'       => __('Font size', Plugin::$config->plugin_text_domain),
				'default'     => '',
				'size'        => '4',
				'description' => 'px',
				'responsive'  => true
			],
			'line_height'    => [
				'type'       => 'unit',
				'label'      => __('Line height', Plugin::$config->plugin_text_domain),
				'default'    => '',
				'size'       => '2',
				'responsive' => true
			],
			'letter_spacing' => [
				'type'        => 'unit',
				'label'       => __('Letter spacing', Plugin::$config->plugin_text_domain),
				'default'     => '',
				'size'        => '2',
				'description' => 'px',
				'responsive'  => true
			],
			'text_transform' => [
				'type'    => 'select',
				'label'   => __('Text Transform', Plugin::$config->plugin_text_domain),
				'default' => 'regular',
				'options' => [
					'inherit'   => __('Inherit', Plugin::$config->plugin_text_domain),
					'uppercase' => __('Uppercase', Plugin::$config->plugin_text_domain),
				]
			],
			'text_color'     => [
				'type'       => 'color',
				'label'      => __('Color', Plugin::$config->plugin_text_domain),
				'show_reset' => true,
			]
		];
	}

	/**
	 * @param bool $display_heading_text_margin_fields Choose whether to include heading margin fields
	 *
	 * @return array Heading typography tab of fields
	 */
	public static function getHeadingTypographyTab($display_heading_text_margin_fields = false)
	{
		$tab[ 'typography' ] = [
			'title'    => __('Typography', Plugin::$config->plugin_text_domain),
			'sections' => [
				'typography' => [
					'title'  => __('General', Plugin::$config->plugin_text_domain),
					'fields' => self::getHeadingTypographyFieldGroup($display_heading_text_margin_fields)
				]
			]
		];

		return $tab[ 'typography' ];
	}

	/**
	 * @param bool $display_heading_text_margin_fields Choose whether to include heading margin fields
	 *
	 * @return array Heading typography fields
	 */
	public static function getHeadingTypographyFieldGroup($display_heading_text_margin_fields = false)
	{
		$heading_text_margin_fields = [];

		if ($display_heading_text_margin_fields) {
			$heading_text_margin_fields = [
				'heading_margin_top'    => [
					'type'        => 'unit',
					'label'       => __('Heading text margin top', Plugin::$config->plugin_text_domain),
					'default'     => '',
					'size'        => '3',
					'description' => 'px',
					'responsive'  => true
				],
				'heading_margin_right'  => [
					'type'        => 'unit',
					'label'       => __('Heading text margin right', Plugin::$config->plugin_text_domain),
					'default'     => '',
					'size'        => '3',
					'description' => 'px',
					'responsive'  => true
				],
				'heading_margin_bottom' => [
					'type'        => 'unit',
					'label'       => __('Heading text margin bottom', Plugin::$config->plugin_text_domain),
					'default'     => '',
					'size'        => '3',
					'description' => 'px',
					'responsive'  => true
				],
				'heading_margin_left'   => [
					'type'        => 'unit',
					'label'       => __('Heading text margin left', Plugin::$config->plugin_text_domain),
					'default'     => '',
					'size'        => '3',
					'description' => 'px',
					'responsive'  => true
				]
			];
		}

		return array_merge([
			'heading_tag' => [
				'type'    => 'select',
				'label'   => __('Heading Tag', Plugin::$config->plugin_text_domain),
				'default' => 'h2',
				'options' => self::getHeadingTags(),
			],
		], self::getGenericTypographyFieldGroup(), $heading_text_margin_fields);
	}

	/**
	 * @return array Get fields for a link
	 */
	public static function getLinkFieldGroup()
	{
		return [
			'link_type'    => [
				'type'    => 'select',
				'label'   => __('Link Type', Plugin::$config->plugin_text_domain),
				'default' => 'none',
				'options' => self::getLinkTypes(),
				'toggle'  => [
					'internal'               => [
						'fields' => ['link', 'link_target']
					],
					'external'               => [
						'fields' => ['link', 'link_target']
					],
					'manual'                 => [
						'fields' => ['link', 'link_target']
					],
					'add_wc_product_to_cart' => [
						'fields' => ['product_link']
					],
					'modal'                  => [
						'fields' => ['modal_link']
					],
				]
			],
			'link'         => [
				'type'        => 'link',
				'label'       => __('Link', Plugin::$config->plugin_text_domain),
				'placeholder' => __('http://www.example.com', Plugin::$config->plugin_text_domain),
			],
			'product_link' => [
				'type'   => 'suggest',
				'label'  => __('Select Product', Plugin::$config->plugin_text_domain),
				'action' => 'fl_as_posts',
				'data'   => 'product',
				'limit'  => 1,
			],
			'modal_link'   => [
				'type'   => 'suggest',
				'label'  => __('Select Modal', Plugin::$config->plugin_text_domain),
				'action' => 'fl_as_posts',
				'data'   => 'wpd-bb-modal',
				'limit'  => 1,
			],
			'link_target'  => [
				'type'    => 'select',
				'label'   => __('Link Target', Plugin::$config->plugin_text_domain),
				'default' => '_self',
				'options' => [
					'_self'  => __('Same Window', Plugin::$config->plugin_text_domain),
					'_blank' => __('New Window', Plugin::$config->plugin_text_domain)
				],
			],
		];
	}

	/**
	 * @return array List of link types
	 */
	public static function getLinkTypes()
	{
		$link_options = [
			'none'     => __('None', Plugin::$config->plugin_text_domain),
			'internal' => __('Internal', Plugin::$config->plugin_text_domain),
			'external' => __('External', Plugin::$config->plugin_text_domain),
			'manual'   => __('Manual (eg. mailto:)', Plugin::$config->plugin_text_domain),
		];

		if (class_exists('WooCommerce')) {
			$link_options[ 'add_wc_product_to_cart' ] = __('Add WC product to cart', Plugin::$config->plugin_text_domain);
		}

		if (post_type_exists('wpd-bb-modal')) {
			$link_options[ 'modal' ] = __('Modal', Plugin::$config->plugin_text_domain);
		}

		return $link_options;
	}

	/**
	 * @return array List of heading tags
	 */
	public static function getHeadingTags()
	{
		return [
			'h1' => 'h1',
			'h2' => 'h2',
			'h3' => 'h3',
			'h4' => 'h4',
			'h5' => 'h5',
			'h6' => 'h6'
		];
	}

	/**
	 * @return  array List of alignment options
	 */
	public static function getAlignmentOptions()
	{
		return [
			'default' => __('Default', Plugin::$config->plugin_text_domain),
			'left'    => __('Left', Plugin::$config->plugin_text_domain),
			'center'  => __('Center', Plugin::$config->plugin_text_domain),
			'right'   => __('Right', Plugin::$config->plugin_text_domain)
		];
	}

	/**
	 * @param bool $include_alignment_fields Choose whether to include alignment fields
	 *
	 * @return array Button fields
	 */
	public static function getButtonLayoutFieldGroup($include_alignment_fields = false)
	{
		$button_fields = [
			'button_style'                => [
				'type'    => 'select',
				'label'   => __('Button Style', Plugin::$config->plugin_text_domain),
				'default' => 'btn-primary',
				'options' => self::getButtonStyles()
			],
			'button_size'                 => [
				'type'    => 'select',
				'label'   => __('Button Size', Plugin::$config->plugin_text_domain),
				'default' => 'btn',
				'options' => [
					'btn'        => 'Regular',
					'btn btn-sm' => 'Small',
					'btn btn-lg' => 'Large',
				],
			],
			'button_structure'            => [
				'type'    => 'select',
				'label'   => __('Button Structure', Plugin::$config->plugin_text_domain),
				'default' => 'btn-inline',
				'options' => [
					'btn-inline' => 'Inline',
					'btn-block'  => 'Block',
				],
			],
			'button_structure_responsive' => [
				'type'    => 'select',
				'label'   => __('Responsive Button Structure', Plugin::$config->plugin_text_domain),
				'default' => 'btn-inline',
				'options' => [
					'btn-inline' => 'Inline',
					'btn-block'  => 'Block',
				],
			],
		];

		if ($include_alignment_fields) {
			$button_fields = array_merge($button_fields, self::getResponsiveAlignmentFieldGroup('button', 'Button'));
		}

		return $button_fields;
	}

	/**
	 * @return array Array of button CSS classes
	 */
	public static function getButtonStyles()
	{
		return [
			'btn-primary'           => __('Primary', Plugin::$config->plugin_text_domain),
			'btn-outline-primary'   => __('Primary (outline)', Plugin::$config->plugin_text_domain),
			'btn-secondary'         => __('Secondary', Plugin::$config->plugin_text_domain),
			'btn-outline-secondary' => __('Secondary (outline)', Plugin::$config->plugin_text_domain),
			'btn-info'              => __('Info', Plugin::$config->plugin_text_domain),
			'btn-outline-info'      => __('Info (outline)', Plugin::$config->plugin_text_domain),
			'btn-success'           => __('Success', Plugin::$config->plugin_text_domain),
			'btn-outline-success'   => __('Success (outline)', Plugin::$config->plugin_text_domain),
			'btn-warning'           => __('Warning', Plugin::$config->plugin_text_domain),
			'btn-outline-warning'   => __('Warning (outline)', Plugin::$config->plugin_text_domain),
			'btn-danger'            => __('Danger', Plugin::$config->plugin_text_domain),
			'btn-outline-danger'    => __('Danger (outline)', Plugin::$config->plugin_text_domain),
		];
	}
}
