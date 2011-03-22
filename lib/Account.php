<?php

define('ACCOUNTCLASS', 1);
if ( ! defined('MISC'))		include(LIB_DIR.'Misc.php');
if ( ! defined('mailfunctions'))	include(LIB_DIR.'mailfunctions.php');

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

      $sql = 'SELECT "id", "email", "username", "reset_code"
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

      if (strlen($this->dbc->rows['reset_code']) > 0) {
	 $update['reset_code'] = 0;
	 $where['id'] = $this->dbc->escape($this->id);
	 if ($this->dbc->update_db($this->user_table, $update, $where) === FALSE) {
	    $this->ec->create_error(24, 'Unable to remove reset code', $this->ecp);
	    return FALSE;
	 }
      }

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
	 $this->ec->create_error(2, 'Passwords do not match '.$this->password.'-'.$this->confirm, $this->ecp);
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

   public function change_password() {
      if ($this->sanity_check() === FALSE) return FALSE;

      if ($this->verify_profile('password') === FALSE) return FALSE;
      if ($this->verify_profile('username') === FALSE) return FALSE;

      if ($this->encrypt_password($this->username) === FALSE)
	 return FALSE;

      $update['password'] = '__SQL_FUNCTION__' . $this->enc_password;
      $where['id'] = $this->dbc->escape($this->id);
      if ($this->dbc->update_db($this->user_table, $update, $where) === FALSE) {
	 $this->ec->create_error(15, 'Could not update password', $this->ecp);
	 return FALSE;
      }

      return TRUE;
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

      $this->id = $insert2['user_id'];;
      return TRUE;
   }

   private function encrypt_password($key) {

      $hash = base64_encode(pbkdf2($this->password, SESS_RAND, 1000, 256));

      if (($this->enc_password = $this->dbc->encrypt($hash, strtolower($key).SESS_RAND, 'aes')) === FALSE) {
	    $this->ec->create_error(8, 'Could not encrypt password', $this->ecp);
	    return FALSE;
	 }

      return TRUE;
   }

   private function add_reset_code($code) {
      if ($this->sanity_check() === FALSE) return FALSE;

      if (strlen($code) != 32) {
	 $this->ec->create_error(18, 'Invalid reset code length', $this->ecp);
	 return FALSE;
      }

      if (verify_int($this->id) === FALSE) {
	 $this->ec->create_error(19, 'Invalid user id', $this->ecp);
	 return FALSE;
      }

      $update['reset_code'] = $this->dbc->escape($code);
      $where['id'] = $this->dbc->escape($this->id);
      if ($this->dbc->update_db($this->user_table, $update, $where)  === FALSE) {
	 $this->ec->create_error(20, 'Could not update table with reset code', $this->ecp);
	 return FALSE;
      }

      return TRUE;
   }

   public function forgot_password() {
      if ($this->sanity_check() === FALSE) return FALSE;

      if ($this->get_by_username_email() === FALSE) {
	 $this->ec->create_error(16, 'Invalid username or email', $this->ecp);
	 return FALSE;
      }

      $pass_code = md5(create_random_string(RESET_PASSWORD_CODE_LEN, 48, 122));

      if ($this->add_reset_code($pass_code) === FALSE) {
	 return FALSE;
      }

      $subject = 'AreaPilot - Password reset confirmation';
      $headers = create_smtp_headers($subject, 'noreply@areapilot.com', $this->username, 'AreaPilot', $this->first_name.' '.$this->last_name);
      $data = 'Someone is trying to reset the password to your account on AreaPilot.com'."\n";
      $data .= 'If this was you, please follow the link below (or cut and paste it into your web browser) to continue resetting your password'."\n";
      $data .= SROOT_URL.'reset_password/'.$pass_code."\n\n\n";
      $data .= 'Email: '.$this->email."\n";
      $data .= 'IP: '.$_SERVER['REMOTE_ADDR']."\n";

      $data = $headers . $data;

      if (send_smtp_relay(MAIL_HOSTNAME, 'noreply@areapilot.com', $this->email, $data, MAIL_RELAY) === FALSE) {
	 $this->ec->create_error(17, 'There was an error sending an email to your address.  Please try again.', $this->ecp);
	 return FALSE;
      }

      return TRUE;
   }

   public function reset_password($reset_code) {
      if ($this->sanity_check() === FALSE) return FALSE;

      if ($this->get_by_reset_code($reset_code) === FALSE) {
	 return FALSE;
      }

      $this->password = md5(create_random_string(RESET_PASSWORD_CODE_LEN, 48, 122));
      $this->encrypt_password($this->username);

      $update['password']   = '__SQL_FUNCTION__'.$this->enc_password;
      $update['reset_code'] = 0;
      $where['id'] = $this->dbc->escape($this->id);
      if ($this->dbc->update_db($this->user_table, $update, $where) === FALSE) {
	 $this->ec->create_error(23, 'Could not update password', $this->ecp);
	 return FALSE;
      }

      return TRUE;
   }

   private function get_by_reset_code($reset_code) {
      if ($this->sanity_check() === FALSE) return FALSE;

      if (strlen($reset_code) != 32) {
	 $this->ec->create_error(21, 'Invalid reset code', $this->ec->ecp);
	 return FALSE;
      }

      $reset_code = $this->dbc->escape($reset_code);
      $sql = 'SELECT u."id", u."username", u."email", 
	        p."first_name", p."last_name", p."area_id"
	       FROM "'.$this->user_table.'" as u
	       LEFT OUTER JOIN "'.$this->profile_table.'" as p 
		ON (u."id" = p."user_id")
	       WHERE u."reset_code" = \''.$reset_code.'\' 
	       LIMIT 1';
      $this->dbc->query($sql);
      $this->dbc->fetch_row();
      if ($this->dbc->row_count < 1) {
	 $this->ec->create_error(22, 'Invalid reset code', $this->ecp);
	 return FALSE;
      }

      $this->populate_self_from_array($this->dbc->rows);
      return TRUE;
   }

   public function get_by_username_email() {
      if ($this->sanity_check() === FALSE) return FALSE;

      if ($this->verify_username() === FALSE) return FALSE;
      if ($this->verify_email() === FALSE) return FALSE;

      $username = $this->dbc->escape($this->username);
      $email    = $this->dbc->escape($this->email);
      $sql = 'SELECT u."id", u."username", u."email", 
	        p."first_name", p."last_name", p."area_id"
	       FROM "'.$this->user_table.'" as u
	       LEFT OUTER JOIN "'.$this->profile_table.'" as p 
		ON (u."id" = p."user_id")
	       WHERE u."username" = \''.$username.'\' 
	        AND u."email" = \''.$email.'\'
	       LIMIT 1';
      $this->dbc->query($sql);
      $this->dbc->fetch_row();
      if ($this->dbc->row_count < 1) return FALSE;

      $this->populate_self_from_array($this->dbc->rows);
      return TRUE;
   }

   private function populate_self_from_array($arr) {
      if ( ! is_array($arr)) return FALSE;

      if (isset($arr['id']))         $this->id = $arr['id'];
      if (isset($arr['username']))   $this->username = $arr['username'];
      if (isset($arr['email']))      $this->email = $arr['email'];
      if (isset($arr['first_name'])) $this->first_name = $arr['first_name'];
      if (isset($arr['last_name']))  $this->first_name = $arr['last_name'];

      return TRUE;
   }


   /* 
    * The following functions can be used as static class methods
    */

   public function user_exists($user_id=FALSE, &$db_class=FALSE) {
      if ($user_id === FALSE) {
	 $user_id = $this->id;
      }

      if ($db_class === FALSE) {
	 if ($this->sanity_check() === FALSE) return FALSE;
	 $user_table = $this->user_table;
	 $db_class = &$this->dbc;
      } else {
	 $user_table = 'user';
      }

      if (verify_int($user_id) === FALSE) {
	 return FALSE;
      }

      $user_id = $db_class->escape($user_id);

      $sql = 'SELECT "id"
	       FROM "'.$user_table.'"
	       WHERE "id" = \''.$user_id.'\' ';
      $db_class->query($sql);
      $db_class->fetch_row();
      if ($db_class->row_count < 1) return FALSE;

      return TRUE;
   }

   public function get_username($user_id=FALSE, &$db_class=FALSE) {
      if ($user_id === FALSE) {
	 $user_id = $this->id;
      }

      if ($db_class === FALSE) {
	 if ($this->sanity_check() === FALSE) return FALSE;
	 $user_table = $this->user_table;
	 $db_class = &$this->dbc;
      } else {
	 $user_table = 'user';
      }

      if (verify_int($user_id) === FALSE) {
	 return FALSE;
      }

      $user_id = $db_class->escape($user_id);

      $sql = 'SELECT "username"
	       FROM "'.$user_table.'"
	       WHERE "id" = \''.$user_id.'\' ';
      $db_class->query($sql);
      $db_class->fetch_row();
      if ($db_class->row_count < 1) return FALSE;

      return $db_class->rows['username'];
   }

   public function get_account_details($user_id=FALSE, &$db_class=FALSE) {
      if ($user_id === FALSE) {
	 $user_id = $this->id;
      }

      if ($db_class === FALSE) {
	 if ($this->sanity_check() === FALSE) return FALSE;
	 $user_table = $this->user_table;
	 $profile_table = $this->profile_table;
	 $db_class = &$this->dbc;
      } else {
	 $user_table = 'user';
	 $profile_table = 'profile';
      }

      if (verify_int($user_id) === FALSE) {
	 return FALSE;
      }

      $user_id = $db_class->escape($user_id);

      $sql = 'SELECT u."id", u."username", u."email", 
	        p."first_name", p."last_name", p."area_id"
	       FROM "'.$user_table.'" as u
	       LEFT OUTER JOIN "'.$profile_table.'" as p 
		ON (u."id" = p."user_id")
	       WHERE u."id" = \''.$user_id.'\' ';
      $db_class->query($sql);
      $db_class->fetch_row();
      if ($db_class->row_count < 1) return FALSE;

      return $db_class->rows;
   }

}

?>
