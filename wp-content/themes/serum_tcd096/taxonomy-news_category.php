<?php
     get_header();
     $options = get_design_plus_option();
     $catch_direction = $options['archive_news_catch_direction'];
     $image = wp_get_attachment_image_src($options['archive_news_header_bg_image'], 'full');
     $image_mobile = wp_get_attachment_image_src($options['archive_news_header_bg_image'], 'size3');
     $query_obj = get_queried_object();
     $current_cat_id = $query_obj->term_id;
     $catch = ($query_obj->name)? $query_obj->name : $options['archive_news_catch'];
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

<div class="breadcrumb_type3">
 <?php get_template_part('template-parts/breadcrumb'); ?>
</div>

<div id="archive_news">

 <?php
      $news_category_list = get_terms( 'news_category', array( 'orderby' => 'order', 'hide_empty' => true ) );
      if ( $news_category_list && ! is_wp_error( $news_category_list ) ) {
 ?>
 <script type="text/javascript">
 (function($){
    function news_category_scroll_bar(){
      var menu_width = 0;
      $(".news_category_button_wrap li").each(function () {
        menu_width += $(this).width();
      });
      var winW = $(window).innerWidth() - 20;
      if(winW < menu_width ){
        $(".news_category_button_wrap").addClass('use_scroll');
<?php if(!is_mobile()){ ?>
        new SimpleBar($('.news_category_button_wrap')[0]);
<?php }; ?>
      } else {
        $(".news_category_button_wrap").removeClass('use_scroll');
      }
    };
    news_category_scroll_bar();
    $(window).on('load resize', function(){
      news_category_scroll_bar();
    });
 })(jQuery);
 </script>
 <div class="news_category_button_wrap">
  <ol class="news_category_button">
   <li><a href="<?php echo esc_url(get_post_type_archive_link('news')); ?>"><?php _e('ALL', 'tcd-serum');  ?></a></li>
   <?php
        foreach ( $news_category_list as $cat ):
          $cat_id = $cat->term_id;
          $cat_name = $cat->name;
          $cat_url = get_term_link($cat_id,'news_category');
   ?>
   <li<?php if($current_cat_id == $cat_id){ echo ' class="current"'; }; ?>><a href="<?php echo esc_url($cat_url); ?>"><?php echo esc_html($cat_name); ?></a></li>
   <?php endforeach; ?>
  </ol>
 </div>
 <?php }; ?>

 <?php if ( have_posts() ) : ?>

 <div id="news_list">
  <?php
       while ( have_posts() ) : the_post();
         if(has_post_thumbnail()) {
           $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'size2' );
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
    <h2 class="title"><a href="<?php the_permalink(); ?>"><span><?php the_title(); ?></span></a></h2>
    <ul class="meta">
     <?php if ($options['blog_show_date'] == 'display'){ ?>
     <li><time class="date entry-date published" datetime="<?php the_modified_time('c'); ?>"><?php the_time('Y.m.d'); ?></time></li>
     <?php }; ?>
    </ul>
   </div>
  </div>
  <?php endwhile; ?>
 </div><!-- END #news_list -->

 <?php get_template_part('template-parts/navigation'); ?>

 <?php else: ?>

 <p id="no_post"><?php _e('There is no registered post.', 'tcd-serum');  ?></p>

 <?php endif; ?>

</div><!-- END #archive_news -->

<?php get_footer(); ?>