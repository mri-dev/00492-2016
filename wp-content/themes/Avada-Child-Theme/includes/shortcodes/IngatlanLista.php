<?php

class IngatlanLista extends AvadaTemplate
{
    const SHORTCODE_TAG = 'ingatlan-lista';
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
        // Queries
        $params = array(
            'post_type' => 'listing',
            'tax_query' => array(
                array(
                    'taxonomy' => 'positions',
                    'field' => 'term_id',
                    'terms' => 57
                )
            )
        );
        $list = new WP_Query($params);

        /* Set up the default variables. */
        $r->_('<div class="'.self::SHORTCODE_TAG.'">');

            $r->_($r->row_start());
                while( $list->have_posts() ){ $list->the_post();
                    $id = $list->the_ID();
                    $r->_(get_the_title($is));
                }
                wp_reset_postdata();
            $r->_($r->row_end());

        $r->_('</div>');

        /* Return the output of the tooltip. */
        return apply_filters( self::SHORTCODE_TAG, $this->render );
    }

    private function get_taxonomy_tag_id($tax_name, $tag_name)
    {
        $tax_terms = get_terms(array(
            'taxonomy' => $tax_name,
            'hide_empty' => false
        ));
        foreach ($tax_terms as $tag) {
            if($tag->slug == $tag_name) {
                return $tag->term_id;
            }
        }
    }

    private function _($text){
        $this->render .= $text;
    }
}

new IngatlanLista();
