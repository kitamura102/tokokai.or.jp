global $post;
while ($this->lcp_query->have_posts()) :
  $this->lcp_query->the_post();

  // Check if protected post should be displayed
  if (!$this->check_show_protected($post)) continue;

  //Start a List Item for each post:
  $lcp_display_output .= $this->open_inner_tag($post, 'li');

  //Post Thumbnail
  $lcp_display_output .= $this->get_thumbnail($post);

  //Show date:
  $lcp_display_output .= $this->get_date($post);

  //Show date modified:
  $lcp_display_output .= $this->get_modified_date($post);


  //Show the title and link to the post:
  $lcp_display_output .= $this->get_post_title($post);

  // Show categories
  $lcp_display_output .= $this->get_posts_cats($post);

  // Show tags
  $lcp_display_output .= $this->get_posts_tags($post);

  //Show comments:
  $lcp_display_output .= $this->get_comments($post);


  //Show author
  $lcp_display_output .= $this->get_author($post);

  // Show post ID
  $lcp_display_output .= $this->get_display_id($post);

  //Custom fields:
  $lcp_display_output .= $this->get_custom_fields($post);
