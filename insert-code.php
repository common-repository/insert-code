<?php
/*
Plugin Name: Insert Code
Plugin URI: https://www.bcswebsitesolutions.com/downloads/insert-code/
Description: Allows you to insert code before or after the content and also allow you to insert code after nth paragraphs of your post or page contents.
Version: 2.4
Tested up to: 4.4
License: GPLv2 or later.
Author: BCS Website Services
Author URI: https://www.bcswebsitesolutions.com/
*/
ob_start();
class InsertCode{
	
	public function __construct() {
		// Plugin Details
        $this->plugin               = new stdClass;
        $this->plugin->name         = 'insert-code'; // Plugin Folder
        $this->plugin->displayName  = 'Insert Code'; // Plugin Name
        $this->plugin->posttype 	= 'insertcode';
        $this->plugin->version      = '1.0.0';
        $this->plugin->folder       = plugin_dir_path( __FILE__ );
        $this->plugin->url          = plugin_dir_url( __FILE__ );
		
		//Startup Script
		add_action('init', array($this, 'insertcode_registerPostTypes'));
 		add_action('admin_enqueue_scripts', array($this, 'insertcode_adminScriptsAndCSS'));
		add_action('admin_menu', array($this, 'insertcode_adminPanelsAndMetaBoxes'));
		add_action('save_post', array($this, 'insertcode_save'));
		
		add_action( 'admin_notices',  array($this, 'insertcode_wp_admin_area_notice') );
		add_action('admin_init', array($this, 'insertcode_ignore'));
		add_action('admin_init', array($this,  'insertcode_ignore_ad'));
		add_action( 'admin_notices', array($this, 'insertcode_my_admin_notice') );
				
 		add_filter('the_content', array(&$this, 'checkInsertCodeRequiredPlaces'));
    
   		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( &$this, 'plugin_manage_link' ), 10, 1 );


	}
function insertcode_wp_admin_area_notice() {
 	  global $current_user ;
 
        $user_id = $current_user->ID;
	if ( is_super_admin() ) {
	 /* Check that the user hasn't already clicked to ignore the message */
	 if ( ! get_user_meta($user_id, 'insertcode_thanks_ad') ) {
		 
   echo '<div class="updated  is-dismissible"><p>';
 
        printf(__('Thank You For Installing "Insert Code"  developed by <a href="https://www.bcswebsitesolutions.com" target="_blank">BCS Website Solutions</a>| <a href="%1$s">Hide Notice</a>'), '?insertcode_thanks_ad=0');
 
        echo "</p></div>";

	 }}
}

function insertcode_ignore() {
	global $current_user;
        $user_id = $current_user->ID;
        /* If user clicks to ignore the notice, add that to their user meta */
        if ( isset($_GET['insertcode_thanks_ad']) && '0' == $_GET['insertcode_thanks_ad'] ) {
             add_user_meta($user_id, 'insertcode_thanks_ad', 'true', true);
	}
}

function insertcode_ignore_ad() {
	global $current_user;
        $user_id = $current_user->ID;
        /* If user clicks to ignore the notice, add that to their user meta */
        if ( isset($_GET['insertcode_company_ad']) && '0' == $_GET['insertcode_company_ad'] ) {
             add_user_meta($user_id, 'insertcode_company_ad', 'true', true);
	}
}

function insertcode_my_admin_notice() {
global $current_user ;
 
        $user_id = $current_user->ID;
	if ( is_super_admin() ) {
	 /* Check that the user hasn't already clicked to ignore the message */
	 if ( ! get_user_meta($user_id, 'insertcode_company_ad') ) {
		 ?>
    <div class="updated">
        <?php
		printf(__('<p><img src="https://www.bcswebsiteservices.com/images/Logo-only-50x50.png" style="margin:10px;display:block;" width="40" height="40" align="left" />Looking for a website like no other? You\'re at the right place. BCS Website Services can help your business to develop a web presence that matches your corporate identity, and use the internet to gain new customers and better serve your existing clients.</p><p><a href="https://www.bcswebsiteservices.com" target="_blank">Read More...</a> | <a href="%1$s">Hide Notice</a></p>'), '?insertcode_company_ad=0');
 
   ?>
    </div>
    <?php
	 }}
}

	function plugin_manage_link( $links ) {
		return array_merge(
		array( 'settings' => '<a href="' . admin_url('edit.php?post_type=insertcode&page=insert-code').'">Settings</a>'),
		$links
	);
		}
   
	function insertcode_adminScriptsAndCSS(){
        wp_enqueue_style($this->plugin->name.'-admin', $this->plugin->url.'/css/admin.css', array(), $this->plugin->version, false);
	}
	
	
	function insertcode_adminPanelsAndMetaBoxes(){
        add_submenu_page('edit.php?post_type='.$this->plugin->posttype, "Settings", "Settings", 'manage_options', $this->plugin->name, array($this, 'insertcode_adminPanel'));
		add_meta_box('insertcode_meta', 'Insert Code', array( &$this, 'insertcode_displayMetaBox'), $this->plugin->posttype, 'normal', 'high');
		$postTypes = get_post_types(array(
			'public' => true,
		), 'objects');
		if ($postTypes) {
			foreach ($postTypes as $postType) {
				// Skip attachments
				if ($postType->name == 'attachment') {
					continue;
				}
				
				// Skip our CPT
				if ($postType->name == $this->plugin->posttype) {
					continue;
				}
				add_meta_box('insertcode_meta',$this->plugin->displayName, array( &$this, 'insertcode_displayOptionsMetaBox'), $postType->name, 'normal', 'high');
			}
		}
		
		remove_submenu_page( 'edit.php?post_type=insertcode', 'edit-tags.php?taxonomy=category&amp;post_type=insertcode' );
 register_taxonomy_for_object_type( 'category', 'page' );
		 
	}
	
	function insertcode_adminPanel(){
		// Save Settings
        if (isset($_POST['submit'])) {
        	if (isset($_POST[$this->plugin->name])) {
        		delete_option($this->plugin->name);
        		update_option($this->plugin->name, $_POST[$this->plugin->name]);
				$this->message ='Insert Code Settings Saved.';
			}
			
        }
        
        // Get latest settings
        $this->settings = get_option($this->plugin->name);
		// Load Settings Form
        include_once(WP_PLUGIN_DIR.'/'.$this->plugin->name.'/dashboard/views/settings.php');
	}
	
	function insertcode_displayMetaBox($post) {
		// Get meta
		$insertcode_Code = get_post_meta($post->ID, '_insertcode_code', true);
		$insertcode_Position = get_post_meta($post->ID, '_insertcode_position', true);
		$insertcode_paragraphNumber = get_post_meta($post->ID, '_insertcode_paragraph_number', true);
		
		// Nonce field
		wp_nonce_field($this->plugin->name, $this->plugin->name.'_nonce'); ?>

<p>
  <textarea name="insertcode_code" id="insertcode_code" style="width: 100%; height: 100px; font-family: Courier; font-size: 12px;"><?php echo $insertcode_Code; ?></textarea>
</p>
<p>
  <label for="insertcode_position">Display the Code:</label>
  <select name="insertcode_position" size="1">
    <option value="top"<?php echo (($insertcode_Position == 'top') ? ' selected' : ''); ?>>Before Content</option>
    <option value=""<?php echo (($insertcode_Position == '') ? ' selected' : ''); ?>>After Paragraph Number</option>
    <option value="bottom"<?php echo (($insertcode_Position == 'bottom') ? ' selected' : ''); ?>>After Content</option>
  </select>
  <input type="number" name="insertcode_paragraph_number" value="<?php echo $insertcode_paragraphNumber; ?>" min="1" max="999" step="1" id="paragraph_number" />
</p>
<?php
	}
	function insertcode_displayOptionsMetaBox($post) {
		// Get meta
		$disable = get_post_meta($post->ID, '_insertcode_disable', true);
		
		// Nonce field
		wp_nonce_field($this->plugin->name, $this->plugin->name.'_nonce');
		?>
<p>
  <label for="insertcode_disable">Disable Insert Code</label>
  <input type="checkbox" name="insertcode_disable" id="insertcode_disable" value="1"<?php echo ($disable ? ' checked' : ''); ?> />
</p>
<p class="description"> Check this option if you wish to disable all Post Ads from displaying on this content. </p>
<?php
	}
	function insertcode_save($post_id) {
		// Check if our nonce is set.
		if (!isset($_POST[$this->plugin->name.'_nonce'])) {
			return $post_id;
		}
		
		// Verify that the nonce is valid.
		if (!wp_verify_nonce($_POST[$this->plugin->name.'_nonce'], $this->plugin->name)) {
			return $post_id;
		}
		
		// Check the logged in user has permission to edit this post
		if (!current_user_can('edit_post', $post_id)) {
			return $post_id;
		}
	    
		// OK to save meta data
		if (isset($_POST['insertcode_disable'])) {
			update_post_meta($post_id, '_insertcode_disable', $_POST['insertcode_disable']);
		} else {
			delete_post_meta($post_id, '_insertcode_disable');
		}
		
		if (isset($_POST['insertcode_code'])) {
			
			update_post_meta($post_id, '_insertcode_code', $_POST['insertcode_code']);
		}
		if (isset($_POST['insertcode_position'])) {
			update_post_meta($post_id, '_insertcode_position', $_POST['insertcode_position']);
		}
		if (isset($_POST['insertcode_paragraph_number'])) {
			update_post_meta($post_id, '_insertcode_paragraph_number', $_POST['insertcode_paragraph_number']);
		}
	}
	function insertcode_bcsFeed(){
	include_once(WP_PLUGIN_DIR.'/'.$this->plugin->name.'/dashboard/views/dashboard.php');
	
    }
	function insertcode_registerPostTypes() {
		/* register_post_type($this->plugin->posttype, array(
            'labels' => array(
                'name' => _x('Insert Code', 'post type general name'),
                'singular_name' => _x('Insert Code', 'post type singular name'),
                'add_new' => _x('Add New Code', 'insertcode'),
                'add_new_item' => __('Add New Code'),
				'all_items' => 'List Saved Codes',
                'edit_item' => __('Edit Code'),
                'new_item' => __('Add New Code'),
                'view_item' => __('View Code'),
                'search_items' => __('Search Code'),
                'not_found' =>  __('No code found'),
                'not_found_in_trash' => __('No code found in Trash'),
                'parent_item_colon' => ''
            ),
            'description' => 'Insert Code',
            'public' => false,
            'publicly_queryable' => false,
            'exclude_from_search' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'menu_position' => 20,
            'menu_icon' => 'dashicons-lightbulb',
            'capability_type' => 'post',
            'hierarchical' => false,
            'has_archive' => false,
            'show_in_nav_menus' => false,
            'supports' => array('title'),
			  'taxonomies' => array('category' ),
			'show_in_admin_bar'   => true,
			'can_export'          => true,
        )); */
		
		
			$labels = array(
		'name'                => _x( 'Insert Codes', 'Post Type General Name', 'insert_code' ),
		'singular_name'       => _x( 'Insert Code', 'Post Type Singular Name', 'insert_code' ),
		'menu_name'           => __( 'Insert Code', 'insert_code' ),
		'name_admin_bar'      => __( 'Insert Code', 'insert_code' ),
		'parent_item_colon'   => __( '', 'insert_code' ),
		'all_items'           => __( 'List Saved Codes', 'insert_code' ),
		'add_new_item'        => __( 'Add New Code', 'insert_code' ),
		'add_new'             => __( 'Add New Code', 'insert_code' ),
		'new_item'            => __( 'Add New Code', 'insert_code' ),
		'edit_item'           => __( 'Edit Code', 'insert_code' ),
		'update_item'         => __( 'Update Code', 'insert_code' ),
		'view_item'           => __( 'View Code', 'insert_code' ),
		'search_items'        => __( 'Search Code', 'insert_code' ),
		'not_found'           => __( 'No Code Found', 'insert_code' ),
		'not_found_in_trash'  => __( 'No Code Found in Trash', 'insert_code' ),
	);
	$capabilities = array(
		'edit_post'           => 'edit_insertcode',
		'read_post'           => 'read_insertcode',
		'delete_post'         => 'delete_insertcode',
		'edit_posts'          => 'edit_insertcodes',
		'edit_others_posts'   => 'edit_others_insertcodes',
		'delete_others_posts' => 'delete_others_insertcodes',
		'publish_posts'       => 'publish_insertcodes',
		'read_private_posts'  => 'read_private_insertcodes',
		'edit_published_posts'  => 'edit_published_insertcodes',
	'delete_private_posts'  => 'delete_private_insertcodes',
	'delete_published_posts'  => 'delete_published_insertcodes',
	);
				 
	             
	$args = array(
		'label'               => __( 'Insert Code', 'insert_code' ),
		'description'         => __( 'Insert Code', 'insert_code' ),
		'labels'              => $labels,
		'supports'            => array( 'title', ),
		'taxonomies'          => array( 'category' ),
		'hierarchical'        => false,
		'public'              => false,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 20,
		'menu_icon'           => 'dashicons-lightbulb',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => false,
		'can_export'          => true,
		'has_archive'         => false,
		'exclude_from_search' => true,
		'publicly_queryable'  => false,
	 	'capability_type' => array('insertcode','insertcodes'),//'post',
		'map_meta_cap' => true,
		'capabilities'        => $capabilities,
	);
	register_post_type( $this->plugin->posttype, $args );
	
        flush_rewrite_rules();
	// Add the roles you'd like to administer the custom post types
		$roles = array('administrator');
		
		// Loop through each role and assign capabilities
		foreach($roles as $the_role) {

		     $role = get_role($the_role);
			
	             $role->add_cap( 'read' );
	             $role->add_cap( 'read_insertcode');
	             $role->add_cap( 'read_private_insertcodes' );
	             $role->add_cap( 'edit_insertcode' );
	             $role->add_cap( 'edit_insertcodes' );
	             $role->add_cap( 'edit_others_insertcodes' );
	             $role->add_cap( 'edit_published_insertcodes' );
	             $role->add_cap( 'publish_insertcodes' );
	             $role->add_cap( 'delete_others_insertcodes' );
	             $role->add_cap( 'delete_private_insertcodes' );
	             $role->add_cap( 'delete_published_insertcodes' );
		
		}
		
		
 register_taxonomy_for_object_type( 'category', 'page' );
	}
	
	
	
	/**
	* Checks if the current screen on the frontend needs advert(s) adding to it
	*/
	function checkInsertCodeRequiredPlaces($content) {
		global $post;
		$categoryIDs = array();
		$categories = get_the_category($post->ID);
		foreach($categories as $category) {
		$categoryIDs[] = $category->cat_ID;
		}
		// Settings
		$this->settings = get_option($this->plugin->name);
		if (!is_array($this->settings)) {
			return $content;
		}
		//print_r($this->settings);
		if (count($this->settings) == 0) {
			return $content;
		}
		
		// Check if we are on a singular post type that's enabled
		foreach ($this->settings as $postType=>$enabled) {
			if (is_singular($postType)) {
				// Check the post hasn't disabled Insert Code
				$disable = get_post_meta($post->ID, '_insertcode_disable', true);
				if (!$disable) {
					return $this->insertCodePlease($content,$categoryIDs);
				}
			}
		}
		
		return $content;
	}
	
 	function insertCodePlease($content, $insertpostcat=array()) {
		$codeplease = new WP_Query(array(
			'post_type' => $this->plugin->posttype,
			'post_status' => 'publish',
			'category__in' => $insertpostcat,
			'posts_per_page' => -1,
		));
		//print_r($codeplease);
		if ($codeplease->have_posts()) {
			while ($codeplease->have_posts()) {
				$codeplease->the_post();
				
				$codepleaseID = get_the_ID();
				$insertcode_Code = get_post_meta($codepleaseID, '_insertcode_code', true);
				$insertcode_Position = get_post_meta($codepleaseID, '_insertcode_position', true);
				$insertcode_paragraphNumber = get_post_meta($codepleaseID, '_insertcode_paragraph_number', true);
				
				switch ($insertcode_Position) {
					case 'top':
						$content = $insertcode_Code.$content;
						break;
					case 'bottom':
						$content = $content.$insertcode_Code;
						break;
					default:
						$content = $this->insertCodeAfterParagraph($insertcode_Code, $insertcode_paragraphNumber , $content);
						break;
				}
			}
		}
		
		wp_reset_postdata();
		return $content;
	}
	
 	function insertCodeAfterParagraph( $insertion, $paragraph_id, $content ) {
		$closing_p = '</p>';
		$paragraphs = explode( $closing_p, $content );
		foreach ($paragraphs as $index => $paragraph) {
			// Only add closing tag to non-empty paragraphs
			if ( trim( $paragraph ) ) {
				// Adding closing markup now, rather than at implode, means insertion
				// is outside of the paragraph markup, and not just inside of it.
				$paragraphs[$index] .= $closing_p;
			}

			// + 1 allows for considering the first paragraph as #1, not #0.
			if ( $paragraph_id == $index + 1 ) {
				$paragraphs[$index] .= '<div class="'.$this->plugin->name.'"'.(isset($this->settings['css']) ? '' : ' style="'.$this->settings['custom_css'].'"').'>'. $insertion .'</div>';
			}
		}
		return implode( '', $paragraphs );
	}
 
 
function insertcode_add_action_plugin( $actions, $plugin_file )
{
	
	static $plugin;
 
	if (!isset($plugin))
	  	$plugin = plugin_basename(__FILE__);
	if ($plugin == $plugin_file) {

			$settings = array('settings' => '<a href="options-general.php#redirecthere">' . __('Settings', 'General') . '</a>');
			$site_link = array('support' => '<a href="https://www.bcswebsitesolutions.com/?page_id=825" target="_blank">Support</a>');
		
    			$actions = array_merge($settings, $actions);
				$actions = array_merge($site_link, $actions);
			
		}
		
		return $actions;
}
}
$insertCode = new insertCode();
ob_end_flush();?>
