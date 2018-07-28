<div class="wpd-optimised-video">
	<?php if ('yes' === $settings->enable_delayed_cta && 'above' === $settings->button_placement) :
		echo '<div class="wpd-optimised-video__timed-cta-button-container">';
		FLBuilder::render_module_html('button', $settings->regular_bb_button);
		echo '</div>';
	endif; ?>
    <div class="wpd-optimised-video__player">
        <div class="wpd-optimised-video__thumbnail-container">
			<?php if ('yes' === $settings->display_thumbnail_overlay) : ?>
                <div class="wpd-optimised-video__thumbnail-overlay"></div>
			<?php endif; ?>
        </div>
    </div>
	<?php if ('yes' === $settings->enable_delayed_cta && 'below' === $settings->button_placement) :
		echo '<div class="wpd-optimised-video__timed-cta-button-container">';
		\FLBuilder::render_module_html('button', $settings->regular_bb_button);
		echo '</div>';
	endif; ?>
</div>
