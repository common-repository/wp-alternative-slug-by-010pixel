<?php
/*
 * Plugin Name: WP Alternative Slug by 010Pixel
 * Plugin URI: http://www.010pixel.com/
 * Description: Create alternative slug for each page, post or custom post type
 * Author: 010 Pixel
 * Version: 1.3.0
 * Author URI: http://www.010pixel.com/
 */

	/**
	 *
	 * The plug is consist of 3 classes.
	 *
	 * 1. set_alternative_slug_metabox_010Pixel
	 * 2. alternative_slug_admin_010Pixel
	 * 3. alternative_slug_redirect_010Pixel
	 *
	 * The Plugin will check which page user is accessing and load the class accordingly
	 *
	 */
?><?php
	if ( ! is_admin() ) { // User is accessing Front end
		$GLOBALS['alternative_slug_redirect_010Pixel'] = new alternative_slug_redirect_010Pixel();
	} else { // User is accessing Admin Panel
		$GLOBALS['set_alternative_slug_metabox_010Pixel'] = new set_alternative_slug_metabox_010Pixel();
		
		// Manage Template Allowed List Admin Panel
		$GLOBALS['alternative_slug_admin_010Pixel'] = new alternative_slug_admin_010Pixel();
	}
?><?php
	/**
	* 
	*/
	class alternative_slug_base_010pixel
	{
		
		protected $metaBoxTitle = 'Alternative Slug';
		protected $metaInputBox = 'post_alt_slug';
		protected $metaKey = 'alt_slug';
		protected $optionKey = "alternative_slug_post_type_list";
		
		function __construct() {}

		public function get_slug () {
			global $wp;

			$q = $wp->request;
			
			$q = preg_replace("/(\.*)(html|htm|php|asp|aspx)$/","", $wp->request);
			$parts = explode('/', $q);
			$q = end($parts);

			return $q;
		}

		protected function get_content_from_metadata ( $value = false ) {

			// Get the values as array
			$this->PostTypesWithAlternativeSlugAllowed = (array) get_option( $this->optionKey );

			$args = array(
				'meta_query' => array(
					array (
						'key' => $this->metaKey,
						'value' => $value
					)
				),
				'post_type' => $this->PostTypesWithAlternativeSlugAllowed
			);

			$posts_array = get_posts( $args );

			return $posts_array;

		}

		protected function create_slug ( $value ) {
			return strtolower( str_replace(" ", "-", $value) );
		}
	}
?><?php
	/**
	 *
	 * set_alternative_slug_metabox_010Pixel
	 * ============================================================================
	 * This class is to create metabox for Alternative Slug input metabox Post Types which are
	 * in allowed post types for alternative slug
	 *
	 */

	class set_alternative_slug_metabox_010Pixel extends alternative_slug_base_010pixel
	{

		// Create an Array of Post Types which are allowed to use alternative slug
		public $PostTypesWithAlternativeSlugAllowed = array ();

		// Initiate Class
		public function __construct()
		{
			// Add values to Post Types with Allowed Alternative Slug List from alternative_slug_post_type_list option
			// Get the values as array
			$this->PostTypesWithAlternativeSlugAllowed = (array) get_option( $this->optionKey );

			// Start creating Meta Box
			add_action('add_meta_boxes',array( &$this, 'add_alternative_slug_metabox' ));
			
			// Save Data to Database
			add_action('save_post',array( &$this, 'save_alt_slug' ),10,2);
			
			// Add link in plugins list
			$prefix = is_network_admin() ? 'network_admin_' : '';
			add_filter( $prefix . 'plugin_action_links_' . plugin_basename(__FILE__), array( &$this,'addSettingsLink' ), 10, 2 );
		}
		
		// Add Meta Box to Current Post Type
		public function add_alternative_slug_metabox() {
			$currentPostType = get_post_type();
			if ( in_array($currentPostType, $this->PostTypesWithAlternativeSlugAllowed) ) {
				add_meta_box('postparentdiv_alt_slug_010pixel', __($this->metaBoxTitle),  array( &$this, 'alternative_slug_meta_box' ), $currentPostType, 'side', 'core');
			}
		}
		
		// Create Meta Box
		public function alternative_slug_meta_box($post) {
			
			// Check if the Current Post Type is in the Allowed Alternative Slug Post Types list
			if ( in_array($post->post_type, $this->PostTypesWithAlternativeSlugAllowed) ) {
				// Get current page Alternative Slug Name (if any)
				$alternativeSlug = get_post_meta($post->ID, $this->metaKey,true);
				
				$html = '';
				// Security Nonce Key
				$html .= '<input type="hidden" name="custom_type_noncename" id="custom_type_noncename" value="' . wp_create_nonce( $post->post_type . $post->ID ) . '" />';
				$html .= '<label class="screen-reader-text" for="'. $this->metaInputBox .'">' . translate($this->metaBoxTitle) . '</label>';
				$html .= '<input type="text" name="'. $this->metaInputBox .'" id="'. $this->metaInputBox .'" placeholder="alternative slug" value="' . $alternativeSlug . '" />';

				echo $html;
			}
		}

		// Save Data to Database
		public function save_alt_slug($post_id,$post) {
			
		   // verify this came from the our screen and with proper authorization.
			if ( !wp_verify_nonce( @$_POST['custom_type_noncename'], $post->post_type . $post_id )) {
				return $post_id;
			}
			
			// verify if this is an auto save routine. If it is our form has not been submitted, so we dont want to do anything
			if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
				return $post_id;
				
			// Check permissions
			if ( !current_user_can( 'edit_post', $post_id ) )
				return $post_id;

			$alt_slug_value = $this->create_slug ( $_POST[ $this->metaInputBox ] );
				
 			// Check if the Current Post Type is in the Allowed Alternative Slug Post Types list and Post Alternative Slug value is not empty
			if ( in_array($post->post_type, $this->PostTypesWithAlternativeSlugAllowed) && !empty($alt_slug_value) )
			{
				// Save Alternative Slug to metadata
				$existing_post = $this->get_content_from_metadata($alt_slug_value)[0];
				if ( !$existing_post || ($existing_post->ID == $post->ID) ) {
					update_post_meta($post->ID, $this->metaKey,$alt_slug_value);
				}
			} else {
				// If the value is empty, then delete existing alternative slug (if any)
				delete_post_meta($post->ID, $this->metaKey);
			}
			
		}
		
		// Add a link to the setting option page
		public static function addSettingsLink( $links ) {
		
			$links[] = '<a href="'.admin_url('options-general.php?page=010pixel_alternative_slug').'"> '.__( 'Settings', 'sis' ).' </a>';
			
			return $links;
		}
	}
?><?php
	/**
	 *
	 * alternative_slug_admin_010Pixel
	 * ============================================================================
	 * This class will create Admin Page where all the post types will be visible.
	 * Tick the post types for which alternative slug input metabox is needed.
	 *
	*/
	class alternative_slug_admin_010Pixel extends alternative_slug_base_010pixel
	{
		
		// Initiate Class
		public function __construct()
		{
			add_action('admin_menu', array( &$this, 'add_alt_slug_mngr_page') );
			add_action('admin_init', array( &$this, 'alt_slug_mngr_serialize' )); 
		}
		
		// Create Menu to access Alternative Slug List Admin Page
		public function add_alt_slug_mngr_page() {  
		  
			add_options_page(  
				'Alternative Slug Metabox',
				'Alternative Slug Metabox',
				'administrator',
				'010pixel_alternative_slug',
				array( &$this, 'alternative_slug_plugin_options' )
			);  
		  
		}
		
		// Create Alternative Slug Settings Page
		public function alternative_slug_plugin_options() { 
			?> 
			<div class="wrap"> 
			 
				<?php screen_icon(); ?>
				<h2>Alternative Slug Allowed Post Types List</h2> 
				<?php // settings_errors(); ?> 
				 
				<form method="post" action="options.php"> 
					<?php
						// Load the form
						settings_fields( $this->optionKey );  
						do_settings_sections( $this->optionKey );  
						submit_button();
					?> 
				</form> 
				 
			</div>
			<?php 
		}
		
		// Create Form for Post Type Lists
		public function alt_slug_mngr_serialize() { 
		 
			// If the alternative slug post type list options don't exist, create them.  
			if( false == get_option( $this->optionKey ) ) {    
				add_option( $this->optionKey );  
			}
		  
			// Register a section
			add_settings_section(  
				'alternative_slug_list_section',
				'Choose Post Types for which you want to allow Alternative Slug.',
				array( &$this, 'alternative_slug_list_callback' ),
				$this->optionKey
			);
			
			// Introduce the fields for Listing Post Types with checkbox
			add_settings_field(   
				'post_types_list',
				'Post Types',
				array( &$this, 'post_types_list_callback' ),
				$this->optionKey,
				'alternative_slug_list_section'
			); 
			
			// Register the fields with WordPress 
			register_setting( 
				$this->optionKey, 
				$this->optionKey 
			); 
			 
		}
		
		public function alternative_slug_list_callback() { 
			echo '<hr />'; 
		}
		
		// Post Types Checkbox
		public function post_types_list_callback($args) { 
			
			// Read the options collection  
			$options = get_option($this->optionKey); 
			
			// Get the Post Types List
			$post_types=get_post_types(array('public' => true),'objects');
			
			$html = '';
			
			foreach ($post_types  as $post_type ) {
				if ( $post_type->name != 'attachment' ) {
					$html .= '<input 
								type="checkbox" 
								id="'. $this->optionKey .'['. $post_type->name .']"
								name="'. $this->optionKey .'['. $post_type->name .']" 
								value="'. $post_type->name .'" ' . checked($post_type->name, isset($options[$post_type->name]) ? $options[$post_type->name]: '' , false) . '
							/>&nbsp;';   
					$html .= '<label for="'. $this->optionKey .'['. $post_type->name .']">' . $post_type->label .'</label><br />';
				}
			}
			 
			echo $html; 
		}
	}
?><?php
	/**
	 *
	 * alternative_slug_redirect_010Pixel
	 * ============================================================================
	 * This class will be loaded in front end. It will check the post alternative slug
	 * and redirect relevant page
	 *
	*/
	class alternative_slug_redirect_010Pixel extends alternative_slug_base_010pixel
	{
		
		public function __construct()
		{
			add_filter( 'template_redirect', array( $this, 'load_custom_template') );
		}

		public function load_custom_template (  $default_template  )
		{
			if ( is_404() ) {
				// Get slug from URL
				$slug = $this->create_slug ( urldecode($this->get_slug()) );

				// Get all posts/pages/custom posts which includes $slug as alternative slug
				$post = $this->get_content_from_metadata($slug)[0];

				if ( $post->ID && in_array($post->post_type, $this->PostTypesWithAlternativeSlugAllowed) ) {
					// Redirect to necessary page or show 404 page
					wp_redirect(get_post_permalink($post->ID));
				}
			}
		}
		protected function create_slug ( $value ) {
			return strtolower( str_replace(" ", "-", $value) );
		}
	}
?>