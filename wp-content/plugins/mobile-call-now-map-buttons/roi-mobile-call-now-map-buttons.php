<?php
/*
Plugin Name: 	Mobile Call Now & Map Buttons
Plugin URI: 	https://wordpress.org/plugins/mobile-call-now-map-buttons/
Description: 	Adds custom "Call Now" and/or Google "Directions" buttons for mobile visitors.
Tags: 			mobile, cell, responsive, buttons, phone, call, contact, map, location, directions, customize
Author URI: 	https://davidsword.ca/
Author: 		davidsword
Version: 		1.5.0
License: 		GPLv3
Text Domain:    rpb
*/

defined( 'ABSPATH' ) || exit; // Exit if accessed directly

// Huston ..we have lift off.
add_action( 'init', function() {
	new rpb();
} );

class rpb {
	public $menu_id;

	/**
	 * Plugin initialization
	 *
	 * @since 1.0
	 */
	public function __construct() {

		// localization
		load_plugin_textdomain( 'rpb' );

		// admin
		add_action( 'admin_menu', array( $this, 'rpb_add_admin_menu' ));
		add_action( 'admin_enqueue_scripts', array( $this, 'rpb_admin_scripts' ));

		// create needed initialization
		add_action('admin_init', array( $this, 'rpb_register_options_settings') );

		// create custom footer
		add_action('wp_footer', array( $this, 'rpb_add_buttons'), 10);

		// grab the options, use for entire object
		$this->rpb_options = $this->rpb_options();
	}

	/**
	 * Add Menu Page
	 *
	 * @since 1.0
	 */
	public function rpb_add_admin_menu() {
    	add_options_page('Settings Page for Mobile Call Now & Map Plugin', 'Mobile Call Now & Map Buttons', 'publish_posts', 'mobile_contact_buttons', array($this,'rpb_options_page'),'');
	}

	/**
	 * Add Resources
	 *
	 * @since 1.0
	 */
	function rpb_admin_scripts() {

		if (get_current_screen()->base == 'settings_page_mobile_contact_buttons') {
	        wp_register_style( 'rpb_css', plugins_url('rpb-admin.css', __FILE__), false, filemtime( plugin_dir_path( __FILE__ ) . 'rpb-admin.css' ) );
	        wp_enqueue_style( 'rpb_css' );

	        wp_register_script( 'rpb_js', plugins_url('rpb-admin.js', __FILE__), array('jquery'), filemtime( plugin_dir_path( __FILE__ ) . 'rpb-admin.js' ), true );
	        wp_enqueue_script( 'rpb_js' );

		    wp_enqueue_style('wp-color-picker');
		    wp_enqueue_script('iris', admin_url('js/iris.min.js'),array('jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch'), false, 1);
		    wp_enqueue_script('wp-color-picker', admin_url('js/color-picker.min.js'), array('iris'), false, 1);
	    }
	}


	/**
	 * Whitelist Options
	 *
	 * @since 0.1
	 */
	function rpb_register_options_settings() {
	    register_setting( 'rpb_custom_options-group', 'RPB_options' );
	}


	/**
	 * Options Page
	 *
	 * @since 0.1
	 */
	function rpb_options_page() {
		global $_wp_admin_css_colors, $wp_version;

		// access control
	    if ( !(isset($_GET['page']) && $_GET['page'] == 'mobile_contact_buttons' ))
	    	return;
		?>

		<div class='wrap' id='rpb'>
			<h2><?php _e('Mobile Call Now & Map Buttons','rpb') ?></h2>
			<form method="post" action="options.php" class="form-table">
				<?php
				wp_nonce_field('RPB_options');
				settings_fields('rpb_custom_options-group');
				?>
				<input type="hidden" name="action" value="update" />
				<input type="hidden" name="page_options" value="RPB_options" />



				<h2 class='title'><?php _e('Settings','rpb') ?></h2>
				<p><?php _e('Select the range of devices you wish to have the "Call Now" and "Directions" buttons show on.','rpb') ?></p>
				<table border=0 cellpadding=2 cellspacing="2">
				<tr>
				<th>
					<br /><?php _e('Display on','rpb') ?>
				</th>
				<td>
					<div id='rpb_devices'>
						<div id='iphone_se' data-size='320' class='rpb_device'>
							<span></span>
							<span>320px</span>
						</div>
						<div id='iphone_6s' data-size='375' class='rpb_device'>
							<span></span>
							<span>375px</span>
						</div>
						<div id='iphone_6sp' data-size='414' class='rpb_device'>
							<span></span>
							<span>414px</span>
						</div>
						<div id='default' data-size='680' class='rpb_device'>
							<span></span>
							<span>680px</span>
						</div>
						<div id='ipad_air2' data-size='768' class='rpb_device'>
							<span></span>
							<span>768px</span>
						</div>
						<div id='ipad_pro' data-size='1024' class='rpb_device'>
							<span></span>
							<span>1024px</span>
						</div>
					</div>
					<br class='clear' />
					<br class='clear' />
					<input type="hidden" size=4 id="mobile_size_input" name="RPB_options[mobile_size]" value='<?php echo $this->rpb_options['mobile_size']; ?>' />
				</td>
				</tr>
					<th>
						<?php _e('Bar Background','rpb') ?>
					</th>
					<td>
						<input type="text" class="colourme" name="RPB_options[bg_color]" value="<?php echo $this->rpb_options['bg_color']; ?>">
					</td>
				</tr>
				</table>

				<h2 class='title'><?php _e('Call Now Button','rpb') ?></h2>
				<p><?php _e('By adding a phone number, the "Call Now" button will display when viewing the site with a device in the "Display On" range.','rpb') ?></p>
				<table border=0 cellpadding=2 cellspacing="2">
				<tr>
				<th>
					<?php _e('Text','rpb') ?>
				</th>
				<td>
					<input type="text" id="callbutton_text" name="RPB_options[call_text]" value="<?php echo $this->rpb_options['call_text'] ?>" placeholder="Call Now" /><br />
					<input type="text" class="colourme" name="RPB_options[call_text_color]" value="<?php echo $this->rpb_options['call_text_color']; ?>">
				</td>
				</tr>


				<tr>
					<th><?php _e('Button','rpb') ?></th>
					<td>
						<input type="text" class="colourme" name="RPB_options[call_color]" value="<?php echo $this->rpb_options['call_color']; ?>">
					</td>
				</tr>


				<tr>
					<th><?php _e('Phone Number','rpb') ?></th>
					<td>
						<input name="RPB_options[phone_number]" placeholder="555-555-5555" value='<?php echo $this->rpb_options['phone_number']; ?>' /><br />
					</td>
				</tr>
				</table>


				<h2 class='title'><?php _e('Map Button','rpb') ?></h2>
				<p><?php _e('By adding an Address, the "Directions" button will display. The Directions links to a google.com/maps query.','rpb') ?></p>
				<table border=0 cellpadding=2 cellspacing="2">
				<tr>
				<th>
					<?php _e('Text','rpb') ?>
				</th>
				<td>
					<input type="text" id="mapbutton_text" name="RPB_options[map_text]" value="<?php echo $this->rpb_options['map_text']; ?>" placeholder="Directions"/><br />
					<input type="text" class="colourme" name="RPB_options[map_text_color]" value="<?php echo $this->rpb_options['map_text_color']; ?>">
				</td>
				</tr>
				<tr>
					<th><?php _e('Button','rpb') ?></th>
					<td>
						<input type="text" class="colourme" name="RPB_options[map_color]" value="<?php echo $this->rpb_options['map_color']; ?>">
					</td>
				</tr>
				<tr>
					<th><?php _e('Location','rpb') ?></th>
					<td>
						<label>
							<input type='radio' name='RPB_options[location]' value='address' <?php echo ($this->rpb_options['location'] == 'address') ? 'checked' : '' ?> />
							<?php _e('Street Address','rpb') ?>
						</label> &nbsp; &nbsp;
						<label>
							<input type='radio' name='RPB_options[location]' value='gps' <?php echo ($this->rpb_options['location'] == 'gps') ? 'checked' : '' ?> />
							<?php _e('GPS Coordinates','rpb') ?>
						</label>

						<div class='location_option' data-type='address'>
							<input name="RPB_options[street]" value='<?php echo $this->rpb_options['street'] ?>'      style='width:98.5%' placeholder="<?php _e('Number and Street','rpb') ?>" /><br />
							<input name="RPB_options[city]" value='<?php echo $this->rpb_options['city'] ?>'        style='width:67%' placeholder="<?php _e('City','rpb') ?>"/>
							<input name="RPB_options[province]" value='<?php echo $this->rpb_options['province'] ?>'    style='width:30%' placeholder="<?php _e('Province / State','rpb') ?>"/><br />
							<input name="RPB_options[country]" value='<?php echo $this->rpb_options['country'] ?>'     style='width:47%' placeholder="<?php _e('Country','rpb') ?>"/>
							<input name="RPB_options[postal_code]" value='<?php echo $this->rpb_options["postal_code"] ?>' style='width:50%' placeholder="<?php _e('Postal / Zip Code','rpb') ?>"/>
						</div><!--/location_option-->

						<div class='location_option' data-type='gps'>
							<label class='label'><?php _e('Latitude','rpb') ?>:</label>
							<input name="RPB_options[lat]" class='code' value='<?php echo $this->rpb_sanitize_gps($this->rpb_options["lat"]) ?>' placeholder="dd.ddddd"/><br />
							<label class='label'><?php _e('Longitude','rpb') ?>:</label>
							<input name="RPB_options[lng]" class='code' value='<?php echo $this->rpb_sanitize_gps($this->rpb_options["lng"]) ?>'  placeholder="dd.ddddd"/>
					</td>
				</tr>

				</table>

				<h2 class='title'><?php _e('Advanced Options','rpb') ?></h2>
				<p style='max-width:640px;'><?php _e('Something not working for your theme or setup? We\'ve got a few advanced settings. For further information on the settings see','rpb') ?> <a href='https://wordpress.org/plugins/mobile-call-now-map-buttons/#faq' target='_Blank'><?php _e('FAQ Section','rpb') ?></a> <?php _e('If you\'re still having troubles please open a','rpb') ?> <a href='https://wordpress.org/support/plugin/mobile-call-now-map-buttons#new-post' target='_Blank'><?php _e('support request','rpb') ?></a> <?php _e('with the Profile code below, and we\'ll try to help you out.','rpb') ?></p>

				<table border=0 cellpadding=2 cellspacing="2">
				<tr>
				<th>
					<?php _e('z-index','rpb') ?>
				</th>
				<td>
					<input type="number" id="zindex" name="RPB_options[zindex]" value="<?php echo $this->rpb_options['zindex']; ?>" placeholder="998"/><br />
				</td>
				</tr>

				<tr>
				<th>
					<?php _e('Append to body','rpb') ?>
				</th>
				<td>
					<label><input type='checkbox' name="RPB_options[forcebtm]" value='1' <?php echo (isset($this->rpb_options['forcebtm']) && $this->rpb_options['forcebtm'] == '1') ? " checked" : '' ?> /> <?php _e('Move plugin to absolute bottom','rpb') ?></label><br />
				</td>
				</tr>


				<tr>
				<th>
					<?php _e('Number Sanitizing','rpb') ?>
				</th>
				<td>
					<label><input type='checkbox' name="RPB_options[nosanitizing]" value='1' <?php echo (isset($this->rpb_options['nosanitizing']) && $this->rpb_options['nosanitizing'] == '1') ? " checked" : '' ?> /> <?php _e("Don't reformat my phone number",'rpb') ?></label><br />
				</td>
				</tr>


				<tr>
				<th>
					<?php _e('Profile','rpb') ?>
				</th>
				<td>
				<div class='code'><?php
					$my_theme = wp_get_theme();
					$setup = array(
							'PHP' => phpversion(),
							'Wordpress' => $wp_version,
							'Theme' => $my_theme->get( 'Name' )." (".get_option('template').") ".$my_theme->get( 'Version' ),
							'URL' => str_replace(array('http://','https://','www.'),'',get_option('home')),
							'Plugins' => get_option('active_plugins')
						);
					echo "`<br />".json_encode($setup)."<br />`";

				?></div>
				</td>
				</tr>

				</table>


				<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e('Save Changes','rpb') ?>" />
				</p>
			</form>
			<p id='streetcred'>
				<?php _e('Plugin By','rpb') ?>
				<a href='https://lakkineni.com/' target='_Blank'>V. K. Lakkineni</a> &amp; <a href='https://davidsword.ca/' target='_Blank'>David Sword</a>
			</p>
		</div>
		<style>
			<?php
			$primc = $_wp_admin_css_colors[get_user_option( 'admin_color' )]->colors[2];
			?>
			#rpb #rpb_devices > div.active span:nth-child(1) { border-color: <?php echo $primc ?> !important; }
			#rpb #rpb_devices > div.active span:nth-child(2) { color: <?php echo $primc ?> !important; }
		</style>
	  	<?php
	}


	// adding custom footer to the mobile version
	function rpb_add_buttons() {

		$return =  "\n\n
			<!-- Mobile Call Now and Map Buttons -->";

		// make sure we have some settings to work with
		if ($this->rpb_haveInfo()) {

			// adding the enque here will setup the style to print in wp_footer
			wp_enqueue_style( 'rpb_css', plugins_url('rpb.css', __FILE__) , 'dashicons' );

			// we need dashicons since 1.0 for color-changeable icons
			wp_enqueue_style( 'dashicons' );

			// ladies and gentleman, our buttons:
			$return .=  "
			<div id='rpb_spacer'></div>
			<div id='rpb'>";

			if ( !empty($this->rpb_options['phone_number']) ) {
				$return .= "
				<div>
					<a href='tel:".$this->rpb_sanitize_phone($this->rpb_options['phone_number'])."' id='call_now'
						onClick= \" ga('send', 'event', 'Phone Call', 'Click to Call', '".$this->rpb_sanitize_phone($this->rpb_options['phone_number'])."'); \" >
						<span class='dashicons dashicons-phone'></span> {$this->rpb_options['call_text']}
					</a>
				</div>";
			}

			$directions = $this->rpb_google_map_url();
			if ( !empty($directions) ) {
				$return .= "
				<div>
					<a href='{$directions}' id='map_now' target='_Blank'>
						<span class='dashicons dashicons-location'></span> {$this->rpb_options['map_text']}
					</a>
				</div>";
			}
			$return .= "
			</div>
			<style>
				@media screen and (max-width: {$this->rpb_options['mobile_size']}px) {
				div#rpb { display: flex !important; background: {$this->rpb_options['bg_color']}; }
				div#rpb_spacer { display: block !important; }
				}
				div#rpb { background: {$this->rpb_options['bg_color']}; }
			    div#rpb div a#call_now { background: {$this->rpb_options['call_color']}; color: {$this->rpb_options['call_text_color']}; }
				div#rpb div a#map_now { background: {$this->rpb_options['map_color']}; color: {$this->rpb_options['map_text_color']}; }";
			if (!empty($this->rpb_options['zindex'])) {
				$return .= "
				div#rpb { z-index: ".intval($this->rpb_options['zindex'])." !important;} ";
			}
			$return .= "
			</style>";
		} else {

			// since there's user inputs, there's a high chance of user error,
			// we'll add this in to help with support requests
			$return .= "
			<!-- ".__('Plugin not configured properly. Missing Phone Number or Address values.','rpb')." -->\n";
		}

		$return .= "
			<!-- /Mobile Call Now and Map Buttons -->\n\n";

		if ($this->rpb_options['forcebtm'] == '1') {
			$return .= "
				<script type='text/javascript'>
				setTimeout(function(){
					document.body.appendChild(document.getElementById('rpb_spacer'));
					document.body.appendChild(document.getElementById('rpb'));
				}, 500);
				</script>
			";
		}

		echo apply_filters('rpb_output',$return);
	}


	/**
	 * Options helper
	 *
	 * @since 1.0
	 */
	function rpb_options() {
		$defaults = array(
			'mobile_size' 	 => '680',
			'bg_color' 		 => '#1a1919',

			'call_text'   	 => __('Call Now','rpb'),
			'call_color' 	 => '#0c3',
			'call_text_color'=> '#fff',
			'phone_number' 	 => '',

			'map_text'   	 => __('Directions','rpb'),
			'map_text_color' => '#fff',
			'map_color' 	 => '#fc3',
			'location'       => 'address',

			'street' 		 => '',
			'city' 			 => '',
			'province' 		 => '',
			'country' 		 => '',
			'postal_code' 	 => '',

			'lat' 	         => '',
			'lng' 	         => '',

			'zindex'		 => '',
			'forcebtm' 		 => '',
			'nosanitizing'   => ''
		);

		// get user options
		$RPB_options = get_option('RPB_options');

		// if the user hasn't made settings yet, default
		if (is_array($RPB_options)) {
			// lets make sure we have a value for each as some might be new
			foreach ($defaults as $k => $v)
				if (!isset($RPB_options[$k]) || empty($RPB_options[$k]))
					$RPB_options[$k] = $v;
		}
		// must be first, lets use defaults
		else {
			$RPB_options = $defaults;
		}

		return $RPB_options;
	}

	/**
	 * Validator helper, need city or phone number at very least
	 *
	 * @since 1.0
	 */
	function rpb_haveInfo() {
		return ((isset($this->rpb_options['phone_number']) && !empty($this->rpb_options['phone_number'])) OR (isset($this->rpb_options['city']) && !empty($this->rpb_options['city']))) ? true : false;
	}


	/**
	 * helper, generates link to Google Maps from given parameters
	 *
	 * @since 1.0
	 */
	function rpb_google_map_url(){
		$map_link = "https://maps.google.com/?q=";
		$sepp = ", ";
		if ($this->rpb_options['location'] == 'address') {
			$location = $this->rpb_options['street']. $sepp;
			$location .= $this->rpb_options['city']. $sepp;
			$location .= $this->rpb_options['province']. $sepp;
			$location .= $this->rpb_options['country']. $sepp;
			$location .= $this->rpb_options["postal_code"];
		} else {
			$location = $this->rpb_sanitize_gps($this->rpb_options['lat']). $sepp;
			$location .= $this->rpb_sanitize_gps($this->rpb_options['lng']);
		}

		// make sure we have a location valye, then send it
		$checkData = trim(str_replace($sepp,'',$location));
		if ( !empty($checkData) ) {
			$map_link .= urlencode($location);
			return $map_link;
		} else {
			return NULL;
		}
	}


	/**
	 * helper, clean phone
	 *
	 * @since 1.0
	 */
	function rpb_sanitize_phone($number) {
		if ($this->rpb_options['nosanitizing'] == '1')
			return $number;
		else
			return str_replace( array(' ','(',')','.'), array('','','-','-'), $number);
	}

	function rpb_sanitize_gps($coord) {
		return str_replace( array('°','N','E','S','W',' '), '', $coord);
	}



}
?>
