<?php
# vim: ts=2 et
  require_once('lib/lib.php');
  require_once('lib/programdb.php');
  xhtmlhead('Conference Schedule', 'printstyle.css', '<link rel="stylesheet" type="text/css" media="print" href="'.BASEURL.'static/print.css" />');
?>
<body id="content">
<div class="menu">Back to the <a href="<?=local_url('program')?>">conference site</a>.</div>
<h1 class="center"><?=SHORTTITLE?> - Conference Schedule</h1>
<div class="center"><?=$config['headerlocation']?></div>
<div class="center">All times are CEST = UTC+2</div>
<?php
  require_once('lib/lib.php');
  require_once('lib/programdb.php');

  table_program($db,1,true);
  table_program($db,2,true);
  table_program($db,3,true);

  echo '<h2 class="ptitle">Day 4 -  Sunday, May/4</h2>';
  # TODO extract from pages/excursion.php (include it and use print CSS?)
?>
<p>
The final event of the conference will be a trip to the countryside to a small village in the &laquo;Kranichgau&raquo;, a hilly region in between the Black Forest and Odenwald about 40 minutes east of Karlsruhe.
</p>
<p>
We'll visit the Kloster Maulbronn which is the best-preserved medieval Cistercian monastery complex in Europe. Apart from its architectural diversity (Romanesque art to late Gothic) it features a unique acoustic, not to mention a brewery with refreshing Kloster-bier.
</p>
<p><br/></p>
<?php
  hardcoded_concert_and_installation_info($db, false);
  hardcoded_disclaimer();
?>
</body>
</html>
