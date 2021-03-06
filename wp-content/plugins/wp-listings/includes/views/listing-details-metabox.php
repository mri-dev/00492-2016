<?php
wp_nonce_field( 'wp_listings_metabox_save', 'wp_listings_metabox_nonce' );

global $post;

$pattern = '<p><label>%s<br /><input type="text" name="wp_listings[%s]" value="%s" /></label></p>';

echo '<div style="width: 45%; float: left">';

	foreach ( (array) $this->property_details['col1'] as $label => $key ) {
		printf( $pattern, esc_html( $label ), $key, esc_attr( get_post_meta( $post->ID, $key, true ) ) );
	}

	$pf = new PropertyFactory();
	$sel_pf = esc_attr( get_post_meta( $post->ID, '_listing_flag_pricetype', true ) );

	if($sel_pf == '' || empty($sel_pf) || !$sel_pf)
	{
		$sel_pf = 0;
	}
	echo '<p>
	<label for="_listing_flag_pricetype">'.__('Ár jellege', 'ti').'</label> <br>
	<select id="_listing_flag_pricetype" name="wp_listings[_listing_flag_pricetype]">';
	foreach ((array)$pf->price_types as $key => $value) {
		echo '<option '. ( ($sel_pf == $value)?'selected="selected"':'' ) .' value="'.$value.'">'.$pf->i18n_pricetype_values($value).'</option>';
	}
	echo '</select></p>';

echo '</div>';

echo '<div style="width: 45%; float: right;">';

	foreach ( (array) $this->property_details['col2'] as $label => $key ) {
		printf( $pattern, esc_html( $label ), $key, esc_attr( get_post_meta( $post->ID, $key, true ) ) );
	}

echo '</div><br style="clear: both;" />';

$pattern = '<p><label>%s<br /><textarea type="text" name="wp_listings[%s]" value="%s" rows="2" style="width: 100&#37;;">%s</textarea></label></p>';

/*
_e('<h4>Extended Details:</h4>', 'wp-listings');
echo '<div style="width: 45%; float: left">';

	foreach ( (array) $this->extended_property_details['col1'] as $label => $key ) {
		printf( $pattern, esc_html( $label ), $key, esc_attr( get_post_meta( $post->ID, $key, true ) ), esc_attr( get_post_meta( $post->ID, $key, true ) ) );
	}

echo '</div>';


echo '<div style="width: 45%; float: right;">';

	foreach ( (array) $this->extended_property_details['col2'] as $label => $key ) {
		printf( $pattern, esc_html( $label ), $key, esc_attr( get_post_meta( $post->ID, $key, true ) ), esc_attr( get_post_meta( $post->ID, $key, true ) ) );
	}

echo '</div>';
*/
echo '<br style="clear: both;" />';

/*
echo '<div style="width: 45%; float: left">';
	_e('<h4>Price Options</h4>', 'wp-listings');
	printf( __('<p><label>Hide the price from visitors?<br /> <input type="checkbox" name="wp_listings[_listing_hide_price]" value="1" %s /></label></p>'),
		checked( get_post_meta( $post->ID, '_listing_hide_price', true ), 1, 0 ) );

	_e('<p><label>Text to display instead of price (or leave blank):<br />', 'wp-listings');
	printf( __( '<input type="text" name="wp_listings[_listing_price_alt]" value="%s" /></label></p>', 'wp-listings' ), htmlentities( get_post_meta( $post->ID, '_listing_price_alt', true) ) );
echo '</div>';

echo '<div style="width: 90%; float: left;">';

	_e('<h4>Custom Overlay Text</h4>', 'wp-listings');
	_e('<p><label>Custom text to display as overlay on featured listings<br />', 'wp-listings');
	printf( __( '<input type="text" name="wp_listings[_listing_text]" value="%s" /></label></p>', 'wp-listings' ), htmlentities( get_post_meta( $post->ID, '_listing_text', true) ) );

echo '</div><br style="clear: both;" /><br /><br />';
*/

echo '';

echo '<div style="width: 100%; float: left;">';
	_e('<h4>Kereső kulcsszavak</h4>', 'ti');
	echo '<em>Szóközzel válassza el azokat a kulcsszavakat, melyek alapján rátalálhat a látogató.</em>';
	echo '<textarea rows="5" cols="15" style="width: 100%;" name="wp_listings[_listing_keywords]">'.get_post_meta( $post->ID, '_listing_keywords', true).'</textarea>';
	echo '</div>';
echo '<hr>';

echo '<div style="width: 90%; float: left;">';
	_e('<h4>Lista címke felirat</h4>', 'ti');
	echo '<p><label for="label_text">'.__('Felirat szövege (rövid, 1 szavas - Pl.: Medencés)', 'ti').'</label><br>';
	echo '<input type="text" id="label_text" name="wp_listings[_listing_listlabel_text]" value="'.get_post_meta( $post->ID, '_listing_listlabel_text', true).'" /></p>';
	echo '<p><label for="label_color">'.__('Felirat háttérszín (sötét árnyalat, fehér a szöveg)', 'ti').'</label><br>';
	echo '<input type="text" id="label_color" class="color-picker" data-default-color="#ffffff" name="wp_listings[_listing_listlabel_bgcolor]" value="'.get_post_meta( $post->ID, '_listing_listlabel_bgcolor', true).'" /></p>';
echo '<hr>';

echo '<div style="width: 100%; float: left">';

	_e('<h4>Térkép beállítások</h4>', 'ti');
	_e('<em>Ha nincs megadva koordináta, akkor az automatikusan lekért város koordináták a mérvadóak.</em>', 'ti');

	if(get_post_meta($post->ID, '_listing_automap', 1) == FALSE) {
    	update_post_meta($post->ID, '_listing_automap', 'y');
    }
		/*
	printf( __('<p><label>Automatically insert map based on latitude/longitude? <strong>Will be overridden if a shortode is entered below.</strong><br /> <input type="radio" name="wp_listings[_listing_automap]" value="y" %s>Yes</input> <input type="radio" name="wp_listings[_listing_automap]" value="n" %s>No</input></label></p>'),
		checked( get_post_meta( $post->ID, '_listing_automap', true ), 'y', 0 ),
		checked( get_post_meta( $post->ID, '_listing_automap', true ), 'n', 0 ) );
		*/
echo '</div>';
echo '<div style="clear: both; width: 45%; float: left;">';
	printf( __('<p><label>Latitude: <br /><input type="text" name="wp_listings[_listing_gps_lat]" value="%s" /></label></p>', 'wp-listings'), get_post_meta( $post->ID, '_listing_gps_lat', true) );
echo '</div>';
echo '<div style="width: 45%; float: right;">';
	printf( __('<p><label>Longitude: <br /><input type="text" name="wp_listings[_listing_gps_lng]" value="%s" /></label></p>', 'wp-listings'), get_post_meta( $post->ID, '_listing_gps_lng', true) );
echo '</div><br style="clear: both;" />';


echo '</div><br style="clear: both;" />';
echo '<hr>';
echo '<div style="width: 100%; float: left;">';
	_e('<h4>Tervrajz</h4>', 'ti');

	$wplistings_gallery_content = get_post_meta( $post->ID, '_listing_property_layouts', true);
	$wplistings_gallery_editor_id = '_listing_property_layouts';

	$wplistings_gallery_editor_settings = array(
			'wpautop' 			=> false,
			'textarea_name' 	=> 'wp_listings[_listing_property_layouts]',
			'editor_class'		=> 'wplistings_gallery',
			'textarea_rows'		=> 20,
			'tinymce'			=> true,
			'quicktags'			=> true,
			'drag_drop_upload'	=> true
		);

	wp_editor($wplistings_gallery_content, $wplistings_gallery_editor_id, $wplistings_gallery_editor_settings);

echo '</div><br style="clear: both;" /><br>';

echo '<hr>';
echo '<div style="width: 100%; float: left;">';
	_e('<h4>Videó bemutató</h4>', 'ti');
	printf( __( '<textarea name="wp_listings[_listing_video]" rows="5" cols="18" style="%s">%s</textarea></label></p>', 'wp-listings' ), 'width: 99%;', htmlentities( get_post_meta( $post->ID, '_listing_video', true) ) );

	/*
	_e('<p><label>Or enter Map Embed Code or shortcode from Map plugin (such as <a href="http://jetpack.me/support/shortcode-embeds/" target="_blank" rel="nofollow">Jetpack Shortcodes</a>, <a href="https://wordpress.org/plugins/simple-google-maps-short-code/" target="_blank" rel="nofollow">Simple Google Maps Short Code</a> or <a href="https://wordpress.org/plugins/mappress-google-maps-for-wordpress/" target="_blank" rel="nofollow">MapPress</a>):<br /><em>Recommend size: 660x300 (If possible, use 100% width, or your themes content width)</em><br />', 'wp-listings');
	printf( __( '<textarea name="wp_listings[_listing_map]" rows="5" cols="18" style="%s">%s</textarea></label></p>', 'wp-listings' ), 'width: 99%;', htmlentities( get_post_meta( $post->ID, '_listing_map', true) ) );
	*/

echo '</div>';
echo '<br style="clear: both;" />';
