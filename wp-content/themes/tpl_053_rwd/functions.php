<?php
remove_action('wp_head', 'wp_generator');


/*	Register navigation
/*---------------------------------------------------------*/
register_nav_menus( array(
	'primary' => __('Main Navigation', 'tpl_053_rwd'),
	));

/*	Register sidebars
/*---------------------------------------------------------*/
register_sidebar(array(
	'name' => __('sub-footer1'),
  'before_widget' => '',
  'after_widget' => '',
  'before_title' => '<h3>',
  'after_title' => '</h3>'
	));
register_sidebar(array(
	'name' => __('sub-footer2'),
  'before_widget' => '',
  'after_widget' => '',
  'before_title' => '<h3>',
  'after_title' => '</h3>'
	));
register_sidebar(array(
	'name' => __('sub-footer3'),
  'before_widget' => '',
  'after_widget' => '',
  'before_title' => '<h3>',
  'after_title' => '</h3>'
	));
	
function is_sidebar_active($index = 1) {
	global $wp_registered_sidebars;
	
	if (is_int( $index)) :
		$index = "sidebar-$index";
	else:
    $index = sanitize_title( $index );
		foreach ( (array) $wp_registered_sidebars as $key => $value ) :
			if ( sanitize_title( $value['name'] ) == $index ) :
				$index = $key;
				break;
			endif;
		endforeach;
	endif;
		
	$sidebars_widgets = wp_get_sidebars_widgets();

	if (empty( $wp_registered_sidebars[$index]) || !array_key_exists($index, $sidebars_widgets) || !is_array($sidebars_widgets[$index]) || empty( $sidebars_widgets[$index]))
		return false;
	else
		return true;
}

add_filter( 'wp_list_categories', 'tpl_053_rwd_list_categories', 10, 2 );
function tpl_053_rwd_list_categories( $output, $args ) {
  $output = preg_replace('/<\/a>\s*\((\d+)\)/',' ($1)</a>',$output);
  return $output;
}

add_filter( 'get_archives_link', 'tpl_053_rwd_archives_link' );
function tpl_053_rwd_archives_link( $output ) {
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
           $description  = ! empty( $item->description ) ? '<br><span>'.esc_attr( $item->description ).'</span>' : '';

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
define('HEADER_IMAGE_WIDTH', 940);
define('HEADER_IMAGE_HEIGHT', 300);
add_theme_support('custom-header');
if (!function_exists('admin_header_style')) :
function admin_header_style() { }
endif;

if (!isset( $content_width ))$content_width = 625;


/*	This theme uses post thumbnails
/*---------------------------------------------------------*/
add_theme_support( 'post-thumbnails' );
add_image_size('size1',220,175);


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
function tpl_053_rwd_admin_css(){
	$adminCssPath = get_template_directory_uri().'/cTpl_admin.css';
	wp_enqueue_style( 'theme', $adminCssPath , false, '2012');
}
add_action('admin_head', 'tpl_053_rwd_admin_css', 11);


/*	Display navigation to next/previous pages when applicable
/*---------------------------------------------------------*/
function tpl_053_rwd_content_nav( $nav_id ) {
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