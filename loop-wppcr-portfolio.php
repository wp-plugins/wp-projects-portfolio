<?php
//global $loop;
global $wp_query;
global $portfolio_types;
global $portfolio_output;
global $num_per_page;
global $limit_portfolios_returned;
global $currpageurl;
global $port;
global $display_the_credit;
global $options;


$odd_class ="";
$ul_close ="";
$li_close ="";

$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$portfolio_open_empty = "";
$ul_open_empty = "";
$li_open_odd_empty = "";
$post_class = "portfolio_entry ".$odd_class;

if (!isset($currpageurl)) {
	$currpageurl = get_permalink();
	$port = 0;
	$portnum = "";
} else {
	$port ++;
	$portnum = "-" . $port;
}

$wp_query_holder = $wp_query;
$wp_query = null;

// if the user has set a hard value for the number of portfolios to return in the shortcode
if ( is_numeric($limit_portfolios_returned) ) {
	if ($limit_portfolios_returned > 0) {
		$paged = 0;
		$num_per_page = $limit_portfolios_returned;
	}
}

// if the portfolio shortcode had no portfolio types defined
if ( empty($portfolio_types) ) {
	$wp_query = new WP_Query();
	$wp_query->query(array( 'post_type' => 'wpprojects_portfolio', 'posts_per_page' => $num_per_page, 'orderby' => 'meta_value' . $options['sort_numerically'], 'meta_key' => '_sortorder', 'order' => 'ASC', 'paged'=> $paged ) );
} else {
	$wp_query = new WP_Query();
	$wp_query->query( array( 'post_type' => 'wpprojects_portfolio', 'wpprojects_portfolio_type' => $portfolio_types, 'post_status'=>'publish','posts_per_page' => $num_per_page, 'orderby' => 'meta_value' . $options['sort_numerically'], 'meta_key' => '_sortorder', 'order' => 'ASC', 'paged'=> $paged ) );
}

			
/*$portfolio_open = '<div id="wp-portfolios' . $portnum . '" class="wpprojects_portfolio">';
$portfolio_open_empty = '<div id="portfolios' . $portnum . '" class="wpprojects_portfolio empty">';*/
$portfolio_open	= '';
//if ( $loop->have_posts() ) {
if ( $wp_query->have_posts() ) {

	$portfolio_output .= $portfolio_open;
	
	$portfolio_output .= '<div class="project-container">';
	$portfolio_output .='<ul>';
	while ( $wp_query->have_posts() ) {
		$wp_query->the_post();
		
		$description = '';
		$description = get_the_content();
		
		$type = get_post_meta(get_the_ID(), "_wpprojects_portfolio_type", true);
		$wpprojects_portfolio_type = get_term_by( 'slug', $type, 'wpprojects_portfolio_type' );
		if (isset($wpprojects_portfolio_type->name)) {
			$type = $wpprojects_portfolio_type->name;
		} else {
			$type = "";
		}
		$datecreate = get_post_meta(get_the_ID(), "_createdate", true);
		$client = get_post_meta(get_the_ID(), "_clientname", true);
		$technical_details = get_post_meta(get_the_ID(), "_technical_details", true);
		$siteurl = get_post_meta(get_the_ID(), "_siteurl", true);
		if(strpos($siteurl, 'http://') !== 0)
		{
			$siteurl = 'http://' . $siteurl;
		}

		$imageurl = get_post_meta(get_the_ID(), "_imageurl", true);
		$sortorder = get_post_meta(get_the_ID(), "_sortorder", true);
	
		//$portfolio_output .= '<div id="post-' . get_the_ID() . $post_multi_port . '" class="' . implode(" ", get_post_class($post_class)) . '" style="background-color:#FB00FB;">';
		
		//$portfolio_output .= '<h3 style="width:250px;"><a href="'.get_permalink().'">'.get_the_title().'</a></h3>';
		/*if($imageurl !=''):
		$portfolio_output .= '<a href="'.$imageurl.'" target="_blank"><img src="'.$imageurl.'" width="200" height="150"/></a>';
		else:
		$portfolio_output .= '<img src="' .plugins_url( 'images/no-pimage.gif' , __FILE__ ). '" width="200" height="150" border="1"/> ';
		endif;*/
		
		if (!empty($description)) {
			$description = apply_filters('the_content', $description);
			$description = strip_tags($description);
			if(strlen($description) >=200):
			$description_val= substr($description,0,200)."...";
			else:
			$description_val = $description;
			endif;
		}
		$portfolio_title = substr(get_the_title(), 0,37)."..." ;
                      $portfolio_output .='  
                        <li>
                            <h2>'.$portfolio_title.'</h2> 
                            <div class="proj-img">
                                <img src="'.$imageurl.'" width="235" height="150" alt=""/>
                                <a href="'.$siteurl.'" target="_blank">'.$siteurl.'</a>
                            </div>
                            <div class="proj-cont">
                                <p>'.$description_val.'</p>
                            </div>
                            <div class="proj-details">
                                <a href="'.get_permalink().'">View Details</a>
                            </div>
                        </li>';
				
				
						
						
			
						
			/*$portfolio_output .= '<div class="project-container">
									<a href="'.get_permalink().'" style="font-weight:bold;">'.get_the_title().'</a><br>' .nl2br($description_val).'&nbsp;';
		
			$portfolio_output .= '<h5><a href="'.get_permalink().'"><img src="'.plugins_url( 'images/view_details.png' , __FILE__ ).'" border="0"/></a></h5></div><br/>';
		
		
		$portfolio_output .= '</div><hr style="border:1px dotted #EEEEEE;background-color:#FBFBFD;"/><!-- #post-## -->';*/
		
	} // endwhile;
$portfolio_output .='</ul>';
$portfolio_output .=' <div class="clear"></div>';
	wpprojects_nav_pages($wp_query, $currpageurl, "wpprojects_nav_bottom");
	
} else {
	
	$portfolio_output .= $portfolio_open_empty;
	$portfolio_output .= $ul_open_empty;
	$portfolio_output .= $li_open_odd_empty;
	$portfolio_output .= '<div class="' . implode(" ", get_post_class($post_class)) . ' empty">';
	$portfolio_output .= '	<div class="portfolio_page_img">Coming Soon.......</div>';
	$portfolio_output .= '</div>';
	$portfolio_output .= $li_close;
	$portfolio_output .= $ul_close;
	
}

$portfolio_output .= '</div><!-- #portfolios -->';
$wp_query = $wp_query_holder;
?>