#!/usr/bin/php

<?php

chdir('../');
if ( ! defined('config')) require('config.php');
require('../code/init.php');
chdir('tools/');

if ( ! defined('LOCATION')) require('../lib/Location.php');

$loc = new Location($db_class, $error_class);
$cities = $loc->get_cities_from_area($argv[1]);

for ($i = 0, $iz = count($cities); $i < $iz; ++$i) {
   printf("% 30s % 30s\n", $cities[$i]['city'], $cities[$i]['area']);
}

?>
