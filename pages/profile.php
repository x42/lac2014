<?php
require_once('lib/userdb.php');
if (!defined('NL')) define('NL', "\n");
global $config;

$uid=0;
$ok=false;
$silent=false;
$ukey=''; # user key/handle , session
$mode='';
$lemail=''; $kemail='';
if (isset($_REQUEST['ukey'])) $ukey=rawurldecode($_REQUEST['ukey']);
if (isset($_POST['mode'])) $mode=rawurldecode($_POST['mode']);
if (isset($_POST['pdb_mail'])) $lemail=rawurldecode($_POST['pdb_mail']);
if (isset($_POST['pdb_email'])) $kemail=rawurldecode($_POST['pdb_email']);

switch ($mode) {
	case 'sendkey':
		if (empty($ukey) && !empty($kemail) && empty($lemail)) {
			$ok=true;
			$userid=intval(usr_get_uid($db, $kemail));
			if ($userid<1) {
				echo '<div class="dbmsg">The given email <tt>'.$kemail.'</tt> is unknown to us. If you think this is a mistake, please contact '.$config['txtemail'].'</div>'."\n";
			} else {
				usr_msg_sendhash($db, $userid, $kemail);
			}
		}
		break;
	case 'login':
		if (!empty($ukey) && !empty($lemail) && empty($kemail)) {
			$ok=true;
			$uid=usr_auth_ukey($db, $ukey, $lemail);
			if ($uid<1) {
				echo '<div class="dbmsg">Given Access Token is invalid.</div>'."\n";
			}
		}
		break;
	default:
		if (empty($ukey) && empty($lemail) && empty($kemail)) {
			$ok=true;
		}
		else if (!empty($ukey) && empty($lemail) && empty($kemail)) {
			$ok=true;
			$uid=usr_auth_ukey($db, $ukey);
			if ($uid<1) {
				echo '<div class="dbmsg">Given Access Token is invalid.</div>'."\n";
			}
		}
		break;
}

if (!$ok) {
	echo '<div class="dbmsg">An error occured. Please try again. If the problem persists, please contact '.$config['txtemail'].'</div>'."\n";
	$uid=0;
}


if ($uid<1) {
	# Login form
	echo '<form action="index.php" method="post" id="myform">
		<fieldset class="pdb">
			<input name="page" type="hidden" value="profile" id="page"/>
			<input name="mode" type="hidden" value="sendkey" id="mode"/>
			';
	echo '<legend>Signup / Request Access Key:</legend>'."\n";
	echo '<label for="pdb_email">Email:</label><br/>';
	echo '<input id="pdb_email" name="pdb_email" length="80" value="" /><br/>';
	echo '<input class="button" type="submit" title="Send Logon Info" value="Send Logon Info" />'."\n";
	echo '<div class="clearer"></div>';
	echo '</fieldset><fieldset class="pdb">';
	echo '<legend>or direct Login:</legend>'."\n";
	echo '<label for="pdb_mail">Email:</label><br/>';
	echo '<input id="pdb_mail" name="pdb_mail" length="80" value="" /><br/>';
	echo '<label for="ukey">Password:</label><br/>';
	echo '<input id="ukey" name="ukey" type="password" length="80" value="" /><br/>';
	echo '<input class="button" type="button" title="Login" value="Login" onclick="document.getElementById(\'mode\').value=\'login\';formsubmit(\'myform\');"/>'."\n";
	echo "</fieldset>".NL;
	echo "</form>".NL;
	$silent=true;
}

$suid=0; $r=NULL;
if (isset($_POST['uid'])) $suid=rawurldecode($_POST['uid']);
if ($suid != $uid || $uid < 1) { $mode=''; }

switch ($mode) {
	case 'unlockprofile':
		unlock($db, $uid, 'user');
		echo '<div><a href="'.local_url('profile', 'ukey='.$ukey).'" >Edit Profile</a></div><br/>';
		render_profile(fetch_user($db, $uid, true), fetch_user_activities($db, $uid));
		$silent=true;
		break;
	case 'saveprofile':
		unlock($db, $uid, 'user');
		$flags=0;
		$flags|=isset($_REQUEST['pdb_flag_pub'])?1:0;
		$q='UPDATE user set '
		.' bio='.$db->quote(rawurldecode($_REQUEST['pdb_bio']))
		.',flags=(flags&~1)|('.intval($flags).')'
		.',tagline='.$db->quote(rawurldecode($_REQUEST['pdb_tagline']))
		.',url_image='.$db->quote(usr_sanitize_imgurl(rawurldecode($_REQUEST['pdb_url_image'])))
		.',url_person='.$db->quote(usr_sanitize_imgurl(rawurldecode($_REQUEST['pdb_url_person'])))
		.',url_institute='.$db->quote(usr_sanitize_imgurl(rawurldecode($_REQUEST['pdb_url_institute'])))
		.',url_project='.$db->quote(usr_sanitize_imgurl(rawurldecode($_REQUEST['pdb_url_project'])))
		.',udate=DATETIME()'
		.' WHERE id='.$uid.';';
		$err=($db->exec($q) !== 1)?1:0;
		echo '<div class="dbmsg">Saved User-ID='.$uid.'.. '.($err==0?'OK':'Error:'.$err).'</div>'."\n";

		echo '<div><a href="'.local_url('profile', 'ukey='.$ukey).'" >Edit Profile</a></div><br/>';
		render_profile(fetch_user($db, $uid, true), fetch_user_activities($db, $uid));
		$silent=true;
	default:
		break;
}

if ($uid>0) {
	$q='SELECT * FROM user WHERE id ='.$uid.';';
	$res=$db->query($q);
	if ($res) {
		$r=$res->fetch(PDO::FETCH_ASSOC);
	}
}

if (!is_array($r)) {
	if (!$silent) {
		echo "error - invalid profile or user-id: $uid\n";
	}
	$silent=true;
} else if (!$silent) {
	if (lock($db, $uid, 'user') !== -1 ) {
		echo '<form action="index.php" method="post" id="myform">
			<fieldset class="pdb">
				<input name="page" type="hidden" value="profile" id="page"/>
				<input name="mode" type="hidden" value="" id="mode"/>
				<input name="uid"  type="hidden" value="'.$r['id'].'" id="uid"/>
				<input name="ukey" type="hidden" value="'.$ukey.'" id="ukey"/>
			';
		echo '<div class="dbmsg">This profile is currently being edited. Please try again in a few minutes.</div>'."\n";
		echo '<input class="button" type="submit" title="Retry" value="Retry" />'."\n";
		echo "</fieldset>".NL;
		echo "</form>".NL;
		$silent=true;
	}
}

if (!$silent) {
	#echo '<div><a href="'.local_url('speakers', 'uid='.$r['id']).'" rel="external">View Public Profile</a></div>';

	echo '<div id="profile">';
	echo '<form action="index.php" method="post" id="myform">
		<fieldset class="pdb">
			<input name="page" type="hidden" value="profile" id="page"/>
			<input name="mode" type="hidden" value="" id="mode"/>
			<input name="uid"  type="hidden" value="'.$r['id'].'" id="uid"/>
			<input name="ukey" type="hidden" value="'.$ukey.'" id="ukey"/>
			';
	echo '<legend>Author Entry:</legend>'."\n";
	#echo '<em>ID:</em> '.$r['id'];
	echo ' <label>Class:</label> ';
	if ($r['vip']&1) echo 'Speaker ';
	if ($r['vip']&2) echo 'Organizer ';
	if ($r['vip']&4) echo 'Committee ';
	if ($r['vip']&8) echo 'Artist ';
	echo '<br/>';

	echo '<div style="float:right; text-align:right; max-width:50%; font-size:small; line-height:1.0em;">Your name is taken from the submission or registration-form.<br/>If it is incorrectly spelled, please contact us, at '.$config['txtemail'].'. It is most likely wrong in the conference schedule and the paper, as well.</div>';
	html_text_readonly('Name', 'name', $r);
	echo '<div class="clearer"></div>';

	html_checkbox('Publish Profile', 'flag_pub', $r['flags']&1);
	echo '<br/>';

	html_text_input('Tagline/Affiliation', 'tagline', $r);
	if (empty($r['url_image']) && !empty($r['email']))  {
		echo '<div style="text-align:right;"><a href="http://gravatar.com/emails/" rel="external">Change your image on gravatar.com</a></div>';
	}
	echo '<div class="portrait"><img src="'.usr_imgurl($r).'" alt="Image -- '.$r['name'].'"/></div>';
	echo '<div style="margin-right:220px;">';
	html_text_input('Image URL (if empty: use gravatar.com) [&le; 200x200px]', 'url_image', $r);
	html_text_input('URL 1 (Personal)', 'url_person', $r);
	html_text_input('URL 2 (Institute, Company)', 'url_institute', $r);
	html_text_input('URL 3 (Project)', 'url_project', $r);
	echo '</div>';
	echo '<div class="clearer"></div>';
	echo '<script type="text/javascript">
setonclosewarn();
</script>';

	echo '<label for="pdb_bio">Bio:</label><br/>';
	echo '<textarea id="pdb_bio" name="pdb_bio" rows="8" cols="70">'.xhtmlify($r['bio']).'</textarea><br/><br/>';

	echo '<input class="button" type="button" title="Save" value="Save" onclick="noonclosewarn();document.getElementById(\'mode\').value=\'saveprofile\';formsubmit(\'myform\');"/>'."\n";
	echo '<input class="button" type="button" title="Cancel" value="Cancel" onclick="noonclosewarn();document.getElementById(\'mode\').value=\'unlockprofile\';formsubmit(\'myform\');"/>'."<br/>&nbsp;\n";
	echo '</div>';
	echo "</fieldset>".NL;
	echo "</form>".NL;
}
