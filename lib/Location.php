<?php

define('LOCATIONCLASS', 1);

abstract class Location {

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

   public $zip;
   public $city;
   public $county;
   public $area_code;
   public $state;
   public $state_abbr;
   public $country;
   public $area;
   public $neighborhood;

   public $zip_id;
   public $city_id;
   public $county_id;
   public $area_code_id;
   public $state_id;
   public $state_abbr_id;
   public $country_id;
   public $area_id;
   public $neighborhood_id;

   protected function Location_construct() {
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

      return TRUE;
   }

   protected function get_location_from_zip() {
      if ($this->sanity_check() === FALSE) return FALSE;

      $this->zip = $this->dbc->escape($this->zip);

      $sql = 'SELECT zip."id", city."name" AS city, county."name" AS county, state."name" AS state,
	        state."abbr" AS abbr
               FROM "'.$this->zip_table.'" AS zip 
               LEFT OUTER JOIN "'.$this->zip_city_table.'" AS zci ON (zip.id = zci."zip_id")
               LEFT OUTER JOIN "'.$this->city_table.'" AS city ON (zci."city_id" = city."id")
               LEFT OUTER JOIN "'.$this->state_table.'" AS state ON (city."state_id" = state."id")
               LEFT OUTER JOIN "'.$this->zip_county_table.'" AS zco ON (zip."id" = zco."zip_id")
               LEFT OUTER JOIN "'.$this->county_table.'" AS county 
                ON (zco ."county_id" = county."id")
               WHERE zip."zip" = \''.$this->zip.'\' 
	       LIMIT 1';
      $this->dbc->query($sql);
      $this->dbc->fetch_row();
      if ($this->dbc->row_count < 1) return FALSE;

      $this->city       = $this->dbc->rows['city'];
      $this->county     = $this->dbc->rows['county'];
      $this->state      = $this->dbc->rows['state'];
      $this->state_abbr = $this->dbc->rows['abbr'];

      return TRUE;
   }

   protected function get_area_from_zip() {
      if ($this->sanity_check() === FALSE) return FALSE;

      $this->zip = $this->dbc->escape($this->zip);

      $sql = 'SELECT area."name" as area, area."id" as area_id, a2."name" as parent, 
	        a2."id" as parent_id, neighborhood."name" as neighborhood,
	        neighborhood."id" as neighborhood_id
               FROM "zip"
               LEFT OUTER JOIN "zip_area" AS za ON (za."zip_id" = zip."id")
               LEFT OUTER JOIN "area" ON (area."id" = za."area_id")
               LEFT OUTER JOIN "area" as a2 ON (a2."id" = area."parent")
               LEFT OUTER JOIN "zip_neighborhood" AS zn ON (zn."zip_id" = zip."id")
               LEFT OUTER JOIN "neighborhood" ON (neighborhood."id" = zn."neighborhood_id")
	       WHERE zip."zip" = \''.$this->zip.'\'
	       ORDER BY area."parent" DESC
	       LIMIT 1';
      $this->dbc->query($sql);
      $this->dbc->fetch_row();
      if ($this->dbc->row_count < 1 || strlen(trim($this->dbc->rows['area'])) < 1 ) return FALSE;

      $this->area            = $this->dbc->rows['area'];
      $this->area_id         = $this->dbc->rows['area_id'];
      $this->parent          = $this->dbc->rows['parent'];
      $this->parent_id       = $this->dbc->rows['parent_id'];
      $this->neighborhood    = $this->dbc->rows['neighborhood'];
      $this->neighborhood_id = $this->dbc->rows['neighborhood_id'];

      return TRUE;
   }

}

?>

