<?php
/**
 * This file contains the WP_Listings class.
 */

/**
 * This class handles the creation of the "Listings" post type, and creates a
 * UI to display the Listing-specific data on the admin screens.
 *
 */
class WP_Listings {

	var $settings_page = 'wp-listings-settings';
	var $settings_field = 'wp_listings_taxonomies';
	var $menu_page = 'register-taxonomies';
	private $temppostid = false;

	var $options;

	/**
	 * Property details array.
	 */
	var $property_details;

	/**
	 * Construct Method.
	 */
	function __construct() {

		$this->options = get_option('plugin_wp_listings_settings');

		$this->property_details = apply_filters( 'wp_listings_property_details', array(
			'col1' => array(
			  __( 'Irányár', 'ti' ) 	=> '_listing_price',
				__( 'Akciós irányár', 'ti' ) 	=> '_listing_offprice',
			),
			'col2' => array(
		    __( 'Építés éve', 'ti' )  => '_listing_year_built',
				__( 'Emeletek (db)', 'ti' )  => '_listing_level_numbers',
				__( 'Szobák (db)', 'ti' )  => '_listing_room_numbers',
				__( 'Hálók (db)', 'ti' )  => '_listing_bedrooms',
				__( 'Méret (nm)', 'ti' )  => '_listing_property_size',
				__( 'Telekméret (nm)', 'ti' )  => '_listing_lot_size',
				__( 'Fürdő (db)', 'ti' )  => '_listing_bathroom_numbers',
				__( 'Garázs (db)', 'ti' )  => '_listing_garage',
				__( 'Ingatlan állapota (1-5)', 'ti' )  => '_listing_star_property',
				__( 'Épület állapota kívül (1-5)', 'ti' )  => '_listing_star_outside',
				__( 'Fényviszony', 'ti' )  => '_listing_light_condition',
			),
			'checkbox' => array(
				__( 'Autóbeálló', 'ti' )  => '_listing_driveways',
				__( 'Kertcsoport, udvar', 'ti' )  => '_listing_garden',
				__( 'Erkély', 'ti' )  => '_listing_balcony',
				__( 'Lift', 'ti' )  => '_listing_lift',
				__( 'Zöldövezet', 'ti' )  => '_listing_green_area',
			),
			'flags' => array(
				__( 'Kiemelt', 'ti' )  => '_listing_flag_highlight',
				__( 'Ár jellege', 'ti' )  => '_listing_flag_pricetype',
			),
		) );

		$this->extended_property_details = apply_filters( 'wp_listings_extended_property_details', array(
			'col1' => array(
			    __( 'Property Type:', 'wp-listings' ) 			=> '_listing_proptype',
			    __( 'Condo:', 'wp-listings' )					=> '_listing_condo',
			    __( 'Financial:', 'wp-listings' )				=> '_listing_financial',
			    __( 'Condition:', 'wp-listings' )				=> '_listing_condition',
			    __( 'Construction:', 'wp-listings' )			=> '_listing_construction',
			    __( 'Exterior:', 'wp-listings' )				=> '_listing_exterior',
			    __( 'Fencing:', 'wp-listings' ) 				=> '_listing_fencing',
				__( 'Interior:', 'wp-listings' ) 				=> '_listing_interior',
				__( 'Flooring:', 'wp-listings' ) 				=> '_listing_flooring',
				__( 'Heat/Cool:', 'wp-listings' ) 				=> '_listing_heatcool'
			),
			'col2' => array(
				__( 'Lot size:', 'wp-listings' ) 				=> '_listing_lotsize',
				__( 'Location:', 'wp-listings' ) 				=> '_listing_location',
				__( 'Scenery:', 'wp-listings' )					=> '_listing_scenery',
				__( 'Community:', 'wp-listings' )				=> '_listing_community',
				__( 'Recreation:', 'wp-listings' )				=> '_listing_recreation',
				__( 'General:', 'wp-listings' )					=> '_listing_general',
				__( 'Inclusions:', 'wp-listings' )				=> '_listing_inclusions',
				__( 'Parking:', 'wp-listings' )					=> '_listing_parking',
				__( 'Rooms:', 'wp-listings' )					=> '_listing_rooms',
				__( 'Laundry:', 'wp-listings' )					=> '_listing_laundry',
				__( 'Utilities:', 'wp-listings' )				=> '_listing_utilities'
			),
		) );

		add_action( 'init', array( $this, 'create_post_type' ) );

		add_filter( 'manage_edit-listing_columns', array( $this, 'columns_filter' ) );
		add_action( 'manage_posts_custom_column', array( $this, 'columns_data' ) );

		add_action( 'admin_menu', array( $this, 'register_meta_boxes' ), 5 );
		add_action( 'save_post', array( $this, 'metabox_save' ), 1, 2 );

		add_action( 'save_post', array( $this, 'save_post' ), 1, 3 );
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );

		add_action( 'admin_init', array( &$this, 'register_settings' ) );
		add_action( 'admin_init', array( &$this, 'add_options' ) );
		add_action( 'admin_menu', array( &$this, 'settings_init' ), 15 );

		add_action( 'admin_enqueue_scripts', array( &$this, 'app_add_color_picker' ) );

	}

	function app_add_color_picker( $hook ) {
	    if( is_admin() ) {
	        // Add the color picker css file
	        wp_enqueue_style( 'wp-color-picker' );
	        // Include our custom jQuery file with WordPress Color Picker dependency
	        wp_enqueue_script( 'custom-script-handle', plugins_url( '/js/label-colorpicker.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
	    }
	}

	/**
	 * Registers the option to load the stylesheet
	 */
	function register_settings() {
		register_setting( 'wp_listings_options', 'plugin_wp_listings_settings' );
	}

	/**
	 * Sets default slug and default post number in options
	 */
	function add_options() {

		$new_options = array(
			'wp_listings_archive_posts_num' => 9,
			'wp_listings_slug' => 'listings'
		);

		if ( empty($this->options['wp_listings_slug']) && empty($this->options['wp_listings_archive_posts_num']) )  {
			add_option( 'plugin_wp_listings_settings', $new_options );
		}

	}

	/**
	 * Adds settings page and IDX Import page to admin menu
	 */
	function settings_init() {
		add_submenu_page( 'edit.php?post_type=listing', __( 'Settings', 'wp-listings' ), __( 'Settings', 'wp-listings' ), 'manage_options', $this->settings_page, array( &$this, 'settings_page' ) );
	}

	/**
	 * Creates display of settings page along with form fields
	 */
	function settings_page() {
		include( dirname( __FILE__ ) . '/views/wp-listings-settings.php' );
	}

	/**
	 * Creates our "Listing" post type.
	 */
	function create_post_type() {

		$args = apply_filters( 'wp_listings_post_type_args',
			array(
				'labels' => array(
					'name'					=> __( 'Listings', 'wp-listings' ),
					'singular_name'			=> __( 'Listing', 'wp-listings' ),
					'add_new'				=> __( 'Add New', 'wp-listings' ),
					'add_new_item'			=> __( 'Add New Listing', 'wp-listings' ),
					'edit'					=> __( 'Edit', 'wp-listings' ),
					'edit_item'				=> __( 'Edit Listing', 'wp-listings' ),
					'new_item'				=> __( 'New Listing', 'wp-listings' ),
					'view'					=> __( 'View Listing', 'wp-listings' ),
					'view_item'				=> __( 'View Listing', 'wp-listings' ),
					'search_items'			=> __( 'Search Listings', 'wp-listings' ),
					'not_found'				=> __( 'No listings found', 'wp-listings' ),
					'not_found_in_trash'	=> __( 'No listings found in Trash', 'wp-listings' ),
					'filter_items_list'     => __( 'Filter Listings', 'wp-listings' ),
					'items_list_navigation' => __( 'Listings navigation', 'wp-listings' ),
					'items_list'            => __( 'Listings list', 'wp-listings' )
				),
				'public'		=> true,
				'query_var'		=> true,
				'show_in_rest'  => true,
				'rest_base'     => 'listing',
				'rest_controller_class' => 'WP_REST_Posts_Controller',
				'menu_position'	=> 5,
				'menu_icon'		=> 'dashicons-admin-home',
				'has_archive'	=> true,
				'supports'		=> array( 'title', 'editor', 'author', 'comments', 'excerpt', 'thumbnail', 'revisions', 'equity-layouts', 'equity-cpt-archives-settings', 'genesis-seo', 'genesis-layouts', 'genesis-simple-sidebars', 'genesis-cpt-archives-settings', 'publicize', 'wpcom-markdown'),
				'rewrite'		=> array( 'slug' => $this->options['wp_listings_slug'], 'feeds' => true, 'with_front' => false ),
			)
		);

		register_post_type( 'listing', $args );

	}

	function register_meta_boxes()
	{
		add_meta_box( 'listing_images_metabox', __( 'Ingatlan képei', 'ti' ), array( &$this, 'listing_images_metabox' ), 'listing', 'normal', 'high' );

		add_meta_box( 'listing_check_flags_metabox', __( 'Extra paraméterek', 'ti' ), array( &$this, 'listing_check_flags_metabox' ), 'listing', 'normal', 'high' );

		add_meta_box( 'listing_details_metabox', __( 'Ingatlan paraméterek', 'ti' ), array( &$this, 'listing_details_metabox' ), 'listing', 'normal', 'high' );

		//add_meta_box( 'listing_features_metabox', __( 'Additional Details', 'wp-listings' ), array( &$this, 'listing_features_metabox' ), 'listing', 'normal', 'high' );

		if ( !class_exists( 'Idx_Broker_Plugin' ) ) {
			add_meta_box( 'idx_metabox', __( 'IDX Broker', 'wp-listings' ), array( &$this, 'idx_metabox' ), 'wp-listings-options', 'side', 'core' );
		}
		if( !function_exists( 'equity' ) ) {
			add_meta_box( 'agentevo_metabox', __( 'Equity Framework', 'wp-listings' ), array( &$this, 'agentevo_metabox' ), 'wp-listings-options', 'side', 'core' );
		}

	}

	function listing_images_metabox() {
		include( dirname( __FILE__ ) . '/views/listing-images-metabox.php' );
	}

	function listing_details_metabox() {
		include( dirname( __FILE__ ) . '/views/listing-details-metabox.php' );
	}

	function listing_features_metabox() {
		include( dirname( __FILE__ ) . '/views/listing-features-metabox.php' );
	}

	function listing_check_flags_metabox() {
		include( dirname( __FILE__ ) . '/views/listing-checkboxflags-metabox.php' );
	}

	function agentevo_metabox() {
		include( dirname( __FILE__ ) . '/views/agentevo-metabox.php' );
	}

	function idx_metabox() {
		include( dirname( __FILE__ ) . '/views/idx-metabox.php' );
	}

	function metabox_save( $post_id, $post ) {

		/** Run only on listings post type save */
		if ( 'listing' != $post->post_type )
			return;

		/* * /
		print_r($_FILES);
		exit;
		/* */

		if ( !isset( $_POST['wp_listings_metabox_nonce'] ) || !wp_verify_nonce( $_POST['wp_listings_metabox_nonce'], 'wp_listings_metabox_save' ) )
	        return $post_id;

	    /** Don't try to save the data under autosave, ajax, or future post */
	    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	    if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) return;
	    if ( defined( 'DOING_CRON' ) && DOING_CRON ) return;

	    /** Check permissions */
	    if ( ! current_user_can( 'edit_post', $post_id ) )
	        return;


			$checkbox_collector = $_POST['wp_listings']['checkboxes'];
			unset($_POST['wp_listings']['checkboxes']);
			$checkbox_selected = $_POST['wp_listings']['checkbox'];
			unset($_POST['wp_listings']['checkbox']);

	    $property_details = $_POST['wp_listings'];

	    if ( ! isset( $property_details['_listing_hide_price'] ) )
				$property_details['_listing_hide_price'] = 0;

	    /** Store the property details custom fields */
	    foreach ( (array) $property_details as $key => $value ) {

	        /** Save/Update/Delete */
	        if ( $value ) {
	            update_post_meta($post->ID, $key, $value);
	        } else {
	            delete_post_meta($post->ID, $key);
	        }

	    }

			// checkboxes
			foreach ((array)$checkbox_selected as $cb => $f) {
				update_post_meta($post->ID, $cb, '1');
				unset($checkbox_collector[array_search($cb, (array)$checkbox_collector)]);
			}


			if(!empty($checkbox_collector))
			foreach ((array)$checkbox_collector as $key) {
				delete_post_meta($post->ID, $key);
			}

			$tempfiles = $_FILES;

		 /********************************************************************
		 * Images
		 *********************************************************************/
		 $post_id = $post->ID;
		 $this->temppostid = $post_id;
		 $uploads_dir = wp_upload_dir();
		 $property_image_dir = $uploads_dir['basedir'] . '/listing/' . $post_id;

		 if ( $_FILES && isset($_FILES['property_images']) )
		 {
			 $first_imaged = false;
			 add_filter( 'upload_dir', array( $this, 'upload_dir_filter') );
			 add_filter( 'intermediate_image_sizes', '__return_empty_array', 99 );

			 $files = $_FILES["property_images"];

			 foreach ($files['name'] as $key => $value) {
				 if ($files['name'][$key]) {
					 $file = array(
							 'name' => $files['name'][$key],
							 'type' => $files['type'][$key],
							 'tmp_name' => $files['tmp_name'][$key],
							 'error' => $files['error'][$key],
							 'size' => $files['size'][$key]
					 );

					 $_FILES = array ("property_images" => $file);

					 foreach ($_FILES as $file => $array) {
						 $newupload = $this->uploads_handler( $file, $post_id);

						 $changed['image_uploads'][] = $newupload;

						 // Első kép beállítása
						 /*
						 if ( !$first_imaged && empty($extra['feature_img_id']) ) {
							 set_post_thumbnail( $this->temppostid, $newupload);
							 $first_imaged = true;
						 }*/

					 }
				 }
			 }
			 remove_filter( 'upload_dir', array( $this, 'upload_dir_filter') );
			 remove_filter( 'intermediate_image_sizes', '__return_empty_array', 99 );
		 }

		 $_FILES = $tempfiles;

		 /**
		 *	Slide kép feltöltése
		 **/
		 if ( $_FILES && isset($_FILES['slide_img']) )
		 {
			 $files = $_FILES["slide_img"];

			 if ($files['name'] != '') {
				 add_filter( 'upload_dir', array( $this, 'upload_dir_filter') );
				 add_filter( 'intermediate_image_sizes', '__return_empty_array', 99 );


				 $_FILES 	= array ("slide_img" => $files);

				 $slide_img_id = $this->uploads_handler( 'slide_img', $post_id);

				 if($slide_img_id) {
					 $slide_check = (int)get_post_meta($post_id, '_listing_slide_img_id', true);

					 if($slide_check == 0) {
						 add_post_meta($post_id, '_listing_slide_img_id', $slide_img_id);
					 }else{
						 wp_delete_attachment($slide_check, true);
						 update_post_meta($post_id, '_listing_slide_img_id', $slide_img_id);
					 }
				 }

				 remove_filter( 'upload_dir', array( $this, 'upload_dir_filter') );
				 remove_filter( 'intermediate_image_sizes', '__return_empty_array', 99 );
		 	}
		 }

		 $_FILES = $tempfiles;


		 $extra = $_POST['wp_listings']['extra'];

		 // Profilkép cseréje
      if ( $extra['feature_img_id'] != get_post_thumbnail_id($this->temppostid)) {
        set_post_thumbnail( $this->temppostid, $extra['feature_img_id']);
      }
      // Kép(ek) törlése
      if (!empty($extra['deleting_imgs'])) {
        foreach ($extra['deleting_imgs']as $did => $v) {
          wp_delete_attachment( $did );
        }
      }

		 $this->temppostid = false;
	}

	public function upload_dir_filter( $dir )
  {
    return array(
      'path'   => $dir['basedir'] . '/listing/'.$this->temppostid,
      'url'    => $dir['baseurl'] . '/listing/'.$this->temppostid,
      'subdir' => '/listing/'.$this->temppostid,
     ) + $dir;
  }

	public function uploads_handler ( $file_handler, $post_id, $set_thu = false )
  {
    // check to make sure its a successful upload
    if ($_FILES[$file_handler]['error'] !== UPLOAD_ERR_OK) __return_false();

    require_once(ABSPATH . "wp-admin" . '/includes/image.php');
    require_once(ABSPATH . "wp-admin" . '/includes/file.php');
    require_once(ABSPATH . "wp-admin" . '/includes/media.php');

    $attach_id = media_handle_upload( $file_handler, $this->temppostid );

    // Resize
    if ( true && $attach_id )
    {
      $this->resize_attachment($attach_id, 1200, 1200);
    }

    // Watermark
    if ( $this->do_watermark && $attach_id )
    {
			/*
      $image = new ImageModifier();
      $image->loadResourceByID($attach_id);
      $image->watermark();
			*/
    }

    return $attach_id;
  }


  private function resize_attachment( $attachment_id, $width = 1024, $height = 1024 )
  {
    // Get file path
    $file = get_attached_file($attachment_id);

    // Get editor, resize and overwrite file
    $image_editor = wp_get_image_editor($file);
    $image_editor->resize($width, $height);
    $image_editor->set_quality(80);
    $saved = $image_editor->save($file);

    // We need to change the metadata of the attachment to reflect the new size

    // Get attachment meta
    $image_meta = get_post_meta($attachment_id, '_wp_attachment_metadata', true);

    // We need to change width and height in metadata
    $image_meta['height'] = $saved['height'];
    $image_meta['width']  = $saved['width'];

    // Update metadata
    return update_post_meta($attachment_id, '_wp_attachment_metadata', $image_meta);
  }

	/**
	 * Filter the columns in the "Listings" screen, define our own.
	 */
	function columns_filter ( $columns ) {

		$columns = array(
			'cb'					=> '<input type="checkbox" />',
			'listing_thumbnail'		=> __( 'Thumbnail', 'wp-listings' ),
			'title'					=> __( 'Listing Title', 'wp-listings' ),
			'listing_details'		=> __( 'Details', 'wp-listings' ),
			'listing_tags'			=> __( 'Tags', 'wp-listings' )
		);

		return $columns;

	}

	/**
	 * Filter the data that shows up in the columns in the "Listings" screen, define our own.
	 */
	function columns_data( $column ) {

		global $post, $wp_taxonomies;

		$image_size = 'style="max-width: 115px;"';

		apply_filters( 'wp_listings_admin_listing_details', $admin_details = $this->property_details['col1']);

		if (isset($_GET["mode"]) && trim($_GET["mode"]) == 'excerpt' ) {
			apply_filters( 'wp_listings_admin_extended_details', $admin_details = $this->property_details['col1'] + $this->property_details['col2']);
			$image_size = 'style="max-width: 150px;"';
		}

		$image = wp_get_attachment_image_src(get_post_thumbnail_id(), 'thumbnail');

		switch( $column ) {
			case "listing_thumbnail":
				echo '<p><img src="' . $image[0] . '" alt="listing-thumbnail" ' . $image_size . '/></p>';
				break;
			case "listing_details":
				foreach ( (array) $admin_details as $label => $key ) {
					printf( '<b>%s</b> %s<br />', esc_html( $label ), esc_html( get_post_meta($post->ID, $key, true) ) );
				}
				break;
			case "listing_tags":
				_e('<b>Status</b>: ' . get_the_term_list( $post->ID, 'status', '', ', ', '' ) . '<br />', 'wp-listings');
				_e('<b>Property Type:</b> ' . get_the_term_list( $post->ID, 'property-types', '', ', ', '' ) . '<br />', 'wp-listings');
				_e('<b>Location:</b> ' . get_the_term_list( $post->ID, 'locations', '', ', ', '' ) . '<br />', 'wp-listings');
				_e('<b>Features:</b> ' . get_the_term_list( $post->ID, 'features', '', ', ', '' ), 'wp-listings');
				break;
		}

	}

	/**
	 * Adds query var on saving post to show notice
	 * @param  [type] $post_id [description]
	 * @param  [type] $post    [description]
	 * @param  [type] $update  [description]
	 * @return [type]          [description]
	 */
	function save_post( $post_id, $post, $update ) {
		if ( 'listing' != $post->post_type )
			return;

		add_filter( 'redirect_post_location', array( &$this, 'add_notice_query_var' ), 99 );
	}

	function add_notice_query_var( $location ) {
		remove_filter( 'redirect_post_location', array( &$this, 'add_notice_query_var' ), 99 );
		return add_query_arg( array( 'wp-listings' => 'show-notice' ), $location );
	}

	/**
	 * Displays admin notices if show-notice url param exists or edit listing page
	 * @return object current screen
	 * @uses  wp_listings_admin_notice
	 */
	function admin_notices() {

		$screen = get_current_screen();

		if ( isset( $_GET['wp-listings']) || $screen->id == 'edit-listing' ) {
			if ( !class_exists( 'Idx_Broker_Plugin') ) {
				echo wp_listings_admin_notice( __( '<strong>Integrate your MLS Listings into WordPress with IDX Broker!</strong> <a href="http://www.idxbroker.com/features/idx-wordpress-plugin">Find out how</a>', 'wp-listings' ), false, 'activate_plugins', (isset( $_GET['wp-listings'])) ? 'wpl_listing_notice_idx' : 'wpl_notice_idx' );
			}
			if( !function_exists( 'equity' ) ) {
				echo wp_listings_admin_notice( __( '<strong>Stop filling out forms. Equity automatically enhances your listings with extra details and photos.</strong> <a href="http://www.agentevolution.com/equity/">Find out how</a>', 'wp-listings' ), false, 'activate_plugins', (isset( $_GET['wp-listings'])) ? 'wpl_listing_notice_equity' : 'wpl_notice_equity' );
			}
			if( get_option('wp_listings_import_progress') == true ) {
				echo wp_listings_admin_notice( __( '<strong>Your listings are being imported in the background. This notice will dismiss when all selected listings have been imported.</strong>', 'wp-listings' ), false, 'activate_plugins', 'wpl_notice_import_progress' );
			}
		}

		return $screen;
	}

}
