<?php
define('DEVMODE', true);
define('THEMEROOT', get_stylesheet_directory_uri() );
define('IMGROOT', THEMEROOT.'/images/' );
define('IMG', IMGROOT );
define('SLUG_INGATLAN', 'ingatlan' );
define('SLUG_INGATLANOK', 'ingatlan-kereso' );
define('SLUG_INGATLAN_LIST', 'ingatlan-kereso' );
define('PHONE_PREFIX', '+36' );

// Includes
require_once "includes/include.php";

function theme_enqueue_styles() {
    wp_enqueue_style( 'avada-parent-stylesheet', get_template_directory_uri() . '/style.css?t=' . ( (DEVMODE === true) ? time() : '' ) );
}
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );

function custom_theme_enqueue_styles() {
    wp_enqueue_style( 'tenerifeingatlan-css', THEMEROOT . '/tenerifeingatlan.css?t=' . ( (DEVMODE === true) ? time() : '' ) );
}
add_action( 'wp_enqueue_scripts', 'custom_theme_enqueue_styles', 100 );

function avada_lang_setup() {
	$lang = get_stylesheet_directory() . '/languages';
	load_child_theme_textdomain( 'Avada', $lang );
}
add_action( 'after_setup_theme', 'avada_lang_setup' );

function after_logo_slogan()
{
    echo '<div class="logo-slogan">a kanári szigetek szakértője</div>';
}
add_action( 'avada_logo_append', 'after_logo_slogan' );

function content_before_copyright_text()
{
    $menu = wp_nav_menu(array(
        'menu' => 'Lábrész Lábléc Menü',
        'echo' => false
    ));
   echo $menu;
}
add_action('avada_footer_copyright_content', 'content_before_copyright_text');

function app_init()
{
  date_default_timezone_set('Europe/Budapest');

  add_rewrite_rule('^'.SLUG_INGATLAN.'/([^/]+)/([^/]+)/([^/]+)', 'index.php?custom_page='.SLUG_INGATLAN.'&regionslug=$matches[1]&cityslug=$matches[2]&urlstring=$matches[3]', 'top');
  add_rewrite_rule('^'.SLUG_FAVORITE.'/?', 'index.php?custom_page='.SLUG_FAVORITE.'&urlstring=$matches[1]', 'top');
}
add_action('init', 'app_init');


function app_custom_template($template)
{
  global $post, $wp_query;

  if(isset($wp_query->query_vars['custom_page'])) {
    add_filter( 'body_class','ingatlan_class_body' );
    //add_filter( 'document_title_parts', 'ingatlan_custom_title' );
    return get_stylesheet_directory() . '/'.$wp_query->query_vars['custom_page'].'.php';
  } else {
    return $template;
  }
}
add_filter( 'template_include', 'app_custom_template' );

/**
* Szerepkörök
**/
$user_roles = false;;
function gh_custom_role()
{
  global $user_roles;
  /**
  * Új szerepkörök
  **/
  $user_roles = new UserRoles();
  $user_roles->addRoles(array(
    array( 'reference_manager', __('Ingatlan referens','gh') ),
    array( 'starter', __('Előregisztráló','gh') )
  ));
  // Alap felhasználói körök eltávolítása
  $user_roles->removeRoles(array('subscriber', 'contributor', 'author', 'editor'));

  // Jogkörök
  // Referens
  $user_roles->addAvaiableCaps( 'reference_manager', array(
    'property_create', 'property_archive', 'property_edit', 'property_edit_price', 'property_edit_status',
    'property_edit_autoconfirm_price', 'property_edit_autoconfirm_datas', 'property_archive_autoconfirm',
    'stat_property'
  ) );
  $user_roles->addCap('reference_manager', 'read');

  // Admin
  $user_roles->addAvaiableCaps( 'administrator', array(
    'property_create', 'property_delete', 'property_archive', 'property_edit', 'property_edit_price', 'property_edit_status',
    'user_property_connector',
    'property_edit_autoconfirm_price', 'property_edit_autoconfirm_datas', 'property_archive_autoconfirm',
    'stat_property'
  ) );

  /* * /
  print_r(wp_get_current_user());
  /* */
}
add_action('after_setup_theme', 'gh_custom_role');

function ingatlan_class_body( $classes ) {
  $classes[] = 'ingatlan_page';
  return $classes;
}

function app_query_vars($aVars)
{
  $aVars[] = "custom_page";
  $aVars[] = "urlstring";
  $aVars[] = "cityslug";
  $aVars[] = "regionslug";
  return $aVars;
}
add_filter('query_vars', 'app_query_vars');
