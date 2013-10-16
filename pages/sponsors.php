<h1>Sponsors</h1>
<p>
As admittance to the conference is free, several things need sponsoring.
If you want to contribute to the conference and want to know what you
can sponsor and what we offer in return, please contact the
conference organisation: <?=$config['txtemail']?> 
</p>
<h2>Why should I sponsor LAC?</h2>
<p>
  The Linux Audio Conference is an annual free-of-charge event that is in its 12th year now. This international conference has been a meeting place for many of the world leaders in development and use of non-proprietary professional Audio Visual software.
</p>
<p>
  LAC has been the incubating event
  for many of the well-established Free and Open Source packages that have made a significant impact
  on the area, including the 
<a rel="external" href="http://jackaudio.org/">Jack Audio Connection Kit</a>, 
<a rel="external" href="http://ardour.org/">Ardour</a> and
<a rel="external" href="http://qtractor.sourceforge.net/">Qtractor</a>, 
  just to mention a few.
</p>
<p>
  Along the years, many important technologies were demonstrated at the conference, such as systems
  for spatial audio (Wave Field Synthesis, Ambisonics), networked audio and real-time processing.
  LAC also featured presentations by key people involved in Sound Synthesis research
  (<a rel="external" href="http://www.csounds.com/">Csound</a>, 
   <a rel="external" href="http://puredata.info/">Pure Data</a>, 
   <a rel="external" href="http://www.audiosynth.com/">SuperCollider</a>, etc.) and
  development of commercial products
  (<a rel="external" href="http://mixbus.harrisonconsoles.com/">Harrison Consoles</a>,
   <a rel="external" href="http://64studio.com/">64studio</a>, 
   <a rel="external" href="http://www.lionstracs.com/">Lionstracs</a>, 
   <a rel="external" href="http://www.trinityaudiogroup.com/">Trinity</a>, ...). It provides plenty of space 
  for developer discussions and - of course - concerts and music (of various flavours and genres).
  Recently, the conference has also been a host to the Open Video community
  (<a rel="external" href="http://lumiera.org/">lumiera</a>, 
   <a rel="external" href="http://openmovieeditor.org/">openmovieeditor</a>,
   <a rel="external" href="http://www.kdenlive.org/">kdenlive</a>,
   <a rel="external" href="http://www.kinodv.org/">kino</a>),
  expanding its range to incorporate visual technologies.
</p>
<p>
  In summary: LAC is the place for companies looking to interface with the non-proprietary pro-AV community,
  researchers, developers, artists and users.
</p>
<?php /*
<p>
As admittance to the conference was free, several things required sponsoring which was made possible by the following partners:
</p>
 */ ?>
<h2>Supporters</h2>
<table border="0" width="100%" id="supporter">
<tr>
<?php
  $cnt=0;
  foreach ($sponsors as $sl => $si) {
    if ($cnt>0 && ($cnt%4 ==0)) {
      echo "</tr>\n<tr>\n";
    }
    echo "  <td>\n";
    echo '    <a href="'.$sl.'"'."\n";
    echo '     rel="supporter"><img src="'.$si['img'].'" title="'.$si['title'].'" alt="'.$si['title'].'"/><br/>';
    echo $si['title']."</a>\n  </td>\n";
    $cnt++;
  }
  while ($cnt++%4 !=0) {
    echo '  <td></td>';
  }

?>
</tr>
</table>
