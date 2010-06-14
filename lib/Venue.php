<?php

define('VENUECLASS', 1);
if ( ! defined('EVENTCLASS')) require(LIB_DIR.'Event.php');

class Venue Extends Event {

   public $venue_id;
   public $name;
   public $address;
   public $phone;
   public $venue_table;

   public $ecp;

   public function __construct(&$db_class, &$error_class) {
      $this->Venue_construct($db_class, $error_class);

      return TRUE;
   }

   protected function Venue_construct(&$db_class, &$error_class) {
      $this->Event_construct($db_class, $error_class);

      $this->init();

      return TRUE;
   }

   private function init() {
      $this->ecp = 'Venue';
      $this->venue_table = 'venue';
      
      $this->address     = NULL;
   }

   public function create() {
      $this->dbc->begin();
      if ($this->verify_venue() === FALSE) {
	 $this->dbc->rollback();
	 return FALSE;
      }

      if ($this->create_venue() === FALSE) {
	 $this->dbc->rollback();
	 return FALSE;
      }

      if (parent::create() === FALSE) {
	 $this->dbc->rollback();
	 return FALSE;
      }

      $this->dbc->commit();
      
      return TRUE;
   }

   protected function verify_venue() {
      $this->sanitize_phone();

      if ($this->verify_column('name') === FALSE)        return FALSE;
      if ($this->verify_column('address') === FALSE)     return FALSE;
      if ($this->verify_column('zip') === FALSE)         return FALSE;
      if ($this->verify_column('phone') === FALSE)       return FALSE;

      return TRUE;
   }

   protected function create_venue() {
      $this->dbc->begin();

      if ($this->add_venue() === FALSE) {
	 $this->dbc->rollback();
	 return FALSE;
      }

      $this->dbc->commit();

      return TRUE;
   }

   protected function add_venue() {
      if ($this->sanity_check() === FALSE) return FALSE;

      $insert['name']    = $this->dbc->escape($this->name);
      $insert['address'] = $this->dbc->escape($this->address);
      $insert['zip_id']  = $this->dbc->escape($this->zip_id);
      $insert['phone']   = $this->dbc->escape($this->phone);
      if ($this->dbc->insert_db($this->venue_table, $insert) === FALSE) {
	 $this->ec->create_error(2, 'Could not add venue to the database', $this->ecp);
	 return FALSE;
      }

      $this->venue_id = $this->dbc->last_seq;

      return TRUE;
   }

   protected function verify_column($column) {
      if (parent::verify_column($column) === FALSE) return FALSE;

      if ($column === 'name') {
	 if ($this->verify_name() === FALSE) return FALSE;
      } else if ($column === 'address') {
	 if ($this->verify_address() === FALSE) return FALSE;
      } else if ($column === 'zip') {
	 if ($this->verify_zip() === FALSE) return FALSE;
      } else if ($column === 'phone') {
	 if ($this->verify_phone() === FALSE) return FALSE;
      }

      return TRUE;
   }

   private function verify_name() {
	 $this->name = trim($this->name);
	 if (strlen($this->name) < MIN_VENUENAME_LEN || strlen($this->name) > MAX_VENUENAME_LEN) {
	    $this->ec->create_error(1, 'Invalid venue title length', $this->ecp);
	    return FALSE;
	 }

	 return TRUE;
   }
   
   private function verify_address() {
      if (strlen($this->address) < 0 || strlen($this->address) > 255) {
	 $this->ec->create_error(8, 'Invalid address length', $this->ecp);
	 return FALSE;
      }

      return TRUE;
   }

   private function verify_zip() {
      if ($this->get_location_from_zip() === FALSE) {
	 $this->ec->create_error(9, 'Invalid zip code', $this->ecp);
	 return FALSE;
      }

      if ($this->get_area_from_zip() === FALSE) {
	 $this->ec->create_error(16, 'There is no area associated with this zip code', $this->ecp);
	 return FALSE;
      }

      return TRUE;
   }

   private function verify_phone() {
      $this->phone = trim($this->phone);
      if (verify_int($this->phone) === FALSE) {
	 $this->ec->create_error(17, 'Invalid phone number', $this->ecp);
	 return FALSE;
      }

      return TRUE;
   }

   private function sanitize_phone() {
      $this->phone = preg_replace("/[^0-9]/", '', $this->phone);
      if (strlen($this->phone) < 1) $this->phone = 0;

      return TRUE;
   }

}

?>
