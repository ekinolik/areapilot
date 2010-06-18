<?php

define('EVENTCLASS', 1);

if ( ! defined('LOCATIONCLASS')) require(LIB_DIR.'Location.php');

class Event extends Location {

   public $dbc;
   public $ec;

   public $events;

   public $event_id;
   public $title;
   public $date;
   public $time;
   public $timestamp;
   public $url;
   public $description;

   public $category;
   public $category_id;

   public $venue_id;
   public $user_id;

   public $event_table;
   public $event_description_table;
   public $tag_table;
   public $event_tag_table;
   public $venue_table;
   public $category_table;
   public $event_category_table;

   // FIXME: Move these to their own class
   private $user_table;

   protected $sc;
   private $ecp;

   public function __construct(&$db_class, &$error_class) {
      $this->Event_construct($db_class, $error_class);

      return TRUE;
   }

   protected function Event_construct(&$db_class, &$error_class) {
      $this->Location_construct();
      $this->init();

      if (is_object($db_class)) $this->dbc = &$db_class;
      if (is_object($error_class)) $this->ec = &$error_class;

      return TRUE;
   }

   private function init() {
      $this->sc = FALSE;
      $this->ecp = 'Event';

      $this->title       = NULL;
      $this->date        = NULL;
      $this->time        = NULL;
      $this->url         = NULL;
      $this->description = NULL;
      $this->category    = NULL;

      $this->event_table = 'event';
      $this->event_description_table = 'event_description';
      $this->tag_table = 'tag';
      $this->event_tag_table = 'event_tag';
      $this->venue_table = 'venue';
      $this->category_table = 'category';
      $this->event_category_table = 'event_category';

      // FIXME: Move these to their own class
      $this->user_table = 'user';

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
      if ($this->verify_event() === FALSE) return FALSE;
      if ($this->verify_category() === FALSE) return FALSE;
      return $this->create_event();
   }

   protected function verify_event() {
      if ($this->verify_column('title') === FALSE)       return FALSE;
      if ($this->verify_column('time') === FALSE)        return FALSE;
      //if ($this->verify_column('url') === FALSE)         return FALSE;
      if ($this->verify_column('description') === FALSE) return FALSE;

      //if ($this->sanitize_tag() === FALSE) return FALSE;

      if ($this->exists()) {
	 $this->ec->create_error(12, 'This event already exists', $this->ecp);
	 return FALSE;
      }

      return TRUE;
   }

   protected function verify_category() {
      if ($this->verify_column('category_id') === FALSE) return FALSE;

      return TRUE;
   }

   protected function create_event() {
      $this->dbc->begin();

      if ($this->add_event() === FALSE) {
	 $this->dbc->rollback();
	 return FALSE;
      }

      /*
      if ($this->add_tag() === FALSE) {
	 $this->dbc->rollback();
	 return FALSE;
      }
       */

      $this->dbc->commit();

      return TRUE;
   }

   public function get_events() {
      if ($this->sanity_check() === FALSE) return FALSE;

      $sql = 'SELECT e."id", u."username", e."time", e."title", ed."description",
	        c."name" as city, a."name" as area
	       FROM "'.$this->event_table.'" as e
	       LEFT OUTER JOIN "'.$this->event_description_table.'" as ed
	       ON (ed."event_id" = e."id")
	       LEFT OUTER JOIN "'.$this->venue_table.'" as v ON (v."id" = ed."venue_id")
	       LEFT OUTER JOIN "'.$this->zip_table.'" as z ON (z."id" = v."zip_id")
	       LEFT OUTER JOIN "'.$this->zip_city_table.'" as zc ON (zc."zip_id" = z."id")
	       LEFT OUTER JOIN "'.$this->city_table.'" as c ON (c."id" = zc."city_id")
	       LEFT OUTER JOIN "'.$this->area_table.'" as a ON (a."id" = e."area_id")
	       LEFT OUTER JOIN "'.$this->user_table.'" as u ON (u."id" = e."user_id")
	       ORDER BY ed."date_added" DESC
	       LIMIT '.EVENT_LIST_COUNT.' ';
      $this->dbc->query($sql);
      $this->dbc->fetch_array();

      $this->events = $this->dbc->rows;

      return TRUE;
   }

   public function get_event() {
      if ($this->sanity_check() === FALSE) return FALSE;

      if ($this->verify_column('event_id') === FALSE) return FALSE;

      $this->event_id = $this->dbc->escape($this->event_id);

      $sql = 'SELECT e."id", u."username", e."time", e."title", ed."description",
	        c."name" as city, a."name" as area, v."name" as venuename, v."address" as address,
	        v."phone" as venuephone, z."zip", s."abbr" as state
	       FROM "'.$this->event_table.'" as e
	       LEFT OUTER JOIN "'.$this->event_description_table.'" as ed
	       ON (ed."event_id" = e."id")
	       LEFT OUTER JOIN "'.$this->venue_table.'" as v ON (v."id" = ed."venue_id")
	       LEFT OUTER JOIN "'.$this->zip_table.'" as z ON (z."id" = v."zip_id")
	       LEFT OUTER JOIN "'.$this->zip_city_table.'" as zc ON (zc."zip_id" = z."id")
	       LEFT OUTER JOIN "'.$this->city_table.'" as c ON (c."id" = zc."city_id")
	       LEFT OUTER JOIN "'.$this->zip_state_table.'" as zs ON (zs."zip_id" = z."id")
	       LEFT OUTER JOIN "'.$this->state_table.'" as s ON (s."id" = zs."state_id")
	       LEFT OUTER JOIN "'.$this->area_table.'" as a ON (a."id" = e."area_id")
	       LEFT OUTER JOIN "'.$this->user_table.'" as u ON (u."id" = e."user_id")
	       WHERE e."id" = \''.$this->event_id.'\'
	       ORDER BY ed."date_added" DESC
	       LIMIT 1';
      $this->dbc->query($sql);
      $this->dbc->fetch_row();

      if ($this->dbc->row_count < 1) {
	 $this->ec->create_error(23, 'No event found', $this->ecp);
	 return FALSE;
      }

      $this->events[] = $this->dbc->rows;

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

      $this->event_id = $this->dbc->last_seq;

      $insert2['event_id']    = $this->dbc->escape($this->event_id);
      $insert2['description'] = $this->dbc->escape($this->description);
      // FIXME: $insert2['phone']       = $this->dbc->escape($this->phone);
      $insert2['link_url']    = $this->dbc->escape($this->url);
      // FIXME: $inesrt2['link_title']  = $this->dbc->escape($this->link_title);
      $insert2['venue_id']    = $this->dbc->escape($this->venue_id);
      if ($this->dbc->insert_db($this->event_description_table, $insert2, TRUE) === FALSE) {
	 $this->ec->create_error(14, 'Could not add the event to the database', $this->ecp);
	 return FALSE;
      }

      return TRUE;
   }

   private function add_event_category() {
      if ($this->sanity_check() === FALSE) return FALSE;

      $insert['event_id'] = $this->dbc->escape($this->event_id);
      $insert['category_id'] = $this->dbc->escape($this->cateogry_id);
      if ($this->dbc->insert_db($this->event_category_table, $insert, TRUE) === FALSE) {
	 $this->ec->create_error(21, 'Could not add category reference', $this->ecp);
	 return FALSE;
      }

      return TRUE;
   }

   private function add_tag() {
      if ($this->sanity_check() === FALSE) return FALSE;

      for ($i = 0, $iz = count($this->tag); $i < $iz; ++$i) {
	 $this->tag[$i] = strtolower(trim($this->tag[$i]));
	 if (strlen($this->tag[$i]) < 3) continue;

	 if (($tag_id = $this->tag_exists($this->tag[$i])) === FALSE) {
	    $insert['name'] = $this->dbc->escape($this->tag[$i]);
	    if ($this->dbc->insert_db($this->tag_table, $insert) === FALSE) {
	       $this->ec->create_error(17, 'Could not create tag', $this->ecp);
	       return FALSE;
	    }

	    $tag_id = $this->dbc->last_seq;
	 }

	 $insert2['event_id'] = $this->dbc->escape($this->event_id);
	 $insert2['tag_id'] = $this->dbc->escape($tag_id);
	 if ($this->dbc->insert_db($this->event_tag_table, $insert2, TRUE) === FALSE) {
	    $this->ec->create_error(18, 'The event and tag could\'t start a relationship', $this->ecp);
	    return FALSE;
	 }
      }

      return TRUE;
   }

   private function tag_exists($tag) {
      if ($this->sanity_check() === FALSE) return FALSE;

      $tag = $this->dbc->escape($tag);

      $sql = 'SELECT "id"
	       FROM "'.$this->tag_table.'"
	       WHERE "name" = \''.$tag.'\' 
	       LIMIT 1';
      $this->dbc->query($sql);
      $this->dbc->fetch_row();
      if ($this->dbc->row_count < 1) return FALSE;

      return TRUE;
   }

   private function sanitize_tag() {
      if ( ! is_array($this->tag)) {
	 $this->tag = array($this->tag);
      }

      $this->tag = preg_replace('/[^A-Za-z0-9_\- ]/i', '', $this->tag);

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

   protected function verify_column($column) {
      if ($column === 'title') {
	 if ($this->verify_title() === FALSE) return FALSE;
      } else if ($column === 'time') {
	 if ($this->verify_timestamp() === FALSE) return FALSE;
      } else if ($column === 'url') {
	 if ($this->verify_url() === FALSE) return FALSE;
      } else if ($column === 'description') {
	 if ($this->verify_description() === FALSE) return FALSE;
      } else if ($column === 'user_id') {
	 if ($this->verify_user_id() === FALSE) return FALSE;
      } else if ($column === 'category_id') {
	 if ($this->verify_category_id() === FALSE) return FALSE;
      } else if ($column === 'event_id') {
	 if ($this->verify_event_id() === FALSE) return FALSE;
      }

      return TRUE;
   }

   private function verify_event_id() {
      if (verify_int($this->event_id) === FALSE) {
	 $this->ec->create_error(22, 'Invalid event id', $this->ecp);
	 return FALSE;
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

      $this->timestamp = strtotime($datetime);
      if ($this->timestamp < CURRENT_TIME + (60 * 30)) {
	 $this->ec->create_error(2, 'Event time can not be sooner than 30 minutes from now', $this->ecp);
	 return FALSE;
      }

      return TRUE;
   }

   protected function verify_category_id() {
      if ($this->sanity_check() === FALSE) return FALSE;

      if (verify_int($this->category_id) === FALSE || $this->category_id < 1) {
	 $this->ec->create_error(19, 'Invalid category ID', $this->ecp);
	 return FALSE;
      }

      if ($this->category_exists() === FALSE) {
	 $this->ec->create_error(20, 'Category does not exist', $this->ecp);
	 return FALSE;
      }

      return TRUE;
   }

   protected function category_exists() {
      if ($this->sanity_check() === FALSE) return FALSE;

      $this->category_id = $this->dbc->escape($this->category_id);

      $sql = 'SELECT "id"
	       FROM "'.$this->category_table.'"
	       WHERE "id" = \''.$this->category_id.'\' and "active" = \'t\' 
	       LIMIT 1';
      $this->dbc->query($sql);
      $this->dbc->fetch_row();
      if ($this->dbc->row_count < 1) return FALSE;

      return TRUE;
   }


   /* 
    * The following functions can be used as static class methods
    */

   public function event_exists($event_id=FALSE, &$db_class=FALSE) {
      if ($event_id === FALSE) {
	 $event_id = &$this->event_id;
      }

      if ($db_class === FALSE) {
	 if ($this->sanity_check() === FALSE) return FALSE;
	 $event_table = &$this->event_table;
	 $db_class = &$this->dbc;
      } else {
	 $event_table = 'event';
      }

      if (verify_int($event_id) === FALSE) {
	 return FALSE;
      }

      $event_id = $db_class->escape($event_id);

      $sql = 'SELECT "id"
	       FROM "'.$event_table.'"
	       WHERE "id" = \''.$event_id.'\' ';
      $db_class->query($sql);
      $db_class->fetch_row();
      if ($db_class->row_count < 1) return FALSE;

      return TRUE;
   }

}

?>
