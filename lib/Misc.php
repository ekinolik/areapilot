<?php

define('MISC', 1);

function verify_int($number) {
   if (is_numeric($number) && strpos($number, '.') === FALSE) return TRUE;

   return FALSE;
}

function verify_email($address) {
      return ereg("[A-Za-z0-9._+-]+@[A-Za-z0-9.-]+\.[A-Za-z]+", $address);
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

function array_from_md_element($array, $key) {
   if ( ! is_array($array)) return FALSE;

   $new = array();
   while((list($idx, $value) = each($array)) !== FALSE) {
      if ( ! array_key_exists($key, $array[$idx])) continue;
      $new[] = $array[$idx][$key];
   }

   return $new;
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

function set_category($cat_array) {
   if ( ! is_array($cat_array)) {
      define('CATEGORY_ID', '');
      define('CATEGORY_TITLE', '');
      define('CATEGORY_PARENT', '');
      define('CATEGORY_ACTIVE', '');
      define('CATEGORY_SEQUENCE', '');
      define('CATEGORY_PARENT_TITLE', '');

      return FALSE;
   }

   define('CATEGORY_ID', $cat_array['id']);
   define('CATEGORY_TITLE', $cat_array['title']);
   define('CATEGORY_PARENT', $cat_array['parent']);
   define('CATEGORY_ACTIVE', $cat_array['active']);
   define('CATEGORY_SEQUENCE', $cat_array['sequence']);
   define('CATEGORY_PARENT_TITLE', $cat_array['parent_title']);

   return TRUE;
}

function set_page($page) {
   if ( verify_int($page) === FALSE || $page <= 1) {
      define('PAGE', 1);
   } else { 
      define('PAGE', $page);
   }

   return TRUE;
}

function set_date_range($start_date, $end_date=FALSE) {
   if (verify_int($start_date) && $start_date > 0) {
      $start_year  = substr($start_date, 0, 4);
      $start_month = substr($start_date, 4, 2);
      $start_day   = substr($start_date, 6, 2);
      define('DATE_START', $start_date);
      define('TIME_START', mktime(0, 0, 0, $start_month, $start_day, $start_year));
   } else {
      /* Set start time to current time */
      define('DATE_START', date("Ymd", CURRENT_TIME));
      define('TIME_START', CURRENT_TIME);
   }

   if (verify_int($end_date) && $end_date > 0) {
      $end_year  = substr($end_date, 0, 4);
      $end_month = substr($end_date, 4, 2);
      $end_day   = substr($end_date, 6, 2);
      define('DATE_END', $end_date);
      define('TIME_END', mktime(0, 0, 0, $end_month, $end_day, $end_year) + 86399);
   } else {
      if (verify_int($start_date) && $start_date > 0) {
	 define('DATE_END', date("Ymd", TIME_START));
      } else {
	 define('DATE_END', get_last_day_of_week(CURRENT_TIME));
      }
      define('TIME_END', mktime(23, 59, 59, substr(DATE_END,4,2), substr(DATE_END,6,2), substr(DATE_END, 0, 4)));
   }

   return TRUE;
}

function get_timestamp_from_datestamp($date) {
   $year  = substr($date, 0, 4);
   $month = substr($date, 4, 2);
   $day   = substr($date, 6, 2);
   
   return mktime(0, 0, 0, $month, $day, $year);
}

function get_start_of_day($date) {
   return mktime(0, 0, 0, substr($date, 4, 2), substr($date, 6, 2), substr($date, 0, 4));
}

function get_current_day_of_week($timestamp) {
   return date("Ymd", $timestamp);
}

function get_first_day_of_week($timestamp, $diff=0) {
   $dow = date("N", $timestamp);
   if ($dow === '7') $dow = '0';

   $diff = 86400 * ($diff * 7);
   $timestamp += $diff;

   return date("Ymd", $timestamp - (86400 * $dow));
}

function get_last_day_of_week($timestamp, $diff=0) {
   $dow = date("N", $timestamp);
   if ($dow === '7') $dow = '0';
   $dow = 6 - $dow;

   $diff = 86400 * ($diff * 7);
   $timestamp += $diff;

   return date("Ymd", $timestamp + (86400 * $dow));
}

function get_first_day_of_month($date, $diff=0) {
   $year  = substr($date, 0, 4);
   $month = substr($date, 4, 2);
   $day   = substr($date, 6, 2);

   $month = $month + $diff;
   while ($month > 12 || $month < 1) {
      if ($month > 12) {
	 ++$year;
	 $month -= 12;
      } else {
	 --$year;
	 $month += 12;
      }
   }

   return sprintf("%04d%02d01", $year, $month);
}

function get_last_day_of_month($date, $diff=0) {
   $year  = substr($date, 0, 4);
   $month = substr($date, 4, 2);
   $day   = substr($date, 6, 2);

   $month = $month + $diff;

   return date("Ymd", mktime(0, 0, 0, $month+1, 0, $year));
}

function time_convert_24_to_12($time) {
   $hour = substr($time, 0, 2);
   if ($hour > 12) {
      $hour = $hour - 12;
      $ampm = ' PM';
   } else if ($hour > 0 && $hour <= 12) {
      $ampm = ' AM';
   } else if ($hour == 0) {
      $hour = 12;
      $ampm = ' AM';
   } else {
      $ampm = '';
   }

   return $hour.substr($time, 2).$ampm;
}

function time_age($datetime, $convert=7, $beautify_date=TRUE) {
   $year  = substr($datetime, 0, 4);
   $month = substr($datetime, 5, 2);
   $day   = substr($datetime, 8, 2);
   $hour  = substr($datetime, 11, 2);
   $min   = substr($datetime, 14, 2);
   $sec   = substr($datetime, 17, 2);

   $time = mktime($hour, $min, $sec, $month, $day, $year);

   $diff = CURRENT_TIME - $time;
   if (($diff / 86400) > $convert) {
      if ($beautify_date === TRUE)
	 $ago = date("M d, Y", strtotime($datetime));
      else
	 $ago = $year.'-'.$month.'-'.$day;
   } else if ($diff > 86400) {
      $ago = floor($diff / 60 / 60 / 24) . ' day(s) ago';
   } else if ($diff > 3600) {
      $hours = floor($diff / 60 / 60);
      $min = floor(($diff - ($hours * 60 * 60)) / 60);
      $ago = $hours .' hour(s) '.$min.' minute(s) ago';
   } else {
      $ago = floor($diff / 60) . ' minute(s) ago';
   }

   return $ago;
}

function char_replace($haystack, $needle, $repl) {
   $pos = strpos($haystack, $needle);
   return char_all_replace($haystack, $needle, $repl, $pos);
}

function char_rreplace($haystack, $needle, $repl) {
   $pos = strrpos($haystack, $needle);
   return char_all_replace($haystack, $needle, $repl, $pos);
}

function char_all_replace($haystack, $needle, $repl, $pos) {
   $begin = substr($haystack, 0, $pos);
   $end = substr($haystack, $pos + strlen($needle));

   return $begin.$repl.$end;
}

function pbkdf2($pass, $salt, $c, $key_length) {
   if (strlen($salt) < 8) {
      return FALSE;
   }

   $hash_length = strlen(hash('sha512', ' ', TRUE));
   $parts = ceil($key_length / $hash_length);
   $key = '';

   for ($i = 0; $i < $parts; ++$i) {
      $ib = $b = hash_hmac('sha512', $salt . pack('N', $i), $pass, true);

      for ($j = 1; $j < $c; ++$j) {
	 $ib ^= ($b = hash_hmac('sha512', $b, $pass, true));
      }

      $key .= $ib;
   }

   return substr($key, 0, $key_length);
}

function is_spam_bot(&$req, $array) {
   while ((list($key, $value) = each($array)) !== FALSE) {
      if ( ! isset($req[$key]) || $req[$key] !== $value) return TRUE;
   }

   return FALSE;
}

function add_elipsis($str, $max_len) {
   /* This finds the last space in a string before $max_len and replaces
    * everything after it with elipsis */

   $max_elipsis_len = $max_len - 3;
   if (strlen($str) === $max_len) {
      $pos = strrpos($str, ' ');
      if ($pos === FALSE || $pos > $max_elipsis_len) 
	 $pos = $max_elipsis_len;
      $str = substr($str, 0, $pos).'...';
   }

   return $str;
}
?>
