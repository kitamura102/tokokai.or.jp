<?php
     get_header();
     $options = get_design_plus_option();
     if ( have_posts() ) : while ( have_posts() ) : the_post();
?>

<div id="main_contents" style="border-top:1px solid #ddd; padding:100px 0 60px;">

 <div id="main_col">

  <article id="article">

   <?php
        // アイキャッチ画像 -----------------------------------
        $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );
   ?>
   <img style="display:block; margin:0 auto;" src="<?php echo esc_attr($image[0]); ?>" width="<?php echo esc_attr($image[1]); ?>" height="<?php echo esc_attr($image[2]); ?>" />

  </article><!-- END #article -->

  <?php endwhile; endif; ?>

 </div><!-- END #main_col -->

</div><!-- END #main_contents -->

<?php get_footer(); ?>