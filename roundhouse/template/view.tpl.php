<script type="text/javascript">
	
	ajax = ajax();
	
	function view_comments (id, title, name, email, comment)  {
		
		ajax.onreadystatechange = function() {
			if(ajax.readyState == 4) {
				get('box_comments_' + id).innerHTML = ajax.responseText;
				objShowHide('box_comments_' + id);
			}
		}

		if (!name) {
			name = "";
		}

		if (!email) {
			email = "";
		}

		if (!comment) {
			comment = "";
		}

		ajax.open("POST","/ajax/get_comments.php",true);
		ajax.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		ajax.send('blog_id=' + id + '&blog_title='+title+'&comment_name='+name+'&comment_email='+email+'&comment_body='+comment);
		
	}

</script>

<div id="col_left">

	<form method="post">
	<div class="box" id="box_contact">
		<div class="box_header">
			<h1><?php echo _("Email me");?></h1>
		</div>

		<div class="box_body">

			<p>
				<label for="id_contact_email"><?php echo _("Email");?></label>
				<input type="text" name="contact_email" id="id_contact_email" />
			</p>

			<p class="note">
				<?php echo _("Your email address will be used to me to reply to you. It is not kept.");?>
			</p>

			<p>
				<label for="id_contact_subject"><?php echo _("Subject");?></label>
				<input type="text" name="contact_subject" id="id_contact_subject" />
			</p>
			
			<p>
				<label for="id_contact_message"><?php echo _("Message");?></label></br />
				<textarea name="contact_message" id="id_contact_message"></textarea>
			</p>

			<p>
				<?php echo _("Please solve the following mathematical problem so that we know you are a human.");?>
			</p>

			<p id="id_p_captcha">
				<label for="id_captcha"><?php echo $maptcha; ?></label></br />
				<input type="text" name="maptcha_text" id="id_captcha" value="" />
			</p>
	
			<p class="note">
				<?php echo _("Example: 2 * 2 = 4 or 0 - 9 = -9");?>
			</p>

			<p class="buttons">
				<input type="submit" name="submit_contact_form" value="<?php echo _("send");?>" />
			</p>
		</div>
	</div>
	</form>


	<div class="box" id="box_blog">
		<div class="box_body">
			<?php
			if (isset($blogs)) {
			?>
			<ul>
				<?php
				foreach ($blogs as $key => $i):
				?>
				<li<?php if (isset($i['blog_imported'])) { echo " class=\"remote\"";}?>>
					<div class="header">
						<?php
						if ($i['blog_published'] != 1) {
						?>
						<span class="not_published"><?php echo _("Not published");?>: </span>
						<?php }?>
						<a href="/<?php echo $i['blog_title'];?>"><?php echo $i['blog_title_display'];?></a>
					</div>

					<div class="body">
						<?php
						if (!empty($i['blog_import_title'])) {
						?>
						<div class="imported_item">
							<p class="imported_body">
								<?php echo $i['blog_import_body'];?>
							</p>
	
							<p class="imported_metadata">
								<?php
								$txt = _("<a href='{link}'>{title}</a> imported from <a href='{source_link}'>{source_title}</a>.");
								$txt = str_replace("{link}", $i['blog_import_link'], $txt);
								$txt = str_replace("{title}", $i['blog_import_title'], $txt);
								$txt = str_replace("{source_link}", $i['blog_import_source_link'], $txt);
								$txt = str_replace("{source_title}", $i['blog_import_source_title'], $txt);
								echo $txt;
								?>
							</p>
						</div>
						<?php } ?>

						<?php
						if (!empty($i['blog_body'])) {
						?>
						<p>
							<?php echo $i['blog_body'];?>
						</p>
						<?php } ?>
					</div>

					<div class="footer">
						<p>
							<?php
							$txt = _("Added {create_datetime}.");
							$txt = str_replace("{create_datetime}", timeDiff($i['blog_create_datetime']), $txt);
							echo $txt;
							?>

							<?php
							if (!empty($i['blog_accept_comment'])) {
							$txt = ngettext("{comment_total} comment received.", "{comment_total} comments received.", $i['comment_total']);
							$txt = str_replace("{comment_total}", $i['comment_total'], $txt);
							?>
							<span onclick="javascript:view_comments('<?php echo $i['blog_id'];?>', '<?php echo $i['blog_title'];?>');" class="comment_button"><?php echo $txt;?></span>

							<span onclick="javascript:view_comments('<?php echo $i['blog_id'];?>', '<?php echo $i['blog_title'];?>');" class="comment_button"><?php echo _("Add a comment");?></span>. 
							<?php }?>

							<span onclick="javascript:objShowHide('box_share_<?php echo $i['blog_id'];?>');" class="share_button"><?php echo _("Share it.");?></span>

							<?php
							if (!empty($i['tags'])) {
							?>
							<?php echo _("Tagged with");?> 
							<?php
							foreach ($i['tags'] as $tkey => $t):
							?>
							<a href="/view/tag/<?php echo $t['tag_name'];?>"><?php echo $t['tag_display_name'];?></a>
							<?php
							if ($tkey+1 < count($i['tags'])) {
								echo " * ";
							}
							endforeach;
							echo ".";
							}
							?>

							<span onclick="javascript:objShowHide('box_permalink_<?php echo $i['blog_id'];?>');" class="permalink_button"><?php echo _("Permalink it.");?></span> 
							
							<?php
							if (isset($_SESSION['user_id'])) {
							?>
							<a href="/editor/<?php echo $i['blog_id'];?>"><?php echo _("Edit this");?></a>
							<?php }?>
						</p>
					</div>
					
					<div class="permalink" id="box_permalink_<?php echo $i['blog_id'];?>" style="display:none;">
						<?php
						$url = SCRIPT_HTTP_HOST . "/" . $i['blog_title'];
						$txt = _("The permalink, or 'link directly to this article' is<br /><a href='/{blog_title}'>{url}</a>");
						$txt = str_replace("{blog_title}", $i['blog_title'], $txt);
						$txt = str_replace("{url}", $url, $txt);
						echo $txt;
						?>
					</div>


					<div class="share" id="box_share_<?php echo $i['blog_id'];?>" style="display:none;">
						<?php
						$url = urlencode(SCRIPT_HTTP_HOST . "/" . $i['blog_title']);
						?>

						<ul>
							<li>
								<a href="http://del.icio.us/post?url=<?php echo $url;?>&amp;title=<?php echo urlencode($i['blog_title']);?>"><img src="/<?php echo SCRIPT_THEME_PATH;?>img/delicious.png" alt="del.icio.us logo" border="0" /></a>
								<a href="http://del.icio.us/post?url=<?php echo $url;?>&amp;title=<?php echo urlencode($i['blog_title']);?>"><?php echo _("del.icio.us");?></a>
							</li>
					
							<li>
								<a href="http://digg.com/submit?phase=2&amp;url=<?php echo $url;?>&amp;title=<?php echo urlencode($i['blog_title']);?>"><img src="/<?php echo SCRIPT_THEME_PATH;?>img/digg.png" alt="Digg logo" border="0" /></a>
								<a href="http://digg.com/submit?phase=2&amp;url=<?php echo $url;?>&amp;title=<?php echo urlencode($i['blog_title']);?>"><?php echo _("Digg");?></a>
							</li>
							
							<li>
								<a href="http://www.stumbleupon.com/submit?url=<?php echo $url;?>&amp;title=<?php echo urlencode($i['blog_title']);?>"><img src="/<?php echo SCRIPT_THEME_PATH;?>img/stumbleupon.png" alt="StumbleUpon logo" border="0" /></a>
								<a href="http://www.stumbleupon.com/submit?url=<?php echo $url;?>&amp;title=<?php echo urlencode($i['blog_title']);?>"><?php echo _("StumbleUpon");?></a>
							</li>
							
							<li>
								<a href="http://www.technorati.com/faves?add=<?php echo $url;?>&amp;title=<?php echo urlencode($i['blog_title']);?>"><img src="/<?php echo SCRIPT_THEME_PATH;?>img/technorati.png" alt="Technorati logo" border="0" /></a>
								<a href="http://www.technorati.com/faves?add=<?php echo $url;?>&amp;title=<?php echo urlencode($i['blog_title']);?>"><?php echo _("Technorati");?></a>
							</li>
						</ul>
					</div>

					<div class="comments" id="box_comments_<?php echo $i['blog_id'];?>" style="display:none;"></div>

				</li>
				<?php
				endforeach;
				?>
			</ul>
			
			<?php
			if (isset($prev)) {
			?>
				<div style="float: left;">
					<?php
					if ($prev == 0) {
					?>
						<a href="/">&laquo; <?php echo _("previous");?></a>
					<?php
					}
					else {
					?>
						<a href="/page<?php echo $prev; ?>">&laquo; <?php echo _("previous");?></a>
					<?php }?>
				</div>
			<?php }?>
			
			<?php
			if (isset($next)) {
			?>
				<div style="float: right;">
					<a href="/page<?php echo $next; ?>"><?php echo _("next");?> &raquo;</a>
				</div>
			<?php }?>
			<div style="clear: both;"></div>
			
			<?php
			}
			else {
			?>
			<p>
				<?php echo _("Sorry, there are no blog entries to display.");?>
			</p>
			<?php }?>
		</div>
	</div>
</div>

<div id="col_right">
	<div class="box" id="box_profile">
		<div class="box_header">
			<h1><?php echo _("About me");?></h1>
		</div>

		<div class="box_body">
			<p>
				<?php echo $webspace['user_name']; ?>,
				<?php echo $webspace['user_location']; ?>
			</p>

			<div class="avatar">
				<img src="/get_file.php?avatar=<?php echo $webspace['user_id'];?>&amp;width=200" />
			</div>
		</div>

		<div class="box_footer">
			<a href="#" onclick="javascript:objShowHide('box_contact');"><?php echo _("Contact me");?></a>
		</div>
	</div>
	
	<div class="box" id="box_blog_intro">
		<div class="box_header">
			<h1><?php if (isset($webspace['user_blog_title'])) { echo ucfirst($webspace['user_blog_title']); } else { echo _("Blog"); }?></h1>
		</div>

		<div class="box_body">
			<?php
			if (isset($webspace['user_blog_description'])) {
			?>
			<p>
				<?php echo $webspace['user_blog_description'];?>
			</p>
			<?php }?>

			<p class="rss">
				<a href="/feed/rss.php"><img src="/<?php echo SCRIPT_THEME_PATH;?>/img/rss.png" alt="RSS" border="0" /></a>
			</p>
		</div>

		<?php
		if (isset($uri_routing[1])) {
		?>
		<div class="box_footer">
			<a href="/view"><?php echo _("View latest entries");?></a>
		</div>
		<?php }?>
	</div>
	
	<?php
	if (isset($blog_highlights)) {
	?>
	<div class="box" id="box_highlights">
		<div class="box_header">
			<h1><?php echo _("Highlights");?></h1>
		</div>

		<div class="box_body">
			<ul>
				<?php
				foreach ($blog_highlights as $key => $i):
				?>
				<li><a href="/view/<?php echo $i['blog_title'];?>"><?php echo $i['blog_title_display'];?></a></li>
				<?php
				endforeach;
				?>
			</ul>
		</div>
	</div>
	<?php }?>


		
	<?php
	if (isset($tags)) {
	?>
	<div class="box" id="box_tags">
		<div class="box_header">
			<h1><?php echo _("Tags");?></h1>
		</div>

		<div class="box_body">
			<?php
			$max_qty = 0;
			$number_of_styles = 5;

			foreach($tags as $key => $t):
				if ($t['tag_total'] > $max_qty) {
					$max_qty = $t['tag_total'];
				}
			endforeach;
			?>
			<p>
			<?php
			foreach($tags as $key => $t):

			if ($t['tag_total'] > 0 && $max_qty > 0) {
				$percent = floor(($t['tag_total'] / $max_qty) * 100);

				$tag_size = ceil(($number_of_styles/100)*$percent);

			}
			else {
				$tag_size = 1;
			}
			?>
			<a href="/view/tag/<?php echo $t['tag_name'];?>" class="tagsize<?php echo $tag_size;?>"><?php echo $t['tag_display_name'];?></a>
			<?php
			if ($key+1 < count($tags)) {
				echo " * ";
			}

			endforeach;
			?>
			</p>
		</div>
	</div>
		<?php }?>
	

	<?php
	if (isset($blog_archive)) {
	?>
	<div class="box" id="box_archive">
		<div class="box_header">
			<h1><?php echo _("Archive");?></h1>
		</div>

		<div class="box_body">
			<ul>
				<?php
				$month_names[] = _("January");
				$month_names[] = _("February");
				$month_names[] = _("March");
				$month_names[] = _("April");
				$month_names[] = _("May");
				$month_names[] = _("June");
				$month_names[] = _("July");
				$month_names[] = _("August");
				$month_names[] = _("September");
				$month_names[] = _("October");
				$month_names[] = _("November");
				$month_names[] = _("December");

				foreach ($blog_archive as $key => $i):
				if (array_key_exists($i['month']-1, $month_names)) {
				?>
				<li><a href="/view/date/<?php echo $i['year'];?>/<?php echo $i['month'];?>"><?php echo $month_names[$i['month']-1];?> <?php echo $i['year'];?></a> <sup>(<?php echo $i['total'];?>)</sup></li>
				<?php
				}
				endforeach;
				?>
			</ul>
		</div>
	</div>
	<?php }?>
</div>

<?php
if (isset($add_comment_error)) {
?>
<script type="text/javascript">
	view_comments(<?php echo $_POST['blog_id'];?>, '<?php echo $_POST['blog_title'];?>', '<?php echo $_POST['comment_user_name'];?>', '<?php echo $_POST['comment_email'];?>', '<?php echo $_POST['comment_body'];?>');
</script>
<?php }?>