<?php
/*
Template Name:Staff list
*/
__('Staff list', 'tcd-serum');
?>
<?php
     get_header();
     $options = get_design_plus_option();
     $catch = get_the_title();
     $catch_direction = get_post_meta($post->ID, 'page_catch_direction', true) ?  get_post_meta($post->ID, 'page_catch_direction', true) : 'type1';
     $image = wp_get_attachment_image_src(get_post_meta($post->ID, 'bg_image', true), 'full');
     $image_mobile = wp_get_attachment_image_src(get_post_meta($post->ID, 'bg_image', true), 'full');
     $hide_page_header = get_post_meta($post->ID, 'hide_page_header', true) ?  get_post_meta($post->ID, 'hide_page_header', true) : 'no';
     $page_hide_bread = get_post_meta($post->ID, 'page_hide_bread', true) ?  get_post_meta($post->ID, 'page_hide_bread', true) : 'no';
     $header_type = get_post_meta($post->ID, 'header_type', true) ?  get_post_meta($post->ID, 'header_type', true) : 'type1';
     if($hide_page_header != 'yes' && $image){
?>
<div id="page_header"<?php if($header_type == 'type2'){ echo ' class="type2"'; }; ?>>
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
<?php if($page_hide_bread != 'yes'){ get_template_part('template-parts/breadcrumb'); }; ?>
<?php } else { ?>
<h1 class="single_title" id="page_title"><?php echo wp_kses_post($catch); ?></h1>
<?php }; ?>

<article id="page_contents">

 <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

 <?php // post content ------------------------------------------------------------------------------------------------------------------------ ?>
 <div class="post_content clearfix">
  <?php
       the_content();
       if ( ! post_password_required() ) {
         custom_wp_link_pages();
       }
  ?>
 </div>

 <?php endwhile; endif; ?>

</article><!-- END #page_contents -->

<?php get_footer(); ?>