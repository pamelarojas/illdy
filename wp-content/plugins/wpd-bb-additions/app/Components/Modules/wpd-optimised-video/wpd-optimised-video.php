<?php

namespace WPD\BBAdditions\Components\Modules;

use WPD\BBAdditions\Plugin;
use WPD\BBAdditions\Utils\FieldGroups;

/**
 * Class WPDOptimisedVideo
 *
 * @package WPD\BBAdditions\Modules
 */
class WPDOptimisedVideo extends Base
{

	/**
	 * WPDOptimisedVideo constructor.
	 */
	public function __construct()
	{
		parent::__construct([
			'name'            => __('Optimised Video Embed', Plugin::$config->plugin_text_domain),
			'description'     => __('A faster loading video embed.', Plugin::$config->plugin_text_domain),
			'category'        => __('WPD Modules', Plugin::$config->plugin_text_domain),
			'group'           => __('WPD Modules', Plugin::$config->plugin_text_domain),
			'partial_refresh' => false,
			'slug'            => 'wpd-optimised-video'
		]);

		add_filter('wp_resource_hints', [$this, 'set_resource_hints'], 10, 2);
	}

	/**
	 * Overriding the default Beaver Builder method which doesn't
	 * work yet
	 *
	 * @since 1.9.2
	 *
	 * @param null $icon
	 *
	 * @return bool|String
	 */
	public function get_icon($icon = null)
	{
		return file_exists(dirname(__FILE__) . '/icon.svg') ? file_get_contents(dirname(__FILE__) . '/icon.svg') : $this->get_default_icon();
	}

	/**
	 * @param $urls
	 * @param $relation_type
	 *
	 * @return array
	 */
	public function set_resource_hints($urls, $relation_type)
	{
		$prefetch = [];

		if ($video_data = $this->get_video_data()) {
			if ('youtube' === $video_data[ 'type' ]) {
				$prefetch[] = 'www.youtube.com';
				$prefetch[] = 's.ytimg.com';
			}
			else if ('vimeo' === $video_data[ 'type' ]) {
				$prefetch[] = 'www.vimeo.com';
				$prefetch[] = 'player.vimeo.com';
				$prefetch[] = 'f.vimeocdn.com';
			}
		}

		if ('dns-prefetch' === $relation_type) {
			$urls = array_merge($urls, $prefetch);
		}

		return array_unique($urls);
	}

	/**
	 * @return array|bool
	 */
	public function get_video_data()
	{
		if (is_object($this->settings) && isset($this->settings->video_url)) {

			if (method_exists('FLBuilderUtils', 'get_video_data')) {
				$video_data = \FLBuilderUtils::get_video_data($this->settings->video_url);
			}
			else {
				// Temp as above method FLBuilderUtils::get_video_data was only introducted in 1.9
				$y_matches  = [];
				$vm_matches = [];
				$yt_pattern = '/^(?:(?:(?:https?:)?\/\/)?(?:www.)?(?:youtu(?:be.com|.be))\/(?:watch\?v\=|v\/|embed\/)?([\w\-]+))/is';
				$vm_pattern = '#(?:https?://)?(?:www.)?(?:player.)?vimeo.com/(?:[a-z]*/)*([0-9]{6,11})[?]?.*#';
				$video_data = ['type' => 'mp4', 'video_id' => ''];

				preg_match($yt_pattern, $this->settings->video_url, $yt_matches);
				preg_match($vm_pattern, $this->settings->video_url, $vm_matches);

				if (isset($yt_matches [ 1 ])) {
					$video_data[ 'type' ]     = 'youtube';
					$video_data[ 'video_id' ] = $yt_matches[ 1 ];
				}
				elseif (isset($vm_matches[ 1 ])) {
					$video_data[ 'type' ]     = 'vimeo';
					$video_data[ 'video_id' ] = $vm_matches[ 1 ];
				}
			}

			return $video_data;
		}

		return false;
	}

	/**
	 *
	 */
	public function enqueue_scripts()
	{
		if ($this->settings && isset( $this->settings->open_video_in_lightbox ) && $this->settings->open_video_in_lightbox === 'yes') {
			$this->add_js('jquery-magnificpopup');
			$this->add_css('jquery-magnificpopup');
		}
	}
}

\FLBuilder::register_module(__NAMESPACE__ . '\WPDOptimisedVideo', [
	'general'       => [
		'title'    => __('General', Plugin::$config->plugin_text_domain),
		'sections' => [
			'video'                  => [
				'title'  => __('Video', Plugin::$config->plugin_text_domain),
				'fields' => [
					'video_url' => [
						'type'        => 'text',
						'label'       => __('Video URL', Plugin::$config->plugin_text_domain),
						'description' => __('YouTube or Vimeo', Plugin::$config->plugin_text_domain),
						'connections' => ['url'],
					],
				],
			],
			'video_url_parameters'   => [
				'title'  => __('Video Parameters', Plugin::$config->plugin_text_domain),
				'fields' => [
					'video_url_parameters' => [
						'type'        => 'text',
						'label'       => __('Parameter', Plugin::$config->plugin_text_domain),
						'description' => __('Add your own URL parameter (eg. rel=0)', Plugin::$config->plugin_text_domain),
						'placeholder' => __('rel=0', Plugin::$config->plugin_text_domain),
						'multiple'    => true,
					],
				],
			],
			'thumbnail'              => [
				'title'  => __('Thumbnail Settings', Plugin::$config->plugin_text_domain),
				'fields' => array_merge([
					'use_custom_thumbnail'      => [
						'type'    => 'select',
						'label'   => __('Use custom thumbnail', Plugin::$config->plugin_text_domain),
						'options' => [
							'yes' => __('Yes', Plugin::$config->plugin_text_domain),
							'no'  => __('No', Plugin::$config->plugin_text_domain)
						],
						'default' => 'no',
						'toggle'  => [
							'yes' => [
								'fields' => ['custom_thumbnail_image']
							],
							'no'  => [
								'fields' => ['thumbnail_quality']
							]
						]
					],
					'custom_thumbnail_image'    => [
						'type'        => 'photo',
						'label'       => __('Image', Plugin::$config->plugin_text_domain),
						'description' => __('Upload the Image', Plugin::$config->plugin_text_domain),
						'connections' => ['photo', 'custom_field'],
					],
					'thumbnail_quality'         => [
						'type'    => 'select',
						'label'   => __('Thumbnail Quality', Plugin::$config->plugin_text_domain),
						'options' => [
							'regular' => __('Regular', Plugin::$config->plugin_text_domain),
							'high'    => __('High', Plugin::$config->plugin_text_domain)
						],
						'default' => 'regular'
					],
					'display_thumbnail_overlay' => [
						'type'    => 'select',
						'label'   => __('Display Thumbnail Overlay', Plugin::$config->plugin_text_domain),
						'options' => [
							'no'  => __('No', Plugin::$config->plugin_text_domain),
							'yes' => __('Yes', Plugin::$config->plugin_text_domain),
						],
						'toggle'  => [
							'yes' => [
								'fields' => ['thumbnail_overlay_color', 'thumbnail_overlay_opacity']
							]
						],
						'default' => 'no'
					],
					'thumbnail_overlay_color'   => [
						'type'       => 'color',
						'label'      => __('Overlay Color', Plugin::$config->plugin_text_domain),
						'show_reset' => true,
						'preview'    => [
							'type'     => 'css',
							'selector' => '.wpd-optimised-video__thumbnail-overlay',
							'property' => 'background-color'
						]
					],
					'thumbnail_overlay_opacity' => [
						'type'        => 'text',
						'label'       => __('Overlay Opacity', Plugin::$config->plugin_text_domain),
						'description' => '%',
						'maxlength'   => '3',
						'size'        => '4',
						'preview'     => [
							'type'     => 'css',
							'selector' => '.wpd-optimised-video__thumbnail-overlay',
							'property' => 'opacity',
							'unit'     => '%'
						]
					]
				], FieldGroups::getCssFilterFieldGroup())
			],
			'play_button'            => [
				'title'  => __('Play Button', Plugin::$config->plugin_text_domain),
				'fields' => FieldGroups::getIconFieldGroup()
			],
			'aspect_ratio'           => [
				'title'  => __('Aspect Ratio', Plugin::$config->plugin_text_domain),
				'fields' => [
					'change_default_aspect_ratio' => [
						'label'   => __('Aspect Ratio', Plugin::$config->plugin_text_domain),
						'type'    => 'select',
						'options' => [
							'default' => __('Default (16:9)', Plugin::$config->plugin_text_domain),
							'custom'  => __('Custom', Plugin::$config->plugin_text_domain),
						],
						'default' => 'default',
						'toggle'  => [
							'custom' => [
								'fields' => ['aspect_ratio_height', 'aspect_ratio_width']
							],
						],
					],
					'aspect_ratio_width'          => [
						'label'       => __('Width', Plugin::$config->plugin_text_domain),
						'type'        => 'unit',
						'description' => __('Width ratio', Plugin::$config->plugin_text_domain)
					],
					'aspect_ratio_height'         => [
						'label'       => __('Height', Plugin::$config->plugin_text_domain),
						'type'        => 'unit',
						'description' => __('Height ratio', Plugin::$config->plugin_text_domain)
					],
				]
			],
			'open_video_in_lightbox' => [
				'title'  => __('Open Video in Lightbox', Plugin::$config->plugin_text_domain),
				'fields' => [
					'open_video_in_lightbox' => [
						'type'    => 'select',
						'label'   => __('Open Video in Lightbox', Plugin::$config->plugin_text_domain),
						'default' => 'no',
						'options' => [
							'yes' => __('Enable', Plugin::$config->plugin_text_domain),
							'no'  => __('Disable', Plugin::$config->plugin_text_domain)
						],
						'toggle'  => [
							'yes' => [],
							'no'  => [
								'tabs' => ['buttons']
							]
						]
					]
				]
			]
		],
	],
	'custom_styles' => [
		'title'    => __('Styles', Plugin::$config->plugin_text_domain),
		'sections' => [
			'custom_module_css' => [
				'title'  => __('Custom CSS', Plugin::$config->plugin_text_domain),
				'fields' => [
					'custom_module_css' => [
						'label'  => __('Custom CSS', Plugin::$config->plugin_text_domain),
						'help'   => __('Add CSS here to automatically apply it to the module \<div\>, without using the node ID. Great for background colours', Plugin::$config->plugin_text_domain),
						'type'   => 'code',
						'editor' => 'html',
						'rows'   => '10'
					]
				],
			],
		]
	],
	'buttons'       => [
		'title'    => __('Button', Plugin::$config->plugin_text_domain),
		'sections' => [
			'enable_delayed_cta' => [
				'title'       => __('Enable Delayed Call to Action', Plugin::$config->plugin_text_domain),
				'description' => __('Display a call to action (button) after a certain number of seconds after the visitor clicks play on the video', Plugin::$config->plugin_text_domain),
				'fields'      => [
					'enable_delayed_cta' => [
						'label'   => __('Enable Delayed Button', Plugin::$config->plugin_text_domain),
						'type'    => 'select',
						'options' => [
							'yes' => __('Yes', Plugin::$config->plugin_text_domain),
							'no'  => __('No', Plugin::$config->plugin_text_domain)
						],
						'default' => 'no',
						'toggle'  => [
							'yes' => [
								'fields' => [
									'delay_time',
									'reveal_animation',
									'button_placement',
									'button_spacing',
									'regular_bb_button'
								]
							]
						]
					],
					'delay_time'         => [
						'label'       => __('Delay', Plugin::$config->plugin_text_domain),
						'type'        => 'unit',
						'description' => 'seconds',
						'placeholder' => '2'
					],
					'reveal_animation'   => [
						'label'   => __('Reveal Animation', Plugin::$config->plugin_text_domain),
						'type'    => 'select',
						'options' => [
							'fade'    => __('Fade', Plugin::$config->plugin_text_domain),
							'slideUp' => __('Slide Up', Plugin::$config->plugin_text_domain)
						],
						'default' => 'fade'
					],
					'button_placement'   => [
						'label'   => __('Button Placement', Plugin::$config->plugin_text_domain),
						'type'    => 'select',
						'options' => [
							'above' => __('Above', Plugin::$config->plugin_text_domain),
							'below' => __('Below', Plugin::$config->plugin_text_domain),
						],
						'default' => 'below'
					],
					'button_spacing'     => [
						'label'       => __('Spacing between button and video', Plugin::$config->plugin_text_domain),
						'type'        => 'unit',
						'description' => 'px'
					],
					'regular_bb_button'  => [
						'type'         => 'form',
						'label'        => __('BB Button', Plugin::$config->plugin_text_domain),
						'form'         => 'wpd_bb_button_form',
						'preview_text' => 'text',
					]
				],
			],
		]
	]
]);


