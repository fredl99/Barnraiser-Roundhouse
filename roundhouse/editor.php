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


if (!isset($_SESSION['user_id'])) {
	header('location: /');
	exit;
}

if (isset($_POST['submit_blog_save']) || isset($_POST['submit_blog_publish'])) {
	
	$blog_title = stripslashes(trim($_POST['blog_title']));
	$blog_title_parsed = parse_tag($blog_title);
	
	$blog_body = stripslashes(trim($_POST['blog_body']));
	$tags = trim($_POST['tags']);
	$blog_highlight = isset($_POST['blog_highlight']) ? 1 : 0;
	$blog_accept_comment = isset($_POST['blog_comments']) ? 1 : 0;

	if (!isset($_POST['import_feed']) && empty($blog_body)) {
		$GLOBALS['script_error_log'][] = _("Body not set.");
	}

	if (empty($blog_title)) {
		$GLOBALS['script_error_log'][] = _("The title is empty.");	
	}
	
	if (empty($GLOBALS['script_error_log'])) {	
		if (isset($uri_routing[1])) {
			
			$blog_id = $uri_routing[1];

			$query = "
				DELETE
				FROM " . $db->prefix . "_tag
				WHERE blog_id=" . $blog_id
			;
			
			$db->Execute($query);
			
			if (!empty($tags)) {
				$tags = explode(',', $tags);
				foreach($tags as $key => $val):
					$rec = array();
					$rec['user_id'] = $_SESSION['user_id'];
					$rec['blog_id'] = $uri_routing[1];
					$rec['tag_name'] = parse_tag(trim($val));
					$rec['tag_display_name'] = trim($val);
					$table = $db->prefix . '_tag';
					
					$db->insertDB($rec, $table);
				endforeach;
			}
			
			if (isset($_POST['submit_blog_publish'])) {
				$blog_published = 1;
			}
			else {
				$blog_published = 0;
			}

			$query = "
				UPDATE " . $db->prefix . "_blog
				SET blog_title=" . $db->qstr($blog_title_parsed) . ",
				blog_title_display=" . $db->qstr($blog_title) . ",
				blog_body=" . $db->qstr(parse($blog_body)) . ",
				blog_highlight=" . $blog_highlight . ",
				blog_accept_comment=" . $blog_accept_comment . ", 
				blog_published=" . $blog_published . " 
				WHERE blog_id=" . $blog_id . "
				AND user_id=" . $_SESSION['user_id']
			;
			
			$db->Execute($query);

		}
		else {
			
			if (isset($_POST['submit_blog_publish'])) {
				$blog_published = 1;
			}
			else {
				$blog_published = 0;
			}
			
			$rec = array();
			$rec['user_id'] = $_SESSION['user_id'];
			$rec['blog_title'] = $blog_title_parsed;
			$rec['blog_title_display'] = $blog_title;
			$rec['blog_body'] = parse($blog_body);
			$rec['blog_create_datetime'] = time();
			$rec['blog_highlight'] = $blog_highlight;
			$rec['blog_accept_comment'] = $blog_accept_comment;
			$rec['blog_published'] = $blog_published;

			if (isset($_POST['import_feed'])) {
				$blog_import_link = $_POST['import_link'];
				$blog_import_source_title = $_POST['import_source_title'];
				$blog_import_source_link = $_POST['import_source_link'];
				$blog_import_title = $_POST['import_title'];
				$blog_import_body = stripslashes($_POST['import_description']);

				$rec['blog_import_link'] = $blog_import_link;
				$rec['blog_import_title'] = $blog_import_title;
				$rec['blog_import_body'] = parse($blog_import_body);
				$rec['blog_import_source_title'] = $blog_import_source_title;
				$rec['blog_import_source_link'] = $blog_import_source_link;
			}
			
			$table = $db->prefix . '_blog';
			
			$db->insertDB($rec, $table);
			
			$blog_id = $db->insertID();
			
			$tags = explode(',', $tags);
			foreach($tags as $key => $val):
				$rec = array();
				$rec['user_id'] = $_SESSION['user_id'];
				$rec['blog_id'] = $blog_id;
				$rec['tag_name'] = parse_tag(trim($val));
				$rec['tag_display_name'] = trim($val);
				$table = $db->prefix . '_tag';
				
				$db->insertDB($rec, $table);
			endforeach;
			

		}

		if ($blog_published == 1) {
			header('location: /view/' . $blog_title_parsed);
			exit;
		}
		else {
			header('location: /editor/' . $blog_id);
			exit;
		}
	}
	else {
		$body->set('blog', $_POST);
	}
}

if (isset($uri_routing[1])) {
	$query = "
		SELECT *
		FROM " . $db->prefix . "_blog
		WHERE blog_id=" . $uri_routing[1] . "
		AND user_id=" . $_SESSION['user_id']
	;
	
	$result = $db->Execute($query);
	
	if (!empty($result)) {
		
		$query = "
			SELECT *
			FROM " . $db->prefix . "_tag
			WHERE blog_id=" . $uri_routing[1] . "
			AND user_id=" . $_SESSION['user_id']
		;
		
		$result2 = $db->Execute($query);
		
		if (!empty($result2)) {
			$blog_tags = "";
			foreach($result2 as $key => $val):
				$blog_tags .= $val['tag_display_name'] . ', ';
			endforeach;
			$blog_tags = rtrim($blog_tags, ', ');
			
			$result[0]['tags'] = $blog_tags;
		}
		
		$result[0]['blog_body'] = str_replace("<br />", "", $result[0]['blog_body']);
		
		$body->set('blog', $result[0]);

		if (!empty($result[0]['blog_import_title'])) {
			$body->set('display', 'import');
		}
	}
}
elseif (isset($_POST['import_feed'])) {
	$_POST['import_title'] = stripslashes($_POST['import_title']);

	$blog['blog_import_link'] = $_POST['import_link'];
	$blog['blog_import_title'] = $_POST['import_title'];
	$blog['blog_import_body'] = $_POST['import_description'];
	$blog['blog_import_source_link'] = $_POST['import_source_link'];
	$blog['blog_import_source_title'] = $_POST['import_source_title'];
	$blog['blog_title_display'] = $blog['blog_import_title'];
	
	$body->set('blog', $blog);
	$body->set('display', 'import');
}

?>