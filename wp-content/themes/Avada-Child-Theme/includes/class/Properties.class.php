<?php
class Properties extends PropertyFactory
{
  public $arg = array();
  private $datalist = array();
  private $exclue_megye_str = array();
  private $count = 0;
  private $query = null;

  public function __construct( $arg = array() )
  {
    $this->arg = array_replace( $this->arg, $arg );

    return $this;
  }

  public function getRegions()
  {
    $terms = get_terms(array(
      'taxonomy' => 'locations'
    ));

    $t = array();
    $temp = array();

    foreach ($terms as $term) {
      $temp[$term->term_id] = $term;
    }

    foreach ($temp as $tid => $term) {
      if($term->parent == 0) {
        $term->children = $this->getChildRegion($term->term_id, $temp);
        $t[$tid] = $term;
      }
    }

    return $t;
  }

  private function getChildRegion($parent, $obj)
  {
    $child = array();

    foreach ($obj as $o) {
      if($o->parent == 0) continue;
      if($o->parent == $parent) {
        $o->children = $this->getChildRegion($o->term_id, $obj);
        $child[$o->term_id] = $o;
      }
    }

    return $child;
  }

  public function getSelectors( $id, $sel_values = array() )
  {
    $terms = get_terms(array(
      'taxonomy' => $id
    ));

    $t = array();

    foreach ($terms as $term) {
      $term->selected = (in_array($term->term_id, $sel_values)) ? true : false;
      $term->name = $this->i18n_taxonomy_values($term->name);
      $t[] = $term;
    }

    return $t;
  }

  public function listChangeHistory( $arg = array() )
  {
    global $wpdb;

    $data = array(
      'page' => array(
        'current' => 1,
        'max'     => 1
      ),
      'count' => 0,
      'data' => array()
    );

    $params = array();

    // query
    $q = "SELECT
      h.ID,
      h.changer_user_id,
      h.item_id,
      h.mod_data_json,
      h.transaction_date
    FROM listing_change_history as h
    WHERE 1=1 ";

    $q .= " and group_key ='property'";

    if (isset($arg['property_id'])) {
      $q .= " and h.item_id IN(%d)";
      $params[] = (int)$arg['property_id'];
    }

    if (isset($arg['user_id'])) {
      $q .= " and h.changer_user_id IN(%d)";
      $params[] = (int)$arg['user_id'];
    }

    $q .= " ORDER BY h.transaction_date DESC";

    $qry = $wpdb->get_results($wpdb->prepare( $q, $params ));
    $count = $wpdb->get_var( "SELECT FOUND_ROWS();" );

    if($qry)
    foreach ($qry as $qr)
    {
      $qr->modify = json_decode($qr->mod_data_json, true);
      unset($qr->mod_data_json);

      $data['data'][] = new PropertyHistory( $qr->item_id, $qr );
    }

    $data['count'] = $count;

    return $data;
  }

  public function getList()
  {
    $data     = array();
    $post_arg = array(
      'post_type' => 'listing',
      'no_found_rows' => false
    );
    $meta_qry = array();

    if (isset($this->arg['highlight']))
    {
      $meta_qry[] = array(
          array(
            'key' => '_listing_flag_highlight',
            'value' => '1'
          )
      );
      $post_arg['tax_query'][] = array(
        'taxonomy'  => 'positions',
        'field'     => 'name',
        'terms'     => 'Főoldal kiemelt',
        'compare'   => '='
      );

    }

    if (isset($this->arg['orderby'])) {
      $post_arg['orderby'] = $this->arg['orderby'];
    }
    if (isset($this->arg['order'])) {
      $post_arg['order'] = $this->arg['order'];
    }

    if (isset($this->arg['id'])) {
      $post_arg['post__in'] = array((int)$this->arg['id']);
    }
    if (isset($this->arg['ids']) && is_array($this->arg['ids'])) {
      if(empty($this->arg['ids'])) {
        $this->arg['ids'] = array(0);
      }
      $post_arg['post__in'] = $this->arg['ids'];
    }
    if (isset($this->arg['exc_ids']) && is_array($this->arg['exc_ids'])) {
      $post_arg['post__not_in'] = $this->arg['exc_ids'];
    }

    if (isset($this->arg['author'])) {
      $post_arg['author'] = $this->arg['author'];
    }

    if (isset($this->arg['post_status'])) {
      $post_arg['post_status'] = $this->arg['post_status'];
    }

    if (isset($this->arg['hide_archived']) && $this->arg['hide_archived']) {
      $meta_qry[] = array(
          'relation' => 'OR',
          array(
            'key' => '_listing_flag_archived',
            'compare' => 'NOT EXISTS'
          ),

          array(
            'key' => '_listing_flag_archived',
            'value' => ''
          )
      );
    }

    if (isset($this->arg['only_archived']) && $this->arg['only_archived']) {
      $meta_qry[] = array(
        'key' => '_listing_flag_archived',
        'value' => '1'
      );
    }

    if (isset($this->arg['idnumber'])) {
      $meta_qry[] = array(
        'key' => '_listing_idnumber',
        'value' => $this->arg['idnumber']
      );
    }

    if (isset($this->arg['location']) && !empty($this->arg['location'])) {
      $post_arg['tax_query'][] = array(
        'taxonomy'  => 'locations',
        'field'     => 'term_id',
        'terms'     => $this->arg['location'],
        'compare'   => 'IN'
      );
    } else {
      if (isset($this->arg['cities']) && !empty($this->arg['cities'])) {
        $post_arg['tax_query'][] = array(
          'taxonomy'  => 'locations',
          'field'     => 'name',
          'terms'     => $this->arg['cities'],
          'compare'   => 'LIKE'
        );
      } else {
        if(isset($this->arg['regio']) && !empty($this->arg['regio'])) {
          $this->arg['regio'] = explode(",", $this->arg['regio']);
          $post_arg['tax_query'][] = array(
            'taxonomy'  => 'locations',
            'field'     => 'term_id',
            'terms'     => $this->arg['regio'],
            'compare'   => 'IN'
          );
        }
      }
    }

    // Ingatlan státusz
    if (isset($this->arg['status']) && !empty($this->arg['status'])) {
      $post_arg['tax_query'][] = array(
        'taxonomy'  => 'status',
        'field'     => 'term_id',
        'terms'     => $this->arg['status'],
        'compare'   => 'IN'
      );
    }

    // Ingatlan típus
    if (isset($this->arg['property-types']) && !empty($this->arg['property-types'])) {
      $post_arg['tax_query'][] = array(
        'taxonomy'  => 'property-types',
        'field'     => 'term_id',
        'terms'     => $this->arg['property-types'],
        'compare'   => 'IN'
      );
    }

    // Ingatlan állapota
    if (isset($this->arg['condition']) && !empty($this->arg['condition'])) {
      $post_arg['tax_query'][] = array(
        'taxonomy'  => 'property-condition',
        'field'     => 'term_id',
        'terms'     => $this->arg['condition'],
        'compare'   => 'IN'
      );
    }

    // Pozíciók
    if (isset($this->arg['position']) && !empty($this->arg['position'])) {
      $post_arg['tax_query'][] = array(
        'taxonomy'  => 'positions',
        'field'     => 'name',
        'terms'     => $this->arg['position'],
        'compare'   => '='
      );
    }

    if (isset($this->arg['limit'])) {
      $post_arg['posts_per_page'] = $this->arg['limit'];
    } else {
      $post_arg['posts_per_page'] = 30;
    }

    // Rooms
    if (isset($this->arg['rooms'])) {
      $meta_qry[] = array(
        'key' => '_listing_room_numbers',
        'value' => (int) $this->arg['rooms'],
        'type' => 'numeric',
        'compare' => '>='
      );
    }

    // Alapterület
    if (isset($this->arg['alapterulet'])) {
      $meta_qry[] = array(
        'key' => '_listing_lot_size',
        'value' => (int) $this->arg['alapterulet'],
        'type' => 'numeric',
        'compare' => '>='
      );
    }

    // Price
    if (isset($this->arg['price'])) {
      $price_meta_qry = array();
      $all_price = false;

      if (isset($this->arg['price_from']) && isset($this->arg['price_to'])) {
        $all_price = true;
      }

      if ( $all_price ) {
        $price_meta_qry['relation'] = 'AND';
      }

      if (isset($this->arg['price_from'])) {
        $price_meta_qry[] = array(
          'key' => '_listing_price',
          'value' => (int) $this->arg['price_from'],
          'type' => 'numeric',
          'compare' => '>='
        );
      }

      if (isset($this->arg['price_to'])) {
        $price_meta_qry[] = array(
          'key' => '_listing_price',
          'value' => (int) $this->arg['price_to'],
          'type' => 'numeric',
          'compare' => '<='
        );
      }

      $meta_qry[] = $price_meta_qry;
    }

    if (isset($this->arg['src'])) {
      $src = $this->arg['src'];
      $src_meta_qry = array();
      $src_meta_qry['relation'] = 'AND';

      foreach ((array)$src as $s) {
        $src_meta_qry[] = array(
          'key' => '_listing_keywords',
          'value' => $s,
          'compare' => 'LIKE'
        );
      }

      if(!empty($src_meta_qry)){
        $meta_qry[] = $src_meta_qry;
      }

    }

    if (!empty($meta_qry)) {
      $post_arg['meta_query'] = $meta_qry;
    }

    $post_arg['paged'] = (int)$this->arg['page'];

    $posts = new WP_Query($post_arg);

    $this->query = $posts;
    $this->count = $posts->found_posts;

    /*
    echo '<pre>';
    print_r($post_arg);
    echo '</pre>';
    */

    foreach($posts->posts as $post) {
      $this->datalist[] = new Property($post);
    }
    return $this->datalist;
  }

  public function CountTotal()
  {
    return $this->count;
  }

  public function pagination( $base = '' )
  {
    return paginate_links( array(
    	'base'   => $base.'%_%',
    	'format'  => '?page=%#%',
    	'current' => max( 1, get_query_var('page') ),
    	'total'   => $this->query->max_num_pages
    ) );
  }

  public function getQuery()
 {
   return $this->query;
 }

  public function Count()
  {
    return count($this->datalist);
  }

  public function getListParams( $taxonomy, $selected = null, $render_select = true )
  {
    wp_dropdown_categories(array(
      'show_option_all' => __('-- válasszon --', 'ti'),
      'taxonomy'        => $taxonomy,
      'name'            => 'tax['.$taxonomy.']',
      'id'              => self::PROPERTY_TAXONOMY_META_PREFIX.str_replace("-","_", $taxonomy),
      'orderby'         => 'name',
      'selected'        => $selected,
      'show_count'      => false,
      'hide_empty'      => false,
      'class'           => 'form-control',
      'walker'          => new Properties_Select_Walker
    ));
  }

  public function logView()
  {
    global $wpdb;
    if ($this->arg['id']) {
      $wpdb->insert(
        self::LOG_VIEW_DB,
        array(
          'ip' => $_SERVER['REMOTE_ADDR'],
          'ucid' => ucid(),
          'pid' => $this->arg['id'],
          'ref' => $_SERVER['HTTP_REFERER'],
          'qrystr' => $_SERVER['QUERY_STRING']
        ),
        array(
          '%s', '%d', '%d', '%s', '%s'
        )
      );
    }
  }
}

class Properties_Select_Walker extends Walker_CategoryDropdown {
  function start_el(&$output, $category, $depth, $args) {
		$pad = str_repeat(' ', $depth * 3);

		$cat_name = apply_filters('list_cats', $category->name, $category);

    $cat_name = PropertyFactory::i18n_taxonomy_values($cat_name);

		$output .= "\t<option class=\"level-$depth\" value=\"".$category->term_id."\"";
		if ( $category->term_id == $args['selected'] )
			$output .= ' selected="selected"';
		$output .= '>';
		$output .= $pad.$cat_name;
		if ( $args['show_count'] )
			$output .= '  ('. $category->count .')';
		if ( $args['show_last_update'] ) {
			$format = 'Y-m-d';
			$output .= '  ' . gmdate($format, $category->last_update_timestamp);
		}
		$output .= "</option>\n";
	}
}
?>
