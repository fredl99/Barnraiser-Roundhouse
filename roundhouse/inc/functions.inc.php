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


// URL routing into array
function routeURL ($webspace_name=null) {

	$document_root = trim(dirname($_SERVER['PHP_SELF']), '/');
	$script_name = $_SERVER['PHP_SELF'];

	$request_uri = substr($_SERVER['REQUEST_URI'], strlen($document_root) + 1);
	
	$tmp = strpos($request_uri, '?');
	
	if ($tmp) {
		$request_uri = substr($request_uri, 0, $tmp);
	}
	
	$request_arr = explode('/', $request_uri);
	
	foreach ($request_arr as $key => $i):
		if (empty($i)) {
			unset($request_arr[$key]);
		}
	endforeach;

	return $request_arr;
}

function tag_cmp($a, $b) {
	return $a['tag_name'] > $b['tag_name'];
}


function timeDiff($timestamp){
	
	$timestamp = strtotime($timestamp);

        $now = time();

        //If the difference is positive "ago" - negative "away"
        ($timestamp >= $now) ? $action = 'om' : $action = 'sedan';

        switch($action) {
        case 'om':
                $diff = $timestamp - $now;
                break;
        case 'sedan':
        default:
                // Determine the difference, between the time now and the timestamp
                $diff = $now - $timestamp;
                break;
        }

        // Set the periods of time
        $periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");

        // Set the number of seconds per period
        $lengths = array(1, 60, 3600, 86400, 604800, 2630880, 31570560, 315705600);

        // Go from decades backwards to seconds
        for ($val = sizeof($lengths) - 1; ($val >= 0) && (($number = $diff / $lengths[$val]) <= 1); $val--);

        // Ensure the script has found a match
        if ($val < 0) $val = 0;

        // Determine the minor value, to recurse through
        $new_time = $now - ($diff % $lengths[$val]);

        // Set the current value to be floored
        $number = floor($number);

        // If required create a plural
        if($number != 1) {
        	if ($periods[$val] == 'hour') {
        		$periods[$val] = "hours";
        	}
        	elseif ($periods[$val] == 'day') {
        		$periods[$val] = "days";
        	}
        	elseif ($periods[$val] == 'week') {
        		$periods[$val] = "weeks";
        	}
        	elseif ($periods[$val] == 'year') {
        		$periods[$val] = "years";
        	}
        	else {
	        	$periods[$val].= "s";
	        }
        }

        // Return text
        $text = sprintf("%d %s ", $number, $periods[$val]);
        return $text . "ago";
}

function parse($str) {

	$str = strip_tags($str, '<object><param><embed><a><img><h1><h2><h3><h4><h5><h6><hr><code>');
	$str = nl2br($str);

	$str = link_parse($str); // we need to linkparse after all other links (youtube) has been parsed
	
	// some hacks
	$str = str_replace("</h1><br />", "</h1>", $str);
	$str = str_replace("</h2><br />", "</h2>", $str);
	$str = str_replace("</h3><br />", "</h3>", $str);
	$str = str_replace("</h4><br />", "</h4>", $str);
	$str = str_replace("</h5><br />", "</h5>", $str);
	$str = str_replace("</h6><br />", "</h6>", $str);

	return addslashes($str);
}

function link_parse($str) {
	
	$pattern = '#(^|[^"\'=\]>]{1})(http|HTTP|ftp)(s|S)?://([^\s<>\.]+)\.([^\s<>]+)#sm';
	
	if (preg_match_all($pattern, $str, $matches, PREG_PATTERN_ORDER)) { 
		
		foreach ($matches[0] as $key => $val) {

			$val_trimmed = trim($val);

			if (strlen($val_trimmed) > 30) {
				$title = substr($val_trimmed, 0, 30) . '...';
			}
			else {
				$title = $val_trimmed;
			}

			$link = '<a href="' . $val_trimmed . '">' . $title . '</a>';
			$count = 1;
			$str = str_replace ($val_trimmed, $link, $str, $count);
		}
	}

	return $str;
}

// used in register and account to format the DOB
function formatDate($y, $m, $d) {
	if (empty($y)) {
		$GLOBALS['script_error_log'][] = _("Please provide us with a valid year.");
		return 0;
	}
	
	if (empty($m)) {
		$GLOBALS['script_error_log'][] = _("Please provide us with a valid month.");
		return 0;
	}
	
	if (empty($d)) {
		$GLOBALS['script_error_log'][] = _("Please provide us with a valid day.");
		return 0;
	}
	
	return date('Y-m-d', mktime(0, 0, 0, $m, $d, $y));
}


// used in register and account
function checkEmail($email) {
  $result = TRUE;
  if(!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$", $email)) {
    $result = FALSE;
  }
  return $result;
}

function parse_tag($tag) {
	$tag = mb_strtolower($tag, mb_detect_encoding($tag));
	$look_for = array('å', 'ä', 'ö', ' ', '/', '\\', '&');
	$replace_width = array('a', 'a', 'o', '-', '-', '-', '-');
	
	
	return str_replace($look_for, $replace_width, $tag);
}

function formatIdentityName($id) {
	$id = strtolower($id);

	if (!preg_match('/^[a-zA-Z0-9.~]+$/', $id)) {
		$GLOBALS['script_error_log'][] = _("Blog name format is not correct. Only a-z, A-Z and 0-9 are allowed.");
	}

	if (strlen($id) < 2) {
        $GLOBALS['script_error_log'][] = _("Blog name needs to be at least 2 characters long.");
    }

	if (strlen($id) > 50) { // link too long
		$GLOBALS['script_error_log'][] = _("Blog name can only be 50 characters long.");
	}
	return $id;
}


function gen_maptcha() {
	$numbers = array();
	$numbers['ascii'] = array(0,1,2,3,4,5,6,7,8,9,10);
	$numbers['words'] = array('zero','one','two','three','four','five','six','seven','eight','nine','ten');

	$operators = array();
	$operators['ascii'] = array('+','-','*');
	$operators['words'] = array('plus','minus','times');
	
	$_SESSION['maptcha'] = "";
	
	$m = 'ascii';
	if (rand(0,1)) {
		$m = 'words';
	}
	$n1 = rand(0, count($numbers[$m])-1);
	
	$x = $numbers[$m][$n1];
	
	$m = 'ascii';
	if (rand(0,1)) {
		$m = 'words';
	}
	$n2 = rand(0, count($numbers[$m])-1);
	
	$y = $numbers[$m][$n2];
	
	$m = 'ascii';
	if (rand(0,1)) {
		$m = 'words';
	}
	$n3 = rand(0, count($operators[$m])-1);
	
	$o = $operators[$m][$n3];
	eval('$_SESSION[\'maptcha\']=' . intval($numbers['ascii'][$n1]) . $operators['ascii'][$n3] . intval($numbers['ascii'][$n2]) . ';');
	
	if (rand(0,1)) {
		return 'Calculate this: ' . $x . ' ' . $o . ' ' .$y;
	}
	elseif (rand(0,1)) {
		return 'Solve this equation: ' . $x . ' ' . $o . ' ' .$y;
	}
	elseif (rand(0,1)) {
		return 'Work this out: ' . $x . ' ' . $o . ' ' .$y . " ?";
	}
	else {
		return 'Solve this little puzzle: ' . $x . ' ' . $o . ' ' .$y;
	}
}

function match_maptcha($answer) {
	return intval($answer) == intval($_SESSION['maptcha']);
}


// scan a directory for directory names
function barnraiser_scandir($dir) {

	$dirnames = array();
	
	$entries = @scandir($dir);
	
	if (!empty($entries)) {
		foreach($entries as $i):
			if ($i != '.' && $i != '..' && $i != 'CVS' && $i != '.DS_Store') {
				array_push($dirnames, $i);
			}
		endforeach;
	}

	return $dirnames;
}


// Makes the webspace header title image
function makeThemeHeader($path, $user_id, $theme, $title) {
	// we get require vars needed for this function
	if (is_readable('theme/' . $theme . '/theme_header.inc.php')) {
		require('theme/' . $theme . '/theme_header.inc.php');
	}
	else {
		return false;
	}

	$color = Hex2RGB($color);
	$background_color = Hex2RGB($background_color);

	$font = 'theme/' . $theme . '/font/' . $font;
	
	$img_width = 760;
	$img_height = 40 + $font_size;
	$img_base = 20 + $font_size;
	
	
	if (isset($title, $font, $color, $background_color)) {
		$title = stripslashes($title);
		
		$im = imagecreatetruecolor($img_width, $img_height);
		
		$t_color = imagecolorallocate($im, $color[0], $color[1], $color[2]);
		$b_color = imagecolorallocate($im, $background_color[0], $background_color[1], $background_color[2]);
		imagefilledrectangle($im, 0, 0, $img_width, $img_height, $b_color);
		imagettftext($im, $font_size, 0, 10, $img_base, $t_color, $font, $title);


		// OK TO HERE
		$dir = $path . '/titles/';
		$fname = $user_id . '.png';
		
		@unlink($dir . '/' . $fname);
		
		
		 @imagepng($im, $dir . '/' . $fname);
	}
}


// Used by makeThemeHeader
function Hex2RGB($color){
	$color = str_replace('#', '', $color);
	if (strlen($color) != 6){ return array(0,0,0); }
		$rgb = array();
		for ($x=0;$x<3;$x++){
			$rgb[$x] = hexdec(substr($color,(2*$x),2));
	}
	return $rgb;
}

?>