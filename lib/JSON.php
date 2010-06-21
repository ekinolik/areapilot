<?php

define('JSONCLASS', 1);

class JSON {

   public function decode($json_string) {
      /* Decode a json string to an object*/
      if (strlen($json_string) < 1) return FALSE;

      $json = json_decode($json_string);

      if (is_object($json) === FALSE) return FALSE;

      return $json;
   }

   public function get_from_file($file) {
      /* Load the specified JSON file then decode it returning the result of the decode */
      if (strlen(trim($file)) < 1 || ! is_readable($file)) return FALSE;

      $json_string = file_get_contents($file);

      return JSON::decode($json_string);
   }

   public function encode($json) {
      /* Encode a array to a json string */
      if (is_object($json)) return FALSE;
      if ( ! isset($json['error'])) $json['error'] = '';

      $json_string = json_encode($json);

      if (strlen(trim($json_string)) < 1) return FALSE;

      return $json_string;
   }

}

?>
