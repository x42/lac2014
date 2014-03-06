<?php
# vim: ts=2 et
#default page
  $homepage='about';

#pages listed as 'tabs' on the site
  $pages = array(
    'about' => 'About',
    'participation' => 'CFP',
    'registration' => 'Registration',
    'participants' => 'Attendees',
    'travel' => 'Travel &amp; Stay',
    'contact' => 'Contact',
    'sponsors' => 'Supporters',
  );

# other available pages - not shown as 'tabs'
  $hidden = array(
    'program'  => 'Schedule',
    'speakers'  => 'Delegates',
    'excursion' => 'Excursion',
    #'files' => 'Download',
    #'profile' => 'Profile',
  );

#pages that require authentication
  $adminpages = array(
    'upload',
    'admin',
    'adminschedule',
  );

#define sponsors/supportes
  $sponsors = array(
    'http://zkm.de/' => array('img' => 'img/logos/zkm_logo_website.png', 'title' => 'ZKM'),
    'http://linuxaudio.org/' => array('img' => 'img/logos/lao.png', 'title' => 'linuxaudio.org'),
    'http://www.zthmusic.com/' => array('img' => 'img/logos/zthmusic.png', 'title' => 'ZTH Music'),
    'http://www.univ-paris8.fr/en/' => array('img' => 'img/logos/citu.png', 'title' => 'CiTu/Univ Paris 8'),
  );

  function clustermap() {
?>
    <div class="center">
<a href="http://www2.clustrmaps.com/counter/maps.php?url=http://lac.linuxaudio.org/2014/" id="clustrMapsLink" rel="external"><img src="http://www2.clustrmaps.com/counter/index2.php?url=http://lac.linuxaudio.org/2014/" style="border:0px;" alt="Locations of visitors to this page" title="Locations of visitors to this page" id="clustrMapsImg" />
</a>
<script type="text/javascript">
function cantload() {
img = document.getElementById("clustrMapsImg");
img.onerror = null;
img.src = "http://www2.clustrmaps.com/images/clustrmaps-back-soon.jpg";
document.getElementById("clustrMapsLink").href = "http://www2.clustrmaps.com";
}
img = document.getElementById("clustrMapsImg");
img.onerror = cantload;
</script>
    </div>
<?php
  }
