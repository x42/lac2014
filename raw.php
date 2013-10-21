<?php
# vim: ts=2 et
  require_once('lib/lib.php');
  require_once('lib/programdb.php');

  $filter=array('user' => '0', 'day' => '0', 'type' => '0', 'location' => '0', 'id' => '0');
  $title='';
  if (isset($_REQUEST['pdb_filterid'])) {
    $filter['id'] = intval(rawurldecode($_REQUEST['pdb_filterid']));
    $title=pdb_html_title($db, $filter['id']);
  }
  if (empty($title)) {
    $title=SHORTTITLE.' - Presentation Detail';
  }
  xhtmlhead($title);
?>
<body id="content" style="border:0px; background:#ffffff;">
<div>
<?php
  if (isset($_REQUEST['pdb_filterid'])) {
    list_filtered_program($db, $filter, 1);
  }
?>
</div>
</body>
</html>
