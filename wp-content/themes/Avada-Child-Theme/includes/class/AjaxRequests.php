<?php

class AjaxRequests
{
  public function __construct()
  {
    return $this;
  }

  public function check_property_fav()
  {
    add_action( 'wp_ajax_'.__FUNCTION__, array( $this, 'checkPropertyFavorites'));
    add_action( 'wp_ajax_nopriv_'.__FUNCTION__, array( $this, 'checkPropertyFavorites'));
  }

  public function property_fav_action()
  {
    add_action( 'wp_ajax_'.__FUNCTION__, array( $this, 'propertyFavAction'));
    add_action( 'wp_ajax_nopriv_'.__FUNCTION__, array( $this, 'propertyFavAction'));
  }

  public function city_autocomplete()
  {
    add_action( 'wp_ajax_'.__FUNCTION__, array( $this, 'AutocompleteCity'));
    add_action( 'wp_ajax_nopriv_'.__FUNCTION__, array( $this, 'AutocompleteCity'));
  }

  public function set_regio_gps()
  {
    add_action( 'wp_ajax_'.__FUNCTION__, array( $this, 'setRegioGPS'));
    add_action( 'wp_ajax_nopriv_'.__FUNCTION__, array( $this, 'setRegioGPS'));
  }

  public function maplist()
  {
    add_action( 'wp_ajax_'.__FUNCTION__, array( $this, 'loadMapItems'));
    add_action( 'wp_ajax_nopriv_'.__FUNCTION__, array( $this, 'loadMapItems'));
  }

  public function loadMapItems()
  {
    extract($_POST);
    $data = array();
    $arg = array();

    $get = json_decode(stripslashes($get), true);

    // Default return
    $return = array();
    $return['filters'] = $_POST;
    $return['gets'] = $get;
    $return['limit'] = (isset($limit)) ? (int)$limit : 30;
    $return['fromto'] = array(
      'from' => 0,
      'to' => (isset($limit)) ? (int)$limit : 30
    );
    $return['page'] = array(
      'current' => (isset($page)) ? (int)$page : 1,
      'max' => 1,
      'next' => 1,
      'prev' => 1
    );
    $return['data'] = array();
    $return['data_info'] = array();

    // Query filters
    $arg[limit] = $return['limit'];
    $arg[page] = $return['page']['current'];

    // GETS
    if (isset($get['hl']) && $get['hl'] == '1') {
      $arg['highlight'] = 1;
    }

    if (isset($get['n']) && !empty($get['n'])) {
      $arg['idnumber'] = $get['n'];
    }

    if (isset($get['rg']) && !empty($get['rg'])) {
      $arg['regio'] = $get['rg'];
    }
    if (isset($get['pa']) && !empty($get['pa'])) {
      $arg['price_from'] = (float)str_replace(array("."," "), "", $get['pa']);
      $arg['price'] = 1;
    }
    if (isset($get['pb']) && !empty($get['pb'])) {
      $arg['price_to'] = (float)str_replace(array("."," "), "", $get['pb']);
      $arg['price'] = 1;
    }

    if (isset($get['r']) && !empty($get['r'])) {
      $arg['rooms'] = $get['r'];
    }

    if (isset($get['ps']) && !empty($get['ps'])) {
      $arg['alapterulet'] = $get['ps'];
    }

    if (isset($get['ci']) && !empty($get['ci'])) {
      $arg['location'] = explode(",", $get['ci']);
    }
    if (isset($get['cities']) && !empty($get['cities'])) {
      $arg['cities'] = explode(",", $get['cities']);
    }

    if (isset($get['st']) && !empty($get['st'])) {
      $arg['status'] = explode(",", $get['st']);
    }

    if (isset($get['co']) && !empty($get['co'])) {
      $arg['condition'] = explode(",", $get['co']);
    }

    if (isset($get['c']) && !empty($get['c'])) {
      $arg['property-types'] = explode(",", $get['c']);
    }

    // Query
    $properties = new Properties($arg);
    $list = $properties->getList();

    // Return params
    $return[query] = $properties->getQuery();
    $max_page = (int)$return[query]->max_num_pages;
    $return['page']['max'] = $max_page;

    $return['data_info']['total_items'] = (int)$properties->CountTotal();

    $prev_page = $return['page']['current'] - 1;
    $next_page = $return['page']['current'] + 1;

    if($prev_page < 0) {
      $prev_page = 0;
    }

    if($next_page > $max_page) {
      $next_page = $max_page;
    }

    $return['page']['next'] = $next_page;
    $return['page']['prev'] = $prev_page;
    $return['fromto']['from'] = ($return['page']['current'] * $return['limit']) - $return['limit'];

    foreach ((array)$list as $item){
      $data[] = array(
        'id' => $item->ID(),
        'title' => $item->Title(),
        'region' => $item->RegionName(false, 0),
        'url' => $item->URL(),
        'image' => $item->ProfilImg(),
        'desc' => $item->ShortDesc(),
        'price' => $item->Price(),
        'price_text' => $item->getValuta().$item->Price(true),
        'label' => $item->PropertyLabel(),
        'gps' => $item->GPS(),
        'params' => $item->Paramteres()
      );
    }

    $return['data'] = $data;

    echo json_encode($return);
    die();
  }

  public function setRegioGPS()
  {
    extract($_POST);

    $return = array(
      'error' => 0,
      'msg'   => ''
    );

    $lat = (float)$lat;
    $lng = (float)$lng;

    $lat_meta_id = add_term_meta( $term, 'gps_lat', $lat, true);
    $lng_meta_id = add_term_meta( $term, 'gps_lng', $lng, true);

    $return['data']['lng'] = $lng_meta_id;
    $return['data']['lat'] = $lat_meta_id;

    echo json_encode($return);
    die();
  }


  public function checkPropertyFavorites()
  {
    global $wpdb;

    extract($_POST);
    $return = array(
      'error' => 0,
      'msg'   => '',
      'in_fav' => array()
    );
    $total = 0;

    $ucid = ucid();

    $ids = $wpdb->get_results("SELECT
      f.pid
    FROM listing_favorites as f
    LEFT JOIN $wpdb->posts as p ON p.ID = f.pid
    WHERE f.ucid = '$ucid' and p.post_status = 'publish'
    GROUP BY pid;", ARRAY_A);

    foreach ($ids as $id) {
      $total++;
      $return['in_fav'][] = (int)$id[pid];
    }

    $return['check_ids'] = $_POST['ids'];
    $return['num'] = $total;


    //ob_start();
  	  //include(locate_template('templates/mails/utazasi-ajanlatkero-ertesites.php'));
      //$message = ob_get_contents();
		//ob_end_clean();


    echo json_encode($return);
    die();
  }

  public function propertyFavAction()
  {
    global $wpdb;

    extract($_POST);

    $return = array(
      'error' => 0,
      'msg'   => '',
      'id'    => (int)$id
    );

    $ucid = ucid();

    //check
    $c = (int)$wpdb->get_var( $wpdb->prepare("SELECT count(ID) FROM listing_favorites WHERE ucid = %s and pid = %d", $ucid, $id) );

    if ( $c == 0 ) {
      $wpdb->insert(
        "listing_favorites",
        array(
          'ucid' => $ucid,
          'pid' => $id
        ),
        array( '%s', '%d' )
      );
      $return['did'] = 'add';
    } else {
      $wpdb->delete( "listing_favorites", array("ucid" => $ucid, "pid" => $id), array('%s', '%d'));
      $return['did'] = 'remove';
    }



    echo json_encode($return);
    die();
  }

  public function AutocompleteCity()
  {
    global $wpdb;

    extract($_GET);

    $pf = new PropertyFactory();

    $return = array();
    $arg    = array(
      'taxonomy' => 'locations',
      'hierarchical' => 1,
      'hide_empty' => 1,
      'orderby' => 'name',
      'order' => 'ASC'
    );

    if ($region) {
      $arg['child_of'] = $region;
    }

    //$arg['name__like'] = $search;

    $terms = get_terms($arg);

    foreach ($terms as $t) {
      if ($t->parent == 0) {
        continue;
      }
      if ($t->parent != 0) {
        $parent = get_term($t->parent);
      }

      $name = ( ($parent->slug == 'budapest') ? $parent->name.' / '.$t->name.' '.__('kerület') : $t->name  );

      if (!empty($search) && stristr($name, $search) === FALSE) {
        continue;
      }

      $return[] = array(
        'label' => $name,
        'value' => (int)$t->term_id,
        'slug' => $t->slug,
        'region' => $t->parent,
        'count' => $t->count
      );
    }

    header('Content-Type: application/json;charset=utf8');
    echo json_encode($return);
    die();
  }

  public function getMailFormat(){
      return "text/html";
  }

  public function getMailSender($default)
  {
    return get_option('admin_email');
  }

  public function getMailSenderName($default)
  {
    return get_option('blogname', 'Wordpress');
  }

  private function returnJSON($array)
  {
    echo json_encode($array);
    die();
  }

}
?>
