<?php get_header(); ?>

<!-- コンテンツ -->
<section id="main">
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	<section id="post-<?php the_ID(); ?>" class="content">

    <h3 class="heading"><?php the_title(); ?></h3>     
    <article class="post">
			<?php the_content(); ?>
    <?php wp_link_pages('before=<p id="pageLinks">ページ:&after='); ?> 
  	</article>
  </section>
	<?php endwhile;?>
  <?php endif; ?>
</section>
<!-- / コンテンツ -->
<?php get_sidebar(); ?>

<?php get_footer(); ?>