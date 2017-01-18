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

        $properties = new Properties();

        $t = new ShortcodeTemplates(__CLASS__.'/'.$attr['view']);

        $output .= $t->load_template();

        $output .= '</div>';

        /* Return the output of the tooltip. */
        return apply_filters( self::SCTAG, $output );
    }

}

new ListingSlider();

?>
