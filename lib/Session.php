<?php

define('SESSION', 1);

if ( ! defined('MISC'))     require(LIB_DIR.'Misc.php');
if ( ! defined('DATABASECLASS')) require(LIB_DIR.'Database.php');

class Session extends Database {

   public $ec;

   private $sc;
   private $ecp;

   public $user_id;
   public $sess_id;
   public $keep_alive;

   public $session_table;
   
   public function __construct(&$error_class) {
      $this->Database_construct();

      $this->sc = FALSE;
      $this->ecp = 'Session';

      $this->session_table = 'session';

      if ( ! defined('SESS_RAND')) define('SESS_RAND', 1);

      if (is_object($error_class)) $this->ec = &$error_class;
      if ($this->create_db_connection() === FALSE) {
	 return FALSE;
      }

      return TRUE;
   }

   public function sanity_check() {
      if ($this->sc !== FALSE) return TRUE;

      if (is_object($this->ec) === FALSE) return FALSE;

      return TRUE;
   }

   private function create_db_connection() {
      $this->sql = SESS_DB_TYPE;
      $this->dbg = 1;

      if (($this->connect_to_db(SESS_DB_NAME, SESS_DB_USER, SESS_DB_PASS, SESS_DB_HOST)) === FALSE){
	 $this->ec->create_error(1, 'Could not connect to the database', 'init');
	 return FALSE;
      }

      return TRUE;
   }


   public function create() {
      if ($this->sanity_check() === FALSE) return FALSE;

      $this->sess_id = md5($_SERVER['REMOTE_ADDR'].$this->user_id.SESS_RAND);

      /* Delete any existing sessions from this address */
      $this->delete();

      //$where['sess_id'] = $this->dbc->escape($this->sess_id);
      //$this->dbc->delete_db($this->session_table, $where);

      /* Create a new session */
      $insert['user_id'] = $this->escape($this->user_id);
      $insert['sess_id'] = $this->escape($this->sess_id);
      if ($this->keep_alive === TRUE) $insert['keep_alive'] = 't';
      else $insert['keep_alive'] = 'f';

      if ($this->insert_db($this->session_table, $insert) === FALSE) {
	 $this->ec->create_error(2, 'Could not create sessions', $this->ecp);
	 return FALSE;
      }

      if ($this->keep_alive === TRUE) $expire = time()+(60*60*24*GC_MAXLIFETIME);
      else $expire = 0;

      setcookie('id', $this->user_id, $expire, '/', COOKIE_DOMAIN);

      return TRUE;
   }

   public function verify() {
      if ($this->sanity_check() === FALSE) return FALSE;

      $this->sess_id = md5($_SERVER['REMOTE_ADDR'].$this->user_id.SESS_RAND);

      $this->user_id = $this->escape($this->user_id);
      $this->sess_id = $this->escape($this->sess_id);

      $sql = 'SELECT "last"
	       FROM "'.$this->session_table.'"
	       WHERE "user_id" = \''.$this->user_id.'\' AND "sess_id" = \''.$this->sess_id.'\' 
	       LIMIT 1';
      $this->query($sql);
      $this->fetch_row();
      if ($this->row_count < 1) {
	 $this->ec->create_error(3, 'Invalid session', $this->ecp);
	 return FALSE;
      }

      $this->update(strtotime($this->rows['last']));

      return TRUE;
   }

   public function update($time) {
      if ($this->sanity_check() === FALSE) return FALSE;

      $curtime = time();
      /* Stop any sessions that are more than a day old */
      if ($time < ($curtime - (60 * 60 * 24))) return FALSE;

      $update['last'] = '__SQL_FUNCTION__ now()';
      $where['user_id'] = $this->escape($this->user_id);
      $where['sess_id'] = $this->escape($this->sess_id);
      $this->update_db($this->session_table, $update, $where);

      return TRUE;
   }

   public function get_last($user_id) {
      if ($this->sanity_check() === FALSE) return FALSE;

      $user_id = $this->escape($user_id);

      $sql = 'SELECT "last"
	       FROM "'.$this->session_table.'"
	       WHERE "user_id" = \''.$user_id.'\' 
	       ORDER BY "last" DESC
	       LIMIT 1';
      $this->query($sql);
      $this->fetch_row();
      if ($this->row_count < 1) {
	 return 0;
      }

      return strtotime($this->rows['last']);
   }

   public function should_run() {
      if ( ! isset($_COOKIE['id']) || verify_int($_COOKIE['id']) === FALSE) return FALSE;
      $this->user_id = $_COOKIE['id'];
      return TRUE;
   }

   public function delete() {
      if ($this->sanity_check() === FALSE) return FALSE;

      $where['sess_id'] = $this->escape($this->sess_id);
      $this->delete_db($this->session_table, $where);

      return TRUE;
   }

   public function gc_delete() {
      if ($this->sanity_check() === FALSE) return FALSE;

      $sql = 'DELETE FROM "'.$this->session_table.'" 
	       WHERE ( "last" < ( CURRENT_DATE - INTEGER \''.GC_MAXLIFETIME.'\' ) 
	              AND "keep_alive" = \'t\' )
	             OR
		     ( "last" < ( CURRENT_DATE - INTEGER \'1\' )
		       AND "keep_alive" = \'f\' ) ';
      $this->query($sql);
      return TRUE;
   }

   public function gc() {
      if ( rand(1, GC_PROBABILITY) !== 1) return FALSE;
      return $this->gc_delete();
   }
}
