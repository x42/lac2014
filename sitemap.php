<?php
# vim: ts=2 et
require_once('config.php');
require_once('site.php');

define('NL', "\n");
header ("Content-Type:text/xml");

echo '<?xml version="1.0" encoding="UTF-8"?>'.NL;
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'.NL;

$sitemap = array();

foreach ($pages as $p => $k) {
  $sitemap[$p]['priority'] = .5;
  $sitemap[$p]['changefreq'] = 'daily';
}

$sitemap['']['priority'] = .8;
$sitemap['']['changefreq'] = 'weekly';
$sitemap['about']['priority'] = .8;
$sitemap['about']['changefreq'] = 'weekly';

$sitemap['participation']['priority'] = .7;
$sitemap['participation']['changefreq'] = 'weekly';

if (isset($sitemap['program'])) {
  $sitemap['program']['priority'] = .6;
  $sitemap['program']['changefreq'] = 'weekly';
}
$sitemap['contact']['priority'] = .6;
$sitemap['contact']['changefreq'] = 'monthly';

$sitemap['registration']['priority'] = .3;
$sitemap['registration']['changefreq'] = 'monthly';
$sitemap['participants']['priority'] = .3;
$sitemap['participants']['changefreq'] = 'always';

foreach ($sitemap as $p => $k) {

  if ($config['userewrite']) {
    echo '  <url>'.NL.'    <loc>'.CANONICALURL.$p.'</loc>'.NL;
    if (!empty($k['changefreq'])) 
      echo '    <changefreq>'.$k['changefreq'].'</changefreq>'.NL;
    if (!empty($k['priority'])) 
      echo '    <priority>'.$k['priority'].'</priority>'.NL;
    echo '  </url>'.NL;
  } 
  else if (!empty($p)) {
    echo '  <url>'.NL.'    <loc>'.CANONICALURL.'?page='.$p.'</loc>'.NL;
    if (!empty($k['changefreq'])) 
      echo '    <changefreq>'.$k['changefreq'].'</changefreq>'.NL;
    if (!empty($k['priority'])) 
      echo '    <priority>'.$k['priority'].'</priority>'.NL;
    echo '  </url>'.NL;
  }
}
echo '</urlset>'.NL;
