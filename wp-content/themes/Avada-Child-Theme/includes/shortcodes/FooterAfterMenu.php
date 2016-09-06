<?php

class Footer_After_Menu extends AvadaTemplate
{
	const SHORTCODE_TAG = 'footer-after-menu';
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

        // Kereső
        $search = '
        <form class="searchform seach-form" role="search" method="get" action="'.get_option('url', true).'">
            <input type="hidden" name="lang" value="hu">
            <input id="searchform" type="text" value="" name="s" class="s" placeholder="">
            <button type="submit"><i class="fa fa-search"></i></button>
        </form>';

        // Social
        $social = new Avada_Social_Icons();
        $socials = $social->render_social_icons(array(
            'position' => 'footer'
        ));

        /* Set up the default variables. */
        $r->_('<div class="'.self::SHORTCODE_TAG.'">');

        	$r->_($r->row_start());
                $r->_($r->column( 1, 2, '<a href="'.get_option('siteurl', true).'"><img src="'.IMGROOT.'viasale-travel-logo-100x50.png" alt="'.get_option('blogname', true).'"></a>', 'no'));
                $r->_($r->column( 1, 2, '<div class="footer-right-side">
                        <div class="footer-search">'.$search.'</div>
                        <div class="social-links">'.$socials.'</div>
                    </div>', 'no', true));
         	$r->_($r->row_end());

        $r->_('</div>');


        /* Return the output of the tooltip. */
        return apply_filters( self::SHORTCODE_TAG, $this->render );
    }

    private function _($text){
    	$this->render .= $text;
    }
}

new Footer_After_Menu();
