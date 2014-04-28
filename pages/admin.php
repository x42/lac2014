<?php

if (!defined('REGLOGDIR')) die();

$mode='';
if (isset($_POST['mode'])) $mode=rawurldecode($_POST['mode']);

switch ($mode) {
  case 'vip_author':
    $mode='vip';
    $vip='author';
    break;
  case 'vip_organizer':
    $mode='vip';
    $vip='organizer';
    break;
  case 'vip_none':
    $mode='vip';
    $vip='';
    break;
  default:
    break;
}

adminpage();
switch ($mode) {
  case 'csv':
    $handle = fopen(TMPDIR.'registrations.csv', "w");
    fwrite($handle, export_sv(","));
    fclose($handle);
    echo 'Download: <a href="download.php?file=registrations.csv">registrations.csv</a>';
    break;
  case 'email':
    $r=scan_registrations();
    echo 'Email copy/paste:<br/>';
    echo '<pre style="font-size:9px; background:#ccc; line-height:1.3em;">'."\n";
    echo wordwrap(list_emails($r),100)."\n";
    echo '</pre><br/>'."\n";
    echo 'List of Participants:<br/>';
    show_fields($r,'reg_email');
    break;
  case 'badgespdf':
    $r=scan_registrations();
    gen_badges_pdf($r);
    echo '<div style="height:1em;">&nbsp;</div>';
    echo 'Download: <a href="download.php?file=lac2014badges.pdf">lac2014badges.pdf</a>';
    break;
  case 'badgestex':
    $r=scan_registrations();
    echo '<pre style="font-size:9px; background:#ccc; line-height:1em;">';
    echo gen_badges_source($r);
    echo '</pre>';
    break;
  case 'remarks':
    $r=scan_registrations();
    show_fields($r,'reg_notes');
    break;
  case 'proceedings':
    $r=scan_registrations();
    $v=count_fields($r,'reg_proceedings');
    echo '<p>Got '.$v.' requests for proceedings out of '.count($r).' total registrations.</p>';
    show_fields($r,'reg_proceedings');
    break;
  case 'food':
    $r=scan_registrations();
    show_fields($r,'reg_food');
    break;
  case 'detail':
    show_registration($_POST['param']);
    break;
  case 'vip':
    if (isset($vip))
      set_vip(rawurldecode($_POST['param']), $vip);
  case 'list':
    $r=scan_registrations();
    echo '<p>We have '.count($r).' registered participants:</p>';
    echo '<table class="adminlist" cellspacing="0">'."\n";
    echo '<tr><th>Name</th><th></th><th colspan="3">Change Attribution</th></tr>';
    foreach ($r as $f) {
      echo '<tr><td style="border-bottom: dotted 1px;">';
      echo substr($f, 16);
      echo '</td><td>';
      echo '<span style="cursor:pointer; color:blue;" onclick="document.getElementById(\'param\').value=\''.rawurlencode($f).'\';document.getElementById(\'mode\').value=\'detail\';formsubmit(\'myform\');">Show Details</span>';
      echo '</td><td>';

      $filename=$name = preg_replace('/[^a-zA-Z0-9_-]/','_', $f).'.ini';
      $v=parse_ini_file(REGLOGDIR.$filename);

      if (!isset($v['reg_vip'])) { $v['reg_vip']=''; }
      switch(strtolower($v['reg_vip'])) {
        case 'author':
          echo '<td><span style="font-weight:bold;">[Author]</span></td>';
          echo '<td><span style="cursor:pointer; color:blue;" onclick="document.getElementById(\'param\').value=\''.rawurlencode($f).'\';document.getElementById(\'mode\').value=\'vip_organizer\';formsubmit(\'myform\');">Organizer</span></td>';
          echo '<td><span style="cursor:pointer; color:blue;" onclick="document.getElementById(\'param\').value=\''.rawurlencode($f).'\';document.getElementById(\'mode\').value=\'vip_none\';formsubmit(\'myform\');">No-VIP</span></td>';
          break;
        case 'organizer':
          echo '<td><span style="cursor:pointer; color:blue;" onclick="document.getElementById(\'param\').value=\''.rawurlencode($f).'\';document.getElementById(\'mode\').value=\'vip_author\';formsubmit(\'myform\');">Author</span></td>';
          echo '<td><span style="font-weight:bold;">[Organizer]</span></td>';
          echo '<td><span style="cursor:pointer; color:blue;" onclick="document.getElementById(\'param\').value=\''.rawurlencode($f).'\';document.getElementById(\'mode\').value=\'vip_none\';formsubmit(\'myform\');">No-VIP</span></td>';
          break;
        default:
          echo '<td><span style="cursor:pointer; color:blue;" onclick="document.getElementById(\'param\').value=\''.rawurlencode($f).'\';document.getElementById(\'mode\').value=\'vip_author\';formsubmit(\'myform\');">Author</span></td>';
          echo '<td><span style="cursor:pointer; color:blue;" onclick="document.getElementById(\'param\').value=\''.rawurlencode($f).'\';document.getElementById(\'mode\').value=\'vip_organizer\';formsubmit(\'myform\');">Organizer</span></td>';
          echo '<td><span>[No-VIP]</span></td>';
          break;
      }
      echo '</td></tr>'."\n";
    }
    echo '</table>';
    break;
  default:
    echo 'Choose an action from the menu.';
    break;
}


function scan_registrations() {
  $dir = opendir(REGLOGDIR);
  $filearray = array();
  while ($file_name = readdir($dir))
    if($file_name[0] != '.' && is_file(REGLOGDIR.$file_name))
      $filearray[] = preg_replace('/\.ini$/','',$file_name);
  return $filearray;
}

function show_registration($fn) {
  $filename=$name = preg_replace('/[^a-zA-Z0-9_-]/','_', $fn).'.ini';
  echo '<p>File: '.$fn.'</p>';
  echo '<pre style="font-size:9px;">';
  echo wordwrap(format_registration(parse_ini_file(REGLOGDIR.$filename)), 100);
  echo '</pre>';
}

function set_vip($fn, $vip='author') {
  $filename=$name = REGLOGDIR.preg_replace('/[^a-zA-Z0-9_-]/','_', $fn).'.ini';
  # TODO flock file  ?

  #remove previous reg_vip (if any)
  $sh=fopen($filename, 'r');
  if (!$sh) {
    return false;
  }
  $th=fopen($filename.'.tmp', 'w');
  if (!$th) {
    fclose($sh);
    return false;
  }
  while (!feof($sh)) {
    $line=fgets($sh);
    if (strpos($line, 'reg_vip')===false) {
      fwrite($th, $line);
    }
  }
  fclose($sh);
  if (!empty($vip)) {
    fwrite($th, 'reg_vip="'.preg_replace('/[";]/','.',$vip)."\"\n");
  }
  fclose($th);
  #delete old source file
  unlink($filename);
  #rename target file to source file
  rename($filename.'.tmp', $filename);
}

function count_fields($f, $k) {
  $cnt=0;
  foreach ($f as $fn) {
    $filename=$name = preg_replace('/[^a-zA-Z0-9_-]/','_', $fn).'.ini';
    $v=parse_ini_file(REGLOGDIR.$filename);
    if ($v[$k]) $cnt++;
  }
  return $cnt;
}

function show_fields($f, $k) {
  $found=0;
  echo "<ul>\n";
  foreach ($f as $fn) {
    $filename=$name = preg_replace('/[^a-zA-Z0-9_-]/','_', $fn).'.ini';
    $v=parse_ini_file(REGLOGDIR.$filename);
    if (!empty($v[$k])) {
      $found++;
      echo '<li style="cursor:pointer; color:blue;" onclick="document.getElementById(\'param\').value=\''.rawurlencode($fn).'\';document.getElementById(\'mode\').value=\'detail\';formsubmit(\'myform\');">';
      echo $v['reg_prename'].' '.$v['reg_name'].': '.$v[$k].'</li>'."\n";
    }
  }
  echo "</ul>\n";
  if ($found==0 ) {
    echo '<div class="error">No entries found.</div>';
    return;
  }
}

function list_emails($f) {
  $rv='';
  foreach ($f as $fn) {
    $filename=$name = preg_replace('/[^a-zA-Z0-9_-]/','_', $fn).'.ini';
    $v=parse_ini_file(REGLOGDIR.$filename);
    $rv.=$v['reg_email'].', ';
  }
  return $rv;
}

#export SV escape
function exes($text, $sep="\t") {
  # replace $sep with /space/; replace '"' with "''"
  if ($sep != ' ')
    $text=str_replace($sep,' ',$text);
  $text=str_replace('"',"''",$text);
  return $text;
}

function export_sv($sep="\t") {
  $rv='';
  $rv.= '"Last Name"'.$sep;
  $rv.= '"First Name"'.$sep;
  $rv.= '"Tagline"'.$sep;
  $rv.= '"Email"'.$sep;
  $rv.= '"Age"'.$sep;
  $rv.= '"County"'.$sep;
  $rv.= '"Using Linux"'.$sep;
  $rv.= '"Profi"'.$sep;
  $rv.= '"Interests"'.$sep;
  $rv.= '"Profession"'.$sep;
  $rv.= '"Proceedings"'.$sep;
  $rv.= '"Public reg."'.$sep;
  $rv.= '"VIP"'.$sep;
  $rv.= '"Notes"'."\n";

  $r=scan_registrations();

  foreach ($r as $fn) {
    $filename=$name = preg_replace('/[^a-zA-Z0-9_-]/','_', $fn).'.ini';
    $v=parse_ini_file(REGLOGDIR.$filename);

    $rv.= '"'.exes($v['reg_name']).'"'.$sep;
    $rv.= '"'.exes($v['reg_prename']).'"'.$sep;
    $rv.= '"'.exes($v['reg_tagline']).'"'.$sep;
    $rv.= '"'.exes($v['reg_email']).'"'.$sep;
    $rv.= '"'.exes(preg_replace('/A(\d{2})(.{2})/','${1}-${2}',$v['reg_agegroup'])).'"'.$sep;
    $rv.= '"'.exes($v['reg_country']).'"'.$sep;
    $rv.= '"'.($v['reg_useathome']?'at home, ':'').($v['reg_useatwork']?'at work':'').'"'.$sep;
    $rv.= '"'
      .(($v['reg_audiopro']==1)?'no':'')
      .(($v['reg_audiopro']==2)?'yes':'')
      .(($v['reg_audiopro']==0)?'??':'');
    $rv.= '"'.$sep;
    $rv.= '"'.exes($v['reg_about']);
#   $rv.= '"';
#   $rv.= ($v['reg_vmusician']?'Composer or musician, ':'');
#   $rv.= ($v['reg_vdj']?'DJ, ':'');
#   $rv.= ($v['reg_vswdeveloper']?'Software devel, ':'');
#   $rv.= ($v['reg_vhwdeveloper']?'Hardware devel, ':'');
#   $rv.= ($v['reg_vmediapro']?'Media Professional, ':'');
#   $rv.= ($v['reg_vmproducer']?'Music Producer, ':'');
#   $rv.= ($v['reg_vvproducer']?'Video Producer, ':'');
#   $rv.= ($v['reg_vresearcher']?'Researcher, ':'');
#   $rv.= ($v['reg_vswuser']?'User, ':'');
#   $rv.= ($v['reg_vpress']?'Press, ':'');
#   $rv.= ($v['reg_vinterested']?'Just Interested, ':'');
#   $rv.= ($v['reg_vother']?'Other':'');
    $rv.= '"'.$sep;
    $rv.= '"'.$v['reg_profession'].'"'.$sep;
    $rv.= '"'.($v['reg_proceedings']?'yes':'no').'"'.$sep;
    $rv.= '"'.($v['reg_whoelselist']?'yes':'no').'"'.$sep;
    if (isset($v['reg_vip'])) {
      $rv.= '"'.exes($v['reg_vip']).'"'.$sep;
    } else {
      $rv.= '""'.$sep;
    }
    $rv.= '"'.exes($v['reg_notes']).'"'."\n";
  }
  return $rv;
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////
function gen_badges_pdf($f) {
  $handle = fopen(TMPDIR.'lac2014badges.tex', "w");
  fwrite($handle, gen_badges_source($f));
  fclose($handle);
  @copy (DOCROOTDIR.'img/lac14badgebanner.png', TMPDIR.'badgelogo.png');
  @copy (DOCROOTDIR.'img/fonts/T1-WGL4x.enc', TMPDIR.'T1-WGL4x.enc');
  @copy (DOCROOTDIR.'img/fonts/ttfonts.map', TMPDIR.'ttfonts.map');
  @copy (DOCROOTDIR.'img/fonts/Univers.afm', TMPDIR.'Univers.afm');
  @copy (DOCROOTDIR.'img/fonts/Univers.tfm', TMPDIR.'Univers.tfm');
  @copy (DOCROOTDIR.'img/fonts/Univers.ttf', TMPDIR.'Univers.ttf');

  @unlink (TMPDIR.'lac2014badges.pdf');
  echo '<pre style="font-size:70%; line-height:1.2em;">';
  system('cd '.TMPDIR.'; pdflatex lac2014badges.tex');
  echo '</pre>';
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////

function mynamesort($a,$b) {
  $a = preg_replace('@^[0-9_]*-@', '', $a);
  $b = preg_replace('@^[0-9_]*-@', '', $b);
  return strcasecmp($a, $b);
}

function is_orga($fn) {
	# not very efficient but WTH.
	$filename=$name = preg_replace('/[^a-zA-Z0-9_-]/','_', $fn).'.ini';
	$v=parse_ini_file(REGLOGDIR.$filename);
	if (isset($v['reg_vip'])) {
		if (strtolower($v['reg_vip']) == 'organizer') {
			return true;
		}
	}
	return false;
}

function mytimesort($a,$b) {
	if (is_orga($a) && !is_orga($b)) return -1;
	if (!is_orga($a) && is_orga($b)) return 1;
  return strcasecmp($a, $b);
}

function read_wifi_keys() {
	$wlankeys = array();
	return $wlankeys;
	$wk=fopen(TMPDIR.'LAC13-wlan.csv', 'r');
	if (!$wk) return $wlankeys;

  while (!feof($wk)) {
		$line=fgets($wk);
		list($wu,$wp) = explode(';',$line);
		$wlankeys[] = array('user' => $wu, 'pass' => $wp);
	}
  fclose($wk);
	return $wlankeys;
}

function gen_badges_source($f) {
  global $config;
	usort($f, 'mytimesort');
	$wifikeys= array('user' => '', 'pass' => $config['WIFIKEY']);
  $cnt=0;
  $rv=badge_tex_header();
  $rv.='%
\begin{picture}(180,270)%
';
  $end = 10 * ceil(.1 * count($f));
	for ($i=0; $i < $end; $i++) {
		if (isset($f[$i])) {
			$fn = $f[$i];

		if (false) { // skip already printed registrations XXX
			# XXX WON't work properly if new organizers are registered!
      $regtime=preg_replace('@-.*$@', '', $fn);
			if (strcasecmp($regtime, '20130503_145204') <= 0) {
				continue;
			}
    }

    $filename=$name = preg_replace('/[^a-zA-Z0-9_-]/','_', $fn).'.ini';
    $v=parse_ini_file(REGLOGDIR.$filename);
		$name=str_replace(',','',$v['reg_prename'].' '.$v['reg_name']);

		if (empty($v['reg_prename']) && empty($v['reg_email'])) {
			$md5name='blank';
		} else {
			$md5name=md5($name.$email);
			setlocale(LC_CTYPE, "en_US.UTF-8");
			system('echo -ne "\xEF\xBB\xBFBEGIN:VCARD\nVERSION:3.0\r\n'
			.'N:"'.escapeshellarg($v['reg_name']).'";"'.escapeshellarg($v['reg_prename']).'"\r\n'
			.'EMAIL:"'.escapeshellarg($v['reg_email'])
			.'"\r\nEND:VCARD\r\n"'
			.' | qrencode -d 300 -o '.TMPDIR.'/qr/'.$md5name.'.png');
		}

    $prename=texify_umlauts(str_replace(',','',$v['reg_prename']));
    $famname=texify_umlauts(str_replace(',','',$v['reg_name']));
    $what=texify_umlauts($v['reg_tagline']);
    $badgebg='';
    if (isset($v['reg_vip'])) {
      switch(strtolower($v['reg_vip'])) {
        case 'author':
          $badgebg='Author';
          break;
        case 'organizer':
          $badgebg='Organizer';
          break;
        default:
          $badgebg='';
          break;
      }
    }

# http://web.image.ufl.edu/help/latex/fonts.shtml
#\tiny 5 5
#\scriptsize 7 7
#\footnotesize 8 8
#\small 9 9
#\normalsize 10 10
#\large 12 12
#\Large 14 14.40
#\LARGE 18 17.28
#\huge 20 20.74
#\Huge 24 24.88
	$cmp=preg_replace('@[^a-zA-Z ]@','', $prename.' '.$famname);
	if (strlen($cmp) > 25) {
		$prename='\\UniversTO '.$prename;
		$famname='\\UniversTO '.$famname;
	}
	else {
		$prename='\\UniversTT '.$prename;
		$famname='\\UniversTT '.$famname;
	}

		} else {
			$what = '';
			$prename='\\UniversTT {~}';
			$famname='\\UniversTT {~}';
			$md5name='blank';
			$badgebg='';
		}

	if (strlen($what) > 56) $what='\\UniversEight '.$what;
	elseif (strlen($what) > 40) $what='\\UniversNine '.$what;
	elseif (strlen($what) > 0)  $what='\\UniversTen '.$what;
	else $what='';
	if (!empty($what)) $what.="\\\\";

	if (!empty($badgebg)) $badgebg='\\UniversTen '.$badgebg."\\\\";


    $x=($cnt%2)?"90":"0.0";
    $y=270 - 54*floor(($cnt%10)/2);

    #$y+=0.1; ## vertical offset

    $rv.='\\put('.$x.','.$y.'){\\makebox(90,54)[t]{\\card{'.$prename.'}{'.$famname.'}{'.$what.'}{'.$badgebg.'}}}'."\n";
    $cnt++;
    if ($cnt%10 == 0) {
      $rv.='%
\\end{picture}

\\pagebreak

\\begin{picture}(180,270)%

%backside
';
      $rv.='\\put(0.0,270){\\makebox(90,54)[b]{\\bside{'.$wifikeys['user'].'}{'.$wifikeys['pass'].'}}}'."\n";
      $rv.='\\put(90, 270){\\makebox(90,54)[b]{\\bside{'.$wifikeys['user'].'}{'.$wifikeys['pass'].'}}}'."\n";
      $rv.='\\put(0.0,216){\\makebox(90,54)[b]{\\bside{'.$wifikeys['user'].'}{'.$wifikeys['pass'].'}}}'."\n";
      $rv.='\\put(90, 216){\\makebox(90,54)[b]{\\bside{'.$wifikeys['user'].'}{'.$wifikeys['pass'].'}}}'."\n";
      $rv.='\\put(0.0,162){\\makebox(90,54)[b]{\\bside{'.$wifikeys['user'].'}{'.$wifikeys['pass'].'}}}'."\n";
      $rv.='\\put(90, 162){\\makebox(90,54)[b]{\\bside{'.$wifikeys['user'].'}{'.$wifikeys['pass'].'}}}'."\n";
      $rv.='\\put(0.0,108){\\makebox(90,54)[b]{\\bside{'.$wifikeys['user'].'}{'.$wifikeys['pass'].'}}}'."\n";
      $rv.='\\put(90, 108){\\makebox(90,54)[b]{\\bside{'.$wifikeys['user'].'}{'.$wifikeys['pass'].'}}}'."\n";
      $rv.='\\put(0.0, 54){\\makebox(90,54)[b]{\\bside{'.$wifikeys['user'].'}{'.$wifikeys['pass'].'}}}'."\n";
			$rv.='\\put(90,  54){\\makebox(90,54)[b]{\\bside{'.$wifikeys['user'].'}{'.$wifikeys['pass'].'}}}'."\n";

      $rv.='%
\\end{picture}
';
			if ($cnt != $end) $rv.='%

\\pagebreak

\\begin{picture}(180,270)%
';
    }
	}

      $rv.='%

\\end{document}
';
 return $rv;
}

function badge_tex_header() {
  return '
\\documentclass[a4paper]{article}
\\usepackage{array}

%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% MARGINS %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
\\textwidth       180mm
\\textheight      270mm
\\oddsidemargin    -10.25mm
\\evensidemargin   -10.25mm
\\topmargin        28mm
\\itemindent      0.00in
\\parindent       0.00in


%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%  FONTS  %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
\\renewcommand{\\encodingdefault}{T1}
\\usepackage{fontenc}
\\font\\UniversEight Univers at 8pt
\\font\\UniversNine Univers at 9pt

\\font\\UniversTen Univers at 10pt
\\font\\UniversTO Univers at 20pt
\\font\\UniversTT Univers at 22pt

%%%%%%%%%%%%%%%%%%%%%%% IMAGES FOR LATEX AND PDFLATEX %%%%%%%%%%%%%%%%%%%%%%%
\\ifnum \\pdfoutput=0
  \\usepackage[dvips]{graphicx}
  \\usepackage{epsfig}
\\else
  \\usepackage[pdftex]{graphicx}
\\fi
\\newcommand{\\image}[2]{
  \\ifnum \\pdfoutput=0
    \\includegraphics[#1]{#2.eps}
  \\else
    \\includegraphics[#1]{#2.png}
  \\fi
}

%%%%%%%%%%%%%%%%%%%%%%%%%%%%% CARD MACRO [\card] %%%%%%%%%%%%%%%%%%%%%%%%%%%%
\\def\\card#1#2#3#4{
  \\begin{tabular}{>{\\centering\\hspace{0pt}}m{8.5cm}}
  \\vspace*{-1mm}\\\\%
  \\image{height=11mm}{badgelogo}
  \\vspace*{0mm}\\\\%
  {\UniversTen Linux Audio Conference 2014}
  \\vspace*{5mm}\\\\%
  {#1}
  \\vspace*{2mm}\\\\%
  {#2}
  \\vspace*{2mm}\\\\%
  #4
  #3
 \\end{tabular}%
}

\\def\\bside#1#2{
  \\begin{tabular}{rl}
  {\\tt ESSID}:& {\\tt lac2014}\\\\
%  Username:& {\\tt #1}\\\\
   Password:& {\\tt #2}\\\\
  \\vspace*{0mm}\\\\%
  \\end{tabular}
}


%%%%%%%%%%%%%%%%%%%%%%%%%%%% BEGIN DOCUMENT %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
\\pagestyle{empty}
\\begin{document}
\\setlength{\\unitlength}{1mm}%
';
}
