<?php

  function program_header($mode,$details) {
    echo '<h1>Conference Schedule</h1>'."\n";
### Note during conference about streaming and IRC ###
#    echo '<div class="center" style="margin-top:.5em; margin-bottom:.-5em;"><p>During the conference, live A/V streams are available for the main track: <a href="http://lacstreamer.stackingdwarves.net/lac2014.ogg" rel="external">View Video in Browser</a></p><p>Remote participants are invited to join <a href="http://webchat.freenode.net/?channels=lac2014" rel="external">#lac2014 on irc.freenode.net</a>, to be able to take part in the discussions, ask questions, and get technical assistance in case of stream problems.</p><p>Conference Material can be found on the <a href="'.local_url('files').'">Download Page</a>.</p><br/></div>';

#    echo 'Direct links to video stream:<ul><li><a href="http://lacstreamer.stackingdwarves.net/lac2014.ogg" rel="external">http://lacstreamer.stackingdwarves.net/lac2014.ogg</a> (Europe)</li><li><a href="http://radio.linuxaudio.org/lac2014.ogg" rel="external">http://radio.linuxaudio.org/lac2014.ogg</a> (US)</li></ul>';
#    echo '<div>The A/V is provided in Ogg/Theora/Vorbis (plays in firefox, chrom[e|ium]). Should you have problems playing these, consult Wikipedia\'s <a href="http://en.wikipedia.org/wiki/Wikipedia:Media_help_%28Ogg%29" rel="external">OGG media help.</a></div><br/>';
##    echo '<div class="center red" style="margin-top:.5em; margin-bottom:.-5em; font-size:110%; font-weight: bold;">The schedule is still being worked on. The information below is not yet valid.<br/></div>';
#    echo '<hr/><br/>'."\n";

    echo '<p class="ptitle">Timetable Format: ';
    if ($mode!='list' || $details)
      echo '<a href="'.local_url('program', 'mode=list&amp;details=0').'">Plain List</a>&nbsp;|&nbsp;';
    if ($mode!='list' || !$details)
      echo '<a href="'.local_url('program', 'mode=list&amp;details=1').'">List with Abstracts</a>&nbsp;|&nbsp;';
    if ($mode!='table')
      echo '<a href="'.local_url('program', 'mode=table').'">Table</a>&nbsp;|&nbsp;';
    #echo 'vCal (<a href="vcal.php?v=1.0">v1.0-iCal</a>)&nbsp;';
    #echo '(<a href="vcal.php">v2.0</a>)&nbsp;|&nbsp;';
    echo '<a href="vcal.php">iCal</a>&nbsp;|&nbsp;';
    echo '<a href="printprogram.php">Printable Version</a>'."\n";
    echo '<br/>All times are <a href="http://en.wikipedia.org/wiki/Central_European_Time" rel="external">CEST</a> = UTC+2'."\n";
    echo "</p>\n";
  }
### Note before conference about streaming and IRC ###

  $mode='list';
  if (isset($_REQUEST['mode'])&&!empty($_REQUEST['mode'])) $mode=$_REQUEST['mode'];
  #$details=isset($_REQUEST['details'])?true:false;
  $details=isset($_REQUEST['details'])?($_REQUEST['details']?true:false):true;

  switch ($mode) {
    case 'vcal':
      program_header($mode,$details);
      echo '<p>Download Link: <a href="vcal.php">vCal/iCal</a><br/><br/></p><hr>';
      echo '<pre>';
      vcal_program($db,'2.0',false);
      echo '</pre><hr/>';
      break;
    case 'table':
      $now=time(); $day=1;
      $days=count(array_keys(fetch_selectlist(0,'days')));
      #for($cday=1; $cday <= $days; $cday++) {
      #  if ($now < conference_dayend($cday)) {$day=$cday; break;}
      #}
      if (isset($_REQUEST['day'])) $day=intval($_REQUEST['day']);
      if ($day<1 || $day>$days) {
        program_header('',$details);
        hardcoded_concert_and_installation_info($db);
      } else if ($day == 4) {
        program_header($mode,$details);

        echo '<div style="float:right;">';
        for ($i=1; $i<=4; $i++) {
          if ($i == $day) { echo 'Day '.$i.'&nbsp;&nbsp;'; continue;}
          echo '<a href="'.local_url('program', 'mode=table&amp;day='.$i).'">Day '.$i.'</a>&nbsp;&nbsp;';
        }
        echo '<a href="'.local_url('program', 'mode=table&amp;day=0').'">Concerts&amp;Installations</a>&nbsp;&nbsp;';
        echo '</div>';
        $a_days = fetch_selectlist(0, 'days');
        echo '<h2 class="ptitle pb">Day '.$a_days[$day].'</h2>';
        echo '<br/><hr/><br/>';
        require_once('pages/excursion.php');

      } else {
        program_header($mode,$details);
        table_program($db,$day);
      }
      break;
    default:
    case 'list':
      program_header($mode,$details);
      if ($f=print_filter($db))
        list_filtered_program($db, $f, $details);
      else
        list_program($db, $details);
      break;
  }
  hardcoded_disclaimer();

# vim: ts=2 et
