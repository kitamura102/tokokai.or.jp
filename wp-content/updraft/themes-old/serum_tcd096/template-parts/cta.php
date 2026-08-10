<?php
     $options = get_design_plus_option();
     if(is_singular('treatment')) {
       $post_type = 'treatment';
     } elseif(is_singular('news')) {
       $post_type = 'news';
     } else {
       $post_type = 'blog';
     }
     if($options['show_'. $post_type . '_cta']){
       if($options[$post_type . '_cta_type'] == 'type1'){
         $image = wp_get_attachment_image_src($options[$post_type . '_cta_type1_image'], 'full');
         $catch = $options[$post_type . '_cta_type1_catch'];
         $overlay_color = hex2rgb($options[$post_type . '_cta_type1_overlay_color']);
         $overlay_color = implode(",",$overlay_color);
         $overlay_opacity = $options[$post_type . '_cta_type1_overlay_opacity'] ?  $options[$post_type . '_cta_type1_overlay_opacity'] : '0.3';
?>
<div id="cta_type1">
 <a class="animate_background" href="<?php echo esc_url($options[$post_type . '_cta_type1_url']); ?>"<?php if($options[$post_type . '_cta_type1_target']){ echo ' target="_blank" rel="nofollow noopener"'; }; ?>>
  <?php if(!empty($image)) { ?>
  <div class="image_wrap">
   <div class="overlay" style="background:rgba(<?php echo esc_attr($overlay_color); ?>,<?php echo esc_attr($overlay_opacity); ?>);"></div>
   <img class="image" src="<?php echo esc_attr($image[0]); ?>" width="<?php echo esc_attr($image[1]); ?>" height="<?php echo esc_attr($image[2]); ?>" />
  </div>
  <?php }; ?>
  <div class="content">
   <?php if($catch){ ?>
   <h3 class="catch"><?php echo wp_kses_post(nl2br($catch)); ?></h3>
   <?php }; ?>
  </div>
 </a>
</div>
<?php } else { ?>
<div id="cta_type2">
 <?php
      for ( $i = 1; $i <= 2; $i++ ) :
        $image = wp_get_attachment_image_src($options[$post_type . '_cta_type2_image'.$i], 'full');
        $catch = $options[$post_type . '_cta_type2_catch'.$i];
        $desc = $options[$post_type . '_cta_type2_desc'.$i];
        $overlay_color = hex2rgb($options[$post_type . '_cta_type2_overlay_color'.$i]);
        $overlay_color = implode(",",$overlay_color);
 ?>
 <a class="animate_background" href="<?php echo esc_url($options[$post_type . '_cta_type2_url'.$i]); ?>"<?php if($options[$post_type . '_cta_type2_target'.$i]){ echo ' target="_blank" rel="nofollow noopener"'; }; ?>>
  <div class="content">
   <?php if($catch){ ?>
   <h3 class="catch"><?php echo wp_kses_post(nl2br($catch)); ?></h3>
   <?php }; ?>
   <?php if($desc){ ?>
   <p class="desc"><?php echo wp_kses_post(nl2br($desc)); ?></p>
   <?php }; ?>
  </div>
  <?php if(!empty($image)) { ?>
  <div class="image_wrap">
   <div class="overlay" style="background: linear-gradient(to bottom, rgba(<?php echo esc_attr($overlay_color); ?>,0) 0%,rgba(<?php echo esc_attr($overlay_color); ?>,1) 100%);"></div>
   <img class="image" src="<?php echo esc_attr($image[0]); ?>" width="<?php echo esc_attr($image[1]); ?>" height="<?php echo esc_attr($image[2]); ?>" />
  </div>
  <?php }; ?>
 </a>
 <?php endfor; ?>
</div>
<?php
       };
     };
?>