<?php
     get_header();
     $options = get_design_plus_option();
     $bg_image = wp_get_attachment_image_src($options['page_404_bg_image'], 'full');
?>

<div id="page_404_header"<?php if(!$bg_image){ echo ' class="no_bg_image"'; }; ?>>

 <div class="content">
  <h2 class="catch common_catch"><?php if($options['page_404_catch']){ echo nl2br(esc_html($options['page_404_catch'])); } else { echo '404 NOT FOUND'; }; ?></h2>
  <?php if ($options['page_404_desc']) { ?>
  <div class="desc item"><?php echo apply_filters('the_content', $options['page_404_desc'] ); ?></div>
  <?php } ?>
 </div>

 <?php if(!empty($bg_image)) { ?>

 <?php if($options['page_404_overlay_opacity'] != 0){ ?>
 <div class="overlay"></div>
 <?php }; ?>

 <div class="bg_image" style="background:url(<?php echo esc_attr($bg_image[0]); ?>) no-repeat center top; background-size:cover;"></div>
 <?php }; ?>

</div>

<?php get_footer(); ?>