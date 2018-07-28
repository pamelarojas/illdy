<?php
/**
 * Displays the header content
 *
 * @package Theme Freesia
 * @subpackage Edge
 * @since Edge 1.0
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<?php
$edge_settings = edge_get_theme_options(); ?>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<div id="page" class="hfeed site">
<!-- Masthead ============================================= -->
<header id="masthead" class="site-header">
		<?php if ( function_exists( 'the_custom_header_markup' ) ) {
			if ( is_header_video_active() && ( has_header_video() || is_customize_preview() ) ) {
				echo '<div class="custom-header">
					<div class="custom-header-media">';
				the_custom_header_markup();
				echo '</div>
				</div>';
			}else{ 
				if ( has_header_image() ) {?>
					<a href="<?php echo esc_url(home_url('/'));?>"><img src="<?php header_image(); ?>" class="header-image" height="<?php echo get_custom_header()->height; ?>" width="<?php echo get_custom_header()->width; ?>" alt="<?php echo esc_attr(get_bloginfo('name', 'display'));?>" /> </a>
				<?php }
			}
		} else { ?>
		<a href="<?php echo esc_url(home_url('/'));?>"><img src="<?php header_image(); ?>" class="header-image" height="<?php echo get_custom_header()->height; ?>" width="<?php echo get_custom_header()->width; ?>" alt="<?php echo esc_attr(get_bloginfo('name', 'display'));?>" /> </a>
		<?php } ?>
		<div class="top-header">
			<div class="container clearfix">
				<?php
				if( is_active_sidebar( 'edge_header_info' )) {
					dynamic_sidebar( 'edge_header_info' );
				}
				if($edge_settings['edge_top_social_icons'] == 0):
					echo '<div class="header-social-block">';
						do_action('social_links');
					echo '</div>'.'<!-- end .header-social-block -->';
				endif;
				 do_action('edge_site_branding'); ?>
			</div> <!-- end .container -->
		</div> <!-- end .top-header -->
		<!-- Main Header============================================= -->
				<div id="sticky_header">
					<div class="container clearfix">
					  	<h3 class="nav-site-title">
							<a href="<?php echo esc_url(home_url('/'));?>" title="<?php echo esc_attr(get_bloginfo('name', 'display'));?>"><?php bloginfo('name');?></a>
						</h3>
					<!-- end .nav-site-title -->
						<!-- Main Nav ============================================= -->
						<?php
							if (has_nav_menu('primary')) { ?>
						<?php $args = array(
							'theme_location' => 'primary',
							'container'      => '',
							'items_wrap'     => '<ul id="primary-menu" class="menu nav-menu">%3$s</ul>',
							); ?>
						<nav id="site-navigation" class="main-navigation clearfix">
							<button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
								<span class="line-one"></span>
					  			<span class="line-two"></span>
					  			<span class="line-three"></span>
						  	</button>
					  		<!-- end .menu-toggle -->
							<?php wp_nav_menu($args);//extract the content from apperance-> nav menu ?>
						</nav> <!-- end #site-navigation -->
						<?php } else {// extract the content from page menu only ?>
						<nav id="site-navigation" class="main-navigation clearfix">
							<button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
								<span class="line-one"></span>
					  			<span class="line-two"></span>
					  			<span class="line-three"></span>
						  	</button>
					  	<!-- end .menu-toggle -->
							<?php	wp_page_menu(array('menu_class' => 'menu', 'items_wrap'     => '<ul id="primary-menu" class="menu nav-menu">%3$s</ul>')); ?>
						</nav> <!-- end #site-navigation -->
						<?php }
						$search_form = $edge_settings['edge_search_custom_header'];
						if (1 != $search_form) { ?>
							<div id="search-toggle" class="header-search"></div>
							<div id="search-box" class="clearfix">
								<?php get_search_form();?>
							</div>  <!-- end #search-box -->
						<?php } 

			echo '</div> <!-- end .container -->
			</div> <!-- end #sticky_header -->';
		$enable_slider = $edge_settings['edge_enable_slider'];
		edge_slider_value();
		if ($enable_slider=='frontpage'|| $enable_slider=='enitresite'){
			if(is_front_page() && ($enable_slider=='frontpage') ) {
				if($edge_settings['edge_slider_type'] == 'default_slider') {
						edge_page_sliders();
				}else{
					if(class_exists('Edge_Plus_Features')):
						edge_image_sliders();
					endif;
				}
			}
			if($enable_slider=='enitresite'){
				if($edge_settings['edge_slider_type'] == 'default_slider') {
						edge_page_sliders();
				}else{
					if(class_exists('Edge_Plus_Features')):
						edge_image_sliders();
					endif;
				}
			}
		} ?>
</header> <!-- end #masthead -->
<!-- Main Page Start ============================================= -->
<div id="content">
<div class="container clearfix">
<?php 
if(is_front_page()){
	do_action('edge_display_frontpage_features');
}
if(!is_home()){?>
	<div class="page-header">
		<h1 class="page-title"><?php echo edge_header_title(); ?></h1>
		<!-- .page-title -->
		<?php edge_breadcrumb(); ?>
		<!-- .breadcrumb -->
	</div>
	<!-- .page-header -->
<?php }