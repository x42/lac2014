<?php
# vim: ts=2 et
#print_r($_SERVER); exit;

if (!empty($_SERVER['PHP_AUTH_DIGEST'])) {
  header ('Location: index.php?page=admin');
}

#header ('Location: index.php');
header ('401: Authorization Required');
