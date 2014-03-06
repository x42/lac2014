<?php
if (!defined('REGLOGDIR')) die();
?>
<form action="index.php" method="post" id="myform">
<?php
  $mode='';
  if (isset($_REQUEST['mode']))
    $mode=rawurldecode($_REQUEST['mode']);

  $showdefault=false;

  switch ($mode) {
    case 'unlocklocation':
      $id=intval(rawurldecode($_REQUEST['param']));
      unlock($db, $id, 'location');
    case 'savelocation':
      if ($mode==='savelocation') dbadmin_savelocation($db);
    case 'dellocation': # TODO :check if locked before deleting..
      if ($mode==='dellocation') dbadmin_dellocation($db);
    case 'listlocation':
      admin_fieldset();
      program_fieldset();
      echo '<legend>Location List:</legend>'."\n";
      dbadmin_listlocations($db);
      dbadmin_jumpselected();
      break;
    case 'unlockuser':
      $id=intval(rawurldecode($_REQUEST['param']));
      unlock($db, $id, 'user');
    case 'saveuser':
      if ($mode==='saveuser') dbadmin_saveuser($db);
    case 'deluser': # TODO :check if locked before deleting..
      if ($mode==='deluser') dbadmin_deluser($db);
    case 'listuser':
      admin_fieldset();
      program_fieldset();
      echo '<legend>Author List:</legend>'."\n";
      dbadmin_listusers($db);
      dbadmin_jumpselected();
      break;
    case 'editlocation':
      $id=intval(rawurldecode($_REQUEST['param']));
      if (lock($db, $id, 'location') === -1 ) {
        program_fieldset();
        echo '<legend>Location Entry:</legend>'."\n";
        dbadmin_locationform($db, $id);
      } else {
        echo '<div class="dbmsg">Entry is currently being edited.</div>'."\n";
        $showdefault=true;
      }
      break;
    case 'edituser':
      $id=intval(rawurldecode($_REQUEST['param']));
      if (lock($db, $id, 'user') === -1 ) {
        program_fieldset();
        echo '<legend>Author Entry:</legend>'."\n";
        dbadmin_authorform($db, $id);
      } else {
        echo '<div class="dbmsg">Entry is currently being edited.</div>'."\n";
        $showdefault=true;
      }
      break;
    case 'edit':
      $id=intval(rawurldecode($_REQUEST['param']));
      if (lock($db, $id) === -1 ) {
        program_fieldset();
        echo '<legend>Program Entry:</legend>'."\n";
        dbadmin_editform($db, $id);
      } else {
        echo '<div class="dbmsg">Entry is currently being edited.</div>'."\n";
        $showdefault=true;
      }
      break;
    case 'texify':
      admin_fieldset();
      program_fieldset();
      $handle = fopen(TMPDIR.'schedule.tex', "w");
      fwrite($handle, export_progam_tex($db));
      fclose($handle);
      echo '<legend>Conference Program - Export to tex:</legend>'."\n";
      echo 'Download: <a href="download.php?file=schedule.tex">schedule.tex</a>';
      echo '</fieldset>';
      break;
      break;
    case 'export':
      admin_fieldset();
      program_fieldset();
      $handle = fopen(TMPDIR.'schedule.csv', "w");
      fwrite($handle, export_progam_sv($db, ","));
      fclose($handle);
      echo '<legend>Conference Program - Export to spreadsheet:</legend>'."\n";
      echo 'Download: <a href="download.php?file=schedule.csv">schedule.csv</a>';
      echo '</fieldset>';
      break;
    case 'profilenotify':
      require_once('lib/userdb.php');
      admin_fieldset();
      $uid=intval(rawurldecode($_REQUEST['param']));
      if ($uid>0) {
        echo '<div class="dbmsg">inviting user '.$uid.'</div>';
        usr_msg_sendhash($db, $uid);
      }
      program_fieldset();
      echo '<legend>Send User Profile Invitations</legend>'."\n";
      dbadmin_profilecheck($db, $uid==-1);
      echo '</fieldset>';
      break;
    case 'profileinfo':
      require_once('lib/userdb.php');
      admin_fieldset();
      $uid=intval(rawurldecode($_REQUEST['param']));
      if ($uid>0) {
        echo '<div class="dbmsg">re-inviting user '.$uid.'</div>';
        usr_msg_sendhash($db, $uid);
      }
      program_fieldset();
      echo '<legend>User Profiles</legend>'."\n";
      dbadmin_profilelist($db);
      echo '</fieldset>';
      break;
    case 'orphans':
      admin_fieldset();
      program_fieldset();
      echo '<legend>Conference Program - Orphan entries:</legend>'."\n";
      dbadmin_orphans($db);
      echo '</fieldset>';
      break;
    case 'conflicts':
      admin_fieldset();
      program_fieldset();
      echo '<legend>Conference Program - Schedule conflicts:</legend>'."\n";
      dbadmin_checkconflicts($db);
      echo '</fieldset>';
      break;
    case 'unlockactivity':
      $id=intval(rawurldecode($_REQUEST['param']));
      unlock($db, $id);
    case 'delentry': # TODO :check if locked before deleting..
      if ($mode==='delentry') dbadmin_delentry($db);
    case 'saveedit':
      if ($mode==='saveedit') dbadmin_saveedit($db);
    default:
      $showdefault=true;
      break;
  }

  if ($showdefault) {
    admin_fieldset();
    program_fieldset();
    echo '<legend>Conference Program:</legend>'."\n";
    $sort=''; if (isset($_REQUEST['sort'])) $sort=rawurldecode($_REQUEST['sort']);
    dbadmin_listall($db, $sort); # does print_filterfields()
    dbadmin_jumpselected();
  } else {
    # hidden filterfields - remember
    $filter=array('user' => '0', 'day' => '0', 'type' => '0');
    if (isset($_REQUEST['pdb_filterday'])) $filter['day'] = intval(rawurldecode($_REQUEST['pdb_filterday']));
    if (isset($_REQUEST['pdb_filtertype'])) $filter['type'] = substr(rawurldecode($_REQUEST['pdb_filtertype']),0,1);
    if (isset($_REQUEST['pdb_filterauthor'])) $filter['user'] = intval(rawurldecode($_REQUEST['pdb_filterauthor']));
    echo '<input id="pdb_filterday" name="pdb_filterday" type="hidden" value="'.rawurldecode($filter['day']).'"/>'."\n";
    echo '<input id="pdb_filtertype" name="pdb_filtertype" type="hidden" value="'.rawurldecode($filter['type']).'"/>'."\n";
    echo '<input id="pdb_filterauthor" name="pdb_filterauthor" type="hidden" value="'.rawurldecode($filter['user']).'"/>'."\n";
  }

?>
  </fieldset>
</form>
