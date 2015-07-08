<div id="col_full">
	<form method="post">
	<div class="box">
		<div class="box_body">
			<h1><?php echo _("Register to share knowledge!");?></h1>

			<p>
				<?php
				if (isset($email_domains)) {
					$email_domains_str = implode (", ", $email_domains);
					$txt = _("Registration is limited to people whom have an email account ending with {email_domains}.");
					$txt = str_Replace("{email_domains}", $email_domains_str, $txt);
					echo $txt;
				}
				else {
					echo _("Thank you for joining using your OpenID. We want to know a few extra things about you before we complete your registration.");
				}
				?>
			</p>
			
			<h1><?php echo _("Email</h1>
			
			<p>
				<?php echo _("Your email is used for sending you a digest of activity around you. You can control this from your 'account' page when you get in.");?>
			</p>
 
			<p>
				<label for="id_user_email"><?php echo _("Email");?></label>
				<input type="text" name="user_email" id="id_user_email" value="<?php if (isset($_POST['user_email'])) { echo $_POST['user_email'];}?>"/>
			</p>
			
			<p>
				<?php echo _("Choose your blog name.");?>
			</p>
			
			<p>
				<label for="id_user_webspace"><?php echo _("blog name");?></label>
				http://<input type="text" name="user_webspace" id="id_user_webspace" value="<?php if (isset($_POST['user_webspace'])) { echo $_POST['user_webspace'];}?>" />.roundhouse.barnraiser.net
			</p>
	
			
			<h1><?php echo _("About you");?></h1>
	
			<p>
				<label for="id_user_name"><?php echo _("Name");?></label>
				<input type="text" id="id_user_name" name="user_name" value="<?php if (isset($_POST['user_name'])) { echo $_POST['user_name']; }?>"/>
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
				<input type="submit" name="submit_openid_registration" value="<?php echo _("REGISTER");?>" />
			</p>
		</div>
	</div>
	</form>
</div>