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
                // Fejrész
                $r->_('<div class="sb-header">Ingatlankeresés</div>');

                // Body
                $r->_('<div class="sb-body">');
                    // Régió
                    $r->_($r->column(1, 3, '<div class="sb-inp-wrapper"><label for="sb-inp-regio">Régió</label><select name="region" id="sb-inp-regio">'.$regio_options.'</select></div>', 'no'));
                    // Szobaszám
                    $r->_($r->column(1, 6, '<div class="sb-inp-wrapper"><label for="sb-inp-szobaszam">Szobák száma</label><select name="rooms" id="sb-inp-szobaszam">'.$szoba_options.'</select></div>', 'no'));
                    // Alapterület
                    $r->_($r->column(1, 6, '<div class="sb-inp-wrapper"><label for="sb-inp-alapnm">Alapterület</label><input type="number" min="0" step="1" value="" placeholder="nm" id="sb-inp-alapnm"></div>', 'no'));
                    // Kategória
                    $r->_($r->column(1, 3, '<div class="sb-inp-wrapper"><label for="sb-inp-kategoria">Kategória</label><select name="category" id="sb-inp-kategoria">'.$kategoria_options.'</select></div>', 'no'));
                    $r->_('<div class="fusion-clearfix"></div>');
                $r->_('</div>');

                // Lábrész
                $r->_('<div class="sb-footer">');
                    //$r->_($r->column(1, 6, '<a href="">Térképes kereső</a>'));
                    $r->_($r->column(1, 2, '<a href="">Részletesebben szeretnék keresni <i class="fa fa-caret-right"></i></a>', 'no', true));
                    $r->_('<div class="fusion-clearfix"></div>');
                $r->_('</div>');
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
