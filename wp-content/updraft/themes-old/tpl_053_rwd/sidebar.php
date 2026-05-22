<aside id="sub" class="gridWrapper">
<?php if (is_sidebar_active('sub-footer1')) : ?><section class="grid"><?php endif;?>
<?php dynamic_sidebar('sub-footer1'); ?>
<?php if (is_sidebar_active('sub-footer1')) : ?></section><?php endif;?>
<?php if (is_sidebar_active('sub-footer2')) : ?><section class="grid"><?php endif;?>
<?php dynamic_sidebar('sub-footer2'); ?>
<?php if (is_sidebar_active('sub-footer2')) : ?></section><?php endif;?>
<?php if (is_sidebar_active('sub-footer3')) : ?><section class="grid"><?php endif;?>
<?php dynamic_sidebar('sub-footer3'); ?>
<?php if (is_sidebar_active('sub-footer3')) : ?></section><?php endif;?>
</aside>