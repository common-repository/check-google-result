<?php
/*

**************************************************************************

Plugin Name:  Check Google Result
Plugin URI:   http://www.arefly.com/check-google-result/
Description:  Check your post whether it is in Google Search Result.
Version:      1.0.7
Author:       Arefly
Author URI:   http://www.arefly.com/
Text Domain:  check-google-result
Domain Path:  /lang/

**************************************************************************

	Copyright 2014  Arefly  (email : eflyjason@gmail.com)

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

**************************************************************************/

define("CHECK_GOOGLE_RESULT_PLUGIN_URL", plugin_dir_url( __FILE__ ));
define("CHECK_GOOGLE_RESULT_FULL_DIR", plugin_dir_path( __FILE__ ));
define("CHECK_GOOGLE_RESULT_TEXT_DOMAIN", "check-google-result");

/* Plugin Localize */
function check_google_result_load_plugin_textdomain() {
	load_plugin_textdomain(CHECK_GOOGLE_RESULT_TEXT_DOMAIN, false, dirname(plugin_basename( __FILE__ )).'/lang/');
}
add_action('plugins_loaded', 'check_google_result_load_plugin_textdomain');

include_once CHECK_GOOGLE_RESULT_FULL_DIR."options.php";

/* Add Links to Plugins Management Page */
function check_google_result_action_links($links){
	global $arefly_plugins_info, $arefly_plugins_lang, $arefly_plugins_locale_code;
	$links[] = '<a href="'.get_admin_url(null, 'options-general.php?page='.CHECK_GOOGLE_RESULT_TEXT_DOMAIN.'-options').'">'.__("Settings", CHECK_GOOGLE_RESULT_TEXT_DOMAIN).'</a>';
	return $links;
}
add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'check_google_result_action_links');

function check_google_result_check($url){
	$url = 'http://www.google.com/search?q=site:'.$url;
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	$rs = curl_exec($curl);
	curl_close($curl);
	if(strpos($rs, 'did not match any documents.')){
		return FALSE;
	}else{
		return TRUE;
	}  
}

function check_google_result($content){
	if(is_singular()){
		$get_name = get_option('check_google_result_get_name');
		if(!empty($get_name)){
			if(!isset($_GET[$get_name])){
				return $content;
			}
		}
		if(get_option('check_google_result_show_to') == "admin"){
			if(!is_super_admin()){
				return $content;
			}
		}
		if(check_google_result_check(get_permalink())){
			$content = '<p style="text-align: right; color: green;">'.__("This URL is in Google Search result.", CHECK_GOOGLE_RESULT_TEXT_DOMAIN).'</p>'.$content; 
		}else{
			$content = '<p style="text-align: right; color: red;">'.__("This URL is not in Google Search result.", CHECK_GOOGLE_RESULT_TEXT_DOMAIN).'</p>'.$content;  
		}
	}
	return $content;
}
add_filter('the_content', 'check_google_result');