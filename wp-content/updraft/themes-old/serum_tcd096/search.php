<?php
     get_header();
     $options = get_design_plus_option();
     if ( !empty( get_search_query() ) ) {
       $catch = sprintf( __( 'Search result for %s', 'tcd-serum' ), get_search_query() );
     } else {
       $catch = __( 'Search result', 'tcd-serum' );
     }
     $catch_direction = 'type1';
     $image = wp_get_attachment_image_src($options['archive_blog_header_bg_image'], 'full');
     $image_mobile = wp_get_attachment_image_src($options['archive_blog_header_bg_image'], 'size3');
?>
<?php
     // 検索結果がある場合
     if ( isset($_GET['s']) && !empty($_GET['s']) && have_posts() ) :
?>
<div id="page_header">
 <?php if($catch){ ?>
 <h1 class="catch direction_<?php echo esc_attr($catch_direction); ?>"><?php echo wp_kses_post(sepLine($catch)); ?></h1>
 <?php }; ?>
 <?php if(!empty($image)) { ?>
 <div class="overlay"></div>
 <div class="bg_image">
  <picture>
   <?php if($image_mobile) { ?>
   <source media="(max-width: 800px)" srcset="<?php echo esc_attr($image_mobile[0]); ?>">
   <?php }; ?>
   <img src="<?php echo esc_attr($image[0]); ?>" alt="" width="<?php echo esc_attr($image[1]); ?>" height="<?php echo esc_attr($image[2]); ?>">
  </picture>
 </div>
 <?php }; ?>
</div>

<div class="breadcrumb_type2">
 <?php get_template_part('template-parts/breadcrumb'); ?>
</div>

<div id="archive_blog">

 <div id="blog_list">
  <?php
       while ( have_posts() ) : the_post();
         $post_type = $post->post_type;
         if(has_post_thumbnail()) {
           if($post_type == 'treatment'){
             $category = wp_get_post_terms( $post->ID, 'treatment_category' , array( 'orderby' => 'term_order' ));
             if ( $category && ! is_wp_error($category) ) {
               foreach ( $category as $cat ) :
                 $cat_id = $cat->term_id;
                 break;
               endforeach;
               $term_meta = get_option( 'taxonomy_' . $cat_id, array() );
               $image = !empty($term_meta['header_image']) ? wp_get_attachment_image_src( $term_meta['header_image'], 'full' ) : wp_get_attachment_image_src($options['archive_treatment_header_bg_image'], 'size3');
             } else {
               $image = $options['archive_treatment_header_bg_image'] ? wp_get_attachment_image_src($options['archive_treatment_header_bg_image'], 'size3') : '';
             }
           } else {
             $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'size3' );
           }
         } elseif($options['no_image']) {
           $image = wp_get_attachment_image_src( $options['no_image'], 'full' );
         } else {
           $image = array();
           $image[0] = get_bloginfo('template_url') . "/img/common/no_image2.gif";
           $image[1] = '465';
           $image[2] = '270';
         }
  ?>
  <div class="item">
   <a class="image_wrap animate_background" href="<?php the_permalink(); ?>">
    <div class="image">
     <img src="<?php echo esc_attr($image[0]); ?>" width="<?php echo esc_attr($image[1]); ?>" height="<?php echo esc_attr($image[2]); ?>" />
    </div>
   </a>
   <div class="content">
    <h3 class="title"><a href="<?php the_permalink(); ?>"><span><?php the_title(); ?></span></a></h3>
    <?php if ($options['blog_show_date'] == 'display'){ ?>
    <time class="date entry-date published" datetime="<?php the_modified_time('c'); ?>"><?php the_time('Y.m.d'); ?></time>
    <?php }; ?>
   </div>
  </div>
  <?php endwhile; ?>
 </div><!-- END #blog_list -->

 <?php get_template_part('template-parts/navigation'); ?>

</div><!-- END #archive_blog -->

<?php
     else:

     // 検索結果が無い場合、もしくはキーワードが空の場合 --------------------------------------------------------------------
     $bg_image = wp_get_attachment_image_src($options['search_result_bg_image'], 'full');
     $overlay_color = hex2rgb($options['search_result_overlay_color']);
     $overlay_opacity = $options['search_result_overlay_opacity'];
     $overlay_color = implode(",",$overlay_color);
?>

<div id="page_404_header"<?php if(!$bg_image){ echo ' class="no_bg_image"'; }; ?>>

 <div class="content inview">
  <?php if($options['search_result_headline']){ ?>
  <h2 class="catch common_catch"><?php echo nl2br(esc_html($options['search_result_headline'])); ?></h2>
  <?php } ?>
  <?php if ($options['search_result_desc']) { ?>
  <div class="desc"><?php if(empty($_GET['s'])){ echo __( 'Search keyword is blank.', 'tcd-genesis' ); } else { echo apply_filters('the_content', $options['search_result_desc'] );  }; ?></div>
  <?php } ?>
  <div class="search_form">
   <form role="search" method="get" action="<?php echo esc_url(home_url()); ?>">
    <div class="input_area"><input <?php if($options['search_result_placeholder']){ echo 'placeholder="' . esc_html($options['search_result_placeholder']) . '"'; }; ?> type="text" value="" name="s" autocomplete="off"></div>
    <div class="search_button"><label for="no_search_result_button"></label><input type="submit" id="no_search_result_button" value=""></div>
   </form>
  </div>
 </div>

 <?php if(!empty($bg_image) && $options['search_result_overlay_opacity'] != 0){ ?>
 <div class="overlay" style="background-color:rgba(<?php echo esc_attr($overlay_color); ?>,<?php echo esc_attr($overlay_opacity); ?>);"></div>
 <?php }; ?>

 <?php if(!empty($bg_image)) { ?>
 <div class="bg_image" style="background:url(<?php echo esc_attr($bg_image[0]); ?>) no-repeat center top; background-size:cover;"></div>
 <?php }; ?>

</div>

<?php endif; ?>

<?php get_footer(); ?>