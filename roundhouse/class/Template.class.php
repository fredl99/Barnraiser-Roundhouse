<?php

// -----------------------------------------------------------------------
// This file is part of Roundhouse
//
// Copyright (C) 2003 - 2008 Barnraiser
// http://www.barnraiser.org/
// info@barnraiser.org
//
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2, or (at your option) any
// later version.
//
// This program is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
// General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with program; see the file COPYING. If not, write to the Free
// Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA
// 02110-1301, USA.
// -----------------------------------------------------------------------



class Template {
	var $vars; // contains all template variables
	

	// the constructor
	// Tom Calthrop, 26th March 2007
	//
	function Template() {
	
	} // EO Constructor


	// Set
	// Tom Calthrop, 26th March 2007
	// sets template variable
	//
	function set($key, $value) {
		$this->vars[$key] = $value;
	} // EO set


	// Fetch
	// Tom Calthrop, 26th March 2007
	// output buffers file
	//
	function fetch($file) {
		if (!empty($this->vars)) {
			extract($this->vars);
		}
		ob_start();
		include($file);
		$contents = ob_get_contents();
		ob_end_clean();
		return $contents;
	} // EO fetch


	// Parse
	// Tom Calthrop, 26th March 2007
	// output buffers file
	//
	function parse($content) {
		if (!empty($this->vars)) {
			extract($this->vars);
		}
		ob_start();
		eval("?>".$content);
		$content = ob_get_contents();
		ob_end_clean();
		
		return $content;
	} // EO fetch

	// render any database information for screen display
	function am_render($str) {

		$str = str_replace("<p>", "", $str);
		$str = str_replace("</p>", "", $str);
	
		// process <code>
		$pattern = "/<code>(.*?)?<\/code>/s";
	
		if (preg_match_all($pattern, $str, $code_blocks)) {
		
			if (!empty($code_blocks[1])) {
				foreach ($code_blocks[1] as $key => $i):
					$replace = str_replace("<br />", "", $code_blocks[1][$key]);
					$replace = "<code>" . $replace . "</code>";
					$str = str_replace($code_blocks[0][$key], $replace, $str);
				endforeach;
			}
		}
		$str = str_replace("<br />", "\n", $str);
	
		return trim($str);
	}
	
	function outputBody($str) {
		return stripslashes($str);
	}


	function getLanguage($key) {
		if (@array_key_exists($key, $this->lang)) {
			echo $this->lang[$key];
		}
		else {
			echo "MISSING LANGUAGE PACK ITEM";
			$GLOBALS['am_error_log'][] = array ('language_item_missing', $key);
		}
	}
}
?>
