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

// SESSION HANDLER --------------------------------------------------
session_name($core_config['php']['session_name']);
session_start();

// SETUP AROUNDMe CORE ----------------------------------------------
require_once('../class/Db.class.php');
$db = new Database($core_config['db']);

if (isset($_POST['blog_id'])) {
	$query = "
		SELECT c.blog_id, c.comment_user_name, c.comment_id, c.comment_body, 
		c.comment_email, c.comment_create_datetime 
		FROM " . $db->prefix . "_blog_comment c 
		WHERE 
		c.blog_id=" . $_POST['blog_id'] . "
		ORDER BY c.comment_create_datetime"
	;
	
	$result = $db->Execute($query);
}

$maptcha = gen_maptcha();

?>

<div class="comments">
	<?php
	if (!empty($result)) {
	?>
		<h3><?php echo _("Comments");?></h3>
		
		<ul>
			<?php
			foreach($result as $key => $val):
			?>
				<li>
					<?php
					$txt = _("Added by {user_name} {time_difference}.");
					$txt = str_replace("{user_name}", $val['comment_user_name'], $txt);
					$txt = str_replace("{time_difference}", timeDiff($val['comment_create_datetime']), $txt);
					echo $txt;
					?>
					<div class="comment">
						<?php echo stripslashes($val['comment_body']); ?>
					</div>
					<?php
					if (isset($_SESSION['user_id'])) {
					?>
					<div class="manage">
						<?php
						if (!empty($val['comment_email'])) {
						?>
						<a href="mailto:<?php echo $val['comment_email'];?>"><?php echo _("mail contributor");?></a>
						<?php }?>

						<form method="post">
						<input type="hidden" name="delete_comment_id" value="<?php echo $val['comment_id'];?>" />
						<input type="submit" name="delete_comment" value="<?php echo _("delete this comment");?>" />
						</form>
					</div>
					<?php }?>
				</li>
			<?php endforeach; ?>
		</ul>

		<div style="clear:both;"></div>
	<?php }?>
	
	<h3><label for="id_comment<?php echo $_POST['blog_id']; ?>"><?php echo _("Add your comment");?></label></h3>

	<form method="post" class="comment_form">
		<input type="hidden" name="blog_id" value="<?php echo $_POST['blog_id']?>" />
		<input type="hidden" name="blog_title" value="<?php echo $_POST['blog_title']?>" />
		
		<p>
			<label for="id_comment_user_name<?php echo $_POST['blog_id']; ?>"><?php echo _("Name");?></label>
			<input type="text" name="comment_user_name" id="id_comment_user_name<?php echo $_POST['blog_id']; ?>" value="<?php if (!empty($_POST['comment_name'])) { echo $_POST['comment_name'];}?>" />
		</p>

		<p>
			<label for="id_comment_email<?php echo $_POST['blog_id']; ?>"><?php echo _("Email (optional)");?></label>
			<input type="text" name="comment_email" id="id_comment_email<?php echo $_POST['blog_id']; ?>" value="<?php if (!empty($_POST['comment_email'])) { echo $_POST['comment_email'];}?>" />
		</p>

		<p class="note">
			<?php echo _("Your email is optional. Include if you want a reply.");?>
		</p>

		<p>
			<label for="id_comment<?php echo $_POST['blog_id']; ?>"><?php echo _("Comment");?></label><br />
			<textarea name="comment_body" id="id_comment<?php echo $_POST['blog_id']; ?>"><?php if (!empty($_POST['comment_body'])) { echo $_POST['comment_body'];}?></textarea>
		</p>

		<p>
			<?php echo _("Please solve the following mathematical problem so that we know you are a human.");?>
		</p>

		<p>
			<label for="id_captcha"><?php echo $maptcha; ?></label>
			<input type="text" name="maptcha_text" id="id_captcha" value="" />
		</p>
		
		<p class="note">
			<?php echo _("Example: 2 * 2 = 4 or 0 - 9 = -9");?>
		</p>
	
		<p class="buttons">
			<input type="submit" name="post_comment" value="<?php echo _("add your comment");?>" />
		</p>
	</form>
</div>