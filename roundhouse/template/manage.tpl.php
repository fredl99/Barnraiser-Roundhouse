
<?php
if (isset($_SESSION['user_id'])) {
?>

<div id="col_left">
	<?php
	if (isset($uri_routing[1]) && $uri_routing[1] == "import") {
	
	if (isset($import)) {
	?>
	<div class="box" id="box_import_list">
		<div class="box_header">
			<?php
			$txt = _("Imported from {title}");
			$txt = str_replace("{title}", $import['title'], $txt);
			?>
			<h1><?php echo $txt;?></h1>
		</div>
		
		<div class="box_body">
			<p>
				<?php echo $import['description'];?>
			</p>

			<p>
				From: <a href="<?php echo $import['link'];?>" target="_new"><?php echo $import['link'];?></a>
			</p>
			
			<?php
			if (!empty($import['items'])) {
			?>
			<ul>
				<?php
				foreach($import['items'] as $key => $val):
				?>
					<li>
						<a href="<?php echo $val['link']; ?>"><?php echo $val['title']; ?></a>
						<br /><i><?php echo $val['description']; ?></i>
						<form action="/editor" method="post">
							<input type="hidden" name="import_feed" value="<?php echo $key; ?>"/>
							<input type="hidden" name="import_link" value="<?php echo $val['link']; ?>" />
							<input type="hidden" name="import_title" value="<?php echo $val['title']; ?>" />
							<input type="hidden" name="import_source_link" value="<?php echo $import['link']; ?>" />
							<input type="hidden" name="import_source_title" value="<?php echo $import['title']; ?>" />
							<input type="hidden" name="import_description" value="<?php echo htmlspecialchars($val['description']); ?>" />
							<p class="buttons">
								<input type="submit" name="import_feed" value="<?php echo _("import");?>" />
							</p>
						</form>
					</li>
				<?php
				endforeach;
				?>
			</ul>
			<?php }?>
		</div>
	</div>
	<?php }?>
	
	<div class="box" id="box_import_list">
		<div class="box_header">
			<h1><?php echo _("Import a feed");?></h1>
		</div>
		<form method="post">
			<div class="box_body">
				<p>
					<?php echo _("Input an RSS/ATOM feed url and we will list all the entries it has. You can then select the entries you like and choose to import them directly into your blog as an entry.");?>
				</p>
			
				<p>
					<label for="id_import_url"><?php echo _("URL");?></label>
					<input type="text" id="id_import_url" name="import_url" />
				</p>
			
				<p class="buttons">
					<input type="submit" name="submit_import_url" value="<?php echo _("list");?>" />
				</p>
			</div>
		</form>
	</div>
	<?php
	}
	else {
	?>
	<div class="box" id="box_blog_list">
		<div class="box_header">
			<h1><?php echo _("Blog entries");?></h1>
		</div>
		
		<?php
		if (isset($blogs)) {
		?>
		<div class="box_body">
			<form method="post">
				<ul>
				<?php
				foreach ($blogs as $key => $i):
				?>
					<li>
						<input type="checkbox" name="delete_blog_entry[]" value="<?php echo $i['blog_id']; ?>" />
						<a href="/editor/<?php echo $i['blog_id'];?>"><?php echo $i['blog_title_display'];?></a>
						
						<?php
						if ($i['blog_published'] != 1) {
						?>
						&nbsp;(<?php echo _("not published");?>)
						<?php
						}
						else {
						?>
						&nbsp;(<?php echo _("published");?>)
						<?php }?>
					</li>
				<?php
				endforeach;
				?>
				</ul>
			
				<p class="buttons">
					<input type="submit" name="submit_delete_blog_entries" value="<?php echo _("delete");?>" />
				</p>
			</form>
			<?php
			if (isset($prev)) {
			?>
				<div style="float: left;">
					<?php
					if ($prev == 0) {
					?>
						<a href="/manage">&laquo; <?php echo _("previous");?></a>
					<?php
					}
					else {
					?>
						<a href="/manage/page<?php echo $prev; ?>">&laquo; <?php echo _("previous");?></a>
					<?php }?>
				</div>
			<?php }?>
			
			<?php
			if (isset($next)) {
			?>
				<div style="float: right;">
					<a href="/manage/page<?php echo $next; ?>"><?php echo _("next");?> &raquo;</a>
				</div>
			<?php }?>
			<div style="clear: both;"></div>
		</div>
		
		<?php
		}
		else {
		?>
		
		<div class="box_body">
			<p>
				<?php echo _("You have not blog entries to list.");?>
			</p>
		</div>
		<?php }?>
	</div>
	<?php }?>
</div>

<div id="col_right">
	<div class="box" id="box_import_list">
		<div class="box_header">
			<h1><?php echo _("Options");?></h1>
		</div>

		<div class="box_body">
			<ul>	
				<?php
				$link_css = "";
				if (!isset($uri_routing[1]) || $uri_routing[1] != "import") {
				?>
				<li><?php echo _("Blog entries");?></li>
				<?php
				}
				else {
				?>
				<li><a href="/manage"<?php echo $link_css;?>><?php echo _("Blog entries");?></a></li>
				<?php }?>

				<li><a href="/editor"><?php echo _("Add");?></a></li>

				<?php
				$link_css = "";
				if (isset($uri_routing[1]) && $uri_routing[1] == "import") {
				?>
				<li><?php echo _("Import");?></li>
				<?php
				}
				else {
				?>
				<li><a href="/manage/import"><?php echo _("Import");?></a></li>
				<?php }?>
			</ul>
		</div>
	</div>
</div>
<?php
}
else {
?>

<div id="col_left">
	<div class="box">
		<div class="box_header">
			<h1><?php echo _("Manage your blog");?></h1>
		</div>

		<div class="box_body">
			<p>
				<?php echo _("Please log in to manage your blog.");?>
			</p>
		</div>
	</div>
</div>

<div id="col_right">
	<form method="post">
		<div class="box" id="box_login">
			<div class="box_header">
				<h1><?php echo _("Login");?></h1>
			</div>

			<div class="box_body">
				<p>
					<label for="id_login_email"><?php echo _("Email");?></label>
					<input type="text" id="id_login_email" value="" name="login_email" />
				</p>

				<p>
					<label for="id_login_password"><?php echo _("Password");?></label>
					<input type="password" id="id_login_password" value="" name="login_password" />
				</p>

				<p class="buttons">
					<input type="submit" name="login_admin" value="<?php echo _("log in");?>" />
				</p>
			</div>
		</div>
	</form>
	
	<div class="box">
		<div class="box_header">
			<h1><?php echo _("OpenID login");?></h1>
		</div>

		<div class="box_body">
		<form method="post">
			<p>
				<label for="id_login_openid"><?php echo _("OpenID");?></label>
				<input type="text" id="id_login_openid" value="" name="openid_login" />
			</p>

			<p class="buttons">
				<input type="submit" name="submit_openid_login" value="<?php echo _("log in");?>" />
			</p>
		</form>
		</div>
	</div>

	<form method="post">
	<div class="box">
		<div class="box_header">
			<h1><?php echo _("Lost password?");?></h1>
		</div>
		
		<div class="box_body">
			<?php
			if (isset($new_password)) {
			?>
			<p>
				<?php echo _("Your new password has been emailed to you.");?>
			</p>
			<?php
			}
			else {
			?>
			<p>
				<?php echo _("Fill in the following details and we will email you a new password.");?>
			</p>
			
			<p>
				<label for="id_dob_year"><?php echo _("Memorable date");?></label><br />
				<select name="dob_year" id="id_dob_year">
					<option value=""><?php echo _("Year");?></option>
					<?php 
						for($i = 2008; $i > 1908; $i--) {
					?>
						<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
					<?php } ?>
				</select> -
				<select name="dob_month" id="id_dob_month">
					<option value=""><?php echo _("Month");?></option>
					<option value="01">01</option>
					<option value="02">02</option>
					<option value="03">03</option>
					<option value="04">04</option>
					<option value="05">05</option>
					<option value="06">06</option>
					<option value="07">07</option>
					<option value="08">08</option>
					<option value="09">09</option>
					<option value="10">10</option>
					<option value="11">11</option>
					<option value="12">12</option>
				</select> - 
				<select name="dob_day" id="id_dob_day">
					<option value=""><?php echo _("Day");?></option>
					<option value="01">01</option>
					<option value="02">02</option>
					<option value="03">03</option>
					<option value="04">04</option>
					<option value="05">05</option>
					<option value="06">06</option>
					<option value="07">07</option>
					<option value="08">08</option>
					<option value="09">09</option>
					<option value="10">10</option>
					<option value="11">11</option>
					<option value="12">12</option>
					<option value="13">13</option>
					<option value="14">14</option>
					<option value="15">15</option>
					<option value="16">16</option>
					<option value="17">17</option>
					<option value="18">18</option>
					<option value="19">19</option>
					<option value="20">20</option>
					<option value="21">21</option>
					<option value="22">22</option>
					<option value="23">23</option>
					<option value="24">24</option>
					<option value="25">25</option>
					<option value="26">26</option>
					<option value="27">27</option>
					<option value="28">28</option>
					<option value="29">29</option>
					<option value="30">30</option>
					<option value="31">31</option>
				</select>
			</p>

			<p>
				<label for="id_email"><?php echo _("Email");?></label>
				<input type="text" name="new_password_email" id="id_email" />
			</p>
			
			<p class="buttons">
				<input type="submit" name="submit_new_password" value="<?php echo _("send");?>" />
			</p>
			<?php }?>
		</div>
	</div>
	</form>
</div>
<?php }?>