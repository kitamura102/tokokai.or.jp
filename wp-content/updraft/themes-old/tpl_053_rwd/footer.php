</div>
<!-- / WRAPPER -->

<!-- フッター -->
<footer id="footer">
	<div class="inner">
  
  	<!-- 3カラム -->
    <section class="gridWrapper">
		
			<article class="grid">
	  		<!-- ロゴ -->
				<p class="logo">		
  				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
						<?php tpl_053_rwd_print_Logo_name(); ?>
						<?php tpl_053_rwd_print_Logo_slogan(); ?>
    			</a>
				</p>
        <!-- / ロゴ -->
     	</article> 
			
      <!-- 電話番号+受付時間 -->
    	<article class="grid">
    		<?php tpl_053_rwd_print_tel(); ?>
    		<?php tpl_053_rwd_print_open_time(); ?>
			<!-- / 電話番号+受付時間 -->
      </article>
		
    	<article class="grid copyright">
      	Copyright(c) <?php echo date('Y'); ?> <?php bloginfo('name'); ?> All Rights Reserved. Design by <a href="http://f-tpl.com" target="_blank" rel="nofollow">http://f-tpl.com</a>
      </article>
	
    </section>
		<!-- / 3カラム -->
      
	</div>
</div>
<!-- / フッター -->

<?php wp_footer(); ?>
</body>
</html>