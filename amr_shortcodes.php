<?php
/*
Plugin Name: amr shortcodes
Plugin URI: 
Description: View the shortcodes in use on a site, with links to the pages or posts for editing.
Author: anmari
Version: 1.7
Author URI: http://webdesign.anmari.com

*/

if (!class_exists('amr_shortcodes_plugin_admin')) {
	class amr_shortcodes_plugin_admin {
		var $hook 		= 'amr_shortcodes';
		var $filename	= 'amr_shortcodes/amr_shortcodes.php';
		var $longname	= 'Shortcodes';
		var $shortname	= 'Shortcodes';
		var $optionname = '';
		var $homepage	= '';
		var $parent_slug = 'plugin_listings_menu';
		var $accesslvl	= 'manage_options';
		
		function __construct() {  
			add_action('admin_menu', array(&$this, 'register_tools_page') );	
		}		
		
		function register_tools_page() {
			add_management_page( $this->longname, $this->shortname, $this->accesslvl, $this->hook, array(&$this,'config_page'));
		}		
		
		function plugin_options_url() {
			return admin_url( 'tools.php?page='.$this->hook );
		}		
 
		function admin_heading($title)  {

		echo '<div class="wrap" >
			<div id="icon-options-general" class="icon32"><br />
			</div>
			<h2>';
			$active1 = '';
			$active2 = '';
			$active3 = '';
			$active4 = '';
			if (empty($_REQUEST['tab']) OR ($_REQUEST['tab'] == 'available')   ) 
				$active3 = 'nav-tab-active';
			elseif ($_REQUEST['tab'] == 'all') 
				$active2 = 'nav-tab-active';
			elseif ($_REQUEST['tab'] == 'missing') 
				$active1 = 'nav-tab-active';
			else
				$active4 = 'nav-tab-active';
			
			echo '<a class="nav-tab '.$active3.'" href="'.add_query_arg('tab','available',$this->plugin_options_url()) .'">'.__('Available shortcodes','amr-shortcodes').'</a>  &nbsp; ';
			
			echo '<a class="nav-tab '.$active4.'" href="'.add_query_arg('tab','where',$this->plugin_options_url()).'">'
			.__('Where used','amr-shortcodes').'</a>'	
			.'<a class="nav-tab '.$active1.'" href="'
			.add_query_arg('tab','missing',$this->plugin_options_url()).'">'.$title.' '
			.__(' maybe missing function','amr-shortcodes').'</a> &nbsp; '
			.'<a class="nav-tab '.$active2.'" href="'.add_query_arg('tab','all',$this->plugin_options_url()).'">'
			.__('Posts with shortcodes','amr-shortcodes').'</a>'		
			.'</h2><br />';

			
		}
		
		function config_page() {
			$this->admin_heading($this->longname); 

			if (empty($_REQUEST['tab']) OR ($_REQUEST['tab'] == 'available')) {
				$url = wp_nonce_url(add_query_arg('show_available_shortcodes', 1, home_url()),'available_shortcodes');
				echo '<p>Try front end availabilty (admin users only): <a class="button" title="Attempt to show all available shortcodes including those that plugins have only added in front end" href="'.$url.'">available shortcodes added before the wp action template_redirect</a></p>';
				echo '<p>Below are the available shortcodes that wordpress knows about in the backend.  Sometimes a plugin will only add the shortcode if it knows it is running in the front end.</p>';
				echo '<p>Available shortcodes should be ones where the function to run them is loaded, (the plugin is active) </p>';

				echo '<p>For shortcode text where the function may be missing, see <a href="'.admin_url('tools.php?page=amr_shortcodes&tab=missing').'">All posts with shortcodes</a> or <a href="'.admin_url('tools.php?page=amr_shortcodes&tab=missing').'">Maybe Missing Function</a></p>';
				
				$this->shortcodes_available();
			}	
			elseif (($_REQUEST['tab'] == 'all') OR ($_REQUEST['tab'] == 'missing') )
				$this->where_shortcode('');
			else
				$this->where_one_shortcode();
		}		
		
		function shortcodes_available() {
		global $shortcode_tags;

		
			$builtin = array('caption','gallery','audio','video','playlist','embed', 'wp_caption');

			ksort($shortcode_tags);
			
			echo '<br/>'.PHP_EOL.'<table class="widefat wp-list-table striped"><thead><tr><th class="manage-column">'
			.__('Shortcode').'</th><th>'
			.__('Function called').'</th><th>'
			.__('Built in by WordPress <br/>but could be overwritten').'</th></tr></thead><tbody>';
			foreach ($shortcode_tags as $code => $func) {
				if (is_array($func)) $func = get_class ($func[0]).'->'.$func[1];
				echo '<tr><td>'
				.'<a href="'
				.add_query_arg('shortcode_text',$code,admin_url( '/tools.php?page=amr_shortcodes&tab=where')).
				'">'.$code.'</a>'
				.'</td><td>'.$func.'</td><td>';
				if (in_array( $code,$builtin)) _e('built-in');
				else echo ' ';
				echo '</td></tr>';
			}
			echo '</tbody></table>';
		}

		function shortcode_post_list ($results) {
			
			if (!empty($_REQUEST['tab']) and !($_REQUEST['tab'] == 'missing') )
				$doall = true;
			else 
				$doall = false;

			//if (isset($_REQUEST['dir'])) {
				
			
			echo '<table class="widefat wp-list-table striped"><thead><tr><th>';
			_e('Posts','amr-shortcodes');
			echo '</th><th>';
			_e('ID','amr-shortcodes');
			echo '</th><th>';
			_e('Type');

			echo '</th><th>';
			_e('Shortcodes','amr-shortcodes'); 
			if (empty($_REQUEST['tab'])) _e(' (Plugin or theme not active?) ');
			echo '</th><th>';
			_e('Published','amr-shortcodes');
			echo '</th></tr>'
			.'</thead><tbody>';
			foreach($results as $i => $result) {
			
				preg_match_all("^\[(.*)\]^",$result->post_content,$matches, PREG_PATTERN_ORDER);

				$shorts = array();	
				
				foreach ($matches[0] as $j=> $m) {
					if (substr($m,0,2) == '[[') continue; // its really not a shortcode
					if (substr($m,0,2) == "['") continue; // its really not a shortcode

					$sp = strpos($m,' '); 			
					if (!$sp) { // there was no space
						$close = strpos($m,']')-1;
						if (!$close) { // its not a shortcode, there was no close
							$code ='';
						}
						else {
							$code =  substr($m,1,$close); // there was no space							
						} 
					}
					else { 
						$code = (substr($m,1,$sp));
					}
					$code = str_replace (' ','',$code);
				
					if (substr($code,0,1) === '/') {// might be closing shortcode, check if we had opening
							$code = substr($code,1,-1);
							//if (strpos($result->post_content,$code ) < strpos($result->post_content,'/'.$code )) {//its ok

						}
						
					if (!empty($code) and (!stristr($code, 'CDATA'))) {
						if ($doall or !shortcode_exists( $code )) {
							$shorts[$code][] = $m;
						}
					}	

				}
			
				if (empty($shorts)) {continue;}
				echo '<tr><td>';
				edit_post_link($result->post_title.' ',' ',' ',$result->ID);
				echo '</td><td>'.$result->ID;
				echo '</td><td>'.$result->post_type.' ';
				if (!($result->post_status == 'publish')) _e($result->post_status);
				echo '</td><td>';
				//preg_match_all("^\[(.*)\]^",$result->post_content,$matches, PREG_PATTERN_ORDER);

				foreach ($shorts as $short=> $instances) {
				
					if ( !shortcode_exists($short ) ) {
							$flag = '<a style="color:red;" href="'.
							get_edit_post_link($result->post_title.' ',' ',' ',$result->ID)
							.'" title="Plugin or theme might not be active, or may not add shortcode in admin area - edit the post.">'
							.__('X?').'</a>';					
					}
					else {
						$flag = '<span style="color:green;">'.'&#10004;'.'</span>';
					}	
					foreach ($instances as $i => $m) {
						$s = substr($m, 0, strpos($m,']',0)+1);
						$t = substr($s, 1, strpos($s,' '));
						if (empty($t)) 
							$t = substr($s, 1, strpos($s,']')-1);
						$link = '<a title="'.
						__('Search for only this shortcode in all posts and pages', 'amr-shortcodes')
						.'" href="'
						.add_query_arg('shortcode_text',$t,admin_url( '/tools.php?page=amr_shortcodes&tab=where' ))
						.'">'.$t.'</a>';
						$text = str_replace($t, $link, $s);
						echo $flag.' '.$text ;//***
						echo '<br />';
					}	
											
				};
				echo '</td><td>';
				echo substr($result->post_date,0,11);

				echo '</td></tr>';
				
			}
			echo '</tbody></table>';
		}
	
		function where_shortcode($shortcode_text) {
			global $wpdb;
			global $shortcode_tags;

			if (empty($shortcode_text)) 
				$shortcode_text = '%';
			else 
				$shortcode_text = $shortcode_text.'%';
			$types = get_post_types(array( 'public'=> true), 'names' ); 
			$text = "('".implode("','",$types)."')";
			$results 	= array();
			$orderby = 'ORDER BY post_type ASC, post_status ASC, post_date DESC;';
			if (isset($_REQUEST['orderby'])) {
				$orderby = 'ORDER BY '.sanitize_text_field($_REQUEST['orderby']);
				if (isset($_REQUEST['dir'])) {
					$dir = sanitize_text_field($_REQUEST['dir']);
				}
				else $dir = 'ASC';
				$orderby = $orderby.' '.$dir.';';
			}

				
			$query  	= "SELECT * FROM $wpdb->posts WHERE post_type IN ".$text." AND post_status IN ( 'publish', 'future', 'pending', 'draft', 'private') and post_content LIKE '%[".$shortcode_text."]%' AND post_date is not null  ".$orderby ;

			$results 	= $wpdb->get_results($query);			
			$this->shortcode_post_list ($results);
		}
				
		function where_one_shortcode() {

			if (!empty($_REQUEST['shortcode_text']))
				$shortcode_text = sanitize_text_field($_REQUEST['shortcode_text']);
			else 
				$shortcode_text = 'gallery';
			echo '<form method="post" action="'.esc_html( admin_url( '/tools.php?page=amr_shortcodes&tab=where' )).'">'
			.'<input type="text" name="shortcode_text" value="'.$shortcode_text.'" />';

			submit_button('Search for shortcode in posts', 'amr-shortcodes');
			echo PHP_EOL.' </form>';
			
			$this->where_shortcode($shortcode_text);
		}
	}
}	

function amr_shortcodes_add_action_links ( $links ) {
 $mylinks[] = 
 '<a title="Go" href="'.admin_url( 'tools.php?page=amr_shortcodes').'">'  . 'Manage Shortcodes</a>';
return array_merge( $links, $mylinks );
}

function amr_shortcodes_load_text() { 
// wp (see l10n.php) will check wp-content/languages/plugins if nothing found in plugin dir
	$result = load_plugin_textdomain( 'amr-shortcodes', false, 
	dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

add_action('plugins_loaded'         , 'amr_shortcodes_load_text' );

add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'amr_shortcodes_add_action_links' );


if (is_admin() ) 	$amr_shortcodes_plugin_admin = new amr_shortcodes_plugin_admin();  

function available_shortcodes_template_redirect() {
	// add_shortcode('where_one_test', 'test'); //test to see if shows in front end 
    if ( current_user_can('administrator') and !empty($_REQUEST['show_available_shortcodes'])
		and check_admin_referer( 'available_shortcodes' ) ) {        
	
		echo '<div style="margin: 2em;">';
		$amr_shortcodes_plugin_admin = new amr_shortcodes_plugin_admin();  
		$amr_shortcodes_plugin_admin->shortcodes_available();
		echo '</div>';
        exit();
    }
}

add_action( 'template_redirect', 'available_shortcodes_template_redirect' );
//add_action( 'wp_head', 'available_shortcodes_template_redirect' );

?>