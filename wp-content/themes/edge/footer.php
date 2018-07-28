<?php
/**
 * The template for displaying the footer.
 *
 * @package Theme Freesia
 * @subpackage Edge
 * @since Edge 1.0
 */
$edge_settings = edge_get_theme_options();
if($edge_settings['edge_blog_layout'] == 'photography_layout' && !is_page() && !is_single()){
	if(class_exists('WooCommerce') && is_shop()): ?>
		</div> <!-- end .container -->
	<?php else: ?>
	</div> <!-- end #main -->
	<?php
	endif;
}else{?>
</div> <!-- end .container -->
<?php
} ?>
</div> <!-- end #content -->
<!-- Footer Start ============================================= -->
<footer id="colophon" class="site-footer clearfix">
<?php
if ( is_front_page() && is_home() ) {
	if ((function_exists('display_instagram')) && $edge_settings['edge_instagram_feed_display'] !=0){
		echo do_shortcode('[instagram-feed]');
	}// Default homepage
} elseif ( is_front_page()){
	if ((function_exists('display_instagram')) && $edge_settings['edge_instagram_feed_display'] !=0){
		echo do_shortcode('[instagram-feed]');
	}//Static homepage
} else {
//silence is golden
}
$footer_column = $edge_settings['edge_footer_column_section'];
	if( is_active_sidebar( 'edge_footer_1' ) || is_active_sidebar( 'edge_footer_2' ) || is_active_sidebar( 'edge_footer_3' ) || is_active_sidebar( 'edge_footer_4' )) { ?>
	<div class="widget-wrap">
		<div class="container">
			<div class="widget-area clearfix">
			<?php
				if($footer_column == '1' || $footer_column == '2' ||  $footer_column == '3' || $footer_column == '4'){
				echo '<div class="column-'.$footer_column.'">';
					if ( is_active_sidebar( 'edge_footer_1' ) ) :
						dynamic_sidebar( 'edge_footer_1' );
					endif;
				echo '</div><!-- end .column'.$footer_column. '  -->';
				}
				if($footer_column == '2' ||  $footer_column == '3' || $footer_column == '4'){
				echo '<div class="column-'.$footer_column.'">';
					if ( is_active_sidebar( 'edge_footer_2' ) ) :
						dynamic_sidebar( 'edge_footer_2' );
					endif;
				echo '</div><!--end .column'.$footer_column.'  -->';
				}
				if($footer_column == '3' || $footer_column == '4'){
				echo '<div class="column-'.$footer_column.'">';
					if ( is_active_sidebar( 'edge_footer_3' ) ) :
						dynamic_sidebar( 'edge_footer_3' );
					endif;
				echo '</div><!--end .column'.$footer_column.'  -->';
				}
				if($footer_column == '4'){
				echo '<div class="column-'.$footer_column.'">';
					if ( is_active_sidebar( 'edge_footer_4' ) ) :
						dynamic_sidebar( 'edge_footer_4' );
					endif;
				echo '</div><!--end .column'.$footer_column.  '-->';
				}
				?>
			</div> <!-- end .widget-area -->
		</div> <!-- end .container -->
	</div> <!-- end .widget-wrap -->
	<?php } ?>
<div class="site-info" <?php if($edge_settings['edge-img-upload-footer-image'] !=''){?>style="background-image:url('<?php echo esc_url($edge_settings['edge-img-upload-footer-image']); ?>');" <?php } ?>>
	<div class="container">
	<?php
		if($edge_settings['edge_buttom_social_icons'] == 0):
			do_action('social_links');
		endif;
		do_action('edge_footer_menu');

		if ( is_active_sidebar( 'edge_footer_options' ) ) :
		dynamic_sidebar( 'edge_footer_options' );
		else:
			echo '<div class="copyright">' .'&copy; ' . date('Y') .' '; ?>
			<a title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" target="_blank" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo get_bloginfo( 'name', 'display' ); ?></a> | 
							<?php esc_html_e('Designed by:','edge'); ?> <a title="<?php echo esc_attr__( 'Theme Freesia', 'edge' ); ?>" target="_blank" href="<?php echo esc_url( 'https://themefreesia.com' ); ?>"><?php esc_html_e('Theme Freesia','edge');?></a> | 
							<?php esc_html_e('Powered by:','edge'); ?> <a title="<?php echo esc_attr__( 'WordPress', 'edge' );?>" target="_blank" href="<?php echo esc_url( 'http://wordpress.org' );?>"><?php esc_html_e('WordPress','edge'); ?></a>
			</div>
		<?php endif; ?>
			<div style="clear:both;"></div>
		</div> <!-- end .container -->
	</div> <!-- end .site-info -->
	<?php
		$disable_scroll = $edge_settings['edge_scroll'];
		if($disable_scroll == 0):?>
	<div class="go-to-top"><a title="<?php esc_html_e('Go to Top','edge');?>" href="#masthead"><i class="fa fa-angle-double-up"></i></a></div> <!-- end .go-to-top -->
	<?php endif; ?>
</footer> <!-- end #colophon -->
</div> <!-- end #page -->
<?php wp_footer(); ?>
</body>
</html>