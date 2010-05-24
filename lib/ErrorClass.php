<?php

define('ERRORCLASS', '1');

class ErrorClass {
   public $error;
   public $errno;
   public $all_errors;
   public $errtype;

   public $show_errno;

   public function __construct() {
      $this->reset();
      $this->show_errno = TRUE;

      return TRUE;
   }

   public function reset() {
      $this->all_errors = '';
      $this->error = '';
      $this->errno = 0;
      $this->errtype = '';

      return TRUE;
   }

   public function create_error($errno, $string, $err_prefix) {
      $this->errno = $errno;
      $this->error = '';
      $this->errtype = $err_prefix;
      if ($this->show_errno === TRUE) $this->error = $err_prefix.' Error #'.$errno.': ';
      $this->error .= $string."<br />\n";
      $this->all_errors .= $this->error;
      return TRUE;
   }

   public function has_error() {
      if (strlen($this->error) > 0) 
         return TRUE; 
      else 
         return FALSE;
   }

   public function __tostring() {
      if(ERROR_DEBUG == 1) {
	 return $this->all_errors;
      }

      return $this->error;
   }

}

?>
