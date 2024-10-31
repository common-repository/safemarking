<?php

/*
	Plugin Name: SafeMarking
	Plugin URI: http://www.avant5.com
	Description: Social bookmarking and sharing plugin for Wordpress
	Author: Avant 5 Multimedia
	Version: 1.2.2
	Author URI: http://www.avant5.com
	
	Copyright 2013  Avant 5 Multimedia

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

$blogURL = get_bloginfo('url');
$safemarking_plugin_file = plugin_basename(__FILE__);
$safemarking_plugin_URL = plugin_dir_url(__FILE__);
$safemarking_plugin_directory = dirname(__FILE__);


function sm_get_attribute($content,$attrib="") {
	if (!$attrib) return false;
	$begPoint = stripos($content, "{$attrib}=\"");
	if (!begPoint) return false;
	$attrLength = strlen($attrib) + 2;
	$content = substr($content,$begPoint+$attrLength);
	$begPoint = strpos($content, '"');
	$content = substr($content, 0, $begPoint);
	return $content;
}



if ( is_admin() ) include("safemarking-admin.php");

function smkeys($length=12,$chars=0) {
	   $pattern = ($chars)?"1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-_":"1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-";
	   for($i=0;$i<$length;$i++)
	   {
		 $key .= $pattern{rand(0,( strlen($pattern) - 1) )};
	   }
	   return $key;
}

function safemarking($icon_order="",$atts="") {
	$y = do_safemarking($icon_order);
	$actions = explode(",",$atts);
		foreach ($actions as $x) {
			$p = explode("=",$x);
			$q = $p[0];
			$r = $p[1];
			$attribute[$q] = $r;
		}
	if ( $attribute['echo'] == "false" ) return $y;
	echo $y;
	// for theme use
} // safemarking()

function safemarking_post($content){

	$options = get_option('safemarking');
	$default = $options['default'];
	
	// Don't waste resources processing if the option is no output
	if ( (is_home()) && (!$default['homepage']) ) return $content;
	if ( (is_page()) && (!$default['pages']) ) return $content;
	
	$y = do_safemarking();

	switch ($default['location']) {
		case "top":
			$content = $y.$content;
		break;	
		default:
			$content .= $y;
	}
	return $content;
} // safemarking_post()

function safemarking_shortcode($atts,$content=null) {

	$safemarking_options = get_option('safemarking');
	$default = $safemarking_options['default'];
	
	// Don't waste resources processing if the option is no output
	// Shortcode version only checks for homepage block.  Assumes if shortcode inserted into [type] page, user wants this.
	if ( (is_home()) && (!$default['homepage']) && ($atts['homepage'] != "true") ) return $content;
	
	if ($atts['homepage'] == "false") return $content;
	
	if ( $atts['marks'] ):
		$marks = explode(",",$atts['marks']);
	endif;
	$y = do_safemarking($marks);
	return $y;
} // safemarking_shortcode

function do_safemarking($icon_set="") {

	GLOBAL $blogURL,$safemarking_plugin_file,$safemarking_plugin_URL;
	$thisPermalink = get_permalink();
	$thisTitle = get_the_title();
	$thisThumbnail = get_the_post_thumbnail();
	if ($thisThumbnail) $thisThumbnail = sm_get_attribute($thisThumbnail,$attrib="src");

	
	
	if ($icon_set):
		$icon_order = explode(",",$icon_set);
	endif;
	
	$safemarking_options = get_option('safemarking');
	$default = $safemarking_options['default'];
	
	if (!$icon_order) $icon_order = $default['marks'];
	if (!$icon_order) $icon_order = array("facebook","twitter","delicious");
	$icon_directory = ($default['icon_set'])?$default['icon_set']:"avant_5_glossy";
	if ( $default['target'] ) $safemarking_link_target = " target=\"_blank\"";
	
	
	
	foreach ( $icon_order as $x ){
	
		switch ( strtolower( trim($x) ) ) {
		
			case "facebook":
			case "fb":
				$y .= "<li class=\"sm-facebook\"><a href=\"http://www.facebook.com/sharer.php?u=$thisPermalink\"$safemarking_link_target><img src=\"{$safemarking_plugin_URL}icons/$icon_directory/facebook.png\" title=\"Share on Facebook\" alt=\"Share on Facebook\" /></a></li>";
			break;
			
			case "email":
				$y .= "<li class=\"sm-email\"><a href=\"mailto:?subject=$thisTitle&amp;body=$thisPermalink\"$safemarking_link_target><img src=\"{$safemarking_plugin_URL}icons/$icon_directory/email.png\" title=\"Email this article\" alt=\"Email this article\" /></a></li>";
			break;
			
			case "twitter":
				$y .= "<li class=\"sm-twitter\"><a href=\"http://twitter.com/home?status=Reading: $thisTitle - $thisPermalink\"><img src=\"{$safemarking_plugin_URL}icons/$icon_directory/twitter.png\" title=\"Share on Twitter\" alt=\"Share on Twitter\" /></a></li>";
			break;
			
			case "delicious":
			case "del":
				$y .= "<li class=\"sm-delicious\"><a href=\"http://www.delicious.com/post?url=$thisPermalink&title=$thisTitle\"><img src=\"{$safemarking_plugin_URL}icons/$icon_directory/delicious.png\" title=\"Bookmark on Delicious\" alt=\"Bookmark on Delicious\" /></a></li>";
			break;
			
			case "digg":
				$y .= "<li><a href=\"http://www.digg.com/submit?phase=2&url=$thisPermalink&title=$thisTitle\" class=\"sm-digg\"><img src=\"{$safemarking_plugin_URL}icons/$icon_directory/digg.png\" title=\"Bookmark on Digg\" alt=\"Bookmark on Digg\" /></a></li>";		
			break;
			
			case "newsvine":
				$y .= "<li><a href=\"http://www.newsvine.com/_tools/seed&save?u=$thisPermalink&h=$thisTitle\" class=\"sm-newsvine\"><img src=\"{$safemarking_plugin_URL}icons/$icon_directory/newsvine.png\" title=\"Share on Newsvine\" alt=\"Share on Newsvine\" /></a></li>";		
			break;
			
			case "like_count":
			case "likecount":
				$y .= "<li class=\"sm-like\"><iframe src=\"http://www.facebook.com/plugins/like.php?href=$thisPermalink&amp;send=false&amp;layout=button_count&amp;width=77&amp;show_faces=false&amp;font&amp;colorscheme=light&amp;action=like&amp;height=21\" scrolling=\"no\" frameborder=\"0\" style=\"border:none; overflow:hidden; width:77px; height:21px;\" allowTransparency=\"true\"></iframe></li>";
			break;
			
			case "like_standard":
			case "likestandard":
				$y .= "<li class=\"sm-like\"><iframe src=\"http://www.facebook.com/plugins/like.php?href=$thisPermalink&amp;layout=standard&amp;show_faces=false&amp;
width=450&amp;action=like&amp;colorscheme=light\" scrolling=\"no\" frameborder=\"0\" allowTransparency=\"true\" style=\"border:none; overflow:hidden; width:450px; height:23px;\">
</iframe></li>";
			break;			
			
			case "linkedin":
			case "li":
				$y .= "<li class=\"sm-linkedin\"><a href=\"http://www.linkedin.com/shareArticle?mini=true&url=$thisPermalink&title=$thisTitle\"><img src=\"{$safemarking_plugin_URL}icons/$icon_directory/linkedin.png\" title=\"Share on LinkedIn\" alt=\"Share on LinkedIn\" /></a></li>";			
			break;
			
			case "gplus_sm":
			case "gplussmall":
				$y .= "<li class=\"sm-gplus\"><g:plusone size=\"small\" href=\"$thisPermalink\"></g:plusone></li>";
			break;
			case "gplus_st":
			case "gplus":
				$y .= "<li class=\"sm-gplus\"><g:plusone href=\"$thisPermalink\"></g:plusone></li>";
			break;
			case "gplus_md":
			case "gplusmedium":
				$y .= "<li class=\"sm-gplus\"><g:plusone size=\"medium\" href=\"$thisPermalink\"></g:plusone></li>";
			break;
			case "gplus_tl":
			case "gplustall":
				$y .= "<li class=\"sm-gplus\"><g:plusone size=\"tall\" href=\"$thisPermalink\"></g:plusone></li>";
			break;
			
			case "stumble":
			case "su":
				$y .= "<li class=\"sm-stumble\"><a href=\"http://www.stumbleupon.com/submit?url=$thisPermalink&title=$thisTitle\"><img src=\"{$safemarking_plugin_URL}icons/$icon_directory/stumbleupon.png\" title=\"Share on StumbleUpon\" alt=\"Share on StumbleUpon\" /></a></li>";
			break;
			
			case "technorati":
				$y .= "<li class=\"sm-technorati\"><a href=\"http://www.technorati.com/faves?add=$thisPermalink\"><img src=\"{$safemarking_plugin_URL}icons/$icon_directory/technorati.png\" title=\"Share on Technorati\" alt=\"Share on Technorati\" /></a></li>";
			break;
			
			case "reddit":
				$y .= "<li class=\"sm-reddit\"><a href=\"http://reddit.com/submit?url=$thisPermalink&title=$thisTitle\"><img src=\"{$safemarking_plugin_URL}icons/$icon_directory/reddit.png\" title=\"Share on Reddit\" alt=\"Share on Reddit\" /></a></li>";
			break;
			
			case "google":
				$y .= "<li class=\"sm-google\"><a href=\"http://www.google.com/bookmarks/mark?op=edit&bkmk=$thisPermalink&title=$thisTitle\"><img src=\"{$safemarking_plugin_URL}icons/$icon_directory/google.png\" title=\"Bookmark on Google\" alt=\"Bookmark on Google\" /></a></li>";
			break;
			
			case "yahoo":
				$y .= "<li class=\"sm-yahoo\"><a href=\"http://bookmarks.yahoo.com/toolbar/savebm?u=$thisPermalink&t=$thisTitle\"><img src=\"{$safemarking_plugin_URL}icons/$icon_directory/yahoo.png\" title=\"Bookmark on Yahoo Bookmarks\" alt=\"Save on Yahoo Bookmarks\" /></a></li>";
			break;
			
			case "pinit":
			case "pinit-vertical";
			case "pinit-horizontal";
				$pinLayout = "none";
				if ( $x == "pinit-vertical" ) $pinLayout = "vertical";
				if ( $x == "pinit-horizontal" ) $pinLayout = "horizontal";
				// count-layouts: none, horizontal, vertical
				$y .= "<li class=\"sm-pinterest\"><a href=\"http://pinterest.com/pin/create/button/?url=$thisPermalink&media=$thisThumbnail\" class=\"pin-it-button\" count-layout=\"{$pinLayout}\"><img src=\"{$safemarking_plugin_URL}icons/$icon_directory/pinterest.png\" title=\"Pin it on Pinterest!\" alt=\"Pin it on Pinterest!\" /></a><script type=\"text/javascript\" src=\"http://assets.pinterest.com/js/pinit.js\"></script><br /></li>"; 
			break;

			
			default:
			// nothing
		} // $x switch
	
		
	}
	
	if ($y):
		$y = "<div class=\"safemarking\"><ul>$y</ul></div>";
		return $y;
	else:
		return false;
	endif;
	

}


function safemarking_header() {
	GLOBAL $safemarking_plugin_URL,$safemarking_plugin_directory;
	$options = get_option('safemarking');
	$default = $options['default'];
	$thisStylesheet = $safemarking_plugin_directory.'/icons/'.$default['icon_set'];
	if ( (!$default['css_disable']) && (file_exists( $safemarking_plugin_directory.'/icons/'.$default['icon_set'].'/safemarking.css' )) ) print "<link rel=\"stylesheet\" href=\"{$safemarking_plugin_URL}icons/{$default['icon_set']}/safemarking.css\" />";

} // safemarking_header()


function safemarking_footer() {
	print "<script type=\"text/javascript\" src=\"http://apis.google.com/js/plusone.js\"></script>";
}



if ( get_option('safemarking_auto') ) add_filter('the_content', 'safemarking_post');
add_action('wp_footer','safemarking_footer');
add_action('wp_head','safemarking_header');
add_shortcode('safemarking','safemarking_shortcode');

if ( is_admin() ):
add_action('admin_menu', 'safemarking_register_admin');
add_action('admin_head', 'safemarking_admin_header');
register_activation_hook(__FILE__,'safemarking_activate');
register_deactivation_hook(__FILE__,'safemarking_deactivate');
register_uninstall_hook(__FILE__,'safemarking_uninstall');
endif;

?>