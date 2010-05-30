<?php

define('EVENTCLASS', 1);

if ( ! defined('LOCATIONCLASS')) require(LIB_DIR.'Location.php');

class Event extends Location {

   public $dbc;
   public $ec;

   public $title;
   public $date;
   public $time;
   public $timestamp;
   public $address;
   public $url;
   public $description;

   public $user_id;

   private $event_table;
   private $event_description_table;

   private $sc;
   private $ecp;

   public function __construct(&$db_class, &$error_class) {
      $this->Location_construct();

      $this->build_vars();

      if (is_object($db_class)) $this->dbc = &$db_class;
      if (is_object($error_class)) $this->ec = &$error_class;

      return TRUE;
   }

   private function build_vars() {
      $this->sc = FALSE;
      $this->ecp = 'Event';

      $this->title       = NULL;
      $this->date        = NULL;
      $this->time        = NULL;
      $this->address     = NULL;
      $this->city        = NULL;
      $this->state       = NULL;
      $this->zip         = NULL;
      $this->url         = NULL;
      $this->description = NULL;

      $this->event_table = 'event';
      $this->event_description_table = 'event_description';

      return TRUE;
   }

   protected function sanity_check() {
      if ($this->sc !== FALSE) return TRUE;

      if (is_object($this->ec) === FALSE) return FALSE;
      if (is_object($this->dbc) === FALSE) {
	 $this->ec->create_error(1, 'Database Connection Falied', $this->ecp);
	 return FALSE;
      }

      $this->sc = TRUE;

      return TRUE;
   }

   public function create() {
      if ($this->verify_event('title') === FALSE)       return FALSE;
      if ($this->verify_event('time') === FALSE)        return FALSE;
      if ($this->verify_event('address') === FALSE)     return FALSE;
      if ($this->verify_event('zip') === FALSE)         return FALSE;
      //if ($this->verify_event('url') === FALSE)         return FALSE;
      if ($this->verify_event('description') === FALSE) return FALSE;

      if ($this->exists()) {
	 $this->ec->create_error(12, 'This event already exists', $this->ecp);
	 return FALSE;
      }

      $this->dbc->begin();

      if ($this->add_event() === FALSE) {
	 $this->dbc->rollback();
	 return FALSE;
      }

      $this->dbc->commit();

      return TRUE;
   }

   private function add_event() {
      if ($this->sanity_check() === FALSE) return FALSE;

      $datetime = date("Y-m-d H:i:00", $this->timestamp);

      $insert['user_id'] = $this->dbc->escape($this->user_id);
      $insert['title']   = $this->dbc->escape($this->title);
      $insert['time']    = $this->dbc->escape($datetime);
      $insert['area_id'] = $this->dbc->escape($this->area_id);
      if ($this->dbc->insert_db($this->event_table, $insert) === FALSE) {
	 $this->ec->create_error(13, 'Could not add the event to the database', $this->ecp);
	 return FALSE;
      }

      $insert2['event_id']    = $this->dbc->escape($this->dbc->last_seq);
      $insert2['description'] = $this->dbc->escape($this->description);
      $insert2['address']     = $this->dbc->escape($this->zip_id);
      $insert2['zip_id']      = $this->dbc->escape($this->zip_id);
      // FIXME: $insert2['phone']       = $this->dbc->escape($this->phone);
      $insert2['link_url']    = $this->dbc->escape($this->url);
      // FIXME: $inesrt2['link_title']  = $this->dbc->escape($this->link_title);
      if ($this->dbc->insert_db($this->event_description_table, $insert2, TRUE) === FALSE) {
	 $this->ec->create_error(14, 'Could not add the event to the database', $this->ecp);
	 return FALSE;
      }

      return TRUE;
   }

   private function exists() {
      if ($this->sanity_check() === FALSE) return FALSE;

      //FIXME:
      // This should check if the title exists on that date already
      //

      return FALSE;
      return TRUE;
   }

   private function verify_event($column) {
      if ($column === 'title') {
	 if ($this->verify_title() === FALSE) return FALSE;
      } else if ($column === 'time') {
	 if ($this->verify_timestamp() === FALSE) return FALSE;
      } else if ($column === 'address') {
	 if ($this->verify_address() === FALSE) return FALSE;
      } else if ($column === 'zip') {
	 if ($this->verify_zip() === FALSE) return FALSE;
      } else if ($column === 'url') {
	 if ($this->verify_url() === FALSE) return FALSE;
      } else if ($column === 'description') {
	 if ($this->verify_description() === FALSE) return FALSE;
      } else if ($column === 'user_id') {
	 if ($this->verify_user_id() === FALSE) return FALSE;
      }

      return TRUE;
   }

   private function verify_title() {
      if (strlen($this->title) < MIN_TITLE_LEN || strlen($this->title) > MAX_TITLE_LEN) {
	 $this->ec->create_error(6, 'Invalid title length', $this->ecp);
	 return FALSE;
      }

      return TRUE;
   }

   private function verify_timestamp() {
      if (strlen($this->timestamp) < 1 && $this->create_timestamp() === FALSE) return FALSE;

      if (verify_int($this->timestamp) === FALSE) {
	 $this->ec->create_error(7, 'Invalid timestamp', $this->ecp);
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

   private function verify_url() {
      if (verify_url($this->url) === FALSE) {
	 $this->ec->create_error(10, 'Invalid URL', $this->ecp);
	 return FALSE;
      }

      return TRUE;
   }

   private function verify_description() {
      if (strlen($this->description) < MIN_DESCRIPTION_LEN || strlen($this->description) > MAX_DESCRIPTION_LEN) {
	 $this->ec->create_error(11, 'Invalid description length', $this->ecp);
	 return FALSE;
      }

      return TRUE;
   }

   private function verify_user_id() {
      if (verify_int($this->user_id) === FALSE || $this->user_id < 1) {
	 $this->ec->create_error(15, 'Invalid user ID', $this->ecp);
	 return FALSE;
      }

      return TRUE;
   }

   private function create_timestamp() {
      $this->time = trim($this->time);
      $this->meridian = strtolower(trim($this->meridian));

      if (verify_date($this->date) === FALSE) {
	 $this->ec->create_error(3, 'Invalid date', $this->ecp);
	 return FALSE;
      }

      if (strlen($this->time) > 0 && verify_int(str_replace(':', '', $this->time)) === FALSE) {
	 $this->ec->create_error(4, 'Invalid time', $this->ecp);
	 return FALSE;
      }

      if (strlen($this->time) > 0 && $this->meridian !== 'am' && $this->meridian !== 'pm') {
	 $this->ec->create_error(5, 'Is this AM or PM?', $this->ecp);
	 return FALSE;
      }

      if (strlen($this->time) > 0) {
	 $datetime = $this->date . ' ' . $this->time . ' ' . $this->meridian;
      } else {
	 $datetime = $this->date;
      }

      $this->timestamp = strtotime($this->date.' '.$this->time);
      if ($this->timestamp < CURRENT_TIME + (60 * 30)) {
	 $this->ec->create_error(2, 'Event time can not be sooner than 30 minutes from now', $this->ecp);
	 return FALSE;
      }

      return TRUE;
   }

}

?>
