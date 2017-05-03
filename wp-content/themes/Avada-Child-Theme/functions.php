<?php
define('DEVMODE', true);
define('THEMEROOT', get_stylesheet_directory_uri() );
define('IMGROOT', THEMEROOT.'/images/' );
define('IMG', IMGROOT );
define('IFROOT', THEMEROOT );
define('SLUG_INGATLAN', 'ingatlan' );
define('SLUG_INGATLANOK', 'ingatlan-kereso' );
define('SLUG_INGATLAN_LIST', 'ingatlan-kereso' );
define('SLUG_WATCHED', 'megtekintett');
define('SLUG_NEWS', 'news');
define('PHONE_PREFIX', '+36' );
define('GOOGLE_API_KEY', 'AIzaSyDxeIuQwvCtMzBGo53tV7AdwG6QCDzmSsQ');
define('LANGKEY','hu');
define('FB_APP_ID', '');

// Includes
require_once "includes/include.php";

function theme_enqueue_styles() {
    wp_enqueue_style( 'avada-parent-stylesheet', get_template_directory_uri() . '/style.css?' . ( (DEVMODE === true) ? time() : '' )  );
    wp_enqueue_style( 'avada-child-stylesheet', IFROOT . '/style.css?' . ( (DEVMODE === true) ? time() : '' ) );
    wp_enqueue_style( 'slick', IFROOT . '/assets/vendor/slick/slick.css?t=' . ( (DEVMODE === true) ? time() : '' ) );
    wp_enqueue_style( 'slick-theme', IFROOT . '/assets/css/slick-theme.css?t=' . ( (DEVMODE === true) ? time() : '' ) );
    wp_enqueue_script( 'slick', IFROOT . '/assets/vendor/slick/slick.min.js?t=' . ( (DEVMODE === true) ? time() : '' ) , array('jquery'));
    wp_enqueue_script( 'simpleSlider', IFROOT . '/assets/vendor/simpleSlider/jquery.simpleslide.js?t=' . ( (DEVMODE === true) ? time() : '' ) , array('jquery'));
    wp_enqueue_script( 'google-maps', '//maps.googleapis.com/maps/api/js?sensor=false&language=hu&region=hu&libraries=places&key='.GOOGLE_API_KEY);
    //wp_enqueue_script( 'google-charts', '//www.gstatic.com/charts/loader.js');
    //wp_enqueue_script( 'mocjax', IFROOT . '/assets/vendor/autocomplete/scripts/jquery.mockjax.js');
    wp_enqueue_script( 'autocomplete', IFROOT . '/assets/vendor/autocomplete/dist/jquery.autocomplete.min.js');
}
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );

function custom_theme_enqueue_styles() {
    wp_enqueue_style( 'tenerifeingatlan-css', THEMEROOT . '/tenerifeingatlan.css?t=' . ( (DEVMODE === true) ? time() : '' ) );
}
add_action( 'wp_enqueue_scripts', 'custom_theme_enqueue_styles', 100 );

function avada_lang_setup() {
	$lang = get_stylesheet_directory() . '/languages';
	load_child_theme_textdomain( 'Avada', $lang );

  $ucid = ucid();

  $ucid = $_COOKIE['uid'];
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
  add_rewrite_rule('^'.SLUG_WATCHED.'/?', 'index.php?custom_page='.SLUG_WATCHED.'&urlstring=$matches[1]', 'top');
  add_rewrite_rule('^'.SLUG_NEWS.'/?', 'index.php?custom_page='.SLUG_NEWS.'&urlstring=$matches[1]', 'top');
}
add_action('init', 'app_init');

$is_ingatlan_list = false;
function app_custom_template($template)
{
  global $post, $wp_query, $is_ingatlan_list;

  if (SLUG_INGATLAN_LIST == $wp_query->query['pagename']) {
    $is_ingatlan_list = true;
  }

  if(isset($wp_query->query_vars['custom_page'])) {
    add_filter( 'body_class','ingatlan_class_body' );
    add_filter( 'body_class','template_class_body' );
    add_filter( 'document_title_parts', 'custom_title' );
    return get_stylesheet_directory() . '/'.$wp_query->query_vars['custom_page'].'.php';
  } else {
    return $template;
  }
}
add_filter( 'template_include', 'app_custom_template' );

$is_ingatlan_page = false;
function custom_title($title)
{ global $wp_query, $is_ingatlan_page;

  if($wp_query->query_vars['custom_page'] == 'ingatlan' ) {
    $xs = explode("-",$wp_query->query_vars['urlstring']);
    $ingatlan_id = end($xs);
    $properties = new Properties(array(
      'id' => $ingatlan_id,
      'post_status' => array('publish'),
    ));
    $property = $properties->getList();
    $property = $property[0];

    if ($property) {
      $is_ingatlan_page = true;
      $title['title'] = $property->Title() . ' - ' . $property->PropertyStatus(true) . ' '. $property->PropertyType(true) . ' - '. $property->RegionName( false );
      add_filter('the_title', 'custom_ingatlan_title_bar');
    }
  }

  if($wp_query->query_vars['custom_page'] == SLUG_WATCHED ) {
    $title['title'] = __('Korábban megtekintett ingatlanok', 'gh');
    add_filter('the_title', 'custom_ingatlan_title_bar');
  }

  if($wp_query->query_vars['custom_page'] == SLUG_NEWS ) {
    $title['title'] = __('Új ingatlan hirdetések', 'gh');
    add_filter('the_title', 'custom_ingatlan_title_bar');
  }

  return $title;
}

function custom_ingatlan_title_bar( $title )
{ global $wp_query;

  if ($title == 'Helló Világ!')
  {
    if($wp_query->query_vars['custom_page'] == 'ingatlan' ) {
      $xs = explode("-",$wp_query->query_vars['urlstring']);
      $ingatlan_id = end($xs);
      $properties = new Properties(array(
        'id' => $ingatlan_id,
        'post_status' => array('publish'),
      ));
      $property = $properties->getList();
      $property = $property[0];

      if ($property) {
        $title = $property->Title();
      }
    }

    if($wp_query->query_vars['custom_page'] == SLUG_WATCHED){
      $title = __('Korábban megtekintett ingatlanok', 'gh');
    }

    if($wp_query->query_vars['custom_page'] == SLUG_NEWS){
      $title = __('Új ingatlanok', 'gh');
    }
  }

  return $title;
}


function ingatlan_social_share_titlebar()
{
  ob_start();
    include(locate_template('/templates/parts/ingatlan_social_share_titlebar.php'));
    $output = ob_get_contents();
  ob_end_clean();

  return $output;
}

function facebook_og_meta_header()
{
  global $wp_query;

  if ( !in_array($wp_query->query['custom_page'], array(SLUG_INGATLAN, SLUG_INGATLAN_LIST)) ) {
    return;
  }

  $title = get_option('blogname');
  $image = 'http://globalhungary.mri-dev.com/wp-content/uploads/global-hungary-logo-wtext-h75.png';
  $desc  = get_option('blogdescription');
  $url   = get_option('site_url');

  if($wp_query->query_vars['custom_page'] == 'ingatlan' ) {
    $xs = explode("-",$wp_query->query_vars['urlstring']);
    $ingatlan_id = end($xs);
    $properties = new Properties(array(
      'id' => $ingatlan_id,
      'post_status' => array('publish'),
    ));
    $property = $properties->getList();
    $property = $property[0];

    if ($property) {
      $title = $property->Title() . ' - ' . $property->PropertyStatus(true) . ' '. $property->PropertyType(true) . ' - '. $property->RegionName( false );
      $image = $property->ProfilImg();
      $desc = $property->ShortDesc();
      $url = $property->URL();
    }
  }

  echo '<meta property="fb:app_id" content="'.FB_APP_ID.'"/>'."\n";
  echo '<meta property="og:title" content="' . $title . '"/>'."\n";
  echo '<meta property="og:type" content="article"/>'."\n";
  echo '<meta property="og:url" content="' . $url . '/"/>'."\n";
  echo '<meta property="og:description" content="' . $desc . '/"/>'."\n";
  echo '<meta property="og:site_name" content="'.get_option('blogname').'"/>'."\n";
  echo '<meta property="og:image" content="' . $image . '"/>'."\n";

}
add_action( 'wp_head', 'facebook_og_meta_header', 5);


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

function template_class_body( $classes ) {
  global $wp_query;

  $classes[] = $wp_query->query_vars['custom_page'] . '_template_page';
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

/**
* AJAX REQUESTS
*/
function ajax_requests()
{
  $ajax = new AjaxRequests();
  $ajax->check_property_fav();
  $ajax->property_fav_action();
  $ajax->city_autocomplete();
  $ajax->set_regio_gps();
}
add_action( 'init', 'ajax_requests' );

// AJAX URL
function get_ajax_url( $function )
{
  return admin_url('admin-ajax.php?action='.$function);
}

function add_post_enctype() {
    echo ' enctype="multipart/form-data"';
}
add_action('post_edit_form_tag', 'add_post_enctype');

function ucid()
{
  $ucid = $_COOKIE['ucid'];

  if (!isset($ucid)) {
    $ucid = mt_rand();
    setcookie( 'ucid', $ucid, time() + 60*60*24*365*2, "/");
  }

  return $ucid;
}

function gh_get_fnc()
{
  global $wpdb;
  if (isset($_GET['setwatched']) && $_GET['setwatched'] == '1')
  {
    $ucid = ucid();

    $wpdb->insert(
      \PropertyFactory::LOG_WATCHTIME_DB,
      array(
        'ucid'  => $ucid,
        'ip'    => $_SERVER['REMOTE_ADDR'],
        'wtime' => current_time('mysql')
      ),
      array(
        '%s', '%s', '%s'
      )
    );

    wp_redirect('/news/?settedWatchedAll=1');
    exit;
  }
}
add_action('init', 'gh_get_fnc');
