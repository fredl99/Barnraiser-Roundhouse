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


unset($result);

if (isset($_POST['delete_comment'])) {
	if (!empty($_POST['delete_comment_id'])) {
		$query = "
			DELETE FROM " . $db->prefix . "_blog_comment 
			WHERE 
			comment_id=" . $_POST['delete_comment_id']
		;
		
		$db->Execute($query);

	}
}
elseif (isset($_POST['post_comment'])) {
	
	if (!match_maptcha($_POST['maptcha_text'])) {
		$GLOBALS['script_error_log'][] = _("You failed the math test dismally. Please try again.");
	}

	$comment_body = trim($_POST['comment_body']);

	if (empty($comment_body)) {
		$GLOBALS['script_error_log'][] = _("You have not included any comment.");
	}

	if (empty($_POST['comment_user_name'])) {
		$GLOBALS['script_error_log'][] = _("You have not included your name.");
	}

	$comment_body = parse($comment_body);

	if (empty($GLOBALS['script_error_log'])) {
		$rec = array();
		$rec['comment_body'] = $comment_body;
		$rec['comment_create_datetime'] = time();
		$rec['blog_id'] = $_POST['blog_id'];
		$rec['comment_user_name'] = $_POST['comment_user_name'];

		if (!empty($_POST['comment_email'])) {
			$rec['comment_email'] = $_POST['comment_email'];
		}

		$table = $db->prefix . '_blog_comment';

		$db->insertDB($rec, $table);


		// we mail the webspace owner if they requested it
		if (!empty($webspace['user_email_notify'])) {
			require_once('class/Mail/class.phpmailer.php');
	
			$mail->Subject = _("You have received a blog comment");
		
			$email_message = _("You have received the following comment from {user_name} \n\n<hr />{comment_body}<hr />\nYou can see it at {url}");
			$email_message = str_replace("{user_name}", $_POST['comment_user_name'], $email_message);
			$email_message = str_replace("{comment_body}", $comment_body, $email_message);
			$email_message = str_replace("{url}", SCRIPT_HTTP_HOST . "/" . $_POST['blog_title'], $email_message);
			
			if (!empty($_POST['comment_email'])) {
				$email_message .= _("\n\nThe sender included their email as {comment_email}.");
				$email_message = str_replace("{comment_email}", $_POST['comment_email'], $email_message);
			}
	
			$email_message .= _("\nThis mail was sent from Roundhouse");
		
		
			// HTML-version of the mail
			$html  = "<HTML><HEAD><TITLE></TITLE></HEAD>";
			$html .= "<BODY>";
			$html .= utf8_decode(nl2br($email_message));
			$html .= "</BODY></HTML>";
		
			$mail->Body = $html;
			// non - HTML-version of the email
			$mail->AltBody   = utf8_decode($email_message);
		
			$mail->ClearAddresses();
			$mail->AddAddress($webspace['user_email']);
		
			if($mail->Send()) {
				// sent
				$contact_msg = 1;
			}
		}

		header('Location: /view');
		exit;
	}
	else {
		// errors were recorded
		$body->set('add_comment_error', 1);
	}
}
elseif (isset($_POST['submit_contact_form'])) {
	if (empty($_POST['contact_subject'])) {
		$GLOBALS['script_error_log'][] = _("Subject empty.");
	}

	if (empty($_POST['contact_message'])) {
		$GLOBALS['script_error_log'][] = _("Message empty.");
	}
	
	if (!match_maptcha($_POST['maptcha_text'])) {
		$GLOBALS['script_error_log'][] = _("You failed the math test dismally. Please try again.");
	}

	if (empty($GLOBALS['script_error_log'])) {

		$query = "
			SELECT user_email
			FROM " . $db->prefix . "_user
			WHERE user_id=" . WEBSPACE_ID
		;
		
		$result = $db->Execute($query);

		$user_email = $result[0]['user_email'];	

		require_once('class/Mail/class.phpmailer.php');

		$email_subject = stripslashes(htmlspecialchars($_POST['contact_subject']));
		
		$mail->Subject = $email_subject;
	
		$email_message = stripslashes(htmlspecialchars($_POST['contact_message']));
	
		if (!empty($_POST['contact_email'])) {
			$email_message .= _("\n\nThe sender included their email as {comment_email}.");
			$email_message = str_replace("{comment_email}", $_POST['contact_email'], $email_message);

			$mail->From = $_POST['contact_email'];
		}

		$email_message .= _("\n\nThis mail was sent from Roundhouse");
	
	
		// HTML-version of the mail
		$html  = "<HTML><HEAD><TITLE></TITLE></HEAD>";
		$html .= "<BODY>";
		$html .= utf8_decode(nl2br($email_message));
		$html .= "</BODY></HTML>";
	
		$mail->Body = $html;
		// non - HTML-version of the email
		$mail->AltBody   = utf8_decode($email_message);
	
		$mail->ClearAddresses();
		$mail->AddAddress($user_email);
	
		if($mail->Send()) {
			// sent
			$contact_msg = 1;
		}
	}
}


// GET BLOG ENTRIES ------------------------------------

if (isset($uri_routing[1]) && $uri_routing[1] == "date") { // archive

	if (!empty($uri_routing[2])) {
		$year = $uri_routing[2];

		$query = "
			SELECT b.blog_id, b.blog_title, b.blog_title_display, b.blog_body, 
			b.blog_create_datetime, b.blog_import_title, b.blog_import_body, 
			b.blog_import_source_title, b.blog_import_source_link, 
			b.blog_import_link, b.blog_highlight, b.blog_published, 
			b.blog_accept_comment, u.user_name, 
			count(c.blog_id) as comment_total 
			FROM " . $db->prefix . "_blog b 
			LEFT JOIN " . $db->prefix . "_blog_comment c ON  b.blog_id=c.blog_id 
			INNER JOIN " . $db->prefix . "_user u ON b.user_id=u.user_id 
			WHERE 
			b.user_id=" . WEBSPACE_ID . " AND 
			YEAR(b.blog_create_datetime)=" . $year . " AND "
		;

		if (!empty($uri_routing[3])) {
			$month = $uri_routing[3];

			$query .= "MONTH(b.blog_create_datetime)=" . $month . " AND ";
		}

		if (!isset($_SESSION['user_id'])) {
			$query .= "b.blog_published=1 AND ";
		}

		$query .= "
			1=1 
			GROUP BY b.blog_id 
			ORDER BY b.blog_create_datetime desc"
		;
	
		$result = $db->Execute($query, 200); // this needs paging and not a "200" hard coded limit
	}

}
elseif (isset($uri_routing[1]) && $uri_routing[1] == "tag" && isset($uri_routing[2])) { // tag

	$query = "
		SELECT b.blog_id, b.blog_title, b.blog_title_display, b.blog_body, 
		b.blog_create_datetime, b.blog_import_title, b.blog_import_body, 
		b.blog_import_source_title, b.blog_import_source_link, 
		b.blog_import_link, b.blog_highlight, b.blog_published, 
		b.blog_accept_comment, u.user_name, 
		count(c.blog_id) as comment_total 
		FROM " . $db->prefix . "_blog b 
		LEFT JOIN " . $db->prefix . "_blog_comment c ON  b.blog_id=c.blog_id 
		INNER JOIN " . $db->prefix . "_user u ON b.user_id=u.user_id 
		INNER JOIN " . $db->prefix . "_tag t ON t.blog_id=b.blog_id 
		WHERE 
		b.user_id=" . WEBSPACE_ID . " AND 
		t.tag_name=" . $db->qstr($uri_routing[2]) . " AND "
	;

	if (!isset($_SESSION['user_id'])) {
		$query .= "b.blog_published=1 AND ";
	}
	
	$query .= "
		1=1 
		GROUP BY b.blog_id 
		ORDER BY b.blog_create_datetime desc"
	;

	$result = $db->Execute($query);
}
elseif (isset($uri_routing[1]) && substr($uri_routing[1], 0, 4) != 'page') { // single blog
	$query = "
		SELECT b.blog_id, b.blog_title, b.blog_title_display, b.blog_body, 
		b.blog_create_datetime, b.blog_import_title, b.blog_import_body, 
		b.blog_import_source_title, b.blog_import_source_link, 
		b.blog_import_link, b.blog_highlight, b.blog_published, 
		b.blog_accept_comment, u.user_name, 
		count(c.blog_id) as comment_total 
		FROM " . $db->prefix . "_blog b 
		LEFT JOIN " . $db->prefix . "_blog_comment c ON  b.blog_id=c.blog_id 
		INNER JOIN " . $db->prefix . "_user u ON b.user_id=u.user_id 
		WHERE 
		b.user_id=" . WEBSPACE_ID . " AND 
		b.blog_title=". $db->qstr($uri_routing[1]) . " AND "
	;

	if (!isset($_SESSION['user_id'])) {
		$query .= "b.blog_published=1 AND ";
	}
	
	$query .= "
		1=1 
		GROUP BY b.blog_id 
		ORDER BY b.blog_create_datetime desc"
	;

	$result = $db->Execute($query, 1);
	
}
else { // we list the latest 10 blog entries

	$query = "
		SELECT COUNT(*) AS total_blogs
		FROM " . $db->prefix . "_blog
		WHERE 
		user_id=" . WEBSPACE_ID
	;

	if (!isset($_SESSION['user_id'])) {
		$query .= " AND blog_published=1";
	}
	
	$result = $db->Execute($query);

	$from = 0;
	if (isset($uri_routing[1]) && substr($uri_routing[1], 0, 4) == 'page') {
		if (is_numeric(substr($uri_routing[1], 4))) {
			$from = (int) substr($uri_routing[1], 4) * 10;
		}
	}
	
	if ($from >= 10) {
		$body->set('prev', substr($uri_routing[1], 4) - 1);
	}
	
	if ($from < $result[0]['total_blogs']-10) {
		$body->set('next', $from/10 + 1);
	}

	$query = "
		SELECT b.blog_id, b.blog_title, b.blog_title_display, b.blog_body, 
		b.blog_create_datetime, b.blog_import_title, b.blog_import_body, 
		b.blog_import_source_title, b.blog_import_source_link, 
		b.blog_import_link, b.blog_highlight, b.blog_published, 
		b.blog_accept_comment, u.user_name, 
		count(c.blog_id) as comment_total 
		FROM " . $db->prefix . "_blog b 
		LEFT JOIN " . $db->prefix . "_blog_comment c ON  b.blog_id=c.blog_id 
		INNER JOIN " . $db->prefix . "_user u ON b.user_id=u.user_id 
		WHERE 
		b.user_id=" . WEBSPACE_ID . " AND "
	;

	if (!isset($_SESSION['user_id'])) {
		$query .= "b.blog_published=1 AND ";
	}
	
	$query .= "
		1=1 
		GROUP BY b.blog_id 
		ORDER BY b.blog_create_datetime desc"
	;

	$result = $db->Execute($query, 10, $from);
}

// Add tags to blog entries and output
if (!empty($result)) {
	foreach ($result as $key => $i):
		$query = "
			SELECT t.tag_name, t.tag_display_name 
			FROM " . $db->prefix . "_tag t 
			WHERE 
			t.user_id=" . WEBSPACE_ID . " AND 
			t.blog_id=" . $i['blog_id'] . "
			ORDER BY tag_name "
		;
		
		$tag_result = $db->Execute($query);
		
		if (isset($tag_result)) {
			$result[$key]['tags'] = $tag_result;
		}
	endforeach;

	$body->set('blogs', $result);

	$tpl->set('first_blog_title', $result[0]['blog_title_display']);
}



// SELECT TAGS --------------------------------------------
$query = "
	SELECT t.tag_name, t.tag_display_name, count(t.tag_name) as tag_total 
	FROM " . $db->prefix . "_tag t 
	WHERE 
	t.user_id=" . WEBSPACE_ID . "
	GROUP BY tag_name "
;

$result = $db->Execute($query);

if (!empty($result)) {
	$body->set('tags', $result);
}


// SELECT ARCHIVE ------------------------------------------
$query = "
	SELECT count(b.blog_id) as total, YEAR(b.blog_create_datetime) as year, MONTH(b.blog_create_datetime) as month 
	FROM " . $db->prefix . "_blog b 
	WHERE 
	b.user_id=" . WEBSPACE_ID . " AND 
	b.blog_published=1 
	GROUP BY YEAR(b.blog_create_datetime), MONTH(b.blog_create_datetime)"
;

$result = $db->Execute($query);

if (!empty($result)) {
	$body->set('blog_archive', $result);
}


// SELECT HIGHLIGHTS ---------------------------------------
$query = "
	SELECT b.blog_id, b.blog_title, b.blog_title_display 
	FROM " . $db->prefix . "_blog b 
	WHERE 
	b.user_id=" . WEBSPACE_ID . " AND 
	b.blog_highlight=1 AND 
	b.blog_published=1  
	ORDER BY b.blog_create_datetime"
;

$result = $db->Execute($query, 10);

if (!empty($result)) {
	$body->set('blog_highlights', $result);
}


$maptcha = gen_maptcha();
$body->set('maptcha', $maptcha);

?>