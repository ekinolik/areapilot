<?php

define('ACCOUNTCLASS', 1);

class Account {

   public $dbc;
   public $ec;

   public $id;
   public $username;
   public $password;
   public $confirm;
   public $email;
   public $first_name;
   public $last_name;

   private $enc_password;

   private $sc;
   private $ecp;

   private $user_table;
   private $profile_table;

   public function __construct(&$db_class, &$error_class) {
      $this->init();

      if (is_object($db_class)) $this->dbc = &$db_class;
      if (is_object($error_class)) $this->ec = &$error_class;

      return TRUE;
   }

   public function create() {
      if ($this->verify_profile('password') === FALSE) return FALSE;
      if ($this->verify_profile('username') === FALSE) return FALSE;
      if ($this->verify_profile('email') === FALSE)    return FALSE;
      if ($this->verify_profile('name') === FALSE)     return FALSE;
      
      if ($this->get_id('username')) {
	 $this->ec->create_error(6, 'This username already exists', $this->ecp);
	 return FALSE;
      }
      
      if ($this->get_id('email')) {
	 $this->ec->create_error(11, 'This email address is already registered', $this->ecp);
	 return FALSE;
      }

      $this->dbc->begin();

      if ($this->add_user() === FALSE) {
	 $this->dbc->rollback();
	 return FALSE;
      }

      $this->dbc->commit();

      return TRUE;
   }


   public function login() {
      if ($this->verify_profile('password') === FALSE) return FALSE;
      if ($this->verify_profile('username') === FALSE) return FALSE;

      return $this->authenticate();
   }

   private function authenticate() {
      if ($this->sanity_check() === FALSE) return FALSE;

      if ($this->encrypt_password($this->username) === FALSE) return FALSE;

      $this->username = $this->dbc->escape($this->username);

      $sql = 'SELECT "id", "email", "username"
	 FROM "'.$this->user_table.'"
	 WHERE lower("username") = lower(\''.$this->username.'\') 
	 AND "password" = '.$this->enc_password.' AND "active" = \'t\' 
	 LIMIT 1 ';
      $this->dbc->query($sql);
      $this->dbc->fetch_row();
      if ($this->dbc->row_count < 1) {
	 $this->ec->create_error(14, 'Invalid username or password', $this->ecp);
	 return FALSE;
      }

      $this->id       = $this->dbc->rows['id'];
      $this->username = $this->dbc->rows['username'];
      $this->email    = $this->dbc->rows['email'];

      return TRUE;
   }
 
   private function init() {
      $this->sc = FALSE;
      $this->ecp = 'Account';

      $this->user_table = 'user';
      $this->profile_table = 'profile';

      $this->username   = NULL;
      $this->password   = NULL;
      $this->confirm    = NULL;
      $this->email      = NULL;
      $this->first_name = NULL;
      $this->last_name  = NULL;
   }

   private function sanity_check() {
      if ($this->sc !== FALSE) return TRUE;

      if (is_object($this->ec) === FALSE) return FALSE;
      if (is_object($this->dbc) === FALSE) {
	 $this->ec->create_error(1, 'Database Connection Falied', $this->ecp);
	 return FALSE;
      }

      $this->sc = TRUE;

      return TRUE;
   }

   private function verify_profile($column) {
      if ($column === 'password') {
	 if ($this->verify_password() === FALSE) return FALSE;
      } else if ($column === 'username') {
	 if ($this->verify_username() === FALSE) return FALSE;
      } else if ($column === 'email') {
	 if ($this->verify_email() === FALSE) return FALSE;
      } else if ($column === 'name') {
	 if ($this->verify_name() === FALSE) return FALSE;
      } else {
	 return FALSE;
      }

      return TRUE;
   }

   private function verify_name() {
      if (strlen($this->first_name) > MAX_FIRST_NAME_LEN) {
	 $this->ec->create_error(12, 'First name is too long, '.MAX_FIRST_NAME_LEN.' characters max', $this->ecp);
	 return FALSE;
      }

      if (strlen($this->last_name) > MAX_LAST_NAME_LEN) {
	 $this->ec->create_error(13, 'Last name is too long, '.MAX_LAST_NAME_LEN.' characters max', $this->ecp);
	 return FALSE;
      }

      return TRUE;
   }

   private function verify_password() {
      if ($this->confirm !== NULL && $this->password !== $this->confirm) {
	 $this->ec->create_error(2, 'Passwords do not match', $this->ecp);
	 return FALSE;
      }

      $this->password = trim($this->password);

      if (strlen($this->password) < MIN_PASS_LEN) {
	 $this->ec->create_error(3, 'Your password must be at least '.MIN_PASS_LEN.' characters long', $this->ecp);
	 return FALSE;
      }

      return TRUE;
   }

   private function verify_username() {
      if (strlen($this->username) < MIN_USERNAME_LEN || strlen($this->username) > MAX_USERNAME_LEN){
	 $this->ec->create_error(4, 'Your username must be between '.MIN_USERNAME_LEN.' and '.MAX_USERNAME_LEN.' characters long', $this->ecp);
	 return FALSE;
      }

      if (preg_match(NOT_ALLOWED_USERNAME_REGEX, $this->username)) {
	 $this->ec->create_error(5, 'You may only use alpha-numeric charaters in your username', $this->ecp);
	 return FALSE;
      }

      return TRUE;
   }

   private function verify_email() {
      if (verify_email($this->email) === FALSE) {
	 $this->ec->create_error(7, 'Invalid email address', $this->ecp);
	 return FALSE;
      }

      return TRUE;
   }

   private function get_id($column) {
      if ($this->sanity_check() === FALSE) return FALSE;

      if ($column === 'username') {
	 $username = $this->dbc->escape($this->username);
	 $where = ' lower("username") = lower(\''.$username.'\') ';
      } elseif ($column === 'email') {
	 $email = $this->dbc->escape($this->email);
	 $where = ' lower("email") = lower(\''.$email.'\') ';
      } else {
	 $where = ' 1=1 ';
      }

      $sql = 'SELECT "id" 
	       FROM "'.$this->user_table.'" 
	       WHERE '.$where.'
	       LIMIT 1';
      $this->dbc->query($sql);
      $this->dbc->fetch_row();
      if ($this->dbc->row_count === 0) return FALSE;

      return $this->dbc->rows['id'];
   }

   private function add_user() {
      if ($this->sanity_check() === FALSE) return FALSE;

      if ($this->encrypt_password($this->username) === FALSE)
	 return FALSE;

      $insert['username'] = $this->dbc->escape($this->username);
      $insert['email']    = $this->dbc->escape($this->email);
      $insert['password']   = '__SQL_FUNCTION__' . $this->enc_password;
      if ($this->dbc->insert_db($this->user_table, $insert) === FALSE) {
	 $this->ec->create_error(9, 'Could not insert the user into the database', $this->ecp);
	 return FALSE;
      }

      $insert2['user_id'] = $this->dbc->last_seq;
      $insert2['first_name'] = $this->dbc->escape($this->first_name);
      $insert2['last_name']  = $this->dbc->escape($this->last_name);
      if ($this->dbc->insert_db($this->profile_table, $insert2, TRUE) === FALSE) {
	 $this->ec->create_error(10, 'Could not insert the profile into the database', $this->ecp);
	 return FALSE;
      }

      return TRUE;
   }

   private function encrypt_password($key) {
      if (($this->enc_password = $this->dbc->encrypt($this->password, strtolower($key).SESS_RAND, 'aes')) === FALSE) {
	    $this->ec->create_error(8, 'Could not encrypt password', $this->ecp);
	    return FALSE;
	 }

      return TRUE;
   }

}

?>
