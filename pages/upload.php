<h1>Conference Material Upload</h1>
<?php

  try {
    $db=new PDO("sqlite:tmp/lac2014.db"); // XXX -> config.php
  } catch (PDOException $exception) {
    die ('Database Failure: '.$exception->getMessage());
  }

$printform=true;
$dirlist=true;

$destdir='./download'; # XXX

if (    isset($_POST['postit'])
    &&  isset($_FILES['userfile'])
    &&  is_uploaded_file($_FILES['userfile']['tmp_name'])) {

  $ok=false;
  $err=0;

  $userfile=$_FILES['userfile']['tmp_name'];
  $mfn=rawurldecode($_POST['myfilename']);
  if (empty($mfn)) {
    $userfile_name=$_FILES['userfile']['name'];
  } else {
    $userfile_name=$mfn;
  }

  $userfile_name=preg_replace('/[^a-zA-Z0-9_\-\]\[\.]/','_',$userfile_name);

  if (is_file($destdir.'/'.$userfile_name)) {
    echo 'Note: destination file exists.';
    if ($_POST['override'] == '1') {
      unlink($destdir.'/'.$userfile_name);  # TODO: check for error
      echo '.. replaced.';
    } else {
      $err|=1;
    }
    echo '<br/>';
  }

  if (is_file($userfile) && $err==0) {
    $userfile_name=basename($userfile_name); 
    if(copy($userfile, $destdir.'/'.$userfile_name)) { 
      unlink($userfile); 
      echo "File '$userfile_name' uploaded sucessfully.<br/>";
      $ok=true;
    } 
  } 

  $id =intval(rawurldecode($_POST['pdb_slides']));
  # TODO allow to select link-type of upload: slides, audio, image,..
  if ($ok && $err==0 && $id>0 ) {
      $pdb_url_slides=CANONICALURL.'download/'.$userfile_name;
      $q='UPDATE activity set'
  .' url_slides='.$db->quote($pdb_url_slides)
  .' WHERE id='.$id.';';
    if ($db->exec($q) !== 1) {
      echo 'Error updating slides URL in programdb.';
      $err|=2;
    } else {
      echo 'Updating slides-URL in programdb.';
    }
    echo '&nbsp;|&nbsp;<a href="'.local_url('adminschedule', 'mode=edit&amp;param='.$id).'">Edit ID:'.$id.'</a><br/>';
  }

  if (!$ok) {
    echo 'Upload failed.';
  }

  echo '<div style="margin-bottom:1em;">&nbsp;</div>';
}

if ($printform) {
  echo '<form action="index.php" method="post" id="myform" enctype="multipart/form-data">';
  echo '<fieldset>';
  echo '<input name="page" type="hidden" value="upload"/>';
  echo '<input name="secret" type="hidden" value="'.$_REQUEST['secret'].'"/>';
  echo '<input type="hidden" name="MAX_FILE_SIZE" value="200000000">';
  echo '<legend>File:</legend>';
  echo '<input name="userfile" type="file"/><br/>';
  echo '<label for="myfilename">Save as filename (leave empty to use original):</label>';
  echo '<input id="myfilename" name="myfilename" type="text" value=""/><br/>';
  echo '<label for="override">Overwrite existing file:</label>';
  echo '<input id="override" name="override" type="checkbox" value="1"/><br/>';
  echo '<input name="postit" type="submit" value="Post it." style="float:right;"/>';
  echo '</fieldset>';
  echo '<fieldset>';
  echo '<legend>Meta:</legend>';
  echo '<label for="pdb_slides">Assign as Slides-URL for talk:</label>';
  echo '<select id="pdb_slides" name="pdb_slides" size="1">';
  
  $db->sqliteCreateFunction("typesort", "typesort", 1);
  $q='SELECT * FROM activity WHERE type=\'p\' OR type=\'w\' ORDER BY day,strftime(\'%H:%M\',starttime)';
  echo '    <option value="-1">'.xhtmlify('--none--').'</option>'."\n";
  $res=$db->query($q);
  $result=$res->fetchAll();
  foreach ($result as $r) {
    echo '    <option value="'.$r['id'].'">'.$r['day'].' '.$r['starttime'].'-'.xhtmlify(limit_text($r['title'],40)).'</option>'."\n";
  }
  echo '</select>&nbsp;';
  echo '</fieldset>';
  echo '</form>';
  echo '<div style="margin-bottom:1em;">&nbsp;</div>';

  if ($dirlist) {
    dirlisttable($destdir);
  }
}
