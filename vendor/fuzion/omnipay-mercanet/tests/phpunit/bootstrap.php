
<?php
// checking if the file exists allows compilation elsewhere if desired.
if (file_exists( __DIR__ . '/vendor/autoload.php')) {
  require_once __DIR__ . '/vendor/autoload.php';
}
elseif (file_exists( __DIR__ . '/../../../../autoload.php')) {
  require_once __DIR__ . '/../../../../autoload.php';
}