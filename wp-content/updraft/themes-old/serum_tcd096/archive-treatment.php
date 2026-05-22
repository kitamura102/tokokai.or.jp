<?php
     get_header();
     $options = get_design_plus_option();
     $catch = $options['archive_treatment_catch'];
     $catch_direction = $options['archive_treatment_catch_direction'];
     $image = wp_get_attachment_image_src($options['archive_treatment_header_bg_image'], 'full');
     $image_mobile = wp_get_attachment_image_src($options['archive_treatment_header_bg_image'], 'size3');
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

<?php get_template_part('template-parts/breadcrumb'); ?>

<div id="archive_treatment">

 <?php
      $category_list = get_terms( 'treatment_category', array( 'orderby' => 'order' ) );
      if ( $category_list && ! is_wp_error( $category_list ) ) :
 ?>
 <div id="treatment_archive_list">
  <?php
       foreach ( $category_list as $cat ):
         $cat_id = $cat->term_id;
         $cat_name = $cat->name;
         $cat_url = get_term_link($cat_id,'treatment_category');
         $term_meta = get_option( 'taxonomy_' . $cat_id, array() );
         $desc1 = isset($term_meta['desc1']) ?  $term_meta['desc1'] : '';
         $desc2 = isset($term_meta['desc2']) ?  $term_meta['desc2'] : '';
         $desc3 = isset($term_meta['desc3']) ?  $term_meta['desc3'] : '';
         $image = isset($term_meta['image1']) ? wp_get_attachment_image_src( $term_meta['image1'], 'full' ) : '';
  ?>
  <section class="post_content clearfix design_content_wrap">

   <div class="design_content">

    <div class="header layout_type2">
     <h2 class="catch common_catch"><?php echo wp_kses_post(nl2br($cat_name)); ?></h2>
     <?php if($desc1){ ?>
     <div class="content">
      <p class="desc"><?php echo wp_kses_post(nl2br($desc1)); ?></p>
     </div>
     <?php }; ?>
    </div>

    <?php if(!empty($image)) { ?>
    <div class="image">
     <img loading="lazy" src="<?php echo esc_attr($image[0]); ?>" width="<?php echo esc_attr($image[1]); ?>" height="<?php echo esc_attr($image[2]); ?>" />
    </div>
    <?php }; ?>

    <?php if($desc2 || $desc3){ ?>
    <div class="bottom_content">
     <?php if($desc2){ ?><p class="desc"><?php echo wp_kses_post(nl2br($desc2)); ?></p><?php }; ?>
     <?php if($desc3){ ?><p class="desc"><?php echo wp_kses_post(nl2br($desc3)); ?></p><?php }; ?>
    </div>
    <?php }; ?>

    <div class="link_button bottom">
     <a class="design_button" href="<?php echo esc_url($cat_url); ?>"><span><?php echo esc_html($cat_name); ?></span></a>
    </div>

   </div>

  </section>
  <?php endforeach; ?>
 </div>
 <?php endif; ?>

</div><!-- END #archive_treatment -->

<?php get_footer(); ?>