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


include_once 'inc/openid_consumer.inc.php';

if (isset($_POST['login_admin'])) {
	
	$login_email = trim($_POST['login_email']);
	$login_password = trim($_POST['login_password']);
	
	if (empty($login_email)) {
		$GLOBALS['script_error_log'][] = _("Email address is not set.");
	}
	
	if (empty($login_password)) {
		$GLOBALS['script_error_log'][] = _("Password not set.");
	}
	
	if (empty($GLOBALS['script_error_log'])) {
		$query = "
			SELECT *
			FROM " . $db->prefix . "_user
			WHERE user_email=" . $db->qstr($login_email) . "
			AND user_password=" . $db->qstr(md5($login_password))
		;

		$result = $db->Execute($query);
		
		if (!empty($result)) {
			$_SESSION['user_id'] = $result[0]['user_id'];
			$_SESSION['user_email'] = $result[0]['user_email'];
			$_SESSION['user_name'] = $result[0]['user_name'];
			$_SESSION['user_dob'] = $result[0]['user_dob'];
			$_SESSION['user_location'] = $result[0]['user_location'];
		
			$query = "
				UPDATE " . $db->prefix . "_user
				SET user_last_login_datetime=" . $db->qstr(time()) . "
				WHERE user_id=" . $_SESSION['user_id']
			;
			$db->Execute($query);
			
			$maintainer_openids = array();
			
			if (!empty($core_config['security']['maintainer_webspace_ids'])) {
				$maintainer_webspace_ids = explode(",", $core_config['security']['maintainer_webspace_ids']);
		
				foreach ($maintainer_webspace_ids as $key => $i):
					$maintainer_webspace_ids[$key] = trim($i);
				endforeach;
			}
	
			if (in_array($result[0]['user_webspace'], $maintainer_webspace_ids)) {
				$_SESSION['user_is_maintainer'] = 1;
			}
		}
		else {
			$GLOBALS['script_error_log'][] = _("Login failed. Please check your email and password.");
		}
	}
}
elseif (isset($_POST['submit_new_password'])) {
	$dob_year = (int) $_POST['dob_year'];
	$dob_month = (int) $_POST['dob_month'];
	$dob_day = (int) $_POST['dob_day'];

	$dob = formatDate($dob_year, $dob_month, $dob_day);

	if (empty($_POST['new_password_email'])) {
		$GLOBALS['script_error_log'][] = _("You must provide a valid email address.");
	}
	
	if (empty($GLOBALS['script_error_log'])) {
		
		$query = "
			SELECT user_id
			FROM " . $db->prefix . "_user
			WHERE
			user_email=" . $db->qstr($_POST['new_password_email']) . " AND 
			user_dob=" . $db->qstr($dob)
		;

		$result = $db->Execute($query, 1);
		
		if (!empty($result[0]['user_id'])) {
			// we reset the password
			$new_password  = $new_password = substr(md5(time()), 0, 5);
			
			// we send a message
			$query = "
				UPDATE " . $db->prefix . "_user
				SET user_password=" . $db->qstr(md5($new_password)) . "
				WHERE user_id=" . $result[0]['user_id']
			;
			
			$db->Execute($query);
			
			require_once('class/Mail/class.phpmailer.php');
	
			// email, subject, message
			$email_subject = "Here is your new password";
		
			$mail->Subject = $email_subject;
		
			$email_message = "Hi!\nThis is your new password: " . $new_password;
		
			// HTML-version of the mail
			$html  = "<HTML><HEAD><TITLE></TITLE></HEAD>";
			$html .= "<BODY>";
			$html .= utf8_decode(nl2br($email_message));
			$html .= "</BODY></HTML>";
	
			$mail->Body = $html;
			// non - HTML-version of the email
			$mail->AltBody   = utf8_decode(strip_tags($email_message));
			$mail->AddAddress($_POST['new_password_email']);
		
			if($mail->Send()) {

				// sent
				$body->set('new_password', 1);
			}
			// success message
			$GLOBALS['script_message_log'][] = array("Your profile information was updated.");
		}
		else {
			$GLOBALS['script_error_log'][] = _("We could not find a match to your email and memorable date. Please use the 'contact us' form to inform us if you are unable to login.");
		}
	}
}
elseif (isset($_POST['submit_delete_blog_entries'])) {
	if (!empty($_POST['delete_blog_entry'])) {
		$blog_ids = "";
		foreach($_POST['delete_blog_entry'] as $key => $val):
			$blog_ids .= $val . ', ';
		endforeach;
		$blog_ids = rtrim($blog_ids, ', ');
		
		$query = "
			DELETE
			FROM " . $db->prefix . "_blog
			WHERE blog_id IN(" . $blog_ids . ")
			AND user_id=" . $_SESSION['user_id']
		;
		
		$db->Execute($query);
	}
}
elseif (isset($_POST['submit_import_url'])) {
	$import_url = trim($_POST['import_url']);
	
	if (!empty($import_url)) {
		$http_check = substr($import_url, 0, 4);

		if ($http_check != "http") {
			$import_url = "http://" . $import_url;
		}
	}
	else {
		$GLOBALS['script_error_log'][] = _("You need to enter the url for the feed.");
	}
	
	if (empty($GLOBALS['script_error_log'])) {
		if (is_xml($import_url)) {
			$feed = fetch_xml_feed($import_url);
		}
		else {
			$feed_url = get_link_tags($import_url);

			if (substr($feed_url, 0, 4) != "http") {
				$feed_url = $import_url.$feed_url;
			}
			
			if (is_xml($feed_url)) {
				$feed = fetch_xml_feed($feed_url);
			}
		}
		
		if (isset($feed)) {
			$body->set('import', $feed);
		}
		else {
			$GLOBALS['script_error_log'][] = _("The feed either cannot be found or has returned no items.");
		}
	}
}


if (isset($_SESSION['user_id'])) {
	// order by datetime desc
	
	$query = "
		SELECT COUNT(*) AS total_blogs
		FROM " . $db->prefix . "_blog
		WHERE user_id=" . WEBSPACE_ID
	;
	
	$result = $db->Execute($query);

	$from = 0;
	if (isset($uri_routing[1]) && substr($uri_routing[1], 0, 4) == 'page') {
		if (is_numeric(substr($uri_routing[1], 4))) {
			$from = (int) substr($uri_routing[1], 4) * 20;
		}
	}
	
	if ($from >= 20) {
		$body->set('prev', substr($uri_routing[1], 4) - 1);
	}
	
	if ($from < $result[0]['total_blogs']-20) {
		$body->set('next', $from/20 + 1);
	}
	
	$query = "
		SELECT blog_id, blog_title, blog_create_datetime, blog_title_display, 
		blog_published 
		FROM " . $db->prefix . "_blog
		WHERE user_id=" . $_SESSION['user_id'] . "
		ORDER BY blog_create_datetime DESC"
	;
	
	$result = $db->Execute($query, 20, $from);
	
	if (!empty($result)) {
		$body->set('blogs', $result);
	}
}



// help functions
function OLDget_link_tags($str) {
	$html = 0;
	$html = @file_get_contents($str);
	$output = array();
	preg_match_all("/<link(.*?)href=\"(.*?)\"(.*?)>/", $html, $out,PREG_PATTERN_ORDER);
	if ($html) {
		foreach($out[0] as $key => $val):
			if (strstr($val, 'application/rss+xml')) {
				$output[] = $out[2][$key];
			}
		endforeach;
		return $output;
	}
	else {
		return $output;
	}
}

function OLDis_rss($str) {
	$headers = 0;
	$headers = @get_headers($str, 1);
	
	if ($headers) {

		if (isset($headers['Content-Type'])) {
				$xml = 0;
				$xml = @file_get_contents($str);
				
				if ($xml) {
					return preg_match("/<rss(.*?)>(.*?)<\/rss>/s", $xml, $out);
				}

		}
	}
	return 0;
}

function OLDis_atom($str) {
	$headers = 0;
	$headers = @get_headers($str, 1);
	
	if ($headers) {

		if (isset($headers['Content-Type'])) {
				
				$xml = 0;
				$xml = @file_get_contents($str);

				if ($xml) {
					return preg_match("/<feed(.*?)>(.*?)<\/feed>/s", $xml, $out);
				}
		}
	}
	return 0;
}

function OLDfetch_rss_feed($str) {
	$xml = file_get_contents($str);
	$s = new SimpleXMLElement($xml);
	
	$feed = array();
	
	if (isset($s->channel) && isset($s->channel->item)) {
		foreach($s->channel->item as $key => $val):
			
			$info['source'] = $s->channel->title;
			
			if (isset($val->link)) {
				$info['link'] = (string) $val->link;
			}
			
			if (isset($val->title)) {
				$info['title'] = (string) $val->title;
			}
			
			if (isset($val->description)) {
				$info['description'] = (string) $val->description;
			}
			
			$feed[] = $info;
		endforeach;
	}
	
	return $feed;
}

function OLDfetch_atom_feed($str) {
	$xml = file_get_contents($str);
	$s = new SimpleXMLElement($xml);
	
	$feed = array();
	
	if (isset($s->entry)) {
		foreach($s->entry as $key => $val):
		
			$info['source'] = $s->title;
		
			$tmp = (array) $val->link;

			if (isset($tmp['@attributes']['href'])) {
				$info['link'] = (string) $tmp['@attributes']['href'];
			}
			
			if (isset($val->title)) {
				$info['title'] = (string) $val->title;
			}
			
			if (isset($val->content)) {
				$info['description'] = (string) $val->content;
			}
			
			$feed[] = $info;
		endforeach;
	}
	
	return $feed;
}

function get_link_tags($str) {
	$html = 0;
	$html = @file_get_contents($str);

	$output = array();
	preg_match_all("/<link(.*?)href=\"(.*?)\"(.*?)>/", $html, $out,PREG_PATTERN_ORDER);

	if ($html) {

		foreach($out[0] as $key => $val):
			if (strstr($val, 'application/rss+xml')) {
				return $out[2][$key];
				break;
			}
			elseif (strstr($val, 'text/xml')) {
				return $out[2][$key];
				break;
			}
		endforeach;
	}
	
	return 0;
}



function is_xml($str) {
	
	$headers = 0;
	$headers = @get_headers($str, 1);
	
	if (isset($headers['Content-type'])) {
		$headers['Content-Type'] = $headers['Content-type'];
	}

	if (isset($headers['Content-Type'])) {
		// Header content type can either return an array or a single value
		// We make single values into an array then we loop through the 
		// array looking to see if the header content type contains XML
		if (!is_array($headers['Content-Type'])) {
			$headers['Content-Type'] = array($headers['Content-Type']);
		}
		
		foreach ($headers['Content-Type'] as $key => $i):
			if (preg_match("/application\/(.*?)xml/s", (string) $i)) {
				return 1;
				break;
			}
			elseif (preg_match("/text\/xml/s", (string) $i)) {
				return 1;
				break;
			}
		endforeach;
	}

	return 0;
}

function fetch_xml_feed($str) {
	
	$xml = file_get_contents($str);
	$s = new SimpleXMLElement($xml);
	
	$feed = array();

	if (isset($s->channel) && isset($s->channel->item)) { // RSS
		
		if (isset($s->channel->title)) {
			$feed['title'] = (string) $s->channel->title;
		}

		if (isset($s->channel->description)) {
			$feed['description'] = (string) $s->channel->description;
		}

		if (isset($s->channel->link)) {
			$feed['link'] = (string) $s->channel->link;
		}

		if (isset($s->channel->image->url)) {
			$feed['image'] = (string) $s->channel->image->url;
		}

		$feed['items'] = array();
		
		foreach($s->channel->item as $key => $val):
			
			if (isset($val->pubDate)) {
				$item['datetime'] = (string) $val->pubDate;
				$item['datetime'] = strtotime($item['datetime']);
			}
			
			if (isset($val->title)) {
				$item['title'] = (string) $val->title;
			}

			if (isset($val->link)) {
				$item['link'] = (string) $val->link;
			}
			
			if (isset($val->description)) {
				$item['description'] = (string) $val->description;
			}
			
			$feed['items'][] = $item;
		endforeach;
	}
	elseif (isset($s->entry)) { // ATOM
		
		if (isset($s->title)) {
			$feed['title'] = (string) $s->title;
		}

		if (isset($s->description)) {
			$feed['description'] = (string) $s->description;
		}

		if (isset($s->link)) {
			$feed['link'] = (string) $s->link;
		}

		if (isset($s->logo)) {
			$feed['image'] = (string) $s->logo;
		}

		$feed['items'] = array();
		
		foreach($s->channel->item as $key => $val):
			if (isset($val->updated)) {
				$item['datetime'] = (string) $val->updated;
				$item['datetime'] = strtotime($item['datetime']);
			}

			if (isset($val->title)) {
				$item['title'] = (string) $val->title;
			}
			
			$tmp = (array) $val->link;
			
			if (isset($tmp['@attributes']['href'])) {
				$item['link'] = (string) $tmp['@attributes']['href'];
			}
			
			if (isset($val->content)) {
				$item['description'] = (string) $val->content;
			}
			
			$feed['items'][] = $item;
		endforeach;
	}
	elseif (isset($s->channel) && isset($s->item)) { // RDF
		
		if (isset($s->channel->title)) {
			$feed['title'] = (string) $s->channel->title;
		}

		if (isset($s->channel->description)) {
			$feed['description'] = (string) $s->channel->description;
		}

		if (isset($s->channel->link)) {
			$feed['link'] = (string) $s->channel->link;
		}

		if (isset($s->channel->image->url)) {
			$feed['image'] = (string) $s->channel->image->url;
		}

		$feed['items'] = array();
		
		foreach($s->item as $key => $val):
			
			$dc = $val->children('http://purl.org/dc/elements/1.1/');

			if (isset($dc->date)) {
				$item['datetime'] = (string) $dc->date;
				$item['datetime'] = strtotime($item['datetime']);
			}
			
			if (isset($val->title)) {
				$item['title'] = (string) $val->title;
			}

			if (isset($val->link)) {
				$item['link'] = (string) $val->link;
			}
			
			if (isset($val->description)) {
				$item['description'] = (string) $val->description;
			}
			
			$feed['items'][] = $item;
		endforeach;
	}
	
	return $feed;
}

?>