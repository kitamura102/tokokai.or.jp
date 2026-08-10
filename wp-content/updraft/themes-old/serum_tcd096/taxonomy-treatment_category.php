<?php
     get_header();
     $options = get_design_plus_option();
     $query_obj = get_queried_object();
     $current_cat_id = $query_obj->term_id;
     $term_meta = get_option( 'taxonomy_' . $current_cat_id, array() );
     $catch = $query_obj->name;
     $catch_direction = $options['archive_treatment_catch_direction'];
     $image = !empty($term_meta['header_image']) ? wp_get_attachment_image_src( $term_meta['header_image'], 'full' ) : wp_get_attachment_image_src($options['archive_treatment_header_bg_image'], 'full');
     $image_mobile = !empty($term_meta['header_image']) ? wp_get_attachment_image_src( $term_meta['header_image'], 'size3' ) : wp_get_attachment_image_src($options['archive_treatment_header_bg_image'], 'size3');
     $overlay_color = !empty($term_meta['overlay_color']) ? $term_meta['overlay_color'] : '#000000';
     $overlay_color = hex2rgb($overlay_color);
     $overlay_color = implode(",",$overlay_color);
     if(!isset($term_meta['overlay_opacity'])){
       $overlay_opacity = '0.2';
     } else {
       $overlay_opacity = $term_meta['overlay_opacity'];
     }
?>
<div id="page_header">
 <?php if($catch){ ?>
 <h1 class="catch direction_<?php echo esc_attr($catch_direction); ?>"><?php echo wp_kses_post(sepLine($catch)); ?></h1>
 <?php }; ?>
 <?php if(!empty($image)) { ?>
 <div class="overlay" style="background:rgba(<?php echo esc_attr($overlay_color); ?>,<?php echo esc_attr($overlay_opacity); ?>);"></div>
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

<?php get_template_part('template-parts/breadcrumb'); ?>

<?php
     $catch = isset($term_meta['catch']) ?  $term_meta['catch'] : '';
     $desc = isset($term_meta['desc4']) ?  $term_meta['desc4'] : '';
     $image = isset($term_meta['image2']) ? wp_get_attachment_image_src( $term_meta['image2'], 'full' ) : '';
?>

<div id="category_treatment"<?php if(!$catch && !$desc && !$image){ echo ' class="no_data"'; }; ?>>

 <?php if($catch || $desc || $image){ ?>
 <section class="post_content clearfix design_content_wrap" style="border:none;">

  <div class="design_content">

   <div class="header layout_type2">
    <h2 class="catch common_catch"><?php echo wp_kses_post(nl2br($catch)); ?></h2>
    <?php if($desc){ ?>
    <div class="content">
     <p class="desc"><?php echo wp_kses_post(nl2br($desc)); ?></p>
    </div>
    <?php }; ?>
   </div>

   <?php if(!empty($image)) { ?>
   <div class="image">
    <img loading="lazy" src="<?php echo esc_attr($image[0]); ?>" width="<?php echo esc_attr($image[1]); ?>" height="<?php echo esc_attr($image[2]); ?>" />
   </div>
   <?php }; ?>

  </div>

 </section>
 <?php }; ?>

 <?php
      $list_headline = isset($term_meta['list_headline']) ?  $term_meta['list_headline'] : '';
      $list_desc = isset($term_meta['list_desc']) ?  $term_meta['list_desc'] : '';
 ?>
 <section id="treatment_list">
  <?php if($list_headline || $list_desc){ ?>
  <div class="header">
   <?php if($list_headline){ ?>
   <h3 class="catch common_catch"><?php echo wp_kses_post(nl2br($list_headline)); ?></h3>
   <?php }; ?>
   <?php if($list_desc){ ?>
   <p class="desc"><?php echo wp_kses_post(nl2br($list_desc)); ?></p>
   <?php }; ?>
  </div>
  <?php }; ?>
  <?php
       $args = array( 'post_type' => 'treatment', 'posts_per_page' => -1, 'orderby' => array('menu_order' => 'ASC', 'date' => 'DESC'), 'tax_query' => array( array( 'taxonomy' => 'treatment_category', 'field' => 'id', 'terms' => $current_cat_id)) );
       //$args = array( 'post_type' => 'treatment', 'posts_per_page' => -1, 'orderby' => array('menu_order' => 'ASC', 'date' => 'DESC') );
       $post_list = new wp_query($args);
       if($post_list->have_posts()):
  ?>
  <div class="post_list">
   <?php
        while( $post_list->have_posts() ) : $post_list->the_post();
          if(has_post_thumbnail()) {
            $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'size1' );
          } else {
            $image = array();
            $image[0] = get_bloginfo('template_url') . "/img/common/no_image1.gif";
            $image[1] = '250';
            $image[2] = '250';
          }
   ?>
   <div class="item">
    <a class="link animate_background" href="<?php the_permalink(); ?>">
     <div class="image_wrap">
      <div class="image">
       <img loading="lazy" src="<?php echo esc_attr($image[0]); ?>" width="<?php echo esc_attr($image[1]); ?>" height="<?php echo esc_attr($image[2]); ?>" />
      </div>
     </div>
     <p class="title"><span><?php the_title(); ?></span></p>
    </a>
   </div>
   <?php endwhile; wp_reset_query(); ?>
  </div>
  <?php endif; ?>

  <div class="link_button">
   <a class="design_button" href="<?php echo esc_url(get_post_type_archive_link('treatment')); ?>"><span><?php echo esc_html($options['treatment_label']); ?></span></a>
  </div>

 </section>

</div><!-- END #category_treatment -->

<?php get_footer(); ?>