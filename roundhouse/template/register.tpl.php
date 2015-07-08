<?php
if (isset($email_sent)) { 
?>
<div id="col_full">
	<div class="box">
		<div class="box_body">
			<h1><?php echo _("Thank you!");?></h1>
			<p>
				<?php echo _("You have been sent an email. Please click the link in it to activate your account.");?>
			</p>
		</div>
	</div>
</div>
<?php
}
else {
?>
<div id="col_full">
	<form method="post">
	<div class="box">
		<div class="box_body">
			<h1><?php echo _("Register to blog!");?></h1>

			<p>
				<?php
				if (isset($email_domains)) {
					$email_domains_str = implode (", ", $email_domains);
					$txt = _("Registration is limited to people whom have an email account ending with {email_domains}.");
					$txt = str_Replace("{email_domains}", $email_domains_str, $txt);
					echo $txt;
				}
				else {
					echo _("Joining is free for you. Simply fill in this registration form to come in!");
				}
				?>
			</p>
			
			<h1><?php echo _("Email and password");?></h1>

			<p>
				<?php echo _("We use a combination of your email and password to log you in.");?>
			</p>
			
			<p>
				<label for="id_user_email"><?php echo _("Email");?></label>
				<input type="text" name="user_email" id="id_user_email" value="<?php if (isset($_POST['user_email'])) { echo $_POST['user_email'];}?>"/>
			</p>
			
			<p>
				<?php echo _("Choose your blog name.");?>
			</p>

			<p>
				<?php
				$url = $http . "://tom." . $domain;
				$txt =  _("The blog name is used in your blog web address. If you use 'tom' your web address will become somthing like {url}.");
				$txt = str_replace("{url}", $url, $txt);
				echo $txt;
				?>
			</p>
			
			<p>
				<label for="id_user_webspace"><?php echo _("blog name");?></label>
				<input type="text" name="user_webspace" id="id_user_webspace" value="<?php if (isset($_POST['user_webspace'])) { echo $_POST['user_webspace'];}?>" />
			</p>
			
			<p>
				<?php echo _("Your password must be over 5 characters long.");?>
			</p>
			
			<p>
				<label for="id_user_password1"><?php echo _("Password");?></label>
				<input type="password" name="user_password1" id="id_user_password1" value="<?php if (isset($_POST['user_password1'])) { echo $_POST['user_password1']; }?>" />
			</p>
	
			<p>
				<label for="id_user_password2"><?php echo _("Repeat password");?></label>
				<input type="password" name="user_password2" id="id_user_password2" value="<?php if (isset($_POST['user_password2'])) { echo $_POST['user_password2']; }?>" />
			</p>
	
			
			<h1><?php echo _("About me");?></h1>
	
			<p>
				<label for="id_user_name"><?php echo _("Name");?></label>
				<input type="text" id="id_user_name" name="user_name" value="<?php if (isset($_POST['user_name'])) { echo $_POST['user_name']; }?>"/>
			</p>
			
			<p>
				<label for="id_nickname"><?php echo _("Memorable date");?></label>
				<select name="dob_year" id="id_dob_year">
					<option value=""><?php echo _("Year");?></option>
					<?php
						for($i = 2003; $i > 1908; $i--) {
							$selected = "";
							if (isset($_POST['dob_year']) && $_POST['dob_year'] == $i) {
								$selected = " selected=\"selected\"";
							}
					?>
						<option value="<?php echo $i; ?>"<?php echo $selected; ?>><?php echo $i; ?></option>
					<?php } ?>
				</select> -
				<select name="dob_month" id="id_dob_month">
					<option value=""><?php echo _("Month");?></option>
					<option value="01"<?php if (isset($_POST['dob_month']) && $_POST['dob_month'] == 1) echo " selected=\"selected\""; ?>>01</option>
					<option value="02"<?php if (isset($_POST['dob_month']) && $_POST['dob_month'] == 2) echo " selected=\"selected\""; ?>>02</option>
					<option value="03"<?php if (isset($_POST['dob_month']) && $_POST['dob_month'] == 3) echo " selected=\"selected\""; ?>>03</option>
					<option value="04"<?php if (isset($_POST['dob_month']) && $_POST['dob_month'] == 4) echo " selected=\"selected\""; ?>>04</option>
					<option value="05"<?php if (isset($_POST['dob_month']) && $_POST['dob_month'] == 5) echo " selected=\"selected\""; ?>>05</option>
					<option value="06"<?php if (isset($_POST['dob_month']) && $_POST['dob_month'] == 6) echo " selected=\"selected\""; ?>>06</option>
					<option value="07"<?php if (isset($_POST['dob_month']) && $_POST['dob_month'] == 7) echo " selected=\"selected\""; ?>>07</option>
					<option value="08"<?php if (isset($_POST['dob_month']) && $_POST['dob_month'] == 8) echo " selected=\"selected\""; ?>>08</option>
					<option value="09"<?php if (isset($_POST['dob_month']) && $_POST['dob_month'] == 9) echo " selected=\"selected\""; ?>>09</option>
					<option value="10"<?php if (isset($_POST['dob_month']) && $_POST['dob_month'] == 10) echo " selected=\"selected\""; ?>>10</option>
					<option value="11"<?php if (isset($_POST['dob_month']) && $_POST['dob_month'] == 11) echo " selected=\"selected\""; ?>>11</option>
					<option value="12"<?php if (isset($_POST['dob_month']) && $_POST['dob_month'] == 12) echo " selected=\"selected\""; ?>>12</option>
				</select> -
				<select name="dob_day" id="id_dob_day">
					<option value=""><?php echo _("Day");?></option>
					<option value="01"<?php if (isset($_POST['dob_day']) && $_POST['dob_day'] == 1) echo " selected=\"selected\""; ?>>01</option>
					<option value="02"<?php if (isset($_POST['dob_day']) && $_POST['dob_day'] == 2) echo " selected=\"selected\""; ?>>02</option>
					<option value="03"<?php if (isset($_POST['dob_day']) && $_POST['dob_day'] == 3) echo " selected=\"selected\""; ?>>03</option>
					<option value="04"<?php if (isset($_POST['dob_day']) && $_POST['dob_day'] == 4) echo " selected=\"selected\""; ?>>04</option>
					<option value="05"<?php if (isset($_POST['dob_day']) && $_POST['dob_day'] == 5) echo " selected=\"selected\""; ?>>05</option>
					<option value="06"<?php if (isset($_POST['dob_day']) && $_POST['dob_day'] == 6) echo " selected=\"selected\""; ?>>06</option>
					<option value="07"<?php if (isset($_POST['dob_day']) && $_POST['dob_day'] == 7) echo " selected=\"selected\""; ?>>07</option>
					<option value="08"<?php if (isset($_POST['dob_day']) && $_POST['dob_day'] == 8) echo " selected=\"selected\""; ?>>08</option>
					<option value="09"<?php if (isset($_POST['dob_day']) && $_POST['dob_day'] == 9) echo " selected=\"selected\""; ?>>09</option>
					<option value="10"<?php if (isset($_POST['dob_day']) && $_POST['dob_day'] == 10) echo " selected=\"selected\""; ?>>10</option>
					<option value="11"<?php if (isset($_POST['dob_day']) && $_POST['dob_day'] == 11) echo " selected=\"selected\""; ?>>11</option>
					<option value="12"<?php if (isset($_POST['dob_day']) && $_POST['dob_day'] == 12) echo " selected=\"selected\""; ?>>12</option>
					<option value="13"<?php if (isset($_POST['dob_day']) && $_POST['dob_day'] == 13) echo " selected=\"selected\""; ?>>13</option>
					<option value="14"<?php if (isset($_POST['dob_day']) && $_POST['dob_day'] == 14) echo " selected=\"selected\""; ?>>14</option>
					<option value="15"<?php if (isset($_POST['dob_day']) && $_POST['dob_day'] == 15) echo " selected=\"selected\""; ?>>15</option>
					<option value="16"<?php if (isset($_POST['dob_day']) && $_POST['dob_day'] == 16) echo " selected=\"selected\""; ?>>16</option>
					<option value="17"<?php if (isset($_POST['dob_day']) && $_POST['dob_day'] == 17) echo " selected=\"selected\""; ?>>17</option>
					<option value="18"<?php if (isset($_POST['dob_day']) && $_POST['dob_day'] == 18) echo " selected=\"selected\""; ?>>18</option>
					<option value="19"<?php if (isset($_POST['dob_day']) && $_POST['dob_day'] == 19) echo " selected=\"selected\""; ?>>19</option>
					<option value="20"<?php if (isset($_POST['dob_day']) && $_POST['dob_day'] == 20) echo " selected=\"selected\""; ?>>20</option>
					<option value="21"<?php if (isset($_POST['dob_day']) && $_POST['dob_day'] == 21) echo " selected=\"selected\""; ?>>21</option>
					<option value="22"<?php if (isset($_POST['dob_day']) && $_POST['dob_day'] == 22) echo " selected=\"selected\""; ?>>22</option>
					<option value="23"<?php if (isset($_POST['dob_day']) && $_POST['dob_day'] == 23) echo " selected=\"selected\""; ?>>23</option>
					<option value="24"<?php if (isset($_POST['dob_day']) && $_POST['dob_day'] == 24) echo " selected=\"selected\""; ?>>24</option>
					<option value="25"<?php if (isset($_POST['dob_day']) && $_POST['dob_day'] == 25) echo " selected=\"selected\""; ?>>25</option>
					<option value="26"<?php if (isset($_POST['dob_day']) && $_POST['dob_day'] == 26) echo " selected=\"selected\""; ?>>26</option>
					<option value="27"<?php if (isset($_POST['dob_day']) && $_POST['dob_day'] == 27) echo " selected=\"selected\""; ?>>27</option>
					<option value="28"<?php if (isset($_POST['dob_day']) && $_POST['dob_day'] == 28) echo " selected=\"selected\""; ?>>28</option>
					<option value="29"<?php if (isset($_POST['dob_day']) && $_POST['dob_day'] == 29) echo " selected=\"selected\""; ?>>29</option>
					<option value="30"<?php if (isset($_POST['dob_day']) && $_POST['dob_day'] == 30) echo " selected=\"selected\""; ?>>30</option>
					<option value="31"<?php if (isset($_POST['dob_day']) && $_POST['dob_day'] == 31) echo " selected=\"selected\""; ?>>31</option>
				</select>
			</p>

			<p class="note">
				<?php echo _("We do not display your memorable date anywhere, but we do use it to verify you should you wish to contact us.");?>
			</p>
	
			<p>
				<label for="id_user_location"><?php echo _("Location");?></label>
				<input type="text" id="id_user_location" name="user_location" value="<?php if (isset($_POST['user_location'])) echo $_POST['user_location']; ?>"/>
			</p>

			<h1><?php echo _("A little test");?></h1>
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
			
	
			<p class="toc">
				<label for="id_tos"><?php echo _("I agree to the <a href='/about/toc' target='_new'>terms</a> and I confirm that do not smell of cheese.");?></label>
				<input type="checkbox" name="tos" id="id_tos" value="1" <?php if (isset($_POST['tos']) && !empty($_POST['tos'])) echo "checked=\"checked\"";?>/>
			</p>
	
			<p class="buttons">
				<input type="submit" name="register" value="<?php echo _("REGISTER");?>" />
			</p>
		</div>
	</div>
	</form>
</div>
<?php }?>