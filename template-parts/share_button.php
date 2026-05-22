<?php
     $options = get_design_plus_option();
     $url_encode = urlencode( get_permalink( $post->ID ) );
     $title_encode = urlencode( get_the_title( $post->ID ) );
     $thumnail_image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );
     if($thumnail_image){
       $pinterestimage = $thumnail_image;
     } else {
       $noimage = array();
       $noimage[0] = esc_url(get_bloginfo('template_url')) . "/img/no_image2.gif";
       $pinterestimage = $options['no_image']? wp_get_attachment_image_src( $options['no_image'], 'full' ) : $noimage;
     }

     // Type5 --------------------------------------------------------------
     if ( $options['sns_share_design_type'] === 'type5' ) :
?>
<div class="share_button_list_default">
 <ul>
  <?php if ( $options['show_sns_share_twitter'] ) { ?>
  <li class="twitter_button">
   <a href="https://twitter.com/intent/tweet?ref_src=twsrc%5Etfw" class="twitter-share-button" data-show-count="false">Post</a>
  </li>
  <?php }; ?>
  <?php if ( $options['show_sns_share_fblike'] ) { ?>
  <li class="<?php echo ( is_mobile() ) ? 'facebook' : 'fblike'; ?>_button">
   <div class="fb-like" data-href="<?php the_permalink(); ?>" data-width="" data-layout="button" data-action="like" data-size="small" data-share=""></div>
  </li>
  <?php }; ?>
  <?php if ( $options['show_sns_share_fbshare'] ) { ?>
  <li class="<?php echo ( is_mobile() ) ? 'facebook' : 'fbshare'; ?>_button2">
   <div class="fb-share-button" data-href="<?php the_permalink(); ?>" data-layout="button_count"></div>
  </li>
  <?php }; ?>
  <?php if ( $options['show_sns_share_hatena'] ) { ?>
  <li class="hatena_button">
     <a href="//b.hatena.ne.jp/entry/<?php the_permalink();?>" class="hatena-bookmark-button" data-hatena-bookmark-title="<?php the_title();?>" data-hatena-bookmark-layout="<?php echo ( is_mobile() ) ? 'simple' : 'standard'; ?>-balloon" data-hatena-bookmark-lang="<?php echo get_locale() === 'ja' ? 'ja' : 'en' ?>" title="このエントリーをはてなブックマークに追加"><img src="//b.st-hatena.com/images/v4/public/entry-button/button-only@2x.png" alt="このエントリーをはてなブックマークに追加" width="20" height="20" style="border: none;" /></a>

  </li>
  <?php }; ?>
  <?php if ( $options['show_sns_share_pocket'] ) { ?>
  <li class="pocket_button">
   <div class="socialbutton pocket-button">
    <a data-pocket-label="pocket" data-pocket-count="horizontal" class="pocket-btn" data-lang="en"></a>
   </div>
  </li>
  <?php }; ?>
  <?php if ( $options['show_sns_share_feedly'] ) { ?>
  <li class="feedly_button">
     <a href='//feedly.com/i/subscription/feed%2F<?php bloginfo('rss2_url'); ?>' target='blank'><img id='feedlyFollow' src='//s1.feedly.com/legacy/feedly-follow-rectangle-flat-small_2x.png' alt='follow us in feedly' width='66' height='20'></a>
  </li>
  <?php }; ?>
  <?php if ( $options['show_sns_share_pinterest'] ) { ?>
  <li class="pinterest_button">
   <a data-pin-do="buttonPin" data-pin-color="red" data-pin-count="beside" href="https://www.pinterest.com/pin/create/button/?url=<?php echo $url_encode ?>&media=<?php echo $pinterestimage[0]; ?>&description=<?php echo $title_encode ?>"><img src="//assets.pinterest.com/images/pidgets/pinit_fg_en_rect_red_20.png" /></a>
  </li>
  <?php }; ?>
 </ul>
</div>
<?php
     // Type1, Type2, Type3, Type4 ------------------------------------------------
     else :
?>
<div class="share_button_list <?php if($options['sns_share_design_type'] === 'type1' || $options['sns_share_design_type'] === 'type2'){ echo 'small_size'; } else { echo 'large_size'; }; if($options['sns_share_design_type'] === 'type1' || $options['sns_share_design_type'] === 'type3'){ echo ' color'; } else { echo ' mono'; }; ?>">
 <ul>
  <?php if ( $options['show_sns_share_twitter'] ) { ?>
  <li class="twitter_button">
   <a href="//twitter.com/intent/tweet?text=<?php echo $title_encode ?>&url=<?php echo $url_encode ?>&via=<?php echo esc_attr($options['twitter_info']); ?>&tw_p=tweetbutton&related=<?php echo esc_attr($options['twitter_info']); ?>"<?php if(!is_mobile()){ ?> onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=400,width=600');return false;"<?php }; ?>><span class="label">Post</span></a>
  </li>
  <?php }; ?>
  <?php if ( $options['show_sns_share_fbshare'] ) { ?>
  <li class="facebook_button">
   <a href="//www.facebook.com/sharer/sharer.php?u=<?php the_permalink() ?>&amp;t=<?php echo $title_encode ?>" class="facebook-btn-icon-link" target="blank" rel="nofollow"><span class="label">Share</span></a>
  </li>
  <?php }; ?>
  <?php if ( $options['show_sns_share_hatena'] ) { ?>
  <li class="hatena_button">
   <a href="//b.hatena.ne.jp/add?mode=confirm&url=<?php echo $url_encode ?>"<?php if(!is_mobile()){ ?> onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=400,width=510');return false;"<?php }; ?>><span class="label">Hatena</span></a>
  </li>
  <?php }; ?>
  <?php if ( $options['show_sns_share_pocket'] ) { ?>
  <li class="pocket_button">
   <a href="//getpocket.com/edit?url=<?php echo $url_encode;?>&title=<?php echo $title_encode;?>"><span class="label">Pocket</span></a>
  </li>
  <?php }; ?>
  <?php if ( $options['show_sns_share_rss'] ) { ?>
  <li class="rss_button">
   <a href="<?php bloginfo('rss2_url'); ?>"><span class="label">RSS</span></a>
  </li>
  <?php }; ?>
  <?php if ( $options['show_sns_share_feedly'] ) { ?>
  <li class="feedly_button">
   <a href="//feedly.com/index.html#subscription/feed/<?php bloginfo('rss2_url'); ?>"><span class="label">feedly</span></a>
  </li>
  <?php }; ?>
  <?php if ( $options['show_sns_share_pinterest'] ) { ?>
  <li class="pinterest_button">
   <a rel="nofollow" href="https://www.pinterest.com/pin/create/button/?url=<?php echo $url_encode; ?>&media=<?php echo $pinterestimage[0]; ?>&description=<?php echo $title_encode ?>" data-pin-do="buttonPin" data-pin-custom="true"><span class="label">Pin&nbsp;it</span></a>
  </li>
  <?php }; ?>
 </ul>
</div>
<?php
     endif; 
?>