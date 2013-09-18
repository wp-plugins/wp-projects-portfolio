<?php
global $debug;
	
$debug = true;
// Define and register the WPProject-Portfolio custom post type
function wpprojects_portfolio_post_type_init() {
	
	$labels = array(
		'name' => __('Projects', 'post type general name'),
		'singular_name' => __('Project', 'post type singular name'),
		'add_new' => __('Add Project', 'Portfolio'),
		'add_new_item' => __('Add Project'),
		'edit_item' => __('Edit Project'),
		'new_item' => __('New Project'),
		'view_item' => __('View Project'),
		'search_items' => __('Search Projects'),
		'not_found' =>  __('No Projects found'),
		'not_found_in_trash' => __('No Portfolios found in Trash'), 
		'parent_item_colon' => ''
	);
	$args = array(
		'labels' => $labels,
		'public' => true,
		'show_in_menu' => true,
		'query_var' => true,
		'rewrite' => array("slug" => 'wp-projects/%wpprojects_portfolio_type%','with_front' => false), //false, array("slug" => $rewrite_slug) // since we aren't pushing to single pages we don't need a re-write rule or permastructure.
		'capability_type' => 'post',
		'hierarchical' => false,
		'menu_position' => 5,
		'supports' => array('title','editor','author'),
		'register_meta_box_cb' => 'add_wpprojects_portfolio_metaboxes',
		'taxonomies' => array('wpprojects_portfolio_type','wpprojects_portfolio_tag')
	); 
	
	register_post_type('wpprojects_portfolio',$args);
		flush_rewrite_rules();

}

// define a custom Portfolio Type taxonomy and populate it
function generate_wpprojects_portfolio_type_taxonomy() {
	
	if (!taxonomy_exists('wpprojects_portfolio_type')) {
		
		$labels = array(
				
			'name'              => __( 'Project Categories', 'Portfolio' ),
			'singular_name'     => __( 'Project Category', 'Portfolio' ),
			'search_items'      => __( 'Search Project Category', 'Portfolio' ),
			'popular_items'     => __( 'Popular Project Categories', 'Portfolio' ),
			'all_items'         => __( 'All Project Categories', 'Portfolio' ),
			'parent_item'       => __( 'Parent Project Category', 'Portfolio' ),
			'parent_item_colon' => __( 'Parent Project Category:', 'Portfolio' ),
			'edit_item'         => __( 'Edit Project Category', 'Portfolio' ),
			'update_item'       => __( 'Update Project Category', 'Portfolio' ),
			'add_new_item'      => __( 'Add New Project Category', 'Portfolio' ),
			'new_item_name'     => __( 'New Project Category', 'Portfolio' ),
			'menu_name'         => __( 'Project Categories', 'Portfolio' )
				
		);
		
		register_taxonomy('wpprojects_portfolio_type', 
						  'wpprojects_portfolio',
						  array(	'hierarchical' => true, 
									'labels' => $labels,
									'show_tagcloud' => true,
									'public' => true,
									'show_in_nav_menus' => true,
									'show_ui' => true,
									'query_var' => 'wpprojects_portfolio_type',
									'rewrite' => array( 'slug' => 'wpprojects_portfolio_type'),
								)
						  );
	 	
		// if there are no WP-Projects-Portfolio Type terms, add a default term
		if (count(get_terms('wpprojects_portfolio_type', 'hide_empty=0')) == 0) {
			wp_insert_term('Default', 'wpprojects_portfolio_type');
		}
	}
	
	if (!taxonomy_exists('wpprojects_portfolio_tag')) {
		
		$labels = array(
				
			'name'              => __( 'Project Tags', 'Project' ),
			'singular_name'     => __( 'Project Tag', 'Project' ),
			'search_items'      => __( 'Search Project Tags', 'Project' ),
			'popular_items'     => __( 'Popular Project Tags', 'Project' ),
			'all_items'         => __( 'All Project Tags', 'Project' ),
			'parent_item'       => __( 'Parent Project Tag', 'Project' ),
			'parent_item_colon' => __( 'Parent Project Tag:', 'Project' ),
			'edit_item'         => __( 'Edit Project Tag', 'Project' ),
			'update_item'       => __( 'Update Project Tag', 'Project' ),
			'add_new_item'      => __( 'Add New Project Tag', 'Project' ),
			'new_item_name'     => __( 'New Project Tag Name', 'Project' ),
			'menu_name'         => __( 'Project Tags', 'Project' )
				
		);
		
		register_taxonomy('wpprojects_portfolio_tag', 
						  'wpprojects_portfolio',
						  array(	'hierarchical' => false, 
									'labels' => $labels,
									'show_tagcloud' => true,
									'public' => true,
									'show_in_nav_menus' => true,
									'show_ui' => true,
									'query_var' => 'wpprojects_portfolio_tag',
									'rewrite' => array( 'slug' => 'wpprojects_portfolio_tag')));
	 	
	}
}

add_filter('post_link', 'wpprojects_portfolio_permalink', 10, 3);
add_filter('post_type_link', 'wpprojects_portfolio_permalink', 10, 3);
 
function wpprojects_portfolio_permalink($permalink, $post_id, $leavename) {

    if (strpos($permalink, '%wpprojects_portfolio_type%') === FALSE) return $permalink;
        // Get post
        $post = get_post($post_id);
        if (!$post) return $permalink;
 
        // Get taxonomy terms
        $terms = wp_get_object_terms($post->ID, 'wpprojects_portfolio_type');  
        if (!is_wp_error($terms) && !empty($terms) && is_object($terms[0])) $taxonomy_slug = $terms[0]->slug;
        else $taxonomy_slug = get_option('wprewrite_slug');
    return str_replace('%wpprojects_portfolio_type%', $taxonomy_slug, $permalink);
}


// Define the WP-Projects-Portfolio custom post type update messages
function wpprojects_portfolio_updated_messages( $messages ) {
	
	global $post, $post_ID;
	
	$messages['wpprojects_portfolio'] = array(
		0 => '', // Unused. Messages start at index 1.
		1 => __('Project details updated.'),
		2 => __('Custom field updated.'),
		3 => __('Custom field deleted.'),
		4 => __('Project updated.'),
		5 => isset($_GET['revision']) ? sprintf( __('Project restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		6 => __('Published.'),
		7 => __('Project details saved.'),
		8 => __('Project details submitted.'),
		9 => sprintf( __('Project scheduled for: <strong>%1$s</strong>.'), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ) ),
		10 => __('Project draft updated.'),
	);
	
	
	return $messages;
}



function wpprojects_portfolio_session_start() {
	
	if ( ! session_id() ) {
		session_start();
	}
	
}

function wpprojects_portfolio_session_end() {
	
	session_destroy();
	
}



/* Define WP-Projects-Portfolio Plugin Activation process */
function wpprojects_portfolio_install() {

    wpprojects_portfolio_post_type_init();
	
	flush_rewrite_rules();
	
}


// Add Portfolio Options menu item
function wpprojects_portolio_admin_menu() {
	
	$page = add_submenu_page('edit.php?post_type=wpprojects_portfolio', 'WP Projects Portfolio Options', 'Options', 'manage_options', 'wp-projects-portfolio', 'wpp_settings_page' );
	
	//add_action('admin_print_styles-post.php', 'portfolio_post_css');

	remove_submenu_page( 'edit.php?post_type=wpprojects_portfolio', 'edit-tags.php?taxonomy=post_tag&amp;post_type=wpprojects_portfolio' );

}



// Add plugin Settings link
function add_wpprojects_portfolio_plugin_settings_link($links) {
	$x = str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
	$settings_link = '<a href="edit.php?post_type=wpprojects_portfolio&page=' . $x .'">' . __('Settings','Portfolio') . '</a>';
	array_unshift($links, $settings_link);
	return $links;
}


function wpp_settings_page() {
?>
<div class="wrap">
<h2>WP Projects Portfolio Options</h2>
<?php if(isset($_POST['action']) && $_POST['action'] == 'update' && $_POST['option_page'] == 'wp-settings-tab') {
	update_option('wprewrite_slug', $_POST['rewrite_slug']);
	update_option('wprec_message', $_POST['rec_message']);
	update_option('wpcr_recmessage', $_POST['cr_recmessage']);
	echo ('<div class="updated"><p><strong>Settings Updated !</strong></p></div>');
}
?>
<form method="post" action="">
    <?php settings_fields( 'wp-settings-tab'); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">Rewrite Slug</th>
        <td><input type="text" name="rewrite_slug" value="<?php echo get_option('wprewrite_slug'); ?>" size="40"/><br>
		Custom rewrite slug settings - portfolio & details page. </td>
        </tr>
         
        <tr valign="top">
        <th scope="row">CR-Request Message</th>
        <td><textarea name="rec_message" rows="6" cols="80"><?php echo get_option('wprec_message'); ?></textarea></td>
        </tr>
        
        <tr valign="top">
        <th scope="row">CR-Received Message</th>
        <td><textarea name="cr_recmessage" rows="6" cols="80"><?php echo get_option('wpcr_recmessage'); ?></textarea></td>
        </tr>
    </table>
    
   <div align="center"><?php submit_button(); ?></div>

</form>
</div>
<?php }

// Make certain the scripts and css necessary to support the file upload button are active
function wpprojects_portfolio_admin_scripts() {
	
	global $post;
	
	$continue = "False";
	
	// don't include the media upload script if we are not on a portfolio edit page
	if (!empty($post)) {
		if (strtolower($post->post_type) == "wpprojects_portfolio") {
			$continue = "True";
		}
	}
	if ($continue == "True") {
		$script = plugins_url('scripts/file_uploader.js', __FILE__);
		$script = wpprojects_portfolio_clear($script);
	
		$ceditorfixscript = plugins_url('scripts/wpppeditor.js', __FILE__);
		$ceditorfixscript_val = wpprojects_portfolio_clear($ceditorfixscript);
		
		wp_enqueue_script('media-upload');
		wp_enqueue_script('thickbox');
		wp_register_script('portfolio-image-upload', $script, array('jquery','media-upload','thickbox'));
		wp_enqueue_script('portfolio-image-upload');
		wp_register_script( 'wpppeditorfix', $ceditorfixscript_val);
		wp_enqueue_script( 'wpppeditorfix' );
		
	}
}

function wpprojects_portfolio_admin_styles() {
	
	global $post;
	
	$continue = "False";
	
	// don't include the media upload script if we are not on a portfolio edit page,
	if (!empty($post)) {
		if (strtolower($post->post_type) == "wpprojects_portfolio") {
			$continue = "True";
		}
	}

}

// check and display any plugin messages
function wpprojects_portfolio_display_update_alert() {
	
	// if the current user has no ability to manage options then don't bother showing them the transient message
    if (!current_user_can('manage_options')) {
			//wp_die( __('Your user account does not have sufficient privileges to use WP Projects Portfolio Plugin.') );
	 }
	
	if ( ( ! empty($message) ) && ( $message != 'empty' ) ) {
		
		echo '	<div class="wpprojects_portfolio_message">';
		echo '		<div class="errrror">	<p>' . $message . '</p></div>';
		echo '	</div>';
		
		// now that we've displayed the alert, clear it out

	}
}

// remove the Porfolio Type tag sidebar widget from the Portfolio edit screen as the Portfolio Type dropdown manages this
// also remove author dropdown list as this really doesn't apply to Portfolios
function remove_wpprojects_portfolio_post_custom_fields() {
	remove_meta_box( 'tagsdiv-wpprojects_portfolio_type' , 'wpprojects_portfolio' , 'side' );
	remove_meta_box( 'authordiv' , 'wpprojects_portfolio' , 'content' );
}

/* Register the WP Projects Portfolio columns to display in the Portfolio Admin listing */
function add_new_wpprojects_portfolio_columns($columns) {
	// note: columns in the listing are ordered in line with where they are created below
	unset($columns['author']);
	unset($columns['date']);
	$columns['title'] = _x('Project', 'column name');
	$columns['_imageurl'] = _x( 'Screenshot', 'column name' );
	$columns['_wpp_passcode'] = _x( 'CR-Status', 'column name' );
	$columns['_clientname'] = _x( 'Client Name', 'column name' );
	$columns['_clientemail'] = _x( 'Client Email', 'column name' );
	$columns['_wpprojects_portfolio_type'] = _x( 'Category', 'column name' );
	//$new_columns['_sortorder'] = _x( 'Sort Order', 'column name' );
	$columns['date'] = _x('Date', 'column name');

	return $columns;
	
}


/* Define the data retrieval arguments for the Portfolio list columns */
function manage_wpprojects_portfolio_columns($column_name, $id) {
	
	global $wpdb;
	$strcont ='';
	
	switch ($column_name) {
	case '_sortorder':
		echo get_post_meta( $id , '_sortorder' , true );
		break;
	case '_imageurl':
		$wppp_imgurl = get_post_meta( $id , '_imageurl' , true );
		if(!empty($wppp_imgurl)):
			$strcont ='<img src="'.$wppp_imgurl.'" border="0" width="100" height="90"></br>';
		endif;
		  echo $strcont .= '<a href="'.get_post_meta( $id , '_siteurl' , true ).'" target="_blank">'.get_post_meta( $id , '_siteurl' , true).'</a>';

		break;
	
	case '_clientname':
		// Get the name of the client for whom the development was performed
		echo get_post_meta( $id , '_clientname' , true );
		break;
		
	case '_clientemail':
		// Get the name of the client for whom the development was performed
		echo get_post_meta( $id , '_clientemail' , true );
		break;
		
	case '_technical_details':
		// Get the technical details
		echo get_post_meta( $id , '_technical_details' , true );
		break;
		
	case '_wpp_passcode':
	// Get the URL to the actual website
	$crpasscodeval = get_post_meta( $id , '_wpp_passcode' , true );
	$crecval = get_post_meta( $id , '_clientrecommendation' , true );
	
	if($crpasscodeval !=''):
	$wppcont_strcont = "<span class='psuc-msg'>Request sent</span>";
	else:
	$wppcont_strcont = "<span class='perror-msg'>No Request</span>";
	endif;
	
	if(!empty($crpasscodeval) && !empty($crecval)):
	$crdisp_sts = get_post_meta( $id , '_wpp_crstatus' , true );
	$wppcont_strcont = "<span class='prec-msg'>CR-Received<br>";
	$wppcont_strcont .= "<span class='";
	if($crdisp_sts == 'on') { 
	$wppcont_strcont .= "prsent";
	} else {
	$wppcont_strcont .= "perror-msg";
	}
	$wppcont_strcont .= "' align='center' style='margin:0px 0px 0px 20px;'>";
	if($crdisp_sts == 'on') { 
	$wppcont_strcont .= "(ON)";
	} else {
	$wppcont_strcont .= "(OFF)";
	}
	$wppcont_strcont .= "</span></span>";
	endif;
	echo $wppcont_strcont;
	
	break;
		
	case '_wpprojects_portfolio_type':
		$wpp_terms = wp_get_object_terms($id, 'wpprojects_portfolio_type');
		if(!empty($wpp_terms)){
		if(!is_wp_error( $wpp_terms )){
		$term_data	= '';
		foreach($wpp_terms as $term){
		$term_data .= '<strong>'.$term->name.'</strong> ,'; 
		
		}
		echo $wpp_termdata = substr($term_data, 0, strlen($term_data)-1);
		}
		}
		
		break;
	
	default:
		break;
	} // end switch
	
}

// hide the Post Tags and Portfolio Types Quick Edit fields on the WP Projects Portfolio listing
function wpprojects_portfolio_quickedit() {
	
	global $post;
	
	if ( is_object($post) ) {
	    if ( $post->post_type == 'wpprojects_portfolio' ) {
			echo '<style type="text/css">';
			echo '	.inline-edit-tags {display: none !important;}';
			echo '</style>';
		}
	}
	
}


//removes view from portfolio list
function remove_wpprojects_portfolio_quick_edit( $actions ) {
	
	global $post;
	
    if( $post->post_type == 'wpprojects_portfolio' ) {
  		//unset($actions['inline hide-if-no-js']);
		//unset($actions['edit']);
	}
	
    return $actions;
	
}


function wpprojects_portfolio_custom_edit( $actions, $post )
{
	$actions['customedit'] = '';
	$clientrec_val = get_post_meta($post->ID, '_clientrecommendation', true);

	
	if ( $post->post_type == 'wpprojects_portfolio' )
	{
		if($clientrec_val == ''):
		//Adding a custom link and passing the post id with it
		$actions['customedit'] .= '<a href=\''.admin_url('edit.php?post_type=wpprojects_portfolio&post='.$post->ID).'&wppaction=1\' class="crecomm"><strong style="color:#0033CC;">Request Client Recommendation</strong></a>';
		endif;
	}
return $actions;
}



//******* PORTFOLIO EDIT SCREEN CODE START  *******//

function save_wpprojects_portfolio_meta($post_id) {
	
	$postid = wp_is_post_revision( $post_id );
	
	if ( $postid == false ) {
		
		// if the save was initiated by an autosave or a quick edit, exit out as the Portfolio fields being updated here may get over written or hang the save
		if (!isset($_POST['autosave_quickedit_check'])) {
			return $post_id;
		}
		
		// verify this call is the result of a POST
		if ( empty($_POST) ) {
			return $post_id;
		}
	 
		// if the user isn't saving a portfolio
		if (strtolower($_POST['post_type']) != "wpprojects_portfolio") {
			return $post_id;
		}
		
		// verify this came from our screen and with proper authorization, because save_post can be triggered at other times
		if ( !check_admin_referer('wpprojects_portfolio_edit','wppportfoliometanonce') ) {
			return $post_id;
		}
	 
		// Is the user allowed to edit the post or page?
		if ( !current_user_can( 'edit_post', $post_id )) {
			return $post_id;
		}
		
		// OK, we're authenticated: we need to find and save the data
		
		$portfolio_meta['_siteurl'] = $_POST['_siteurl'];
		$portfolio_meta['_imageurl'] = $_POST['_imageurl'];
		$portfolio_meta['_projectproblem'] = $_POST['_projectproblem'];
		$portfolio_meta['_projectsolution'] = $_POST['_projectsolution'];
		$portfolio_meta['_clientname'] = $_POST['_clientname'];
		$portfolio_meta['_clientemail'] = $_POST['_clientemail'];
		$portfolio_meta['_clientphoto'] = $_POST['_clientphoto'];
		$portfolio_meta['_wpp_passcode'] = $_POST['_wpp_passcode'];
		$portfolio_meta['_technical_details'] = $_POST['_technical_details'];
		$portfolio_meta['_clientrecommendation'] = $_POST['_clientrecommendation'];
		if (!empty($_POST['_sortorder'])) {
			$portfolio_meta['_sortorder'] = $_POST['_sortorder'];
		} else {
			$portfolio_meta['_sortorder'] = -1*($post_id);
		}
		
	 
		// Add values of $portfolio_meta as custom fields
	 
		foreach ($portfolio_meta as $key => $value) { // Cycle through the $portfolio_meta array!
			$value = implode(',', (array)$value); // If $value is an array, make it a CSV (unlikely)
			if (get_post_meta($post_id, $key, false)) { // If the custom field already has a value
				update_post_meta($post_id, $key, $value);
			} else { // If the custom field doesn't have a value
				add_post_meta($post_id, $key, $value);
			}
			if (!$value) delete_post_meta($post_id, $key); // Delete if blank
		}
	}
}

//*************************************************//
//******** PORTFOLIO EDIT SCREEN CODE END  ********//
//*************************************************//


// Define the Portfolio edit form custom fields
function wpprojects_portfolio_edit_init() {
	
	global $post;
 
	// Noncename needed to verify where the data originated
	wp_nonce_field( 'wpprojects_portfolio_edit', 'wppportfoliometanonce' );
	
	// Gather any existing custom data for the Portfolio
	$datecreate = get_post_meta($post->ID, '_createdate', true);
	$siteurl = get_post_meta($post->ID, '_siteurl', true);
	$imageurl = get_post_meta($post->ID, '_imageurl', true);
	$projectproblem = get_post_meta($post->ID, '_projectproblem', true);
	$projectsolution = get_post_meta($post->ID, '_projectsolution', true);
	$client_name = get_post_meta($post->ID, '_clientname', true);
	$clientemail = get_post_meta($post->ID, '_clientemail', true);
	$client_photo = get_post_meta($post->ID, '_clientphoto', true);
	$client_passcode = get_post_meta($post->ID , "_wpp_passcode", true);
	$technical_details = get_post_meta($post->ID, '_technical_details', true);
	$clientrecommendation = get_post_meta($post->ID, '_clientrecommendation', true);
	$wpp_portfolio_type = get_post_meta($post->ID, '_wpprojects_portfolio_type', true);
	$sortorder = get_post_meta($post->ID, '_sortorder', true);
	if ($sortorder=="") $sortorder = "-" . $post->ID;
 
	// Gather the list of WP Projects Portfolio Types
	$portfolio_type_list = get_terms('wpprojects_portfolio_type', 'hide_empty=0'); 
 
 	// Build out the form fields
	
	echo '<p><label for="_siteurl">Project URL : </label>';
	echo '<input type="text" id="_siteurl" name="_siteurl" value="' . $siteurl . '" class="widefat" /></p>';
	
	echo '<p><label for="_imageurl">Project Screenshot URL: </label>';
	echo '<input id="upload_portfolio_image_button" class="upload_image_button" type="button" value="Upload Image" /><br />';
	echo '<input type="text" id="_imageurl" name="_imageurl" value="' . $imageurl . '" class="widefat shortbottom" /><br />';
	
	if(!empty($imageurl)):
	
	echo '<p><a href="'.$imageurl.'" rel="" target="_blank"><img src="'.$imageurl.'" border="0" width="120" height="120"></a></p>';
	
	endif;	

	echo '<p><label for="_portfolio_project_problem"><b>&nbsp;Problem :</b></label></br>';

	echo '<textarea id="_projectproblem" name="_projectproblem" rows="5" cols="90">'.$projectproblem.'</textarea>';
	

	echo '<p><label for="_portfolio_project_problem"><b>&nbsp;Solution :</b></label></br>';

	echo '<textarea id="_projectsolution" name="_projectsolution" rows="5" cols="90">'.$projectsolution.'</textarea>';
   
    echo '<p><label for="_clientname">Client Name : </label>';
	echo '<input type="text" id="_clientname" name="_clientname" value="' . $client_name . '" class="widefat" /></p>';
	
	echo '<p><label for="_clientname">Client Email : </label>';
	echo '<input type="text" id="_clientemail" name="_clientemail" value="' . $clientemail . '" class="widefat" /></p>';
	
	echo '<p><label for="_imageurl">Client Photo: </label>';
	echo '<input id="upload_cphoto_image_button" class="upload_image_button" type="button" value="Upload Image" /><br />';
	echo '<input type="text" id="_clientphoto" name="_clientphoto" value="' . $client_photo . '" class="widefat shortbottom" /><br />';
	
	if(!empty($client_photo)):
	echo '<p><a href="'.$client_photo.'" rel="" target="_blank"><img src="'.$client_photo.'" border="0" width="48" height="48"></a></p>';
	endif;	
	
	echo '<p><label for="_sortorder">Client Passcode: </label>';
	echo '<input type="text" id="_wpp_passcode" name="_wpp_passcode" value="' . $client_passcode . '" class="code" />';
	
    echo '<p><label for="_technical_details">Additional Details ( Phone no & address ): </label>';
	echo '<input type="text" id="_technical_details" name="_technical_details" value="' . $technical_details . '" class="widefat" /></p>';
	
	echo '<p><label for="_portfolio_client_recommendation"><b>&nbsp;Recommendation :</b></label></br>';

	echo '<textarea id="_clientrecommendation" name="_clientrecommendation" rows="5" cols="90">'.$clientrecommendation.'</textarea>';
	
	echo '<p><label for="_portfolio_client_recommendation"><b>&nbsp;Display in the website:</b></label>';

	echo '<div class="switch-ajax" id="'.$post->ID.'"></div>
	<div id="crdisp"></div>
	<div class="clear"></div>';
	
  	$crsts_val = get_post_meta($post->ID , "_wpp_crstatus", true);
	if($crsts_val):
		$switch_status = $crsts_val;
	else:
		$switch_status = "off";
	endif;
	
	echo  '<script type="text/javascript">
    jQuery("#'.$post->ID.'").iphoneSwitch("'.$switch_status.'", 
     function() {
	  var postidval = jQuery("#'.$post->ID.'").attr("id");
	 	clientrec_disp_status(postidval,"on");
      },
      function() {
	   var postidval = jQuery("#'.$post->ID.'").attr("id");
	  clientrec_disp_status(postidval,"off");
      },
      {
        switch_on_container_path: "'.plugins_url( 'images/switch_container_off.png' , __FILE__ ).'"
      },"'.plugin_dir_url(__FILE__).'");
  </script>';
	
	
    echo '<p><label for="_sortorder">Sort Order: </label>';
	echo '<input type="text" id="_sortorder" name="_sortorder" value="" class="code" />';
	echo '<input type="hidden" name="autosave_quickedit_check" value="true" /></p>';

}

/* Add the Portfolio custom fields (called as an argument of the custom post type registration) */
function add_wpprojects_portfolio_metaboxes() {
	add_meta_box('wpprojects_portfolio_edit_init', 'Website Information', 'wpprojects_portfolio_edit_init', 'wpprojects_portfolio', 'normal', 'high');
}


// Manage Portfolio Types taxonomy counts
function wpprojects_portfolio_type_taxonomy_count_rec($post_id) {
	
	global $wpdb;
	
	$postid = wp_is_post_revision( $post_id );
	
	if ( $postid == false ) {
		$postid = $post_id;
	}
	
	$wpdb->query(
		"
		DELETE	FROM $wpdb->term_relationships
		WHERE	object_id = '".$postid."'
		AND		EXISTS (
				SELECT	1
				FROM	$wpdb->term_taxonomy stt
				WHERE	stt.term_taxonomy_id = $wpdb->term_relationships.term_taxonomy_id
				AND		stt.taxonomy = 'wpprojects_portfolio_type')
		"
	);
	
	$wpdb->query(
		"
		INSERT INTO $wpdb->term_relationships (object_id, term_taxonomy_id, term_order)
		SELECT	sp.id 'object_id',
			(	SELECT	ssstt.term_taxonomy_id
				FROM	$wpdb->postmeta spm INNER JOIN
						$wpdb->terms ssst ON spm.meta_value = ssst.slug INNER JOIN
						$wpdb->term_taxonomy ssstt ON ssst.term_id = ssstt.term_id AND ssstt.taxonomy = 'wpprojects_portfolio_type'
				WHERE	spm.meta_key = 'wpprojects_portfolio_type'
				AND		spm.post_id = sp.id) 'term_taxonomy_id',
				0 'term_order'
		FROM	$wpdb->posts sp
		WHERE	sp.id = '".$postid."'
		AND		sp.post_type = 'wpprojects_portfolio'
		AND		EXISTS (
				SELECT	1
				FROM	$wpdb->postmeta sspm INNER JOIN
						$wpdb->terms sssst ON sspm.meta_value = sssst.slug INNER JOIN
						$wpdb->term_taxonomy sssstt ON sssst.term_id = sssstt.term_id AND sssstt.taxonomy = 'wpprojects_portfolio_type'
				WHERE	sspm.meta_key = '_wpprojects_portfolio_type'
				AND		sspm.post_id = sp.id)
		AND		NOT EXISTS (
				SELECT	1
				FROM	$wpdb->term_relationships str INNER JOIN
						$wpdb->term_taxonomy stt ON str.term_taxonomy_id = stt.term_taxonomy_id AND stt.taxonomy = 'wpprojects_portfolio_type' INNER JOIN
						$wpdb->terms st ON stt.term_id = st.term_id
				WHERE	str.object_id = sp.id)
		"
	);
	
	// update the WP Projects Portfolio (Post) counts on the Portfolio Types
	$wpdb->query(
		"
		UPDATE	$wpdb->term_taxonomy
		SET		count = (SELECT count(ssp.id) FROM $wpdb->posts ssp INNER JOIN $wpdb->term_relationships str ON ssp.id = str.object_id WHERE ssp.post_type = 'wpprojects_portfolio' AND ssp.post_status = 'publish' AND str.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id)
		WHERE	taxonomy = 'wpprojects_portfolio_type'
		"
	);
	
}



/* Define Portfolio Plugin De-activation process */
function wpprojects_portfolio_remove() {
	
	$deletedata = "false";
	// if the delete data option is set to delete, then delete the Portfolio records and Portfolio Type taxonomy records
	if ( $deletedata == "true" ) {
		
		// Gather the Portfolios
		$portfolios_to_delete = new WP_Query(array('post_type' => 'wpprojects_portfolio', 'post_status' => 'any', 'orderby' => 'ID', 'order' => 'DESC'));
		
		// Loop through and delete the Portfolios
		if ( $portfolios_to_delete->have_posts() ) {
			while ( $portfolios_to_delete->have_posts() ) : $portfolios_to_delete->the_post();
				wp_delete_post( get_the_id(), true );
			endwhile;
		}

		// Gather the list of Portfolio Types
		$portfolio_type_list = get_terms('wpprojects_portfolio_type', 'hide_empty=0');
		
		// Loop thru the types and delete each one, the last will clear the taxonomy
		foreach ($portfolio_type_list as $portfolio_item) {
			wp_delete_term( $portfolio_item->term_id, 'wpprojects_portfolio_type' );
		}
		
		// Gather the list of Portfolio Tags
		$portfolio_type_list = get_terms('wpprojects_portfolio_tag', 'hide_empty=0');
		
		// Loop thru the tags and delete each one
		foreach ($portfolio_type_list as $portfolio_item) {
			wp_delete_term( $portfolio_item->term_id, 'wpprojects_portfolio_tag' );
		}
		
	}

}



function wpprojects_portfolio_set_admin_css() {
	$file = plugins_url('css/wpproj_admin.css', __FILE__);
	wp_register_style('wpproj_admin', $file);
	wp_enqueue_style('wpproj_admin');
}

function wpprojects_portfolio_post_css() {
	
	global $post;
	
	// don't include the Portfolio Post CSS file if we aren't on the Portfolio Post edit screen
	if (strtolower($post->post_type) == "wpprojects_portfolio") {
		$file = plugins_url('css/post_wpportfolio.css', __FILE__);
		wp_register_style('post_wpportfolio', $file);
		wp_enqueue_style('post_wpportfolio');
	}
}


if(!function_exists('wpprojects_create_portfolio')) {
function wpprojects_create_portfolio() {
		wpprojects_portfolio_googleapis_jquery();
		add_wpprojects_portfolio_css();
		deregister_wpprojec_plugin_styles();
		add_action('wp_print_scripts', 'deregister_wpprojec_plugin_scripts');
}
}

function add_wpprojects_portfolio_css() {
		$css = plugins_url('css/wppcr_main.css', __FILE__);
		wp_register_style('wppcr_main', $css);
		wp_enqueue_style('wppcr_main');
	
}


function deregister_wpprojec_plugin_styles() {
	wp_deregister_style('thickbox');
}

function deregister_wpprojec_plugin_scripts() {
	wp_deregister_script('thickbox');
}


if(!function_exists('wpprojects_create_single_portfolio')) {
function wpprojects_create_single_portfolio() {
	$js = plugins_url('css/post_single_wpproject.css', __FILE__);
	$js = wpprojects_portfolio_clear($js);
	wp_register_style('wpprojects_single_portfolio', $js);
	wp_enqueue_style('wpprojects_single_portfolio');
}
}



/* define the Portfolio ShortCode and set defaults for available arguments */
function wpprojects_portfolio_loop($atts, $content = null) {
	
	if ( is_admin() ) { return null; }
	
	global $for;
	global $portfolio_types;
	global $portfolio_output;
	global $num_per_page;
	global $limit_portfolios_returned;
	global $display_the_credit;
	
	wpprojects_clear_global_entries();
	
	$max_nav_spread = '';
	$portfolio_type = '';
	
	extract( shortcode_atts( array(
      'max_nav_spread' => 5,
	  'portfolio_type' => '',
	  'thickbox' => '',
	  'id' => '',
	  'per_page' => '',
	  'limit' => '',
      'credit' =>''), $atts ) );
	
	$for = $max_nav_spread;
	$portfolio_types = $portfolio_type;
	
	if ( !empty($per_page) && is_numeric($per_page) ) {
		$num_per_page = $per_page;
	}
	
	if ( !empty($id) ) {
		$portfolio_output = '<div id="' . $id . '">';
	}
	
	if ( !empty($content) ) {
		$portfolio_output .= '<div class="wpprojects_portfolio_page_content">' . $content . '</div>';
	}
	
	if ( !empty($limit) && is_numeric($limit) ) {
		$limit_portfolios_returned = $limit;
	}
	
	include('loop-wppcr-portfolio.php');
	
	if ( !empty($id) ) {
		$portfolio_output .= '</div>';
	}
	
	return $portfolio_output;
	
}


/* clear out the shortcode values otherwise they get re-used if more than one shortcode is used per page */
function wpprojects_clear_global_entries() {
	
	global $wp_query;
	global $for;
	global $portfolio_types;
	global $click_behavior;
	global $portfolio_output;
	global $num_per_page;
	global $limit_portfolios_returned;
	global $display_the_credit;
	
	$wp_query->query_vars['portfoliotype'] = '';
	$for = '';
	$portfolio_types = '';
	$click_behavior = '';
	$portfolio_output = '';
	$num_per_page = '';
	$limit_portfolios_returned = '';
	$display_the_credit = '';
	
}


// Passcode validate FUNCTION
function cr_validate_passcode(){
$wpp_passcode = trim($_POST['wpppcode']);
$wpp_postid = $_POST['wpppid'];
if(!empty($wpp_passcode) && !empty($wpp_postid)):
$dbpasscode = get_post_meta($wpp_postid , "_wpp_passcode", true);
$dbpassvalue = ($dbpasscode == "") ? 0 : $dbpasscode;
if($wpp_passcode  == $dbpassvalue) {
$message = "spcode";
} else {
$message = "fpcode";
}
else:
$message = "empcode";
endif;
echo $message;
die();
}

function author_wpinfo() {

   	echo "\n<!-- WP Projects Portfolio Developed by Sundar Rajan of http://wpdeveloper.com !-->";
   
	echo '<script type="text/javascript" src="'.WP_PLUGIN_URL.'/wp-projects-portfolio/scripts/cr_script.js"></script>';
	echo '<script type="text/javascript" src="'.WP_PLUGIN_URL.'/wp-projects-portfolio/scripts/on-off-script.js"></script>';

  	echo "<!-- WP Projects Portfolio Script Ends-->\n";


}


function crecommend_script_enqueuer() {
  
   wp_register_script( "crecommend_script", WP_PLUGIN_URL.'/wp-projects-portfolio/scripts/cr_script.js', array('jquery') );
   wp_localize_script( 'crecommend_script', 'crAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));        
   wp_enqueue_script( 'jquery' );
   wp_enqueue_script( 'crecommend_script' );  
   wp_register_script( "onoff_script",WP_PLUGIN_URL.'/wp-projects-portfolio/scripts/on-off-script.js', array('jquery') );
   wp_enqueue_script( 'onoff_script' );
}

add_action('wp_head', 'author_wpinfo');
add_action( 'init', 'crecommend_script_enqueuer');

add_action( 'wp_ajax_crpcode_nonce', 'cr_validate_passcode');
add_action( 'wp_ajax_nopriv_crpcode_nonce', 'cr_validate_passcode');

function clientrec_contentsubmit(){
$wpp_crcontent = trim($_POST['crboxcontent']);
$crpostid = $_POST['crpid'];
$cl_name = get_post_meta($crpostid, "_clientname", true);
$cl_email = get_post_meta($crpostid, "_clientemail", true);
$crpost_title = get_the_title($crpostid);
$wpp_peditlink = admin_url()."/post.php?post=".$crpostid."&action=edit";
$crplink = get_permalink($crpostid);
$wp_adminemail =  get_option('admin_email');

$crec_subject = "New Client's Recommendation Received From -".$cl_name."";
	
$crec_mail_headers  = "MIME-Version: 1.0" . "\r\n";
$crec_mail_headers .= "Content-type: text/html; charset=".get_bloginfo('charset')."" . "\r\n";
$crec_mail_headers .= 'From:'.$cl_name."<".$cl_email. ">\r\n";

$wpcr_recmessage  = get_option('wpcr_recmessage');

if($wpcr_recmessage != '') {

$crec_mail_content = get_option('wpcr_recmessage')."<br/><br/>";
$crec_mail_content .= "Project Title<strong>".$crpost_title."</strong><br/></br>";
$crec_mail_content .= "Recommendation<strong>".stripslashes($wpp_crcontent)."</strong><br/></br>";
$crec_mail_content .= "Please click on the following link & approve this recommendation.</br><br/>".$wpp_peditlink."<br/><br/>
					  Thanks,";	

} else {
$crec_mail_content = 'Hello Admin,<br/><br/>New recommendation received for&nbsp;<strong>'.$crpost_title.'</strong>&nbsp;project.<br/><br/>
					  '.$crplink.'<br/><br/><strong>Recommendation:</strong><br/><br/>'.stripslashes($wpp_crcontent).'<br/><br/>
					  Please click on the following link & approve this recommendation.</br><br/>'.$wpp_peditlink.'<br/><br/>
					  Thanks,';	
}

	
if(!empty($wpp_crcontent) && !empty($crpostid)):
$dbcrcontent = update_post_meta($crpostid , "_clientrecommendation", $wpp_crcontent);
if($dbcrcontent) {
	wp_mail($wp_adminemail, $crec_subject, $crec_mail_content, $crec_mail_headers);
	$message = "scrcont";
} else {
$message = "fcrcont";
}
else:
$message = "empcrcont";
endif;
echo $message;
die();
}


//Now to get the 'test' argument, and triggering a function based on it...

//Pass code generator

function passcodegen($length=10)
{
	$passcode = '';
	list($usec, $sec) = explode(' ', microtime());
	mt_srand((float) $sec + ((float) $usec * 100000));
	
   	$inputs = array_merge(range('z','a'),range(0,9),range('A','Z'));

   	for($i=0; $i<$length; $i++)
	{
   	    $passcode .= $inputs{mt_rand(0,61)};
	}
	return $passcode;
}

function wpp_clientrec_sent()
{
	global $current_screen;
	$ptrashed ="";
	$puntrashed = "";
	
	if(isset($_REQUEST['trashed'])):
		$ptrashed = $_REQUEST['trashed'];
	endif;
	
	if(isset($_REQUEST['untrashed'])):
		$puntrashed = $_REQUEST['untrashed'];
	endif;
	
		if ( 'wpprojects_portfolio' == $current_screen->post_type && !$ptrashed && !$ptrashed ){
			_e('<div class="updated"><p><strong>Client recommendation request has been sent!</strong></p></div>');
		}
}

//CR-action starts//

if(isset($_GET['wppaction']) && isset($_GET['post']))
{
		$currentwpp_id = $_REQUEST['post'];
		
		if( $_REQUEST['wppaction'] == 1 && !empty($_GET['post'])):
		
		$wpp_pcode = passcodegen(); //random passcode value		
		$prv_pcode = get_post_meta($currentwpp_id, '_wpp_passcode', true); //existing passcode value
		
		if ($prv_pcode !='') { // If the custom field already has a value
			$extpasscode = get_post_meta($currentwpp_id, '_wpp_passcode', true);
			update_post_meta($currentwpp_id, '_wpp_passcode', $extpasscode);
		} else { // If the custom field doesn't have a value
			add_post_meta($currentwpp_id, '_wpp_passcode', $wpp_pcode);
			$sendpasscode = $wpp_pcode;
		}
			
			add_action( 'plugins_loaded', 'cremail_message' );
			add_action('admin_notices', 'wpp_clientrec_sent');    
			
		endif;
}

function cremail_message()
{
		global $wpdb;
		
		$currentwpp_id = $_REQUEST['post'];
		
		$rewrite_slug = get_option('wprewrite_slug');
				
		$ctermsql = "SELECT	st.slug FROM	$wpdb->posts sp, $wpdb->term_relationships str INNER JOIN
		$wpdb->term_taxonomy stt ON str.term_taxonomy_id = stt.term_taxonomy_id AND stt.taxonomy = 'wpprojects_portfolio_type' INNER JOIN
		$wpdb->terms st ON stt.term_id = st.term_id
		WHERE	str.object_id = sp.id AND sp.ID ='".$_REQUEST['post']."' limit 0,1";
		
		$cterm_data = $wpdb->get_var($ctermsql);
		
		if($cterm_data != ''):
		
		$catslug = $cterm_data;
		
		else:
		
		$catslug = get_option('wprewrite_slug');
		
		endif;
		
		$wpp_clientname = get_post_meta($currentwpp_id, '_clientname', true);
		
		$wpp_clientemail = get_post_meta($currentwpp_id, '_clientemail', true);
		
		$sendpasscode = get_post_meta($currentwpp_id, '_wpp_passcode', true);
		
		$wpp_postplink = site_url()."/$rewrite_slug/$catslug/".basename(get_permalink($currentwpp_id));
		
		$crmail_subject = "Client Recommendation Request -".get_option('blogname');
		
		$crmail_headers  = "MIME-Version: 1.0" . "\r\n";
		$crmail_headers .= "Content-type: text/html; charset=".get_bloginfo('charset')."" . "\r\n";
		$crmail_headers .= 'From: '.get_option('admin_email')."\r\n";
		
		$wprec_message = get_option('wprec_message');
		
		if($wprec_message != '') {
		$crmail_content  = 'Hello '.$wpp_clientname.',<br/><br/>';
		$crmail_content .= $wprec_message;
		$crmail_content .= 'Submit Testimonial by clicking on the link'.$wpp_postplink.'</br></br>';
		$crmail_content .=  '<strong>Use the  Password:&nbsp;</strong><strong style="color:#009900;">'.$sendpasscode .'</strong><br/><br/>Thanks,';
		
		} else {
		
		$crmail_content = 'Hello '.$wpp_clientname.',<br/><br/>Thanks for offering me the project & it was nice working with you on this project as per your request.<br/><br/>
						   So I would request you to provide testimonial on my portfolio for the same by clicking on the link below</br><br/>'.$wpp_postplink.'<br/><br/>
						   <strong>Use the  Password:&nbsp;</strong><strong style="color:#009900;">'.$sendpasscode .'</strong>&nbsp;to provide the testimonial.<br/><br/>
						   Thanks,';
		}
		
		wp_mail($wpp_clientemail, $crmail_subject, $crmail_content, $crmail_headers);
   
}


add_action( 'wp_ajax_crcontent_nonce', 'clientrec_contentsubmit');
add_action( 'wp_ajax_nopriv_crcontent_nonce', 'clientrec_contentsubmit');
// test for whether a hook should be applied or not
function wpprojects_portfolio_apply_hook( $query, $hook ) {
	return (
		// We have query vars
		property_exists( $query, 'query_vars' ) &&
		( array_key_exists( 'post_type', $query->query_vars ) && $query->query_vars['post_type'] == 'wpprojects_portfolio' )
	);
}

// add "portfoliotype" into the recognized set of query variables
function wpprojects_portfolio_queryvars( $qvars ) {
	$qvars[] = 'portfoliotype';
	return $qvars;
}

// status mode - FUNCTION
function cr_non_dstatus(){
$crstatus = $_POST['crsts'];
$cr_postid = $_POST['crpid'];
if(!empty($crstatus) && !empty($cr_postid)):
$crsts_update = update_post_meta($cr_postid , "_wpp_crstatus", $crstatus);
if($crsts_update) {
$message = "crstss";
} else {
$message = "crstsf";
}
endif;
echo $message;
die();
}

// augment the JOIN if a Portfolio Type is part of the search
function wpprojects_portfolio_search_join( $join, $query ) {
	
	global $wpdb, $wp_query;
	
	// if the portfolio type has been defined in the search vars
	if ( wpprojects_portfolio_apply_hook( $query, 'join' ) ) {
		
		// add the join to the wp_postmeta table for meta records that are of a Portfolio Type
		$join .=  " LEFT OUTER JOIN " . $wpdb->prefix . "postmeta AS port ON (" . $wpdb->posts . ".ID = port.post_id AND port.meta_key = '_wpprojects_portfolio_type') ";
		
	}
	
	return $join;
}

// augment the WHERE clause if a Portfolio Type is part of the search
function wpprojects_portfolio_search_where( $where, $query ) {
	
	global $wp_query;
	
	if ( is_admin() ) { return $where; }

	// if the portfolio type has been defined in the search vars
	if ( wpprojects_portfolio_apply_hook( $query, 'where' ) ) {
		
		// clear out our portfolio type buckets
		$IN = "";
		$OUT = "";
		
		$types = get_query_var('portfoliotype');
		
		// place the portfolio types into an array so that it is easier to process them
		$ptypes = explode(",",$types);
		
		// loop through the portfolio array
		foreach ($ptypes as $value) {
			
			// if the portfolio type is not lead by a minus sign then add it to the IN bucket
			if (substr($value, 0, 1) != '-') {
				if ( !empty($IN) ) $IN .= ",";
				$IN .= $value;
			} else { // otherwise, add it to the OUT bucket
				if ( !empty($OUT) ) $OUT .= ",";
				$OUT .= substr($value, 1);
			}
		}
		
		// if some of the portfolio types were flagged for inclusion then add an IN() clause
		if ( !empty($IN) ) {
			if (!empty($where)) $where .= " AND ";
			$where .= " port.meta_value IN ('" . str_replace(',', "','", $IN) . "')";
		}
		
		// if some of the portfolio types were flagged for exclusion then add a NOT IN() clause
		if ( !empty($OUT) ) {
			if (!empty($where)) $where .= " AND ";
			$where .= " port.meta_value NOT IN ('" . str_replace(',', "','", $OUT) . "')";
		}
		
	}
	
	return $where;
}

add_action( 'wp_ajax_crsts_nonce', 'cr_non_dstatus');
add_action( 'wp_ajax_nopriv_crsts_nonce', 'cr_non_dstatus');
// extend standard WordPress tag cloud to include Portfolio tags
function wpprojects_portfolio_tag_cloud_inc($args = array()) {
	
	$include = "False";
	
	if ($include == 'True') {
		
		if (is_array($args['taxonomy'])) {
			array_push($args['taxonomy'],"wpprojects_portfolio_tag");
		} else {
			$args['taxonomy'] = array($args['taxonomy'],'wpprojects_portfolio_tag');
		}
		
	}
	
	return $args;
	
}

if ( ! is_admin() ) {
	add_filter('widget_tag_cloud_args', 'wpprojects_portfolio_tag_cloud_inc', 90);
}


function wp_admin_bar_crtotalcount_item() {
global $wpdb, $wp_admin_bar;

if ( ! is_super_admin() || ! is_admin_bar_showing() )
	  return;


$cr_count = $wpdb->get_var( "SELECT COUNT( * ) AS count
FROM {$wpdb->postmeta} pm
LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
WHERE (
pm.meta_key = '_clientrecommendation'
AND pm.meta_value != ''
)
AND p.post_status = 'publish'
AND p.post_type = 'wpprojects_portfolio' ");

$wp_admin_bar->add_menu(array(
'id' => 'wp-admin-bar-new-item',
'title' => __('<strong style="color:#FFFF8C;">Total CR-Received (<span style="font-weight:bold;color:#FFFF8C;padding:2px 2px 2px 2px;">'.$cr_count.'</span>)</strong>'),
'href' => admin_url().'edit.php?post_type="wpprojects_portfolio&sfname=_clientrecommendation"'
));
}
add_action('wp_before_admin_bar_render', 'wp_admin_bar_crtotalcount_item');
// if we are on a post or a page with the wp-projects-portfolio shortcode in the content then carry off certain actions
function wpprojects_shortcode() {
	
	$cont = "";
	
	global $post;
	
	if ( is_single() || is_page() ) {
		$cont = getWPProjectsPageContent($post->ID);
	}
	
	// if the wp-projects-portfolio shortcode is within the content take the actions indicated
	if ( strpos($cont, "wp-projects-portfolio") > 0 ) {
		add_action('wp_print_styles', 'wpprojects_create_portfolio');
	} else {
		if ($_SERVER["REMOTE_ADDR"] == '127.0.0.1') { // asterisk - when running locally this was needed to avert a non-ending re-direct
			remove_filter('template_redirect', 'redirect_canonical');
		}
//		add_action('template_redirect', 'use_single_portfolio_page_template');
		add_filter('template_include', 'wpprojects_portfolio_template_include');
//		add_filter('template_include', 'wpprojects_portfolio_tag_template_include');
		add_action('wp_print_styles', 'wpprojects_create_single_portfolio');
	}
	
}

function wpprojects_portfolio_template_include($incFile) {
	
	if ( get_post_type() == 'wpprojects_portfolio' ) {
		$incFile = wpprojects_portfolio_post_templatefile_include($incFile);
	}
	
	return $incFile;
	
}

if(!function_exists('wpprojects_portfolio_post_templatefile_include')) {
function wpprojects_portfolio_post_templatefile_include($incFile) {
	
	global $wp_query;
	
	if (is_single()) {
		add_action('wp_print_styles', 'wpprojects_create_single_portfolio');
		$file = get_stylesheet_directory() . '/single-wp-projects-portfolio.php';
		if ( ! file_exists($file) ) {
			$file = plugin_dir_path(__FILE__) . 'single-wp-projects-portfolio.php';
		}
		if (file_exists($file)) {
			$incFile = $file;
		}
	} else {
		$wp_query->is_404 = true;
	}
	
	return $incFile;
}
}

if(!function_exists('getWPProjectsPageContent')) {
function getWPProjectsPageContent($pageId) {
	
	if(!is_numeric($pageId)) {
		return;
	}
	
	global $wpdb;
	
	$sql_query = 'SELECT DISTINCT * FROM ' . $wpdb->posts . ' WHERE ' . $wpdb->posts . '.ID=' . $pageId;
	
	$posts = $wpdb->get_results($sql_query);
	
	if(!empty($posts)) {
		
		foreach($posts as $post) {
			
			return nl2br($post->post_content);
			
		}
	}
	
}
}

// smart jquery inclusion
function wpprojects_portfolio_googleapis_jquery() {
		wp_deregister_script('jquery');
		wp_register_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js');
		wp_enqueue_script('jquery');

}


// clear the passed in path up to wp-content as some code and hosting providers don't play nicely with arguments containing http://www
if ( ! function_exists( 'wpprojects_portfolio_clear' ) ) :

function wpprojects_portfolio_clear($url) {
	
	$return = $url;

	$use_full_path = 'True';
	
	if ( $use_full_path != 'True' ) {
		
		$pos = strpos($return, 'wp-content');
		
		if ( ! empty($pos) ) {
			$return = str_replace(substr($return, 0, strpos($return, 'wp-content') - 1), "", $return);
		}
		
	}
	
	return $return;
}

endif;



/************** Project Requirements label display******************************/

// 'Enter property description here' label filter

function customs_tinyMCE_label($content){

	if($content == '') $content = 'ENTER THE PROJECT REQUIREMENTS HERE...';

	return $content;

}



// add custom tinyMCE settings filter

function customs_tinyMCE($settings){

	$settings['setup'] = 'tinyEvent';

	return $settings;

}



// add custom tinyMCE script and custom tinyMCE initialize content

$bozuri = $_SERVER['REQUEST_URI'];

if ( strstr($bozuri, 'post_type=wpprojects_portfolio') )

{ 

	add_filter('tiny_mce_before_init','customs_tinyMCE');

	add_filter('the_editor_content', 'customs_tinyMCE_label');

}



/* Build out the navigation elements for paging through the WP projects Portfolio pages */
function wpprojects_nav_pages($qryloop, $pageurl, $class) {
	
	global $for;
	global $portfolio_output;
	global $navcontrol;
	global $limit_portfolios_returned;
	
	// get total number of pages in the query results
	$pages = $qryloop->max_num_pages;
	$legacy = '';
	$top = "";
	$bottom = "";
	if ($legacy == 'True') {
		$top = " top";
		$bottom = " bottom";
	}
	
	// if the user has set a hard value for the number of portfolios to return in the shortcode
	if ( is_numeric($limit_portfolios_returned) ) {
		if ($limit_portfolios_returned > 0) {
			$pages = 1;
		}
	}
	
	// if there is more than one page of Portfolio query results
	if ($pages > 1) {
		

		if ( ($class == "wpprojects_nav_bottom") && ( !empty($navcontrol) ) ) {
			$portfolio_output .= '<div class="pagination' . $bottom . ' ' . $class . '">' . $navcontrol . '</div>';
			$navcontrol = array();
			return $portfolio_output;
		}
		
		$paged_1 = $pageurl;
		
		if ( strpos($pageurl, "?page_id=") > 0 ) {
			$paged = $pageurl . "&paged=";
			$paged_end = "";
		} else {
//			$paged = $pageurl . "?paged=";
			$paged = $pageurl . "/page/";
			$paged_end = "/";
		}		
		
		// get current page number
		intval(get_query_var('paged')) == 0 ? $curpage=1 : $curpage = intval(get_query_var('paged'));
		
		// determine the starting page number of the nav control
		
		// figure out where to start and end the nav control numbering as well as what arrow elements we need on each end, if any
		$start = $curpage - round(($for/2),0) + 1;
		if ( ($start + $for) > $pages ) { $start = $pages - $for + 1; }
		if ($start < 1) { $start = 1; }
		if ( ($start + $for) > $pages ) { $for = $pages - $start + 1; }
		$before = 0;
		if ($start > 2) {
			$before = 2;
		} elseif ($start > 1) {
			$before = 1;
		}
		$after = $pages - ($start + $for - 1);
		if ($after > 2) {
			$after = 2;
		} elseif ( $after < 0) {
			$after = 0;
		}		
		
		// now build out the navigation page control elements
		$nav = '<ul>';
		if ($before == 1) {
			$nav .= '<li><a href="' . $paged . ($start - 1) . $paged_end . '">&lt;</a></li>';
		} elseif ($before == 2) {
			$nav .= '<li><a href="' . $paged_1 . '">&laquo;</a></li>';
			$nav .= '<li><a href="' . $paged . ($start - 1) . $paged_end . '">&lt;</a></li>';
		}
		for ($i=$start;$i<=($start+$for-1);$i++) {
			if ($i == 1) {
				$pagenav = $paged_1;
			} else {
				$pagenav = $paged . $i . $paged_end;
			}
			if ($curpage!=$i) {
				$nav .= '<li><a href="' . $pagenav . '"';
			} else {
				$nav .= '<li class="selected"><a href="' . $pagenav . '" class="selected"';
			}
			$nav .= '>' . $i . '</a></li>';
		}
		if ($after == 1) {
			$nav .= '<li><a href="' . $paged . ($start + $for) . $paged_end . '">&gt;</a></li>';
		} elseif ($after == 2) {
			$nav .= '<li><a href="' . $paged . ($start + $for) . $paged_end . '">&gt;</a></li>';
			$nav .= '<li><a href="' . $paged . $pages . $paged_end . '">&raquo;</a></li>';
		}
		$nav .= '</ul>';
		
		$portfolio_output .= '<div class="pagination' . $top . ' ' . $class . '">' . $nav . '</div>';
		
		if ($class == "wpprojects_nav_top") {
			$navcontrol = $nav;
		}
		
	}
	
	return $portfolio_output;
}

add_filter( 'parse_query', 'wpprojects_portfolio_search_filter' );
add_action( 'restrict_manage_posts', 'wpprojects_portfolio_search_filter_downlist' );

function wpprojects_portfolio_search_filter( $query )
{
    global $pagenow;
    if ( is_admin() && $pagenow=='edit.php' && isset($_GET['sfname']) && $_GET['sfname'] != '') {
        $query->set('meta_key',$_GET['sfname']);
    if (isset($_GET['sfvalue']) && $_GET['sfvalue'] != '')
	  $query->set('meta_value',$_GET['sfvalue']);
	  $query->set( 'meta_compare', 'LIKE' );
    }
}

function wpprojects_portfolio_search_filter_downlist()
{	
	$ptype = isset($_REQUEST['post_type'])? $_REQUEST['post_type']:'';
	if($ptype == 'wpprojects_portfolio'):
	
	$drop_down_array = array(  array('_clientname'=>'Client Name'), 
							   array('_clientemail'=> 'Client Email'), 
							   array('_siteurl'=> 'Website URL'),
							   array('_clientrecommendation'=> 'CR-Received'),
							    );	
?>
<select name="sfname">
<option value=""><?php _e('Filter By Client Information', 'wpportfolio'); ?></option>
<?php
    $current_item = isset($_GET['sfname'])? $_GET['sfname']:'';
    $current_txtval = isset($_GET['sfvalue'])? $_GET['sfvalue']:'';
	
	for ($dfrow = 0; $dfrow < count($drop_down_array); $dfrow++)
	{
		 foreach ($drop_down_array[$dfrow] as $dpdown_key=>$dpdown_val) {
		 
		 printf
				(
					'<option value="%s"%s>%s</option>',
					$dpdown_key,
					$dpdown_key == $current_item? ' selected="selected"':'',
					$dpdown_val
				);
		 }
	
	} //endfor

?>
</select> <?php _e('Value:', 'wpportfolio'); ?><input type="text" name="sfvalue" value="<?php echo $current_txtval; ?>" />
<?php
endif;
}
?>
<?php
// check that the current environment supports the WP Projects Portfolio plugin
function wpprojets_portfolio_requirements_message() {
	
    global $wpdb;
	
	if (empty($portfolio_rqmts_checked)) {
		
		if (empty($top_message_head) && empty($message) && empty($message_head)) {
			
			$is_php_valid = version_compare(phpversion(), '5.0.0', '>');
			$is_mysql_valid = version_compare($wpdb->db_version(), '5.0.0', '>');
			$is_wp_valid = version_compare(get_bloginfo("version"), '3.0.0', '>');
			$meets_requirements = ($is_php_valid && $is_mysql_valid && $is_wp_valid);
			$class = $meets_requirements ? "update-message" : "error";
			
			if ( !$meets_requirements ) {
	
				$top_message_head = "<div class='error' style='margin:5px; padding:3px; text-align:left; width:93%; margin-bottom: 15px;'>";
		
				$message = "Your host setup is not compatible with WP Projects Portfolio. The following items must be upgraded:<br/> ";
		
				if(!$is_php_valid){
					$message .= " - <strong>PHP</strong> (Current version: " .  phpversion() . ", Required: 5.0)<br/> ";
				}
		
				if(!$is_mysql_valid){
					$message .= " - <strong>MySql</strong> (Current version: " .  $wpdb->db_version() . ", Required: 5.0)<br/> ";
				}
		
				if(!$is_wp_valid){	
					$message .= " - <strong>Wordpress</strong> (Current version: " .  get_bloginfo("version") . ", Required: 3.0)<br/> ";
				}
		
				$message .= "</div>";
				
				echo $top_message_head . $message;
				
			}
		}
	}

}

?>