<?php

##mysqldump --opt --user lac2013 -p openconf2013

define('YEAR','2013');
define('BASEDIR','/home/sites/lac.linuxaudio.org/'.YEAR);

### INPUT DATABASE (openconf)
# require(BASEDIR.'openconf/config.php'); # not readable:
define("OCC_DB_USER", 'lac'.YEAR);
define("OCC_DB_PASSWORD", "XXX");
define("OCC_DB_HOST", "localhost");
define("OCC_DB_NAME", "openconf".YEAR);
#

$DEBUG=false;

### OUTPUT DATABASE (lac website)
#define('PDOPRGDB','sqlite:'.BASEDIR.'/docroot/tmp/lac'.YEAR.'.db');
define('PDOPRGDB','sqlite:/tmp/lac'.YEAR.'.db');

function ch($s) {
	$s=mb_convert_encoding($s,'utf8');
  $sr = array(
    '@\xe2\x80\x98@' => "`",    # UTF8 single-quote start
    '@\xe2\x80\x99@' => "'",    # UTF8 single-quote end
    '@\xe2\x80\x9c@' => '``',   # UTF8 double-quote start
    '@\xe2\x80\x9d@' => "''",   # UTF8 double-quote end
    '@\xe2\x80\x94@' => ' -- ', # UTF8 dash/minus/hyphen
    '@\xc2\x96@'     => ' -- ', # UTF8 dash/minus/hyphen
    '@\xc2\xa0@'     => ' ',    # non-standard whitespace
    '@\xc2\x97@'     => ' -- ', # dash/minus/hyphen
    '@\xc2\x91@'     => "`",    # omission
    '@\xc2\x92@'     => "'",    # apostrophe
    '@\xc2\x93@'     => '``',   # single quote start
    '@\xc2\x94@'     => "''",   # single quote end
    '@\xc2\x85@'     => "...",  # tiple dots
    '@\xe2\x80\xa6@' => "...",  # tiple dots
  );
	$s=preg_replace(array_keys($sr),array_values($sr), $s);

  $qu = array(
    '@``@' => '"',
    "@''@" => '"',
    "@  +@" => ' ',
  );
	$s=preg_replace(array_keys($qu),array_values($qu), $s);
	return $s;
}

function cday($num) {
	return 1+floor($num / 9);
}
function ctime($num, $type) {
	if ($type !='p') return '';
	switch ($num%9) {
		case 0: return '11:15';
		case 1: return '11:45';
		case 2: return '12:15';
		case 3: return '14:00';
		case 4: return '14:30';
		case 5: return '15:00';
		case 6: return '16:00';
		case 7: return '16:30';
		case 8: return '17:00';
	}
}

### All systems go
$db=new PDO(PDOPRGDB);
$ocdb=new PDO('mysql:host='.OCC_DB_HOST.';dbname='.OCC_DB_NAME, OCC_DB_USER, OCC_DB_PASSWORD);

function oc_query($q) {
  global $ocdb;
  #echo "DEBUG Q: $q\n";
  $res=$ocdb->query($q);
  if ($res) return ($res->fetchAll());
  return false;
}

function lac_query($q, $mode='assoc') {
  global $db;
  $res=$db->query($q);
  if (!$res) return false;
  if ($mode==false) {
    return ($res->fetchAll());
  }
  return ($res->fetch(PDO::FETCH_ASSOC));
}

function lac_exec($q) {
  global $db;
  #echo "DEBUG Q: $q\n";
  if ($db->exec($q))
    return $db->lastInsertId();
  return false;
}

# get papers and fill info
$papers=oc_query('SELECT DISTINCT * from paper where accepted="Accept";');
$px=array();
foreach ($papers as $p) {
  $topics=oc_query('SELECT * from topic join papertopic on papertopic.topicid = topic.topicid where papertopic.paperid='.$p['paperid'].';');
  $p['mytopics'] = $topics;
  $authors=oc_query('SELECT * from author where paperid='.$p['paperid'].' ORDER BY position;');
  $p['myauthors'] = $authors;
  $px[]=$p;
  #print_r($p);
}
$papers=$px; unset($px);
#print_r(count($papers)); exit;

foreach (oc_query('SELECT DISTINCT * from author join paper on paper.paperid=author.paperid where paper.accepted="Accept";') as $a) {
	if ($DEBUG)
		echo "inert user:". $a['name_first'].' '.$a['name_last']."\n";
  $rv=lac_exec('insert into user (name, bio, tagline, email, flags) VALUES('
    .$db->quote(ch($a['name_first'].' '.$a['name_last'])).','
    .$db->quote(ch($a['city'].','.$a['country'])).','
    .$db->quote(ch($a['organization'])).','
    .$db->quote(ch($a['email'])).', 1'
    .');');
  if ($rv===false) {
    echo "insert user:". $a['name_first'].' '.$a['name_last']."\n";
    echo " !!! WARNING -- ADDING user failed : ".$a['email']."\n";
		print_r($db->errorInfo());
		$rv=lac_exec('update user set vip|=1 WHERE email='.$db->quote(ch($a['email'])).';');
  }
}

#print_r(lac_query('SELECT * from user;', false));
echo "-----\n";

$num=1; # skip 1st slot on first day.
foreach ($papers as $p) {
	if ($DEBUG)
		echo "paper :".$p['title']."\n";
	$type='p';
	$location=1;
	$duration=30;

	if (!empty($p['format'])) {
		$paperurl='http://lac.linuxaudio.org/'.YEAR.'/papers/'.$p['paperid'].'.'.$p['format'];
	} else {
		$paperurl='';
	}
	$actid=lac_exec('insert into activity (title, type, abstract, notes, url_paper, duration, location_id'
		.', day, starttime'
		.') VALUES('
    .$db->quote(ch($p['title'])).','
    .$db->quote($type).','
    .$db->quote(ch($p['abstract'])).','
    .$db->quote(ch($p['keywords']."\n".$p['pcnotes']."\n".$p['comments'])).','
    .$db->quote($paperurl)
    .','.$duration.','.$location # duration, location
    .','.cday($num).','.$db->quote(ctime($num, $type))
		.');');

	if ($type=='p') $num++;
	#if ($num==14) $num++; # skip keynote on sat
  #echo "DEBUG: activity: $actid\n";
  if ($actid===false) {
    echo " !!! ERROR INSERTING activity: ".$p['title']."\n";
    print_r($db->errorInfo());
    continue;
  }
  # loop over authors for this paper
	$pos=0;
  foreach ($p['myauthors'] as $a) {
    # loopup lac-authorid
    $lacaid = lac_query('SELECT id from user where email='.$db->quote($a['email']).';');
    if ($lacaid===false) {
      echo " !!! ERROR LOOKING UP user : ".$a['email']."\n";
      continue;
    }
    $rv=lac_exec('insert into usermap (activity_id, user_id, position) VALUES('
      .intval($actid).','
      .intval($lacaid['id']).','
      .(++$pos)
      .');');
    if ($rv===false) {
      echo "author: ".$a['email'].' ('.intval($lacaid['id']).') -> activity: '.$actid."\n";
      echo " !!! ERROR creating user-map.\n";
      print_r($db->errorInfo());
    }
  }
}
$actid=lac_exec('insert into location (name) VALUES ("Main venue");');

# special
lac_exec('insert into activity (title, type, abstract, notes, url_paper, duration, location_id'
		.', day, starttime'
		.') VALUES('
    .$db->quote('Conference Welcome').','
    .$db->quote('o').','
    .$db->quote('').','  # abstract
    .$db->quote('').','  # notes
    .$db->quote('').','  # URL
    .'30, 1' # duration, location
    .',1,'.$db->quote('11:15') # day, time
		.');');

lac_exec('insert into activity (title, type, abstract, notes, url_paper, duration, location_id'
		.', day, starttime'
		.') VALUES('
    .$db->quote('Excursion').','
    .$db->quote('o').','
    .$db->quote('The final event of the conference will be a trip to the beautiful south-eastern Styrian countryside, renowned for its vineyards and pumpkin seed oil... see http://lac.linuxaudio.org/2013/excursion').','  # abstract
    .$db->quote('').','  # Notes
    .$db->quote('').','  # URL
    .'300, 1' # duration, location
    .',4,'.$db->quote('11:15') # day, time
		.');');
echo "OK\n";
