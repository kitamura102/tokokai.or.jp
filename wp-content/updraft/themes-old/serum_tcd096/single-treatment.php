<?php
     get_header();
     $options = get_design_plus_option();
     $category = wp_get_post_terms( $post->ID, 'treatment_category' , array( 'orderby' => 'term_order' ));
     if ( $category && ! is_wp_error($category) ) {
       foreach ( $category as $cat ) :
         $cat_name = $cat->name;
         $cat_id = $cat->term_id;
         $cat_url = get_term_link($cat_id,'treatment_category');
         break;
       endforeach;
     };
     $term_meta = get_option( 'taxonomy_' . isset($cat_id), array() );
     $image = !empty($term_meta['header_image']) ? wp_get_attachment_image_src( $term_meta['header_image'], 'full' ) : wp_get_attachment_image_src($options['archive_treatment_header_bg_image'], 'full');
     $image_mobile = '';
     $overlay_color = !empty($term_meta['overlay_color']) ? $term_meta['overlay_color'] : '#000000';
     $overlay_color = hex2rgb($overlay_color);
     $overlay_color = implode(",",$overlay_color);
     if(!isset($term_meta['overlay_opacity'])){
       $overlay_opacity = '0.2';
     } else {
       $overlay_opacity = $term_meta['overlay_opacity'];
     }
?>
<div id="page_header_small">
 <h1 class="catch"><?php echo the_title(); ?></h1>
 <?php if(!empty($image)) { ?>
 <div class="overlay" style="background:rgba(<?php echo esc_attr($overlay_color); ?>,<?php echo esc_attr($overlay_opacity); ?>);"></div>
 <div class="bg_image">
  <img src="<?php echo esc_attr($image[0]); ?>"<?php if($image_mobile) { ?> srcset="<?php echo esc_attr($image_mobile[0]); ?> 800w, <?php echo esc_attr($image[0]); ?> 801w"<?php }; ?> width="<?php echo esc_attr($image[1]); ?>" height="<?php echo esc_attr($image[2]); ?>" />
 </div>
 <?php }; ?>
</div>

<?php get_template_part('template-parts/breadcrumb'); ?>
<?php if(isset($cat_id)){  ?>

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

 <?php
      // CTA ---------------------------------------
      get_template_part('template-parts/cta');
 ?>

</article><!-- END #single_treatment -->

 <?php
      // 診療一覧 -------------------------------
      $list_headline = isset($term_meta['list_headline']) ?  $term_meta['list_headline'] : '';
      $list_desc = isset($term_meta['list_desc']) ?  $term_meta['list_desc'] : '';
 ?>
 <section id="treatment_list">
  <?php if($list_headline || $list_desc){ ?>
  <div class="header">
   <?php if($list_headline){ ?>
   <h3 class="catch common_catch"><?php echo esc_html($list_headline); ?></h3>
   <?php }; ?>
   <?php if($list_desc){ ?>
   <p class="desc"><?php echo wp_kses_post(nl2br($list_desc)); ?></p>
   <?php }; ?>
  </div>
  <?php }; ?>
  <?php
       $args = array( 'post_type' => 'treatment', 'posts_per_page' => -1, 'orderby' => array('menu_order' => 'ASC', 'date' => 'DESC'), 'tax_query' => array( array( 'taxonomy' => 'treatment_category', 'field' => 'id', 'terms' => $cat_id)) );
       //$args = array( 'post_type' => 'treatment', 'posts_per_page' => -1, 'orderby' => array('menu_order' => 'ASC', 'date' => 'DESC') );
       $post_list = new wp_query($args);
       if($post_list->have_posts()):
  ?>
  <div class="post_list">
   <?php
        while( $post_list->have_posts() ) : $post_list->the_post();
          if(has_post_thumbnail()) {
            $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'size1' );
          } else {
            $image = array();
            $image[0] = get_bloginfo('template_url') . "/img/common/no_image1.gif";
            $image[1] = '250';
            $image[2] = '250';
          }
   ?>
   <div class="item">
    <a class="link animate_background" href="<?php the_permalink(); ?>">
     <div class="image_wrap">
      <div class="image">
       <img loading="lazy" src="<?php echo esc_attr($image[0]); ?>" width="<?php echo esc_attr($image[1]); ?>" height="<?php echo esc_attr($image[2]); ?>" />
      </div>
     </div>
     <h4 class="title"><span><?php the_title(); ?></span></h4>
    </a>
   </div>
   <?php endwhile; wp_reset_query(); ?>
  </div>
  <?php endif; ?>

  <div class="link_button">
   <a class="design_button" href="<?php echo esc_url(get_post_type_archive_link('treatment')); ?>"><span><?php echo esc_html($options['treatment_label']); ?></span></a>
  </div>

 </section>
<?php }else{ ?>
<div id="single_treatment">
 <div id="treatment_list">
  <div class="post_content clearfix">
    <p style="text-align: center; color: #ed2c00;"><?php echo esc_attr( __( 'Please register category', 'tcd-serum' ) ); ?></p>
  </div>
 </div>
</div><!-- END #archive_blog -->
<?php } ?>
<?php get_footer(); ?>