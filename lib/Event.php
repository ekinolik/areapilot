<?php

define('EVENTCLASS', 1);

if ( ! defined('LOCATIONCLASS')) require(LIB_DIR.'Location.php');

class Event extends Location {

   public $dbc;
   public $ec;

   public $events;
   public $total;

   public $event_id;
   public $title;
   public $uri_title;
   public $date;
   public $time;
   public $timestamp;
   public $url;
   public $description;

   public $start_time;
   public $end_time;

   public $category;
   public $category_id;

   public $venue_id;
   public $user_id;
   public $comment_id;

   public $rating;

   public $event_table;
   public $event_description_table;
   public $tag_table;
   public $event_tag_table;
   public $venue_table;
   public $category_table;
   public $event_category_table;
   public $rating_table;

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
      $this->uri_title   = NULL;
      $this->description = NULL;
      $this->category    = NULL;
      $this->rating      = NULL;
      $this->comment_id  = NULL;
      $this->start_time  = NULL;
      $this->end_time    = NULL;

      $this->total = 0;

      $this->event_table = 'event';
      $this->event_description_table = 'event_description';
      $this->tag_table = 'tag';
      $this->event_tag_table = 'event_tag';
      $this->venue_table = 'venue';
      $this->category_table = 'category';
      $this->event_category_table = 'event_category';

      // FIXME: Move these to their own class
      $this->user_table = 'user';
      $this->rating_table = 'rating';

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

   public function get_categories() {
      if (verify_int($this->event_id) === FALSE) {
	 $this->ec->create_error(6, 'Invalid event ID', $this->ecp);
	 return FALSE;
      }

      $this->event_id = $this->dbc->escape($this->event_id);

      $sql = 'SELECT c."id", c."title"
	       FROM "'.$this->event_category_table.'" as ec
	       LEFT OUTER JOIN "'.$this->category_table.'" as c ON (ec."category_id" = c."id")
	       WHERE ec."event_id" = \''.$this->event_id.'\' AND c."active" = \'t\' ';
      $this->dbc->query($sql);
      $this->dbc->fetch_array();

      $this->category = $this->dbc->rows;

      return TRUE;
   }

   protected function verify_event() {
      if ($this->verify_column('title') === FALSE)       return FALSE;
      if ($this->verify_column('time') === FALSE)        return FALSE;
      //if ($this->verify_column('url') === FALSE)         return FALSE;
      if ($this->verify_column('description') === FALSE) return FALSE;

      $this->create_uri_title();
      //if ($this->sanitize_tag() === FALSE) return FALSE;

      if ($this->exists()) {
	 $this->ec->create_error(12, 'This event already exists', $this->ecp);
	 return FALSE;
      }

      return TRUE;
   }

   protected function create_uri_title() {
      $date = date("mdy", $this->timestamp);
      $safe_title = preg_replace("/[^a-z0-9i ]/i", '', strtolower(trim($this->title)));
      $safe_title = preg_replace("/ +/", '_', $safe_title);

      $this->uri_title = $date.'/'.$safe_title;

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

   protected function event_query() {
      $rating_query = $this->create_rating_query();

      //$date_clause = $this->create_date_clause();
      $date_clause = $this->create_date_clause($this->start_time, $this->end_time);
      $category_clause = $this->create_category_clause(CATEGORY_ID);

      $sql = '
	       FROM ( '.$rating_query.' ) as r
	       LEFT OUTER JOIN "'.$this->event_table.'" as e ON (e."id" = r."event_id")
	       LEFT OUTER JOIN "'.$this->event_category_table.'" as ec ON (ec."event_id" = e."id")
	       LEFT OUTER JOIN "'.$this->category_table.'" as cat 
	       ON (cat."id" = ec."category_id" )
	       LEFT OUTER JOIN "'.$this->event_description_table.'" as ed
	       ON (ed."event_id" = e."id")
	       LEFT OUTER JOIN "'.$this->venue_table.'" as v ON (v."id" = ed."venue_id")
	       LEFT OUTER JOIN "'.$this->zip_table.'" as z ON (z."id" = v."zip_id")
	       LEFT OUTER JOIN "'.$this->zip_city_table.'" as zc ON (zc."zip_id" = z."id")
	       LEFT OUTER JOIN "'.$this->city_table.'" as c ON (c."id" = zc."city_id")
	       LEFT OUTER JOIN "'.$this->area_table.'" as a ON (a."id" = e."area_id")
	       LEFT OUTER JOIN "'.$this->user_table.'" as u ON (u."id" = e."user_id")
	       WHERE '.$category_clause.' AND '.$date_clause.' ';

      return $sql;
   }

   public function get_events() {
      if ($this->sanity_check() === FALSE) return FALSE;

      $this->get_event_count();

      $offset = $this->create_offset(PAGE);
      $sql = 'SELECT e."id", u."username", e."time", e."title", e."uri_title", ed."description",
	        c."name" as city, a."name" as area, r."rating", cat."title" as category,
	        v."name" as venue
	       '.$this->event_query().'
	       ORDER BY r."rating" DESC, ed."date_added" DESC
	       LIMIT '.EVENT_LIST_COUNT.' 
	       '.$offset.' ';
      $this->dbc->query($sql);
      $this->dbc->fetch_array();

      $this->events = $this->dbc->rows;

      return TRUE;
   }

   protected function get_event_count() {
      if ($this->sanity_check() === FALSE) return FALSE;

      $sql = 'SELECT count(1) as count '.$this->event_query().' ';
      $this->dbc->query($sql);
      $this->dbc->fetch_row();
      if ($this->dbc->row_count < 1) {
	 $this->total = 0;
	 return FALSE;
      }

      $this->total = $this->dbc->rows['count'];

      return TRUE;
   }

   public function get_top($category=FALSE) {
      /* We allow category to be passed in in case CATEGORY constants could
       * not be set.  This happens on the event page because there was no
       * category in the URL.
       * We may also want to get a top list of categories from a category we
       * currently aren't in */

      if ($this->sanity_check() === FALSE) return FALSE;

      $rating_query = $this->create_rating_query();

      //$date_clause = $this->create_date_clause(CURRENT_TIME, CURRENT_TIME + 86400);
      $date_clause = $this->create_date_clause($this->start_time, $this->end_time);

      if ($category === FALSE || verify_int($category) === FALSE)
	 $category_clause = $this->create_category_clause(CATEGORY_ID);
      else
	 $category_clause = $this->create_category_clause($category);

      $sql = 'SELECT e."id", u."username", e."time", e."title", e."uri_title", ed."description",
	        c."name" as city, a."name" as area, r."rating", cat."title" as category
	       FROM ( '.$rating_query.' ) as r
	       LEFT OUTER JOIN "'.$this->event_table.'" as e ON (e."id" = r."event_id")
	       LEFT OUTER JOIN "'.$this->event_category_table.'" as ec ON (ec."event_id" = e."id")
	       LEFT OUTER JOIN "'.$this->category_table.'" as cat 
	       ON (cat."id" = ec."category_id" )
	       LEFT OUTER JOIN "'.$this->event_description_table.'" as ed
	       ON (ed."event_id" = e."id")
	       LEFT OUTER JOIN "'.$this->venue_table.'" as v ON (v."id" = ed."venue_id")
	       LEFT OUTER JOIN "'.$this->zip_table.'" as z ON (z."id" = v."zip_id")
	       LEFT OUTER JOIN "'.$this->zip_city_table.'" as zc ON (zc."zip_id" = z."id")
	       LEFT OUTER JOIN "'.$this->city_table.'" as c ON (c."id" = zc."city_id")
	       LEFT OUTER JOIN "'.$this->area_table.'" as a ON (a."id" = e."area_id")
	       LEFT OUTER JOIN "'.$this->user_table.'" as u ON (u."id" = e."user_id")
	       WHERE '.$category_clause.' AND '.$date_clause.'
	       ORDER BY r."rating" DESC, ed."date_added" DESC
	       LIMIT '.EVENT_TOP_LIST_COUNT.' ';
      $this->dbc->query($sql);
      $this->dbc->fetch_array();

      return $this->dbc->rows;
   }

   private function create_date_clause($start_ts=CURRENT_TIME, $end_ts=FALSE) {
      if (verify_int($start_ts) === FALSE) $start_ts = CURRENT_TIME;

      $start = $this->dbc->escape(date('Y-m-d H:i:s', $start_ts));

      $sql = ' ( e."time" > \''.$start.'\' ';
      if (verify_int($end_ts) !== FALSE && $end_ts > $start_ts) {
	 $end = $this->dbc->escape(date('Y-m-d H:i:s', $end_ts));
	 $sql .= ' AND e."time" <= \''.$end.'\' ';
      }
      $sql .= ' ) ';

      return $sql;
   }

   private function create_category_clause($category_id) {
      if (verify_int($category_id) && $category_id > 0) {
	 $category_id = $this->dbc->escape($category_id);
	 $sql  = ' ( ec."category_id" = \''.$category_id.'\' ';
	 $sql .= ' OR cat."parent" = \''.$category_id.'\'  ) ';
      } else {
	 $sql = ' 1=1 ';
      }

      return $sql;
   }

   private function create_offset($page) {
      if (verify_int($page) && $page > 0 && ($page * EVENT_LIST_COUNT) < BIGINT) {
	 $sql = ' OFFSET '.(($page - 1) * EVENT_LIST_COUNT);
      } else {
	 $sql = ' ';
      }

      return $sql;
   }

   private function sanitize_uri_title() {
      $this->uri_title = trim($this->uri_title);

      while (substr($this->uri_title, 0, 1) === '/') {
	 $this->uri_title = substr($this->uri_title, 1);
      }

      while (substr($this->uri_title, -1) === '/') {
	 $this->uri_title = substr($this->uri_title, 0, -1);
      }

      return TRUE;
   }

   protected function event_clause($table='') {
      if (strlen($table) > 0 && substr($table, -1) !== '.') $table .= '.';

      $this->sanitize_uri_title();

      if ($this->verify_column('uri_title')) {
	 $this->uri_title = $this->dbc->escape($this->uri_title);
	 return ' '.$table.'"uri_title" = \''.$this->uri_title.'\' ';
      } else if ($this->verify_column('event_id')) {
	 $this->event_id = $this->dbc->escape($this->event_id);
	 return ' '.$table.'"id" = \''.$this->event_id.'\' ';
      } else {
	 return FALSE;
      }

   }

   public function get_event() {
      if ($this->sanity_check() === FALSE) return FALSE;

      if (($clause = $this->event_clause('e.')) === FALSE) return FALSE;

      $rating_query = $this->create_rating_query();
      $sql = 'SELECT e."id", u."username", e."time", e."title", e."uri_title", ed."description",
	        c."name" as city, a."name" as area, v."name" as venuename, v."address" as address,
	        v."phone" as venuephone, z."zip", s."abbr" as state, r."rating"
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
	       LEFT OUTER JOIN ( '.$rating_query.' ) as r ON (r."event_id" = e."id")
	       WHERE '.$clause.'
	       ORDER BY ed."date_added" DESC
	       LIMIT 1';
      $this->dbc->query($sql);
      $this->dbc->fetch_row();

      if ($this->dbc->row_count < 1) {
	 $this->ec->create_error(23, 'No event found', $this->ecp);
	 return FALSE;
      }

      $this->event_id = $this->dbc->rows['id'];
      $this->events[] = $this->dbc->rows;

      return TRUE;
   }

   public function get_attendance() {

      $where_clause = '';
      for ($i = 0, $iz = count($this->events); $i < $iz; ++$i) {
	 if ($i > 0) $where_clause .= ' OR ';

	 $id = &$this->events[$i]['id'];
	 $where_clause .= ' "event_id" = \''.$this->dbc->escape($id).'\' ';
      }
      if (strlen($where_clause) < 1) $where_clause = ' 1=0 ';

      $sql = 'SELECT "event_id", sum(value) as attendance 
	       FROM "'.$this->rating_table.'" as r
	       WHERE "value" = \'2\' AND ( '.$where_clause.' )
	       GROUP BY "event_id" ';
      $this->dbc->query($sql);
      $this->dbc->fetch_array();
      
      for ($i = 0, $iz = count($this->events); $i < $iz; ++$i) {
	 $this->events[$i]['attendance'] = 0;

	 for ($j = 0; $j < $this->dbc->row_count; ++$j) {
	    if ($this->events[$i]['id'] === $this->dbc->rows[$j]['event_id']) {
	       $this->events[$i]['attendance'] = floor($this->dbc->rows[$j]['attendance'] / 2);
	    }
	 }
      }

      return TRUE;
   }

   private function add_event() {
      if ($this->sanity_check() === FALSE) return FALSE;

      $datetime = date("Y-m-d H:i:00", $this->timestamp);

      $insert['user_id']   = $this->dbc->escape($this->user_id);
      $insert['title']     = $this->dbc->escape($this->title);
      $insert['time']      = $this->dbc->escape($datetime);
      $insert['area_id']   = $this->dbc->escape($this->area_id);
      $insert['uri_title'] = $this->dbc->escape($this->uri_title);
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

      if ($this->set_event_category() === FALSE) return FALSE;

      $this->rating = 1;
      return $this->set_rating('event');

   }

   protected function set_event_category() {
      if ($this->sanity_check() === FALSE) return FALSE;
      
      $insert['event_id']    = $this->dbc->escape($this->event_id);
      $insert['category_id'] = $this->dbc->escape($this->category_id);
      if ($this->dbc->insert_db($this->event_category_table, $insert, TRUE) === FALSE) {
	 $this->ec->create_error(28, 'Could not associate a category with this event', $this->ecp);
	 return FALSE;
      }

      return TRUE;
   }

   protected function set_rating($type) {
      if ($this->sanity_check() === FALSE) return FALSE;

      if ($this->verify_column('rating') === FALSE) return FALSE;

      if ($type === 'event') {
	 $insert['event_id'] = $this->dbc->escape($this->event_id);
      } else if ($type === 'comment') {
	 $insert['comment_id'] = $this->dbc->escape($this->comment_id);
      } else {
	 $this->ec->create_error(25, 'Invalid rating type', $this->ecp);
	 return FALSE;
      }

      $insert['user_id'] = $this->dbc->escape($this->user_id);
      $insert['value']   = $this->dbc->escape($this->rating);
      if ($this->dbc->insert_db($this->rating_table, $insert, TRUE) === FALSE) {
	 $this->ec->create_error(26, 'Could not add rating to the database', $this->ecp);
	 return FALSE;
      }

      return TRUE;
   }

   private function add_event_category() {
      if ($this->sanity_check() === FALSE) return FALSE;

      $insert['event_id'] = $this->dbc->escape($this->event_id);
      $insert['category_id'] = $this->dbc->escape($this->category_id);
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

      // This should check if the title exists on that date already

      $uri_title = $this->dbc->escape($this->uri_title);

      $sql = 'SELECT id
	       FROM "'.$this->event_table.'"
	       WHERE uri_title = \''.$uri_title.'\' 
	       LIMIT 1';
      $this->dbc->query($sql);
      $this->dbc->fetch_row();
      if ($this->dbc->row_count > 0) return TRUE;

      return FALSE;
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
      } else if ($column === 'rating') {
	 if ($this->verify_rating() === FALSE) return FALSE;
      } else if ($column === 'uri_title') {
	 if ($this->verify_uri_title() === FALSE) return FALSE;
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

   private function verify_rating() {
      if (verify_int($this->rating) === FALSE || $this->rating < -1 || $this->rating > 2) {
	 $this->ec->create_error(24, 'Invalid rating value', $this->ecp);
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

   private function verify_uri_title() {
      /* URI must be at least 7 + MIN_TITLE_LEN characters long,
       * and no more than 7 + MAX_URI_TITLE_LEN
       * 6 for the date, 1 for the slash */

      $t_len = strlen(trim($this->uri_title));
      if ($t_len < (7 + MIN_TITLE_LEN) || $t_len > (7 + MAX_URI_TITLE_LEN)) {
	 $this->ec->create_error(27, 'Invalid url title', $this->ecp);
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

   private function create_rating_query($event_id=FALSE) {
      if (verify_int($event_id)) {
	 $where_clause = ' "event_id" = \''.$this->dbc->escape($event_id).'\' ';
      } else {
	 $where_clause = ' "event_id" IS NOT NULL ';
      }

      $sql = 'SELECT "event_id", sum("value") as rating
	       FROM "'.$this->rating_table.'" as r
	       WHERE '.$where_clause.'
	       GROUP BY "event_id"
	       ORDER BY rating DESC ';
      return $sql;
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

   public function vote($type, $user_id, $id, $rating, &$db_class=FALSE) {
      if ($db_class === FALSE) {
	 if ($this->sanity_check() === FALSE) return FALSE;
	 $rating_table = &$this->rating_table;
	 $db_class = &$this->dbc;
      } else {
	 $rating_table = 'rating';
      }

      if (verify_int($rating) === FALSE || $rating < -1 || $rating > 2) return FALSE;
      if (verify_int($user_id) === FALSE || $user_id === 0) return FALSE;
      if (verify_int($id) === FALSE || $id == 0) return FALSE;

      if ($type === 'e') {
	 /* Event */
	 $insert['user_id']    = $db_class->escape($user_id);
	 $insert['event_id']   = $db_class->escape($id);
	 $insert['value']      = $db_class->escape($rating);
	 $insert['comment_id'] = 0;
      } else if ($type === 'c' && $rating !== 2) {
	 /* Comment */
	 $insert['user_id']    = $db_class->escape($user_id);
	 $insert['event_id']   = 0;
	 $insert['value']      = $db_class->escape($rating);
	 $insert['comment_id'] = 0;
      } else {
	 /* WTF? */
	 return FALSE;
      }

      if ($db_class->insert_db($rating_table, $insert) === FALSE) return FALSE;

      return TRUE;
   }


   public function rating($type, $id, &$db_class=FALSE) {
      if ($db_class === FALSE) {
	 if ($this->sanity_check() === FALSE) return FALSE;
	 $rating_table = &$this->rating_table;
	 $db_class = &$this->dbc;
      } else {
	 $rating_table = 'rating';
      }

      if (verify_int($id) === FALSE || $id == 0) return FALSE;

      if ($type === 'e') {
	 $where = 'WHERE "event_id" = \''.$db_class->escape($id).'\' ';
      } else if ($type === 'c') {
	 $where = 'WHERE "comment_id" = \''.$db_class->escape($id).'\' ';
      } else {
	 return FALSE;
      }

      $sql = 'SELECT sum("value") as rating
	       FROM "'.$rating_table.'"
	       '.$where;
      $db_class->query($sql);
      $db_class->fetch_row();
      if ($db_class->row_count < 1) return 0;

      return $db_class->rows['rating'];
   }

}

?>
