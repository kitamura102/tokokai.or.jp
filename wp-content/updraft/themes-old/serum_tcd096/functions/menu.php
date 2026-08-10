<?php
/**
 * Add data-megamenu attributes to the global navigation
 */
function nano_walker_nav_menu_start_el( $item_output, $item, $depth, $args ) {

  $options = get_design_plus_option();

  if ( 'global-menu' !== $args->theme_location ) return $item_output;

  if ( ! isset( $options['megamenu_new'][$item->ID] ) ) return $item_output;

  if ( 'dropdown' === $options['megamenu_new'][$item->ID] ) return $item_output;

  if ( 'use_megamenu_a' === $options['megamenu_new'][$item->ID] ) {
    return sprintf( '<a href="%s" class="megamenu_button megamenu_type2" data-megamenu="js-megamenu%d">%s</a>', $item->url, $item->ID, $item->title );
  }
  if ( 'use_megamenu_b' === $options['megamenu_new'][$item->ID] ) {
    return sprintf( '<a href="%s" class="megamenu_button megamenu_type3" data-megamenu="js-megamenu%d">%s</a>', $item->url, $item->ID, $item->title );
  }
  if ( 'use_megamenu_c' === $options['megamenu_new'][$item->ID] ) {
    return sprintf( '<a href="%s" class="megamenu_button megamenu_type4" data-megamenu="js-megamenu%d">%s</a>', $item->url, $item->ID, $item->title );
  }

}

add_filter( 'walker_nav_menu_start_el', 'nano_walker_nav_menu_start_el', 10, 4 );

// Mega menu A -  Category post ---------------------------------------------------------------
function render_megamenu_a( $id, $megamenus ) {
  global $post;
  $options = get_design_plus_option();
  if(isset($megamenus[$id])){
?>
<div class="megamenu megamenu_a" id="js-megamenu<?php echo esc_attr( $id ); ?>">
 <div class="megamenu_inner">

  <ul class="category_list">
   <?php
        $i = 1;
        foreach ( $megamenus[$id] as $menu ) :
          if ( $menu->object != 'news_category') continue;
          $cat_id = $menu->object_id;
          $cat_name = $menu->title;
          $url = $menu->url;
   ?>
   <li<?php if($i == 1) { echo ' class="active"'; }; ?>><a data-cat-id="mega_cat_id<?php echo esc_attr($cat_id); ?>" class="cat_id<?php echo esc_attr($cat_id); ?>" href="<?php echo esc_url($url); ?>"><?php echo esc_html($cat_name); ?></a></li>
   <?php $i++; endforeach; ?>
  </ul>

  <div class="post_list_area">
   <?php
       $post_list_count = 1;
       foreach ( $megamenus[$id] as $menu ) :
         if ( $menu->object != 'news_category') continue;
         $post_type = 'news';
         $category_type = 'news_category';
         $display_date = 'display';
         $cat_id = $menu->object_id;
           $args = array( 'post_type' => $post_type, 'posts_per_page' => 4, 'tax_query' => array( array( 'taxonomy' => $category_type, 'field' => 'term_id', 'terms' => $cat_id ) ) );
           $post_list = new wp_query($args);
           if($post_list->have_posts()):
   ?>
   <div class="post_list mega_cat_id<?php echo esc_attr($cat_id); ?>"<?php if($post_list_count != 1){ echo ' style="display:none;"'; }; ?>>
    <?php
         while( $post_list->have_posts() ) : $post_list->the_post();
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
       <div class="image">
        <img loading="lazy" src="<?php echo esc_attr($image[0]); ?>" width="<?php echo esc_attr($image[1]); ?>" height="<?php echo esc_attr($image[2]); ?>" />
       </div>
      </div>
     </a>
     <div class="content">
      <?php if ($display_date == 'display'){ ?>
      <time class="date entry-date published" datetime="<?php the_modified_time('c'); ?>"><?php the_time('Y.m.d'); ?></time>
      <?php }; ?>
      <p class="title"><a href="<?php the_permalink(); ?>"><span><?php the_title_attribute(); ?></span></a></p>
     </div>
    </div>
    <?php endwhile; wp_reset_query(); ?>
   </div>
   <?php endif; // END end post list ?>
   <?php $post_list_count++; endforeach; ?>
  </div><!-- END post_list_area -->

 </div><!-- END .megamenu_inner -->
</div><!-- END .megamenu_a -->
<?php
  };
}


// Mega menu B - Post carousel ---------------------------------------------------------------
function render_megamenu_b( $id, $megamenus ) {
  global $post;
  $options = get_design_plus_option();
?>
<div class="megamenu megamenu_b" id="js-megamenu<?php echo esc_attr( $id ); ?>">
 <div class="megamenu_inner">

  <?php
       $post_type = $options['megamenu_a_post_type'] ? $options['megamenu_a_post_type'] : 'recent_post';
       $post_order = $options['megamenu_a_post_order'] ? $options['megamenu_a_post_order'] : 'date';
       $post_num = $options['megamenu_a_post_num'] ? $options['megamenu_a_post_num'] : '8';
       $taxonomy_name = 'category';
       $show_date = $options['blog_show_date'];
       if($post_type == 'recent_post') {
         $args = array('post_type' => 'post', 'posts_per_page' => $post_num, 'ignore_sticky_posts' => 1, 'orderby' => $post_order);
       } else {
         $args = array('post_type' => 'post', 'posts_per_page' => $post_num, 'ignore_sticky_posts' => 1, 'orderby' => $post_order, 'meta_key' => $post_type, 'meta_value' => 'on');
       }
       $post_list = new wp_query($args);
       if($post_list->have_posts()):
         $num_post = $post_list->post_count;
  ?>

  <?php if($num_post > 4){ ?>
  <div class="post_list splide mega_carousel">
   <div class="splide__arrows mega_menu_arrow">
    <button class="splide__arrow splide__arrow--prev"><span>Prev</span></button>
    <button class="splide__arrow splide__arrow--next"><span>Next</span></button>
   </div>
   <div class="splide__track">
    <div class="splide__list">
  <?php } else { ?>
  <div class="post_list no_carousel">
   <?php }; ?>
     <?php
          while( $post_list->have_posts() ) : $post_list->the_post();
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
     <div class="<?php if($num_post > 4){ echo 'splide__slide '; }; ?>item">
      <a class="animate_background" href="<?php the_permalink(); ?>">
       <div class="image_wrap">
        <div class="image">
         <img loading="lazy" src="<?php echo esc_attr($image[0]); ?>" width="<?php echo esc_attr($image[1]); ?>" height="<?php echo esc_attr($image[2]); ?>" />
        </div>
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
       <p class="title"><a href="<?php the_permalink(); ?>"><span><?php the_title(); ?></span></a></p>
       <?php if($show_date == 'display'){ ?><time class="date entry-date published" datetime="<?php the_modified_time('c'); ?>"><?php the_time('Y.m.d'); ?></time><?php }; ?>
      </div>
     </div>
     <?php endwhile; wp_reset_query(); ?>
  <?php if($num_post > 4){ ?>
    </div><!-- END .splide__list -->
   </div><!-- END .splide__track -->
  </div><!-- END .mega_carousel -->
  <?php } else { ?>
  </div><!-- END .post_list -->
  <?php }; ?>
  <?php
         endif;
  ?>

 </div><!-- END .megamenu_inner -->
</div><!-- END .megamenu_b -->
<?php
};


// Mega menu C - Treatment category ---------------------------------------------------------------
function render_megamenu_c( $id, $megamenus ) {
  $options = get_design_plus_option();
  if(isset($megamenus[$id])){
?>
<div class="megamenu megamenu_c" id="js-megamenu<?php echo esc_attr( $id ); ?>">
 <div class="megamenu_inner">

  <div class="splide mega_treatment_category_wrap">
   <?php
        $menu_count = 0;
        foreach ( $megamenus[$id] as $menu ) :
          if ( $menu->object != 'treatment_category') continue;
          $menu_count++;
        endforeach;
        if($menu_count > 2){
   ?>
   <div class="splide__arrows mega_menu_arrow">
    <button class="splide__arrow splide__arrow--prev"><span>Prev</span></button>
    <button class="splide__arrow splide__arrow--next"><span>Next</span></button>
   </div>
   <?php }; ?>
   <div class="splide__track">
    <div class="splide__list mega_treatment_category">

     <?php
          foreach ( $megamenus[$id] as $menu ) :
            if ( $menu->object != 'treatment_category') continue;
            $cat_id = $menu->object_id;
            $cat_name = $menu->title;
            $url = $menu->url;
            $term_meta = get_option( 'taxonomy_' . $cat_id, array() );
            $image = isset($term_meta['header_image']) ? wp_get_attachment_image_src( $term_meta['header_image'], 'size3' ) : '';
            $desc = isset($term_meta['mega_desc']) ?  $term_meta['mega_desc'] : '';
     ?>
     <div class="splide__slide item">
      <a class="animate_background" href="<?php echo esc_url($url); ?>">
       <?php if($image){ ?>
       <div class="image_wrap">
        <div class="image">
         <img loading="lazy" src="<?php echo esc_attr($image[0]); ?>" width="<?php echo esc_attr($image[1]); ?>" height="<?php echo esc_attr($image[2]); ?>" />
        </div>
       </div>
       <?php }; ?>
       <p class="title"><?php echo esc_html($cat_name); ?></p>
       <?php if($desc){ ?>
       <div class="desc">
        <p><span><?php echo wp_kses_post(nl2br($desc)); ?><span></p>
       </div>
       <?php }; ?>
      </a>
     </div>
     <?php  endforeach; ?>

    </div>
   </div>
  </div>

 </div><!-- END .megamenu_inner -->
</div><!-- END .megamenu_c -->
<?php
  };
};

?>