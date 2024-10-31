<?php

if ( !is_admin() ) exit;


function safemarking_verify_include() {
	$smoptions = get_option("safemarking");
}


function safemarking_register_admin() {
	add_menu_page( "SafeMarking Options","SafeMarking","manage_options","safemarking-main","safemarking_tabs",$icon );
}

function safemarking_tabs() {

	switch ( $_GET['tab'] ) {
		case "help":
			$helpTab = " nav-tab-active";
		break;
		case "about":
			$aboutTab = " nav-tab-active";
		break;
		case "icons":
			$iconsTab = " nav-tab-active";
		break;
		default:
			$mainTab = " nav-tab-active";
	} // tab switch for setting current

	print "<div class=\"wrap\">";
		print "<h2>SafeMarking Options</h2>";
		print "<h3 class=\"nav-tab-wrapper\">";
		print " &nbsp;<a href=\"admin.php?page=safemarking-main&tab=main\" class=\"nav-tab$mainTab\">Options</a>";
		print "<a href=\"admin.php?page=safemarking-main&tab=icons\" class=\"nav-tab$iconsTab\">Icons</a>";
		print "<a href=\"admin.php?page=safemarking-main&tab=help\" class=\"nav-tab$helpTab\">Help</a>";
		print "<a href=\"admin.php?page=safemarking-main&tab=about\" class=\"nav-tab$aboutTab\">About</a>";
		print "</h3>";
		
		switch ($_GET['tab']){
			case "help":
				include("help.php");
			break;
			case "about":
				safemarking_info_page();
			break;
			case "icons":
				safemarking_icons();
			break;			
			default:
				safemarking_main_page();
		
		} // TAB switch
		
	print "</div>"; // .wrap
} // safemarking_tabs



function safemarking_info_page() {

print <<<ThisHTML
		<p>
			Developed by <a href="http://www.avant5.com/">Avant 5 Multimedia</a>.
		</p>
		<p>
			Copyright 2017  Avant 5 Multimedia
		</p>
		<p>
			This program is free software; you can redistribute it and/or modify<br />
			it under the terms of the GNU General Public License, version 2, as <br />
			published by the Free Software Foundation.
		</p>
		<p>
			This program is distributed in the hope that it will be useful,<br />
			but WITHOUT ANY WARRANTY; without even the implied warranty of<br />
			MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the<br />
			GNU General Public License for more details.		
		</p>
		<p>
			You should have received a copy of the GNU General Public License<br />
			along with this program; if not, write to the Free Software<br />
			Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA		
		</p>
ThisHTML;

} // _info



function safemarking_main_page() {

	GLOBAL $safemarking_plugin_URL,$safemarking_plugin_directory;

	$autostart = get_option('safemarking_auto');
	$options = get_option('safemarking');
	$default = $options['default'];
	$marks = $default['marks'];

	// && (check_admin_referer( 'sm_update_options', 'sm_nonce'  )) // from POST check below DEBUG
	if ( ($_POST['update_safemarking_main_options'] == "Update Options") ):

		check_admin_referer( 'sm_update_options', 'sm_nonce'  );
		
		$marks = array();
		if ( $_POST['safemarking_facebook'] ) $marks[] = "facebook";
		if ( $_POST['safemarking_twitter'] ) $marks[] = "twitter";
		if ( $_POST['safemarking_delicious'] ) $marks[] = "delicious";
		if ( $_POST['safemarking_linkedin'] ) $marks[] = "linkedin";
		if ( $_POST['safemarking_digg'] ) $marks[] = "digg";
		if ( $_POST['safemarking_newsvine'] ) $marks[] = "newsvine";
		if ( $_POST['safemarking_google'] ) $marks[] = "google";
		if ( $_POST['safemarking_reddit'] ) $marks[] = "reddit";
		if ( $_POST['safemarking_stumble'] ) $marks[] = "stumble";
		if ( $_POST['safemarking_technorati'] ) $marks[] = "technorati";
		if ( $_POST['safemarking_yahoo'] ) $marks[] = "yahoo";
		if ( $_POST['safemarking_email'] ) $marks[] = "email";
		if ( $_POST['safemarking_pinit'] ):
			// // 1.2 upgrade
			switch ( $_POST['safemarking_pinit'] ){
				case "vertical":
					$marks[] = "pinit-vertical";
				break;
				case "horizontal":
					$marks[] = "pinit-horizontal";
				break;
				default:
					$marks[] = "pinit";
			} // pinit switch
		endif; // pinit
		
		$default['homepage'] = ( $_POST['safemarking_homepage'] )?true:false;
		$default['pages'] = ( $_POST['safemarking_pages'] )?true:false;
		$default['target'] = ( $_POST['safemarking_target'] )?true:false;
		$default['css_disable'] = ( $_POST['safemarking_css'] )?true:false;
		// location not true/false because more options for location coming in the future
		switch ( $_POST['safemarking_location'] ) {
			case "top":
				$default['location'] = "top";
			break;
			default:
				$default['location'] = "bottom";
		} // location switch
		
		if ( $_POST['safemarking_fblike'] ):
			switch ( $_POST['safemarking_fblike'] ) {
				case "standard": $marks[] = "like_standard"; break;
				case "count": $marks[] = "like_count"; break;
			} // switch
		endif;
		
		
		if ( $_POST['safemarking_gplus'] ):
			switch ( $_POST['safemarking_gplus'] ) {
				case "small": $marks[] = "gplus_sm"; break;
				case "standard": $marks[] = "gplus_st"; break;
				case "medium": $marks[] = "gplus_md"; break;
				case "tall": $marks[] = "gplus_tl"; break;
			} // switch
		endif;
		
		$default['marks'] = $marks;
		
		$options['default'] = $default;
		update_option('safemarking',$options);
		
		$autoflag = ($_POST['safemarking_auto'])?1:0;
		update_option('safemarking_auto',$autoflag);
		
	endif; // form processing

	$thisNonce = wp_nonce_field( 'sm_update_options', 'sm_nonce', true, false );
	
	// populate form values
	if ( $marks ):
		foreach ( $marks as $x ) {
			switch ($x) {
				case "facebook": $check_facebook = 'checked="checked" ';  break;
				case "twitter": $check_twitter = 'checked="checked" '; break;
				case "digg": $check_digg = 'checked="checked" '; break;
				case "linkedin": $check_linkedin = 'checked="checked" '; break;
				case "delicious": $check_delicious = 'checked="checked" '; break;
				case "newsvine": $check_newsvine = 'checked="checked" '; break;
				case "google": $check_google = 'checked="checked" '; break;
				case "reddit": $check_reddit = 'checked="checked" '; break;
				case "stumble": $check_stumble = 'checked="checked" '; break;
				case "technorati": $check_technorati = 'checked="checked" '; break;
				case "yahoo": $check_yahoo = 'checked="checked" '; break;
				case "email": $check_email = 'checked="checked" '; break;
				case "pinit": $check_pinit = 'checked="checked" '; break;  // 1.2 upgrade
				
				case "like_count": $check_like_count = 'checked="checked" '; break;
				case "like_standard": $check_like_standard = 'checked="checked" '; break;
				case "gplus_sm": $check_gplus_small = 'checked="checked" '; break;
				case "gplus_st": $check_gplus_standard = 'checked="checked" '; break;
				case "gplus_md": $check_gplus_medium = 'checked="checked" '; break;
				case "gplus_tl": $check_gplus_tall = 'checked="checked" '; break;
				case "pinit": $check_pinit = 'checked="checked" '; break;
				case "pinit-vertical": $check_pinit_vertical = 'checked="checked" '; break;
				case "pinit-horizontal": $check_pinit_horizontal = 'checked="checked" '; break;
			} // $x switch
		} // each $marks
	endif; 
	
	if ( (!$check_like_count) && (!$check_like_standard) ) $check_like_zero = 'checked="checked" ';
	if ( (!$check_gplus_small) && (!$check_gplus_standard) && (!$check_gplus_medium) && (!$check_gplus_tall) ) $check_gplus_zero = 'checked="checked" ';
	if ( (!$check_pinit) && (!$check_pinit_horizontal) && (!$check_pinit_vertical) ) $check_pinit_zero  = 'checked="checked" ';
	if ( $default['homepage'] ) $homepage_checked = 'checked="checked" ';
	if ( $default['pages'] ) $pages_checked = 'checked="checked" ';
	if ( $default['target'] ) $target_checked = 'checked="checked" ';
	if ( $default['css_disable'] ) $css_checked = 'checked="checked" ';
	if ( get_option('safemarking_auto') ) $auto_checked = 'checked="checked" ';
	
	switch ( $default['location'] ) {
		case "top":
			$location_checked_top = 'checked="checked" ';
		break;
		default:
			$location_checked_bottom = 'checked="checked" ';
	} // location switch

print <<<ThisHTML

	<!-- DEBUG -->
	<style>
	.safemarking-icon-grid { overflow:hidden; }
	.safemarking-icon-grid p { float:left; width:70px; text-align:center;}
	</style>

	<form method="post">
	$thisNonce
	<table class="form-table">
		<tr>
			<th scope="row">
				Social Bookmarking<br />
				<span class="description">Check to display</span>
			</th>
			<td class="safemarking-icon-grid">
				<p>
					<label for="safemarking-facebook"><img src="{$safemarking_plugin_URL}icons/safemarking/facebook.png" alt="Facebook" title="Facebook" /></label>
					<br /><input type="checkbox" name="safemarking_facebook" id="safemarking-facebook" {$check_facebook}/>
				</p>
				<p>
					<label for="safemarking-twitter"><img src="{$safemarking_plugin_URL}icons/safemarking/twitter.png" alt="Twitter" title="Twitter" /></label>
					<br /><input type="checkbox" name="safemarking_twitter" id="safemarking-twitter" {$check_twitter}/>
				</p>
				<p>
					<label for="safemarking-delicious"><img src="{$safemarking_plugin_URL}icons/safemarking/delicious.png" alt="Delicious" title="Delicious" /></label>
					<br /><input type="checkbox" name="safemarking_delicious" id="safemarking-delicious" {$check_delicious}/>
				</p>
				<p>
					<label for="safemarking-linkedin"><img src="{$safemarking_plugin_URL}icons/safemarking/linkedin.png" alt="LinkedIn" title="LinkedIn" /></label>
					<br /><input type="checkbox" name="safemarking_linkedin" id="safemarking-linkedin" {$check_linkedin}/>
				</p>
				<p>
					<label for="safemarking-digg"><img src="{$safemarking_plugin_URL}icons/safemarking/digg.png" alt="Digg" title="Digg" /></label>
					<br /><input type="checkbox" name="safemarking_digg" id="safemarking-digg" {$check_digg}/>
				</p>
				<p>
					<label for="safemarking-newsvine"><img src="{$safemarking_plugin_URL}icons/safemarking/newsvine.png" alt="Newsvine" title="Newsvine" /></label>
					<br /><input type="checkbox" name="safemarking_newsvine" id="safemarking-newsvine" {$check_newsvine}/>
				</p>
				<p>
					<label for="safemarking-newsvine"><img src="{$safemarking_plugin_URL}icons/safemarking/google.png" alt="Google Bookmarks" title="Google Bookmarks" /></label>
					<br /><input type="checkbox" name="safemarking_google" id="safemarking-google" {$check_google}/>
				</p>
				<p>
					<label for="safemarking-reddit"><img src="{$safemarking_plugin_URL}icons/safemarking/reddit.png" alt="Reddit" title="Reddit" /></label>
					<br /><input type="checkbox" name="safemarking_reddit" id="safemarking-reddit" {$check_reddit}/>
				</p>
				<p>
					<label for="safemarking-stumble"><img src="{$safemarking_plugin_URL}icons/safemarking/stumbleupon.png" alt="stumble" title="stumble" /></label>
					<br /><input type="checkbox" name="safemarking_stumble" id="safemarking-stumble" {$check_stumble}/>
				</p>
				<p>
					<label for="safemarking-technorati"><img src="{$safemarking_plugin_URL}icons/safemarking/technorati.png" alt="technorati" title="technorati" /></label>
					<br /><input type="checkbox" name="safemarking_technorati" id="safemarking-technorati" {$check_technorati}/>
				</p>
				<p>
					<label for="safemarking-yahoo"><img src="{$safemarking_plugin_URL}icons/safemarking/yahoo.png" alt="yahoo" title="yahoo" /></label>
					<br /><input type="checkbox" name="safemarking_yahoo" id="safemarking-yahoo" {$check_yahoo}/>
				</p>
				<p>
					<label for="safemarking-email"><img src="{$safemarking_plugin_URL}icons/safemarking/email.png" alt="email" title="email" /></label>
					<br /><input type="checkbox" name="safemarking_email" id="safemarking-email" {$check_email}/>
				</p>
			</td>
		</tr>
		<tr>
			<th scope="row">
				Pinterest
			</th>
			<td>
				<p>
					<input type="radio" name="safemarking_pinit" id="safemarking_pinit_zero" value="" {$check_pinit_zero}/>
					<label for="safemarking_pinit_zero">None</label>
				</p>
				<p>
					<input type="radio" name="safemarking_pinit" id="safemarking_pinit_standard" value="standard" {$check_pinit}/>
					<label for="safemarking_pinit">Standard<br /><img src="{$safemarking_plugin_URL}examples/pinit.png" alt="Pinit standard" /></label>
				</p>
				<p>
					<input type="radio" name="safemarking_pinit" id="safemarking_pinit_vertical" value="vertical" {$check_pinit_vertical}/>
					<label for="safemarking_pinit_vertical">Pinterest Vertical<br /><img src="{$safemarking_plugin_URL}examples/pinit-v.png" alt="Pinit vertical" /></label>
				</p>
				<p>
					<input type="radio" name="safemarking_pinit" id="safemarking_pinit_horizontal" value="horizontal" {$check_pinit_horizontal}/>
					<label for="safemarking_pinit_horizontal">Pinterest Horizontal<br /><img src="{$safemarking_plugin_URL}examples/pinit-h.png" alt="Pinit horizontal" /></label>
				</p>
			</td>
		</tr>
		<tr>
			<th scope="row">
				Facebook <em>Like</em> Button
			</th>
			<td>
				<p><input type="radio" name="safemarking_fblike" id="safemarking-fblike-zero" value="0" {$check_like_zero}/> <label for="safemarking-fblike-zero">None</label></p>
				<p><input type="radio" name="safemarking_fblike" id="safemarking-fblike-standard" value="standard" {$check_like_standard}/> <label for="safemarking-fblike-standard">Standard<br /><img src="{$safemarking_plugin_URL}examples/like-standard.png" alt="Facebook Like - Standard" /></label></p>
				<p><input type="radio" name="safemarking_fblike" id="safemarking-fblike-count" value="count" {$check_like_count}/> <label for="safemarking-fblike-count">Count only<br /><img src="{$safemarking_plugin_URL}examples/like-count.png" alt="Facebook Like - Count" /></label></p>
			</td>
		</tr>
		<tr>
			<th scope="row">
				G+ Button
			</td>
			<td>
				<p><input type="radio" name="safemarking_gplus" id="safemarking-gplus" value="0" {$check_gplus_zero}/> <label for="safemarking-gplus">None</label></p>
				<p><input type="radio" name="safemarking_gplus" id="safemarking-gplus" value="small" {$check_gplus_small}/> <label for="safemarking-gplus">Small<br /><img src="{$safemarking_plugin_URL}examples/gplus-small.png" alt="G+ - small size" /></label></p>
				<p><input type="radio" name="safemarking_gplus" id="safemarking-gplus" value="standard" {$check_gplus_standard}/> <label for="safemarking-gplus">Standard<br /><img src="{$safemarking_plugin_URL}examples/gplus-medium.png" alt="G+ - medium size" /></label></p>
				<p><input type="radio" name="safemarking_gplus" id="safemarking-gplus" value="medium" {$check_gplus_medium}/> <label for="safemarking-gplus">Medium<br /><img src="{$safemarking_plugin_URL}examples/gplus.png" alt="G+ - standard size" /></label></p>
				<p><input type="radio" name="safemarking_gplus" id="safemarking-gplus" value="tall" {$check_gplus_tall}/> <label for="safemarking-gplus">Tall<br /><img src="{$safemarking_plugin_URL}examples/gplus-tall.png" alt="G+ - tall size" /></label></p>
			</td>
		</tr>
		<tr>
			<th scope="row">
				General Options
			</th>
			<td>
				<p><input type="checkbox" name="safemarking_auto" id="safemarking-auto" {$auto_checked}/> Automatic Filtering<br />
					<span class="description">Check this to automatically add to all posts.  Disable this if using shortcodes, widgets or the safemarking() function in theme code</span></p>
				<p><input type="checkbox" name="safemarking_homepage" id="safemarking-homepage" {$homepage_checked}/> Show on homepage <span class="description">Check to show on homepage posts.  This flag also effects shortcodes placed in posts.</span></p>
				<p><input type="checkbox" name="safemarking_pages" id="safemarking-pages" {$pages_checked}/> Show on pages <span class="description">Check to show on WP pages</span></p>
				<p><input type="checkbox" name="safemarking_target" id="safemarking-target" {$target_checked}/> Open links in new tab/window</p>
				<p>
					<h3 style="margin:0; padding:0;">Bookmarks Location</h3>
					<span class="description">Select to display on top or below the post content</span><br />
					<input type="radio" name="safemarking_location" id="safemarking-location-top" value="top" {$location_checked_top}/> <label for="safemarking-location-top">Top</label><br />
					<input type="radio" name="safemarking_location" id="safemarking-location-bottom" value="bottom" {$location_checked_bottom}/> <label for="safemarking-location-bottom">Bottom</label><br />
				</p>
				<p>
					<input type="checkbox" name="safemarking_css" id="safemarking-css" {$css_checked}/> <label for="safemarking-css">Disable custom CSS</label><br />
					<span class="description">Check to <strong>disable</strong> custom CSS files included in icon packages</span>
				</p>
			</td>
		</tr>
	</table>
	<input type="submit" name="update_safemarking_main_options" value="Update Options" class="button-primary" />
	</form>
ThisHTML;
} // safemarking_main_page()



function safemarking_icons() {
	GLOBAL $safemarking_plugin_directory,$safemarking_plugin_URL;
	
	$options = get_option('safemarking');
	$default = $options['default'];
	
	if ( ( $_POST['sm_icon_set'] ) && ( file_exists( $safemarking_plugin_directory.'/icons/'.$_POST['sm_icon_set'] ) )   ):
		check_admin_referer( 'sm_update_icons', 'sm_nonce'  );
		// update default
		$default['icon_set'] = sanitize_text_field( $_POST['sm_icon_set'] );
		// update options (variable)
		$options['default'] = $default;
		// update options (wp)
		update_option('safemarking',$options);
	
	endif; // $_POST
	
	print "<div class=\"wrap\">";
	print "<h2>SafeMarking Icons</h2>";
	if ($handle = opendir($safemarking_plugin_directory.'/icons')) {
		while (false !== ($entry = readdir($handle))):
			$set_information = array();
			if ($entry != "." && $entry != ".."):
				print "<div class=\"safemarking-icon-row\">";
				if ( file_exists($safemarking_plugin_directory.'/icons/'.$entry.'/info.txt') ):
					$fp = fopen( $safemarking_plugin_directory.'/icons/'.$entry.'/info.txt', 'r' );
					$i = 1;
					while ( !feof($fp) ):
						$line = fgets( $fp, 1024 );
						$i++;
						if ($i > 10) break; // prevent a hacker uploading a multi-million line text file, and crashing the server
						
						// parse template, trim off whitespace user may include in the file
						if ( substr( strtolower($line),0,5) == "name:" ) $set_information['name'] = trim( substr($line,5) );
						if ( substr( strtolower($line),0,7) == "author:" ) $set_information['author'] = trim( substr($line,7) );
						if ( substr( strtolower($line),0,11) == "author url:" ) $set_information['author_url'] = trim( substr($line,11) );
						
					endwhile;
					fclose($fp);
					
					if ( $set_information['name'] ):
						print "<h3>{$set_information['name']}</h3>";
					else:
						// info file, but not name setting
						print "<h3>$entry</h3>";
					endif;
					
				else:
					// No info file
					print "<h3>$entry</h3>";
				endif;
				
				print "<img src=\"{$safemarking_plugin_URL}icons/{$entry}/facebook.png\" alt=\"No Facebook icon available\"/>&nbsp;";
				print "<img src=\"{$safemarking_plugin_URL}icons/{$entry}/twitter.png\" alt=\"No Twitter icon available\"/>&nbsp;";
				print "<img src=\"{$safemarking_plugin_URL}icons/{$entry}/delicious.png\" alt=\"No Delicious icon available\"/>&nbsp;";
				print "<img src=\"{$safemarking_plugin_URL}icons/{$entry}/linkedin.png\" alt=\"No LinkedIn icon available\"/>&nbsp;";
	
$thisNonce = wp_nonce_field( 'sm_update_icons', 'sm_nonce', true, false );	

	print "<div class=\"safemarking-icon-controls\">";
	if ( $default['icon_set'] == $entry ):
		print "Current";
	else:
print <<<ThisHTML
			<form method="post" style="display:inline-block">
				<input type="hidden" name="sm_icon_set" id="sm_icon_set" value="{$entry}" />
				$thisNonce
				<input type="submit" value="Activate" style="border:0; background-color:transparent; padding:0; text-decoration:underline; cursor:pointer;" />
			</form>
ThisHTML;
	endif; // default icon set check


	if ( $set_information['author'] && $set_information['author_url']):
		print " | by: <a href=\"{$set_information['author_url']}\">{$set_information['author']}</a>";
	elseif ( $set_information['author'] ):
		print " | by: {$set_information['author']}";
	endif;

	
print "</div>"; // .safemarking-icon-controls

				
				print "</div>"; // .safemarking-icon-row
			endif; // ./.. entry check
		endwhile;
		closedir($handle);
	} // end icon directory parse
	
	print "</div>"; // .wrap
} // safemarking_icons()

function safemarking_admin_header() {

	GLOBAL $safemarking_plugin_URL;
	
	// test if it's the SM plugin, and execute
	echo "<link rel=\"stylesheet\" href=\"{$safemarking_plugin_URL}admin.css\" />";
	
}

function safemarking_activate() {
	// Set up the secret code for includes
	$newKey = smkeys(40,1);
	// reset inckey every activation
	$smoptions = get_option("safemarking"); // load, just in case there was a deactivation with stored settings - reset only the key
	$smoptions['icon_set'] = ($smoptions['icon_set'])?$smoptions['icon_set']:"avant_5_glossy";
	$smoptions['inckey'] = $newKey;
	update_option("safemarking",$smoptions);
	$auto = get_option("safemarking_auto");
	$auto = ( $auto )?1:0;
	update_option("safemarking_auto",$auto);
}

function safemarking_uninstall() {
	delete_option("safemarking");
	delete_option("safemarking_auto");
}

function safemarking_deactivate() {
	// debug
	
}

?>