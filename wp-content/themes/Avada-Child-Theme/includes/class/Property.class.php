<?php
class Property extends PropertyFactory
{
  private $raw_post = false;

  public function __construct( WP_Post $property_post = null )
  {
    $this->raw_post = $property_post;
    return $this;
  }

  public function load( $id )
  {
    $this->raw_post = get_post($id);
    return $this;
  }

  public function ID()
  {
    return $this->raw_post->ID;
  }
  public function Title()
  {
    return $this->raw_post->post_title;
  }
  public function CreateAt()
  {
    return date(get_option('date_format').' '.get_option('time_format'), strtotime($this->raw_post->post_date));
  }
  public function AuthorID()
  {
    return $this->raw_post->post_author;
  }
  public function AuthorImage( $size = 96 )
  {
    return get_avatar( $this->AuthorID(), $size );
  }
  public function AuthorName()
  {
    return get_author_name( $this->raw_post->post_author );
  }
  public function AuthorPhone()
  {
    $phone = get_the_author_meta('phone', $this->raw_post->post_author);

    return $phone;

    $_phone = substr($phone, 0, 2);
    $last = substr($phone, 2);

    if (strlen($last) > 6) {
      $_phone .= ' '.substr($last, 0, 3).' '. substr($last, 3 );
    } else {
      $_phone .= ' '. substr($last, 0, 3).' '. substr($last, 3 );
    }
    return $_phone;
  }
  public function AuthorEmail()
  {
    $meta = get_the_author_meta('email', $this->raw_post->post_author);
    return $meta;
  }
  public function StatusKey()
  {
    if ($this->isArchived()) {
      return 'archived';
    }
    return $this->raw_post->post_status;
  }
  public function isSold()
  {
    $sold = false;

    $terms = wp_get_post_terms( $this->ID(), 'status' );

    foreach ((array)$terms as $term) {
      if($term->slug == 'eladva'){
        return true;
      }
    }

    return $sold;
  }
  public function URL()
  {
    $regionslug = $this->ParentRegionSlug();
    $megye = $this->RegionSlug();

    if(in_array($this->ParentRegion(), $this->fake_city)) {
      $megye = 'magyarorszag';
    }

    if (empty($regionslug)) {
      $regionslug = '-';
    }

    return get_option('siteurl').'/'.SLUG_INGATLAN.'/'.$megye.'/'.$regionslug.'/'.sanitize_title($this->Title()).'-'.$this->ID();
  }
  public function RegionName( $html_text = true, $start_deep = 0, $return_array = false )
  {
    $region_arr = array();
    $regions = $this->Regions();
    $has_child = true;
    $current = $regions;
    $deep = 0;

    while( $has_child ) {
      if($deep >= $start_deep){
        if($return_array){
          $region_arr[] = $current;
        }else{
          $region_arr[] = $current->name;
        }
      }

      $deep++;

      if( $current->children ) {
        $current = $current->children;
      } else {
        $current = false;
        $has_child = false;
      }
    }

    if ($return_array) {
      return $region_arr;
    }

    $region = implode(' / ', $region_arr);

    //$region = rtrim(' / ',  $region);

    return $region;
  }

  public function Regions()
  {
    $regions  = array();
    $terms    = wp_get_post_terms( $this->ID(), 'locations' );
    $top_term = $this->get_top_term($terms);

    if (!$terms) {
      return false;
    }

    $parent = ($top_term) ? $top_term->term_id : false;

    $top_term->children = $this->load_child_term($parent, $terms);

    return $top_term;

    //return array_reverse($regions);
  }

  private function get_top_term( $terms )
  {
    foreach ((array)$terms as $t ) {
      if($t->parent == 0) return $t;
      continue;
    }

    return false;
  }

  private function load_child_term($parent, $terms)
  {
    foreach ((array)$terms as $t ) {
      if($parent && $t->parent == $parent){
        $t->children = $this->load_child_term($t->term_id, $terms);
        return $t;
      }
      continue;
    }

    return false;
  }

  public function RegionSlug()
  {
    $terms = wp_get_post_terms( $this->ID(), 'locations' );

    foreach ($terms as $term) {
      if($term->taxonomy == 'locations') {
        $parent = false;
        if ($term->parent != 0) {
          $parent = get_term($term->parent);
        }
        return ($parent) ? $parent->slug : $term->slug;
      }
    }

    return false;
  }

  public function ParentRegionSlug()
  {
    $terms = wp_get_post_terms( $this->ID(), 'locations' );

    foreach ($terms as $term) {
      if($term->taxonomy == 'locations') {
        if ($term->parent != 0) {
          return $term->slug;
        }
      }
    }

    return false;
  }

  public function ParentRegion()
  {
    $terms = wp_get_post_terms( $this->ID(), 'locations' );

    foreach ($terms as $term) {
      if($term->taxonomy == 'locations') {
        if ($term->parent != 0) {
          return $term->name;
        }
      }
    }

    return false;
  }

  public function PropertyLabel( $text = false )
  {
    $re = array();
    $text = get_post_meta($this->ID(), '_listing_listlabel_text', true);
    $bgcolor = get_post_meta($this->ID(), '_listing_listlabel_bgcolor', true);

    if( $text == '' ) {
      return false;
    }

    $re = array(
      'text'  => $text,
      'bg'    => ($bgcolor == '') ? '#435061' : $bgcolor,
    );

    return $re;
  }

  public function PropertyStatus( $text = false )
  {
    $terms = wp_get_post_terms( $this->ID(), 'status' );

    foreach ($terms as $term) {
      if($term->taxonomy == 'status') {
        if ($text) {
          return $this->i18n_taxonomy_values($term->name);
        } else {
          return $term->name;
        }
      }
    }

    return false;
  }

  public function PropertyHeating( $text = false )
  {
    $terms = wp_get_post_terms( $this->ID(), 'property-heating' );

    foreach ($terms as $term) {
      if($term->taxonomy == 'property-heating') {
        if ($text) {
          return $this->i18n_taxonomy_values($term->name);
        } else {
          return $term->name;
        }
      }
    }

    return false;
  }

  public function PropertyCondition( $text = false )
  {
    $terms = wp_get_post_terms( $this->ID(), 'property-condition' );

    if ($text) {
      $term  = $terms[0];
      return $term->name;
    } else {
      foreach ($terms as $t) {
        $term[] = $t;
      }
    }

    return $term;
  }

  public function multivalue_list( $term_list = array(), $linked = false, $base = '' )
  {
    $text = '';

    if(!empty($term_list))
    foreach ($term_list as $term) {
      if (!$linked) {
        $text .= $this->i18n_taxonomy_values($term->name).', ';
      }else{
        $link = str_replace('#value#', $term->term_id, $base);
        $text .= '<a target="_blank" href="'.$link.'">'.$this->i18n_taxonomy_values($term->name).'</a>, ';
      }
    }

    $text = rtrim($text, ', ');

    return $text;
  }

  public function PropertyType( $text = false )
  {
    $terms = wp_get_post_terms( $this->ID(), 'property-types' );

    if($text){
        return $terms[0]->name;
    }

    return $terms;
  }

  public function historyChangeCount( $user = false )
  {
    global $wpdb;

    $prep = array();
    $prep[] = $this->ID();

    $q = "SELECT count(ID) FROM listing_change_history WHERE item_id = %d ";

    if ($user && $user->ID()) {
      $q .= " and changer_user_id = %d ";
      $prep[] = $user->ID();
    }

    return $wpdb->get_var($wpdb->prepare($q, $prep));
  }

  public function isNews()
  {
    $h = true;

    // Diff
    $diff = 86400 * self::NEWSDAY;

    $time = ((int)strtotime($this->raw_post->post_date)) + $diff;

    if ( time() > $time ) {
      $h = false;
    }

    return $h;
  }

  public function isArchived()
  {
    if ($this->getMetaValue('_listing_flag_archived') == 1) {
      return true;
    }

    return false;
  }

  public function ArchivingInProgress()
  {
    global $wpdb;

    $arc_reg_id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM listing_archive_reg WHERE postID = %d and accept_userid IS NULL;", $this->ID() ) );

    if ($arc_reg_id) {
      return $arc_reg_id;
    }

    return false;
  }

  public function ArchivingData()
  {
    global $wpdb;

    $arcid = $this->ArchivingInProgress();

    if ($arcid) {
      $data = $wpdb->get_row( "SELECT * FROM listing_archive_reg WHERE ID = ".$arcid );
      return $data;
    }

    return false;
  }

  public function isDropOff()
  {
    $h = false;
    $offp = $this->getMetaValue('_listing_offprice');

    if ($offp != 0 && $offp) {
      $h = true;
    }

    return $h;
  }

  public function isHighlighted()
  {
    $h = true;

    $v = $this->getMetaValue('_listing_flag_highlight');

    if (!$v || $v == '' || $v == '0') {
      return false;
    }

    return $h;
  }

  public function isExclusive()
  {
    $h = true;

    $v = $this->getMetaValue('_listing_flag_exclusive');

    if (!$v || $v == '' || $v == '0') {
      return false;
    }

    return $h;
  }

  public function getSlideIMGID()
  {
    return (int)get_post_meta($this->ID(), '_listing_slide_img_id', true);
  }

  public function Images()
  {
    $images = array();
    $tempimages = get_attached_media( 'image', $this->ID() );
    $slide_img_id = $this->getSlideIMGID();

    foreach ((array)$tempimages as $aid => $img) {
      if($slide_img_id != 0 && $slide_img_id == $aid) continue;

      $imgmeta = wp_get_attachment_metadata($aid);
      if (is_array($imgmeta))
      {
        $width = $imgmeta['width'];
        $height = $imgmeta['height'];

        if ($width === $height) {
          $imgmeta['orientation'] = 'square';
        } else if($width < $height ){
          $imgmeta['orientation'] = 'portrait';
        } else {
          $imgmeta['orientation'] = 'landscape';
        }
      } else {
        $imgmeta = array();

        $prof_img = $img->guid;

        $size = getimagesize($prof_img);

        if (!$size) {
          return false;
        }

        $width = $size[0];
        $height = $size[1];

        if ($width === $height) {
          $imgmeta['orientation'] = 'square';
        } else if($width < $height ){
          $imgmeta['orientation'] = 'portrait';
        } else {
          $imgmeta['orientation'] = 'landscape';
        }
      }
      $img->metadata = $imgmeta;

      $images[$aid] = $img;
    }
    unset($temimages);

    return $images;
  }

  public function PDFDocuments()
  {
    return get_attached_media( 'application/pdf', $this->ID() );
  }


  public function imageNumbers()
  {
    $n = count($this->Images());
    return $n;
  }

  public function StatusID()
  {
    $terms = wp_get_post_terms( $this->ID(), 'status' );
    $ids = array();

    if (!$terms) {
      return 0;
    }

    foreach ($terms as $t) {
      $ids[] = $t->term_id;
    }

    return $ids;
  }

  public function CatID()
  {
    $terms = wp_get_post_terms( $this->ID(), 'property-types' );
    $ids = array();

    if (!$terms) {
      return 0;
    }

    foreach ($terms as $t) {
      $ids[] = $t->term_id;
    }

    return $ids;
  }

  public function GPS()
  {
    $lat = $this->getMetaValue( '_listing_gps_lat' );
    $lng = $this->getMetaValue( '_listing_gps_lng' );

    if (!$lng || !$lat)
    {
      // Mentett GPS vizsgálat GEO alapján
      $parent_zone_term = end($this->Regions());
      $zone_gps = $this->getZoneGPS($parent_zone_term->term_id);

      if ($zone_gps) {
        $lng = (float) $zone_gps['lng'];
        $lat = (float) $zone_gps['lat'];
      }
    }

    if (!$lng || !$lat) {
      return false;
    }

    return array(
      "lat" => (float)$lat,
      "lng" => (float)$lng
    );
  }

  public function HeatingID()
  {
    $terms = wp_get_post_terms( $this->ID(), 'property-heating' );

    if (!$terms) {
      return 0;
    }

    return $terms[0]->term_id;
  }

  public function ConditionID()
  {
    $terms = wp_get_post_terms( $this->ID(), 'property-condition' );
    $ids = array();

    if (!$terms) {
      return 0;
    }

    foreach ($terms as $t) {
      $ids[] = $t->term_id;
    }

    return $ids;
  }

  public function Videos()
  {
    $video = $this->getMetaValue( '_listing_video' );

    return $video;
  }

  public function Layouts()
  {
    $v = $this->getMetaValue( '_listing_property_layouts' );

    return $v;
  }

  public function ShortDesc()
  {
    return $this->raw_post->post_excerpt;
  }

  public function getMetaValue( $key )
  {
    $value = get_post_meta($this->ID(), $key, true);

    return $value;
  }

  public function getMetaCheckbox( $key )
  {
    $v = $this->getMetaValue( $key );

    if ( !empty($v) && $v == '1') {
      return 1;
    } else {
      return 0;
    }
  }

  public function Description( $front = false )
  {
    $content = apply_filters ("the_content", $this->raw_post->post_content);
    if ($front) {
        $content = YoutubeHelper::ember($content);
    }
    return $content;
  }

  public function RawDescription()
  {
    return sanitize_text_field($this->raw_post->post_content);
  }

  public function Address()
  {
    $addr = get_post_meta($this->ID(), '_listing_address', true);

    if (!$addr) {
      return '--';
    }
    return $addr;
  }
  public function Azonosito()
  {
    return get_post_meta($this->ID(), '_listing_idnumber', true);
  }
  public function Price( $formated = false )
  {
    $price = get_post_meta($this->ID(), '_listing_price', true);

    if ($this->isDropOff()) {
      $off_price = $this->getMetaValue('_listing_offprice');
      if ($off_price && $off_price != 0) {
        $price = $off_price;
      }
    }

    if ( !$price ) {
      return '??';
    }

    if ($formated) {
      $price = number_format($price, 0, '.', ' ');
    }

    return $price;
  }
  public function PriceType()
  {
    $price_index = (int)$this->getMetaValue('_listing_flag_pricetype');
    if ($price_index === 0) {
      return false;
    }
    return str_replace($this->getValuta().' ', '', $this->getPriceTypeText($price_index));
  }
  public function PriceTypeID()
  {
    return (int)$this->getMetaValue('_listing_flag_pricetype');
  }
  public function OriginalPrice( $formated = false )
  {
    $price = $this->getMetaValue('_listing_price');

    if ( !$price ) {
      return 0;
    }

    if ($formated) {
      $price = $this->getValuta().number_format($price, 0, '.', ' ');
    }

    return $price;
  }
  public function OffPrice( $formated = false )
  {
    $price = $this->getMetaValue('_listing_offprice');

    if ( !$price ) {
      return 0;
    }

    if ($formated) {
      $price = $this->getValuta().number_format($price, 0, '.', ' ');
    }

    return $price;
  }
  public function ProfilImgID()
  {
    $img = get_post_thumbnail_id( $this->ID() );


    return $img;
  }

  public function Paramteres()
  {
    $params = array();
    $params[] = array(
      'name' => __('Státusz', 'ti'),
      'value' => $this->PropertyStatus(true),
      'after' => false
    );

    $params[] = array(
      'name' => __('Ingatlan típusa', 'ti'),
      'value' => $this->PropertyType(true),
      'after' => false
    );

    $params[] = array(
      'name' => __('Telek mérete', 'ti'),
      'value' => $this->getMetaValue('_listing_lot_size'),
      'after' => 'm<sup>2</sup>'
    );

    $regions = $this->Regions();
    $city = end($regions);

    $params[] = array(
      'name' => __('Település', 'ti'),
      'value' => $city->name,
      'after' => false
    );

    $params[] = array(
      'name' => __('Méret', 'ti'),
      'value' => $this->getMetaValue('_listing_property_size'),
      'after' => 'm<sup>2</sup>'
    );

    $params[] = array(
      'name' => __('Szobák', 'ti'),
      'value' => $this->getMetaValue('_listing_room_numbers'),
      'after' => 'db'
    );

    $params[] = array(
      'name' => __('Fürdők', 'ti'),
      'value' => $this->getMetaValue('_listing_bathroom_numbers'),
      'after' => 'db'
    );

    $params[] = array(
      'name' => __('Teraszok', 'ti'),
      'value' => $this->getMetaValue('_listing_terrace'),
      'after' => 'db'
    );

    $params[] = array(
      'name' => __('Ingatlan állapota', 'ti'),
      'value' => $this->getMetaValue('_listing_star_property'),
      'after' => false
    );


    return $params;
  }

  public function ProfilImgAttr()
  {
    $imgmeta = wp_get_attachment_metadata($this->ProfilImgID());
    if (is_array($imgmeta))
    {
      $width = $imgmeta['width'];
      $height = $imgmeta['height'];

      if ($width === $height) {
        $imgmeta['orientation'] = 'square';
      } else if($width < $height ){
        $imgmeta['orientation'] = 'portrait';
      } else {
        $imgmeta['orientation'] = 'landscape';
      }
    } else {
      $imgmeta = array();

      $prof_img = $this->ProfilImg();

      $size = getimagesize($prof_img);

      if (!$size) {
        return false;
      }

      $width = $size[0];
      $height = $size[1];

      if ($width === $height) {
        $imgmeta['orientation'] = 'square';
      } else if($width < $height ){
        $imgmeta['orientation'] = 'portrait';
      } else {
        $imgmeta['orientation'] = 'landscape';
      }
    }
    return $imgmeta;
  }

  public function Viewed()
  {
    global $wpdb;
    $click = 0;

    $click = $wpdb->get_var("SELECT count(ID) FROM ".self::LOG_VIEW_DB." WHERE pid = ".$this->ID()." GROUP BY pid");

    if (!$click) {
      return 0;
    }


    return $click;
  }

  public function SliderImage()
  {
    $slide_id = $this->getSlideIMGID();

    if($slide_id == 0) {
      $slide = $this->ProfilImg();
    } else {
      $slide = wp_get_attachment_url($slide_id);
    }

    return $slide;
  }

  public function ProfilImg()
  {
    global $wpdb;
    $img_id = (int)get_post_thumbnail_id( $this->ID() );


    if (!$img_id) {
      return IMG.'/no-property-image.png';
    } else {
      $img = wp_get_attachment_url($img_id);
      return $img;
    }
  }

  public function Status( $only_text = true )
  {
    $status = null;

    if (!$only_text) {
      $status .= '<div class="dashboard-label status-label status-'.$this->StatusKey().'" style="background: '.$this->property_status_colors[$this->StatusKey()].';">';
    }

    $status .= $this->StatusText($this->StatusKey());

    if (!$only_text) {
      $status .= '</div>';
    }

    return $status;
  }
}
?>
