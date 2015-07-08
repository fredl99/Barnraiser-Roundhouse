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


// some default values
define("OPENID_DH_MODULUS", '155172898181473697471232257763715539915724801966915404479707795314057629378541917580651227423698188993727816152646631438561595825688188889951272158842675419950341258706556549803580104870537681476726513255747040765857479291291572334510643245094715007229621094194349783925984760375594985848253359305585439638443');
define("OPENID_DH_GEN", 2);
define("OPENID_EXPIRES_IN", 10000000);
define("DEBUG", 0);


class OpenidConsumer {

	// see section 8.3 of specification
	var $association_type = 'HMAC-SHA1';
	//var $association_type = 'HMAC-SHA256';
	
	// see section 8.4 of specification
	var $association_session_type = 'DH-SHA1';
	//var $association_session_type = 'DH-SHA256';
	
	// nr of bytes in the hmac-function
	var $blocksize = 64;

	// All references to specification numbers refer to version 2.0 of the 
	// OpenID authentication specification unless otherwise stated.
	
	
	// constructor
	function OpenidConsumer($db) {
		$this->storage = $db;

		$this->_openid_dh_modulus = OPENID_DH_MODULUS;
		$this->_openid_dh_gen = OPENID_DH_GEN;
	}
	
	// Spec 4.2: Integer representations - Converts $n into a twos
	// compliment of a binary number (encoding it)
	function btwocEncode($long) {
		$cmp = bccomp($long, 0);

		if ($cmp == 0) {
			return "\x00";
		}

		$bytes = array();

		while (bccomp($long, 0) > 0) {
			array_unshift($bytes, bcmod($long, 256));
			$long = bcdiv($long, pow(2, 8));
		}

		if ($bytes && ($bytes[0] > 127)) {
			array_unshift($bytes, 0);
		}

		$string = '';
		foreach ($bytes as $byte) {
			$string .= pack('C', $byte);
		}

		return $string;
	}
	
	// Spec 4.2: Integer representations - Converts $n into a binary
	// from a twos compliment (decoding it)
	function btwocDecode($str) {
		$bytes = array_merge(unpack('C*', $str));
		$n = 0;

		foreach ($bytes as $byte) {
			$n = bcmul($n, pow(2, 8));
			$n = bcadd($n, $byte);
		}
		return $n;
	}
	
	// bitwise exclusive or function - either / or
	// takes 1100 and compares to 1001 to get 1010 
	function _xor($x, $y) {
		$a = '';
		for($i=0; $i < strlen($y); $i++) { 
			$a .= $x[$i] ^ $y[$i];
		}
		return $a;
	}
	
	// encryption-function... for more info read http://en.wikipedia.org/wiki/HMAC
	// is used when creating the signature where $key is assoc_handle/mac-key and $data is key-values (tokens)
	// (see 4.1.1 of specification)
	function hmac($key, $data) {
	
		switch($this->association_type) {
			case 'HMAC-SHA256':
				$hash_function = 'sha256';
			break;
			case 'HMAC-SHA1':
				$hash_function = 'sha1';
			break;
			default:
				$hash_function = 'sha1';
		}
		return hash_hmac($hash_function, $data, $key, true);
	}
	
	// calculates g^x mod p (x=secret number at server) and returns it encoded binary
	function dh_public() {
		$secret_key = '';
		for($i = 0; $i < rand(1, strlen($this->_openid_dh_modulus)-1); $i++) {
			if ($i == 0) {
				$secret_key .= rand(1, 9);
			}
			else {
				$secret_key .= rand(0, 9);
			}
		}
		$_SESSION['openid_secret_key'] = $secret_key;
		
		return base64_encode($this->btwocEncode(bcpowmod($this->_openid_dh_gen, $secret_key, $this->_openid_dh_modulus)));
	}
	
	// normalizes an url
	function normalize($url) {
	
		if (substr($url, 0, strlen('https://')) != 'https://') {
			if (substr($url, 0, strlen('http://')) != 'http://') {
				$url = 'http://' . $url;
			}
			$this->openid_prefix = 'http://';
		}
		else {
			$this->openid_prefix = 'https://';
		}
		
		if (substr($url, -9) == 'index.php') {
			$url = substr($url, 0, -9);
		}

		if (substr($url, -1) == '#') {
			$url = substr($url, 0, strlen($url) - 1);
		}
		
		if (strpos(substr($url, strlen($this->openid_prefix), strlen($url)), '/')) {
			// do nothing
		}
		elseif (strpos(substr($url, strlen($this->openid_prefix), strlen($url)), ':')) {
			// do nothing
		}
		else {
			$url .= '/';
		}

		return $url;
	}
	
	
	// curl-function that senda data to an openid-server
	function _send($data, $method = 'POST', $url=null) {
		
		if (!isset($url)) {
			$url = $this->openid_url_server;
		}
		
		$s = '?';
		if (strpos($url, $s)) {
			$s = '&';
		}
		
		if ($method == 'GET') {
			$url .= $s . http_build_query($data);
		}
	
		$curl = curl_init($url);
		
		if (!ini_get("safe_mode")) {
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		}
		
		if ($method == 'POST') {
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
		}
		else {
			curl_setopt($curl, CURLOPT_HTTPGET, true);
		}
		
		curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0); // this solves the issues with the chunked encoding
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($curl);
		//print_R($response); exit;
		if (curl_errno($curl) == 0){
			return $response;
		}
		else {
			return 0;
		}
	}


	// CONSUMER METHODS START -------------------------------------------------------

	//var $optional_fields = array(); // here you should put nickname, email, etc... (no prefix of openid)
	//var $required_fields = array(); // here you should put nickname, email, etc... (no prefix of openid)
	
	
	// this sends post to a server
	function associate() {
	
		$data_to_send = array();
	
		if (isset($this->openid_version) && $this->openid_version == 2) {
			$data_to_send['openid.ns'] = 'http://specs.openid.net/auth/2.0';
		}
		
		$data_to_send['openid.mode'] = 'associate';
		$data_to_send['openid.assoc_type'] = $this->association_type;
		$data_to_send['openid.session_type'] = $this->association_session_type;
		
		$_SESSION['association_type'] = $data_to_send['openid.assoc_type'];
		$_SESSION['association_session_type'] = $data_to_send['openid.session_type'];
		
		if ($this->association_session_type != 'no-encryption') {
			$data_to_send['openid.dh_modulus'] = base64_encode($this->btwocEncode($this->_openid_dh_modulus)); 
			$data_to_send['openid.dh_gen'] = base64_encode($this->btwocEncode($this->_openid_dh_gen));
			$data_to_send['openid.dh_consumer_public'] = $this->dh_public();
		}
		
		$result = $this->_send($data_to_send);
		//print_R($result);
		if ($result) { 
			$data_to_return = array(); 
			foreach(explode("\n", trim($result)) as $key => $r) {
				$tmp = explode(':', $r);
				if (isset($tmp[0], $tmp[1])) {
					$data_to_return[$tmp[0]] = $tmp[1]; // we need to store this in a smart way later...
				}
				else {
					return 0;
				}
			}
			
			if (isset($data_to_return['assoc_handle'])) {
				$_SESSION['openid_assoc_handle'] = $data_to_return['assoc_handle'];
			}
			else { // no handle was sent, so something is wrong
				// new code (080205)
				if (isset($data_to_return['mode']) && $data_to_return['mode'] == 'error') {
					if (isset($data_to_return['error_code']) && $data_to_return['error_code'] == 'unsupported-type') {
						if (isset($data_to_return['assoc_type'])) {
							$this->association_type = $data_to_return['assoc_type'];
						}
						else {
							$this->association_type = 'HMAC-SHA1';
						}
			      
						if (isset($data_to_return['session_type'])) {
							$this->association_session_type = $data_to_return['session_type'];
						}
						else {
							$this->association_session_type = 'no-encryption';
						}
						return $this->associate();
					}
				}
				return 0; // failed to associate
			}
 
			if ($this->association_session_type != 'no-encryption') {
				$enc_mac_key = base64_decode($data_to_return['enc_mac_key']);
				$composite_key = bcpowmod($this->btwocDecode(base64_decode($data_to_return['dh_server_public'])), $_SESSION['openid_secret_key'], $this->_openid_dh_modulus);
			
			if ($data_to_send['openid.assoc_type'] == 'HMAC-SHA256') {
				$hash_function = 'sha256';
			}
			elseif ($data_to_send['openid.assoc_type'] == 'HMAC-SHA1') {
				$hash_function = 'sha1';
			}
			else {
				$hash_function = 'sha1';
			}
			
			  $sha_composite_key = hash($hash_function, $this->btwocEncode($composite_key), true);

			  $mac_key = '';
			
			  for ($i = 0; $i < strlen($enc_mac_key); $i++) {
				  $mac_key .= chr(ord($enc_mac_key[$i]) ^ ord($sha_composite_key[$i]));
			  }

			  $_SESSION['openid_mac_key'] = base64_encode($mac_key); // store the decrypted mac-key here
			  $_SESSION['openid_enc_mac_key'] = $enc_mac_key; // for debugging. Not really neccesary...?
			  return 1;
			}
			else {
			  /* DUMB MODE HERE */
			   $_SESSION['openid_mac_key'] = $data_to_return['mac_key'];
			   $_SESSION['openid_assoc_handle'] = $data_to_return['assoc_handle'];
			  return 1;
			}
		}
		return 0;
	}
	
	// this function is far away from done. Should be completly rewritten to meet 2.0 spec.
	function checkid_setup() {
		$data_to_send = array();
		
		if (isset($this->openid_version) && $this->openid_version == 2) {
		  $data_to_send['openid.ns'] = 'http://specs.openid.net/auth/2.0';
		}
		
		$data_to_send['openid.mode'] = 'checkid_setup';
		$data_to_send['openid.identity'] = $this->openid_url;
		
		if (isset($this->openid_version) && $this->openid_version == 2) {
		  $data_to_send['openid.claimed_id'] = $this->openid_url; // check this later
		}
		
		$data_to_send['openid.assoc_handle'] = $_SESSION['openid_assoc_handle'];
		
		if (isset($this->openid_return_to)) {
			$data_to_send['openid.return_to'] = $this->openid_return_to;
		}
		else {
			$data_to_send['openid.return_to'] = $this->openid_prefix . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'];
		}

		if (isset($this->openid_realm)) {
			$openid_realm = $this->openid_realm;
		}
		else {
			$openid_realm = $this->openid_prefix . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'];
		}

		if (isset($this->openid_version) && $this->openid_version == 2) {
			$data_to_send['openid.realm'] = $openid_realm;
		}
		else {
			$data_to_send['openid.trust_root'] = $openid_realm;
		}
		
/*
		if (!empty($this->optional_fields)) {
			$data_to_send['openid.sreg.optional'] = implode(',', $this->optional_fields);
		}
		
		if (!empty($this->required_fields)) {
			$data_to_send['openid.sreg.required'] = implode(',', $this->required_fields);
		}
		
		// $this->openid_url_server points to 'the server' (which can be the same url as identity, but
		// it doesnt need to be that)
		// $this->openid_url_server probably needs to be normalized.
*/
		$s = '?';
		if (strpos($this->openid_url_server, $s)) {
			$s = '&';
		}
		
		header('location: ' . $this->openid_url_server . $s . http_build_query($data_to_send));
		exit;
	}
	
	// function validates the decrypted mac-key with recevied signature.
	// this function is probably far from done yet.
	function id_res() {
	
		$this->association_type = $_SESSION['association_type'];
		$this->association_session_type = $_SESSION['association_session_type'];

		$tokens = '';
		$signed = explode (',', $_GET['openid_signed']);
		foreach($signed as $key => $v) {
			$tokens .=  $v . ':' . $_GET['openid_' . str_replace('.', '_', $v)] . "\n"; //do we need to rewrite this?
		}

		// with the hmac-function we check if there was a match using the mac-key+tokens (above) to the signature
		// we got from the server
		if (base64_encode($this->hmac(base64_decode($_SESSION['openid_mac_key']), $tokens)) == $_GET['openid_sig']) {
			// match ok. proceed from here
			return true;
		}
		else {
			// signature not met.
			return false;
		}
	}
	
	// This function should do lookup+validation and set some
	// private vars to this class. Lots of stuff to do here.
	function discover($openid_url) {
		
		$openid_headers = @get_headers($openid_url);
		if ($openid_headers[0] == 'HTTP/1.1 200 OK' || $openid_headers[0] == 'HTTP/1.0 200 OK') {
			$openid_content = file_get_contents($openid_url);
			
			$this->openid_url = $openid_url;
			
			// OpenID 2.0
			$pattern = "/<link rel=\"openid2.local_id\" href=\"(.*?)\"/";
			
			if (preg_match($pattern, $openid_content, $matches)) {
				// openid delegation
				if (!empty($matches[1]) && $matches[1] != $openid_url) {
					return $this->discover($matches[1]);
				}
			}
			
			// OpenID 1.1
			$pattern = "/<link rel=\"openid.delegate\" href=\"(.*?)\"/";
			
			if (preg_match($pattern, $openid_content, $matches)) {
				// openid delegation
				if (!empty($matches[1]) && $matches[1] != $openid_url) {
					return $this->discover($matches[1]);
				}
			}
			
			$pattern2 = "/<link rel=\"openid2.provider\" href=\"(.*?)\"/";
			$pattern1 = "/<link rel=\"openid.server\" href=\"(.*?)\"/";
			
			if (preg_match($pattern2, $openid_content, $matches)) {
				$this->openid_url_server = $matches[1];
				$this->openid_version = 2;
			}
			elseif (preg_match($pattern1, $openid_content, $matches)) {
				$this->openid_url_server = $matches[1];
			}
			else {
				$this->openid_url_server = $this->openid_url;
			}

			/* continue... we want to check it $openid_url indeed is an openid-url + some othe stuff */
			return 1;
		}
		else {
			$GLOBALS['am_error_log'][] = array('openid_discovery_failed');
			return 0;
		}
	}
}

?>
