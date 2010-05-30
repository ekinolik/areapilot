<?php

if ( ! defined('LOCATION')) require('lib/Location.php');
if ( ! defined('MISC')) require(LIB_DIR.'Misc.php');

$loc = new Location($db_class, $error_class);
$loc->get_states();

if (isset($_POST['areaname'])) {

   $zips = array();
   $cities = array();
   $area_codes = array();
   $counties = array();

   /* Create area and Exit if there are no locations to add to the area */
   if ( ! isset($_POST['newareas']) || count($_POST['newareas']) < 1) {
      $db_class->begin();
      if (strlen(trim($_POST['areaname'])) > 0 && $loc->create_area($_POST['areaname']) === FALSE) {
	 $db_class->rollback();
	 return FALSE;
      }
      $db_class->commit();

      header('Location: '.$CURRENT_URL.'add_area.php');
      exit;
   }

   for ($i = 0, $iz = count($_POST['newareas']); $i < $iz; ++$i) {
      $prefix = substr($_POST['newareas'][$i], 0, 3);
      $id = substr($_POST['newareas'][$i], 3);

      if     ($prefix == 'zi_') $zips[] = $id;
      elseif ($prefix == 'ci_') $cities[] = $id;
      elseif ($prefix == 'ac_') $area_codes[] = $id;
      elseif ($prefix == 'co_') $counties[] = $id;
      //elseif ($prefix == 'ar_') $areas[] = $id;
   }

   $loc->cities = $loc->get_zip_ids($cities, $loc->city_table, $loc->zip_city_table, 'city_id');
   $loc->area_codes = $loc->get_zip_ids($area_codes, $loc->area_code_table, $loc->zip_area_code_table, 'area_code_id');
   $loc->counties = $loc->get_zip_ids($counties, $loc->county_table, $loc->zip_county_table, 'county_id');
   //$loc->areas = $loc->get_zip_ids($areas, $loc->area_table, $loc->zip_area_table, 'area_id');

   $loc->cities     = remove_md_array($loc->cities, 'zip_id');
   $loc->area_codes = remove_md_array($loc->area_codes, 'zip_id');
   $loc->counties   = remove_md_array($loc->counties, 'zip_id');
   //$loc->areas      = remove_md_array($loc->areas, 'zip_id');

   array_append($zips, $loc->cities);
   array_append($zips, $loc->area_codes);
   array_append($zips, $loc->counties);
   //array_append($zips, $loc->areas);
   $zips = array_unique($zips);
   sort($zips);

   $db_class->begin();

   if (strlen(trim($_POST['areaname'])) > 0 && $loc->create_area($_POST['areaname']) === FALSE) {
      $db_class->rollback();
      return FALSE;
   } elseif (strlen(trim($_POST['area'])) > 0 && $_POST['area'] != 'na') {
      $loc->area_id = $_POST['area'];
   }

   if (verify_int($loc->area_id) === FALSE) {
      $db_class->rollback();
      $error_class->create_error(1, 'Invalid area', 'Code');
      return FALSE;
   }

   /* Add zip codes */
   if ($loc->add_to_area($zips) === FALSE) {
      $db_class->rollback();
      return FALSE;
   }
   unset($zips);

   $db_class->commit();

   header('Location: '.$CURRENT_URL.'add_area.php');
   exit;
} else if (isset($_POST['subareas'])) {
   $loc->area_id = $_POST['parentarea'];

   $db_class->begin();

   if ($loc->remove_parent_from_area($loc->area_id) === FALSE) {
      $db_class->rollback();
      return FALSE;
   }

   $areas = array();

   for ($i = 0, $iz = count($_POST['subareas']); $i < $iz; ++$i) {
      if ($_POST['subareas'][$i] == $loc->area_id) continue;
      $areas[] = substr($_POST['subareas'][$i], strpos($_POST['subareas'][$i], '_') + 1);
   }

   /* Assign parents to areas */
   if ($loc->assign_parent_areas($areas) === FALSE) {
      $db_class->rollback();
      return FALSE;
   }

   $db_class->commit();

   header('Location: '.$CURRENT_URL.'add_area.php');
   exit;
} else if (isset($_POST['areas'])) {
   $loc->area_id = $_POST['areas'];

   if ($loc->delete_area() === FALSE) return FALSE;

   header('Location: '.$CURRENT_URL.'add_area.php');
   exit;
}



?>
