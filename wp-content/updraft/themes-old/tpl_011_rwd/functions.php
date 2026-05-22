<?php
remove_action('wp_head', 'wp_generator');


/*	Register navigation
/*---------------------------------------------------------*/
register_nav_menus( array(
	'primary' => __('Main Navigation', 'tpl_011_rwd'),
	));
	
register_nav_menus( array(
	'footer' => __('Footer Navigation', 'tpl_011_rwd'),
	));

/*	Register sidebars
/*---------------------------------------------------------*/
register_sidebar(array(
	'name' => __( 'sidebar' ),
  'before_widget' => '<section id="%1$s" class="widget %2$s">',
  'after_widget' => '</article></section>',
  'before_title' => '<h3 class="heading"><span>',
  'after_title' => '</span></h3><article>'
	));

add_filter( 'wp_list_categories', 'tpl_011_rwd_list_categories', 10, 2 );
function tpl_011_rwd_list_categories( $output, $args ) {
  $output = preg_replace('/<\/a>\s*\((\d+)\)/',' ($1)</a>',$output);
  return $output;
}

add_filter( 'get_archives_link', 'tpl_011_rwd_archives_link' );
function tpl_011_rwd_archives_link( $output ) {
  $output = preg_replace('/<\/a>\s*(&nbsp;)\((\d+)\)/',' ($2)</a>',$output);
  return $output;
}


/*	custom walker for the navigation
/*-------------------------------------------*/
class description_walker extends Walker_Nav_Menu
{
      function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0)
      {
           global $wp_query;
           $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

           $class_names = $value = '';

           $classes = empty( $item->classes ) ? array() : (array) $item->classes;

           $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
           $class_names = ' class="'. esc_attr( $class_names ) . '"';

           $output .= $indent . '<li id="menu-item-'. $item->ID . '"' . $value . $class_names .'>';

           $attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
           $attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
           $attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
           $attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';

           $prepend = '<strong>';
           $append = '</strong>';
           $description  = ! empty( $item->description ) ? '<span>'.esc_attr( $item->description ).'</span>' : '';

           if($depth != 0)
           {
                     $description = $append = $prepend = "";
           }

            $item_output = $args->before;
            $item_output .= '<a'. $attributes .'>';
            $item_output .= $args->link_before .$prepend.apply_filters( 'the_title', $item->title, $item->ID ).$append;
            $item_output .= $description.$args->link_after;
            $item_output .= '</a>';
            $item_output .= $args->after;

            $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
            }
}


/*	This is all for compatibility with versions of WordPress prior to 3.4.
/*---------------------------------------------------------*/
define( 'NO_HEADER_TEXT', true );
define( 'HEADER_TEXTCOLOR', true );
define('HEADER_IMAGE', '%s/images/banners/mainImg.jpg');
define('HEADER_IMAGE_WIDTH', 922);
define('HEADER_IMAGE_HEIGHT', 300);
add_theme_support('custom-header');
if (!function_exists('admin_header_style')) :
function admin_header_style() { }
endif;

if (!isset( $content_width ))$content_width = 625;


/*	This theme uses post thumbnails
/*---------------------------------------------------------*/
add_theme_support( 'post-thumbnails' );
add_image_size('size1',260,113);


/*	Custom Excerpt "more" Link
/*---------------------------------------------------------*/
function change_excerpt_more($post) {
  return ' ...';    
}    
add_filter('excerpt_more', 'change_excerpt_more');


/*	Load up the theme options
/*---------------------------------------------------------*/
require( dirname( __FILE__ ) . '/inc/theme-options.php' );


/*	Add admin CSS
/*---------------------------------------------------------*/
function tpl_011_rwd_admin_css(){
	$adminCssPath = get_template_directory_uri().'/cTpl_admin.css';
	wp_enqueue_style( 'theme', $adminCssPath , false, '2012');
}
add_action('admin_head', 'tpl_011_rwd_admin_css', 11);


/*	Display navigation to next/previous pages when applicable
/*---------------------------------------------------------*/
function tpl_011_rwd_content_nav( $nav_id ) {
	global $wp_query;
	if ( $wp_query->max_num_pages > 1 ) : ?>
		<div class="pagenav">
			<div class="prev"><?php previous_posts_link('&laquo; 前のページ'); ?></div>
			<div class="next"><?php next_posts_link('次のページ &raquo;'); ?></div>
		</div>
	<?php endif;
	wp_reset_query();
} 

?>