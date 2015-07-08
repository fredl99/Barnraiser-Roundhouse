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


include_once ("../config/core.config.php");
include_once ("../inc/functions.inc.php");


// SESSION HANDLER ----------------------------------------------------------------------------
// sets up all session and global vars 
session_name($core_config['php']['session_name']);
session_start();


// SETUP AROUNDMe CORE -----------------------------------------------------------------------
require('../class/Db.class.php');
$db = new Database($core_config['db']);


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

if (!isset($user_webspace)) {
	die ('Your domain / subdomain is not set up correctly.');
	exit;
}


// GET WEBSPACE -------------------------------------------
$query = "
	SELECT user_id, user_blog_title, user_blog_description  
	FROM " . $db->prefix . "_user
	WHERE user_webspace=" . $db->qstr($user_webspace)
;

$result = $db->Execute($query, 1);

if (!empty($result[0])) {
	$webspace = $result[0];
	$webspace['user_blog_description'] = strip_tags($webspace['user_blog_description']);
	
	$query = "
		SELECT b.blog_id, b.blog_title, b.blog_title_display, b.blog_body, 
		b.blog_create_datetime, u.user_name 
		FROM " . $db->prefix . "_blog b 
		INNER JOIN " . $db->prefix . "_user u ON b.user_id=u.user_id 
		WHERE 
		b.user_id=" . $webspace['user_id'] . " AND 
		b.blog_published=1 
		ORDER BY b.blog_create_datetime desc"
	;

	$result = $db->Execute($query, 10);

	if (!empty($result)) {
		$feed_items = $result;
		
		foreach($feed_items as $key => $i):

			$feed_items[$key]['link'] = $core_config['script']['core_domain'] . "/" . $i['blog_title'];
			$feed_items[$key]['body'] = trim(strip_tags($i['blog_body']));
			$feed_items[$key]['body'] = mb_substr($feed_items[$key]['body'], 0, 200, 'UTF-8');
			$feed_items[$key]['body'] = htmlspecialchars($feed_items[$key]['body']);
			$feed_items[$key]['title'] = htmlspecialchars($feed_items[$key]['blog_title_display']);
		endforeach;
	}
	
	
	$url = $core_config['script']['core_domain'] . "/";
	
	header("Content-Type: application/xml; charset=ISO-8859-1");
	
	echo "<?xml version=\"1.0\" encoding=\"ISO-8859-1\" ?>\n";
	echo "<?xml-stylesheet title=\"XSL_formatting\" type=\"text/xsl\" href=\"nolsol.xsl\"?>\n";
	echo "<rss version=\"2.0\">\n";
	echo "<channel>\n";
	echo "<title>" . utf8_decode($webspace['user_blog_title']) . "</title>\n";
 	echo "<link>" . $url . "</link>\n";
 	echo "<description>" . utf8_decode($webspace['user_blog_description']) . "</description>\n";
 	echo "<language>en</language>\n";
 	echo "<lastBuildDate>" . date("r") . "</lastBuildDate>\n";
	
	if (!empty($feed_items)) {
		foreach ($feed_items as $key => $i):
			echo "<item>\n";
			echo "<title>" . utf8_decode($i['title']) . "</title>\n";
			echo "<description>" . utf8_decode($i['body']) . "</description>\n";
			echo "<link>" . $i['link'] . "</link>\n";
			echo "<author>" . utf8_decode($i['user_name']) . "</author>\n";
			echo "<pubDate>" . date("r", strtotime($i['blog_create_datetime'])) . "</pubDate>\n";
			echo "</item>";
		endforeach;
	}
	
	echo "</channel>\n";
	echo "</rss>";
}
else {
	exit;
}

?>