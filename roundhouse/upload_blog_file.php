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


include_once ("config/core.config.php");
define("SCRIPT_TEMPLATE_PATH", "template/");
define("SCRIPT_THEME_PATH", "theme/" . $core_config['script']['default_theme_name'] . "/");

// SESSION HANDLER --------------------------------------------------
session_name($core_config['php']['session_name']);
session_start();


if (isset($_SESSION['user_id']) && isset($_POST['submit_upload_blog_file'])) {

	if (empty($_FILES['frm_file']['error'])) {
		$allowable_mime_types = array("image/jpeg", "image/png", "image/gif");
		$mime_type_suffixes = array('jpg' =>'image/jpeg', 'png' =>'image/png', 'gif' =>'image/gif');
	
		$destination = $core_config['file']['dir'] . "blog/" . $_POST['blog_id'] . "/";
	
		if (!is_dir($destination)) {
			$oldumask = umask(0);
			@mkdir ($destination, 0770, 1);
			umask($oldumask);
		}
	
			
		// CHECK MIME TYPE ----------------------------------------------------------------
		if (function_exists('finfo_open')) {
			if (!empty($core_config['php']['magic_path'])) {
				$resource = finfo_open(FILEINFO_MIME, $core_config['php']['magic_path']);
			}
			else {
				$resource = finfo_open(FILEINFO_MIME);
			}
			$mime_type = finfo_file($resource, $_FILES['frm_file']['tmp_name']);
			finfo_close($resource);
		}
		elseif (function_exists('mime_content_type')) {
			$mime_type = mime_content_type($_FILES['frm_file']['tmp_name']);
		}
		else {
			$mime_type = $_FILES['frm_file']['type'];
		}
	
		// We use this to map IE-mimetype to standard mimetype
		$mime_map = array(array("from" => "image/pjpeg", "to" => "image/jpeg"));
	
		foreach($mime_map as $i):
			if ($i['from'] == $mime_type) {
				$mime_type = $i['to'];
			}
		endforeach;
	
		// Is the mime-type allowed?
		if (!validateMimeType($allowable_mime_types, $mime_type)) {
			exit;
		}
	
		// create file name
		foreach($mime_type_suffixes as $key => $mts) {
			if ($mts == $mime_type) {
				$suffix = $key;
			}
		}
			
		// We create thumbnails
		if ($mime_type == "image/gif" || $mime_type == "image/jpeg" || $mime_type == "image/png") {
	
			$image_size = getimagesize($_FILES['frm_file']['tmp_name']);
	
			// we create an avatar
			$type  = explode('/', $mime_type);
			$imagecreatefrom = 'imagecreatefrom' . $type[1];
			$image = 'image' . $type[1];
			$new_image = $imagecreatefrom($_FILES['frm_file']['tmp_name']);
	
			// make_name
			$filename = trim($_FILES['frm_file']['name']);
			//strip suffix
			$filename = substr($filename, 0, -4);
			$filename = mb_strtolower($filename, mb_detect_encoding($filename));
			$filename = preg_replace('/[^a-z0-9_]/i','',$filename);
			
			$filename = $filename . "." . $suffix;
	
			if (!empty($_POST['width']) && is_numeric($_POST['width'])) {
				$width = $_POST['width'];
			}
			else {
				$width = $image_size[0];
			}
	
			$height = ($width / $image_size[0]) * $image_size[1];
	
			$blank_image = ImageCreateTrueColor($width, $height);
			$col = imagecolorallocate($blank_image, 255, 255, 255);
			imagefilledrectangle($blank_image, 0, 0, $width, $height, $col);
			$newimage = ImageCopyResampled($blank_image, $new_image, 0, 0, 0, 0, $width, $height, $image_size[0], $image_size[1]);
			$image($blank_image, $destination . $filename);
			
		}
	}
	
	$_REQUEST['blog_id'] = $_POST['blog_id'];
}


function validateMimeType($mimes, $mime_type) {
	foreach($mimes as $m) {
		if ($m == $mime_type) {
			return 1;
		}
	}
	return 0;
}

if (isset($_SESSION['user_id']) && !empty($_REQUEST['blog_id'])) {
	$files = glob('asset/blog/' . $_REQUEST['blog_id'] . '/*');
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-GB" lang="en-GB">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="Content-Language" content="en" />

	<style type="text/css">
	<!--
	@import url(<?php echo SCRIPT_THEME_PATH;?>css/editor.css);
	-->
	</style>
	
	<script type="text/javascript">
		function formatTag(id, src) {
			tag = '<img src="' + src + '" alt="" border="0" />';
			document.getElementById('display_image_tag').value = tag;
			document.getElementById('display_image_tag_container').style.display = "block";

			
			var L = document.getElementsByTagName('img')
			for(var i=0;i<L.length;i++) {
				L[i].className='image_lowlight';
			}

			document.getElementById(id).className = 'image_highlight';

		}

		
	</script>
</head>

<body id="box_editor_file">
	<?php
	if (!empty($files)) {
	?>
	<div class="box">
		<div class="box_header">
			<h1><?php echo _("Pictures");?></h1>
		</div>
		
		<div class="box_body">
			<div id="blog_files">
				<?php
				foreach ($files as $key => $i):
				?>	
				<div class="file">
					<img src="/<?php echo $i?>" class="image_lowlight" id="image_<?php echo $key?>" width="76" alt="" border="0" onclick="javascript:formatTag('image_<?php echo $key?>', this.src);" />
				</div>
				<?php
				endforeach;
				?>
			</div>
		
			<div id="display_image_tag_container">
				<p>
					<?php echo _("Copy the following tag into your blog entry.");?>
				</p>
				
				<input type="text" id="display_image_tag" onclick="javascript:this.focus();this.select();" readonly="true" />
			</div>
		</div>
	</div>
	<?php }?>
</body>
</html>