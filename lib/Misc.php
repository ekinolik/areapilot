<?php

define('MISC', 1);

function verify_int($number) {
   if (is_numeric($number) && strpos($number, '.') === FALSE) return TRUE;

   return FALSE;
}

function verify_email($address) {
      return ereg("[A-Za-z0-9._-]+@[A-Za-z0-9.-]+\.[A-Za-z]+", $address);
}

function verify_date($date, $format='mm/dd/yyyy') {
   if ($format === 'mm/dd/yyyy') {
      $da = explode('/', $date);
      if ( ! verify_int_array($da) || $da[0] < 1 || $da[0] > 12 || $da[1] < 1 || $da[1] > 31) {
	 return FALSE;
      }
   }

   return TRUE;
}

function remove_md_array($array, $col) {
   if ( ! is_array($array)) return array();

   for ($i = 0, $iz = count($array); $i < $iz; ++$i) {
      $newarray[$i] = $array[$i][$col];
   }

   return $newarray;
}

function array_append(&$array1, $array2) {
   for ($i = 0, $iz = count($array2); $i < $iz; ++$i) 
      $array1[] = $array2[$i];
}

function reindex_array($array) {
   if ( ! is_array($array)) return array($array);

   $newarray = array();
   while ((list($key, $value) = each($array)) !== FALSE) {
      $newarray[] = $value;
   }

   return $newarray;
}

function remove_dupe_md_array($array, $col, $reindex=TRUE) {
   $new_array = array();
   for ($i = 0, $iz = count($array); $i < $iz; ++$i) {
      $val = $array[$i][$col];
      if (strlen(trim($val)) < 1) continue;
      for ($j = 0, $jz = count($new_array); $j < $jz; ++$j) {
	 if ($new_array[$j][$col] == $val) {
	    $new_array[$j] = $array[$i];
	    continue 2;
	 }
      }

      $new_array[] = $array[$i];
   }

   //reindex_array($new_array);

   return $new_array;
}

function create_random_string($len, $start=48, $end=122) {
   /*
    * 48 - 57  = 0 - 9
    * 58 - 64  = :;<=>?@
    * 65 - 90  = A-Z
    * 91 - 96  = [\]^_`
    * 97 - 122 = a-z
    */

   $string = '';
   for ($i = 0; $i < $len; ++$i) {
      $ascii = rand($start, $end);
      $string .= chr($ascii);
   }

   return $string;
}

function usort_name($a, $b) {
   return strcmp(strtolower($a['name']), strtolower($b['name']));
}

function verify_int_array($array) {
   while((list($key, $value) = each($array)) !== FALSE) {
      if (verify_int($value) === FALSE) return FALSE;
   }

   return TRUE;
}

function convert_links($string) {
   return preg_replace(";(https?://)([-\w\d\.]+)+(:\d+)?(/([^\s]*)?)?;i", '<a href="$1$2$3$4" rel="nofollow">$1$2$3$4</a>', $string);
}
?>
