<?php
  require_once('lib/lib.php');
  require_once('lib/programdb.php');
  $version='2.0';
  if (isset($_REQUEST['v']) && rawurldecode($_REQUEST['v'])=='1.0')
    $version='1.0';

  vcal_program($db,$version);
# vim: ts=2 et
