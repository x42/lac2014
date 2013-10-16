<?php
## download protected files -- admin only ##

  $req=rawurldecode($_REQUEST['file']);

  require_once('lib/lib.php');
  if (!authenticate()) die();

  $fn='';
  switch ($req) {
    case 'lac'.LACY.'badges.pdf':
      $ctype='application/pdf';
      $fn = TMPDIR.'lac'.LACY.'badges.pdf';
      break;
    case 'schedule.tex':
      $ctype='text/plain';
      $fn = TMPDIR.'schedule.tex';
      break;
    case 'schedule.csv':
      $ctype='text/csv';
      $fn = TMPDIR.'schedule.csv';
      break;
    case 'registrations.csv':
      $ctype='text/csv';
      $fn = TMPDIR.'registrations.csv';
      break;
    default:
      die();
  }

  if (!is_file($fn)) {
    header('HTTP/1.1 404 Not Found');
    die('file not found.');
  }

  @ob_end_clean(); 
  header('Content-Type: ' . $ctype);
  header('Content-Disposition: attachment; filename="'.$req.'"');
  header('Accept-Ranges: bytes');
  header("Cache-control: private");
  header('Pragma: private');

  $size = filesize($fn);
  $end = $size - 1;
  if(isset($_SERVER['HTTP_RANGE'])) {
    list($a, $range) = explode("=",$_SERVER['HTTP_RANGE']);
    list($start, $a) = explode("-", $range);
    $length = $size - $start;
    header("HTTP/1.1 206 Partial Content");
  } else {
    $start=0;
    $length=$size;
  }
  header("Content-Length: $length");
  header("Content-Range: bytes $start-$end/$size");

  $chunksize = 1*(1024*1024);
  $bytes_send = 0;
  if ($file = fopen($fn, 'r')) {
    if(isset($_SERVER['HTTP_RANGE']))
    fseek($file, $start);
    while(!feof($file) and (connection_status()==0)) {
      $buffer = fread($file, $chunksize);
      print($buffer);
      flush();
      $bytes_send += strlen($buffer);
    }
    fclose($file);
  }
  else die('error can not open file');
  exit();
# vim: ts=2 et
