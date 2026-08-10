<?php
     function load_icon(){
       $options = get_design_plus_option();

       // splash screen -------------------------------------------------
       if( (is_front_page() && $options['show_splash'] && !isset($_COOKIE['splash_screen']) && $options['splash_display_time'] == 'type1') || (is_front_page() && $options['show_splash'] && $options['splash_display_time'] == 'type2') ){
         $catch = $options['splash_catch'];
         $catch_direction = $options['splash_catch_direction'];
         $image = wp_get_attachment_image_src($options['splash_image'], 'full');
         $image_mobile = wp_get_attachment_image_src($options['splash_image_mobile'], 'full');
         $overlay_color = hex2rgb($options['splash_overlay_color']);
         $overlay_color = implode(",",$overlay_color);
         $splash_type = $options['splash_type'];
         $logo_image = wp_get_attachment_image_src( $options['splash_logo'], 'full' );
         if($splash_type == 'type2' && $logo_image){
           $image_width = $logo_image[1];
           $image_height = $logo_image[2];
           if($options['splash_logo_retina'] == 'yes') {
             $image_width = round($image_width / 2);
             $image_height = round($image_height / 2);
           };
         };
?>
<div id="splash_screen">
 <?php if($splash_type == 'type1' && $catch){ ?>
 <p class="catch common_catch direction_<?php echo esc_attr($catch_direction); ?>"><?php echo wp_kses_post(sepLine($catch)); ?></p>
 <?php }; ?>
 <?php if($splash_type == 'type2' && $logo_image){ ?>
 <img class="logo" src="<?php echo esc_attr($logo_image[0]); ?>" alt="" width="<?php echo esc_attr($image_width); ?>" height="<?php echo esc_attr($image_height); ?>" />
 <?php }; ?>
 <?php if(!empty($image)) { ?>
 <div class="overlay" style="background:rgba(<?php echo esc_attr($overlay_color); ?>,<?php echo esc_attr($options['splash_overlay_opacity']); ?>);"></div>
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
<?php
       };

       if ( $options['show_loading'] ) {

       // circle loader ----------------------------
       if ($options['loading_type'] == 'type1') {
?>
<div id="site_loader_overlay">
 <div class="circular_loader">
  <svg class="circular" viewBox="25 25 50 50">
   <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/>
  </svg>
 </div>
</div>
<?php
     // square loader ------------------------
     } elseif ($options['loading_type'] == 'type2') {
?>
<div id="site_loader_overlay">
 <div class="sk-cube-grid">
  <div class="sk-cube sk-cube1"></div>
  <div class="sk-cube sk-cube2"></div>
  <div class="sk-cube sk-cube3"></div>
  <div class="sk-cube sk-cube4"></div>
  <div class="sk-cube sk-cube5"></div>
  <div class="sk-cube sk-cube6"></div>
  <div class="sk-cube sk-cube7"></div>
  <div class="sk-cube sk-cube8"></div>
  <div class="sk-cube sk-cube9"></div>
 </div>
</div>
<?php
     // dot circle loader -----------------------
     } elseif ($options['loading_type'] == 'type3') {
?>
<div id="site_loader_overlay">
 <div class="sk-circle">
  <div class="sk-circle1 sk-child"></div>
  <div class="sk-circle2 sk-child"></div>
  <div class="sk-circle3 sk-child"></div>
  <div class="sk-circle4 sk-child"></div>
  <div class="sk-circle5 sk-child"></div>
  <div class="sk-circle6 sk-child"></div>
  <div class="sk-circle7 sk-child"></div>
  <div class="sk-circle8 sk-child"></div>
  <div class="sk-circle9 sk-child"></div>
  <div class="sk-circle10 sk-child"></div>
  <div class="sk-circle11 sk-child"></div>
  <div class="sk-circle12 sk-child"></div>
 </div>
</div>
<?php
       }; // END loading type

       };

     };
?>