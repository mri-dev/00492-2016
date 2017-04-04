<?php
class ListingSlider
{
    const SCTAG = 'listing-slider';

    public function __construct()
    {
        add_action( 'init', array( &$this, 'register_shortcode' ) );
    }

    public function register_shortcode() {
        add_shortcode( self::SCTAG, array( &$this, 'do_shortcode' ) );
    }

    public function do_shortcode( $attr, $content = null )
    {
      /* Set up the default arguments. */
      $defaults = apply_filters(
          self::SCTAG.'_defaults',
          array(
            'view' => 'v1'
          )
      );
        $attr = shortcode_atts( $defaults, $attr );
        $output = '<div class="'.self::SCTAG.'-holder style-'.$attr['view'].'">';

        $arg = array();
        $arg['position'] = 'Slideshow';
        $arg['order'] = 'ASC';
        $arg['orderby'] = 'ID';
        $properties = new Properties( $arg );
        $list = $properties->getList();


        $t = new ShortcodeTemplates(__CLASS__.'/'.$attr['view']);



        if ( count($list) != 0 ) {
          $output .= '<div class="'.self::SCTAG.'-list" key="psliding" dataSet="1,2" start="1" step="1"><div class="list-wrapper">';
          $i = 0;
          foreach ( $list as $e )
          {
            $i++;
            $output .= $t->load_template( array( 'item' => $e, 'i' => $i ) );
          }
          $output .= '</div></div>';

          ob_start();
          include(locate_template('templates/shortcodes/'.__CLASS__.'/scripts.php'));
          $output .= ob_get_contents();
          ob_end_clean();

        } else {
          ob_start();
          include(locate_template('templates/parts/nodata-listing-get.php'));
          $output .= ob_get_contents();
          ob_end_clean();
        }

        $output .= '</div>';

        /* Return the output of the tooltip. */
        return apply_filters( self::SCTAG, $output );
    }

}

new ListingSlider();

?>
