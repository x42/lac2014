<?php

function stripstreamtable($t) {
	return str_replace(
		'+0000','UTC',
		str_replace(
		'<a href=','<a rel="external" href=',
		str_replace(
		'colspan="7"','colspan="8"',
		preg_replace('@up since.*, @','',
		preg_replace('@</?table[^>]*>@','',$t)))));
}

function printstreamheader() {
	echo '<tr>'."\n";
	echo '<th>URL</th>'."\n";
	echo '<th>Description</th>'."\n";
	echo '<th>Geometry</th>'."\n";
	echo '<th>FPS</th>'."\n";
	echo '<th>A bit/s</th>'."\n";
	echo '<th>V bit/s</th>'."\n";
	echo '<th>Listeners</th>'."\n";
	echo '<th>up since</th>'."\n";
	echo '</tr>'."\n";
}

function streamtable() {
	$src0=file_get_contents('http://ccrma.stanford.edu:8080/lac2012.xsl');
	$src1=file_get_contents('http://streamer.stackingdwarves.net/lac2012.xsl');
	echo '<table class="streaminfo" style="width:100%;font-size:11px;line-height:17px;">'."\n";
	printstreamheader();
	echo stripstreamtable($src0);
	echo stripstreamtable($src1);
	echo '</table>'."\n";
	$enabled=0;
	if (strstr($src0, 'http://ccrma.stanford.edu:8080/lac2012-hq.ogv')) $enabled|=1;
	if (strstr($src0, 'http://ccrma.stanford.edu:8080/lac2012-lq.ogv')) $enabled|=2;
	if (strstr($src1, 'http://streamer.stackingdwarves.net:8000/lac2012-hq.ogv')) $enabled|=4;
	if (strstr($src1, 'http://streamer.stackingdwarves.net:8000/lac2012-lq.ogv')) $enabled|=8;
	if ($enabled) {
		echo '<p>Play live video in browser: ';
		if ($enabled&1)
			echo '<a href="video.php?id=-1">HQ/US</a>, ';
		if ($enabled&2)
			echo '<a href="video.php?id=-2">LQ/US</a>, ';
		if ($enabled&4)
			echo '<a href="video.php?id=-3">HQ/EU</a>, ';
		if ($enabled&8)
			echo '<a href="video.php?id=-4">LQ/EU</a>';
		echo '.</p>'."\n";
	}
}

streamtable();
