<?php get_header();?>
    
    <?php query_posts($query_string . "showposts=4"); ?>
  	<?php if (have_posts()) :?>
		<section class="gridWrapper">
  		<?php while (have_posts()) : the_post(); ?>
  		<article class="grid">
      	<div class="box">
        	<h3><?php the_title(); ?></h3>
    			<p class="img"><?php echo get_the_post_thumbnail($post->ID, 'size1'); ?></p>
      		<?php the_excerpt();?>
        	<p class="readmore"><a href="<?php the_permalink() ?>">詳細を確認する</a></p>
    		</div>
      </article>  
			<?php endwhile; ?>
	  </section>
 		<?php wp_reset_query();endif; ?>
    
    <?php if ( have_posts()) : the_post(); ?>
  	<section id="post-<?php the_ID(); ?>" class="content">
      <h3 class="heading"><?php the_title(); ?></h3>     
      <article class="post">
     		<?php the_content();?>
    	</article>
    </section>
		<?php endif; ?>

<?php get_footer(); ?>