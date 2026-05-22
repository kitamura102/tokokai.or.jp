<?php
     $options = get_design_plus_option();
     global $blog_label, $post;
?>
<div id="bread_crumb">
 <ul class="clearfix" itemscope itemtype="https://schema.org/BreadcrumbList">
 <?php
     // news archive -----------------------
     if(is_post_type_archive('news')) {
 ?>
 <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="home"><a itemprop="item" href="<?php echo esc_url(home_url('/')); ?>"><span itemprop="name"><?php _e('Home', 'tcd-w'); ?></span></a><meta itemprop="position" content="1"></li>
 <li class="last" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"><span itemprop="name"><?php echo esc_html($options['news_label']); ?></span><meta itemprop="position" content="2"></li>
 <?php
     // news taxonomy -----------------------
     } elseif(is_tax('news_category')) {
       $title = single_cat_title('', false);
 ?>
 <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="home"><a itemprop="item" href="<?php echo esc_url(home_url('/')); ?>"><span itemprop="name"><?php _e('Home', 'tcd-serum'); ?></span></a><meta itemprop="position" content="1"></li>
 <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"><a itemprop="item" href="<?php echo esc_url(get_post_type_archive_link('news')); ?>"><span itemprop="name"><?php echo esc_html($options['news_label']); ?></span></a><meta itemprop="position" content="2"></li>
 <li class="last" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"><span itemprop="name"><?php echo esc_html($title); ?></span><meta itemprop="position" content="2"></li>
 <?php
     // news single -----------------------
     } elseif(is_singular('news')) {
       $category = wp_get_post_terms( $post->ID, 'news_category' , array( 'orderby' => 'term_order' ));
 ?>
 <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="home"><a itemprop="item" href="<?php echo esc_url(home_url('/')); ?>"><span itemprop="name"><?php _e('Home', 'tcd-serum'); ?></span></a><meta itemprop="position" content="1"></li>
 <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"><a itemprop="item" href="<?php echo esc_url(get_post_type_archive_link('news')); ?>"><span itemprop="name"><?php echo esc_html($options['news_label']); ?></span></a><meta itemprop="position" content="2"></li>
 <?php
      if ( $category && ! is_wp_error($category) ) {
        foreach ( $category as $cat ) :
          $cat_name = $cat->name;
          $cat_id = $cat->term_id;
          $cat_url = get_term_link($cat_id,'news_category');
          break;
        endforeach;
 ?>
 <li class="category" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
  <a itemprop="item" href="<?php echo esc_url($cat_url); ?>"><span itemprop="name"><?php echo esc_html($cat_name); ?></span></a>
  <meta itemprop="position" content="3">
 </li>
 <?php }; ?>
 <li class="last" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"><span itemprop="name"><?php the_title_attribute(); ?></span><meta itemprop="position" content="<?php if ( $category && ! is_wp_error($category) ) { echo '4'; } else { echo '3'; }; ?>"></li>
 <?php
     // treatment archive -----------------------
     } elseif(is_post_type_archive('treatment')) {
 ?>
 <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="home"><a itemprop="item" href="<?php echo esc_url(home_url('/')); ?>"><span itemprop="name"><?php _e('Home', 'tcd-w'); ?></span></a><meta itemprop="position" content="1"></li>
 <li class="last" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"><span itemprop="name"><?php echo esc_html($options['treatment_label']); ?></span><meta itemprop="position" content="2"></li>
 <?php
     // treatment taxonomy -----------------------
     } elseif(is_tax('treatment_category')) {
       $title = single_cat_title('', false);
 ?>
 <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="home"><a itemprop="item" href="<?php echo esc_url(home_url('/')); ?>"><span itemprop="name"><?php _e('Home', 'tcd-serum'); ?></span></a><meta itemprop="position" content="1"></li>
 <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"><a itemprop="item" href="<?php echo esc_url(get_post_type_archive_link('treatment')); ?>"><span itemprop="name"><?php echo esc_html($options['treatment_label']); ?></span></a><meta itemprop="position" content="2"></li>
 <li class="last" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"><span itemprop="name"><?php echo esc_html($title); ?></span><meta itemprop="position" content="2"></li>
 <?php
     // treatment single -----------------------
     } elseif(is_singular('treatment')) {
       $category = wp_get_post_terms( $post->ID, 'treatment_category' , array( 'orderby' => 'term_order' ));
 ?>
 <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="home"><a itemprop="item" href="<?php echo esc_url(home_url('/')); ?>"><span itemprop="name"><?php _e('Home', 'tcd-serum'); ?></span></a><meta itemprop="position" content="1"></li>
 <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"><a itemprop="item" href="<?php echo esc_url(get_post_type_archive_link('treatment')); ?>"><span itemprop="name"><?php echo esc_html($options['treatment_label']); ?></span></a><meta itemprop="position" content="2"></li>
 <?php
      if ( $category && ! is_wp_error($category) ) {
        foreach ( $category as $cat ) :
          $cat_name = $cat->name;
          $cat_id = $cat->term_id;
          $cat_url = get_term_link($cat_id,'treatment_category');
          break;
        endforeach;
 ?>
 <li class="category" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
  <a itemprop="item" href="<?php echo esc_url($cat_url); ?>"><span itemprop="name"><?php echo esc_html($cat_name); ?></span></a>
  <meta itemprop="position" content="3">
 </li>
 <?php }; ?>
 <li class="last" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"><span itemprop="name"><?php the_title_attribute(); ?></span><meta itemprop="position" content="<?php if ( $category && ! is_wp_error($category) ) { echo '4'; } else { echo '3'; }; ?>"></li>
 <?php
     // treatment single -----------------------
     } elseif(is_singular('treatment')) {
 ?>
 <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="home"><a itemprop="item" href="<?php echo esc_url(home_url('/')); ?>"><span itemprop="name"><?php _e('Home', 'tcd-serum'); ?></span></a><meta itemprop="position" content="1"></li>
 <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"><a itemprop="item" href="<?php echo esc_url(get_post_type_archive_link('treatment')); ?>"><span itemprop="name"><?php echo esc_html($options['treatment_label']); ?></span></a><meta itemprop="position" content="2"></li>
 <li class="last" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"><span itemprop="name"><?php the_title_attribute(); ?></span><meta itemprop="position" content="3"></li>
 <?php
      // Search -----------------------
      } elseif(is_search()) {
 ?>
 <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="home"><a itemprop="item" href="<?php echo esc_url(home_url('/')); ?>"><span itemprop="name"><?php _e('Home', 'tcd-serum'); ?></span></a><meta itemprop="position" content="1"></li>
 <li class="last" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"><span itemprop="name"><?php _e('Search result','tcd-serum'); ?></span><meta itemprop="position" content="2"></li>
 <?php
      // Blog page -----------------------
      } elseif(is_home()) {
 ?>
 <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="home"><a itemprop="item" href="<?php echo esc_url(home_url('/')); ?>"><span itemprop="name"><?php _e('Home', 'tcd-serum'); ?></span></a><meta itemprop="position" content="1"></li>
 <li class="last" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"><span itemprop="name"><?php echo esc_html($blog_label); ?></span><meta itemprop="position" content="2"></li>
 <?php
      // Category, Tag , Archive page -----------------------
      } elseif(is_category() || is_tag() || is_day() || is_month() || is_year() || is_author()) {
        if (is_category()) {
          $title = single_cat_title('', false);
        } elseif( is_tag() ) {
          $title = single_tag_title('', false);
        } elseif (is_day()) {
          $title = sprintf(__('Archive for %s', 'tcd-serum'), get_the_time(__('F jS, Y', 'tcd-serum')) );
        } elseif (is_month()) {
          $title = sprintf(__('Archive for %s', 'tcd-serum'), get_the_time(__('F, Y', 'tcd-serum')) );
        } elseif (is_year()) {
          $title = sprintf(__('Archive for %s', 'tcd-serum'), get_the_time(__('Y', 'tcd-serum')) );
        } elseif (is_author()) {
          $author_info = $wp_query->get_queried_object();
          $author_name = $author_info->display_name;
          $title = $author_name;
          $title = sprintf( __( '%s blog list', 'tcd-serum' ), $title );
        };
 ?>
 <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="home"><a itemprop="item" href="<?php echo esc_url(home_url('/')); ?>"><span itemprop="name"><?php _e('Home', 'tcd-serum'); ?></span></a><meta itemprop="position" content="1"></li>
 <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"><a itemprop="item" href="<?php echo esc_url(get_permalink(get_option('page_for_posts'))); ?>"><span itemprop="name"><?php echo esc_html($blog_label); ?></span></a><meta itemprop="position" content="2"></li>
 <li class="last" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"><span itemprop="name"><?php echo esc_html($title); ?></span><meta itemprop="position" content="3"></li>
 <?php
      //  Page -----------------------
      } elseif(is_page()) {
        $ancestors_ids = array_reverse(get_post_ancestors( $post ));
        $content_num = 2;
 ?>
 <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="home"><a itemprop="item" href="<?php echo esc_url(home_url('/')); ?>"><span itemprop="name"><?php _e('Home', 'tcd-serum'); ?></span></a><meta itemprop="position" content="1"></li>
 <?php
      if(!empty($ancestors_ids)){
        foreach($ancestors_ids as $page_id):
 ?>
 <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"><a itemprop="item" href="<?php echo esc_url(get_permalink($page_id)); ?>"><span itemprop="name"><?php echo esc_html(get_the_title($page_id)); ?></span></a><meta itemprop="position" content="<?php echo esc_attr($content_num); ?>"></li>
 <?php
          $content_num++;
        endforeach;
      };
 ?>
 <li class="last" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"><span itemprop="name"><?php the_title_attribute(); ?></span><meta itemprop="position" content="<?php echo esc_attr($content_num); ?>"></li>
 <?php
      //  Attachment page -----------------------
      } elseif(is_attachment()) {
 ?>
 <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="home"><a itemprop="item" href="<?php echo esc_url(home_url('/')); ?>"><span itemprop="name"><?php _e('Home', 'tcd-serum'); ?></span></a><meta itemprop="position" content="1"></li>
 <li class="last" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"><span itemprop="name"><?php the_title_attribute(); ?></span><meta itemprop="position" content="2"></li>
 <?php
      // Other page -----------------------
      } else {
      $category = get_the_category();
 ?>
 <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="home"><a itemprop="item" href="<?php echo esc_url(home_url('/')); ?>"><span itemprop="name"><?php _e('Home', 'tcd-serum'); ?></span></a><meta itemprop="position" content="1"></li>
 <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"><a itemprop="item" href="<?php echo esc_url(get_permalink(get_option('page_for_posts'))); ?>"><span itemprop="name"><?php echo esc_html($blog_label); ?></span></a><meta itemprop="position" content="2"></li>
 <?php if($category) { ?>
 <li class="category" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
  <?php
       $count=1;
       foreach ($category as $cat) {
  ?>
  <a itemprop="item" href="<?php echo esc_url(get_category_link($cat->term_id)); ?>"><span itemprop="name"><?php echo esc_html($cat->name); ?></span></a>
  <?php $count++; } ?>
  <meta itemprop="position" content="3">
 </li>
 <?php }; ?>
 <li class="last" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"><span itemprop="name"><?php the_title_attribute(); ?></span><meta itemprop="position" content="<?php if ( $category ) { echo '4'; } else { echo '3'; }; ?>"></li>
 <?php }; ?>
 </ul>
</div>
