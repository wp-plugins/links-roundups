<?php
/**
 * Link Roundups Post Type and Supporting Functions
 *
 * @package Link_Roundups
 * @version 0.3
 */

/**
 * The LRoundups class - so we don't have function naming conflicts with link-roundups
 */
class LRoundups {

	// Initialize the plugin
	public static function init() {

		// Register the custom post type of roundup
		add_action('init', array( __CLASS__, 'register_post_type' ) );

		// Add our custom post fields for our custom post type
		add_action( 'admin_init', array( __CLASS__, 'add_custom_post_fields' ) );

		// Add the Link Roundups Options sub menu
		add_action( 'admin_menu', array( __CLASS__, 'add_lroundups_options_page') );

		// Save our custom post fields! Very important!
		add_action( 'save_post', array( __CLASS__, 'save_custom_fields') );

		// Make sure our custom post type gets pulled into the river
		add_filter( 'pre_get_posts', array( __CLASS__,'my_get_posts') );

	}

	// Pull the linkroundups into the queries for is_home, is_tag, is_category, is_archive

	// Merge the post_type query var if there is already a custom post type being pulled in otherwise do post & linkroundups
	public static function my_get_posts( &$query ) {
		// bail out early if suppress filters is set to true
		if ( $query->get( 'suppress_filters' ) ) return;
		if ( is_admin() ) return;

		// Add roundup to the post type in the query if it is not already in it.
		if ( $query->is_home() || $query->is_tag() || $query->is_category() || $query->is_author() ) {
			if ( isset( $query->query_vars['post_type'] ) && is_array( $query->query_vars['post_type'] ) ) {
				if ( ! in_array( 'roundup', $query->query_vars['post_type'] ) ) {
					// There is an array of post types and roundup is not in it
					$query->set( 'post_type', array_merge( array( 'roundup' ), $query->query_vars['post_type'] ) );
				}
			} elseif ( isset( $query->query_vars['post_type'] ) && !is_array( $query->query_vars['post_type'] ) ) {
				if ( $query->query_vars['post_type'] !== 'roundup' ) {
					// There is a single post type, so we shall add it to an array
					$query->set( 'post_type', array( 'roundup', $query->query_vars['post_type'] ) );
				}
			} else {
				// Post type is not set, so it shall be post and roundup
				$query->set( 'post_type', array( 'post','roundup' ) );
			}
		}
	}

	/**
	 * Register the Link Roundups Custom Post Type
	 * Use Options Page settings to set Names and Slug
	 *
	 * @since 0.1
	 */
	public static function register_post_type() {
		$singular_opt = get_option( 'lroundups_custom_name_singular' );
		$plural_opt = get_option( 'lroundups_custom_name_plural' );
		$slug_opt = get_option( 'lroundups_custom_url' );

		if( !empty( $singular_opt ) ) {
			$singular = $singular_opt;
		}
		else {
			$singular = 'Link Roundup';
		}

		if( !empty( $plural_opt ) ) {
			$plural = $plural_opt;
		}
		else {
			$plural = 'Link Roundups';
		}

		$roundup_options = array(
			'labels' 		=> array(
				'name' 			=> $plural,
				'singular_name' => $singular,
				'add_new' 		=> 'Add '. $singular,
				'add_new_item' 	=> 'Add New '. $singular,
				'edit' 			=> 'Edit',
				'edit_item'		=> 'Edit ' . $singular,
				'view' 			=> 'View',
				'view_item' 	=> 'View ' . $singular,
				'search_items' 	=> 'Search ' . $plural,
				'not_found' 	=> 'No ' . $plural . ' found',
				'not_found_in_trash' => 'No ' . $plural . ' found in Trash',
			),
			'description' 	=> $plural,
			'supports' 		=> array(
				'title', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks', 'custom-fields',
				'comments', 'revisions', 'page-attributes', 'post-formats'
			),
			'public' 		=> true,
			'menu_position' => 7,
			'menu_icon' 	=> 'dashicons-list-view',
			'taxonomies' 	=> apply_filters( 'roundup_taxonomies', array( 'category','post_tag' ) ),
			'has_archive' 	=> true,
		);

		if ( $slug_opt != '' )
			$roundup_options['rewrite'] = array( 'slug' => $slug_opt );

		register_post_type( 'roundup', $roundup_options );
	}

	/**
	 * Register meta box for custom fields on roundup edit pages.
	 *
	 * @since 0.1
	 * @see display_custom_fields()
	 */
	public static function add_custom_post_fields() {
		add_meta_box(
			'link_roundups_roundup', 'Recent Saved Links',
			array( __CLASS__, 'display_custom_fields' ), 'roundup', 'advanced', 'high'
		);
	}

	/**
	 * Show our custom post fields in the add/edit Argo Link Roundups admin pages
	 *
	 * @since 0.1
	 */
	public static function display_custom_fields() {
	?>
		<div id='lroundups-display-area'></div>
		<script type='text/javascript'>
		jQuery(function(){
			jQuery( '#lroundups-display-area' ).load( '<?php echo plugin_dir_url(LROUNDUPS_PLUGIN_FILE); ?>inc/saved-links/display-recent.php' );
		});
		</script>
	<?php
	}

	/**
	 * Save the custom post field data.	Very important!
	 *
	 * Wait, does this do anything on roundups!? - Will
	 *
	 * @since 0.1
	 */
	public static function save_custom_fields( $post_id ) {
		if ( isset( $_POST['argo_link_url'] ) ){
			update_post_meta( ( isset( $_POST['post_id'] ) ? $_POST['post_ID'] : $post_id ), 'argo_link_url', $_POST["argo_link_url"] );
		}
		if ( isset( $_POST['argo_link_description'] ) ) {
			update_post_meta( ( isset($_POST['post_id'] ) ? $_POST['post_ID'] : $post_id ), 'argo_link_description', $_POST['argo_link_description'] );
		}
	}

	/**
	 * Add options sub menu for roundups.
	 *
	 * @since 0.1
	 */
	public static function add_lroundups_options_page() {
		add_submenu_page(
			'edit.php?post_type=roundup', 	// $parent_slug
			'Options', 						// $page_title
			'Options', 						// $menu_title
			'edit_posts', 					// $capability
			'link-roundups-options',  	    // $menu_slug
			array( __CLASS__, 'build_lroundups_options_page' ) 	// $function
		);

		// call register settings function
		add_action( 'admin_init', array( __CLASS__, 'register_mysettings' ) );
	}

	public static function register_mysettings() {
		// register our settings
		register_setting( 'lroundups-settings-group', 'lroundups_custom_url' );
		register_setting( 'lroundups-settings-group', 'lroundups_custom_html' );
		register_setting( 'lroundups-settings-group', 'lroundups_custom_name_singular' );
		register_setting( 'lroundups-settings-group', 'lroundups_custom_name_plural' );
		register_setting(
			'lroundups-settings-group', 'lroundups_use_mailchimp_integration',
			array( __CLASS__, 'validate_mailchimp_integration' ) 
		);
		register_setting( 'lroundups-settings-group', 'lroundups_mailchimp_api_key' );
		register_setting( 'lroundups-settings-group', 'lroundups_mailchimp_template' );
		register_setting( 'lroundups-settings-group', 'lroundups_mailchimp_list' );
	}

	public static function validate_mailchimp_integration($input) {
		// Can't have an empty MailChimp API Key if the integration functionality is enabled.
		if ( empty( $_POST['lroundups_mailchimp_api_key'] ) && !empty( $input ) ) {
			add_settings_error(
				'lroundups_use_mailchimp_integration',
				'lroundups_use_mailchimp_integration_error',
				'Please enter a valid MailChimp API Key.',
				'error'
			);
			return '';
		}

		return $input;
	}

	public static function build_lroundups_options_page() {
		$mc_api_key = get_option( 'lroundups_mailchimp_api_key' );

		/**
		 * It's not possible to use this functionality if curl is not enabled in php.
		 */
		if ( ! function_exists('curl_init') ) {
			add_settings_error(
				'lroundups_use_mailchimp_integration',
				'curl_not_enabled',
				__('Curl is not enabled on your server. The MailChimp features will not work without curl. Please contact your server administrator to have curl enabled.', 'link-roundups'),
				'error'
			);
			delete_option( 'lroundups_use_mailchimp_integration' );

		// only query MailChimp if it's possible to do so and if plugins are enabled
		} else if ( get_option( 'lroundups_use_mailchimp_integration' ) && !empty( $mc_api_key ) ) {
			$opts = array( 'debug' => ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? WP_DEBUG : false );
			$mcapi = new Mailchimp( $mc_api_key, $opts );

			$templates = $mcapi->templates->getList(
				array(
					'gallery' 	=> false,
					'base' 		=> false
				),
				array( 'include_drag_and_drop' => true )
			);

			// The endpoint is lists/list, to list the lists, but there is no lists->list. getList with no args is equivalent.
			$lists = $mcapi->lists->getList();
		}

		include_once dirname( LROUNDUPS_PLUGIN_FILE ) . '/templates/options.php';
	}
}
