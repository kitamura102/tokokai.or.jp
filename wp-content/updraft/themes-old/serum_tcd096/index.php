<?php
     get_header();
     $options = get_design_plus_option();
     $catch = $options['archive_blog_catch'];
     $catch_direction = $options['archive_blog_catch_direction'];
     $desc = $options['archive_blog_desc'];
     $desc_mobile = $options['archive_blog_desc_mobile'];
     $image = wp_get_attachment_image_src($options['archive_blog_header_bg_image'], 'full');
     $image_mobile = wp_get_attachment_image_src($options['archive_blog_header_bg_image'], 'size3');
     if (is_category()) {
       $query_obj = get_queried_object();
       $catch = $query_obj->name;
       $catch_direction = 'type1';
       $desc = '';
       $desc_mobile = '';
       if (!empty($query_obj->description)){
         $desc = $query_obj->description;
       }
     } elseif(is_tag()) {
       $query_obj = get_queried_object();
       $catch = $query_obj->name;
       $catch_direction = 'type1';
       $desc = '';
       $desc_mobile = '';
       if (!empty($query_obj->description)){
         $desc = $query_obj->description;
       }
     } elseif ( is_day() ) {
       $catch = sprintf( __( 'Archive for %s', 'tcd-serum' ), get_the_time( __( 'F jS, Y', 'tcd-serum' ) ) );
       $catch_direction = 'type1';
       $desc = '';
       $desc_mobile = '';
     } elseif ( is_month() ) {
       $catch = sprintf( __( 'Archive for %s', 'tcd-serum' ), get_the_time( __( 'F, Y', 'tcd-serum') ) );
       $catch_direction = 'type1';
       $desc = '';
       $desc_mobile = '';
     } elseif ( is_year() ) {
       $catch = sprintf( __( 'Archive for %s', 'tcd-serum' ), get_the_time( __( 'Y', 'tcd-serum') ) );
       $catch_direction = 'type1';
       $desc = '';
       $desc_mobile = '';
     } elseif(is_author()) {
       $author_info = $wp_query->get_queried_object();
       $author_id = $author_info->ID;
       $user_data = get_userdata($author_id);
       $user_name = $user_data->display_name;
       $catch = sprintf( __( 'Archive for %s', 'tcd-serum' ), $user_name );
       $catch_direction = 'type1';
       $desc = '';
       $desc_mobile = '';
     }

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

 <?php if(!is_paged() && $desc){ ?>
 <div id="archive_header">
    <div<?php if($desc_mobile){ echo ' class="pc"'; }; ?>>
      <div class="post_content">
        <?php echo apply_filters('the_content', $desc); ?>
      </div>
    </div>
    <?php if($desc_mobile){ ?>
      <div class="mobile">
        <div class="post_content">
          <?php echo apply_filters('the_content', $desc_mobile); ?>
        </div>
      </div>
    <?php }; ?>
 </div>
 <?php }; ?>

 <?php if ( have_posts() ) : ?>

 <div id="blog_list">
  <?php
       while ( have_posts() ) : the_post();
         if(has_post_thumbnail()) {
           $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'size3' );
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
   <a class="animate_background" href="<?php the_permalink(); ?>">
    <div class="image_wrap">
     <img loading="lazy" class="image" src="<?php echo esc_attr($image[0]); ?>" width="<?php echo esc_attr($image[1]); ?>" height="<?php echo esc_attr($image[2]); ?>" />
    </div>
   </a>
   <?php
        if(!is_category()) {
          $category = wp_get_post_terms( $post->ID, 'category' , array( 'orderby' => 'term_order' ));
          if ( $category && ! is_wp_error($category) ) {
            foreach ( $category as $cat ) :
              $cat_name = $cat->name;
              $cat_id = $cat->term_id;
              break;
            endforeach;
   ?>
   <a class="category" href="<?php echo esc_url(get_term_link($cat_id,'category')); ?>"><?php echo esc_html($cat_name); ?></a>
   <?php
          };
        };
   ?>
   <div class="content">
    <h2 class="title"><a href="<?php the_permalink(); ?>"><span><?php the_title(); ?></span></a></h2>
    <?php if ($options['blog_show_date'] == 'display'){ ?>
    <time class="date entry-date published" datetime="<?php the_modified_time('c'); ?>"><?php the_time('Y.m.d'); ?></time>
    <?php }; ?>
   </div>
  </div>
  <?php endwhile; ?>
 </div><!-- END #blog_list -->

 <?php get_template_part('template-parts/navigation'); ?>

 <?php else: ?>

 <p id="no_post"><?php _e('There is no registered post.', 'tcd-serum');  ?></p>

 <?php endif; ?>

</div><!-- END #archive_blog -->

<?php get_footer(); ?>