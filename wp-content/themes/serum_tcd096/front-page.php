<?php
     $options = get_design_plus_option();
     get_header();
?>
<?php
     // 通常のコンテンツを読み込む ------------------------------------------------------------------------------
     if($options['index_content_type'] == 'type2'){
       if ( have_posts() ) : while ( have_posts() ) : the_post();
       $page_content_width = $options['page_content_width_type'] ?  $options['page_content_width_type'] : 'type1';
       if($page_content_width == 'type2'){
         $page_content_width_size = 'auto';
       } else {
         $page_content_width_size = $options['page_content_width'] . 'px';
       }
?>
<article id="page_contents" style="width:<?php echo esc_html($page_content_width_size); ?>; max-width:<?php echo esc_html($page_content_width_size); ?>;<?php if($page_content_width == 'type2'){ echo ' margin:0 !important;'; }; ?>">
 <div class="post_content clearfix">
  <?php
       the_content();
       if ( ! post_password_required() ) {
         custom_wp_link_pages();
       }
  ?>
 </div>
</div><!-- END #page_contents -->
<?php
        endwhile; endif;
     } else {
?>
<div id="content_builder">
<?php
     // コンテンツビルダー
     if ($options['contents_builder']) :
       $content_count = 1;
       $contents_builder = $options['contents_builder'];
       foreach($contents_builder as $content) :

         // ボックスコンテンツ --------------------------------------------------------------------------------
         if ( $content['cb_content_select'] == 'box_content' && $content['show_content'] ) {
?>
<section class="cb_box_content num<?php echo $content_count; ?>" id="<?php echo 'cb_content_' . $content_count; ?>">

 <?php if($content['catch']){ ?>
 <h2 class="catch common_catch"><?php echo wp_kses_post(nl2br($content['catch'])); ?></h2>
 <?php }; ?>

 <div class="content">

  <?php
       for ( $i = 1; $i <= 3; $i++ ):
         $image = isset($content['image'.$i]) ? wp_get_attachment_image_src( $content['image'.$i], 'full' ) : '';
         if(!empty($image)) {
  ?>
  <a class="item animate_background<?php if(!$content['button_url'.$i]){ echo ' no_link'; }; ?>" href="<?php if($content['button_url'.$i]){ echo esc_url($content['button_url'.$i]); } else { echo '#'; }; ?>" <?php if($content['button_target'.$i]){ echo ' target="_blank" rel="nofollow noopener"'; }; ?>>
   <div class="image_wrap">
    <img class="image" loading="lazy" src="<?php echo esc_attr($image[0]); ?>" width="<?php echo esc_attr($image[1]); ?>" height="<?php echo esc_attr($image[2]); ?>" />
   </div>
   <?php if($content['headline'.$i]) { ?>
   <h3 class="headline common_catch"><?php echo esc_html($content['headline'.$i]); ?></h3>
   <?php }; ?>
   <?php if($content['desc'.$i]) { ?>
   <p class="desc"><?php echo wp_kses_post(nl2br($content['desc'.$i])); ?></p>
   <?php }; ?>
   <?php if($content['button_url'.$i] && $content['button_label'.$i]){ ?>
   <div class="link_button">
    <div class="design_button"><span><?php echo esc_html($content['button_label'.$i]); ?></span></div>
   </div>
   <?php }; ?>
  </a>
  <?php
         };
       endfor;
  ?>

 </div>

</section><!-- END .cb_box_content -->

<?php
         // カルーセル --------------------------------------------------------------------------------
         } elseif ( $content['cb_content_select'] == 'carousel' && $content['show_content'] ) {
?>
<section class="cb_carousel num<?php echo $content_count; ?> cb_content<?php if($content['display_bg_color'] == 'hide'){ echo ' no_bg_color'; }; ?>" id="<?php echo 'cb_content_' . $content_count; ?>">

 <?php if($content['catch']){ ?>
 <h2 class="catch common_catch"><?php echo wp_kses_post(nl2br($content['catch'])); ?></h2>
 <?php }; ?>

 <?php
      $post_num = 10;
      $post_type = $content['post_type'];
      if(!$options['use_news']){
        $post_type = 'post';
      }
      $post_order = $content['post_order'];
      $layout = $content['layout'];
      $autoplay = ( isset($content['autoplay']) && $content['autoplay'] === 'off' ) ? false : true;
      if($post_type == 'news'){
        $taxonomy_name = 'news_category';
        $show_date = 'display';
      } else {
        $taxonomy_name = 'category';
        $show_date = $options['blog_show_date'];
      }
      $args = array( 'post_type' => $post_type, 'posts_per_page' => $post_num, 'orderby' => $post_order );
      $post_list = new wp_query($args);
      $total_posts = $post_list->found_posts; 
      $show_arrow = true;
      /*** 数が少ない時は自動再生を強制的にオフにする ***/
      if(($layout === 'type2' && (!is_mobile()) && $total_posts < 4) || $total_posts < 3 ){
        $autoplay = false;
        $show_arrow = false;
      }
      /*** ボタンのリンク先を指定 ***/
      $button = $content['button'] ?? '';
      $button_url = esc_url(get_permalink(get_option('page_for_posts')));
      if(  $post_type === 'news' ){
        $button_url =esc_url(get_post_type_archive_link('news'));
      }
      if($post_list->have_posts()):
 ?>

 <div class="splide index_carousel<?php if($layout == 'type2'){ echo ' type2'; }; ?> <?php if(!$show_arrow) { echo ' index_carousel__hide_arrow';}; ?>"<?php if(!$autoplay){?> data-splide='{"autoplay":false,"type":"slide"}' <?php } ?>>
  <div class="splide__arrows">
   <button class="splide__arrow splide__arrow--prev"><span>Prev</span></button>
   <button class="splide__arrow splide__arrow--next"><span>Next</span></button>
  </div>
  <div class="splide__track">
   <div class="splide__list">
    <?php
         while( $post_list->have_posts() ) : $post_list->the_post();
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
    <div class="splide__slide item">
     <a class="animate_background" href="<?php the_permalink(); ?>">
      <div class="image_wrap">
       <img class="image" src="<?php echo esc_attr($image[0]); ?>" width="<?php echo esc_attr($image[1]); ?>" height="<?php echo esc_attr($image[2]); ?>" />
      </div>
     </a>
     <?php
          $category = wp_get_post_terms( $post->ID, $taxonomy_name , array( 'orderby' => 'term_order' ));
          if ( $category && ! is_wp_error($category) ) {
            foreach ( $category as $cat ) :
              $cat_name = $cat->name;
              $cat_id = $cat->term_id;
              $cat_url = get_term_link($cat_id,$taxonomy_name);
              break;
            endforeach;
     ?>
     <a class="category" href="<?php echo esc_url($cat_url); ?>"><?php echo esc_html($cat_name); ?></a>
     <?php }; ?>
     <div class="content">
      <h3 class="title"><a href="<?php the_permalink(); ?>"><span><?php the_title(); ?></span></a></h3>
      <?php if($show_date == 'display'){ ?><time class="date entry-date published" datetime="<?php the_modified_time('c'); ?>"><?php the_time('Y.m.d'); ?></time><?php }; ?>
     </div>
    </div>
    <?php endwhile; wp_reset_query(); ?>
   </div><!-- END .splide__list -->
  </div><!-- END .splide__track -->
 </div><!-- END .splide -->
 <?php
        endif;
 ?>
 
 <?php if($button){ ?>
   <div class="link_button">
    <a href="<?php echo $button_url; ?>" class="design_button"><span><?php echo esc_html($button); ?></span></a>
   </div>
  <?php }; ?>

</section><!-- END .cb_carousel -->


<?php
         // フリースペース -----------------------------------------------------
         } elseif ( $content['cb_content_select'] == 'free_space' && $content['show_content'] ) {
?>
<section class="cb_free_space num<?php echo $content_count; ?><?php if($content['display_bg_color'] == 'hide'){ echo ' no_bg_color'; }; ?>" id="<?php echo 'cb_content_' . $content_count; ?>">

 <?php if($content['free_space']){ ?>
 <div class="post_content clearfix">
  <?php echo apply_filters('the_content', $content['free_space'] ); ?>
 </div>
 <?php }; ?>

</section><!-- END .cb_free_space -->
<?php
         };
       $content_count++;
       endforeach;
     endif;

// コンテンツビルダーここまで
?>
</div><!-- END #content_builder -->
<?php
      }; // END index_content_type
?>

<?php get_footer(); ?>