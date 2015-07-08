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


class Database {

	// the constructor
	// Tom Calthrop, 26th March 2007
	//
	function Database($db_core_config) {
		$this->db_config = $db_core_config;
		$this->prefix = $db_core_config['prefix'];
	} //EO Database


	function newConnection() {
		//we connect to the database
		$this->connection = @mysql_connect($this->db_config['host'], $this->db_config['user'] , $this->db_config['pass']);

		if (!is_resource($this->connection)) {
			$GLOBALS['am_error_log'][] = array('db_error', mysql_error());
		}
		else {
			//we select the database
			$db_selected = mysql_select_db($this->db_config['db'], $this->connection);
			if (!$db_selected) {
				$GLOBALS['am_error_log'][] = array('db_select_error', mysql_error());
			}
			else {
				$db->prefix = $this->db_config['prefix'];

				// set up database collation
				$query = "SET NAMES 'utf8'";
				$this->Execute($query);
				$query = "SET CHARACTER SET 'utf8'";
				$this->Execute($query);
			}
		}
	}

	function Execute($query, $rows=null, $offset=null) {
		
		$query = trim($query);

		if (!isset($this->connection)) {
			$this->newConnection();
		}
		
		if (isset($rows) && is_int($rows) && $rows > 0) { // is_numeric
			
			if (isset($offset) && is_int($offset) && $offset > 0) { // is_numeric
				$query .= " LIMIT " . $offset . ", " . $rows;
			}
			else {
				$query .= " LIMIT " . $rows;
			}
		}

		if (defined('AM_DEBUG_MODE')) {
			$query_start_microtime = microtime(true);
		}
		
		$this->resource = mysql_query($query, $this->connection);

		if (defined('AM_DEBUG_MODE')) {
			$query_duration_microtime = microtime(true) - $query_start_microtime;
			array_push($_SESSION['debug_log'], "<p>query: " . $query . "<br />duration: " . $query_duration_microtime . "</p>");
			unset($query_start_microtime, $query_duration_microtime);
		}
		
		if (!$this->resource) {
			$error = mysql_error().  "\n\n" . $query;
			$GLOBALS['am_error_log'][] = array('db_error', $error);
			echo $query; exit;
		}
		else {

			if (is_resource($this->resource)) { // SELECT, SHOW, DESCRIBE or EXPLAIN
				
				if (mysql_num_rows($this->resource) > 0) {
					$result = array();
					while($row = mysql_fetch_array($this->resource)) {
						$result[] = $row;
					}
					//mysql_free_result($resource);
					return $result;
				}
				else {
					return array(); // empty result
				}
			}
			return 1; // It's ok if we reach here!
		}
		return 0; // Not OK
	}
	
	// if magic quotes disabled, use stripslashes()
	function qstr($s) {
		
		if (!get_magic_quotes_gpc()) {
 			$s = addslashes($s);
		}
		return "'" . $s . "'";
	}

	function insertID() {
		if (isset($this->connection)) {
			if (is_resource($this->connection)) {
				return mysql_insert_id ($this->connection);
			}
		}
		return 0;
	}

	function insertDb($data, $table) {
	
		$query = "
			DESCRIBE " . $table
		;
		
		$result = $this->Execute($query);
		
		$query = "INSERT INTO " . $table . "(";
		
		foreach($data as $key => $d):
			$query .= $key . ", ";
		endforeach;
		
		$query = substr($query, 0, strlen($query) - 2);
		$query .= ") VALUES (";
		
		foreach($data as $key => $d):
			
			$data_type = "";
			for ($i = 0; $i < count($result); $i++) {
				if ($key == $result[$i]['Field']) {
					$data_type = $result[$i]['Type'];
				}
			}
			
			if ($data_type == 'datetime') {
				$query .= $this->qstr(date('Y-m-d H:i:s', $d)) . ", ";
			}
			elseif (is_string($d)) {
				$query .= $this->qstr($d, get_magic_quotes_gpc()) . ", ";
			}
			else {
				$query .= $d . ", ";
			}
		endforeach;
		
		$query = substr($query, 0, strlen($query) - 2);
		$query .= ")";
//echo $query; exit;

		if (defined('AM_DEBUG_MODE')) {
			$query_start_microtime = microtime(true);
		}
		
		$insert = $this->Execute($query);

		if (defined('AM_DEBUG_MODE')) {
			$query_duration_microtime = microtime(true) - $query_start_microtime;
			array_push($_SESSION['debug_log'], "<p>query: " . $query . "<br />duration: " . $query_duration_microtime . "</p>");
			unset($query_start_microtime, $query_duration_microtime);
		}

		return $insert;
	}

	function dbTime () {
		$dbtime = date("Y-m-d H:i:s");
		return $this->qstr($dbtime);
	}


	// validate PHP in webpages and blocks
	function check_tokens($str, $invalid_functions) {
		$tokens = token_get_all ('<?php ' . stripslashes(ltrim($str, 'php')) . ' ?>');
		
		foreach($tokens as $k => $v) {
			if (isset($v[0])) {
				if (is_int($v[0])) {
					
					switch ($v[0]) {
						case T_BAD_CHARACTER:
						case T_CHARACTER:
						case T_EVAL:
						case T_FILE:
						case T_INCLUDE:
						case T_INCLUDE_ONCE:
						case T_GLOBAL:
						case T_HALT_COMPILER:
						case T_REQUIRE:
						case T_REQUIRE_ONCE: return 0;
						case T_STRING: {
							if (in_array($v[1], $invalid_functions)) {
								return 0;
							}
						}
					}
				}
			}
		}
		return 1;
	}


	// Main parse-function - prepares data for database entry
	function am_parse($str) {
		if (!get_magic_quotes_gpc()) {
			$str = stripslashes($str);
		}
	
		$str = str_replace("\r", "", $str);
	
		
		// process <code>
		$pattern = "/<code>(.*?)?<\/code>/s";
	
		if (preg_match_all($pattern, $str, $code_blocks)) {
			
			if (!empty($code_blocks[1])) {
				foreach ($code_blocks[1] as $key => $i):
					$replace = $code_blocks[1][$key];
					$replace = trim($replace);
					$replace = htmlspecialchars($replace);
					$replace = "<code>\n" . $replace . "\r</code>";
					$str = str_replace($code_blocks[0][$key], $replace, $str);
				endforeach;
			}
		}
		
		$str = preg_replace("/<script[^>]*>.*<\/script>|<script[^>]*>/is","",$str);

		$str = $this->nls2p($str);
		$str = $this->_nl2br($str);
	
	
		// Making links active
		$pattern = '#(^|[^"\'=\]]{1})(http|HTTP|ftp)(s|S)?://([^\s<>\.]+)\.([^\s<>]+)#sm';
		$replace = '\\1<a href="\\2\\3://\\4.\\5">\\2\\3://\\4.\\5</a>';
		$str = preg_replace($pattern, $replace, $str);
		
		return $str;
	}
	
	
	// content parser
	function nls2p($str) {
		// temporary - we need to do something clever here to ignore inside code tags and
		// no wrap paras around html tags
		$str = str_replace('<p></p>', '', '<p>' . preg_replace('#([\r\n]\s*?[\r\n]){1,}#', '</p>$0<p>', $str) . '</p><br />');
		
		return $str;
	}
	
	
	// content parser
	function _nl2br($str) {
		$str = preg_replace( "/([0-9A-Za-z.!?])\n/", "$1<br />", $str);
	
		return $str;
	}
	
	
	// scan a directory for directory names
	function amscandir($dir) {

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
}
?>
