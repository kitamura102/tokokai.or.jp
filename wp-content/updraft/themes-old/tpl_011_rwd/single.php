<?php get_header(); ?>

<section id="main">
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	<section id="post-<?php the_ID(); ?>" class="content">	
    <h3 class="heading"><?php the_title(); ?></h3>   
    <article class="post">
    <p class="dateLabel"><time datetime="<?php the_time('Y-m-d')?>"><?php the_time('Y/m/d')?></time> 
			<?php the_content(); ?>
      <?php wp_link_pages('before=<p id="pageLinks">ページ:&after=</p>'); ?> 
		</article>
  </section>
	<?php endwhile;?>
  <div class="pagenav">
			<span class="prev"><?php previous_post_link( '%link', '&laquo; 前のページ' ); ?></span>          
			<span class="next"><?php next_post_link( '%link', '次のページ &raquo;' ); ?></span>
	</div>
  <?php endif; ?>
</section>
<!-- / コンテンツ -->
<?php get_sidebar(); ?>
<?php get_footer(); ?>