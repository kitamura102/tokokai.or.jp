<?php get_header(); ?>
<section id="main">
	<section id="post-<?php the_ID(); ?>" class="content">	

		<?php if ( is_category() ) : ?>
		<h3 class="heading"><?php single_cat_title(); ?></h3>
		<?php elseif (is_day()) : ?>
		<h3 class="heading"><?php the_time('Y/m/d'); ?></h3>
		<?php elseif (is_month()) : ?>
		<h3 class="heading"><?php the_time('Y/m'); ?></h3>
		<?php elseif (is_year()) : ?>
		<h3 class="heading"><?php the_time('Y'); ?></h3>
		<?php elseif (isset($_GET['paged']) && !empty($_GET['paged'])) : ?>
		<h3 class="heading">Blog Archives</h3>
		<?php endif; ?>
	
  	<article class="post">
			<?php get_template_part('module_loop'); ?>
		</article>
	</section>
  <?php tpl_053_rwd_content_nav('nav-below'); ?>
</section>
<!-- / コンテンツ -->
<?php get_sidebar(); ?>
<?php get_footer(); ?>