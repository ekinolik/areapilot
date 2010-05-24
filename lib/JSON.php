<?php

define('JSON_CLASS', 1);

abstract class JSON {

   public $json_string;
   public $json;

   public function __construct($json_string='') {
      if (strlen($json) < 1) return TRUE;

      $this->json_string = $json_string;
      $this->decode();

      return TRUE;
   }

   public function decode() {
      /* Decode a json string to an object*/
      if (strlen($this->json_string) < 1) return FALSE;

      $this->json = json_decode($this->json_string);

      if (is_object($this->json) === FALSE) return FALSE;

      return TRUE;
   }

   public function get_from_file($file) {
      /* Load the specified JSON file then decode it returning the result of the decode */
      if (strlen(trim($file)) < 1 || ! is_readable($file)) return FALSE;

      $this->json_string = file_get_contents($file);

      return $this->decode();
   }

   public function encode() {
      /* Encode a array to a json string */
      if (is_object($this->json)) return FALSE;

      $this->json_string = json_encode($this->json);

      if (strlen(trim($this->json_string)) < 1) return FALSE;

      return TRUE;
   }

}

?>
