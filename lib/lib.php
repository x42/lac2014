<?php

  date_default_timezone_set('UTC');
  require_once('config.php');

  function authenticate($group='') {

    #HTTP DIGEST AUTH
    $user=$_SERVER['PHP_AUTH_DIGEST'];
    if (!empty($user)) {
      # TODO: check $group
      return true;
    }
    return false;
  }

  function html5head($title='The Linux Audio Conference', $style='style.css', $add='') {
?><!DOCTYPE html>
<?php
    htmlhead($title, $style, $add);
  }

  function xhtmlhead($title='Linux Audio Conference', $style='style.css', $add='') {
    ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php
    htmlhead($title, $style, $add);
  }

  function htmlhead($title, $style, $add) {
?><html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
  <title><?=SHORTTITLE?>: <?=$title?></title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <link rel="stylesheet" href="<?=BASEURL?>static/<?=$style?>" type="text/css" media="all"/>
  <link rel="stylesheet" href="<?=BASEURL?>static/print.css" type="text/css" media="print"/>
  <meta name="Author" content="Robin Gareus" />
  <meta name="description" content="Linux Audio Conference <?=LACY?>" />
  <meta name="keywords" content="LAC<?=LACY?>, LAC, Linux Audio Conference <?=LACY?>,Linux, Music, Audio, Developer Meeting, CCRMA, Computer Research in Music and Acoustics, Stanford, Stanford University" />
  <link rel="shortcut icon" href="<?=BASEURL?>favicon.ico" type="image/x-icon" />
  <link rel="icon" href="<?=BASEURL?>favicon.ico" type="image/x-icon" />
  <meta name="viewport" content="width=560, initial-scale=.7"/>
  <style type="text/css">
@media all {
  div.braille {display:none;}
}
@media braille,embossed,aural {
  div.braille {display:inline;}
}
  </style>
  <script type="text/javascript" src="<?=BASEURL?>static/script.js"></script>
  <?=$add?>
</head>
<?php
  }

  function xhtmlify($s) {
    return htmlentities($s,ENT_COMPAT,'UTF-8');
    #return htmlentities(mb_convert_encoding($s,'utf-8,'utf-8'),ENT_COMPAT,'UTF-8');
  }

  function local_url($page, $args='') {
    global $config;
    if (isset($config['userewrite']) && $config['userewrite']) {
      return BASEURL.rawurlencode($page).(!empty($args)?'?'.$args:'');
    }
    return BASEURL.'?page='.rawurlencode($page).(!empty($args)?'&amp;'.$args:'');
  }

  function canonical_url($page, $args='', $sep='&amp') {
    global $config;
    if (isset($config['userewrite']) && $config['userewrite']) {
      return CANONICALURL.rawurlencode($page).(!empty($args)?'?'.$args:'');
    }
    return CANONICALURL.'?page='.rawurlencode($page).(!empty($args)?$sep.$args:'');
  }

  # unused ? fn
  function plaindate($e, $start=true) {
    $time= dbadmin_unixtime($e, $start);
    return date("d.M H:i", $time);
  }

  function iso8601($e, $start=true) {
    $time= dbadmin_unixtime($e, $start);
    return date("Ymd\THis\Z", $time);
    #return date(DateTime::ISO8601, $time);
  }

  function limit_text($s,$l=24) {
    if (strlen($s)<=$l) return ($s);
    return (substr($s,0,$l).'..');
  }

  function _slv($k, $c) {
    if ($k == $c) return ' selected="selected"';
    return '';
  }

  function gen_options ($d,$k) {
    foreach ($d as $v => $t) {
      echo '    <option value="'.xhtmlify($v).'"'._slv($k,$v).'>'.xhtmlify($t).'</option>'."\n";
    }
  }


  function bytes_to_text($bytesize) { 
    $sizearray = array('bytes', 'KiB', 'MiB', 'GiB'); 
    $d = 0; 
    while($bytesize / 1024 > 1 && $d < 3) 
      { $d++; $bytesize /= 1024.0; } 
    $unit = $sizearray[$d]; 
    if($d == 0) return number_format($bytesize) . " " . $unit; 
    else return number_format($bytesize, 2, '.', '') . " " . $unit; 
  } 

  function dirlisttable($listdir) {
    $dir = opendir($listdir); 
    $dirarray = array(); 
    $filearray = array(); 
    $dircount = 0; $filecount = 0; 
    while ($file_name = readdir($dir)) {
      if(($file_name == ".") || ($file_name == "..") || ($file_name == "index.php")) continue;
      if (is_dir($listdir.'/'.$file_name)) { $dirarray[$dircount] = $file_name; $dircount++; } 
      else { $filearray[$filecount] = $file_name; $filecount++; }
    }
    sort($dirarray); reset($dirarray); sort($filearray); reset($filearray);
    closedir($dir); clearstatcache(); 

    echo '<div><h2>Dir Listing</h2>';
    echo '<table cellspacing="2" cellpadding="2" border="0">'; 
    echo '<tr><th align="center"><b>Filename</b></th><th align="center"><b>Size</b></th></tr>'; 
    #echo '<tr><th align="center"><b>Filename</b></th><th align="center"><b>Size</b></th><th align="center"><b>Created</b></th></tr>'; 

    for($a = 0; $a < $dircount; $a++) {
      echo '<tr><td><a href="'.BASEURL.'download/'.$dirarray[$a].'">'.$dirarray[$a].'</a></td><td>DIRECTORY</td><td></td></tr>'; 
    } 

    #echo '</table><table cellspacing="4" cellpadding"6">'; 
    for($b = 0; $b < $filecount; $b++) { 
       $currentfile = $listdir.'/'.$filearray[$b]; 
       $size = bytes_to_text((double) filesize($currentfile)); //Filesize UI 
       $time = strftime ("%b %d %Y %H:%M:%S", filectime($currentfile)); 
       echo '<tr><td><a href="'.BASEURL.$listdir.'/'.$filearray[$b].'">'.$filearray[$b].'</a></td><td align="right">'.$size.'</td></tr>'; 
       #echo '<tr><td><a href="'.$listdir.'/'.$filearray[$b].'">'.$filearray[$b].'</a></td><td align="right">'.$size.'</td><td align="right">'.$time.'</td></tr>'; 
    }
    echo '</table>';
    echo '</div>';
  }

$adminfs=array(
  array( 'title' => 'List all registrations', 'value' => 'List Participants',
         'page'  => 'admin', 'mode'  => 'list', 'param' => ''),
  array( 'title' => 'Show non empty remarks', 'value' => 'List Remarks',
         'page'  => 'admin', 'mode'  => 'remarks', 'param' => ''),
  array( 'title' => 'Generate list of email addresses', 'value' => 'Dump Email Contacts',
         'page'  => 'admin', 'mode'  => 'email', 'param' => ''),
  array( 'title' => 'Count Ordered Proceedings', 'value' => 'List Ordered Proceedings',
         'page'  => 'admin', 'mode'  => 'proceedings', 'param' => ''),
  #array('title' => 'Show Badges TeX', 'value' => 'Show Badges TeX',
  #      'page'  => 'admin', 'mode'  => 'badgestex', 'param' => ''),
  array( 'title' => 'Generate badges PDF', 'value' => 'Generate Badges PDF',
         'page'  => 'admin', 'mode'  => 'badgespdf', 'param' => ''),
  array( 'title' => 'Export comma separated value table', 'value' => 'Export registrations (CSV)',
         'page'  => 'admin', 'mode'  => 'csv', 'param' => ''),
);
       
$agendafs=array(
  array( 'title' => 'List Program Entries', 'value' => 'List Program Entries',
         'page'  => 'adminschedule', 'mode'  => '', 'param' => ''),
  array( 'title' => 'List Authors', 'value' => 'List Authors',
         'page'  => 'adminschedule', 'mode'  => 'listuser', 'param' => ''),
  array( 'title' => 'List Locations', 'value' => 'List Locations',
         'page'  => 'adminschedule', 'mode'  => 'listlocation', 'param' => ''),
  array( 'title' => 'Add Program Entry', 'value' => 'Add Program Entry',
         'page'  => 'adminschedule', 'mode'  => 'edit', 'param' => '-1'),
  array( 'title' => 'Add Author', 'value' => 'Add Author',
         'page'  => 'adminschedule', 'mode'  => 'edituser', 'param' => '-1'),
  array( 'title' => 'Add Location', 'value' => 'Add Location',
         'page'  => 'adminschedule', 'mode'  => 'editlocation', 'param' => '-1'),
  array( 'title' => 'Check Timetable for conflicts', 'value' => 'Check for conflicts',
         'page'  => 'adminschedule', 'mode'  => 'conflicts', 'param' => ''),
  array( 'title' => 'List orphaned entries', 'value' => 'List orphaned entries',
         'page'  => 'adminschedule', 'mode'  => 'orphans', 'param' => ''),
  array( 'title' => 'Export Program (CSV)', 'value' => 'Export Program (CSV)',
         'page'  => 'adminschedule', 'mode'  => 'export', 'param' => ''),
  array( 'title' => 'Profile Invites', 'value' => 'Profile Invites',
         'page'  => 'adminschedule', 'mode'  => 'profilenotify', 'param' => ''),
  array( 'title' => 'Profiles', 'value' => 'Profiles',
         'page'  => 'adminschedule', 'mode'  => 'profileinfo', 'param' => ''),
  array( 'title' => 'Texify Schedule', 'value' => 'Texify Schedule',
         'page'  => 'adminschedule', 'mode'  => 'texify', 'param' => ''),
);
$adminfieldsetonce=false;

  function admin_buttonset($btns, $title, $group) {
    echo '<fieldset class="fm">'."\n";
    echo '  <legend>'.$title.'</legend>'."\n";
    $i=0;
    foreach ($btns as $btn) {
      echo '  <input class="button" type="button" title="'.$btn['title']
          .'" value="'.$btn['value'].'" onclick="admingo(\''
          .$btn['page']."','" 
          .$btn['mode']."','" 
          .$btn['param']."');" 
          .'"/>'."\n";
      if ($group==0 ) continue;

      $i++;
      if (($i % abs($group)) > 0) {
        if($group>0) echo "&nbsp;\n";
      } else if ($i>0) {
        echo "<br/>\n";
      }
    }
    echo '</fieldset>'."\n";
  }

  function admin_fieldset($group=3) {
    global $adminfieldsetonce;
    if ($adminfieldsetonce) return;
    $adminfieldsetonce=true;
    global $adminfs,$agendafs;
    admin_buttonset($adminfs, 'Registration Admin:', $group);
    admin_buttonset($agendafs, 'Agenda Admin:', $group);
  }

  # libadmin
  function admin_fieldset2() {
    global $adminfieldsetonce;
    if ($adminfieldsetonce) return;
    $adminfieldsetonce=true;
?>
<fieldset class="fm">
    <legend>Registration Admin:</legend>
    <input class="button" type="button" title="List all registrations" value="List Participants" onclick="admingo('admin','list','');"/>
    &nbsp;
    <input class="button" type="button" title="Show non empty remarks" value="List Remarks" onclick="admingo('admin','remarks','');"/>
    &nbsp;
    <input class="button" type="button" title="Generate list of email addresses" value="Dump Email Contacts" onclick="admingo('admin','email','');"/>
    <br/>
    <input class="button" type="button" title="Count Ordered Proceedings" value="Count Ordered Proceedings" onclick="admingo('admin','proceedings','');"/>
<!-- <input class="button" type="button" title="Show Badges TeX" value="Show Badges TeX" onclick="admingo('admin','badgestex','');"/> !-->
    &nbsp;
    <input class="button" type="button" title="Generate badges PDF" value="Generate Badges PDF" onclick="admingo('admin','badgespdf','');"/>
    &nbsp;
    <input class="button" type="button" title="Export comma separated value table" value="Export registrations (CSV)" onclick="admingo('admin','csv','');"/>
    <br/>
  </fieldset>

  <fieldset class="fm">
    <legend>Agenda Admin:</legend>
    <input class="button" type="button" title="List Program Entries" value="List Program Entries" onclick="admingo('adminschedule','','');"/>
    &nbsp;
    <input class="button" type="button" title="List Authors" value="List Authors" onclick="admingo('adminschedule','listuser','');"/>
    &nbsp;
    <input class="button" type="button" title="List Locations" value="List Locations" onclick="admingo('adminschedule','listlocation','');"/>
    <br/>
    <input class="button" type="button" title="Add Program Entry" value="Add Program Entry" onclick="admingo('adminschedule','edit','-1');"/>
    &nbsp;
    <input class="button" type="button" title="Add Author" value="Add Author" onclick="admingo('adminschedule','edituser','-1');"/>
    &nbsp;
    <input class="button" type="button" title="Add Location" value="Add Location" onclick="admingo('adminschedule','editlocation','-1');"/>
    <br/>
    <div style="height:0.25em"> </div>
    <input class="button" type="button" title="Check Timetable for conflicts" value="Check for conflicts" onclick="admingo('adminschedule','conflicts','');"/>
    &nbsp;
    <input class="button" type="button" title="List orphaned entries" value="List orphaned entries" onclick="admingo('adminschedule','orphans','');"/>
    &nbsp;
    <input class="button" type="button" title="Export Program (CSV)" value="Export Program (CSV)" onclick="admingo('adminschedule','export','');"/>
    <br/>
  </fieldset>
<?php
  }

  function adminpage() {
    echo '
  <form action="index.php" method="post" id="myform">
  ';
    admin_fieldset();
    echo '
      <input name="page" type="hidden" value="admin" id="page"/>
      <input name="mode" type="hidden" value="" id="mode"/>
      <input name="param" type="hidden" value="" id="param"/>
  </form>
  <div style="height:1em;">&nbsp;</div>
  ';
  #  print_r($_POST); # XXX
  }

  function texify_umlauts($v) {
    $v=str_replace("\xc3\x9f",'{\\ss}',$v);
    $v=str_replace("\xc3\xa0",'\\`{a}',$v);
    $v=str_replace("\xc3\xa1",'\\\'{a}',$v);
    $v=str_replace("\xc3\xa2",'\\\^{a}',$v);
    $v=str_replace("\xc3\x84",'\\"{A}',$v);
    $v=str_replace("\xc3\xa4",'\\"{a}',$v);
    $v=str_replace("\xc3\xa8",'\\`{e}',$v);
    $v=str_replace("\xc3\xa9",'\\\'{e}',$v);
    $v=str_replace("\xc3\xaa",'\\^{e}',$v);
    $v=str_replace("\xc3\xb6",'\\"{o}',$v);
    $v=str_replace("\xc3\x96",'\\"{O}',$v);
    $v=str_replace("\xc3\xb8",'{\\o}',$v);
    $v=str_replace("\xc3\xb9",'\\`{u}',$v);
    $v=str_replace("\xc3\xba",'\\\'{u}',$v);
    $v=str_replace("\xc3\xbc",'\\"{u}',$v);
    $v=str_replace("\xc3\x9c",'\\"{U}',$v);
    $v=str_replace("\xc3\xbd",'\\\'{y}',$v);
    $v=str_replace("\xc3\xbf",'\\"{y}',$v);
    $v=str_replace("\xc3\xad",'\\\'{i}',$v);
    $v=str_replace("\xc3\xb2",'\\`{o}',$v);
    $v=str_replace("\xc3\xb2",'\\`{o}',$v);
    $v=str_replace("\xc4\x87",'\\\'{c}',$v);
    $v=str_replace("\xc3\xb3",'\\\'{o}',$v);
    $v=str_replace("\xc3\xa3",'\\~{a}',$v);
    $v=str_replace("\xc3\x98",'{\\O}',$v);
    $v=str_replace("\xc3\xa6",'{\\ae}',$v);
    $v=str_replace("\xc5\xa1",'\\v{s}',$v);
    $v=str_replace("\xc5\xa1",'\\v{s}',$v);
    $v=str_replace("\xc2\xb2",'$^2$',$v);
    $v=str_replace("&",'\&',$v);
    $v=str_replace("#",'\#',$v);
    return $v;
  }
# vim: ts=2 et
