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


// PROCESS
// 1 - We authenticate OPenID
// 2. If authenticated we check if in Db - If yes we log you in / If no we take you to simple registration

require_once ('class/Openid.class.php');
$openid_consumer = new OpenidConsumer($db);

//$_SESSION['openid_identity'] = $openid_consumer->normalize('http://' . $_SERVER['SERVER_NAME']);
		

if (isset($_POST['submit_openid_login'])) { // we connect

	$_POST['openid_login'] = trim($_POST['openid_login']);
	$_POST['openid_login'] = $openid_consumer->normalize($_POST['openid_login']);
	
	unset($_SESSION['openid_login']);
	$_SESSION['openid_login'] = $_POST['openid_login'];

	if ($openid_consumer->discover($_POST['openid_login'])) { // we did discover a server
		if($openid_consumer->associate()) { // association is ok
			$openid_consumer->checkid_setup(); // do the setup
		}
		else {
			$GLOBALS['am_error_log'][] = array('Failed to associate with your OpenID server');
		}
	}
	else {
			$GLOBALS['am_error_log'][] = array('Failed to discover your OpenID server');
	}
}
elseif (isset($_GET['openid_mode']) && $_GET['openid_mode'] == 'id_res') { // we get data back from the server
	if ($openid_consumer->id_res()) { // was the result ok?
		
		// SET CONNECTION
		$openid = $_GET['openid_identity'];
			
		if(substr($openid,-1,1) == '/'){
			$openid = substr($openid, 0, strlen($openid)-1);
		}
			
		$_SESSION['user_authenticated_openid'] = $openid;

		$query = "
			SELECT user_id, user_email, user_name, user_dob, user_location,
			user_openid 
			FROM " . $db->prefix . "_user 
			WHERE 
			user_openid=" . $db->qstr($openid)
		;

		$result = $db->Execute($query, 1);

		if (isset($result[0]['user_id'])) { // We log them in
			$_SESSION['user_id'] = $result[0]['user_id'];
			$_SESSION['user_email'] = $result[0]['user_email'];
			$_SESSION['user_name'] = $result[0]['user_name'];
			$_SESSION['user_dob'] = $result[0]['user_dob'];
			$_SESSION['user_location'] = $result[0]['user_location'];
			$_SESSION['user_openid'] = $result[0]['user_openid'];
			
			$query = "
				UPDATE " . $db->prefix . "_user
				SET user_last_login_datetime=NOW()
				WHERE user_id=" . $_SESSION['user_id']
			;
			$db->Execute($query);
	
			header('location: /manage');
			exit;
			
		}
		else { // we take them to simple registration
			header('location: /register_openid');
			exit;
		}
	}
	else {
		$GLOBALS['am_error_log'][] = array('Failed to authenticate with your OpenID server');
	}
	unset($_SESSION['openid_login']);
}
