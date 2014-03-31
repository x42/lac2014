<?php
# TODO check if included by top-handler.
# TODO: search hardcoded* in this file -> config, settings

  try {
    $db=new PDO(PDOPRGDB);
  } catch (PDOException $exception) {
    die ('Database Failure: '.$exception->getMessage());
  }
  $db->sqliteCreateFunction("typesort", "typesort", 1);

  // returns -1 if lock was aquired; return time until lock expires
  function lock($db, $id, $table='activity') {
    if ($id<0) return -1; # ignore 'new' entries.
    $q='UPDATE '.$table.' set editlock=datetime(\'now\',\'+5 minutes\') WHERE editlock < datetime(\'now\') AND id='.$id.';';
    if ($db->exec($q) == 1) return -1;

    $q='SELECT editlock from '.$table.' WHERE id='.$id.';';
    $res=$db->query($q);
    if (!$res) return 0; # XXX error
    $r=$res->fetch(PDO::FETCH_ASSOC);
    return $r['editlock'];
  }

  function unlock($db,$id, $table='activity') {
    if ($id<0) return; # ignore 'new' entries.
    $q='UPDATE '.$table.' set editlock=0 WHERE id='.$id.';';
    $db->exec($q);
  }

  function typesort($t) {
    switch ($t) {
      case 'p': return 0;
      case 'l': return 0;
      case 'v': return 0;
      case 'w': return 1;
      case 'i': return 2;
      case 'c': return 3;
      default:  return 4;
    }
  }

  function color_type($t) {
    switch ($t) {
      case 'p': $col='#FF0000'; break;
      case 'w': $col='#CC8833'; break;
      case 'i': $col='#00AA88'; break;
      case 'c': $col='#00FF00'; break;
      case 'l': $col='#dddd00'; break;
      case 'v': $col='#997744'; break;
      default:  $col='#0000FF'; break;
    }
    return '<span style="color:'.$col.';">'.$t.'</span>';
  }

  function track_legend() {
    # XXX - hardcoded session/track XXX
    $rv='<div style="width:100%; margin:.5em;">';
    $rv.='<table cellspacing="0" class="legend">';
    $rv.='<tr>';
    $rv.='<td class="trX" colspan="3">Legend</td>';
    $rv.='</tr><tr>';
    $rv.='<td class="tr1">'.track_name('tr1').'</td>';
    $rv.='<td class="tr2">'.track_name('tr2').'</td>';
    $rv.='<td class="tr6">'.track_name('tr6').'</td>';
    $rv.='</tr><tr>';
    $rv.='<td class="tr4">'.track_name('tr4').'</td>';
    $rv.='<td class="tr5">'.track_name('tr5').'</td>';
    $rv.='<td class="trl">'.track_name('trl').'</td>';
    $rv.='</tr><tr>';
    $rv.='<td class="tr7">'.track_name('tr7').'</td>';
    $rv.='<td class="tr3">'.track_name('tr3').'</td>';
    $rv.='<td class="trp">'.track_name('trp').'</td>';
    $rv.='</tr><tr>';
    $rv.='<td></td>';
    $rv.='<td class="trw">'.track_name('trw').'</td>';
    $rv.='<td class="tro">'.track_name('tro').'</td>';
    $rv.='</tr>';
    $rv.='</table></div>';
    return $rv;
  }

  function track_name($tr) {
    # XXX - hardcoded session/track XXX
    switch ($tr) {
      case 'tr1': return 'Audio and Web';
      case 'tr2': return 'Tools to make Tools';
      case 'tr3': return 'SurSound and Ambisonics';
      case 'tr4': return 'Music Programming';
      case 'tr5': return 'Interfaces and Hardware';
      case 'tr6': return 'Live Coding';
      case 'tr7': return 'Licensing';
      case 'trp': return 'Poster Session';
      case 'trl': return 'Lightning Talk';
      case 'trw': return 'Workshop';
      case 'tro': return 'Miscellaneous';
      case 'trm': return 'Concert';
      case 'trmK': return 'Kubus Concert';
      case 'trmL': return 'Lounge Concert';
      case 'trmS': return 'Sound Night';
      default: return '';
    }
  }
  function track_color($d) {
    # XXX - hardcoded session/track XXX
    if (substr($d['title'],0,7) == 'COFFEE ') return 'trz';
    if (substr($d['title'],0,6) == 'LUNCH ') return 'trz';
    if ($d['title'] == 'Poster Session') return 'trp';
    if ($d['day'] == 2 && $d['type'] == 'l') return 'trl';
    if ($d['day'] == 4) return 'trz';
    if ($d['type'] == 'w') return 'trw';
    if ($d['type'] == 'c' && $d['starttime'] == '20:00') return 'trmK';
    if ($d['type'] == 'c' && $d['starttime'] == '22:00' && $d['day'] == 2) return 'trmL';
    if ($d['type'] == 'c' && $d['starttime'] == '22:00' && $d['day'] == 3) return 'trmS';
    if ($d['type'] == 'c') return 'trm';
    if ($d['type'] == 'i') return 'tri';
    if ($d['type'] == 'v') return 'trp';
    if ($d['type'] != 'p') return 'tro';
    switch ($d['track']) {
      case 1: return 'tr1';
      case 2: return 'tr2';
      case 3: return 'tr3';
      case 4: return 'tr4';
      case 5: return 'tr5';
      case 6: return 'tr6';
      case 7: return 'tr7';
    }
    return 'tr0';
  }

  function translate_type($t) {
    switch ($t) {
      case 'p': return 'Paper Presentation';
      case 'l': return 'Lightning Talk';
      case 'v': return 'Poster Presentation';
      case 'w': return 'Workshop';
      case 'i': return 'Installation/Loop';
      case 'c': return 'Concert';
      default: return '(misc event)';
    }
  }

  function translate_time($t) {
    #TODO: unify w/ dbadmin_editform
    /* US only :)
    $a_times = array(
                      '9:00'  => '9am' ,  '9:45' => '9:45'
                    , '10:00' => '10am', '10:15' =>'10:15', '10:30' =>'10:30', '10:45' => '10:45'
                    , '11:00' => '11am', '11:15' =>'11:15', '11:30' =>'11:30', '11:45' => '11:45'
                    , '12:00' => '12pm', '12:15' =>'12:15', '12:30' =>'12:30', '12:45' => '12:45'
                    , '13:00' => '1pm',  '13:15' => '1:15', '13:30' => '1:30', '13:45' =>  '1:45'
                    , '14:00' => '2pm',  '14:15' => '2:15', '14:30' => '2:30', '14:45' =>  '2:45'
                    , '15:00' => '3pm',  '15:15' => '3:15', '15:30' => '3:30', '15:45' =>  '3:45'
                    , '16:00' => '4pm',  '16:15' => '4:15', '16:30' => '4:30', '16:45' =>  '4:45'
                    , '17:00' => '5pm',  '17:15' => '5:15', '17:30' => '5:30', '17:45' =>  '5:45'
                    , '18:00' => '6pm',  '18:15' => '6:15'
                    , '20:00' => '8pm',  '22:00' => '10pm'
                  );
    if (isset($a_times[$t])) return $a_times[$t];
     */
    return $t;
  }

  function say_db_message($msg='') {
    if ($msg)
      echo '<div class="dbmsg">'.$msg.'</div>'."\n";
  }

  function say_db_error($msg='unknown') {
    global $db;
    echo 'DATABASE ERROR: "'.$msg.'" '.print_r($db->errorInfo(),true)."\n";
  }

  function db_select_one($db, $query) {
    $res=$db->query($query);
    if (!$res) {
      say_db_error('db_select_one');
      return null;
    }
    return $res->fetch(PDO::FETCH_ASSOC);
  }

  function fetch_activity_by_location($db, $location_id) {
    $rv = array();
    $q='SELECT DISTINCT title, id from activity
       WHERE location_id ='.$location_id.';';
    $res=$db->query($q);
    if (!$res) { say_db_error('authorids'); return $rv;}
    $result=$res->fetchAll();
    foreach ($result as $r) {
      $rv[]=$r;
    }
    return $rv;
  }


  function fetch_activity_by_author($db, $user_id) {
    $rv = array();
    #$q='SELECT activity_id from usermap where user_id='.$user_id.';';
    $q='SELECT DISTINCT activity.title AS title, activity.id AS id, activity.day AS day, activity.starttime AS starttime, activity.duration AS duration, activity.type AS type, usermap.activity_id from activity,usermap
       WHERE activity.id = usermap.activity_id AND usermap.user_id='.$user_id.';';
    $res=$db->query($q);
    if (!$res) { say_db_error('authorids'); return $rv;}
    $result=$res->fetchAll();
    foreach ($result as $r) {
      $rv[]=$r;
    }
    return $rv;
  }

  function fetch_authorids($db, $activity_id) {
    $rv = array();
    $q='SELECT user_id from usermap where activity_id='.$activity_id.' ORDER BY position;';
    $res=$db->query($q);
    if (!$res) { say_db_error('authorids'); return $rv;}
    $result=$res->fetchAll();
    foreach ($result as $r) {
      $rv[]=intval($r['user_id']);
    }
    return $rv;
  }

  function conference_dayend($day) {
    return strtotime((0+intval($day)).' May 2014 23:00:00 CEST'); # TODO -> config start-date and dates
  }

  function fetch_selectlist($db, $table='user', $order='ORDER BY id') {
    if ($table=='days')
      return array('1' => '1 - Thursday, May/1', '2' => '2 - Friday, May/2', '3' => '3 - Saturday, May/3', '4' => '4 - Sunday, May/4');
    if ($table=='daysx')
      return array('1' => '1 - Thursday, May/1', '2' => '2 - Friday, May/2', '3' => '3 - Saturday, May/3', '4' => '4 - Sunday, May/4', '5' => '-every day-');
    if ($table=='daylist') # TODO derive from 'days' - note: start at '1'.
      return array(1=> 'Thursday, May/1', 2=> 'Friday, May/2', 3=> 'Saturday, May/3', 4=> 'Sunday, May/4');

    if ($table=='types')
      return array('p' => 'Paper Presentation', 'w' => 'Workshop', 'l' => 'Lightning Talk', 'w' => 'Workshop', 'v' => 'Poster', 'c' => 'Concert', 'i' => 'Installation', 'o' => 'Other');
    if ($table=='durations')
      return array('' => '-unset-', '10' => '10 mins', '30' => '30 mins', '45' => '45 mins', '60' => '1 hour', '75' => '75 mins', '90' => '90 mins', '120' => '2 hours', '135' => '2 1/4 hours',  '160' => '2 3/4 hours', '180' => '3 hours');
    if ($table=='status')
      return array('2' => 'hidden', '1' => 'confirmed', '0' => 'cancelled');

    $rv = array('0' => '-unset-');
    $q='SELECT id, name from '.$table.' '.$order.';';
    $res=$db->query($q);
    if (!$res) { say_db_error(); return $rv;}
    $result=$res->fetchAll();
    foreach ($result as $r) {
      $rv[$r['id']] = $r['name'];
    }
    return $rv;
  }

  function dbadmin_jumpselected() {
    if (isset($_REQUEST['param'])) {
      $id=intval(rawurldecode($_REQUEST['param']));
      if ($id>0)
        echo '<script type="text/javascript">'."\nadminjump('jan-$id');\n".'</script>'."\n";
    }
  }

  function dbadmin_listlocations($db) {
    $q='SELECT id, name from location ORDER BY id;';
    $res=$db->query($q);
    if (!$res) { say_db_error(); return $rv;}
    $result=$res->fetchAll();
    echo '<table class="adminlist" cellspacing="0">'."\n";
    echo '<tr><th></th><th>Location</th><th>#talks</td><th>&nbsp;</th>';
    $alt=0;
    foreach ($result as $r) {
      $aids=fetch_activity_by_location($db,$r['id']);
      echo '<tr'.(($alt++%2==1)?' class="alt"':'').'>';
      echo '<td>('.$r['id'].')<a id="jan-'.$r['id'].'" name="jan-'.$r['id'].'"/>&nbsp;</td>';
      echo '<td>'.xhtmlify($r['name']).'</td>';
      echo '<td class="center'.((count($aids)==0)?' red':'').'">'.count($aids).'</td><td>';
      echo '<a class="active" onclick="document.getElementById(\'param\').value='.$r['id'].';document.getElementById(\'mode\').value=\'editlocation\';formsubmit(\'myform\');">Edit</a>';
      echo '&nbsp;|&nbsp;';
      echo '<a class="active" onclick="if (confirm(\'Really delete Location no. '.$r['id'].'?\')) {document.getElementById(\'param\').value='.$r['id'].';document.getElementById(\'mode\').value=\'dellocation\';formsubmit(\'myform\');}">Delete</a>';
      echo '</td></tr>'."\n";
    }
    echo '</table>'."\n";
  }

  # 0: no ukey, no profile
  # 1: ukey-sent
  # 2: profile public
  # 3: profile public & ukey-sent
  function usr_has_profile($db, $uid) {
    $rv=0;
    $q='SELECT flags from user WHERE id='.intval($uid).';';
    $res=$db->query($q);
    if ($res) {
      $d=$res->fetch(PDO::FETCH_ASSOC);
      if ($d['flags'] & 1 ) $rv|=2;
    }
    $q='SELECT ukey from auth WHERE user_id='.intval($uid).';';
    $res=$db->query($q);
    if ($res) {
      $d=$res->fetch(PDO::FETCH_ASSOC);
      if (!empty($d['ukey'])) $rv|=1;
    }
    return $rv;
  }

  function dbadmin_listusers($db) {
    $q='SELECT id, name, email, bio, vip from user ORDER BY name;';
    $res=$db->query($q);
    if (!$res) { say_db_error(); return $rv;}
    $result=$res->fetchAll();
    echo '<table class="adminlist" cellspacing="0">'."\n";
    echo '<tr><th></th><th>Username</th><th>#talks</td><th>Email</th><th>Short Bio</th><th>&nbsp;</th>';
    $alt=0;
    $emaillist='';
    $profilecolours = array('#f00', '#0a0', '#a80', '#000');
    foreach ($result as $r) {
      $aids=fetch_activity_by_author($db,$r['id']);
      echo '<tr'.(($alt++%2==1)?' class="alt"':'').'>';
      echo '<td><span style="color:'.$profilecolours[usr_has_profile($db, $r['id'])].'">('.$r['id'].')</span>';
      echo '<a id="jan-'.$r['id'].'" name="jan-'.$r['id'].'"/>&nbsp;</td><td>'.xhtmlify($r['name']).'</td>';
      if (count($aids)==0) {
        if (!($r['vip']&1))
          echo '<td class="xpad">0';
        else
          echo '<td class="xpad red">0';
      } else
        echo '<td class="xpad"><a class="active" onclick="document.getElementById(\'pdb_filterauthor\').value=\''.$r['id'].'\';document.getElementById(\'param\').value=\'-1\';document.getElementById(\'mode\').value=\'\';formsubmit(\'myform\');">'.count($aids).'</a>';
      if (!($r['vip']&1))  echo '-S';
      if ($r['vip']&14) echo '+';
      if ($r['vip']&8)  echo 'A';
      if ($r['vip']&4)  echo 'C';
      if ($r['vip']&2)  echo 'O';
      echo '</td>';

      echo '<td>'.xhtmlify($r['email']).'</td>';
      if (!empty($r['email']) && count($aids)>0)
        $emaillist.=$r['email'].', ';
      echo '<td>'.limit_text($r['bio'],90).'</td><td>';
      echo '<a class="active" onclick="document.getElementById(\'param\').value='.$r['id'].';document.getElementById(\'mode\').value=\'edituser\';formsubmit(\'myform\');">Edit</a>';
      echo '&nbsp;|&nbsp;';
      echo '<a class="active" onclick="if (confirm(\'Really delete User no. '.$r['id'].'?\')) {document.getElementById(\'param\').value='.$r['id'].';document.getElementById(\'mode\').value=\'deluser\';formsubmit(\'myform\');i}">Delete</a>';
      echo '</td></tr>'."\n";
    }
    echo '</table>'."\n";
    echo '<fieldset style="width:45%; float:right; clear:none;"><legend>Keys for #talks:</legend>';
    echo '<p><br/>Keys for #talks:<br/>';
    echo '<tt><b>-S</b></tt>: No <b>S</b>peaker<br/>';
    echo '<tt><b>+A</b></tt>: <b>A</b>rtist (musician, composer)<br/>';
    echo '<tt><b>+C</b></tt>: Review <b>C</b>ommitte Member<br/>';
    echo '<tt><b>+O</b></tt>: Conference <b>O</b>rganizer<br/>';
    echo '</fieldset>';
    echo '<fieldset style="width:45%; float:left; clear:none;"><legend>ID Color Key</legend>';
    echo '<span style="color:'.$profilecolours[0].'">Hidden Profile - user not yet invited</span><br/>';
    echo '<span style="color:'.$profilecolours[1].'">Hidden Profile - User Notified</span><br/>';
    echo '<span style="color:'.$profilecolours[2].'">Public (default) Profile - no invite sent</span><br/>';
    echo '<span style="color:'.$profilecolours[3].'">Public Profile - Invitation sent</span><br/>';
    echo '</fieldset>';
    echo '<div class="clearer"></div>';

    echo '<hr/>'."\n Speakers/Artists email:";
    echo '<pre style="font-size:9px; background:#ccc; line-height:1.3em;margin-top:2em;">';
    echo wordwrap($emaillist,100);
    echo '</pre><br/>';
  }

  function dbadmin_listall($db, $order='time') {
    $a_users = fetch_selectlist($db, 'user', 'ORDER BY name');
    $a_locations = fetch_selectlist($db, 'location');

    $filter=array('user' => '0', 'day' => '0', 'type' => '0');

    if (1 || $_REQUEST['param'] == -1) { // filter enable
      if (isset($_REQUEST['pdb_filterday'])) $filter['day'] = intval(rawurldecode($_REQUEST['pdb_filterday']));
      if (isset($_REQUEST['pdb_filtertype'])) $filter['type'] = substr(rawurldecode($_REQUEST['pdb_filtertype']),0,1);
      if (isset($_REQUEST['pdb_filterauthor'])) $filter['user'] = intval(rawurldecode($_REQUEST['pdb_filterauthor']));
    }
    echo '<fieldset>';
    print_filterfields($a_users, $a_locations, $filter);
    echo '</fieldset>';

    if ($filter['user'] != '0')
      $q = 'SELECT DISTINCT activity.*
      FROM activity,user,usermap
      WHERE activity.id=usermap.activity_id AND user.id=usermap.user_id
        AND user.id='.$filter['user'];
    else
      $q='SELECT activity.* FROM activity WHERE 1=1';

    if ($filter['type'] != '0') $q.=' AND type='.$db->quote($filter['type']);
    if ($filter['day'] > 0) $q.=' AND day='.$db->quote($filter['day']);

    if ($order=='type')
      $q.=' ORDER BY day, typesort(type), strftime(\'%H:%M\',starttime), location_id, serial;';
    else
      $q.=' ORDER BY day, strftime(\'%H:%M\',starttime), typesort(type), location_id, serial;';

    $res=$db->query($q);
    if (!$res) { say_db_error(); return;}
    $result=$res->fetchAll();

    if (count($result) == 0) {
      echo '<div class="center red">No matching entries found...</div>';
      return;
    }
    echo '<div class="right">'.count($result).' matching entrie(s) found.</div>';

    echo '<table class="adminlist" cellspacing="0">'."\n";
    echo '<tr><th>';
    if ($order=='type')
      echo '<span class="underline">Type</span>-Day-<a class="active" onclick="document.getElementById(\'sort\').value=\'time\';document.getElementById(\'mode\').value=\'\';formsubmit(\'myform\');">Tm</a>';
    else
      echo '<a class="active" onclick="document.getElementById(\'sort\').value=\'type\';document.getElementById(\'mode\').value=\'\';formsubmit(\'myform\');">Type</a>-Day-<span class="underline">Tm</span>';
    echo '</th><th>Title - <em>Author</em></th><th style="width:9em;">Location</th><th>&nbsp;</th></tr>'."\n";

    $alt=0;
    foreach ($result as $r) {
      echo '<tr'.(($alt++%2==1)?' class="alt"':'').'>';
      echo '<td><a id="jan-'.$r['id'].'" name="jan-'.$r['id'].'"/><tt>'.color_type($r['type']).'-'.$r['day'].'-'.$r['starttime'].'</tt></td>';
      echo '<td'.(($r['status']==0)?' class="cancelled"':'').'><b>'.limit_text($r['title'],140).'</b>&nbsp;';

      echo '<em>'; $i=0;
      foreach (fetch_authorids($db, $r['id']) as $user_id) {
        if ($i++>0) echo ', ';
        #echo $user_id.': ';
  #echo xhtmlify($a_users[$user_id]);
        echo '<a class="active" onclick="document.getElementById(\'param\').value='.$user_id.';document.getElementById(\'mode\').value=\'edituser\';formsubmit(\'myform\');">'.xhtmlify($a_users[$user_id]).'</a>';
      }
      echo '</em></td>';

      if (!empty($r['location_id']))
        echo '<td>'.xhtmlify($a_locations[$r['location_id']]).'</td>';
      else
        echo '<td>??</td>';

      echo '<td><a class="active" onclick="document.getElementById(\'param\').value='.$r['id'].';document.getElementById(\'mode\').value=\'edit\';formsubmit(\'myform\');">Edit</a>';
      echo '&nbsp;|&nbsp;';
      echo '<a class="active" onclick="if (confirm(\'Really delete Entry no. '.$r['id'].'?\')) {document.getElementById(\'param\').value='.$r['id'].';document.getElementById(\'mode\').value=\'delentry\';formsubmit(\'myform\');}">Delete</a>';
      echo '</td></tr>'."\n";
    }
    echo '</table>'."\n";

    echo '<fieldset><legend>Keys for type:</legend>';
    $a_types = fetch_selectlist(0, 'types');
    foreach ($a_types as $k => $v) {
      echo '&nbsp;&nbsp;<tt><b>'.color_type($k).'</b></tt>: '.$v.'<br/>';
    }
    echo '</fieldset>';
  }

  function dbadmin_locationform($db, $id) {
    if ($id > 0) {
      $q='SELECT * FROM location WHERE id ='.$id.';';
      $res=$db->query($q);
      if (!$res) { say_db_error(); return;}
      $r=$res->fetch(PDO::FETCH_ASSOC);
      echo 'ID: '.$id.'<br/>';
    } else {
      $r=array('name' =>'', 'id' => -1);
      echo 'ID: new<br/>';
    }
    echo '<label for="pdb_name">Name:</label><br/>';
    echo '<input id="pdb_name" name="pdb_name" length="80" value="'.$r['name'].'" /><br/>';
    echo '<input class="button" type="button" title="Save" value="Save" onclick="document.getElementById(\'param\').value='.$r['id'].';document.getElementById(\'mode\').value=\'savelocation\';formsubmit(\'myform\');"/>'."\n";
    echo '<input class="button" type="button" title="Cancel" value="Cancel" onclick="document.getElementById(\'param\').value='.$r['id'].';document.getElementById(\'mode\').value=\'unlocklocation\';formsubmit(\'myform\');"/>'."<br/>&nbsp;\n";
  }

  function html_text_input($title, $param, $data) {
    echo '<label for="pdb_'.$param.'">'.$title.':</label><br/>';
    echo '<input id="pdb_'.$param.'" name="pdb_'.$param.'" length="80" value="'.$data[$param].'" /><br/>';
  }
  function html_text_readonly($title, $param, $data) {
    echo '<label for="pdb_'.$param.'">'.$title.':</label>&nbsp;';
    echo '<span>'.$data[$param].'</span><br/>';
  }

  function html_checkbox($title, $param, $onoff) {
    echo '<label for="pdb_'.$param.'">'.$title.':&nbsp;';
    echo '<input style="width:2em;" type="checkbox" id="pdb_'.$param.'" name="pdb_'.$param.'"'.($onoff?' checked="checked"':'').'/>';
    echo '</label> &nbsp;';
  }

  function dbadmin_authorform($db, $id) {
    echo '<p>';
    if ($id > 0) {
      $q='SELECT * FROM user WHERE id ='.$id.';';
      $res=$db->query($q);
      if (!$res) { say_db_error(); return;}
      $r=$res->fetch(PDO::FETCH_ASSOC);
      echo 'ID: '.$id.'<br/>';
    } else {
      $r=array('name' =>'', 'bio' => '', 'email' => '', 'id' => -1, 'tagline' => '',
        'flags' => 1, 'vip' => 1,
        'url_image' => '', 'url_person' => '',
        'url_institute' => '', 'url_project' => '',
        'reghandle' => '', # XXX internal use only
      );
      echo 'ID: new<br/>';
    }
    echo '</p>';

    echo '<p>';
    html_checkbox('Public Profile', 'flag_pub', $r['flags']&1);
    #echo '</p>'; echo '<p>';
    echo ' &nbsp; ';
    html_checkbox('Speaker', 'vip_s', $r['vip']&1);
    html_checkbox('Organizer', 'vip_o', $r['vip']&2);
    html_checkbox('Committee', 'vip_c', $r['vip']&4);
    html_checkbox('Artist', 'vip_a', $r['vip']&8);
    echo '</p>';

    html_text_input('Name', 'name', $r);
    html_text_input('Email', 'email', $r);
    html_text_input('Tagline/Affiliation', 'tagline', $r);

    html_text_input('Image URL', 'url_image', $r);
    html_text_input('URL 1 (Personal)', 'url_person', $r);
    html_text_input('URL 2 (Institute)', 'url_institute', $r);
    html_text_input('URL 3 (Project)', 'url_project', $r);

    echo '<label for="pdb_bio">Bio:</label><br/>';
    echo '<textarea id="pdb_bio" name="pdb_bio" rows="8" cols="70">'.xhtmlify($r['bio']).'</textarea><br/><br/>';

    echo '<input class="button" type="button" title="Save" value="Save" onclick="document.getElementById(\'param\').value='.$r['id'].';document.getElementById(\'mode\').value=\'saveuser\';formsubmit(\'myform\');"/>'."\n";
    echo '<input class="button" type="button" title="Cancel" value="Cancel" onclick="document.getElementById(\'param\').value='.$r['id'].';document.getElementById(\'mode\').value=\'unlockuser\';formsubmit(\'myform\');"/>'."<br/>&nbsp;\n";
  }

  function dbadmin_editform($db, $id) {
    # TODO unify w/ translate_time
    $a_times = array(
                    '' => '-unset-'
                    , '9:00' => '9:00', '9:30' => '9:30'
                    , '10:00' => '10:00' , '10:15' => '10:15', '10:30' => '10:30', '10:45' => '10:45'
                    , '11:00' => '11:00' , '11:15' => '11:15', '11:30' => '11:30'
                    , '11:40' => '11:40' , '11:45' => '11:45', '11:50' => '11:50'
                    , '12:00' => '12:00' , '12:10' => '12:10', '12:15' => '12:15'
                    , '12:20' => '12:20' , '12:30' => '12:30'
                    , '13:00' => '13:00' , '13:30' => '13:30'
                    , '14:00' => '14:00' , '14:15' => '14:15', '14:30' => '14:30', '14:45' => '14:45'
                    , '15:00' => '15:00' , '15:15' => '15:15', '15:30' => '15:30', '15:45' => '15:45'
                    , '16:00' => '16:00' , '16:15' => '16:15', '16:30' => '16:30', '16:45' => '16:45'
                    , '17:00' => '17:00' , '17:15' => '17:15', '17:30' => '17:30', '17:45' => '17:45'

                    , '18:00' => '18:00' , '18:30' => '18:30'
                    , '19:00' => '19:00' , '19:30' => '19:30'
                    , '20:00' => '20:00' , '20:30' => '20:30'
                    , '21:00' => '21:00' , '21:30' => '21:30'
                    , '22:00' => '22:00' , '22:30' => '22:30'
                    , '23:00' => '23:00' , '23:30' => '23:30'
                    , '24:00' => '0:00'
               );
    $a_durations = fetch_selectlist(0, 'durations');
    $a_days = fetch_selectlist(0, 'daysx');
    $a_types = fetch_selectlist(0, 'types');
    $a_status = fetch_selectlist(0, 'status');
    $a_locations = fetch_selectlist($db, 'location');
    $a_users = fetch_selectlist($db, 'user', 'ORDER BY name');

    if ($id > 0) {
      $q='SELECT * FROM activity WHERE activity.id ='.$id.';';

      $res=$db->query($q);
      if (!$res) { say_db_error(); return;}
      $r=$res->fetch(PDO::FETCH_ASSOC);
      echo 'ID: '.$id.'&nbsp;&nbsp;';
    } else {
      $r=array('id' => -1, 'title' => '', 'day' => 1, 'type' => 'p' , 'starttime' => '' , 'location_id' => '' , 'duration' => '' , 'abstract' => '' , 'notes' => '', 'url_paper' => '', 'url_misc' => '', 'url_audio' => '', 'url_stream' => '', 'url_slides' => '', 'url_image' => '', 'status' => 1);
      echo 'ID: new&nbsp;&nbsp;';
    }
    echo '<label for="pdb_type">Type:</label>';
    echo '<select id="pdb_type" name="pdb_type" size="1">';
    gen_options($a_types, $r['type']);
    echo '</select>&nbsp;';

    echo '<label for="pdb_status">Status:</label>';
    echo '<select id="pdb_status" name="pdb_status" size="1">';
    gen_options($a_status, $r['status']);
    echo '</select>';

    if ($r['type']== 'c' || $r['type']== 'i') {
      echo '&nbsp;<label for="pdb_serial">Display-Order (larger=later):</label>';
      echo '<input class="duration" id="pdb_serial" name="pdb_serial" length="10" value="'.$r['serial'].'" />';
    } else {
      echo '<input type="hidden" name="pdb_serial" length="10" value="'.$r['serial'].'" />';
    }
    echo '<br/>';

    echo '<label for="pdb_title">Title:</label><br/>';
    echo '<input id="pdb_title" name="pdb_title" length="80" value="'.$r['title'].'" /><br/>';
    echo '<label for="pdb_day">Day:</label>';
    echo '<select id="pdb_day" name="pdb_day" size="1">';
    gen_options($a_days , $r['day']);
    echo '</select>&nbsp;';
    echo '<label for="pdb_time">Time:</label>';
    echo '<select id="pdb_time" name="pdb_time" size="1">';
    gen_options($a_times, $r['starttime']);
    echo '</select>&nbsp;';
    echo '<label for="pdb_duration">Duration:</label>';
    if ($r['type'] != 'p') {
      echo '<input class="duration" id="pdb_duration" name="pdb_duration" length="10" value="'.$r['duration'].'" />&nbsp;';
    } else {
      echo '<select id="pdb_duration" name="pdb_duration" size="1">';
      gen_options($a_durations , $r['duration']);
      echo '</select>&nbsp;';
    }
    echo '<label for="pdb_location">Location:</label>';
    echo '<select id="pdb_location" name="pdb_location" size="1">';
    gen_options($a_locations , $r['location_id']);
    echo '</select><br/>';
    $i=1;
    if ($id>0)
    foreach (fetch_authorids($db, $r['id']) as $user_id) {
      if ($i%2==1 && $i>1) echo '<br/>'; else if ($i>1) echo '&nbsp;';
      echo '<label for="pdb_author['.$i.']">Author '.$i.':</label>';
      echo '<select id="pdb_author['.$i.']" name="pdb_author['.$i.']" size="1">';
      gen_options($a_users , $user_id);
      echo '</select>'."\n";
      $i++;
    }
    $maxusers=6;
    while ($i+2 > $maxusers) $maxusers+=2;
    while ($i<=$maxusers) {
      if ($i%2 ==1) echo '<br/>'; else echo '&nbsp;';
      echo '<label for="pdb_author['.$i.']">Author '.$i.':</label>';
      echo '<select id="pdb_author['.$i.']" name="pdb_author['.$i.']" size="1">';
      gen_options($a_users , 0);
      echo '</select>'."\n";
      $i++;
    }
    echo '<br/>';

    echo '<label for="pdb_abstract">Abstract:</label><br/>';
    echo '<textarea id="pdb_abstract" name="pdb_abstract" rows="5" cols="70">'.$r['abstract'].'</textarea><br/>';

    echo '<label for="pdb_notes">Notes:</label><br/>';
    echo '<textarea id="pdb_notes" name="pdb_notes" rows="3" cols="70">'.$r['notes'].'</textarea><br/>';

    echo '<label for="pdb_url_paper">Paper URL:</label><br/>';
    echo '<input id="pdb_url_paper" name="pdb_url_paper" length="80" value="'.$r['url_paper'].'" /><br/>';
    echo '<label for="pdb_url_slides">Slides URL:</label><br/>';
    echo '<input id="pdb_url_slides" name="pdb_url_slides" length="80" value="'.$r['url_slides'].'" /><br/>';
    echo '<label for="pdb_url_audio">Audio URL:</label><br/>';
    echo '<input id="pdb_url_audio" name="pdb_url_audio" length="80" value="'.$r['url_audio'].'" /><br/>';
    echo '<label for="pdb_url_misc">Site URL:</label><br/>';
    echo '<input id="pdb_url_misc" name="pdb_url_misc" length="80" value="'.$r['url_misc'].'" /><br/>';
    echo '<label for="pdb_url_image">Image URL:</label><br/>';
    echo '<input id="pdb_url_image" name="pdb_url_image" length="80" value="'.$r['url_image'].'" /><br/>';
    echo '<label for="pdb_url_stream">Stream URL:</label><br/>';
    echo '<input id="pdb_url_stream" name="pdb_url_stream" length="80" value="'.$r['url_stream'].'" /><br/><br/>';

    echo '<input class="button" type="button" title="Save" value="Save" onclick="document.getElementById(\'param\').value='.$r['id'].';document.getElementById(\'mode\').value=\'saveedit\';formsubmit(\'myform\');"/>'."\n";
    echo '<input class="button" type="button" title="Cancel" value="Cancel" onclick="document.getElementById(\'param\').value='.$r['id'].';document.getElementById(\'mode\').value=\'unlockactivity\';formsubmit(\'myform\');"/>'."<br/>&nbsp;\n";
  }

  function dbadmin_dellocation($db) {
    $err=0;
    $id=intval(rawurldecode($_REQUEST['param']));
    if ($id < 0) {
      say_db_message('Invalid Location ID given.');
      return;
    }

    $aids=fetch_activity_by_location($db,$id);
    if (count($aids) >0 ) {
      say_db_message('Location is referenced by '.count($aids).' entries and can not be deleted!');
      return;
    }

    $q='DELETE from location WHERE id='.$id.';';
    $err|=($db->exec($q) !== 1)?1:0;
    say_db_message('Deleted Location-ID='.$id.'.. '.($err==0?'OK':'Error:'.$err));
  }

  function dbadmin_deluser($db) {
    $err=0;
    $id=intval(rawurldecode($_REQUEST['param']));
    if ($id < 0) {
      say_db_message('Invalid User ID given.');
      return;
    }

    $aids=fetch_activity_by_author($db,$id);
    if (count($aids) >0 ) {
      say_db_message('User is referenced by '.count($aids).' entries and can not be deleted!');
      return;
    }

    $q='DELETE from user WHERE id='.$id.';';
    $err|=($db->exec($q) !== 1)?1:0;
    say_db_message('Deleted User-ID='.$id.'.. '.($err==0?'OK':'Error:'.$err));
  }

  function dbadmin_delentry($db) {
    $err=0;
    $id=intval(rawurldecode($_REQUEST['param']));
    if ($id < 0) {
      say_db_message('Invalid Enrty ID given.');
      return;
    }

    $aids=fetch_authorids($db,$id);
    if (count($aids) >0 ) {
      say_db_message('This entry references '.count($aids).' Authors. Please first unlink them.!');
      return;
    }

    $q='DELETE from activity WHERE id='.$id.';';
    $err|=($db->exec($q) !== 1)?1:0;
    say_db_message('Deleted Entry-ID='.$id.'.. '.($err==0?'OK':'Error:'.$err));
  }

  function dbadmin_savelocation($db) {
    $err=0;
    $id=intval(rawurldecode($_REQUEST['param']));
    if ($id < 0) {
      $q='INSERT into location (name) VALUES ('
  .' '.$db->quote(rawurldecode($_REQUEST['pdb_name']))
  .');';
    } else {
      unlock($db, $id, 'location');
      $q='UPDATE location set '
  .' name='.$db->quote(rawurldecode($_REQUEST['pdb_name']))
  .' WHERE id='.$id.';';
    }
    #print_r($q);
    $err|=($db->exec($q) !== 1)?1:0;
    if ($id<0)
      $id=$db->lastInsertId();
    echo '<div class="dbmsg">Saving Location-ID='.$id.'.. '.($err==0?'OK':'Error:'.$err).'</div>'."\n";
  }

  function dbadmin_saveuser($db) {
    $err=0;
    $id=intval(rawurldecode($_REQUEST['param']));
    # TODO parse vip, flags !!
    $vip=0; $flags=0;
    $vip|=isset($_REQUEST['pdb_vip_s'])?1:0;
    $vip|=isset($_REQUEST['pdb_vip_o'])?2:0;
    $vip|=isset($_REQUEST['pdb_vip_c'])?4:0;
    $vip|=isset($_REQUEST['pdb_vip_a'])?8:0;

    $flags|=isset($_REQUEST['pdb_flag_pub'])?1:0;

    if ($id < 0) {
      $q='INSERT into user (name, email, bio, vip, flags, tagline, url_image, url_person, url_institute, url_project) VALUES ('
  .' '.$db->quote(rawurldecode($_REQUEST['pdb_name']))
  .','.$db->quote(rawurldecode($_REQUEST['pdb_email']))
  .','.$db->quote(rawurldecode($_REQUEST['pdb_bio']))
  .','.intval    ($vip)
  .','.intval    ($flags)
  .','.$db->quote(rawurldecode($_REQUEST['pdb_tagline']))
  .','.$db->quote(rawurldecode($_REQUEST['pdb_url_image']))
  .','.$db->quote(rawurldecode($_REQUEST['pdb_url_person']))
  .','.$db->quote(rawurldecode($_REQUEST['pdb_url_institute']))
  .','.$db->quote(rawurldecode($_REQUEST['pdb_url_project']))
 .');';
    } else {
      unlock($db, $id, 'user');
      $q='UPDATE user set '
  .' name='.$db->quote(rawurldecode($_REQUEST['pdb_name']))
  .',email='.$db->quote(rawurldecode($_REQUEST['pdb_email']))
  .',bio='.$db->quote(rawurldecode($_REQUEST['pdb_bio']))
  .',vip='.intval($vip)
  .',flags=(flags&~1)|('.intval($flags).')'
  .',tagline='.$db->quote(rawurldecode($_REQUEST['pdb_tagline']))
  .',url_image='.$db->quote(rawurldecode($_REQUEST['pdb_url_image']))
  .',url_person='.$db->quote(rawurldecode($_REQUEST['pdb_url_person']))
  .',url_institute='.$db->quote(rawurldecode($_REQUEST['pdb_url_institute']))
  .',url_project='.$db->quote(rawurldecode($_REQUEST['pdb_url_project']))
  .' WHERE id='.$id.';';
    }
    #print_r($q);
    $err|=($db->exec($q) !== 1)?1:0;
    if ($id<0)
      $id=$db->lastInsertId();
    echo '<div class="dbmsg">Saving User-ID='.$id.'.. '.($err==0?'OK':'Error:'.$err).'</div>'."\n";
  }

  function dbadmin_saveedit($db) {
    $err=0;
    $id=intval(rawurldecode($_REQUEST['param']));
    if ($id < 0) {
      $q='INSERT INTO activity (title, abstract, notes, duration, starttime, location_id, day, type, url_stream, url_paper, url_slides, url_audio, url_misc, url_image, status, serial) VALUES ('
  .' '.$db->quote(rawurldecode($_REQUEST['pdb_title']))
  .','.$db->quote(rawurldecode($_REQUEST['pdb_abstract']))
  .','.$db->quote(rawurldecode($_REQUEST['pdb_notes']))
  .','.$db->quote(rawurldecode($_REQUEST['pdb_duration']))
  .','.$db->quote(rawurldecode($_REQUEST['pdb_time']))
  .','.$db->quote(rawurldecode($_REQUEST['pdb_location']))
  .','.$db->quote(rawurldecode($_REQUEST['pdb_day']))
  .','.$db->quote(rawurldecode($_REQUEST['pdb_type']))
  .','.$db->quote(rawurldecode($_REQUEST['pdb_url_stream']))
  .','.$db->quote(rawurldecode($_REQUEST['pdb_url_paper']))
  .','.$db->quote(rawurldecode($_REQUEST['pdb_url_slides']))
  .','.$db->quote(rawurldecode($_REQUEST['pdb_url_audio']))
  .','.$db->quote(rawurldecode($_REQUEST['pdb_url_misc']))
  .','.$db->quote(rawurldecode($_REQUEST['pdb_url_image']))
  .','.$db->quote(rawurldecode($_REQUEST['pdb_status']))
  .','.$db->quote(rawurldecode($_REQUEST['pdb_serial']))
  .');';
    } else {
      unlock($db, $id, 'activity');
      $q='UPDATE activity set'
  .' title='.$db->quote(rawurldecode($_REQUEST['pdb_title']))
  .',abstract='.$db->quote(rawurldecode($_REQUEST['pdb_abstract']))
  .',notes='.$db->quote(rawurldecode($_REQUEST['pdb_notes']))
  .',duration='.$db->quote(rawurldecode($_REQUEST['pdb_duration']))
  .',starttime='.$db->quote(rawurldecode($_REQUEST['pdb_time']))
  .',location_id='.$db->quote(rawurldecode($_REQUEST['pdb_location']))
  .',day='.$db->quote(rawurldecode($_REQUEST['pdb_day']))
  .',type='.$db->quote(rawurldecode($_REQUEST['pdb_type']))
  .',url_stream='.$db->quote(rawurldecode($_REQUEST['pdb_url_stream']))
  .',url_paper='.$db->quote(rawurldecode($_REQUEST['pdb_url_paper']))
  .',url_slides='.$db->quote(rawurldecode($_REQUEST['pdb_url_slides']))
  .',url_audio='.$db->quote(rawurldecode($_REQUEST['pdb_url_audio']))
  .',url_misc='.$db->quote(rawurldecode($_REQUEST['pdb_url_misc']))
  .',url_image='.$db->quote(rawurldecode($_REQUEST['pdb_url_image']))
  .',status='.$db->quote(rawurldecode($_REQUEST['pdb_status']))
  .',serial='.$db->quote(rawurldecode($_REQUEST['pdb_serial']))
  .' WHERE id='.$id.';';
    }
    #print_r($q);
    $err|=($db->exec($q) !== 1)?1:0;

    if ($id<0)
      $id=$db->lastInsertId();

    $q='DELETE from usermap where activity_id ='.$id.';';
    $err|=($db->exec($q) >=0)?0:2;

    $aid=0;
    foreach ($_REQUEST['pdb_author'] as $author) {
      if ($author == 0) continue;
      $q='INSERT into usermap (\'activity_id\', \'user_id\', \'position\') VALUES ('
          .$id.','
          .intval(rawurldecode($author)).', '.$aid++.');';
      $err|=($db->exec($q) !== 1)?4:0;
    }

    echo '<div class="dbmsg">Saving ID='.$id.'.. '.($err==0?'OK':'Error:'.$err).'</div>'."\n";
  }

  function count_out($db, $q) {
    $res=$db->query($q);
    if (!$res) return 0; // TODO: print error msg

    $result=$res->fetchAll();
    $count = $result[0];

    return $count[0];
  }
  function query_out($db, $q, $details=true, $type=true, $location=true, $day=false, $print=false) {
    $a_users = fetch_selectlist($db);
    $a_locations = fetch_selectlist($db, 'location');
    if ($day)
      $a_days = fetch_selectlist(0, 'days');

    $res=$db->query($q);
    if (!$res) return; // TODO: print error msg

    $result=$res->fetchAll();

    if (count($result) ==0 ) {
      echo '<div class="center red">No matching entries found!</div>';
    }

    $curday = -10;
    $curtme = -10;
    foreach ($result as $r) {
      if (substr($r['title'],0,7) == 'COFFEE ') continue; # XXX
      if (substr($r['title'],0,6) == 'LUNCH ') continue; # XXX
      if ($r['status'] == 2) continue; # hide hidden entries

      if ($curday == -10) $curday = $r['day'];
      else if ($curday != $r['day']) {
        $curday = $r['day'];
        $curtme = -10;
        echo '<div class="spacerDay"></div>'."\n";
      }
      if ($r['type'] == 'c') {
        if ($curtme == -10 ) $curtme = $r['starttime'];
        else if ($curtme != $r['starttime']) {
          $curtme = -10;
          echo '<div class="spacerTime"></div>'."\n";
        }
      }

      if ($day) {
        if ($r['day'] == 5) { ## every day
          if (!$print) {echo 'every day - all day &nbsp;';}
        } else
          echo 'Day '.$a_days[$r['day']].'&nbsp;';
      }
      echo '<div class="righttr '.track_color($r).'">';
      echo track_name(track_color($r));
      echo '</div>';
      if ($r['day'] != 5) ## every day
        echo '<span class="tme">'.translate_time($r['starttime']).'</span>&nbsp;';
      if ($r['status']==0) echo '<span class="red">Cancelled: </span>';
      echo '<span'.(($r['status']==0)?' class="cancelled"':'').'><b>'.xhtmlify($r['title']).'</b></span>';
      if ($type && $r['type'] != 'i')
        echo ' - <span>'.translate_type($r['type']).'</span>';
      #echo '<br/>';
      echo '<br style="clear:right;"/>';
      #echo '<div style="clear:right;"/></div>';
      #if (!empty($r['url_stream'])) $r['url_image']='img/authors/nando_jason.png'; # XXX
      if (!$print && !empty($r['url_image'])) {
        $thumb=$r['url_image'];
        if (strncmp($thumb,'img/authors/',12) == 0) {
          $thumb='img/authors/small/'.basename($r['url_image']);
        }
        echo '<div class="aimg"><a href="'.$r['url_image'].'"><img src="'.$thumb.'" width="100" alt="author image"/></a></div>';
      }
      global $config;
      if ($config['hidepapers']) $r['url_paper'] = '';
      if (!$print) {
      # TODO: abstraction for multiple links: key ('type/name') => value ('url')
      if (!empty($r['url_audio']) || !empty($r['url_misc']) || !empty($r['url_paper']) || !empty($r['url_slides']) || !empty($r['url_stream']))
        echo '<div class="flright">';
      if (!empty($r['url_paper']))
        echo '<a href="'.$r['url_paper'].'" rel="_blank">Paper (PDF)</a>&nbsp;&nbsp;';
      if (!empty($r['url_slides']))
        echo '<a href="'.$r['url_slides'].'" rel="_blank">Slides</a>&nbsp;&nbsp;';
      if (!empty($r['url_stream'])) {
        if (0 === strpos($r['url_stream'], 'http') && false === strpos($r['url_stream'], 'lac.linuxaudio.org/')) {
          echo '<a href="'.$r['url_stream'].'" rel="_blank">Video</a>&nbsp;&nbsp;';
        } else {
          echo '<a href="video.php?id='.rawurlencode($r['id']).'" rel="_blank">Video</a>&nbsp;&nbsp;';
        }
      }
      if (!empty($r['url_audio']))
        echo '<a href="'.$r['url_audio'].'">Audio</a>&nbsp;&nbsp;';
      if (!empty($r['url_misc']))
        echo '<a href="'.$r['url_misc'].'" rel="external">Site</a>&nbsp;&nbsp;';
      if (!empty($r['url_audio']) || !empty($r['url_misc']) || !empty($r['url_paper']) || !empty($r['url_slides']) || !empty($r['url_stream']))
        echo '</div>';
      }


      if ($r['duration']!=-1) {
        echo '('.$r['duration'];
        if (empty($r['duration'])) echo '??';
        if (!strstr($r['duration'], ':')) echo ' min';
        if ($r['type']=='i') echo ' - loop';
        echo ')';
      }
      echo '&nbsp;&nbsp;<em>'; $i=0;
      foreach (fetch_authorids($db, $r['id']) as $user_id) {
        if ($i++>0) echo ', ';
        if ((usr_has_profile($db, $user_id) & 2) ==0)
          echo xhtmlify($a_users[$user_id]);
        else
          echo '<a href="'.local_url('speakers', 'uid='.$user_id).'" rel="parent">'.xhtmlify($a_users[$user_id]).'</a>';
      }
      echo '</em>';

      if (1 || $r['type']!='c') { ### all concerts same location
        if ($location && !empty($r['location_id']))
          echo ' &raquo;&nbsp;Location: '.$a_locations[$r['location_id']];
      }
      echo '<br/>';

      if ($details)
        echo '<div class="abstract">'.str_replace("\\n",'<br/>', xhtmlify($r['abstract'])).'</div>';
      echo '<hr class="psep"/>';
    }
  }

  function program_fieldset() {
?>
  <fieldset class="pdb">
    <input name="page" type="hidden" value="adminschedule" id="page"/>
    <input name="mode" type="hidden" value="" id="mode"/>
    <input name="sort" type="hidden" value="" id="sort"/>
    <input name="param" type="hidden" value="<?php echo $_REQUEST['param'];?>" id="param"/>
<?php
  }

  function dbadmin_unixtime($e, $start=true) {
    date_default_timezone_set('UTC');
    $time= strtotime((0+intval($e['day'])).' May 2014 '.$e['starttime'].':00 CEST');
    if (!$start && !strstr($e['duration'], ':'))
      $time = strtotime('+'.$e['duration'].'minutes', $time);
    return $time;
  }

  function shortmail($email) {
    return limit_text(preg_replace('/^[^@]*@/', '@', $email),20);
  }

  function dbadmin_profilelist($db) {
    $q='SELECT id,name,email,udate,flags from user ORDER BY name;';
    $res=$db->query($q);
    if (!$res) { say_db_error(); return $rv;}
    $result=$res->fetchAll();
    $cnt=0; $miss=0;
    echo '<table style="font-size:90%;width:100%;line-height:1.1em;">';
    foreach ($result as $r) {
      echo '<tr>';
      $notified = (usr_has_profile($db, $r['id']) & 1)?true:false;
      if (!$notified) $miss++;
      echo '<td'.($notified?'':' class="red"').'>'.$r['name'];
      echo '</td><td>'.shortmail($r['email']).'</td><td>';
      echo ($r['flags'] & 1)?'<span>pub':'<span class="red">priv';
      echo '</td><td>'.$r['udate'].'</td><td>';
      if (!empty($r['email']))
        echo '<a class="active" onclick="document.getElementById(\'param\').value='.$r['id'].';document.getElementById(\'mode\').value=\'profileinfo\';formsubmit(\'myform\');">'.($notified?'Re-':'').'Notify</a> ';
      echo "</td></tr>\n";
    }
    echo '</table>';
    if ($miss == 0) echo '<div>All users have already been notified.</div>';
  }

  function dbadmin_profilecheck($db, $notify=false) {
    $q='SELECT id,name,email from user ORDER BY name;';
    $res=$db->query($q);
    if (!$res) { say_db_error(); return $rv;}
    $result=$res->fetchAll();
    $cnt=0; $eml=0; $dne=0;
    foreach ($result as $r) {
      if (empty($r['email'])) continue;
      if (usr_has_profile($db, $r['id']) & 1) { $dne++; continue; }
      if ($notify) {
        $eml++;
        if (usr_msg_sendhash($db, $r['id'], $r['email']) == 0) { $dne++; continue;}
      }
      $cnt++;
      echo '<a class="active" onclick="document.getElementById(\'param\').value='.$r['id'].';document.getElementById(\'mode\').value=\'profilenotify\';formsubmit(\'myform\');">Notify</a> ';
      echo $r['name'].' - '.$r['email'];
      echo "<br/>\n";
    }
    if ($cnt == 0) echo '<div>All users have already been notified.</div>';
    else echo '<br/><div class="center"><a class="active" onclick="document.getElementById(\'param\').value=-1;document.getElementById(\'mode\').value=\'profilenotify\';formsubmit(\'myform\');">Notify ALL</a></div>';

    if ($eml != 0) echo '<div class="dbmsg">'.$eml.' email invitations sent.</div>';
    if ($dne != 0) echo '<div>'.$dne.' users(s) have already received an invitation.</div>';
  }

  function dbadmin_orphans($db) {
    echo "<b>Pass 1: Scheduled Entries</b><br/>";
    $a_locations = fetch_selectlist($db, 'location');
    $q='SELECT * FROM activity ORDER BY day,strftime(\'%H:%M\',starttime)';
    $res=$db->query($q);
    if ($res) {
      $result=$res->fetchAll();
      foreach ($result as $r) {
        $err=0;
        if (!isset($a_locations[$r['location_id']]) || empty($a_locations[$r['location_id']])) {
          echo 'Event ('.$r['id'].') has no assigned location.<br/>';
          $err++;
        }
        if (count(fetch_authorids($db, $r['id'])) == 0) {
          if (substr($r['title'],0,12) == 'COFFEE BREAK') continue; # XXX
          if (substr($r['title'],0,6) == 'LUNCH ') continue; # XXX
          echo 'Event ('.$r['id'].') has no assigned Author(s).<br/>';
          $err++;
        }

        if ($err) {
    echo ' - ('.$r['id'].') <em>day</em>:'.$r['day'].', <em>start</em>:'.$r['starttime'].', <em>duration</em>:'.$r['duration'].', <em>type</em>:'.translate_type($r['type']).'<br/>&nbsp;&nbsp;&nbsp;<em>title</em>:'.limit_text($r['title']).'&nbsp;|&nbsp;';
          echo '<td><a class="active" onclick="document.getElementById(\'param\').value='.$r['id'].';document.getElementById(\'mode\').value=\'edit\';formsubmit(\'myform\');">Edit</a>';
          if ($err==2)
            echo '&nbsp;|&nbsp;<a class="active" onclick="if (confirm(\'Really delete Entry no. '.$r['id'].'?\')) {document.getElementById(\'param\').value='.$r['id'].';document.getElementById(\'mode\').value=\'delentry\';formsubmit(\'myform\');i}">Delete</a>';
          echo '<br/>';
          echo "\n";
        }
      }

    } else {
      echo '&nbsp;*&nbsp;Database query failed<br/>'."\n";
    }


    echo "<b>Pass 2: Persons</b><br/>";
    $showkey = false;
    $q='SELECT id, name, vip from user ORDER BY name;';
    $res=$db->query($q);
    if ($res) {
      $result=$res->fetchAll();
      foreach ($result as $r) {
        $aids=fetch_activity_by_author($db,$r['id']);
        if (count($aids)!=0) continue;
        echo '('.$r['id'].') "'.$r['name'].'" has no assignment.&nbsp;';
        if ($r['vip']&6)  { echo '&hellip;but is marked as VIP ('.$r['vip'].').'; $showkey=true; }
        else
          echo '<a class="active" onclick="if (confirm(\'Really delete User no. '.$r['id'].'?\')) {document.getElementById(\'param\').value='.$r['id'].';document.getElementById(\'mode\').value=\'deluser\';formsubmit(\'myform\');}">Delete</a>';
        echo '<br/>'."\n";
      }
    } else {
      echo '&nbsp;*&nbsp;Database query failed<br/>'."\n";
    }
    if ($showkey) {
      echo '<fieldset style="width:50%; margin:0 auto;"><legend>VIP bitwise numeric key:</legend>';
      echo '<tt><b>1</b></tt>: Speaker<br/>';
      echo '<tt><b>2</b></tt>: Organizer<br/>';
      echo '<tt><b>4</b></tt>: Committee Member<br/>';
      echo '<tt><b>8</b></tt>: Artist (musician, composer)<br/>';
      echo '</fieldset>'."\n";
    }


    echo "<b>Pass 3: Locations</b><br/>";
    $q='SELECT id, name from location ORDER BY id;';
    $res=$db->query($q);
    if ($res) {
      $result=$res->fetchAll();
      foreach ($result as $r) {
        $aids=fetch_activity_by_location($db,$r['id']);
        if (count($aids)!=0) continue;
        echo '('.$r['id'].') "'.$r['name'].'" has no event assigned to it.&nbsp;';
        echo '<a class="active" onclick="if (confirm(\'Really delete Location no. '.$r['id'].'?\')) {document.getElementById(\'param\').value='.$r['id'].';document.getElementById(\'mode\').value=\'dellocation\';formsubmit(\'myform\');}">Delete</a>';
        echo '<br/>'."\n";
      }
    } else {
      echo '&nbsp;*&nbsp;Database query failed<br/>'."\n";
    }

    echo "<b>Pass 4: Check registrations of Authors</b><br/>";
    echo "Take with a grain of salt. Spelling may slightly differ between here and the registration!<br/>";
    $cnt=array('tot' => 0, 'ok' => '0', 'part' => 0);
    list($regs,$list) = get_registrations();

    $q='SELECT id, name, email, vip from user ORDER BY name;';
    $res=$db->query($q);
    $emailmissing='';
    if ($res) {
      echo '<ul>'."\n";
      $result=$res->fetchAll();
      foreach ($result as $r) {
        $cnt['tot']++;
        if (in_array(strtolower($r['name']), $list)) {
          $cnt['ok']++;
          continue;
        }
        echo '<li>'.$r['name'].' is not yet registered';
        if (empty($r['email']))
          echo '<span style="color:red"> and we have no email address</span>';
        else
          $emailmissing.=$r['name'].' &lt;'.$r['email'].'&gt;, ';

        if (!($r['vip']&1))
          echo '; but s/he is no speaker';
        else
          echo '<span style="color:red">(!)</span>';
        echo '.';
        $pm=0;
        foreach (explode(' ',$r['name']) as $np) {
          if (strlen($np) <3) continue;
          if ($np== 'van') continue;
          foreach ($list as $n) {
            if (stristr($n, $np)) {
              echo '<br/>&nbsp;<span style="color:#aaaa00">Maybe: '.$n.'</span>?';
              $pm=1;
            }
          }
        }
        if ($pm) $cnt['part']++;
    echo '</li>';
      }
      echo '</ul>'."\n";
    }
    echo '<p style="color:red">'.$cnt['ok'].' of '.$cnt['tot'].' Authors have registered.</p>';
    echo '<p style="color:#aaaa00">'.$cnt['part'].' partial matche(s).</p>';

    echo '<hr/>'."\n";
    echo '<p>Email of unregistered Authors:</p>'."\n";
    echo '<pre style="font-size:9px; background:#ccc; line-height:1.3em;margin-top:2em;">';
    echo wordwrap($emailmissing,100);
    echo '</pre><br/>';

    echo "<b>Pass 5: Check tagging of Author Registration</b><br/>";
    $checked=0;
    $good=0;
    if ($res) {
      foreach ($result as $r) {
        foreach ($regs as $n) {
          if (strtolower(trim($r['name'])) != strtolower($n['fullname'])) continue;
          $checked++;
          if (empty($n['reg_vip'])) {
            echo 'Author: '.$r['name'].' is not yet marked as VIP in registration.<br/>';
          } else {
            $good++;
          }
        }
      }
    }
    echo '<p style="color:red">Checked '.$checked.' of '.$cnt['tot'].' Authors: good entries: '.$good.'</p>';

    echo "<b>Pass 6: Check profile of Author Registration</b><br/>";
    $checked=0;
    $good=0;
    if ($res) {
      foreach ($result as $r) {
        foreach ($regs as $n) {
          if (strtolower(trim($r['name'])) != strtolower($n['fullname'])) continue;
          $checked++;
          $ok=0;
          if (($r['vip'] & 1) && $n['reg_vip'] == 'author') $ok|=1;
          if (($r['vip'] & 8) && $n['reg_vip'] == 'author') $ok|=8;
          if (($r['vip'] & 2) && $n['reg_vip'] == 'organizer') $ok |=2;
          if (($r['vip'] & 4) ) $ok |=4;
          if (!($r['vip'] & 9) && $n['reg_vip'] == 'author') $ok=0;
          if (!($r['vip'] & 2) && $n['reg_vip'] == 'organizer') $ok=0;
          if ($ok==0) {
            echo 'Author: '.$r['name'].'('.$r['id'].') reg. and profile are inconsistent: '.$r['vip'].' vs "'.$n['reg_vip'].'"<br/>';
          } else {
            $good++;
          }
        }
      }
    }
    echo '<p style="color:red">Checked '.$checked.' of '.$cnt['tot'].' Authors: good entries: '.$good.'</p>';
  }

  function get_registrations() {
    $dir = opendir(REGLOGDIR);
    $rva = array();
    $rvn = array();
    while ($file_name = readdir($dir))
      if($file_name[0] != '.' && is_file(REGLOGDIR.$file_name)) {
        $v=parse_ini_file(REGLOGDIR.$file_name);
        $rva[]= array('first' => $v['reg_prename'], 'last' => $v['reg_name'], 'fullname' => trim($v['reg_prename']).' '.trim($v['reg_name']), 'reg_vip' => (isset($v['reg_vip'])?$v['reg_vip']:''));
        $rvn[]= strtolower(trim($v['reg_prename']).' '.trim($v['reg_name']));
      }
    return array($rva, $rvn);
  }

  function dbadmin_checkconflicts($db) {
    echo "<b>Pass 1: List Incomplete Entries</b><br/>";
    $a_locations = fetch_selectlist($db, 'location');
    $q='SELECT * FROM activity ORDER BY day,strftime(\'%H:%M\',starttime)';
    $res=$db->query($q);
    $grrr=0;
    if ($res) {
      $result=$res->fetchAll();
      foreach ($result as $r) {
  $err=0;
        if (empty($r['day']) || $r['day'] < 1) {
          echo 'Event ('.$r['id'].') has no day set.<br/>';
    $err++;
        }
        if (empty($r['starttime']) && $r['type'] != 'i' && $r['location_id'] != 2) { # XXX ignore radio shows and installations
          echo 'Event ('.$r['id'].') has no start-time set.<br/>';
    $err++;
        }
        if (empty($r['duration']) && $r['type'] != 'c') {
          echo 'Event ('.$r['id'].') has no duration set.<br/>';
    $err++;
        }
        if ($err) {
          $grrr++;
    echo ' - ('.$r['id'].') <em>day</em>:'.$r['day'].', <em>start</em>:'.$r['starttime'].', <em>duration</em>:'.$r['duration'].', <em>type</em>:'.translate_type($r['type']).'<br/>&nbsp;&nbsp;&nbsp;<em>title</em>:'.limit_text($r['title']).'&nbsp;|&nbsp;';
          echo '<td><a class="active" onclick="document.getElementById(\'param\').value='.$r['id'].';document.getElementById(\'mode\').value=\'edit\';formsubmit(\'myform\');">Edit</a>';
          echo '<br/>'."\n";
        }
      }
    }
    if ($grrr==0) echo '&nbsp;*&nbsp;<span class="green">All OK.</span><br/>'."\n";
    else echo '&nbsp;*&nbsp;<span class="red"><b>'.$grrr.' incomplete entries found.</b></span><br/>'."\n";

    echo "<b>Pass 2: Locations Date/Time (Concerts and Posters are ignored)</b><br/>";
    $err=0;

    $q='SELECT * FROM activity ORDER BY day,strftime(\'%H:%M\',starttime)';
    $res=$db->query($q);
    if ($res) {
      $result=$res->fetchAll();
      foreach ($result as $a) {
        foreach ($result as $b) {
          if ($a['id'] == $b['id']) continue;
          if ($a['day'] != $b['day']) continue;
          if ($a['type'] == 'v' || $b['type']=='v') continue;
          if ($a['type'] == 'c' || $b['type']=='c') continue;
          if ($a['type'] == 'i' || $b['type']=='i') continue;
          if ($a['location_id'] != $b['location_id']) continue;
          if ($a['status'] == 0  || $b['status'] == 0) continue;
          $starta = dbadmin_unixtime($a);
          $enda   = dbadmin_unixtime($a, false);
          $startb = dbadmin_unixtime($b);
          if ( ($enda > $startb && $starta < $startb)
             ||($starta == $startb) ) {
            $err++;
            echo '<span class="red">Conflict: ('.$a['id'].') ends in same location AFTER ('.$b['id'].') starts there.</span><br/>';
            echo ' - ('.$a['id'].') <em>day</em>:'.$a['day'].', <em>start</em>:'.$a['starttime'].', <em>duration</em>:'.$a['duration'].', <em>type</em>:'.translate_type($a['type']).'<br/>&nbsp;&nbsp;&nbsp;<em>title</em>:'.limit_text($a['title']).'&nbsp;|&nbsp;';
      echo '<td><a class="active" onclick="document.getElementById(\'param\').value='.$a['id'].';document.getElementById(\'mode\').value=\'edit\';formsubmit(\'myform\');">Edit</a><br/>';
            echo ' - ('.$b['id'].') <em>day</em>:'.$b['day'].', <em>start</em>:'.$b['starttime'].', <em>duration</em>:'.$b['duration'].', <em>type</em>:'.translate_type($b['type']).'<br/>&nbsp;&nbsp;&nbsp;<em>title</em>:'.limit_text($b['title']).'&nbsp;|&nbsp;';
      echo '<td><a class="active" onclick="document.getElementById(\'param\').value='.$b['id'].';document.getElementById(\'mode\').value=\'edit\';formsubmit(\'myform\');">Edit</a><br/>';
            echo "\n";
          }
        }
      }
    } else {
      echo '&nbsp;*&nbsp;Database query failed<br/>'."\n";
    }
    if ($err==0) echo '&nbsp;*&nbsp;<span class="green">No conflicts found.</span><br/>'."\n";
    else echo '&nbsp;*&nbsp;<span class="red"><b>'.$err.' conflict(s) found.</b></span><br/>'."\n";

    $err=0;
    echo "<b>Pass 3: Authors Date/Time</b><br/>";
    $q='SELECT id,name from user';
    $res=$db->query($q);
    if ($res) {
      $result=$res->fetchAll();
      foreach ($result as $u) {
        $aids=fetch_activity_by_author($db,$u['id']);
        if (count($aids)<2) continue;
        foreach ($aids as $a) {
          foreach ($aids as $b) {
            if ($a['id'] == $b['id']) continue;
            if ($a['day'] != $b['day']) continue;
            $starta = dbadmin_unixtime($a);
            $enda   = dbadmin_unixtime($a, false);
            $startb = dbadmin_unixtime($b);
            if ($starta <= $startb &&
              /*note: concerts w/duration -1 are OK, -- maybe todo: check serial..*/
               ($enda > $startb || ($enda > $starta && $enda >= $startb))
            ) {
              $err++;;
              echo '<span class="red">Conflict: '.$u['name'].' has overlapping presentations on day '.$a['day'].'!</span><br/>';
              echo ' - ('.$a['id'].') <em>day</em>:'.$a['day'].', <em>start</em>:'.$a['starttime'].', <em>duration</em>:'.$a['duration'].', <em>type</em>:'.translate_type($a['type']).'<br/>&nbsp;&nbsp;&nbsp;<em>title</em>:'.limit_text($a['title']).'&nbsp;|&nbsp;';
              echo '<td><a class="active" onclick="document.getElementById(\'param\').value='.$a['id'].';document.getElementById(\'mode\').value=\'edit\';formsubmit(\'myform\');">Edit</a><br/>';
              echo ' - ('.$b['id'].') <em>day</em>:'.$b['day'].', <em>start</em>:'.$b['starttime'].', <em>duration</em>:'.$b['duration'].', <em>type</em>:'.translate_type($b['type']).'<br/>&nbsp;&nbsp;&nbsp;<em>title</em>:'.limit_text($b['title']).'&nbsp;|&nbsp;';
              echo '<td><a class="active" onclick="document.getElementById(\'param\').value='.$b['id'].';document.getElementById(\'mode\').value=\'edit\';formsubmit(\'myform\');">Edit</a><br/>';
            } else if ($a['id'] < $b['id']) {
              echo '<span class="yellow">Notice: '.$u['name'].' has more than one presentation on day '.$a['day'].'.</span><br/>';
              echo ' - ('.$a['id'].') <em>day</em>:'.$a['day'].', <em>start</em>:'.$a['starttime'].', <em>duration</em>:'.$a['duration'].', <em>type</em>:'.translate_type($a['type']).'<br/>&nbsp;&nbsp;&nbsp;<em>title</em>:'.limit_text($a['title']).'&nbsp;|&nbsp;';
              echo '<td><a class="active" onclick="document.getElementById(\'param\').value='.$a['id'].';document.getElementById(\'mode\').value=\'edit\';formsubmit(\'myform\');">Edit</a><br/>';
              echo ' - ('.$b['id'].') <em>day</em>:'.$b['day'].', <em>start</em>:'.$b['starttime'].', <em>duration</em>:'.$b['duration'].', <em>type</em>:'.translate_type($b['type']).'<br/>&nbsp;&nbsp;&nbsp;<em>title</em>:'.limit_text($b['title']).'&nbsp;|&nbsp;';
              echo '<td><a class="active" onclick="document.getElementById(\'param\').value='.$b['id'].';document.getElementById(\'mode\').value=\'edit\';formsubmit(\'myform\');">Edit</a><br/>';
            }
          }
        }
      }
    } else {
      echo '&nbsp;*&nbsp;Database query failed<br/>'."\n";
    }
    if ($err==0) echo '&nbsp;*&nbsp;<span class="green">No conflicts found.</span><br/>'."\n";
    else echo '&nbsp;*&nbsp;<span class="red"><b>'.$err.' conflict(s) found.</b></span><br/>'."\n";
  }

  function print_day($db, $num, $name, $details=true) {
    echo '<h2 class="ptitle">Day '.$num.' - '.$name.'</h2>';
    if (count_out($db,
          'SELECT count(*) FROM activity
           WHERE day='.$num.'
           AND ( type=\'p\' OR location_id=\'1\')') > 0) {

             if ($num != 4)
               echo '<h3 class="ptitle">Main Track (ZKM_Media Theater)</h3>';
        query_out($db,
         'SELECT * FROM activity
          WHERE day='.$num.'
          AND ( type=\'p\' OR location_id=\'1\')
          ORDER BY strftime(\'%H:%M\',starttime), serial', $details, false, false
        );
    }

    if (count_out($db,
          'SELECT count(*) FROM activity
           WHERE day='.$num.'
           AND type=\'v\'') > 0) {
        echo '<h3 class="ptitle">Poster Presentations</h3>';
        echo '<div class="ptitle"></div>';
        query_out($db,
         'SELECT * FROM activity
          WHERE day='.$num.'
          AND type=\'v\'
          ORDER BY typesort(type), strftime(\'%H:%M\',starttime), location_id, serial', $details, true, true
        );
    }

    if (count_out($db,
          'SELECT count(*) FROM activity
           WHERE day='.$num.'
           AND NOT (location_id=\'1\' OR location_id=\'2\')') > 0) {
        echo '<h3 class="ptitle">Workshops &amp; Events</h3>';
        echo '<div class="ptitle"></div>';
        query_out($db,
         'SELECT * FROM activity
          WHERE day='.$num.'
          AND NOT (location_id=\'1\'  OR location_id=\'2\')
          ORDER BY typesort(type), strftime(\'%H:%M\',starttime), location_id, serial', $details, true, true
        );
    }
  }

  function print_daily_events($db, $num, $name, $details=true) {
?>
<h2 class="ptitle">Daily Events / Exhibitions</h2>
<div class="ptitle"></div>
<p>
Art installations are exhibited at the media art space on the ZKM_Music Balcony.
</p>
<p>
The exhibition is open from 14:00 to 18:00 every day of the conference.
</p>
<?php
    query_out($db,
     'SELECT * FROM activity
      WHERE day = 5
      AND NOT (type=\'p\' OR location_id=\'1\')
      ORDER BY typesort(type), strftime(\'%H:%M\',starttime), location_id, serial', $details, true, true
    );
  }

  function print_filterfields($a_users, $a_locations, $filter, $usejs=false) {
    $a_days = fetch_selectlist(0, 'days');
    $a_types = fetch_selectlist(0, 'types');
    if ($usejs)
      $ocs=' onchange="submit();" class="small"';
    else
      $ocs=' class="small"';

    if ($filter['day']==0 && $filter['type']=='0' && $filter['user']==0)
      echo '<legend>Filter:</legend>';
    else
      echo '<legend style="color:red">Filter:</legend>';
    echo '<div style="margin:0 auto; text-align:center;">';
    echo '<label for="pdb_filterday">Day:</label>';
    echo '<select id="pdb_filterday" name="pdb_filterday" size="1"'.$ocs.'>';
    gen_options(array_merge(array('0' => '-all-'), $a_days) , $filter['day']);
    echo '</select>&nbsp;'."\n";

    echo '<label for="pdb_filtertype">Type:</label>';
    echo '<select id="pdb_filtertype" name="pdb_filtertype" size="1"'.$ocs.'>';
    gen_options(array_merge(array('0' => '-all-'), $a_types), $filter['type']);
    echo '</select>&nbsp;'."\n";

if (0) {
    echo '<label for="pdb_filterlocation">Location:</label>';
    echo '<select id="pdb_filterlocation" name="pdb_filterlocation" size="1"'.$ocs.'>';
    gen_options($a_locations, $filter['location']);
    echo '</select>&nbsp;'."\n";
    #echo '<input class="smbutton small" type="submit" title="Apply" value="Apply"/>&nbsp;';
}
if (1) {
    echo '<label for="pdb_filterauthor">Author:</label>';
    echo '<select id="pdb_filterauthor" name="pdb_filterauthor" size="1"'.$ocs.'>';
    gen_options($a_users , $filter['user']);
    echo '</select>&nbsp;'."\n";
}
    if ($usejs) {
      echo '<input class="smbutton small" type="submit" title="Apply" value="Apply" />';
      if (!($filter['day']==0 && $filter['type']=='0' && $filter['user']==0))
        echo '&nbsp;<input class="smbutton small" type="button" title="Clear" value="Clear" onclick="document.getElementById(\'pdb_filterday\').value=0;document.getElementById(\'pdb_filtertype\').value=0;document.getElementById(\'pdb_filterauthor\').value=0;formsubmit(\'myform\');"/>';
    } else {
      echo '<input class="smbutton small" type="button" title="Filter" value="Filter" onclick="document.getElementById(\'param\').value=-1;document.getElementById(\'mode\').value=\'\';formsubmit(\'myform\');"/>';
      if (!($filter['day']==0 && $filter['type']=='0' && $filter['user']==0))
        echo '&nbsp;<input class="smbutton small" type="button" title="Clear" value="Clear" onclick="document.getElementById(\'pdb_filterday\').value=0;document.getElementById(\'pdb_filtertype\').value=0;document.getElementById(\'pdb_filterauthor\').value=0;formsubmit(\'myform\');"/>';
    }
    echo '</div>';
  }

  function print_filter($db) {
    $a_users = fetch_selectlist($db, 'user', 'ORDER BY name');
    $a_days = fetch_selectlist(0, 'days');
    $a_types = fetch_selectlist(0, 'types');
    $a_locations = fetch_selectlist($db, 'location');

    $a_locations[0]='-all-';
    $a_users[0]='-all-';
/*
    foreach($a_users as $i => &$a) {
      $a=limit_text($a,19);
    }
*/

    $filter=array('user' => '0', 'day' => '0', 'type' => '0', 'location' => '0', 'id' => '0');

    if (1) { // filter enable
      if (isset($_REQUEST['pdb_filterday'])) $filter['day'] = intval(rawurldecode($_REQUEST['pdb_filterday']));
      if (isset($_REQUEST['pdb_filtertype'])) $filter['type'] = substr(rawurldecode($_REQUEST['pdb_filtertype']),0,1);
      if (isset($_REQUEST['pdb_filterauthor'])) $filter['user'] = intval(rawurldecode($_REQUEST['pdb_filterauthor']));
      if (isset($_REQUEST['pdb_filterlocation'])) $filter['location'] = intval(rawurldecode($_REQUEST['pdb_filterlocation']));
      if (isset($_REQUEST['pdb_filterid'])) $filter['id'] = intval(rawurldecode($_REQUEST['pdb_filterid']));
    }

    echo '<form action="index.php" method="post" id="myform">';
    echo '<fieldset class="pdb">';
    echo '<input name="page" type="hidden" value="program"/>';
    if (isset($_REQUEST['mode']))
      echo '<input name="mode" type="hidden" value="'.$_REQUEST['mode'].'"/>';
    else
      echo '<input name="mode" type="hidden" value=""/>';
    if (isset($_REQUEST['details']))
      echo '<input name="details" type="hidden" value="'.$_REQUEST['details'].'"/>';

    if (isset($_REQUEST['pdb_filterid'])) {
      echo '<input class="small" type="submit" title="Show all entries" value="Show All Entries"/>';
    } else
      print_filterfields($a_users, $a_locations, $filter, true);
    echo '</fieldset>';
    echo '</form>';
    echo '<div style="margin-bottom:1em;">&nbsp;</div>';

    if ($filter['user'] != '0' || $filter['location'] != '0' || $filter['type'] != '0' || $filter['day'] != '0' || $filter['id'] != '0') return $filter;
    return 0;
  }

  function list_filtered_program($db,$filter,$details) {
    if ($filter['user'] != '0')
      $q = 'SELECT DISTINCT activity.*
      FROM activity,user,usermap
      WHERE activity.id=usermap.activity_id AND user.id=usermap.user_id
        AND user.id='.$filter['user'];
    else
      $q='SELECT activity.* FROM activity WHERE 1=1';

    if ($filter['type'] != '0') $q.=' AND type='.$db->quote($filter['type']);
    if ($filter['day'] > 0) $q.=' AND day='.$db->quote($filter['day']);
    if ($filter['location'] > 0) $q.=' AND location_id='.$db->quote($filter['location']);
    if ($filter['id'] > 0) $q.=' AND id='.$db->quote($filter['id']);

    $order='';
    if ($order=='type')
      $q.=' ORDER BY day, typesort(type), strftime(\'%H:%M\',starttime), location_id, serial;';
    else
      $q.=' ORDER BY day, strftime(\'%H:%M\',starttime), typesort(type), location_id, serial;';
    query_out($db, $q, $details, $filter['type'] == '0',  $filter['location'] == '0', true);
  }

  function hardcoded_disclaimer() {
    echo '<div class="disclaimer center">The schedule is a major guideline. There is no guarantee events will take place at the announced timeslot.</div>';
  }

  function hardcoded_concert_and_installation_info($db, $details=true) {
?>
<h2 class="ptitle pb">Concerts &amp; Installations</h2>
<p></p>
<h3>Concerts</h3>
<p>
At LAC'14, there are three electro-acoustic concerts a &laquo;listening session&raquo; and a club-night.
The electro-acoustic concerts take place in the ZKM Kubus, the listening session and club-night on the Balcony in the ZKM.
</p>
<p>
All concert venues are in the ZKM building complex.
</p>
<ul>
<li>The opening concert features novel electro-acoustic compositions presented in the Kubus on Thursday 20:00h-21:00h</li>
<li>On Friday there's a special IMA (Institute for Music and Acoustics) concert presenting works dedicated to the 3D Sound system &laquo;Zirkonium&raquo; in the Kubus 20:00h-21:40h</li>
<li>Later on Friday night there's the &laquo;Listening Session&raquo; of predominantly pre-produced material in the upstairs Lounge, 22:00h-24:00h</li>
<li>Saturday brings more electro-acoustic music with emphasis on Improvisation and Surround Sound in the Kubus, 20:00h-21:40h</li>
<li>We'll go down in style with danceable live-music at the &laquo;Linux Sound Night&raquo;, Saturday 22:00h - open-end on the upstairs Balcony.</li>
</ul>
<h3>Concert Line up</h3>
<div style="padding:.5em 1em; 0em 1em">
<?php
    $q='SELECT activity.* FROM activity WHERE type='.$db->quote('c');
    $q.=' AND NOT location_id=8'; ## XXX skip morning line
    $q.=' AND NOT location_id=9'; ## XXX skip radio shows here
    $q.=' ORDER BY day, strftime(\'%H:%M\',starttime), typesort(type), location_id, serial;';
    query_out($db, $q, $details, false,  true, true, false);
?>
<br/>
<h3>Installations</h3>
<p>
Art installations are exhibited at the media art space on the ZKM_Music Balcony.
</p>
<p>
The exhibition is open from 14:00 to 18:00 every day of the conference.
</p>
<br/>
<?php
    $q='SELECT activity.* FROM activity WHERE type='.$db->quote('i');
    $q.=' ORDER BY day, strftime(\'%H:%M\',starttime), typesort(type), location_id, serial;';
    query_out($db, $q, $details, false,  true, true, true);

    echo '</div>'."\n";
  } ## END hardcoded_concert_and_installation_info ##


  function list_program($db,$details) {
    foreach (fetch_selectlist(0, 'daylist') as $day => $date) {
      print_day($db, $day,$date,$details);
    }
    print_daily_events($db, $day,$date,$details);
  }

  function table_program($db, $day, $print=false) {
    $a_days = fetch_selectlist(0, 'days');
    $a_locations = fetch_selectlist($db, 'location');
    $a_users = fetch_selectlist($db);
    if ($day == 2) {
      $a_times = array(
                      '9:00' => '9:00', '9:30' => '9:30'
                    , '10:00' => '10:00' , '10:15' => '10:15', '10:30' => '10:30', '10:45' => '10:45'
                    , '11:00' => '11:00' , '11:15' => '11:15', '11:30' => '11:30'
                    , '11:40' => '11:40' , '11:50' => '11:50'
                    , '12:00' => '12:00' , '12:10' => '12:10' , '12:20' => '12:20'
                    , '12:30' => '12:30'
                    , '13:00' => '13:00' , '13:30' => '13:30'
                    , '14:00' => '14:00' , '14:15' => '14:15', '14:30' => '14:30', '14:45' => '14:45'
                    , '15:00' => '15:00' , '15:15' => '15:15', '15:30' => '15:30', '15:45' => '15:45'
                    , '16:00' => '16:00' , '16:15' => '16:15', '16:30' => '16:30', '16:45' => '16:45'
                    , '17:00' => '17:00' , '17:15' => '17:15', '17:30' => '17:30', '17:45' => '17:45'
                    , '18:00' => '18:00'
                  );
    } else {
      $a_times = array(
                      '9:00' => '9:00', '9:30' => '9:30'
                    , '10:00' => '10:00' , '10:15' => '10:15', '10:30' => '10:30', '10:45' => '10:45'
                    , '11:00' => '11:00' , '11:15' => '11:15', '11:30' => '11:30', '11:45' => '11:45'
                    , '12:00' => '12:00' , '12:15' => '12:15' , '12:30' => '12:30'
                    , '13:00' => '13:00' , '13:30' => '13:30'
                    , '14:00' => '14:00' , '14:15' => '14:15', '14:30' => '14:30', '14:45' => '14:45'
                    , '15:00' => '15:00' , '15:15' => '15:15', '15:30' => '15:30', '15:45' => '15:45'
                    , '16:00' => '16:00' , '16:15' => '16:15', '16:30' => '16:30', '16:45' => '16:45'
                    , '17:00' => '17:00' , '17:15' => '17:15', '17:30' => '17:30', '17:45' => '17:45'
                    , '18:00' => '18:00'
                  );
    }
    # XXX 2011: Friday
    #if ($day!=1) $a_times[]='18:00';

    if (!$print) {
      echo '<div style="float:right;">';
      for ($i=1; $i<=4; $i++) {
        if ($i == $day) { echo 'Day '.$i.'&nbsp;&nbsp;'; continue;}
        echo '<a href="'.local_url('program', 'mode=table&amp;day='.$i).'">Day '.$i.'</a>&nbsp;&nbsp;';
      }
      echo '<a href="'.local_url('program', 'mode=table&amp;day=0').'">Concerts&amp;Installations</a>&nbsp;&nbsp;';
      echo '</div>';
    }

    echo '<h2 class="ptitle'.(($print && $day>1)?' pb':'').'">Day '.$a_days[$day].'</h2>';
    $q='SELECT DISTINCT location_id FROM activity WHERE day='.$day.'
        AND (type=\'p\' OR type=\'l\' OR type=\'o\' OR type=\'w\' OR location_id=\'1\')
        ORDER BY location_id;';

    $res=$db->query($q);
    if (!$res) return; // TODO: print error msg
    $table=array();$i=0;
    $result=$res->fetchAll();
    foreach ($result as $c) {
      $table[$i]['loc']=$a_locations[$c['location_id']];
      $table[$i]['cskip']=0;
      $q='SELECT * FROM activity WHERE day='.$day.'
          AND (type=\'p\' OR type=\'l\' OR type=\'o\' OR type=\'w\')
          AND location_id='.$c['location_id'].'
          ORDER BY strftime(\'%H:%M\',starttime);';
      $res=$db->query($q);
      if (!$res) return; // TODO: print error msg
      $stmt=$res->fetchAll();
      foreach ($stmt as $r) {
        if (empty($r['starttime'])) continue;
        if ($r['status']!=1) continue; # skip cancelled and hidden
        $table[$i][$r['starttime']]=$r;
      }
      $i++;
    }
    $numloc=$i;

    echo '<table cellspacing="0" class="ptb"><tr><th class="ptb">Time</th>';
    foreach ($table as $c) {
      echo '<th class="ptb">'.$c['loc'].'</th>';
    }
    echo '</tr>'."\n";

    foreach ($a_times as $t => $dpyt) {
      echo '<tr onmouseover="this.className=\'highlight\'" onmouseout="this.className=\'normal\'">';
      echo '<th class="ptb">'.$dpyt.'</th>';
      foreach ($table as &$c) {
        if (isset($c[$t]) && is_array($c[$t])) {
          $d=$c[$t];
          #if ($c['cskip'] > 0) echo 'TIME CONFLICT!! '.$t.' @'.$c['loc'].'<br/>'; // XXX really list that here in plain view for users?
          # TODO Lightning talks...
          if ($d['starttime'] == '9:00')
            $c['cskip']=2;
          if ($d['starttime'] == '9:30')
            $c['cskip']=1;
          else if (substr($d['title'],0,6) == 'LUNCH ')
            $c['cskip']=3;
          else if ($d['starttime'] == '11:30' && $day==2 && $d['type'] == 'w')
            $c['cskip']=6;
          else
            $c['cskip']=ceil($d['duration']/15);

          $track=track_color($d); # tr0 - tr5
          echo '<td class="ptb'.($print?'':' active').' '.$track.'" rowspan="'.$c['cskip'].'"';
          if (!$print) echo ' onclick="showInfoBox('.$d['id'].');"';
          echo '>';
          #if ($d['status']==0) echo '<span class="red">Cancelled: </span>';
          #echo '<span'.(($d['status']==0)?' class="cancelled"':'').'><span>'.xhtmlify($d['title']).'</span></span>';
          echo '<span>'.xhtmlify($d['title']).'</span>';
          echo ' ('.$d['duration'].'mins)';
          echo '<div class="right"><em>'; $i=0;
          foreach (fetch_authorids($db, $d['id']) as $user_id) {
            if ($i++>0) echo ', ';
            echo xhtmlify($a_users[$user_id]);
          }
          echo '</em></div>';
          echo '</td>';
        } else if ($c['cskip'] == 0) {
          echo '<td class="ptb center">-</td>';
        }

        if ($c['cskip']>0) {
          $c['cskip']--;
        }
      }
      echo '</tr>'."\n";
    }

    if ($day == 1) {
      echo '<tr onmouseover="this.className=\'highlight\'" onmouseout="this.className=\'normal\'">';
      echo '<th class="ptb">20:00</th>';
      echo '<td class="ptb'.($print?'':' active').' trC" colspan="'.$numloc.'"><span>Opening Concert (&asymp;90mins) &raquo; Kubus</span></td>';
      echo '</tr>'."\n";
    }
    else if ($day == 2) {
      echo '<tr onmouseover="this.className=\'highlight\'" onmouseout="this.className=\'normal\'">';
      echo '<th class="ptb">20:00</th>';
      echo '<td class="ptb'.($print?'':' active').' trC" colspan="'.$numloc.'"><span>IMA Concert (&asymp;105mins) &raquo; Kubus</span></td>';
      echo '</tr>'."\n";
      echo '<tr onmouseover="this.className=\'highlight\'" onmouseout="this.className=\'normal\'">';
      echo '<th class="ptb">22:00</th>';
      echo '<td class="ptb'.($print?'':' active').' trC" colspan="'.$numloc.'"><span>Listening Session (&asymp;90mins) &raquo; Balcony/Lounge</span></td>';
      echo '</tr>'."\n";
    }
    else if ($day == 3) {
      echo '<tr onmouseover="this.className=\'highlight\'" onmouseout="this.className=\'normal\'">';
      echo '<th class="ptb">20:00</th>';
      echo '<td class="ptb'.($print?'':' active').' trC" colspan="'.$numloc.'"><span>LAC Concert (&asymp;105mins) &raquo; Kubus</span></td>';
      echo '</tr>'."\n";
      echo '<tr onmouseover="this.className=\'highlight\'" onmouseout="this.className=\'normal\'">';
      echo '<th class="ptb">22:00</th>';
      echo '<td class="ptb'.($print?'':' active').' trC" colspan="'.$numloc.'"><span>Sound Night (&gt;3h) &raquo; Balcony/Lounge</span></td>';
      echo '</tr>'."\n";
    }

    echo '</table>';

    if (!$print || $day == 1) {
      echo track_legend();
    }

    if (!$print) {
      programlightbox();
      echo '<div class="center">Concerts &amp; Installations are <b>not</b> included in this table.</div>';
    }
  }

  function programlightbox() {
      echo '<div id="dimmer" style="display:none;" onclick="hideInfoBox();">&nbsp;</div>';
      echo '<div id="infobox" style="display:none;"><div class="center trc"><div class="footbar" style="top:0px;"><a class="active" onclick="hideInfoBox();">close</a></div></div><div class="ibtoptr">&nbsp;</div><object id="infoframe" data="raw.php" type="application/xhtml+xml"><!--[if IE]><iframe id="ieframe" src="raw.php" allowtransparency="true" frameborder="0" ></iframe><![endif]--></object><div class="trc"><div class="footbar" style="bottom:5px;">&nbsp;</div></div><div class="ibfootl">&nbsp;</div><div class="ibfootr">&nbsp;</div></div>';
  }

  function export_progam_tex($db) {
    $sep="\n";
    $rv= '';
    $rv.= "\\documentclass{article}\n";
    #$rv.= "\\usepackage{a4}\n";
    $rv.= "\\usepackage[cm]{fullpage}\n";
    $rv.= "\\pagestyle{empty}\n\\begin{document}\n";
    #$rv.= "\\begin{itemize}$itemspace\n";
    $itemspace="\\addtolength{\itemsep}{-0.5\\baselineskip}";

    # Table Body
    $a_locations = fetch_selectlist($db, 'location');
    $a_types = fetch_selectlist(0, 'types');
    $a_days = fetch_selectlist(0, 'days');

    $q='SELECT * FROM activity WHERE day < 5 ORDER BY day, (location_id%8), strftime(\'%H%M\',starttime), typesort(type), serial';
    $res=$db->query($q);
    if (!$res) return; // TODO: print error msg
    $result=$res->fetchAll();

    $cday=0;
    foreach ($result as $r) {
      if ($cday < $r['day']) {
        $lmark=false;
        if ($cday!=0) {
          $rv.= "\\end{itemize}\n";
        }
        $cday= $r['day'];
        $rv.= "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%\n";
        $rv.= "\\begin{center}\n";
        $rv.= "{\\Huge\n";
        $rv.= "Day ".$a_days[$cday]."\n";
        $rv.= "}\n";
        $rv.= "\\end{center}\n";
        $rv.= "\\begin{center}\n";
        $rv.= "  \\LARGE Main-Track\\\\\n";
        #$rv.= "  \\Large Location: Bewerunge Room\n"; # TODO 2011/2012
        $rv.= "\\end{center}\n";
        $rv.= "\\begin{itemize}$itemspace\n";
      }
      if (!$lmark && ($r['location_id'] != 1 && $r['location_id'] != 8)) {
        $lmark=true;
        $rv.= "\\end{itemize}\n";
        $rv.= "%%%%%%%%%%%%%%\n";
        $rv.= "\\begin{center}\n";
        $rv.= "  \\LARGE Workshops \\& Events\n";
        $rv.= "\\end{center}\n";
        $rv.= "\\begin{itemize}$itemspace\n";
      }

      $rv.= '\item'."\n  ";
      $rv.= translate_time($r['starttime']).' ';  ##
      $rv.= texify_umlauts(trim($r['title']));
      $donenl=false;
      if ($r['location_id'] != 1 && $r['location_id'] != 8) {
        if (!$donenl) {$rv.="\\\\\n"; $donenl=true;}
        $rv.= '  {\em '.($a_types[$r['type']]).'}'.$sep;
        $rv.= '  {\small '.($a_locations[$r['location_id']]).'} -- '.$sep;
      }
      $authorcnt=0;
      $authors=fetch_authorids($db, $r['id']);

      if (!$donenl && count($authors) >0) {$rv.="\\\\\n"; $donenl=true;}
      $rv.= "  {\em ";

      foreach (fetch_authorids($db, $r['id']) as $user_id) {
        $ur=$db->query('SELECT * FROM user WHERE id ='.$user_id.';');
        if (!$ur) continue; ## TODO report error ?
        $ud=$ur->fetch(PDO::FETCH_ASSOC);

        if ($authorcnt++) $rv.=', ';
        $rv.=texify_umlauts(trim($ud['name']));
      }
      $rv.= '}';
      $rv.= $sep;
    }
    $rv.= "\end{itemize}\n";
    // installations -- day 5

    $q='SELECT * FROM activity WHERE day=5 ORDER BY location_id, typesort(type), serial';
    $res=$db->query($q);
    if ($res) {
        $result=$res->fetchAll();
        $rv.= "\\begin{center}\n";
        $rv.= "{\\Huge\n";
        $rv.= "Every Day\n";
        $rv.= "}\n";
        $rv.= "\\end{center}\n";
        $rv.= "\\begin{center}\n";
        $rv.= "  \\LARGE Installations \\& Audio-Loops\n";
        $rv.= "\\end{center}\n";
        $rv.= "\\begin{itemize}$itemspace\n";
        foreach ($result as $r) {
          $rv.= '\item'."\n  ";
          $rv.= texify_umlauts(trim($r['title']));
          #$rv.="\\\\\n";
          $rv.=" -- ";
          #$rv.= '  {\em '.($a_types[$r['type']]).'}'.$sep;
          $rv.= '  {\small '.($a_locations[$r['location_id']]).'} -- '.$sep;
          $authorcnt=0;
          $authors=fetch_authorids($db, $r['id']);

          if (!$donenl && count($authors) >0) {$rv.="\\\\\n"; $donenl=true;}
          $rv.= "  {\em ";

          foreach (fetch_authorids($db, $r['id']) as $user_id) {
            $ur=$db->query('SELECT * FROM user WHERE id ='.$user_id.';');
            if (!$ur) continue; ## TODO report error ?
            $ud=$ur->fetch(PDO::FETCH_ASSOC);

            if ($authorcnt++) $rv.=', ';
            $rv.=texify_umlauts(trim($ud['name']));
          }
          $rv.= '}';
          $rv.= $sep;
        }

        $rv.= "\end{itemize}\n";
    }

    $rv.= "\end{document}\n";
    return $rv;
  }

  function pdb_html_title($db, $id) {
    $q='SELECT * FROM activity WHERE activity.id='.$db->quote($id).';';
    $res=$db->query($q);
    if (!$res) { return ''; }
    $result=$res->fetchAll();
    if (count($result) != 1 ) { return ''; }
    $title='"'.xhtmlify($result[0]['title']).'"';
    $a_users = fetch_selectlist($db);
    $i=0; foreach (fetch_authorids($db, $result[0]['id']) as $user_id) {if ($i++>0) $title.=', '; else $title.=' by '; $title.=xhtmlify($a_users[$user_id]); }
    return $title;
  }

  function export_progam_sv($db, $sep="\t") {
    # Table Header
    $rv='';
    $rv.= '"Start time"'.$sep;
    $rv.= '"End time"'.$sep;
    $rv.= '"Type"'.$sep;
    $rv.= '"Status"'.$sep;
    $rv.= '"Location"'.$sep;
    $rv.= '"Title"'.$sep;
    $rv.= '"Abstract"'.$sep;
    $rv.= '"Notes"'.$sep;
    $rv.= '"Author(s)"'.$sep;

    $rv.= "\n";

    # Table Body
    $a_locations = fetch_selectlist($db, 'location');
    $a_types = fetch_selectlist(0, 'types');

    $q='SELECT * FROM activity ORDER BY day, location_id, strftime(\'%H:%M\',starttime), typesort(type), serial';
    $res=$db->query($q);
    if (!$res) return; // TODO: print error msg
    $result=$res->fetchAll();

    foreach ($result as $r) {
      $rv.= '"'.iso8601($r).'"'.$sep;
      $rv.= '"'.iso8601($r,false).'"'.$sep;
      $rv.= '"'.($a_types[$r['type']]).'"'.$sep;
      $rv.= '"'.($r['status']==1?'confirmed':($r['status']==2?'hidden':'cancelled')).'"'.$sep;
      $rv.= '"'.($a_locations[$r['location_id']]).'"'.$sep;
      $rv.= '"'.trim($r['title']).'"'.$sep;
      $rv.= '"'.
         str_replace("\r",'',
         str_replace("\n",'\n',
         str_replace('"','\"',
          trim($r['abstract'])
         ))).'"'.$sep;
      $rv.= '"'.
         str_replace("\r",'',
         str_replace("\n",'\n',
         str_replace('"','\"',
          trim($r['notes'])
         ))).'"'.$sep;

      $rv.='"'; $authorcnt=0;


      foreach (fetch_authorids($db, $r['id']) as $user_id) {
        $ur=$db->query('SELECT * FROM user WHERE id ='.$user_id.';');
        if (!$ur) continue; ## TODO report error ?
        $ud=$ur->fetch(PDO::FETCH_ASSOC);

        if ($authorcnt++) $rv.=', ';
        $rv.=trim($ud['name']);
        if (!empty($ud['email']))
          $rv.=' ('.trim($ud['email']).')';
        # TODO : add new author fields (tagline, etc)
        if (!empty($ud['bio']))
          $rv.= ' ['.
             str_replace("\r",'',
             str_replace("\n",'\n',
             str_replace('"','\"',
              trim($ud['bio'])
             ))).']';
      }
      $rv.= '"'.$sep;
      $rv.= "\n";
    }
    return $rv;
  }

  function vcal_program($db,$version='2.0',$raw=true) {
    global $config;
    if (!function_exists('quoted_printable_encode'))
      require_once('lib/quoted_printable.php');

    if ($version!='1.0' && $version!='2.0')
      $version='2.0';

    if ($raw) {
      ##header('Content-Type:text/calendar');
      header('Content-type: text/calendar; charset=utf-8');
      #header("Content-Type: text/x-vCalendar");
      header("Content-Disposition: inline; filename=lac".LACY.".ics");
    }

    date_default_timezone_set('UTC');

    $a_users = fetch_selectlist($db);
    $a_locations = fetch_selectlist($db, 'location');

    $q='SELECT * FROM activity WHERE type !=\'c\' and type !=\'i\' ORDER BY day, typesort(type), location_id, strftime(\'%H:%M\',starttime, serial)';
    $res=$db->query($q);
    if (!$res) return; // TODO: print error msg
    $result=$res->fetchAll();
    echo 'BEGIN:VCALENDAR'."\r\n";
    echo 'VERSION:'.$version."\r\n";
    echo 'PRODID:-//'.$config['organizaion'].'/LAC'.LACY.'//NONSGML v1.0//EN'."\r\n";

# XXX hardcoded concerts
    $result[] = array('id'=> 1000, 'day' => '1', 'starttime' => '20:00', 'duration' => '90',  'type' => 'c', 'title' => 'Opening Concert', 'abstract' => '', 'location_id' => 3, 'status' => '1');
    $result[] = array('id'=> 1001, 'day' => '2', 'starttime' => '20:00', 'duration' => '90',  'type' => 'c', 'title' => 'IMA Concert', 'abstract' => '', 'location_id' => 3, 'status' => '1');
    $result[] = array('id'=> 1002, 'day' => '2', 'starttime' => '22:00', 'duration' => '90',  'type' => 'c', 'title' => 'Launge / Playnight', 'abstract' => '', 'location_id' => 4, 'status' => '1');
    $result[] = array('id'=> 1003, 'day' => '3', 'starttime' => '20:00', 'duration' => '90', 'type' => 'c', 'title' => 'LAC Concert', 'abstract' => '', 'location_id' => 3, 'status' => '1');
    $result[] = array('id'=> 1004, 'day' => '3', 'starttime' => '22:00', 'duration' => '180',  'type' => 'c', 'title' => 'Sound Night', 'abstract' => '', 'location_id' => 4, 'status' => '1');

    foreach ($result as $r) {
      if (empty($r['starttime'])) continue;
      if (empty($r['duration']) || $r['duration'] == 0 || strstr($r['duration'], ':')) continue;
      if ($r['status']!=1) continue; // XXX cancelled, hidden

      echo 'BEGIN:VEVENT'."\r\n";
      echo 'UID:lac'.LACY.'-'.$r['id'].'@'.$config['organizaion']."\r\n";

      $dtstamp=filemtime(preg_replace('@^[^:]*:@', '', PDOPRGDB));
      echo 'DTSTAMP:'.date("Ymd\THis\Z", $dtstamp)."\r\n";  // optional

      foreach (fetch_authorids($db, $r['id']) as $user_id) {
        echo 'ATTENDEE;ROLE=CHAIR;CN='.trim($a_users[$user_id]).':MAILTO:no-reply@'.$config['organizaion']."\r\n";
      }
      echo 'DTSTART:'.iso8601($r)."\r\n";
      echo 'DTEND:'.iso8601($r,false)."\r\n";
      if ($version=='2.0') {
        echo 'SUMMARY:LAC'.LACY.' - '.str_replace(',','\,',trim($r['title']))."\r\n";
        echo 'DESCRIPTION:'.str_replace("\r",'',str_replace(';','\;',str_replace(',','\,',str_replace("\n",'\n',trim($r['abstract'])))))."\r\n";
      } else {
        echo 'SUMMARY;ENCODING=QUOTED-PRINTABLE:LAC'.LACY.' - '.quoted_printable_encode(trim($r['title']))."\r\n";
        echo 'DESCRIPTION;ENCODING=QUOTED-PRINTABLE:'.quoted_printable_encode(str_replace("\n",'\n',trim($r['abstract'])))."\r\n";
      }
      if (!empty($r['location_id']) && $r['location_id'] > 0)
        echo 'LOCATION:'.trim($a_locations[$r['location_id']])."\r\n";

      echo 'CATEGORIES:'.translate_type($r['type'])."\r\n";
      #echo 'CATEGORIES:Ambisonics,whatever here, more there'."\r\n";
      echo 'END:VEVENT'."\r\n";
    }
    echo 'END:VCALENDAR'."\r\n";
  }

  function hl_checkauth(&$req, &$res) {
    $authok=false;

    // check social auth
    if (isset($_REQUEST['provider'])) {
      $provider_name = rawurldecode($_REQUEST['provider']);
      $param=null;
      if (isset($_REQUEST['openid_identifier']) && $provider_name=='OpenID') {
        $param=array(
          'openid_identifier' => rawurldecode($_REQUEST['openid_identifier']),
          'hauth_return_to'   => BASEURL.'?page=logon&provider=OpenID' # note NO &amp !
        );
      }
      require_once('lib/Hybrid/Auth.php');
      try{
        $hybridauth = new Hybrid_Auth('cfg-auth.php');
        $adapter = $hybridauth->authenticate( $provider_name, $param);
        if( $hybridauth->isConnectedWith( $provider_name ) ){
          $user_profile = $adapter->getUserProfile();
        } else {
          $res['errmsg'] = 'Log-on failed.';
          $res['visfun']='act_logon_form';
          return false;
        }

        if (get_user_by_provider_and_uid($provider_name, $user_profile->identifier)) {
          $authok=true;
        } else {
          # TODO: check if logged in locally.. verify sign-up key.
          if (create_new_federated_user( $provider_name, $user_profile->identifier, $user_profile)) {
            $authok=true; #
          } else {
            $res['errmsg'] = 'Failed to create a local user for this remote ID.';
            $res['visfun']='act_logon_form';
            return false;
          }
        }

      } catch( Exception $e ){
        $res['errmsg'] = 'Log-on failed: '.$e->getMessage();
        $res['visfun']='act_logon_form';
        return false;
      }
    }
    if (!$authok
      && isset($_REQUEST['email'])
      && isset($_REQUEST['pass']) ){
        if (check_auth(
          rawurldecode($_REQUEST['email']),
          rawurldecode($_REQUEST['pass'])) > 0) {
            $authok=true;
          }
    }

    if (!$authok) {
      $res['errmsg'] = 'Invalid Username or Password';
      $res['visfun']='act_logon_form';
      return false;
    }
    # all OK: logged in
    $req['handlerfun'] = 'hl_default';
    return true;
  }
# vim: ts=2 et
