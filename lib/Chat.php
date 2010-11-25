<?php

define('CHATCLASS', 1);

if (! defined('ACCOUNTCLASS')) require(LIB_DIR.'Account.php');
if (! defined('EVENTCLASS'))   require(LIB_DIR.'Event.php');

class Chat {

   public $dbc;
   public $ec;

   private $ecp;
   protected $sc;

   public $comment;

   public $id;
   public $event_id;
   public $parent_id;

   public $msg;

   public $comment_table;
   public $rating_table;
   public $user_table;

   public function __construct(&$db_class, &$error_class) {
      return $this->Chat_construct($db_class, $error_class);
   }

   protected function Chat_construct(&$db_class, &$error_class) {
      $this->init();

      if (is_object($db_class)) $this->dbc = &$db_class;
      if (is_object($error_class)) $this->ec = &$error_class;

      return TRUE;
   }

   private function init() {
      $this->sc = FALSE;
      $this->ecp = 'Chat';

      $this->comment = array();
      $this->msg = NULL;
      $this->id = 0;
      $this->parent_id = 0;
      
      $this->comment_table = 'comment';
      $this->rating_table  = 'rating';
      $this->user_table    = 'user';
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

   public function add_comment() {
      if ($this->verify_column('comment') === FALSE) return FALSE;
      if ($this->verify_column('user_id') === FALSE) return FALSE;
      if ($this->verify_column('event_id') === FALSE) return FALSE;
      if ($this->verify_column('parent_id') === FALSE) return FALSE;

      return $this->create_comment(); 
   }

   protected function create_comment() {
      if ($this->sanity_check() === FALSE) return FALSE;

      $insert['user_id']  = $this->dbc->escape($this->user_id);
      $insert['event_id'] = $this->dbc->escape($this->event_id);
      $insert['parent']   = $this->dbc->escape($this->parent_id);
      $insert['comment']  = $this->dbc->escape($this->msg);
      if ($this->dbc->insert_db($this->comment_table, $insert) === FALSE) {
	 $this->ec->create_error(2, 'Could not add your comment to the database', $this->ecp);
	 return FALSE;
      }

      $this->id = $this->dbc->last_seq;

      return TRUE;
   }

   protected function verify_column($column) {
      if ($column === 'comment') {
	 if ($this->verify_comment() === FALSE) return FALSE;
      } else if ($column === 'user_id') {
	 if (Account::user_exists($this->user_id, $this->dbc) === FALSE) {
	    $this->ec->create_error(3, 'Invalid user id', $this->ecp);
	    return FALSE;
	 }
      } else if ($column === 'event_id') {
	 if (Event::event_exists($this->event_id, $this->dbc) === FALSE) {
	    $this->ec->create_error(4, 'Invalid event id', $this->ecp);
	    return FALSE;
	 }
      } else if ($column === 'parent_id') {
	 if ($this->verify_parent_id() === FALSE) return FALSE;
      }

      return TRUE;
   }

   protected function verify_comment() {
      if (strlen($this->msg ) < 1 || strlen($this->msg) > MAX_COMMENT_LEN) {
	 $this->ec->create_error(1, 'Invalid comment length', $this->ecp);
	 return FALSE;
      }

      return TRUE;
   }

   protected function verify_parent_id() {
      if ($this->parent_id === 0) return TRUE;

      if ($this->comment_exists($this->parent_id, $this->event_id) === FALSE) {
	 $this->ec->create_error(5, 'Invalid parent comment', $this->ecp);
	 return FALSE;
      }

      return TRUE;
   }

   public function get_parent_comments() {
      if ($this->sanity_check() === FALSE) return FALSE;

      $this->event_id = $this->dbc->escape($this->event_id);

      $sql = 'SELECT c."id", c."user_id", c."comment", c."time", u."username", count(c2."id") as replies
	       FROM "'.$this->comment_table.'" as c
	       LEFT OUTER JOIN "'.$this->user_table.'" as u ON (u."id" = c."user_id")
	       LEFT OUTER JOIN "'.$this->comment_table.'" as c2 ON (c."id" = c2."parent")
	       WHERE c."event_id" = \''.$this->event_id.'\' AND c."parent" IS NULL
	       GROUP BY c."id", c."user_id", c."comment", c."time", u."username"
	       ORDER BY c."time" ';
      $this->dbc->query($sql);
      $this->dbc->fetch_array();

      $this->comment = $this->dbc->rows;

      return TRUE;
   }

   public function get_comments() {
      if ($this->sanity_check() === FALSE) return FALSE;

      if (isset($this->id) && verify_int($this->id)) {
	 $this->id = $this->dbc->escape($this->id);
	 $where_clause = 'c."parent" = \''.$this->id.'\' ';
      } else if (isset($this->event_id) && verify_int($this->event_id)) {
	 $this->event_id = $this->dbc->escape($this->event_id);
	 $where_clause = 'c."event_id" = \''.$this->event_id.'\' ';
      } else {
	 $this->ec->create_error(6, 'No comment type of comments select', $this->ecp);
	 return FALSE;
      }

      $sql = 'SELECT c."id", c."user_id", c."comment", c."time", c."parent", u."username"
	       FROM "'.$this->comment_table.'" as c
	       LEFT OUTER JOIN "'.$this->user_table.'" as u ON (u."id" = c."user_id")
	       WHERE '.$where_clause.'
	       ORDER BY c."time" ';
      $this->dbc->query($sql);
      $this->dbc->fetch_array();

      $this->comment = $this->dbc->rows;

      return TRUE;
   }


   /* 
    * The following functions can be used as static class methods
    */

   public function comment_exists($comment_id=FALSE, $event_id=FALSE, &$db_class=FALSE) {
      if ($comment_id === FALSE) {
	 $comment_id = $this->comment_id;
      }

      if ($event_id === FALSE) {
	 $event_id = $this->event_id;
      }

      if ($db_class === FALSE) {
	 if ($this->sanity_check() === FALSE) return FALSE;
	 $comment_table = $this->comment_table;
	 $db_class = &$this->dbc;
      } else {
	 $comment_table = 'user';
      }

      if (verify_int($comment_id) === FALSE) {
	 return FALSE;
      }

      $comment_id = $db_class->escape($comment_id);
      $event_id   = $db_class->escape($event_id);

      $sql = 'SELECT "id"
	       FROM "'.$comment_table.'"
	       WHERE "id" = \''.$comment_id.'\' and "event_id" = \''.$event_id.'\'
	       LIMIT 1';
      $db_class->query($sql);
      $db_class->fetch_row();
      if ($db_class->row_count < 1) return FALSE;

      return TRUE;
   }

}
