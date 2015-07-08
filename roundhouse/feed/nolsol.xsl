<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html"/>
<xsl:variable name="title" select="/rss/channel/title"/>
<xsl:template match="/">
	
<html>
<head>
	<title>XML Feed</title>
	<link rel="stylesheet" href="nolsol_xsl.css" type="text/css"/>
</head>

<xsl:apply-templates select="rss/channel"/>
</html>
</xsl:template>
	
<xsl:template match="channel">
<body>
	<div class="topbox">
		<h2>This is an RSS feed for <xsl:value-of select="$title"/></h2>

		<p>
			Below is the latest content available from this feed.<br />
		</p>

		<p>
			An RSS feeds allow you to stay up to date with the latest updates to this webspace. To subscribe to it, you will need a News Reader or other similar device.<br />
		</p>
	</div>
	
	<div class="mainbox">
		<xsl:apply-templates select="item"/>
	</div>
</body>
</xsl:template>

<xsl:template match="item">
	<div id="item">
		<ul>
			<li><a href="{link}" class="item"><xsl:value-of select="title"/></a><br/>
			<xsl:value-of select="description"/>
			
			</li>
		</ul>
	</div>
</xsl:template>
</xsl:stylesheet>