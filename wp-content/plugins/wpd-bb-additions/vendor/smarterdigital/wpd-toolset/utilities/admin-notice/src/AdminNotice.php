<?php

namespace WPD\Toolset\Utilities;

use WPD\Toolset\Package;

final class AdminNotice extends Package
{
	/**
	 * Output notification in admin area
	 *
	 * @since   1.0.0
	 *
	 * @param   string $message The message text
	 * @param   string $type    Allowed types are 'info', 'warning', 'error'
	 *
	 * @return  void
	 */
	public static function add( $message, $type = 'info' ) {
		add_action( 'admin_notices', function () use ( $message, $type ) {
			?>
			<div class="notice notice-<?php echo $type; ?>">
				<p><?php echo $message; ?></p>
			</div>
			<?php
		} );
	}
}