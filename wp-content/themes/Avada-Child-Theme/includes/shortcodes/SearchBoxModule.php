<?php

class Search_Box_Module extends AvadaTemplate
{
    const SHORTCODE_TAG = 'search-box';
    private $render = "";

    /**
     * Sets up our actions/filters.
     *
     * @since 0.1.0
     * @access public
     * @return void
     */
    public function __construct()
    {
        /* Register shortcodes on 'init'. */
        add_action( 'init', array( &$this, 'register_shortcode' ) );
    }

    /**
     * Registers the [tooltip] shortcode.
     *
     * @since  0.1.0
     * @access public
     * @return void
     */
    public function register_shortcode() {
        add_shortcode( self::SHORTCODE_TAG, array( &$this, 'do_shortcode' ) );
    }

    public function do_shortcode( $attr, $content = null )
    {
        $r = $this;
          /* Set up the default arguments. */
        $defaults = apply_filters(
            self::SHORTCODE_TAG.'_defaults',
            array(
            )
        );

        /* Parse the arguments. */
        $attr = shortcode_atts( $defaults, $attr );

        /* Set up the default variables. */
        $r->_('<div class="'.self::SHORTCODE_TAG.'">');

            $r->_($r->row_start());
              $r->_('asdasd');
            $r->_($r->row_end());

        $r->_('</div>');

        /* Return the output of the tooltip. */
        return apply_filters( self::SHORTCODE_TAG, $this->render );
    }

    private function _($text){
        $this->render .= $text;
    }
}

new Search_Box_Module();
