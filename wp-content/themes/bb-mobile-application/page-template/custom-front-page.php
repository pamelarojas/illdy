<?php
/**
 * Template Name: Custom home
 */

get_header(); ?>

<?php do_action( 'bb_mobile_application_before_slider' ); ?>

  <?php /** slider section **/ ?>
  <div class="slider-main">
    <?php
      // Get pages set in the customizer (if any)
      $pages = array();
      for ( $count = 1; $count <= 5; $count++ ) {
        $mod = absint( get_theme_mod( 'bb_mobile_application_slidersettings-page-' . $count ) );
        if ( 'page-none-selected' != $mod ) {
          $pages[] = $mod;
        }
      }
      
      if( !empty($pages) ) :
        $args = array(
          'posts_per_page' => 5,
          'post_type' => 'page',
          'post__in' => $pages,
          'orderby' => 'post__in'
        );
        $query = new WP_Query( $args );
        if ( $query->have_posts() ) :
          $count = 1;
          ?>
          <div id="slider" class="nivoSlider">
            <?php
              $bb_mobile_application_n = 0;
            while ( $query->have_posts() ) : $query->the_post();
                
                $bb_mobile_application_n++;
                $bb_mobile_application_slideno[] = $bb_mobile_application_n;
                $bb_mobile_application_slidetitle[] = get_the_title();
                $bb_mobile_application_slidelink[] = esc_url( get_permalink() );
                ?>
                  <img src="<?php the_post_thumbnail_url('full'); ?>" title="#slidecaption<?php echo esc_attr( $bb_mobile_application_n ); ?>" />
                <?php
              $count++;
            endwhile;
            ?>
          </div>

          <?php
          $bb_mobile_application_k = 0;
            foreach( $bb_mobile_application_slideno as $bb_mobile_application_sln ){ ?>
              <div id="slidecaption<?php echo esc_attr( $bb_mobile_application_sln ); ?>" class="nivo-html-caption">
                <div class="slide-cap  ">
                  <div class="container">
                    <h2><?php echo esc_html( $bb_mobile_application_slidetitle[$bb_mobile_application_k] ); ?></h2>
                    <a class="read-more" href="<?php echo esc_url( $bb_mobile_application_slidelink[$bb_mobile_application_k] ); ?>"><?php esc_html_e( 'Learn More','bb-mobile-application' ); ?></a>
                  </div>
                </div>
              </div>
              <?php $bb_mobile_application_k++;
          }
        else : ?>
            <div class="header-no-slider"></div>
          <?php
        endif;
      else : ?>
          <div class="header-no-slider"></div>
      <?php
      endif; 
    ?>
  </div>

<?php do_action( 'bb_mobile_application_after_slider' ); ?>

<?php /** post section **/ ?>
<section class="creative-feature">
  <div class="container">
    <?php if( get_theme_mod('bb_mobile_application_title') != ''){ ?>
      <div class="heading-line">
        <h3><?php echo esc_html(get_theme_mod('bb_mobile_application_title','')); ?> </h3>
      </div>
    <?php } ?>
    <div class="row">
      <div class="col-md-4 col-sm-4">
        <section id="about" class="darkbox row" >
            <?php 
                $page_query = new WP_Query(array( 'category_name' => get_theme_mod('bb_mobile_application_blogcategory_left_setting','theblog')));?>
                <?php while( $page_query->have_posts() ) : $page_query->the_post(); ?>
                  <div class="left-part">
                    <div class="row"> 
                      <div class="col-md-3 col-sm-3">
                        <div class="abt-img-box"><?php if(has_post_thumbnail()) { ?><?php the_post_thumbnail(); ?><?php } ?></div>
                      </div>
                      <div class="col-md-9 col-sm-9">
                        <a href="<?php the_permalink(); ?>"><h4><?php the_title(); ?></h4></a>                    
                      </div>
                    </div>
                    <div class="clearfix"></div>
                    <p><?php the_excerpt(); ?></p>
                  </div>
                  <?php endwhile;
                  wp_reset_postdata();          
                  ?>          
            <div class="clearfix"></div>
        </section>
      </div>
      <div class="col-md-4 col-sm-4">
        <div class="middle-image">
          <?php
            $args = array( 'name' => get_theme_mod('the_wp_business_middle_image_setting',''));
            $query = new WP_Query( $args );
            if ( $query->have_posts() ) :
              while ( $query->have_posts() ) : $query->the_post(); ?>
              <div class="row">
                <div class="featuered-image">
                  <?php if(has_post_thumbnail()) { ?><?php the_post_thumbnail(); ?><?php } ?>                  
                </div>
              </div>
              <?php endwhile; 
              wp_reset_postdata();?>
            <?php else : ?>
               <div class="no-postfound"></div>
             <?php
            endif; ?>
            <div class="clearfix"></div>
        </div>
      </div>
      <div class="col-md-4 col-sm-4">
        <section id="about" class="darkbox row" >
            <?php 
                $page_query = new WP_Query(array( 'category_name' => get_theme_mod('bb_mobile_application_blogcategory_right_setting','theblog')));?>
                <?php while( $page_query->have_posts() ) : $page_query->the_post(); ?>
                  <div class="right-part">
                    <div class="row">
                    <div class="col-md-3 col-sm-3">
                      <div class="abt-img-box"><?php if(has_post_thumbnail()) { ?><?php the_post_thumbnail(); ?><?php } ?></div>
                    </div>
                    <div class="col-md-9 col-sm-9">
                      <a href="<?php the_permalink(); ?>"><h4><?php the_title(); ?></h4></a>                    
                    </div>
                    <div class="clearfix"></div>
                    <p><?php the_excerpt(); ?></p>                  
                  </div>
                <?php endwhile;
                wp_reset_postdata();
            ?>
            <div class="clearfix"></div>
        </section>
      </div>
    </div>
  </div>
</section>

<?php do_action( 'bb_mobile_application_before_service' ); ?>

<div class="container">
  <?php while ( have_posts() ) : the_post(); ?>
    <?php the_content(); ?>
  <?php endwhile; // end of the loop. ?>
</div>

<?php get_footer(); ?>