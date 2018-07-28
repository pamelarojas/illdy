jQuery(window).load(function() {	
	
	// add colour pickers
	jQuery('.colourme').wpColorPicker();
		
	/* ---------------------------------------------------------------------------------------------------*/
	/* !DEVICES */
	/* ---------------------------------------------------------------------------------------------------*/

	// get device elements
	var devices = jQuery('#rpb_devices > div');
	
	// cycle devices and set "active" when the size is under the #mobile_size_input value
	function activateDevices() {
		var sizeInput = jQuery('#mobile_size_input').val();
		devices.each(function() {
			console.log(jQuery(this).attr('data-size'));
			if (jQuery(this).attr('data-size') <= parseInt(sizeInput))
				jQuery(this).addClass('active')
			else
				jQuery(this).removeClass('active')
		})
	}
	
	// when we click a device
	devices.click(function(){
		// set the #mobile_size_input value
		jQuery('#mobile_size_input').val(jQuery(this).attr('data-size'))
		// re run the active
		activateDevices();
	})
	
	// run on load
	activateDevices();


	/* ---------------------------------------------------------------------------------------------------*/	
	/* !LOCATION TOGGLE */
	/* ---------------------------------------------------------------------------------------------------*/

	// run on load
	rpb_show_location_feilds();
	
	// run on change
	jQuery('input[name="RPB_options[location]"]').change(rpb_show_location_feilds)
	
	// show ADDRESS or GPS inputs
	function rpb_show_location_feilds() {
		jQuery('.location_option').hide();
		jQuery('.location_option[data-type='+jQuery('input[name="RPB_options[location]"]:checked').val()+']').show();
	}

});