<?php
/*
Plugin Name: WP-Projects-Portfolio
Plugin URI: http://wpdeveloper.com/
Version: 1.1
Description: Portfolio listing with WP projects name , image, details and type etc... A [wp-projects-portfolio] shortcode is used to include portfolio on any page.
Author: wpdeveloper
Author URI: http://wpdeveloper.com/
*/

/*	License: GPL2
	
	Copyright @2013  wpdeveloper, Inc.  (email : dev@wpdeveloper.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/*
	THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
	ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
	WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
	DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
	ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
	(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
	LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
	ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
	(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
	SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/


define ( 'WPPROJPORTFOLIO_VERSION', '1.4.6' );
define ( 'WPPROJPORTFOLIO_DB_VERSION', '3.3.2' );
define ( 'WPPROJPORTFOLIO_PORTFOLIO_WP_PAGE', basename($_SERVER['PHP_SELF']) );
include_once("wppcr-functions.php");

// register the WP-Projets-Portfolio  custom post type and shortcode
add_action( 'init', 'wpprojects_portfolio_post_type_init' );

// register the WP-Projets-Portfolio Type taxonomy
add_action( 'init', 'generate_wpprojects_portfolio_type_taxonomy', 1 );

add_filter('post_updated_messages', 'wpprojects_portfolio_updated_messages');

if ( is_admin() ) {
	
	add_action('init', 'wpprojects_portfolio_session_start', 1);
	add_action('wp_logout', 'wpprojects_portfolio_session_end');
	add_action('wp_login', 'wpprojects_portfolio_session_end');
	
	$plugin = plugin_basename(__FILE__);
	$file = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)) . 'css/portfolio_all_admin.css';
	
	//check_options();
	
	register_activation_hook(__FILE__,'wpprojects_portfolio_install');
	
	add_action('admin_menu', 'wpprojects_portolio_admin_menu');
	add_filter( 'plugin_action_links_' . $plugin, 'add_wpprojects_portfolio_plugin_settings_link' );
	add_action('admin_print_scripts', 'wpprojects_portfolio_admin_scripts');
	add_action('admin_print_styles', 'wpprojects_portfolio_admin_styles');
	add_action('admin_notices', 'wpprojects_portfolio_display_update_alert');
	add_action('admin_menu', 'remove_wpprojects_portfolio_post_custom_fields');
	add_filter('manage_edit-wpprojects_portfolio_columns', 'add_new_wpprojects_portfolio_columns');
	add_action('manage_posts_custom_column', 'manage_wpprojects_portfolio_columns', 10, 2);
	add_action('admin_head-edit.php', 'wpprojects_portfolio_quickedit');
    add_filter('post_row_actions','wpprojects_portfolio_custom_edit',10,2);
   
	// Add the Save Metabox Data
	add_action('save_post', 'save_wpprojects_portfolio_meta', 1); // save the custom fields
	
	register_deactivation_hook( __FILE__, 'wpprojects_portfolio_remove' );
	
	add_action('admin_enqueue_scripts', 'wpprojects_portfolio_set_admin_css');
	
	if ( WPPROJPORTFOLIO_PORTFOLIO_WP_PAGE == "plugins.php" ) {
		add_action('after_plugin_row_wp-projects-portfolio/wpp-crportfolio.php', 'wpprojets_portfolio_requirements_message');
	}
	
} else {
	add_shortcode('wp-projects-portfolio', 'wpprojects_portfolio_loop');
	add_action ('wp','wpprojects_shortcode');
	add_filter('query_vars', 'wpprojects_portfolio_queryvars' );
	add_filter('posts_join', 'wpprojects_portfolio_search_join', 10, 2 );
	add_filter('posts_where', 'wpprojects_portfolio_search_where', 10, 2 );
}
?>