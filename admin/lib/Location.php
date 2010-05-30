<?php

define('LOCATION', 1);

class Location {

   public $dbc;
   public $ec;

   public $sc;
   public $ecp;

   public $zip_table;
   public $city_table;
   public $county_table;
   public $area_code_table;
   public $state_table;
   public $country_table;
   public $area_table;
   public $neighborhood_table;
   public $zip_city_table;
   public $zip_county_table;
   public $zip_area_code_table;
   public $zip_state_table;
   public $zip_country_table;
   public $zip_area_table;
   public $zip_neighborhood_table;

   public $zip_id;
   public $zip;
   public $time_zone;

   public $area_id;
   public $area_name;
   public $area_parent;

   public $city_id;
   public $city_name;

   public $state_id;
   public $state_name;
   public $state_abbr;

   public $neighborhood_id;
   public $neighborhood_name;

   public $states;
   public $zips;
   public $cities;
   public $area_codes;
   public $counties;
   public $areas;
   public $parent_areas;
   public $neighborhoods;

   public $state;

   public function __construct(&$db_class, &$error_class) {
      $this->sc = FALSE;
      $this->ecp = 'Location';

      $this->zip_table = 'zip';
      $this->city_table = 'city';
      $this->county_table = 'county';
      $this->area_code_table = 'area_code';
      $this->state_table = 'state';
      $this->country_table = 'country';
      $this->area_table = 'area';
      $this->neighborhood_table = 'neighborhood';
      $this->zip_city_table = 'zip_city';
      $this->zip_county_table = 'zip_county';
      $this->zip_area_code_table = 'zip_area_code';
      $this->zip_state_table = 'zip_state';
      $this->zip_country_table = 'zip_country';
      $this->zip_area_table = 'zip_area';
      $this->zip_neighborhood_table = 'zip_neighborhood';

      $this->states = array();
      $this->zips = array();
      $this->cities = array();
      $this->area_codes = array();
      $this->areas = array();
      $this->counties = array();
      $this->parent_areas = array();
      $this->neighborhoods = array();

      if (is_object($db_class)) $this->dbc = &$db_class;
      if (is_object($error_class)) $this->ec = &$error_class;

      return TRUE;
   }

   public function sanity_check() {
      if ($this->sc !== FALSE) return TRUE;

      if (is_object($this->ec) === FALSE) return FALSE;
      if (is_object($this->dbc) === FALSE) {
	 $this->ec->create_error(1, 'Database Connection Falied', $this->ecp);
	 return FALSE;
      }

      return TRUE;
   }

   public function get_zip_by_zip() {
      if ($this->sanity_check() === FALSE) return FALSE;

      if (verify_int($this->zip) === FALSE) {
	 $this->ec->create_error(2, 'Invalid zip code', $this->ecp);
	 return FALSE;
      }

      $sql = 'SELECT "id", "zip", "time_zone" 
	       FROM "'.$this->zip_table.'"
               WHERE "zip" = \''.$this->dbc->escape($this->zip).'\'
	       LIMIT 1 ';
      $this->dbc->query($sql);
      $this->dbc->fetch_row();
      if (count($this->dbc->rows) < 1) {
	 $this->ec->create_error(3, 'Zip code not found', $this->ecp);
	 return FALSE;
      }

      $this->zip_id = $this->dbc->rows['id'];
      $this->time_zone = $this->dbc->rows['time_zone'];

      return TRUE;
   }


   public function get_states($order='abbr', $active=TRUE) {
      if ($this->sanity_check() === FALSE) return FALSE;

      $order = $this->dbc->escape($order);
      if ($active == TRUE) $where = ' WHERE "active" = \'t\' '; else $where = ' ';
      $sql = 'SELECT "id", "name", "abbr"
	       FROM "'.$this->state_table.'" 
	       '.$where.'
	       ORDER BY "'.$order.'" ';
      $this->dbc->query($sql);
      $this->dbc->fetch_array();

      $this->states = $this->dbc->rows;

      return TRUE;
   }

   public function create_area($name) {
      if ($this->sanity_check() === FALSE) return FALSE;

      if (strlen(trim($name)) < 1) {
	 $this->ec->create_error(4, 'Invalid area name', $this->ecp);
	 return FALSE;
      }

      $insert['name'] = $this->dbc->escape(trim($name));
      if ($this->dbc->insert_db($this->area_table, $insert) === FALSE) {
	 $this->ec->create_error(5, 'Could not add area', $this->ecp);
	 return FALSE;
      }

      $this->area_id = $this->dbc->last_seq;

      return TRUE;
   }

   public function delete_neighborhood() {
      if ($this->sanity_check() === FALSE) return FALSE;

      if (verify_int($this->neighborhood_id) === FALSE || $this->neighborhood_id < 1) {
	 $this->ec->create_error(26, 'Invalid neighborhood ID', $this->ecp);
	 return FALSE;
      }

      $where['id'] = $this->dbc->escape($this->neighborhood_id);
      if ($this->dbc->delete_db($this->neighborhood_table, $where) === FALSE) {
	 $this->ec->create_error(27, 'Could not delete neighborhood', $this->ecp);
	 return FALSE;
      }

      return TRUE;
   }

   public function create_neighborhood() {
      if ($this->sanity_check() === FALSE) return FALSE;

      if (strlen(trim($this->neighborhood_name)) < 1) {
	 $this->ec->create_error(22, 'Invalid neighborhood name', $this->ecp);
	 return FALSE;
      }

      if (verify_int($this->city_id) === FALSE || $this->city_id < 1) {
	 $this->ec->create_error(23, 'Invalid city ID', $this->ecp);
	 return FALSE;
      }

      $this->dbc->begin();

      /* Create Neighborhood */
      $insert['name'] = $this->dbc->escape($this->neighborhood_name);
      $insert['city_id'] = $this->dbc->escape($this->city_id);
      if ($this->dbc->insert_db($this->neighborhood_table, $insert) === FALSE) {
	 $this->ec->create_error(25, 'Could not add neighborhood to the database', $this->ecp);
	 $this->dbc->rollback();
	 return FALSE;
      }
      $this->neighborhood_id = $this->dbc->last_seq;

      for ($i = 0, $iz = count($this->zips); $i < $iz; ++$i) {
	 if (verify_int($this->zips[$i]) === FALSE || $this->zips[$i] < 1) continue;

	 /* Delete existing neighborhood relationships to this zip code */
	 $where['zip_id'] = $this->dbc->escape($this->zips[$i]);
	 if ($this->dbc->delete_db($this->zip_neighborhood_table, $where) === FALSE) {
	    $this->ec->create_error(24, 'Could not delete zip code from database', $this->ecp);
	    $this->dbc->rollback();
	    return FALSE;
	 }

	 /* Create a new neighborhood relationship to this zip code */
	 $insert2['zip_id'] = $this->dbc->escape($this->zips[$i]);
	 $insert2['neighborhood_id'] = $this->dbc->escape($this->neighborhood_id);
	 if ($this->dbc->insert_db($this->zip_neighborhood_table, $insert2, TRUE) === FALSE) {
	    $this->ec->create_error(25, 'Could not create zip/neighborhood relationship', $this->ecp);
	    $this->dbc->rollback();
	    return FALSE;
	 }
      }

      /* Delete any neighborhoods with no associated zip codes */
      $this->delete_empty_neighborhoods();

      $this->dbc->commit();

      return TRUE;
   }


   public function add_to_area($zips) {
      if ($this->sanity_check() === FALSE) return FALSE;

      if (verify_int($this->area_id) === FALSE) {
	 $this->ec->create_error(6, 'Invalid area ID', $this->ecp);
	 return FALSE;
      }

      $insert['area_id'] = $this->dbc->escape($this->area_id);
      for ($i = 0, $iz = count($zips); $i < $iz; ++$i) {
	 if (verify_int($zips[$i]) === FALSE) {
	    $this->ec->create_error(7, 'Invalid zip ID', $this->ecp);
	    return FALSE;
	 }
	 
	 $insert['zip_id'] = $this->dbc->escape($zips[$i]);
	 if ($this->dbc->insert_db($this->zip_area_table, $insert, TRUE) === FALSE) {
	    $this->ec->create_error(8, 'Could not add zip to area', $this->ecp);
	    return FALSE;
	 }

      }

      return TRUE;
   }

   public function assign_parent_areas($areas) {
      if ($this->sanity_check() === FALSE) return FALSE;

      if (count($areas) < 1) return TRUE;

      for ($i = 0, $iz = count($areas); $i < $iz; ++$i) {
	 if (verify_int($areas[$i]) === FALSE || verify_int($this->area_id) === FALSE) continue;

	 $update['parent'] = $this->dbc->escape($this->area_id);
	 $where['id'] = $this->dbc->escape($areas[$i]);
	 if ($this->dbc->update_db($this->area_table, $update, $where) === FALSE) {
	    $this->ec->create_error(9, 'Could not add area', $this->ecp);
	    return FALSE;
	 }
      }

      return TRUE;
   }

   public function get_zip_ids($locations, $table, $rel_table, $rel_col) {
      if ($this->sanity_check() === FALSE) return FALSE;

      if (count($locations) < 1) return FALSE;

      $table = $this->dbc->escape($table);
      $rel_table = $this->dbc->escape($rel_table);
      $rel_col = $this->dbc->escape($rel_col);

      $clause = '';
      for ($i = 0, $iz = count($locations); $i < $iz; ++$i) {
	 if ($i > 0) $clause .= ' OR ';
	 $location = $this->dbc->escape($locations[$i]);
	 $clause .= ' '.$table.'."id" = \''.$location.'\' ';
      }

      $sql = 'SELECT "zip_id"
	       FROM "'.$table.'"
	       LEFT OUTER JOIN "'.$rel_table.'" ON ('.$table.'."id" = '.$rel_table.'."'.$rel_col.'")
	       WHERE '.$clause.' ';
      $this->dbc->query($sql);
      $this->dbc->fetch_array();

      return $this->dbc->rows;
   }

   public function get_zip_in_state($state) {
      if ($this->sanity_check() === FALSE) return FALSE;

      $state = $this->dbc->escape($state);
      $sql = 'SELECT z."id", z."zip", z."time_zone"
	       FROM "'.$this->state_table.'" as s
	       LEFT OUTER JOIN "'.$this->zip_state_table.'" as zs ON (s."id" = zs."state_id")
	       LEFT OUTER JOIN "'.$this->zip_table.'" as z ON (zs."zip_id" = z."id")
	       WHERE s."id" = \''.$state.'\' 
	       ORDER BY z."zip" ';
      $this->dbc->query($sql);
      $this->dbc->fetch_array();
      $this->zips = $this->dbc->rows;

      return TRUE;
   }

   public function get_city_in_state($state) {
      if ($this->sanity_check() === FALSE) return FALSE;

      $state = $this->dbc->escape($state);
      $sql = 'SELECT DISTINCT ON (lower(c."name")) c."id", c."name"
	       FROM "'.$this->state_table.'" as s
	       LEFT OUTER JOIN "'.$this->zip_state_table.'" as zs ON (s."id" = zs."state_id")
	       LEFT OUTER JOIN "'.$this->zip_city_table.'" as zc ON (zs."zip_id" = zc."zip_id")
	       LEFT OUTER JOIN "'.$this->city_table.'" as c ON (zc."city_id" = c."id")
	       WHERE s."id" = \''.$state.'\'
	       ORDER BY lower(c."name") ';
      $this->dbc->query($sql);
      $this->dbc->fetch_array();
      $this->cities = $this->dbc->rows;

      return TRUE;
   }

   public function get_area_code_in_state($state) {
      if ($this->sanity_check() === FALSE) return FALSE;

      $state = $this->dbc->escape($state);
      $sql = 'SELECT DISTINCT ON (ac."area_code") ac."id", ac."area_code"
	       FROM "'.$this->state_table.'" as s
	       LEFT OUTER JOIN "'.$this->zip_state_table.'" as zs ON (s."id" = zs."state_id")
	       LEFT OUTER JOIN "'.$this->zip_area_code_table.'" as zac ON (zs."zip_id" = zac."zip_id")
	       LEFT OUTER JOIN "'.$this->area_code_table.'" as ac ON (zac."area_code_id" = ac."id")
	       WHERE s."id" = \''.$state.'\'
	       ORDER BY ac."area_code" ';
      $this->dbc->query($sql);
      $this->dbc->fetch_array();
      $this->area_codes = $this->dbc->rows;

      return TRUE;
   }

   public function get_county_in_state($state) {
      if ($this->sanity_check() === FALSE) return FALSE;

      $state = $this->dbc->escape($state);
      $sql = 'SELECT DISTINCT ON (lower(co."name")) co."id", co."name"
	       FROM "'.$this->state_table.'" as s
	       LEFT OUTER JOIN "'.$this->zip_state_table.'" as zs ON (s."id" = zs."state_id")
	       LEFT OUTER JOIN "'.$this->zip_county_table.'" as zco ON (zs."zip_id" = zco."zip_id")
	       LEFT OUTER JOIN "'.$this->county_table.'" as co ON (zco."county_id" = co."id")
	       WHERE s."id" = \''.$state.'\'
	       ORDER BY lower(co."name") ';
      $this->dbc->query($sql);
      $this->dbc->fetch_array();
      $this->counties = $this->dbc->rows;

      return TRUE;
   }

   public function get_area_in_state($state) {
      if ($this->sanity_check() === FALSE) return FALSE;

      $state = $this->dbc->escape($state);
      $sql = 'SELECT DISTINCT ON (lower(a."name")) a."id", a."name", a."parent"
	       FROM "'.$this->state_table.'" as s
	       LEFT OUTER JOIN "'.$this->zip_state_table.'" as zs ON (s."id" = zs."state_id")
	       LEFT OUTER JOIN "'.$this->zip_area_table.'" as za ON (zs."zip_id" = za."zip_id")
	       LEFT OUTER JOIN "'.$this->area_table.'" as a ON (za."area_id" = a."id")
	       WHERE s."id" = \''.$state.'\'
	       ORDER BY lower(a."name") ';
      $this->dbc->query($sql);
      $this->dbc->fetch_array();
      $this->areas = $this->dbc->rows;

      return TRUE;
   }

   public function get_parent_areas() {
      if ($this->sanity_check() === FALSE) return FALSE;

      for ($i = 0, $iz = count($this->areas); $i < $iz; ++$i ) {
	 $parent = $this->areas[$i]['parent'];
	 if (verify_int($parent) === FALSE) continue;

	 for ($j = 0, $jz = count($this->areas); $j < $jz; ++$j) {
	    if ($this->areas[$j]['id'] == $parent ) continue 2;
	 }

	 $sql = 'SELECT "id", "name", "parent"
	          FROM "'.$this->area_table.'"
		  WHERE "id" = \''.$parent.'\' ';
	 $this->dbc->query($sql);
	 $this->dbc->fetch_row();
	 $this->dbc->rows['isparent'] = TRUE;
	 $this->areas[] = $this->dbc->rows;
      }

      return TRUE;
   }

   public function get_child_areas($state_id=0) {
      if ($this->sanity_check() === FALSE) return FALSE;

      $tmp_areas = $this->areas;
      $tmp_areas2 = array();
      for ($i = 0, $iz = count($this->parent_areas); $i < $iz; ++$i) {
	 $parent = $this->parent_areas[$i]['id'];
         if (verify_int($parent) === FALSE) continue;

         $this->get_children($parent, TRUE, $state_id);
	 for ($j = 0, $jz = count($this->areas); $j < $jz; ++$j) {
	    if ( ! isset($this->areas[$j]['state_id']) || $this->areas[$j]['state_id'] != $state_id) 
	       continue;
	    $tmp_areas2[] = $this->areas[$j];
	 }
       }

       $this->areas = remove_dupe_md_array(array_merge($tmp_areas, $tmp_areas2), 'id');

       return TRUE;
   }

   public function get_empty_parent_areas($state_id=0) {
      if ($this->sanity_check() === FALSE) return FALSE;

      if (verify_int($state_id) && $state_id > 0) 
	 $clause = ' OR c."state_id" = \''.$this->dbc->escape($state_id).'\' ';
      else
	 $clause = '';

      $sql = 'SELECT a."id", a."name", a."parent", c."state_id", count(1) as subarea_count
	       FROM "'.$this->area_table.'" as a
	       LEFT OUTER JOIN "'.$this->area_table.'" as a2 ON (a."id" = a2."parent")
	       LEFT OUTER JOIN "'.$this->zip_area_table.'" as za ON (za."area_id" = a."id")
	       LEFT OUTER JOIN "'.$this->zip_city_table.'" as zc ON (zc."zip_id" = za."zip_id")
	       LEFT OUTER JOIN "'.$this->city_table.'" as c ON (c."id" = zc."city_id")
	       WHERE a."parent" IS NULL AND ( c."state_id" IS NULL '.$clause.' )
               GROUP BY a."id", a."name", a."parent", c."state_id"
	       ORDER BY lower(a."name") ';
      $this->dbc->query($sql);
      $this->dbc->fetch_array();
      $this->parent_areas = $this->dbc->rows;

      return TRUE;
   }

   public function get_area() {
      if ($this->sanity_check() === FALSE) return FALSE;

      if (verify_int($this->area_id) === FALSE) return FALSE;

      $this->area_id = $this->dbc->escape($this->area_id);
      $sql = 'SELECT "id", "name", "parent"
	       FROM "'.$this->area_table.'"
	       WHERE "id" = \''.$this->area_id.'\' 
	       LIMIT 1';
      $this->dbc->query($sql);
      $this->dbc->fetch_row();
      if ($this->dbc->row_count < 1) return FALSE;

      $this->area_id     = $this->dbc->rows['id'];
      $this->area_name   = $this->dbc->rows['name'];
      $this->area_parent = $this->dbc->rows['parent'];

      return TRUE;
   }

   public function get_children($parent, $include_parent=FALSE, $state_id=0) {
      if ($this->sanity_check() === FALSE) return FALSE;

      if (verify_int($parent) === FALSE) return FALSE;

      $parent = $this->dbc->escape($parent);
      $state_id = $this->dbc->escape($state_id);

      if ($include_parent === TRUE) 
	 $clause = ' OR a."id" = \''.$parent.'\' ';
      else
	 $clause = '';
      if (verify_int($state_id) && $state_id > 0)
	 $state_clause = ' AND c."state_id" = \''.$state_id.'\' ';
      else
	 $state_clause = '';

      $sql = 'SELECT a."id", a."name", a."parent", c."state_id", count(1) as location_count
	       FROM "'.$this->area_table.'" as a
	       LEFT OUTER JOIN "'.$this->zip_area_table.'" as za ON (za."area_id" = a."id")
	       LEFT OUTER JOIN "'.$this->zip_city_table.'" as zc ON (zc."zip_id" = za."zip_id")
	       LEFT OUTER JOIN "'.$this->city_table.'" as c ON (c."id" = zc."city_id")
	       WHERE ( a."parent" = \''.$parent.'\' '.$clause.' ) '.$state_clause.'
	       GROUP BY a."id", a."name", a."parent", c."state_id"
	       ORDER BY lower(a."name") ';
      $this->dbc->query($sql);
      $this->dbc->fetch_array();
      if ($this->dbc->row_count < 1) return FALSE;

      $this->areas = $this->dbc->rows;

      return TRUE;
   }

   public function remove_parent_from_area($parent) {
      if ($this->sanity_check() === FALSE) return FALSE;

      if (verify_int($parent) === FALSE) {
	 $this->ec->create_error(12, 'Invalid parent ID', $this->ecp);
	 return FALSE;
      }

      $update['parent'] = $this->dbc->escape(0);
      $where['parent'] = $this->dbc->escape($parent);
      if ($this->dbc->update_db($this->area_table, $update, $where) === FALSE) {
	 $this->ec->create_error(13, 'Could not remove parent', $this->ecp);
	 return FALSE;
      }

      return TRUE;
   }

   public function delete_area() {
      if ($this->sanity_check() === FALSE) return FALSE;

      if (verify_int($this->area_id) === FALSE || $this->area_id < 1) {
	 $this->ec->create_error(19, 'Invalid area ID', $this->ecp);
	 return FALSE;
      }

      $where['id'] = $this->dbc->escape($this->area_id);
      if ($this->dbc->delete_db($this->area_table, $where) === FALSE) {
	 $this->ec->create_error(20, 'Could not delete area', $this->ecp);
	 return FALSE;
      }

      return TRUE;
   }


   public function get_state_by_abbr($abbr) {
      if ($this->sanity_check() === FALSE) return FALSE;

      if (strlen(trim($abbr)) < 1) {
	 $this->ec->create_error(10, 'Invalid state', $this->ecp);
	 return FALSE;
      }

      $abbr = $this->dbc->escape($abbr);
      $sql = 'SELECT s."id", s."name", s."abbr"
	       FROM "'.$this->state_table.'" as s
	       WHERE lower(s."abbr") = lower(\''.$abbr.'\')
	       LIMIT 1 ';
      $this->dbc->query($sql);
      $this->dbc->fetch_row();
      if ($this->dbc->row_count < 1) {
	 $this->ec->create_error(11, 'Could not find state', $this->ecp);
	 return FALSE;
      }

      $this->state = $this->dbc->rows;

      return TRUE;
   }

   public function get_cities_and_zips() {
      if ($this->sanity_check() === FALSE) return FALSE;

      if (verify_int($this->area_id) === FALSE) {
	 $this->ec->create_error(21, 'Invalid area ID', $this->ecp);
	 return FALSE;
      }

      $this->area_id = $this->dbc->escape($this->area_id);

      $sql = 'SELECT z."id" as zip_id, z."zip", c."id" as city_id, c."name", 
	        n."id" as neighborhood_id, n."name" as neighborhood_name
	       FROM "'.$this->zip_area_table.'" as za 
	       LEFT OUTER JOIN "'.$this->zip_table.'" as z ON (za."zip_id" = z."id")
	       LEFT OUTER JOIN "'.$this->zip_city_table.'" as zc ON (zc."zip_id" = za."zip_id")
	       LEFT OUTER JOIN "'.$this->city_table.'" as c ON (c."id" = zc."city_id")
	       LEFT OUTER JOIN "'.$this->zip_neighborhood_table.'" as zn 
	        ON (zn."zip_id" = za."zip_id")
	       LEFT OUTER JOIN "'.$this->neighborhood_table.'" as n
	        ON (n."city_id" = c."id" AND n."id" = zn."neighborhood_id")
	       WHERE za."area_id" = \''.$this->area_id.'\' 
	       ORDER BY c."name", z."zip" ';
      $this->dbc->query($sql);
      $this->dbc->fetch_array();

      return $this->dbc->rows;
   }

   public function get_neighborhoods_from_city() {
      if ($this->sanity_check() === FALSE) return FALSE;

      if (verify_int($this->city_id) === FALSE || $this->city_id < 1) {
	 $this->ec->create_error(28, 'Invalid city ID', $this->ecp);
	 return FALSE;
      }

      $this->city_id = $this->dbc->escape($this->city_id);

      $sql = 'SELECT n."id", n."name", n."city_id"
	       FROM "'.$this->neighborhood_table.'" as n
	       WHERE n."city_id" = \''.$this->city_id.'\' 
	       ORDER BY lower(n."name") ';
      $this->dbc->query($sql);
      $this->dbc->fetch_array();

      return $this->dbc->rows;
   }

   public function get_zip_id_from_neighborhood() {
      if ($this->sanity_check() === FALSE) return FALSE;

      if (verify_int($this->neighborhood_id) === FALSE) {
	 $this->ec->create_error(30, 'Invalid neighborhood ID', $this->ecp);
	 return FALSE;
      }

      $this->neighborhood_id = $this->dbc->escape($this->neighborhood_id);

      $sql = 'SELECT zn."zip_id"
	       FROM "'.$this->zip_neighborhood_table.'" as zn
	       WHERE zn."neighborhood_id" = \''.$this->neighborhood_id.'\'
	       ORDER BY zn."zip_id"
	       LIMIT 1 ';
      $this->dbc->query($sql);
      $this->dbc->fetch_row();

      if ($this->dbc->row_count < 1) {
	 $this->ec->create_error(31, 'Could not find zip code for this neighborhood', $this->ecp);
	 return FALSE;
      }

      $this->zip_id = $this->dbc->rows['zip_id'];

      return TRUE;
   }

   public function get_neighborhoods_from_area() {
      if ($this->sanity_check() === FALSE) return FALSE;

      if (verify_int($this->area_id) === FALSE || $this->area_id < 1) {
	 $this->ec->create_error(29, 'Invalid area ID', $this->ecp);
	 return FALSE;
      }

      $this->area_id = $this->dbc->escape($this->area_id);

      $sql = 'SELECT n."id", n."name" 
	       FROM "'.$this->zip_area_table.'" as za
               LEFT OUTER JOIN "'.$this->zip_city_table.'" as zc ON (zc."zip_id" = za."zip_id")
	       LEFT OUTER JOIN "'.$this->neighborhood_table.'" as n ON (n."city_id" = zc."city_id")
	       WHERE za."area_id" = \''.$this->area_id.'\' AND n."name" IS NOT NULL
	       GROUP BY n."id", n."name" 
	       ORDER BY n."name" ';
      $this->dbc->query($sql);
      $this->dbc->fetch_array();

      $this->neighborhoods = $this->dbc->rows;

      return TRUE;
   }

   public function get_city_by_zip_id() {
      if ($this->sanity_check() === FALSE) return FALSE;

      if (verify_int($this->zip_id) === FALSE) {
	 $this->ec->create_error(14, 'Invalid zip ID', $this->ecp);
	 return FALSE;
      }

      $this->zip_id = $this->dbc->escape($this->zip_id);
      $sql = 'SELECT c."id" as city_id, c."name" as city_name, s."name" as state_name, s."abbr"
	       FROM "'.$this->zip_city_table.'" AS zc
	       LEFT OUTER JOIN "'.$this->city_table.'" AS c ON (zc."city_id" = c."id")
	       LEFT OUTER JOIN "'.$this->state_table.'" AS s ON (c."state_id" = s."id")
	       WHERE zc."zip_id" = \''.$this->zip_id.'\'
	       LIMIT 1 ';
      $this->dbc->query($sql);
      $this->dbc->fetch_row();
      if ($this->dbc->row_count < 1) {
	 $this->city_name = 'Unknown';
	 $this->state_name = 'Unknown';
	 $this->state_abbr = '??';

	 return FALSE;
      }

      $this->city_name = $this->dbc->rows['city_name'];
      $this->state_name = $this->dbc->rows['state_name'];
      $this->state_abbr = $this->dbc->rows['abbr'];

      return $this->dbc->rows['city_id'];
   }

   public function get_zip_from_area() {
      if ($this->sanity_check() === FALSE) return FALSE;

      if (verify_int($this->area_id) === FALSE) {
	 $this->ec->create_error(15, 'Invalid area ID', $this->ecp);
	 return FALSE;
      }

      $this->area_id = $this->dbc->escape($this->area_id);
      $sql = 'SELECT z."id", z."zip", a."id" as area_id, a."name" as area_name
	       FROM "'.$this->zip_area_table.'" AS za
	       LEFT OUTER JOIN "'.$this->zip_table.'" AS z ON (za."zip_id" = z."id")
	       LEFT OUTER JOIN "'.$this->area_table.'" AS a ON (za."area_id" = a."id")
	       WHERE za."area_id" = \''.$this->area_id.'\' 
	       ORDER BY z."zip"
	       LIMIT 1 ';
      $this->dbc->query($sql);
      $this->dbc->fetch_row();
      if ($this->dbc->row_count < 1) {
	 if ($this->get_children($this->area_id) === FALSE) {
	    $this->ec->create_error(16, 'Could not find zip code for this area', $this->ecp);
	    return FALSE;
	 }

	 $this->area_id = $this->areas[0]['id'];
	 return $this->get_zip_from_area();
      }

      $this->zip_id    = $this->dbc->rows['id'];
      $this->zip       = $this->dbc->rows['zip'];
      $this->area_id   = $this->dbc->rows['area_id'];
      $this->area_name = $this->dbc->rows['area_name'];

      return TRUE;
   }

   public function get_area_by_zip_id($get_parent=FALSE) {
      if ($this->sanity_check() === FALSE) return FALSE;

      if (verify_int($this->zip_id) === FALSE) {
	 $this->ec->create_error(17, 'Invalid zip ID', $this->ecp);
	 return FALSE;
      }

      $this->zip_id = $this->dbc->escape($this->zip_id);
      $sql = 'SELECT a."id", a."name", a."parent"
	       FROM "'.$this->zip_area_table.'" AS za
	       LEFT OUTER JOIN "'.$this->area_table.'" AS a ON (za."area_id" = a."id")
	       WHERE za."zip_id" = \''.$this->zip_id.'\' 
	       LIMIT 1 ';
      $this->dbc->query($sql);
      $this->dbc->fetch_row();
      if ($this->dbc->row_count < 1) {
	 $this->ec->create_error(18, 'No area found', $this->ecp);
	 return FALSE;
      }

      $this->area_id = $this->dbc->rows['id'];
      $this->area_name = $this->dbc->rows['name'];
      $this->area_parent = $this->dbc->rows['parent'];

      if ($get_parent === TRUE && verify_int($this->area_parent) === TRUE) {
	 $this->area_id = $this->area_parent;
	 $this->get_area();
      }

      return TRUE;
   }

   private function delete_empty_neighborhoods() {
      if ($this->sanity_check() === FALSE) return FALSE;

      $sql = 'DELETE FROM "'.$this->neighborhood_table.'" WHERE "id" IN ( 
	       SELECT n."id"
	        FROM "'.$this->neighborhood_table.'" as n
		LEFT OUTER JOIN "'.$this->zip_neighborhood_table.'" as zn 
		 ON (zn."neighborhood_id" = n."id")
		WHERE zn."neighborhood_id" IS NULL
	      ) ';
      $this->dbc->query($sql);

      return TRUE;
   }

}
