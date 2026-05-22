<?php get_header(); ?>
<div id="wrapper">	

<div id="content">
<section>
  <article id="post-<?php the_ID(); ?>" class="content">
	  <header>  	
      <h2 class="title first"><?php _e('Page not found'); ?></h2>
    </header>
		<p><?php _e("No posts found."); ?>
	</article>        
            
</section>
<?php get_footer(); ?>