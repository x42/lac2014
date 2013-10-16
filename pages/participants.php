<h1>Registered Participants</h1>
<p>At registration attendees are given the option to announce their presence:</p>
<?php
$pfn=TMPDIR.'lac2014-reg.list';
if (file_exists($pfn)) {
  readfile($pfn);
} else { 
  echo 'No registered participants, yet.'; 
}


