#!/usr/bin/php

<?php

chdir('../');
if ( ! defined('config')) require('config.php');
require('../code/init.php');
chdir('tools/');

if ( ! defined('LOCATION')) require('../lib/Location.php');

$loc = new Location($db_class, $error_class);
$areas = $loc->get_area_for_city($argv[1]);

for ($i = 0, $iz = count($areas); $i < $iz; ++$i) {
   printf("% 25s % 25s % 25s\n", $areas[$i]['city'], $areas[$i]['area'], $areas[$i]['state']);
}

?>
