<?php

class styled_post_list1_widget extends WP_Widget {

  function __construct() {
    parent::__construct(
      'styled_post_list1_widget',// ID
      __( 'Styled post list (tcd ver)', 'tcd-serum' ),
      array(
        'classname' => 'styled_post_list1_widget',
        'description' => __('Displays styled post list.', 'tcd-serum')
      )
    );
  }

  // Extract Args //
  function widget($args, $instance) {

    extract( $args );
    $title = apply_filters('widget_title', $instance['title']);
    $post_num = $instance['post_num'];
    $post_type = $instance['post_type'];
    $post_order = $instance['post_order'];
    $category = '';
    
    // Post type and category handling
    if($post_type === 'recent_post'){
      $post_type = 'post';
    }
    if($post_type === 'category'){
      $post_type = 'post';
      $category = $instance['category'];
    }
     
    if($post_type === 'news_category'){
      $post_type = 'news';
      $category = $instance['news_category']; 
    }
     
    if($post_type === 'treatment_category'){
      $post_type = 'treatment';
      $category = $instance['treatment_category'];
    }

    // Before widget //
    echo $before_widget;

    // Title of widget //
    if ( $title ) { echo $before_title . $title . $after_title; }

    // Widget output //
    if ( in_array($instance['post_type'], ['category', 'news_category', 'treatment_category']) && $category ) {
      $args = array(
        'post_type' => $post_type,
        'ignore_sticky_posts' => 1,
        'posts_per_page' => $post_num,
        'orderby' => $post_order,
        'tax_query' => array(
          array(
            'taxonomy'         => $instance['post_type'],
            'field'            => 'term_id',
            'terms'            => array( $category),
            'operator'         => 'IN',
            'include_children' => true,
          ),
        ),
      );
    } else if ($post_type == 'recommend_post' || $post_type == 'featured_post') {
      $args = array('post_type' => 'post',  'posts_per_page' => $post_num, 'ignore_sticky_posts' => 1, 'orderby' => $post_order, 'meta_key' => $post_type, 'meta_value' => 'on');
    } else {
      $args = array('post_type' => $post_type ?? 'post', 'posts_per_page' => $post_num, 'ignore_sticky_posts' => 1, 'orderby' => $post_order);
    };

    $options = get_design_plus_option();
    $styled_post_list = new WP_Query($args);

?>
<ol class="styled_post_list1">
<?php
    if ($styled_post_list->have_posts()) {
      while ($styled_post_list->have_posts()) : $styled_post_list->the_post();
        if(has_post_thumbnail()) {
          $image = wp_get_attachment_image_src( get_post_thumbnail_id( $styled_post_list->ID ), 'size1' );
        } elseif($options['no_image']) {
          $image = wp_get_attachment_image_src( $options['no_image'], 'full' );
        } else {
          $image = array();
          $image[0] = esc_url(get_bloginfo('template_url')) . "/img/common/no_image1.gif";
        }
?>
 <li>
  <a class="link animate_background" href="<?php the_permalink() ?>">
   <div class="image_wrap">
   <div class="image">
     <img loading="lazy" src="<?php echo esc_attr($image[0]); ?>" width="<?php echo esc_attr($image[1]); ?>" height="<?php echo esc_attr($image[2]); ?>" />
    </div>
   </div>
   <div class="title"><span><?php the_title_attribute(); ?></span></div>
  </a>
 </li>
 <?php endwhile; wp_reset_query(); } else { ?>
 <li class="no_post"><?php _e('There is no registered post.', 'tcd-serum');  ?></li>
 <?php }; ?>
</ol>
<?php

    // After widget //
    echo $after_widget;

  } // end function widget


  // Update Settings //
  function update($new_instance, $old_instance) {
    $instance['title'] = strip_tags($new_instance['title'] ?? '');
    $instance['post_num'] = $new_instance['post_num'];
    $instance['post_order'] = $new_instance['post_order'];
    $instance['post_type'] = $new_instance['post_type'];
    $instance['category'] = $new_instance['category'] ?? '';
    $instance['news_category'] = $new_instance['news_category'] ?? '';
    $instance['treatment_category'] = $new_instance['treatment_category'] ?? '';
    return $instance;
  }

  // Widget Control Panel //
  function form($instance) {
    global $dp_options, $blog_label;
    if ( ! $dp_options ) $dp_options = get_design_plus_option();
    
    $defaults = array( 'title' => __('Recent post', 'tcd-serum'), 'post_num' => 3, 'post_order' => 'date', 'post_type' => 'recent_post', 'category' => '', 'news_category' => '','treatment_category' => '');
    $instance = wp_parse_args( (array) $instance, $defaults );
?>
<div class="tcd_widget_content">
 <h3 class="tcd_widget_headline"><?php _e('Title', 'tcd-serum'); ?></h3>
 <input class="widefat" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $instance['title']; ?>" />
</div>
<div class="tcd_widget_content tcd_toggle_widget_box">
  <h3 class="tcd_widget_headline"><?php _e( 'Post type', 'tcd-serum' ); ?></h3>
      <select id="<?php echo $this->get_field_id( 'post_type' ); ?>" name="<?php echo $this->get_field_name( 'post_type' ); ?>" class="widefat js-styled-post-list-tab-post_type">
  		    <option value="recent_post" <?php selected( 'post', $instance['post_type'] ); ?>><?php echo $blog_label ?></option>
          <option value="recommend_post" <?php selected('recommend_post', $instance['post_type']); ?>><?php _e('Recommend post', 'tcd-serum'); ?></option>
          <option value="featured_post" <?php selected('featured_post', $instance['post_type']); ?>><?php _e('Featured post', 'tcd-serum'); ?></option>
          <option value="category" <?php selected( 'category', $instance['post_type'] ); ?>><?php _e( 'Category', 'tcd-serum' ); ?></option>
          <hr>
          <option value="news" <?php selected( 'news', $instance['post_type'] ); ?>><?php echo $dp_options['news_label'] ?></option>
          <option value="news_category" <?php selected( 'news_category', $instance['post_type'] ); ?>><?php echo sprintf(__('%scate', 'tcd-serum'), $dp_options['news_label']) ?></option>
          <hr>
          <option value="treatment" <?php selected( 'treatment', $instance['post_type'] ); ?>><?php echo $dp_options['treatment_label'] ?></option>
          <option value="treatment_category" <?php selected( 'treatment_category', $instance['post_type'] ); ?>><?php echo sprintf(__('%scate', 'tcd-serum'), $dp_options['treatment_label']) ?></option>
      </select>
      <?php
        $categories = array(
            'category'            => array('label' => __('Category', 'tcd-serum'), 'taxonomy' => 'category'),
            'news_category' => array('label' => sprintf(__('%scate', 'tcd-serum'), $dp_options['news_label']), 'taxonomy' => 'news_category'),
            'treatment_category' => array('label' => sprintf(__('%scate', 'tcd-serum'), $dp_options['treatment_label']), 'taxonomy' => 'treatment_category'),
        );
        foreach ($categories as $key => $settings) {
          $is_hidden = $key === $instance['post_type'] ? '' : ' hidden';
            ?>
            <p class="styled-post-list-tab-post_type-<?php echo $key . $is_hidden; ?>">
                <label for="<?php echo $this->get_field_id($key); ?>"><?php echo $settings['label']; ?></label>
                <?php
                wp_dropdown_categories(
                    array(
                        'class'        => 'widefat',
                        'echo'         => 1,
                        'hide_empty'   => 0,
                        'hierarchical' => 1,
                        'id'           => $this->get_field_id($key),
                        'name'         => $this->get_field_name($key),
                        'selected'     => $instance[$key],
                        'show_count'   => 0,
                        'taxonomy'     => $settings['taxonomy'],
                        'value_field'  => 'term_id',
                    )
                );
                ?>
            </p>
            <?php
        }
        ?>
</div>

<div class="tcd_widget_content">
 <h3 class="tcd_widget_headline"><?php _e('Number of post', 'tcd-serum'); ?></h3>
 <select name="<?php echo $this->get_field_name('post_num'); ?>" class="widefat" style="width:100%;">
  <option value="2" <?php selected('2', $instance['post_num']); ?>>2</option>
  <option value="3" <?php selected('3', $instance['post_num']); ?>>3</option>
  <option value="4" <?php selected('4', $instance['post_num']); ?>>4</option>
  <option value="5" <?php selected('5', $instance['post_num']); ?>>5</option>
  <option value="6" <?php selected('6', $instance['post_num']); ?>>6</option>
  <option value="7" <?php selected('7', $instance['post_num']); ?>>7</option>
  <option value="8" <?php selected('8', $instance['post_num']); ?>>8</option>
  <option value="9" <?php selected('9', $instance['post_num']); ?>>9</option>
  <option value="10" <?php selected('10', $instance['post_num']); ?>>10</option>
 </select>
</div>
<div class="tcd_widget_content">
 <h3 class="tcd_widget_headline"><?php _e('Post order', 'tcd-serum'); ?></h3>
 <select name="<?php echo $this->get_field_name('post_order'); ?>" class="widefat" style="width:100%;">
  <option value="date" <?php selected('date', $instance['post_order']); ?>><?php _e('Post date', 'tcd-serum'); ?></option>
  <option value="rand" <?php selected('rand', $instance['post_order']); ?>><?php _e('Random', 'tcd-serum'); ?></option>
 </select>
</div>
<?php

  } // end function form

} // end class


function register_styled_post_list1_widget() {
  register_widget( 'styled_post_list1_widget' );
}
add_action( 'widgets_init', 'register_styled_post_list1_widget' );

?>