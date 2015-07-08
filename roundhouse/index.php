<?php

// -----------------------------------------------------------------------
// This file is part of Roundhouse
// 
// Copyright (C) 2003-2008 Barnraiser
// http://www.barnraiser.org/
// info@barnraiser.org
// 
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
// 
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with this program; see the file COPYING.txt.  If not, see
// <http://www.gnu.org/licenses/>
// -----------------------------------------------------------------------


// CHECK INSTALLED
if (is_readable("install/installer.php")) {
	header("Location: install/installer.php");
	exit;
}


include_once ("config/core.config.php");
include_once ("inc/functions.inc.php");


// SESSION HANDLER --------------------------------------------------
session_name($core_config['php']['session_name']);
session_start();


// SETUP URL ROUTING ------------------------------------------------
$uri_routing = routeURL();


// LOGOFF ----------------------------------------------------------
if (isset($uri_routing[0]) && $uri_routing[0] == "disconnect") {
	session_unset();
	session_destroy();
	session_write_close();
	header("Location: /");
	exit;
}


// SET LOCALE ----------------------------------------------------------
define ('LOCALE', $core_config['language']['server_locale']);

if (isset($core_config['language']['standard_locale'])) {
	define ('STND_LOCALE', $core_config['language']['standard_locale']);
}
else {
	define ('STND_LOCALE', $core_config['language']['server_locale']);
}

putenv("LANGUAGE=".LOCALE);
setlocale(LC_ALL, LOCALE);

$domain = 'roundhouse';
bindtextdomain($domain, dirname(__FILE__) . "/language"); 
textdomain($domain);

// Pending - see if we can remove the LC_MESSAGE dir and store all .mp .po files in language dir
//putenv("TEXTDOMAINDIR=./languages");
//echo getenv('TEXTDOMAINDIR'); // we maybe able to alter this to get the required path


// SETUP DATABASE ----------------------------------------------
require_once('class/Db.class.php');
$db = new Database($core_config['db']);


// SETUP OPENID ----------------------------------------------
include_once ('inc/openid_consumer.inc.php');


// SETUP TEMPLATES --------------------------------------------------
define("SCRIPT_TEMPLATE_PATH", "template/");

require_once('class/Template.class.php');
$tpl = new Template(); // outer template
$body = new Template(); // inner template

define('SCRIPT_MAX_LIST_ROWS', $core_config['display']['max_list_rows']);


// SETUP WEBSPACE ---------------------------------------------------
if (!empty($core_config['script']['single_webspace'])) { // single comain name
	$user_webspace = $core_config['script']['single_webspace'];
	define ('SCRIPT_HTTP_HOST', $core_config['script']['core_domain']);
}
elseif (isset($core_config['script']['multiple_webspace_pattern'])) { // using sub-domains
	
	preg_match ($core_config['script']['multiple_webspace_pattern'], $_SERVER['HTTP_HOST'], $matches);
	
	if (!empty($matches[1])) {
		$user_webspace = $matches[1];
		define ('SCRIPT_HTTP_HOST', "http://" . $_SERVER['HTTP_HOST']);
	}
}

if (!empty($user_webspace)) {
	// GET WEBSPACE -------------------------------------------
	$query = "
		SELECT user_id, user_name, user_location, user_blog_title, 
		user_blog_description, user_email, user_email_notify, 
		openid_delegate, openid_server, user_blog_theme, 
		user_blog_language 
		FROM " . $db->prefix . "_user
		WHERE user_webspace=" . $db->qstr($user_webspace)
	;
	
	$result = $db->Execute($query, 1);
	
	if (!empty($result[0])) {
		
		define ('WEBSPACE_ID', $result[0]['user_id']);
		define ('WEBSPACE_NAME', $result[0]['user_name']);
		
		$webspace = $result[0];
	
		$body->set('webspace', $webspace);
		$tpl->set('webspace', $webspace);
	}
	else {
		$uri_routing[0] = "public";
	}
}
elseif (!isset($uri_routing[0]) || $uri_routing[0] != "register") {
	$uri_routing[0] = "public";
}



// SETUP THEME -------------------------------------------
if (!empty($webspace['user_blog_theme']) && is_readable('theme/'.$webspace['user_blog_theme'].'/css/common.css')) {
	define("SCRIPT_THEME_NAME", $webspace['user_blog_theme']);
}
else {
	define("SCRIPT_THEME_NAME", $core_config['script']['default_theme_name']);
}

define("SCRIPT_THEME_PATH", "theme/" . SCRIPT_THEME_NAME . "/");


// SELECT SCRIPT AND TEMPLATE --------------------------------------------
if (isset($uri_routing[0]) && is_readable(SCRIPT_TEMPLATE_PATH . $uri_routing[0] . '.tpl.php')) {
	define("SCRIPT_NAME", $uri_routing[0]);
}
elseif (defined('WEBSPACE_ID')) {
	array_unshift($uri_routing, 'view');
	define("SCRIPT_NAME", 'view');
}
else {
	array_unshift($uri_routing, 'public');
	define("SCRIPT_NAME", 'public');
}

if (defined('SCRIPT_NAME') && is_readable(SCRIPT_NAME . '.php')) {
	require_once(SCRIPT_NAME . '.php');
	$inner_template_body = file_get_contents(SCRIPT_TEMPLATE_PATH . SCRIPT_NAME . '.tpl.php');
}
else {
	header('location: /disconnect');
	exit;
}


// SET TEMPLATE VARS -----------------------------------------------------
$body->set('uri_routing', $uri_routing);
$tpl->set('uri_routing', $uri_routing);

$tpl->set('content', $body->parse($inner_template_body));

$outer_tpl = SCRIPT_TEMPLATE_PATH . 'wrapper.tpl.php';

echo $tpl->fetch($outer_tpl);

?>
