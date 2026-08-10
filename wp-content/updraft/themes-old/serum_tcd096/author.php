<?php
     get_header();
     $options = get_design_plus_option();
     $image = wp_get_attachment_image_src($options['archive_blog_header_bg_image'], 'full');
     $image_mobile = wp_get_attachment_image_src($options['archive_blog_header_bg_image'], 'size3');
     $author_info = $wp_query->get_queried_object();
     $author_id = $author_info->ID;
     $author_name = $author_info->display_name;
     $catch =  $author_name;
     $catch = sprintf( __( '%s blog list', 'tcd-serum' ), $catch );
     $catch_direction = 'type1';
     $desc = '';
     if($author_id){
       $user_data = get_userdata($author_id);
       $desc = $user_data->description;
       $facebook = $user_data->facebook_url;
       $twitter = $user_data->twitter_url;
       $insta = $user_data->instagram_url;
       $pinterest = $user_data->pinterest_url;
       $youtube = $user_data->youtube_url;
       $contact = $user_data->contact_url;
       $author_url = get_author_posts_url($author_id);
       $user_url = $user_data->user_url;
       $tiktok = $user_data->tiktok_url;
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

 <?php if(!is_paged()){ ?>
 <div class="author_profile clearfix">
  <a class="avatar_area animate_image" href="<?php echo esc_url($author_url); ?>"><?php echo wp_kses_post(get_avatar($author_id, 300)); ?></a>
  <div class="info">
   <div class="info_inner">
    <div class="name rich_font_type2"><a href="<?php echo esc_url($author_url); ?>"><span class="author"><?php echo esc_html($user_data->display_name); ?></span></a></div>
    <?php if($desc) { ?>
    <p class="desc"><span><?php echo esc_html($desc); ?></span></p>
    <?php }; ?>
    <?php if($facebook || $twitter || $insta || $pinterest || $youtube || $contact || $user_url || $tiktok) { ?>
    <ul id="author_sns" class="sns_button_list clearfix color_<?php echo esc_attr($options['sns_button_color_type']); ?>">
     <?php if($user_url) { ?><li class="user_url"><a href="<?php echo esc_url($user_url); ?>" target="_blank"><span><?php echo esc_url($user_url); ?></span></a></li><?php }; ?>
     <?php if($insta) { ?><li class="insta"><a href="<?php echo esc_url($insta); ?>" rel="nofollow" target="_blank" title="Instagram"><span>Instagram</span></a></li><?php }; ?>
     <?php if($tiktok) { ?><li class="tiktok"><a href="<?php echo esc_url($tiktok); ?>" rel="nofollow" target="_blank" title="TikTok"><span>TikTok</span></a></li><?php }; ?>
     <?php if($twitter) { ?><li class="twitter"><a href="<?php echo esc_url($twitter); ?>" rel="nofollow" target="_blank" title="X"><span>X</span></a></li><?php }; ?>
     <?php if($facebook) { ?><li class="facebook"><a href="<?php echo esc_url($facebook); ?>" rel="nofollow" target="_blank" title="Facebook"><span>Facebook</span></a></li><?php }; ?>
     <?php if($pinterest) { ?><li class="pinterest"><a href="<?php echo esc_url($pinterest); ?>" rel="nofollow" target="_blank" title="Pinterest"><span>Pinterest</span></a></li><?php }; ?>
     <?php if($youtube) { ?><li class="youtube"><a href="<?php echo esc_url($youtube); ?>" rel="nofollow" target="_blank" title="Youtube"><span>Youtube</span></a></li><?php }; ?>
     <?php if($contact) { ?><li class="contact"><a href="<?php echo esc_url($contact); ?>" rel="nofollow" target="_blank" title="Contact"><span>Contact</span></a></li><?php }; ?>
    </ul>
    <?php }; ?>
   </div>
  </div>
 </div><!-- END .author_profile -->
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
     <img class="image" src="<?php echo esc_attr($image[0]); ?>" width="<?php echo esc_attr($image[1]); ?>" height="<?php echo esc_attr($image[2]); ?>" />
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