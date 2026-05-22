<?php if (have_posts()) :?>
	<?php while (have_posts()) : the_post(); ?>

		<article id="post-<?php the_ID(); ?>" class="archive">
    		<p><time datetime="<?php the_time('Y-m-d')?>"><?php the_time("Y/n/j"); ?></time> <a href="<?php the_permalink(); ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><span><?php the_title(); ?></span></a>
  	</article>
	
	<?php endwhile; ?>
<?php else: ?>
	<p><?php _e('Sorry, no posts matched your criteria.'); ?>
<?php endif; ?>
