#!/usr/bin/php

<?php

chdir('../');
if ( ! defined('config')) require('config.php');
require('../code/init.php');
chdir('tools/');


$db_class->begin();

$sql = 'SELECT count(1) from zip_area';
$db_class->query($sql);
$db_class->fetch_row();
echo 'Start: '.$db_class->rows['count']."\n";
$sql = 'SELECT zip_id, area_id, count(1) from zip_area group by zip_id, area_id';
$db_class->query($sql);
$db_class->fetch_array();
$dupes = $db_class->rows;

for ( $i = 0, $iz = count($dupes); $i < $iz; ++$i) {
   if ($dupes[$i]['count'] <= 1) continue;

      $area_id = $db_class->escape($dupes[$i]['area_id']);
      $zip_id = $db_class->escape($dupes[$i]['zip_id']);
      $sql = 'DELETE FROM zip_area WHERE ctid = (SELECT ctid FROM zip_area WHERE area_id = \''.$area_id.'\' and zip_id = \''.$zip_id.'\' LIMIT 1)';

      $db_class->query($sql);
}

$sql = 'SELECT count(1) from zip_area';
//$sql = 'select count(1) from zip as z left outer join zip_state as zs on (zs.zip_id = z.id) left outer join zip_area as za on (za.zip_id = z.id) where zs.state_id = 52 and za.area_id IS NULL';
$db_class->query($sql);
$db_class->fetch_row();
echo 'End: '.$db_class->rows['count']."\n";

$sql = 'select z.zip, c.name, z.id from zip as z left outer join zip_state as zs on (zs.zip_id = z.id) left outer join zip_area as za on (za.zip_id = z.id) left outer join zip_county as zc on (zc.zip_id = z.id) left outer join county as c on (c.id = zc.county_id) where zs.state_id = 52 and za.area_id IS NULL';
$db_class->query($sql);
$db_class->fetch_array();
for ($i = 0, $iz = count($db_class->rows); $i < $iz; ++$i) {
   //printf("% 20s % 20s % 20s\n",  $db_class->rows[$i]['zip'], $db_class->rows[$i]['name'], $db_class->rows[$i]['id']);
}

$db_class->commit();
/*
if ( ! defined('LOCATION')) require('../lib/Location.php');

$loc = new Location($db_class, $error_class);
$areas = $loc->get_area_for_city($argv[1]);

for ($i = 0, $iz = count($areas); $i < $iz; ++$i) {
   printf("% 25s % 25s % 25s\n", $areas[$i]['city'], $areas[$i]['area'], $areas[$i]['state']);
}
 */
?>
