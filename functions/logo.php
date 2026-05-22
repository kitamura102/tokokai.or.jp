<?php

//スクエアロゴ　---------------------------------------------------------------------------------------------
function header_logo(){

  global $post;
  $options = get_design_plus_option();

  $pc_image_width = '';
  $pc_image_height = '';

  $logo_image = wp_get_attachment_image_src( $options['header_logo_image'], 'full' );
  $logo_image_mobile = wp_get_attachment_image_src( $options['header_logo_image_mobile'], 'full' );

  if( $options['header_logo_type'] == 'type2' && ($logo_image || $logo_image_mobile)){

    if($logo_image){
      $pc_image_width = $logo_image[1];
      $pc_image_height = $logo_image[2];
      if($options['header_logo_retina'] == 'yes') {
        $pc_image_width = round($pc_image_width / 2);
        $pc_image_height = round($pc_image_height / 2);
      };
    };

    if($logo_image_mobile){
      $mobile_image_width = $logo_image_mobile[1];
      $mobile_image_height = $logo_image_mobile[2];
      if($options['header_logo_retina'] == 'yes') {
        $mobile_image_width = round($mobile_image_width / 2);
        $mobile_image_height = round($mobile_image_height / 2);
      };
    };

    $title = get_bloginfo('name');
    $url = home_url();
?>
<<?php $the_tag = is_front_page()? 'h1':'div'; echo $the_tag; ?> id="header_logo">
 <p class="logo">
  <a href="<?php echo esc_url($url); ?>/" title="<?php echo esc_attr($title); ?>">
   <?php if($logo_image){ ?>
   <img class="logo_image<?php if($logo_image_mobile){ echo ' pc'; }; ?>" src="<?php echo esc_attr($logo_image[0]); ?>" alt="<?php echo esc_attr($title); ?>" title="<?php echo esc_attr($title); ?>" width="<?php echo esc_attr($pc_image_width); ?>" height="<?php echo esc_attr($pc_image_height); ?>" />
   <?php }; ?>
   <?php if($logo_image_mobile){ ?>
   <img class="logo_image mobile" src="<?php echo esc_attr($logo_image_mobile[0]); ?>?<?php echo esc_attr(time()); ?>" alt="<?php echo esc_attr($title); ?>" title="<?php echo esc_attr($title); ?>" width="<?php echo esc_attr($mobile_image_width); ?>" height="<?php echo esc_attr($mobile_image_height); ?>" />
   <?php }; ?>
  </a>
 </p>
</<?php $the_tag = is_front_page()? 'h1':'div'; echo $the_tag; ?>>
<?php
  }; // if $logo_image
}


//ヘッダーバー用ロゴ　---------------------------------------------------------------------------------------------
function header_logo2(){

  global $post;
  $options = get_design_plus_option();

  $pc_image_width = '';
  $pc_image_height = '';

  $square_logo_image = wp_get_attachment_image_src( $options['header_logo_image'], 'full' );

  $logo_image1 = wp_get_attachment_image_src( $options['header_logo_image2'], 'full' );
  if($logo_image1) {
    $pc_image_width1 = $logo_image1[1];
    $pc_image_height1 = $logo_image1[2];
    if($options['header_logo_retina'] == 'yes') {
      $pc_image_width1 = round($pc_image_width1 / 2);
      $pc_image_height1 = round($pc_image_height1 / 2);
    };
  };
  $logo_image_mobile1 = wp_get_attachment_image_src( $options['header_logo_image_mobile2'], 'full' );
  if($logo_image_mobile1) {
    $mobile_image_width1 = $logo_image_mobile1[1];
    $mobile_image_height1 = $logo_image_mobile1[2];
    if($options['header_logo_retina'] == 'yes') {
      $mobile_image_width1 = round($mobile_image_width1 / 2);
      $mobile_image_height1 = round($mobile_image_height1 / 2);
    };
  };

  $logo_image2 = wp_get_attachment_image_src( $options['header_logo_image3'], 'full' );
  if($logo_image2) {
    $pc_image_width2 = $logo_image2[1];
    $pc_image_height2 = $logo_image2[2];
    if($options['header_logo_retina'] == 'yes') {
      $pc_image_width2 = round($pc_image_width2 / 2);
      $pc_image_height2 = round($pc_image_height2 / 2);
    };
  };
  $logo_image_mobile2 = wp_get_attachment_image_src( $options['header_logo_image_mobile3'], 'full' );
  if($logo_image_mobile2) {
    $mobile_image_width2 = $logo_image_mobile2[1];
    $mobile_image_height2 = $logo_image_mobile2[2];
    if($options['header_logo_retina'] == 'yes') {
      $mobile_image_width2 = round($mobile_image_width2 / 2);
      $mobile_image_height2 = round($mobile_image_height2 / 2);
    };
  };

  $title = get_bloginfo('name');
  $url = home_url();
?>
<<?php $the_tag = is_front_page()&&(!$square_logo_image||($options['header_logo_type'] == 'type1'))? 'h1':'div'; echo $the_tag; ?> id="header_logo2">
  <a href="<?php echo esc_url($url); ?>/" title="<?php echo esc_attr($title); ?>">
   <?php if( ($options['header_logo_type'] == 'type2') && ($logo_image2 || $logo_image_mobile2) && ($options['header_logo_show_icon_image'] == 'yes')){ ?>
   <div class="icon_image<?php if(!$logo_image2&&$logo_image_mobile2){ echo "_mobile"; }; ?>">
    <?php if($logo_image2){ ?>
    <img <?php if($logo_image_mobile2){ echo 'class="pc"'; }; ?> src="<?php echo esc_attr($logo_image2[0]); ?>" alt="" width="<?php echo esc_attr($pc_image_width2); ?>" height="<?php echo esc_attr($pc_image_height2); ?>" />
    <?php }; ?>
    <?php if($logo_image_mobile2){ ?>
    <img class="mobile" src="<?php echo esc_attr($logo_image_mobile2[0]); ?>" alt="" title="" width="<?php echo esc_attr($mobile_image_width2); ?>" height="<?php echo esc_attr($mobile_image_height2); ?>" />
    <?php }; ?>
   </div>
   <?php }; ?>
   <?php if( ($options['header_logo_type'] == 'type2') && ($logo_image1 || $logo_image_mobile1) ){ ?>
   <div class="text_image<?php if(!$logo_image1&&$logo_image_mobile1){ echo "_mobile"; }; ?>">
    <?php if($logo_image1){ ?>
    <img <?php if($logo_image_mobile1){ echo 'class="pc"'; }; ?> src="<?php echo esc_attr($logo_image1[0]); ?>" alt="<?php echo esc_attr($title); ?>" title="<?php echo esc_attr($title); ?>" width="<?php echo esc_attr($pc_image_width1); ?>" height="<?php echo esc_attr($pc_image_height1); ?>" />
    <?php }; ?>
    <?php if($logo_image_mobile1){ ?>
    <img class="mobile" src="<?php echo esc_attr($logo_image_mobile1[0]); ?>" alt="" title="" width="<?php echo esc_attr($mobile_image_width1); ?>" height="<?php echo esc_attr($mobile_image_height1); ?>" />
    <?php }; ?>
   </div>
   <?php }; ?>
   <?php if( $options['header_logo_type'] == 'type1' || ($options['header_logo_type'] == 'type2' && !($logo_image1) && !($logo_image2)) || ($options['header_logo_type'] == 'type2' && !($logo_image1) && $options['header_logo_show_icon_image'] == 'no') || ($options['header_logo_type'] == 'type2' && !($logo_image1) && $options['header_logo_show_icon_image'] == 'yes' && ($logo_image2))){ ?>
   <span class="logo_text rich_font_<?php echo esc_attr($options['header_logo_font_type']); ?>"><?php echo esc_html($title); ?></span>
   <?php }; ?>
  </a>
</<?php $the_tag = is_front_page()&&(!$square_logo_image||($options['header_logo_type'] == 'type1'))? 'h1':'div'; echo $the_tag; ?>>
<?php
}


//フッターロゴ　---------------------------------------------------------------------------------------------
function footer_logo(){

  global $post;
  $options = get_design_plus_option();

  $pc_image_width = '';
  $pc_image_height = '';

  $logo_image = wp_get_attachment_image_src( $options['header_logo_image'], 'full' );
  if($logo_image) {

    $pc_image_width = $logo_image[1];
    $pc_image_height = $logo_image[2];
    if($options['header_logo_retina'] == 'yes') {
      $pc_image_width = round($pc_image_width / 2);
      $pc_image_height = round($pc_image_height / 2);
    };

    $logo_image_mobile = wp_get_attachment_image_src( $options['header_logo_image_mobile'], 'full' );
    if($logo_image_mobile) {
      $mobile_image_width = $logo_image_mobile[1];
      $mobile_image_height = $logo_image_mobile[2];
      if($options['header_logo_retina'] == 'yes') {
        $mobile_image_width = round($mobile_image_width / 2);
        $mobile_image_height = round($mobile_image_height / 2);
      };
    };

    $title = get_bloginfo('name');
    $url = home_url();

?>
<div id="footer_logo">
 <p class="logo">
  <a href="<?php echo esc_url($url); ?>/" title="<?php echo esc_attr($title); ?>">
   <?php if( ($options['header_logo_type'] == 'type2') && $logo_image ){ ?>
   <img loading="lazy" class="logo_image<?php if($logo_image_mobile){ echo ' pc'; }; ?>" src="<?php echo esc_attr($logo_image[0]); ?>?<?php echo esc_attr(time()); ?>" alt="<?php echo esc_attr($title); ?>" title="<?php echo esc_attr($title); ?>" width="<?php echo esc_attr($pc_image_width); ?>" height="<?php echo esc_attr($pc_image_height); ?>" />
   <?php if($logo_image_mobile){ ?>
   <img loading="lazy" class="logo_image mobile" src="<?php echo esc_attr($logo_image_mobile[0]); ?>?<?php echo esc_attr(time()); ?>" alt="<?php echo esc_attr($title); ?>" title="<?php echo esc_attr($title); ?>" width="<?php echo esc_attr($mobile_image_width); ?>" height="<?php echo esc_attr($mobile_image_height); ?>" />
   <?php }; ?>
   <?php } else { ?>
   <span class="logo_text rich_font_<?php echo esc_attr($options['header_logo_font_type']); ?>"><?php echo esc_html($title); ?></span>
   <?php }; ?>
  </a>
 </p>
</div>
<?php
  };
}

//ドロワーロゴ　---------------------------------------------------------------------------------------------
function drawer_logo(){

  global $post;
  $options = get_design_plus_option();

  $pc_image_width = '';
  $pc_image_height = '';

  $logo_image1 = wp_get_attachment_image_src( $options['header_logo_image2'], 'full' );
  if($logo_image1) {
    $pc_image_width1 = $logo_image1[1];
    $pc_image_height1 = $logo_image1[2];
    if($options['header_logo_retina'] == 'yes') {
      $pc_image_width1 = round($pc_image_width1 / 2);
      $pc_image_height1 = round($pc_image_height1 / 2);
    };
  };
  $logo_image_mobile1 = wp_get_attachment_image_src( $options['header_logo_image_mobile2'], 'full' );
  if($logo_image_mobile1) {
    $mobile_image_width1 = $logo_image_mobile1[1];
    $mobile_image_height1 = $logo_image_mobile1[2];
    if($options['header_logo_retina'] == 'yes') {
      $mobile_image_width1 = round($mobile_image_width1 / 2);
      $mobile_image_height1 = round($mobile_image_height1 / 2);
    };
  };

  $logo_image2 = wp_get_attachment_image_src( $options['header_logo_image3'], 'full' );
  if($logo_image2) {
    $pc_image_width2 = $logo_image2[1];
    $pc_image_height2 = $logo_image2[2];
    if($options['header_logo_retina'] == 'yes') {
      $pc_image_width2 = round($pc_image_width2 / 2);
      $pc_image_height2 = round($pc_image_height2 / 2);
    };
  };
  $logo_image_mobile2 = wp_get_attachment_image_src( $options['header_logo_image_mobile3'], 'full' );
  if($logo_image_mobile2) {
    $mobile_image_width2 = $logo_image_mobile2[1];
    $mobile_image_height2 = $logo_image_mobile2[2];
    if($options['header_logo_retina'] == 'yes') {
      $mobile_image_width2 = round($mobile_image_width2 / 2);
      $mobile_image_height2 = round($mobile_image_height2 / 2);
    };
  };

  $title = get_bloginfo('name');
  $url = home_url();

?>
 <div class="logo">
  <a href="<?php echo esc_url($url); ?>/" title="<?php echo esc_attr($title); ?>">
   <?php if( ($options['header_logo_type'] == 'type2') && $logo_image2 && ($options['header_logo_show_icon_image'] == 'yes')){ ?>
   <div class="icon_image">
    <img <?php if($logo_image_mobile2){ echo 'class="pc"'; }; ?> src="<?php echo esc_attr($logo_image2[0]); ?>" alt="" width="<?php echo esc_attr($pc_image_width2); ?>" height="<?php echo esc_attr($pc_image_height2); ?>" />
    <?php if($logo_image_mobile2){ ?>
    <img class="mobile" src="<?php echo esc_attr($logo_image_mobile2[0]); ?>" alt="" title="" width="<?php echo esc_attr($mobile_image_width2); ?>" height="<?php echo esc_attr($mobile_image_height2); ?>" />
    <?php }; ?>
   </div>
   <?php }; ?>
   <?php if( ($options['header_logo_type'] == 'type2') && $logo_image1 ){ ?>
   <div class="text_image">
    <img <?php if($logo_image_mobile1){ echo 'class="pc"'; }; ?> src="<?php echo esc_attr($logo_image1[0]); ?>" alt="<?php echo esc_attr($title); ?>" title="<?php echo esc_attr($title); ?>" width="<?php echo esc_attr($pc_image_width1); ?>" height="<?php echo esc_attr($pc_image_height1); ?>" />
    <?php if($logo_image_mobile1){ ?>
    <img class="mobile" src="<?php echo esc_attr($logo_image_mobile1[0]); ?>" alt="" title="" width="<?php echo esc_attr($mobile_image_width1); ?>" height="<?php echo esc_attr($mobile_image_height1); ?>" />
    <?php }; ?>
   </div>
   <?php }; ?>
   <?php if( $options['header_logo_type'] == 'type1' || ($options['header_logo_type'] == 'type2' && !$logo_image1 && !$logo_image2) || ($options['header_logo_type'] == 'type2' && !$logo_image1 && $options['header_logo_show_icon_image'] == 'no') || ($options['header_logo_type'] == 'type2' && !$logo_image1 && $options['header_logo_show_icon_image'] == 'yes' && $logo_image2)){ ?>
   <span class="logo_text rich_font_<?php echo esc_attr($options['header_logo_font_type']); ?>"><?php echo esc_html($title); ?></span>
   <?php }; ?>
  </a>
 </div>
<?php
}


?>