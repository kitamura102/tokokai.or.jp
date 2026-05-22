<?php get_header();
$header_image = get_header_image();
if ($header_image):?>
  <div id="mainBanner">
		<?php tpl_011_rwd_print_mainImgLink();?><img src="<?php header_image(); ?>" width="<?php echo HEADER_IMAGE_WIDTH; ?>" height="<?php echo HEADER_IMAGE_HEIGHT; ?>" alt="<?php bloginfo( 'description' ); ?>"><?php tpl_011_rwd_print_mainImgLinkClose();?>
    <div class="slogan">
    <?php tpl_011_rwd_print_mainImgslogan() ?>
    <?php tpl_011_rwd_print_mainImgslogan2(); ?>
    </div>
  </div>
<?php endif;?>
  
    <?php if ( have_posts()) : the_post(); ?>
  	<section id="post-<?php the_ID(); ?>" class="content toppage">
      <h3 class="heading"><?php the_title(); ?></h3>     
      <article class="post">
     		<?php the_content();?>
    	</article>
    </section>
		<?php endif; ?>
    
    <?php query_posts($query_string . "showposts=3"); ?>
  	<?php if (have_posts()) :?>
		<section class="gridWrapper">
  		<?php while (have_posts()) : the_post(); ?>
  		<article class="grid">
      	<div class="box">
    			<?php echo get_the_post_thumbnail($post->ID, 'size1'); ?>
        	<h3><?php the_title(); ?></h3>
      		<?php the_excerpt();?>
        	<p class="readmore"><a href="<?php the_permalink() ?>">詳細を確認する</a></p>
    		</div>
      </article>  
			<?php endwhile; ?>
	  </section>
 		<?php endif; ?>

<?php get_footer(); ?>