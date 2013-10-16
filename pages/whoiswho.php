<h1>LAC 2014 Group Picture</h1>

<div class="center">
<a href="img/LAC2014_group.jpg"><img src="img/LAC2014_group_small.jpg" alt="group picture"/></a>
</div>
<h1>Who is..</h1>
<div class="center">
<a href="img/LAC2014_group_annotatedMEDIUM.jpg"><img src="img/LAC2014_group_annotated.jpg" alt="annotated group picture"/></a>
</div>
<?php
$pt=array(
	'XXX', # 0 placeholder
	'Jack Jones', 
	'Alsa Bogart',
	'Foo Bar',
	'Fubar',
);

echo '<ul class="multicolumn nobullet">'."\n";
$numheads=4;
$missing=0;
for ($i=0; $i<$numheads; $i++) {
	$n=($i%3)*floor($numheads/3) + floor($i/3) +1;
	$name=$pt[$n];
	if (''==$name)$missing++;
	echo '<li>'.$n.'. '.$name.'</li>'."\n";
}
echo "</ul>\n";
echo '<div class="clearer"></div>'."\n";
echo '<p>If you know - or are - any of the '.$missing.' unidentified persons (or notice some other irregularities), please <a href="'.local_url('contact').'">drop us a line</a>.</p>';
echo '<p>The picture was taken right after the closing ceremony on the third (of four) days. Not everybody that came to the conference is in this picture. Sorry, we won\'t <em><a href="http://www.gimp.org/" rel="external">gimp</a></em> you in, your next chance will be LAC\'14 at ZKM, Karlsruhe.</p>';
