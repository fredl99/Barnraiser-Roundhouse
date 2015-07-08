<div id="col_left">
	<form method="post">
	<div class="box" id="box_blog">
		<div class="box_header">
			<h1><?php echo _("Blog");?></h1>
		</div>
		
		<div class="box_body">
			<p>
				<label for="id_user_blog_title"><?php echo _("Title");?></label>
				<input type="text" name="user_blog_title" id="id_user_blog_title" value="<?php if (isset($webspace['user_blog_title'])) { echo $webspace['user_blog_title']; }?>" />
			</p>

			<p>
				<label for="id_user_blog_description"><?php echo _("Name");?></label>
				<textarea name="user_blog_description" id="id_user_blog_description"><?php if (isset($webspace['user_blog_description'])) { echo $webspace['user_blog_description']; }?></textarea>
			</p>
	
	
			<?php
			if (isset($themes)) {
			?>
			<p>
				<label for="id_user_blog_theme"><?php echo _("Choose theme");?></label>
				<select name="theme_name" id="id_user_blog_theme">
					<?php
					foreach ($themes as $key => $i):
			
					$selected = "";
			
					if (isset($webspace['user_blog_theme']) && $i == $webspace['user_blog_theme']) {
						$selected = " selected=\"selected\"";
					}
					elseif (!isset($webspace['user_blog_theme']) && $i == $default_theme) {
						$selected = " selected=\"selected\"";
					}
					?>
					<option id="theme_thumb<?php echo $key;?>" onclick="javascript:selectTheme('<?php echo $i;?>');"<?php echo $selected;?>><?php echo $i;?></option>
					<?php endforeach;?>
				</select>
			</p>
			
			<div id="id_theme_thumb"></div>
			
			<script type="text/javascript">
				function selectTheme(theme) {
					document.getElementById('id_theme_thumb').style.backgroundImage = "url('/theme/"+theme+"/thumb.png')";
				}
		
				selectTheme('<?php if (isset($webspace['user_blog_theme'])) { echo $webspace['user_blog_theme']; } else { echo $default_theme;} ?>');
			</script>
			<?php }?>
			
			<p class="buttons">
				<input type="submit" name="save_blog_information" value="<?php echo _("save");?>" />
			</p>
		</div>
	</div>
	</form>


	<form method="post">
	<div class="box" id="box_notification">
		<div class="box_header">
			<h1><?php echo _("Email notifications");?></h1>
		</div>
		
		<div class="box_body">
			<p>
				<label for="id_email_notify"><?php echo _("Do you want to receive email notications when you receive a comment on your blog?");?></label>
				<input type="checkbox" name="user_email_notify" id="id_email_notify" value="1"<?php if (!empty($webspace['user_email_notify'])) { echo " checked=\"checked\""; }?> />
			</p>
			
			<p class="buttons">
				<input type="submit" name="save_email_notification" value="<?php echo _("save");?>" />
			</p>
		</div>
	</div>
	</form>


	<form method="post">
	<div class="box">
		<div class="box_header">
			<h1><?php echo _("Profile");?></h1>
		</div>
		
		<div class="box_body">
			<p>
				<label for="id_user_name"><?php echo _("Name");?></label>
				<input type="text" name="user_name" id="id_user_name" value="<?php if (isset($_SESSION['user_name'])) { echo $_SESSION['user_name']; }?>" />
			</p>
				
			<p>
				<label for="id_dob_year"><?php echo _("Memorable date");?></label>
				<?php
				if (isset($_SESSION['user_dob'])) {
					$dob = explode('-', $_SESSION['user_dob']);
				}
				?>
				<select name="dob_year" id="id_dob_year">
					<option value=""><?php echo _("Year");?></option>
					<?php 
						for($i = 2003; $i > 1908; $i--) { 
							$selected = "";
							if (isset($dob[0]) && (int) $dob[0] == $i) {
								$selected = " selected=\"selected\"";
							}
					?>
						<option value="<?php echo $i; ?>"<?php echo $selected; ?>><?php echo $i; ?></option>
					<?php } ?>
				</select> -
				<select name="dob_month" id="id_dob_month">
					<option value=""><?php echo _("Month");?></option>
					<option value="01"<?php if (isset($dob[1]) && (int) $dob[1] == 1) echo " selected=\"selected\""; ?>>01</option>
					<option value="02"<?php if (isset($dob[1]) && (int) $dob[1] == 2) echo " selected=\"selected\""; ?>>02</option>
					<option value="03"<?php if (isset($dob[1]) && (int) $dob[1] == 3) echo " selected=\"selected\""; ?>>03</option>
					<option value="04"<?php if (isset($dob[1]) && (int) $dob[1] == 4) echo " selected=\"selected\""; ?>>04</option>
					<option value="05"<?php if (isset($dob[1]) && (int) $dob[1] == 5) echo " selected=\"selected\""; ?>>05</option>
					<option value="06"<?php if (isset($dob[1]) && (int) $dob[1] == 6) echo " selected=\"selected\""; ?>>06</option>
					<option value="07"<?php if (isset($dob[1]) && (int) $dob[1] == 7) echo " selected=\"selected\""; ?>>07</option>
					<option value="08"<?php if (isset($dob[1]) && (int) $dob[1] == 8) echo " selected=\"selected\""; ?>>08</option>
					<option value="09"<?php if (isset($dob[1]) && (int) $dob[1] == 9) echo " selected=\"selected\""; ?>>09</option>
					<option value="10"<?php if (isset($dob[1]) && (int) $dob[1] == 10) echo " selected=\"selected\""; ?>>10</option>
					<option value="11"<?php if (isset($dob[1]) && (int) $dob[1] == 11) echo " selected=\"selected\""; ?>>11</option>
					<option value="12"<?php if (isset($dob[1]) && (int) $dob[1] == 12) echo " selected=\"selected\""; ?>>12</option>
				</select> - 
				<select name="dob_day" id="id_dob_day">
					<option value=""><?php echo _("Day");?></option>
					<option value="01"<?php if (isset($dob[2]) && (int) $dob[2] == 1) echo " selected=\"selected\""; ?>>01</option>
					<option value="02"<?php if (isset($dob[2]) && (int) $dob[2] == 2) echo " selected=\"selected\""; ?>>02</option>
					<option value="03"<?php if (isset($dob[2]) && (int) $dob[2] == 3) echo " selected=\"selected\""; ?>>03</option>
					<option value="04"<?php if (isset($dob[2]) && (int) $dob[2] == 4) echo " selected=\"selected\""; ?>>04</option>
					<option value="05"<?php if (isset($dob[2]) && (int) $dob[2] == 5) echo " selected=\"selected\""; ?>>05</option>
					<option value="06"<?php if (isset($dob[2]) && (int) $dob[2] == 6) echo " selected=\"selected\""; ?>>06</option>
					<option value="07"<?php if (isset($dob[2]) && (int) $dob[2] == 7) echo " selected=\"selected\""; ?>>07</option>
					<option value="08"<?php if (isset($dob[2]) && (int) $dob[2] == 8) echo " selected=\"selected\""; ?>>08</option>
					<option value="09"<?php if (isset($dob[2]) && (int) $dob[2] == 9) echo " selected=\"selected\""; ?>>09</option>
					<option value="10"<?php if (isset($dob[2]) && (int) $dob[2] == 10) echo " selected=\"selected\""; ?>>10</option>
					<option value="11"<?php if (isset($dob[2]) && (int) $dob[2] == 11) echo " selected=\"selected\""; ?>>11</option>
					<option value="12"<?php if (isset($dob[2]) && (int) $dob[2] == 12) echo " selected=\"selected\""; ?>>12</option>
					<option value="13"<?php if (isset($dob[2]) && (int) $dob[2] == 13) echo " selected=\"selected\""; ?>>13</option>
					<option value="14"<?php if (isset($dob[2]) && (int) $dob[2] == 14) echo " selected=\"selected\""; ?>>14</option>
					<option value="15"<?php if (isset($dob[2]) && (int) $dob[2] == 15) echo " selected=\"selected\""; ?>>15</option>
					<option value="16"<?php if (isset($dob[2]) && (int) $dob[2] == 16) echo " selected=\"selected\""; ?>>16</option>
					<option value="17"<?php if (isset($dob[2]) && (int) $dob[2] == 17) echo " selected=\"selected\""; ?>>17</option>
					<option value="18"<?php if (isset($dob[2]) && (int) $dob[2] == 18) echo " selected=\"selected\""; ?>>18</option>
					<option value="19"<?php if (isset($dob[2]) && (int) $dob[2] == 19) echo " selected=\"selected\""; ?>>19</option>
					<option value="20"<?php if (isset($dob[2]) && (int) $dob[2] == 20) echo " selected=\"selected\""; ?>>20</option>
					<option value="21"<?php if (isset($dob[2]) && (int) $dob[2] == 21) echo " selected=\"selected\""; ?>>21</option>
					<option value="22"<?php if (isset($dob[2]) && (int) $dob[2] == 22) echo " selected=\"selected\""; ?>>22</option>
					<option value="23"<?php if (isset($dob[2]) && (int) $dob[2] == 23) echo " selected=\"selected\""; ?>>23</option>
					<option value="24"<?php if (isset($dob[2]) && (int) $dob[2] == 24) echo " selected=\"selected\""; ?>>24</option>
					<option value="25"<?php if (isset($dob[2]) && (int) $dob[2] == 25) echo " selected=\"selected\""; ?>>25</option>
					<option value="26"<?php if (isset($dob[2]) && (int) $dob[2] == 26) echo " selected=\"selected\""; ?>>26</option>
					<option value="27"<?php if (isset($dob[2]) && (int) $dob[2] == 27) echo " selected=\"selected\""; ?>>27</option>
					<option value="28"<?php if (isset($dob[2]) && (int) $dob[2] == 28) echo " selected=\"selected\""; ?>>28</option>
					<option value="29"<?php if (isset($dob[2]) && (int) $dob[2] == 29) echo " selected=\"selected\""; ?>>29</option>
					<option value="30"<?php if (isset($dob[2]) && (int) $dob[2] == 30) echo " selected=\"selected\""; ?>>30</option>
					<option value="31"<?php if (isset($dob[2]) && (int) $dob[2] == 31) echo " selected=\"selected\""; ?>>31</option>
				</select>
			</p>

			<p class="note">
				<?php echo _("We ask you for this if you need to reset your password.");?>
			</p>
			
			<p>
				<label for="id_user_location"><?php echo _("Location");?></label>
				<input type="text" name="user_location" id="id_user_location" value="<?php if (isset($_SESSION['user_location'])) echo $_SESSION['user_location']; ?>" />
			</p>
			
			<p class="buttons">
				<input type="submit" name="save_profile_information" value="<?php echo _("save");?>" />
			</p>
		</div>
	</div>
	</form>


	<form method="post">
	<div class="box" id="box_openid">
		<div class="box_header">
			<h1><?php echo _("OpenID");?></h1>
		</div>
		
		<div class="box_body">
			<p>
				<label for="id_openid_server"><?php echo _("Server");?></label>
				<input type="text" name="openid_server" id="id_openid_server" value="<?php if (isset($webspace['openid_server'])) { echo $webspace['openid_server']; }?>" />
			</p>

			<p>
				<label for="id_openid_delegate"><?php echo _("Delegate");?></label>
				<input type="text" name="openid_delegate" id="id_openid_delegate" value="<?php if (isset($webspace['openid_delegate'])) { echo $webspace['openid_delegate']; }?>" />
			</p>
			
			<p class="buttons">
				<input type="submit" name="save_openid_information" value="<?php echo _("save");?>" />
			</p>

			<p class="hint">
				<?php echo _("If you don't know what these are then leave them empty.");?>
			</p>
		</div>
	</div>
	</form>
</div>

<div id="col_right">
	<form action="upload_file.php" method="post" enctype="multipart/form-data">
	<div class="box">
		<div class="box_header">
			<h1><?php echo _("Profile picture");?></h1>
		</div>
		
		<div class="box_body">
			<div style="text-align:center;">
				<img src="/get_file.php?avatar=<?php echo $_SESSION['user_id'];?>&amp;width=200" width="200" class="avatar" />
			</div>
			
			<p>
				<label for="frm_file"><?php echo _("Select picture");?></label><br />
				<input type="file" name="frm_file" id="frm_file" />
			</p>

			<p class="buttons">
				<?php
				if (isset($display_avatar_delete_button)) {
				?>
				<input type="submit" name="submit_delete_avatar" value="<?php echo _("delete");?>" />
				<?php }?>
				
				<input type="submit" name="submit_upload_avatar" value="<?php echo _("upload");?>" />
			</p>
		</div>
	</div>
	</form>

	
	<form method="post">
	<div class="box" id="id_set_new_account_email">
		<div class="box_header">
			<h1><?php echo _("Account email address");?></h1>
		</div>
		
		<div class="box_body">
			<p>
				<?php
				$txt = _("Your current email address is {email}.");
				$txt = str_replace("{email}", $_SESSION['user_email'], $txt);
				echo $txt;
				?>
			</p>

			<p>
				<label><?php echo _("New email address");?></label>
				<input type="text" name="user_email1" value="" />
			</p>

			<p>
				<label><?php echo _("Repeat email address");?></label>
				<input type="text" name="user_email2" value="" />
			</p>

			<p class="warning">
				<?php echo _("Warning: If you change your email address you will be logged off. You must log in again using your new email address to continue.");?>
			</p>
			
			<p class="buttons">
				<input type="submit" name="change_user_email" value="<?php echo _("save");?>" />
			</p>
		</div>
	</div>
	</form>
	

	<form method="post">
	<div class="box" id="id_set_new_password">
		<div class="box_header">
			<h1><?php echo _("Set password");?></h1>
		</div>
		
		<div class="box_body">
			<p>
				<label for="id_user_password"><?php echo _("Current password");?></label>
				<input type="password" name="user_password_old" id="id_user_password" value="" />
			</p>
			
			<p>
				<label for="id_user_password1"><?php echo _("New password");?></label>
				<input type="password" name="user_password1" id="id_user_password1" value="" />

			</p>
			
			<p>
				<label for="id_user_password2"><?php echo _("Repeat new password");?></label>
				<input type="password" name="user_password2" id="id_user_password2" value="" />
			</p>
			
			<p class="buttons">
				<input type="submit" name="change_user_password" value="<?php echo _("save");?>" />
			</p>
		</div>
	</div>
	</form>
</div>