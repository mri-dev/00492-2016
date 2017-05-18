<?php
class ListingLista
{
    const SCTAG = 'listing-list';
    // Elérhető set-ek
    public $params = array();
    public $template = 'standard';
    public $type;

    public function __construct()
    {
        add_action( 'init', array( &$this, 'register_shortcode' ) );
    }

    public function register_shortcode() {
        add_shortcode( self::SCTAG, array( &$this, 'do_shortcode' ) );
    }

    public function do_shortcode( $attr, $content = null )
    {
        $output = '<div class="'.self::SCTAG.'-holder listing-sc">';

    	  /* Set up the default arguments. */
        $defaults = apply_filters(
            self::SCTAG.'_defaults',
            array(
              'src' => 'list',
              'view' => 'standard',
              'limit' => 6,
              'listing' => 0,
              'loc' => false,
              'city' => '',
              'excid' => 0
            )
        );

        /* Parse the arguments. */
        $attr           = shortcode_atts( $defaults, $attr );
        $this->params   = $attr;
        $this->type     = $attr['src'];
        $this->template = $attr['view'];

        if (!is_null($attr['src']))
        {
          $output .= '<div class="'.self::SCTAG.'-set-'.$this->type.'">';
          switch ( $this->type )
          {
            case 'highlight':
              $output .= $this->highlight();
            break;
            case 'viewed':
              $output .= $this->viewed();
            break;
            case 'get':
              $output .= $this->get();
            break;
            case 'recomended':
              $output .= $this->recomended();
            break;
            case 'unwatched':
              $output .= $this->unwatched();
            break;
            default:
              $output .= $this->no_src();
            break;
          }
          $output .= '</div>';
        }

        $output .= '</div>';

        /* Return the output of the tooltip. */
        return apply_filters( self::SCTAG, $output );
    }


    /**
    * Nem megnézett ingatlanok listázás
    **/
    private function unwatched( $arg = array() )
    {
      global $wpdb;
      global $notify;

      $unwatched_prop = $notify->propertyUnwatched();

      if ($unwatched_prop > 1) {
        $o = '<a class="set-watced-prop" href="/news/?setwatched=1"><i class="fa fa-eye"></i> '.__('Összes megtekintettnek jelölése', 'gh').'</a>';
      }

      $o .= '<h1>'.__('Új ingatlan hirdetések', 'gh').'</h1>';
      $o .= '<div class="subtitle">'.__('Az alábbi listában találja azokat az ingatlanokat, amiket Ön még nem tekintett meg.', 'gh').'</div>';
      $t = new ShortcodeTemplates(__CLASS__.'/'.$this->template);

      if (isset($_GET['settedWatchedAll'])) {
        $o .= '<div class="alert alert-success">'.sprintf(__('Ön a mai dátumig (%s) minden korábbi ingatlant megtekintettnek jelölt.', 'gh'), current_time('mysql')).'</div>';
      }

      $ucid = ucid();

      // Visited
      $qry = "SELECT pid FROM `".\PropertyFactory::LOG_VIEW_DB."` as t WHERE t.`ucid` = '".$ucid."' ORDER BY t.visited DESC;";

      $idsq = $wpdb->get_results($qry, ARRAY_A );
      $ids = array();
      foreach ($idsq as $sid) {
        if(count($ids) >= $this->params['limit']) break;
        if (!in_array($sid['pid'], $ids)) {
          $ids[] = $sid['pid'];
        }
      }

      // Time click
      $t_qry = "SELECT wtime FROM `".\PropertyFactory::LOG_WATCHTIME_DB."` as t WHERE t.`ucid` = '".$ucid."' ORDER BY t.wtime DESC LIMIT 0,1;";
      $watchtimestmp = $wpdb->get_var($t_qry);

      //var_dump($watchtimestmp);

      $arg = array(
        'exc_ids' => $ids,
        'limit' => $this->params['limit'],
        'lang' => get_locale(),
      );

      $arg['page'] = max( 1, get_query_var('page') );

      if ($watchtimestmp) {
        $arg['after_date'] = $watchtimestmp;
      }
      $properties = new Properties($arg);
      $list = $properties->getList();
      $this->pagionation = $properties->pagination('/'.SLUG_NEWS.'/');

      if ( count($list) != 0 ) {
        $o .= '<div class="prop-list im3 style-'.$this->template.'">';
        $o .= '<div class="prop-wrapper">';
        foreach ( $list as $e )
        {
          $o .= $t->load_template( array( 'item' => $e ) );
        }
        $o .= '</div>';
        $o .= '<div class="pagination">'.$this->pagionation.'</div>';
        $o .= '</div>';
      } else {
        ob_start();
        include(locate_template('templates/parts/nodata-listing-unwatched.php'));
        $o .= ob_get_contents();
        ob_end_clean();
      }

      return $o;
    }

    /**
    * Kedvencek
    **/
    private function favorite( $arg = array() )
    {
      global $wpdb;

      $o = '<h1>'.__('Kedvenc ingatlanok listája', 'ti').'</h1>';
      $o .= '<div class="subtitle">'.__('Az alábbi listában találja azokat az ingatlanokat, amiket Ön kedvencnek jelölt.').'</div>';
      $t = new ShortcodeTemplates(__CLASS__.'/'.$this->template);

      $get = $_GET;

      $ucid = ucid();

      $fav_ids = array();
      $favs = $wpdb->get_results($wpdb->prepare( "SELECT pid FROM listing_favorites WHERE ucid = %s ORDER BY added DESC;", $ucid ), ARRAY_A);

      foreach ($favs as $f) {
        $fav_ids[] = $f['pid'];
      }

      $arg = array(
        'limit' => $this->params['limit'],
      );

      if (count($fav_ids) != 0)
      {
        $arg['ids'] = $fav_ids;

        $properties = new Properties($arg);
        $list = $properties->getList();

        $o .= '<div class="prop-list style-'.$this->template.'"><div class="prop-wrapper">';
        foreach ( $list as $e )
        {
          $o .= $t->load_template( array( 'item' => $e ) );
        }
        $o .= '</div></div>';
      } else {
        ob_start();
        include(locate_template('templates/parts/nodata-listing-favorite.php'));
        $o .= ob_get_contents();
        ob_end_clean();
      }

      return $o;
    }

    /**
    * Kereső, standard listázás
    **/
    private function get( $arg = array() )
    {
      $get = $_GET;

      switch ($get['title'])
      {
        case 'news':
          $o = '<h1>'.__('Legújabb ingatlanok', 'ti').'</h1>';
        break;
        case 'highlight':
          $o = '<h1>'.__('Kiemelt ingatlanok', 'ti').'</h1>';
        break;
        default:
          if($this->template != 'maplist') {
            $o = '<h1>'.__('Keresés eredménye', 'ti').'</h1>';
          }
        break;
      }

      $t = new ShortcodeTemplates(__CLASS__.'/'.$this->template);

      $arg = array(
        'limit' => $this->params['limit'],
      );

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

      $arg['page'] = max( 1, get_query_var('page') );

      $properties = new Properties($arg);
      $list = $properties->getList();
      $this->pagionation = $properties->pagination('/'.SLUG_INGATLAN_LIST.'/');
      $query = $properties->getQuery();

      if ( count($list) != 0 ) {
        $o .= '<div class="prop-list im3 style-'.$this->template.'">';
        $o .= '<div class="prop-list-info">
          <div class="total">'.sprintf(__('%d találat', 'gh'), $query->found_posts).'</div>
          <div class="pages">'.sprintf(__('<strong>%d. oldal</strong> / %d', 'gh'), $arg['page'], $query->max_num_pages).'</div>
        </div>';
        $o .= '<div class="prop-wrapper">';
        foreach ( $list as $e )
        {
          $o .= $t->load_template( array( 'item' => $e ) );
        }
        $o .= '</div>';
        $o .= '<div class="pagination">'.$this->pagionation.'</div>';
        $o .= '</div>';
      } else {
        ob_start();
        include(locate_template('templates/parts/nodata-listing-get.php'));
        $o .= ob_get_contents();
        ob_end_clean();
      }
      return $o;
    }

    /**
    * Ajánlott listázás
    **/
    private function recomended( $arg = array() )
    {
      $get = $_GET;



      $t = new ShortcodeTemplates(__CLASS__.'/'.$this->template);

      $arg = array(
        'limit' => $this->params['limit'],
      );

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
        $arg['price_from'] = $get['pa'];
        $arg['price'] = 1;
      }
      if (isset($get['pb']) && !empty($get['pb'])) {
        $arg['price_to'] = $get['pb'];
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

      if (isset($get['c']) && !empty($get['c'])) {
        $arg['property-types'] = explode(",", $get['c']);
      }

      if (isset($this->params['loc']) && $this->params['loc'] !== false) {
        $arg['location'] = array($this->params['loc']);
      }

      if (isset($this->params['excid']) && !empty($this->params['excid'])) {
        $arg['exc_ids'] = array($this->params['excid']);
      }

      $properties = new Properties($arg);
      $list = $properties->getList();


      if ( count($list) != 0 ) {
        $o = '<div class="header">
          <h2>'.sprintf(__('További ingatlanok itt: %s', 'ti'), $this->params['city']).'</h2>
        </div>';
        $o .= '<div class="prop-list im4 style-'.$this->template.'"><div class="prop-wrapper">';
        foreach ( $list as $e )
        {
          $o .= $t->load_template( array( 'item' => $e ) );
        }
        $o .= '</div></div>';
      } else {
        /*ob_start();
        include(locate_template('templates/parts/nodata-listing-get.php'));
        $o .= ob_get_contents();
        ob_end_clean();*/
      }
      return $o;
    }

    /**
    * Kiemelt listázás
    **/
    private function highlight( $arg = array() )
    {
      $o = '<div class="header">
        <div class="morev"><a title="'.__('További kiemelt ingatlanok', 'ti').'" href="/'.SLUG_INGATLAN_LIST.'/?hl=1&title=highlight">'.__('Még több', 'ti').' <i class="fa fa-bars"></i></a></div>
        <h2>'.__('Kiemelt ingatlanok', 'ti').'</h2>
      </div>';
      $t = new ShortcodeTemplates(__CLASS__.'/'.$this->template);

      $arg = array(
        'limit' => $this->params['limit'],
        'orderby' => 'rand',
        'highlight' => true
      );

      $properties = new Properties($arg);
      $list = $properties->getList();

      $o .= '<div class="prop-list style-'.$this->template.'"><div class="prop-wrapper">';
      foreach ( $list as $e )
      {
        $o .= $t->load_template( array( 'item' => $e ) );
      }
      $o .= '</div></div>';

      if($this->template == 'slide')
        {
          ob_start();
          include(locate_template('templates/shortcodes/'.__CLASS__.'/js/'.$this->template.'.php'));
          $o .= ob_get_contents();
          ob_end_clean();
        }

      return $o;
    }

    /**
    * Megnézett ingatlanok listázás
    **/
    private function viewed( $arg = array() )
    {
      global $wpdb;

      if ($this->params['listing'] == '1') {
        $listing = true;
      }

      if (!$listing) {
        $o = '<div class="header">
          <h2>'.__('Legutóbb megtekintett ingatlanok', 'ti').'</h2>
        </div>';
      } else {
        $o = '<div>
          <h1>'.__('Korábban megtekintett ingatlanok', 'ti').'</h1>
        </div>';
      }

      $t = new ShortcodeTemplates(__CLASS__.'/'.$this->template);

      $ucid = ucid();

      // Visited
      $qry = "SELECT pid FROM `".\PropertyFactory::LOG_VIEW_DB."` as t WHERE t.`ucid` = '".$ucid."' ORDER BY t.visited DESC LIMIT 0, 100;";

      $idsq = $wpdb->get_results($qry, ARRAY_A );
      $ids = array();
      foreach ($idsq as $sid) {
        if(count($ids) >= $this->params['limit']) break;
        if (!in_array($sid['pid'], $ids)) {
          $ids[] = $sid['pid'];
        }
      }

      $arg = array(
        'ids' => $ids,
        'limit' => $this->params['limit'],
        'orderby' => 'post__in'
      );

      $arg['page'] = max( 1, get_query_var('page') );

      $properties = new Properties($arg);
      $list = $properties->getList();
      $this->pagionation = $properties->pagination('/'.SLUG_WATCHED.'/');

      $o .= '<div class="prop-list '.( ($listing) ? 'im3' : 'im5' ).' style-'.$this->template.'"><div class="prop-wrapper">';

      if (count($list) > 0) {
        foreach ( $list as $e )
        {
          $o .= $t->load_template( array( 'item' => $e ) );
        }
      } else {
        if ($listing) {
          ob_start();
          include(locate_template('templates/parts/nodata-listing-viewed.php'));
          $o .= ob_get_contents();
          ob_end_clean();
        }
      }

      $o .= '</div>';
      if ($listing) {
        $o .= '<div class="pagination">'.$this->pagionation.'</div>';
      }
      $o .= '</div>';

      return $o;
    }

    private function no_src()
    {
      return sprintf(__('(!) Nincs ilyen source: %s', 'ti'), $this->type);
    }
}

new ListingLista();

?>
