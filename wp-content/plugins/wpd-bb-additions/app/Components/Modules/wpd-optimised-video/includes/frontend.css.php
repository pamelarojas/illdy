<?php
use  WPD\Toolset\Utilities\Color;
use WPD\BBAdditions\Utils\FieldGroups;
?>

.fl-node-<?php echo $id; ?> i.wpd-optimised-video__play-button {
    <?php if ( $settings->icon_color ) : ?>
        color: <?php echo Color::getRgbaOrHex( $settings->icon_color ); ?>;
        transition: color 0.2s linear;
    <?php endif; ?>
}

<?php if ( $settings->icon_color && strpos( $settings->icon_color, 'rgba' ) !== false ) : ?>
    .fl-node-<?php echo $id; ?> .wpd-optimised-video__player:hover i.wpd-optimised-video__play-button {
        color: <?php echo Color::removeAlphaFromRgba( $settings->icon_color ); ?>;
    }
<?php endif; ?>

<?php if ( $settings->icon_size ) : ?>
    .fl-node-<?php echo $id; ?> i.wpd-optimised-video__play-button {
        font-size: <?php echo $settings->icon_size; ?>px;
    }
<?php endif; ?>

<?php if ( $settings->icon_size_medium ) : ?>
    @media (max-width: <?php echo $global_settings->medium_breakpoint; ?>px) {
        .fl-node-<?php echo $id; ?> i.wpd-optimised-video__play-button {
            font-size: <?php echo $settings->icon_size_medium; ?>px;
        }
    }
<?php endif; ?>

<?php if ( $settings->icon_size_responsive ) : ?>
    @media (max-width: <?php echo $global_settings->responsive_breakpoint; ?>px) {
        .fl-node-<?php echo $id; ?> i.wpd-optimised-video__play-button {
            font-size: <?php echo $settings->icon_size_responsive; ?>px;
        }
    }
<?php endif; ?>

<?php if ( 'custom' == $settings->change_default_aspect_ratio && $settings->aspect_ratio_height && $settings->aspect_ratio_width ) : ?>
    .fl-node-<?php echo $id; ?> .wpd-optimised-video__player {
        padding-bottom: <?php echo ( $settings->aspect_ratio_height / $settings->aspect_ratio_width ) * 100; ?>%;
    }
<?php endif; ?>

<?php if ( 'yes' == $settings->display_thumbnail_overlay ) : ?>
    .fl-node-<?php echo $id; ?> .wpd-optimised-video__thumbnail-overlay {
        <?php if ( ! empty( $settings->thumbnail_overlay_color ) ) : ?>
            background-color: #<?php echo $settings->thumbnail_overlay_color; ?>;
            opacity: <?php echo $settings->thumbnail_overlay_opacity / 100; ?>
        <?php endif; ?>
    }
<?php endif; ?>

<?php if ( array_key_exists( $settings->select_css_filter, FieldGroups::getPercentageBasedCssFilters() ) ) : ?>
    .fl-node-<?php echo $id; ?> .wpd-optimised-video__thumbnail {
        -webkit-filter: <?php echo $settings->select_css_filter; ?>(<?php echo $settings->css_filter_percentage ?: '100' ; ?>%);
        filter: <?php echo $settings->select_css_filter; ?>(<?php echo $settings->css_filter_percentage ?: '100' ; ?>%);
    }
<?php endif; ?>

<?php if ( isset( $settings->custom_module_css ) && ! empty( $settings->custom_module_css ) ) : ?>
    .fl-node-<?php echo $id; ?> {
        <?php echo $settings->custom_module_css; ?>
    }
<?php endif; ?>

<?php
if ( 'yes' == $settings->enable_delayed_cta ) :
    \FLBuilder::render_module_css( 'button', $id, $settings->regular_bb_button ); ?>

    .fl-node-<?php echo $id; ?> .wpd-optimised-video__timed-cta-button-container {
        display: none;
    }

    .wpd-optimised-video__cta-active {
        margin-<?php echo ( $settings->button_placement === 'above' ) ? 'bottom' : 'top'; ?>: <?php echo $settings->button_spacing; ?>px;
        position: relative;
    }

<?php endif; ?>
