<script type="text/javascript">
	
	ajax = ajax();
	
	function upload_file (blog_id, file, size)  {
		
		ajax.onreadystatechange = function() {
			if(ajax.readyState == 4) {
				get('box_files').innerHTML = ajax.responseText;
			}
		}

		ajax.open("POST","/ajax/upload_blog_file.php",true);
		ajax.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		ajax.send('blog_id=' + blog_id + '&file=' + title + '&size='+size);
		
	}

</script>

<div id="col_left">
	<form method="post">
	<?php
	if (isset($blog['blog_id'])) {
	?>
		<input type="hidden" name="blog_id" value="<?php echo $blog['blog_id'];?>" />
	<?php }?>
	
	<?php
	if (isset($display) && $display == "import") {
	?>

	<input type="hidden" name="import_feed" value="1"/>
	<input type="hidden" name="import_link" value="<?php echo $blog['blog_import_link'];?>" />
	<input type="hidden" name="import_title" value="<?php echo $blog['blog_import_title'];?>" />
	<input type="hidden" name="import_source_link" value="<?php echo $blog['blog_import_source_link'];?>" />
	<input type="hidden" name="import_source_title" value="<?php echo $blog['blog_import_source_title'];?>" />
	<input type="hidden" name="import_description" value="<?php echo $blog['blog_import_body'];?>" />

	<div class="box" id="box_imported_item">
		<div class="box_header">
			<h1><?php echo _("Your imported Item");?></h1>
		</div>
		
		<div class="box_body">
			<p>
				<?php echo _("The imported item will appear as a blog entry. You can write a blog entry below which will appear under the imported item.");?>
			</p>
			
			<p>
				<label for="id_blog_title"><?php echo _("Title");?></label>
				<input type="text" name="blog_title" id="id_blog_title" value="<?php if (isset($blog['blog_title_display'])) { echo $blog['blog_title_display']; }?>" />
			</p>
			
			<p>
				<textarea disabled="disabled"><?php echo $blog['blog_import_body'];?></textarea>
			</p>

			
			<p class="note">
				Source: <a href="<?php echo $blog['blog_import_source_link'];?>" target="_new"><?php echo $blog['blog_import_source_title'];?></a>
			</p>

			<p>
				<label for="id_blog_body"><?php echo _("Comment");?></label><br />
				<textarea name="blog_body" id="id_blog_body"><?php if (isset($blog['blog_body'])) { echo $blog['blog_body']; }?></textarea>
			</p>

			<p>
				<label for="id_tags"><?php echo _("Tags");?></label>
				<input type="text" name="tags" id="id_tags" value="<?php if (isset($blog['tags'])) { echo $blog['tags']; }?>" />
			</p>

			<p class="note">
				<?php echo _("You can add tags by using commas to separate each tag.");?>
			</p>

			<p class="checkbox_label">
				<label for="id_blog_highlight"><?php echo _("Add this to highlights list");?></label>
				<input type="checkbox" name="blog_highlight" id="id_blog_highlight"<?php if (isset($blog['blog_highlight']) && !empty($blog['blog_highlight'])) { echo " checked=\"checked\""; }?> />
			</p>

			<p class="checkbox_label">
				<label for="id_blog_comments"><?php echo _("Accept comments against this entry");?></label>
				<input type="checkbox" name="blog_comments" id="id_blog_comments"<?php if (isset($blog['blog_accept_comment']) && !empty($blog['blog_accept_comment'])) { echo " checked=\"checked\""; }?> />
			</p>
				
			<p class="buttons">
				<input type="submit" name="submit_blog_save" value="<?php echo _("save");?>" />
				<input type="submit" name="submit_blog_publish" value="<?php echo _("publish");?>" />
			</p>
		</div>
	</div>
	<?php
	}
	else {
	?>

	<div class="box" id="box_blog_edit">
		<div class="box_header">
			<h1><?php echo _("Your blog entry");?></h1>
		</div>
		
		<div class="box_body">
			<p>
				<label for="id_blog_title"><?php echo _("Title");?></label>
				<input type="text" name="blog_title" id="id_blog_title" value="<?php if (isset($blog['blog_title_display'])) { echo $blog['blog_title_display']; }?>" />
			</p>

			<p>
				<label for="id_blog_body"><?php echo _("Entry");?></label><br />
				<textarea name="blog_body" id="id_blog_body"><?php if (isset($blog['blog_body'])) { echo $blog['blog_body']; }?></textarea>
			</p>

			<p>
				<label for="id_tags"><?php echo _("Tags");?></label>
				<input type="text" name="tags" id="id_tags" value="<?php if (isset($blog['tags'])) { echo $blog['tags']; }?>" />
			</p>

			<p class="note">
				<?php echo _("You can add tags by using commas to separate each tag.");?>
			</p>

			<p class="checkbox_label">
				<label for="id_blog_highlight"><?php echo _("Add this to highlights list");?></label>
				<input type="checkbox" name="blog_highlight" id="id_blog_highlight"<?php if (isset($blog['blog_highlight']) && !empty($blog['blog_highlight'])) { echo " checked=\"checked\""; }?> />
			</p>

			<p class="checkbox_label">
				<label for="id_blog_comments"><?php echo _("Accept comments against this entry");?></label>
				<input type="checkbox" name="blog_comments" id="id_blog_comments"<?php if (isset($blog['blog_accept_comment']) && !empty($blog['blog_accept_comment'])) { echo " checked=\"checked\""; }?> />
			</p>
				
			<p class="buttons">
				<input type="submit" name="submit_blog_save" value="<?php echo _("save");?>" />
				<input type="submit" name="submit_blog_publish" value="<?php echo _("publish");?>" />
			</p>
		</div>
	</div>
	</form>
	<?php }?>
</div>

<div id="col_right">

	<div class="box" id="blog_editor_navigation">
		<div class="box_header">
			<h1><?php echo _("Options");?></h1>
		</div>
		
		<div class="box_body">
			<ul>
				<?php
				if (isset($blog['blog_id'])) {
				?>
				<li><a href="/<?php echo $blog['blog_title'];?>"><?php echo _("View this blog entry");?></a></li>
				<?php }?>
				<li><a href="/editor"><?php echo _("Add another blog entry");?></a></li>
			</ul>
		</div>
	</div>

	
	<?php
	if (isset($blog['blog_id'])) {
	?>

	<form action="/upload_blog_file.php?blog_id=<?php echo $blog['blog_id'];?>" target="file_upload_iframe" method="post" enctype="multipart/form-data">
	<input type="hidden" name="blog_id" value="<?php echo $blog['blog_id'];?>" />

	

	<div class="box" id="box_file_upload">
		<div class="box_header">
			<h1><?php echo _("Upload Pictures");?></h1>
		</div>
		
		<div class="box_body">
			
			<p>
				<label for="frm_file"><?php echo _("Select picture");?></label><br />
				<input type="file" name="frm_file" id="frm_file" size="10" />
			</p>

			<p>
				<label for="id_width"><?php echo _("Width");?></label>
				<input type="text" name="width" id="id_width" value="" />
			</p>

			<p class="buttons">
				<input type="submit" name="submit_upload_blog_file" value="<?php echo _("upload");?>" />
			</p>
		</div>
	</div>
	</form>

	<iframe src="/upload_blog_file.php?blog_id=<?php echo $blog['blog_id'];?>" name="file_upload_iframe"></iframe>

	<?php
	}
	else {
	?>
	<div class="box">
		<div class="box_header">
			<h1><?php echo _("Upload Pictures");?></h1>
		</div>
		
		<div class="box_body">
			<p>
				<?php echo _("Please save your blog first.");?>
			</p>
		</div>
	</div>
	<?php }?>
</div>