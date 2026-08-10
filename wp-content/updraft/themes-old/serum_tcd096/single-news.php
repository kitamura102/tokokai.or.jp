<?php
     get_header();
     $options = get_design_plus_option();
     if ( have_posts() ) : while ( have_posts() ) : the_post();
       $category = wp_get_post_terms( $post->ID, 'news_category' , array( 'orderby' => 'term_order' ));
       if ( $category && ! is_wp_error($category) ) {
         foreach ( $category as $cat ) :
           $cat_name = $cat->name;
           $cat_id = $cat->term_id;
           $cat_url = get_term_link($cat_id,'news_category');
           break;
         endforeach;
       };
?>
<div class="breadcrumb_type3">
 <?php get_template_part('template-parts/breadcrumb'); ?>
</div>

<div id="main_content"<?php if($page != '1') { echo ' class="paged"'; }; ?>>

 <div id="main_col">

  <article id="article">

   <?php if($page == '1') { // ***** only show on first page ***** ?>

   <div id="single_post_title">
    <h1 class="title single_title entry-title"><?php the_title(); ?></h1>
    <div class="meta">
     <time class="date entry-date published" datetime="<?php the_modified_time('c'); ?>"><?php the_time('Y.m.d'); ?></time>
     <?php
          $post_date = get_the_time('Ymd');
          $modified_date = get_the_modified_date('Ymd');
          if($options['single_news_show_mod_date'] !== 'hide' && $post_date < $modified_date){
     ?>
     <time class="update entry-date updated" datetime="<?php the_modified_time('c'); ?>"><?php the_modified_date('Y.m.d'); ?></time>
     <?php }; ?>
     <?php if ( $category && ! is_wp_error($category) ) { ?>
     <a class="news_category" href="<?php echo esc_url($cat_url); ?>"><?php echo esc_html($cat_name); ?></a>
     <?php }; ?>
    </div>
   </div>

   <?php
        // アイキャッチ画像 -----------------------------------
        if(has_post_thumbnail()) {
          $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'size3' );
   ?>
   <div id="single_post_image">
    <img src="<?php echo esc_attr($image[0]); ?>" width="<?php echo esc_attr($image[1]); ?>" height="<?php echo esc_attr($image[2]); ?>" />
   </div>
   <?php }; ?>

   <?php
        // sns button top ------------------------------------------------------------------------------------------------------------------------
       if($options['single_news_show_sns_top'] == 'display') {
   ?>
   <div class="single_share clearfix" id="single_share_top">
    <?php get_template_part('template-parts/share_button'); ?>
   </div>
   <?php }; ?>

   <?php
        // copy title&url button ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        if($options['single_news_show_copy_top'] == 'display') {
   ?>
   <div class="single_copy_title_url" id="single_copy_title_url_top">
    <button class="single_copy_title_url_btn" data-clipboard-text="<?php echo esc_attr( strip_tags( get_the_title() ) . ' ' . get_permalink() ); ?>" data-clipboard-copied="<?php echo esc_attr( __( 'COPIED TITLE &amp; URL', 'tcd-serum' ) ); ?>"><?php _e( 'COPY TITLE &amp; URL', 'tcd-serum' ); ?></button>
   </div>
   <?php }; ?>

   <?php }; // ***** END only show on first page ***** ?>

   <?php // post content ------------------------------------------------------------------------------------------------------------------------ ?>
   <div class="post_content clearfix">
    <?php
         the_content();
         if ( ! post_password_required() ) {
           custom_wp_link_pages();
         }
    ?>
   </div>

   <?php
        // copy title&url button ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        if($options['single_news_show_copy_btm'] == 'display') {
   ?>
   <div class="single_copy_title_url" id="single_copy_title_url_bottom">
    <button class="single_copy_title_url_btn" data-clipboard-text="<?php echo esc_attr( strip_tags( get_the_title() ) . ' ' . get_permalink() ); ?>" data-clipboard-copied="<?php echo esc_attr( __( 'COPIED TITLE &amp; URL', 'tcd-serum' ) ); ?>"><?php _e( 'COPY TITLE &amp; URL', 'tcd-serum' ); ?></button>
   </div>
   <?php }; ?>

   <?php
        // sns button ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        if($options['single_news_show_sns_btm'] == 'display') {
   ?>
   <div class="single_share clearfix" id="single_share_bottom">
    <?php get_template_part('template-parts/share_button'); ?>
   </div>
   <?php }; ?>

   <?php
        // page nav ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
   ?>
   <div id="next_prev_post" class="clearfix">
    <?php next_prev_post_link(); ?>
   </div>

  </article><!-- END #article -->

  <?php endwhile; endif; ?>

  <?php
       // recent post ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
         $post_num = $options['recent_news_num'];
         if(is_mobile()){
           $post_num = $options['recent_news_num_sp'];
         }
         if($options['recent_news_post_type'] == 'recent_post'){
           $args =  array('post_type' => 'news', 'posts_per_page' => $post_num, 'ignore_sticky_posts' => 1);
         } else {
           if ($category){
             $args = array( 'post_type' => 'news', 'post__not_in' => array($post->ID), 'orderby' => array('menu_order' => 'ASC', 'date' => 'DESC'), 'posts_per_page' => $post_num, 'tax_query' => array( array( 'taxonomy' => 'news_category', 'field' => 'term_id', 'terms' => $cat_id ) ) );
           } else {
             $args =  array('post_type' => 'news', 'posts_per_page' => $post_num, 'ignore_sticky_posts' => 1);
           }
         }
         $recent_post_list = new wp_query($args);
         if($recent_post_list->have_posts()):
  ?>
  <div id="related_post">
   <h2 class="headline"><?php echo wp_kses_post(nl2br($options['recent_news_headline'])); ?></h2>
   <div class="post_list">
    <?php
         while( $recent_post_list->have_posts() ) : $recent_post_list->the_post();
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
     <a class="animate_background" href="<?php the_permalink(); ?>">
      <div class="image_wrap">
       <img loading="lazy" class="image" src="<?php echo esc_attr($image[0]); ?>" width="<?php echo esc_attr($image[1]); ?>" height="<?php echo esc_attr($image[2]); ?>" />
      </div>
     </a>
     <div class="content">
      <h3 class="title"><a href="<?php the_permalink(); ?>"><span><?php the_title(); ?></span></a></h3>
      <div class="meta">
       <time class="date entry-date published" datetime="<?php the_modified_time('c'); ?>"><?php the_time('Y.m.d'); ?></time>
       <?php
            $category = wp_get_post_terms( $post->ID, 'news_category' , array( 'orderby' => 'term_order' ));
            if ( $category && ! is_wp_error($category) ) {
              foreach ( $category as $cat ) :
                $cat_name = $cat->name;
                $cat_id = $cat->term_id;
                break;
              endforeach;
       ?>
       <a class="news_category" href="<?php echo esc_url(get_term_link($cat_id,'news_category')); ?>"><?php echo esc_html($cat_name); ?></a>
       <?php
            };
       ?>
      </div>
     </div>
    </div>
    <?php endwhile; wp_reset_query(); ?>
   </div><!-- END .post_list -->
  </div><!-- END #related_post -->
  <?php
         endif;
  ?>

  <?php
       // CTA ---------------------------------------
       get_template_part('template-parts/cta');
  ?>

 </div><!-- END #main_col -->

 <?php
      // widget ------------------------
      get_sidebar();
 ?>

</div><!-- END #main_contents -->

<?php get_footer(); ?>