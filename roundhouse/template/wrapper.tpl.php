<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo STND_LOCALE;?>" lang="<?php echo STND_LOCALE;?>">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="Content-Language" content="<?php echo STND_LOCALE;?>" />
	
	<?php
	if (isset($webspace['user_blog_title'])) {
		$webpage_title = $webspace['user_blog_title'];
	}
	else {
		$webpage_title = "Roundhouse";
	}

	if (isset($first_blog_title)) {
		$webpage_title .= ": " . $first_blog_title;
	}
	?>

	<title><?php echo $webpage_title;?></title>

	<?php
	if (!empty($webspace['openid_server'])) {
	?>
	<link rel="openid.server" href="<?php echo $webspace['openid_server'];?>" />
	<?php
	}
	elseif (!empty($webspace['openid_delegate'])) {
	?>
	<link rel="openid.delegate" href="<?php echo $webspace['openid_delegate'];?>" />
	<?php }?>

	
	<style type="text/css">
	<!--
	@import url(/<?php echo SCRIPT_THEME_PATH;?>css/common.css);
	@import url(/<?php echo SCRIPT_THEME_PATH;?>css/<?php echo SCRIPT_NAME;?>.css);
	-->
	</style>

	<?php
	//we reload an image in this template just before the session times out to
	//make sure that the session does not time out
	$session_maxlifetime = ini_get('session.gc_maxlifetime'); // in seconds
	
	// we need to warn 2 minutes before
	$session_warning_time = 120; // seconds
	if ($session_maxlifetime > $session_warning_time) {
		$session_maxlifetime = $session_maxlifetime-$session_warning_time;
	}
	$session_maxlifetime_ms = $session_maxlifetime*1000; // in milliseconds
	?>

	<script type="text/javascript" src="/<?php echo SCRIPT_TEMPLATE_PATH;?>js/functions.js"></script>

	<script type="text/javascript">
	//<![CDATA[
		var session_maxlifetime_ms = <?php echo $session_maxlifetime_ms;?>;

		function ShowTimeoutWarning () {
			// we append the time to the string to avoid caching
			var urldate = new Date()
			var urltime = urldate.getTime()
			document.session_reload_image.src = '/get_file.php?reloadsession=start&now=' + urltime;
			setTimeout( 'ShowTimeoutWarning();', session_maxlifetime_ms );
		}
	//]]>
	</script>
	
		<link href="/feed/rss.php" rel="alternate" type="application/rss+xml" title="RSS feed" />
	
</head>

<body onload="setTimeout( 'ShowTimeoutWarning();', session_maxlifetime_ms );">

	<?php
	if (isset($webspace['user_blog_description'])) {
	?>
	<div style="display: none;">
		<?php echo $webspace['user_blog_description'];?>
	</div>
	<?php }?>
	
	<div id="content_container">
		<div id="header_container">
			<ul>
				<?php
				if (!empty($_SESSION['user_id'])) {
				?>
				<?php
				$link_css = "";
				if (defined('AM_SCRIPT_NAME') && AM_SCRIPT_NAME == "view") {
					$link_css = " class=\"current\"";
				}
				?>
				<li><a href="/view"<?php echo $link_css;?>><?php echo _("Home");?></a></li>


				<?php
				$link_css = "";
				if (defined('AM_SCRIPT_NAME') && AM_SCRIPT_NAME == "manage") {
					$link_css = " class=\"current\"";
				}
				?>
				<li><a href="/manage"<?php echo $link_css;?>><?php echo _("Manage");?></a></li>
				
				<?php
				$link_css = "";
				if (defined('AM_SCRIPT_NAME') && AM_SCRIPT_NAME == "account") {
					$link_css = " class=\"current\"";
				}
				?>
				<li><a href="/account"<?php echo $link_css;?>><?php echo _("Account");?></a></li>

				<?php
				if (!empty($_SESSION['user_is_maintainer'])) {
				$link_css = "";
				if (defined('SCRIPT_NAME') && SCRIPT_NAME == "maintain") {
					$link_css = " class=\"current\"";
				}
				?>
				<li><a href="/maintain"<?php echo $link_css;?>><?php echo _("Maintain");?></a></li>
				<?php }?>
				
				<li><a href="/disconnect"><?php echo _("Logoff");?></a></li>
				<?php }?>
			</ul>
			
			<div id="header_title">
				<a href="/"><img src="/get_file.php?title=<?php if (defined('WEBSPACE_ID')) { echo WEBSPACE_ID; } else { echo 0;}?>" border="0" alt="" /></a>
			</div>
		</div>
		<div style="clear:both;"></div>

		<?php
		if (!empty($GLOBALS['script_error_log'])) {
		?>
		<div id="system_error_container">
			<?php
			foreach($GLOBALS['script_error_log'] as $key => $val):
				echo $val . "<br />";
			endforeach;
			?>
		</div>
		<?php }?>
		
		<div id="body_container">
			<?php echo $content;?>
		</div>
		
		<div style="clear:both;"></div>
		
		<div id="footer_container">
			<ul>
				<li><a href="http://www.gnu.org/copyleft/fdl.html"><?php echo _("This work is licensed under a GNU Free Documentation License");?></a></li>
				<?php
				if (defined('SCRIPT_HTTP_HOST')) {
				?>
				<li><a href="http://babelfish.altavista.com/?url=<?php echo urlencode(SCRIPT_HTTP_HOST . "/view");?>"><?php echo _("Translate this page");?></a></li>
				<?php }?>
				<li>Made with <a href="http://www.barnraiser.org/roundhouse"><?php echo _("Made with Roundhouse");?></a></li>
				<?php
				if (!isset($_SESSION['user_id']) && defined('WEBSPACE_ID')) {
				?>
				<li><a href="/manage"><?php echo _("Manage");?></a></li>
				<?php }?>
			</ul>
		</div>
		
		<div style="clear:both;"></div>

		<div id="id_session_reload_image">
			<img name="session_reload_image" src="/get_file.php?reloadsession=1" alt="" />
		</div>
	</div>
</body>
</html>