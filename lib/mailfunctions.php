<?php

#####						   #####
# This code creates a connection to the Mail eXchanger #
# of someones email address.  After connecting it will #
# send an email to the email address.		       #
# 						       #
# This code was created and owned by,		       #
#		Eric Kinolik, Dire Networks 	       #
#####						   #####

define('mailfunctions', 1);

$last_output = '';

function create_smtp_headers($subject, $from, $to, $full_from_name='',
							 $full_to_name='', $content='text/plain', $content_body='text/plain') {
	$date = date("r");

        /* 
	 * If attachments exist, create mime headers, otherwise leave mime
	 * variables blank to be ignored when creating the message headers
	 */
	 
	if ($content == 'multipart/mixed') {
	   global $boundary;
	   
	   $MIME = TRUE;
	   if ($boundary == '') $boundary = create_boundary();
	   $content = 'multipart/mixed;'.' boundary="'.$boundary.'"';

	   $mime_header  = 'This is a multi-part message in MIME format.'."\n";
	   $mime_header .= '--'.$boundary."\n";
	   $mime_header .= 'Content-Type: '.$content_body.'; charset=ISO-8859-1; format=flowed'."\n";
	   $mime_header .= 'Content-Transfer-Encoding: 7bit'."\n";
	   $mime_header .= "\n";
	   $mime_version = 'MIME-Version: 1.0';
	} else {
	   $mime_header  = '';
	   $mime_version = '';
	}

        /*
         * Make this look less like spam
         */
        if (trim($full_from_name) == '') $full_from_name = $from;

        /*
	 * Create message headers, blank variables will be ignored
	 */
	$header = fold_headers('From: "' . $full_from_name . '" <' . $from . '>');
	$header .= fold_headers('To: "' . $full_to_name . '" <' . $to . '>');
	$header .= fold_headers('Date: ' . $date);
	$header .= fold_headers('Subject: ' . $subject);
	$header .= fold_headers($mime_version);
	$header .= fold_headers('Content-Type: ' . $content);
	$header .= "\n";
	$header .= $mime_header;

	return $header;
}


function send_smtp($HOSTNAME, $from, $to, $data, $empty1='', $empty2='') {

global $last_output;

$to = str_replace("\n", "", $to);
$to = str_replace("\r", '', $to);

#This section sends the email to the server.

if (!(ereg("@", $to))) {
	$last_output .= "invalid email address\n";
	return FALSE;
}

$todomain = split("@", $to);
$domain = str_replace("\r\n", '', $todomain[1]);
$domain = str_replace('>', '', $domain);
$domain = str_replace('<', '', $domain);
$to = '<' . $to . '>';
$from = '<' . $from . '>';
$data = str_replace("\n", "\r\n", $data);
unset ($todomain);

$fp = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
$mxcount = 0;
do {
	if (getmxrr($domain, $mxrecord)) {
		foreach ($mxrecord as $record) {
			if ($record == "") {continue;}
			$mx_addrs = array();
			$mx_addrs = gethostbynamel($record);
			foreach ($mx_addrs as $mx_addr) {
			        if(! ping($record) ) {$noping = 1;}
				$result = @socket_connect($fp, $record, 25);
				if (!$result && $noping == 1) {$mxcount++; continue;} else {$gotone = 1; break 2;}
			}
		}
	} else {
		$last_output .= "bad mail server";
		return FALSE;
	}
} while(!isset($gotone) && $mxcount < 10);

if (!isset($gotone)) {
   $last_output .= "Could not establish connection";
   return FALSE;
}

unset ($domain, $record, $mxrecord, $mx_addrs, $mx_addr);

while ($out = socket_read($fp, 1024)) {
	if (strstr($out, "\n")) {break;}
}
unset ($out);

$in = "HELO $HOSTNAME\r\n";
$result = socket_write($fp, $in, strlen($in));
if (!(socketreadline($fp, "250", "HELO"))) 
	return FALSE;

$in = rtrim("MAIL FROM: $from") . "\r\n";
$result = socket_write($fp, $in, strlen($in));

if (!(socketreadline($fp, "250", "MAIL FROM"))) 
	return FALSE;

$in = rtrim("RCPT TO: $to") . "\r\n";
$result = socket_write($fp, $in, strlen($in));

$exist = socketreadline($fp, "250", "RCPT TO");
if ($exist == -1) 
	return FALSE;
elseif (!$exist)
	return FALSE;


$in = "DATA\r\n";
$result = socket_write($fp, $in, strlen($in));

if (!(socketreadline($fp, "354", "DATA WAIT"))) 
	return FALSE;

$in = "$data\r\n.\r\n";
$result = socket_write($fp, $in, strlen($in));

if (!(socketreadline($fp, "250", "DATA WRITE"))) 
	return FALSE;

$in = "QUIT\r\n";
$result = socket_write($fp, $in, strlen($in));

if (!(socketreadline($fp, "221", "QUIT"))) 
	return FALSE;

socket_close($fp);
unset ($in, $result, $fp, $to);
return 1;
}


function send_smtp_relay($HOSTNAME, $from, $to, $data, $mail_server='127.0.0.1', $mail_server_port='25') {

global $last_output;

$to = str_replace("\n", "", $to);
$to = str_replace("\r", '', $to);

#This section sends the email to the server.

if (!(ereg("@", $to))) {
	$last_output .= "invalid email address\n";
	return FALSE;
}

$to = '<' . $to . '>';
$from = '<' . $from . '>';
$data = str_replace("\n", "\r\n", $data);

$fp = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
$mxcount = 0;

if (ip2long($mail_server) === -1) {
   if (($ip_addrs = gethostbynamel($mail_server)) === FALSE) {
      $last_output = 'Could not resolve domain';
      return FALSE;
   }
} else {
   $ip_addrs[] = $mail_server;
}

$no_connect = 0;
foreach ($ip_addrs as $ip_addr) {
   if (! ping($ip_addr) ) { $no_connect = 1; continue; }
   $no_connect = 0;
   $result = socket_connect($fp, $ip_addr, $mail_server_port);
   if ($result === FALSE) {$no_connect = 1; continue; }
   $no_connect = 0;
}

if ($no_connect === 1) {
   $last_output = 'Could not establish connection';
   return FALSE;
}

unset ($ip_addrs, $ip_addr, $no_connect);

while ($out = socket_read($fp, 1024)) {
	if (strstr($out, "\n")) {break;}
}
unset ($out);

$in = "HELO $HOSTNAME\r\n";
$result = socket_write($fp, $in, strlen($in));
if (!(socketreadline($fp, "250", "HELO"))) 
	return FALSE;

$in = rtrim("MAIL FROM: $from") . "\r\n";
$result = socket_write($fp, $in, strlen($in));

if (!(socketreadline($fp, "250", "MAIL FROM"))) 
	return FALSE;

$in = rtrim("RCPT TO: $to") . "\r\n";
$result = socket_write($fp, $in, strlen($in));

$exist = socketreadline($fp, "250", "RCPT TO");
if ($exist == -1) 
	return FALSE;
elseif (!$exist)
	return FALSE;


$in = "DATA\r\n";
$result = socket_write($fp, $in, strlen($in));

if (!(socketreadline($fp, "354", "DATA WAIT"))) 
	return FALSE;

$in = "$data\r\n.\r\n";
$result = socket_write($fp, $in, strlen($in));

if (!(socketreadline($fp, "250", "DATA WRITE"))) 
	return FALSE;

$in = "QUIT\r\n";
$result = socket_write($fp, $in, strlen($in));

if (!(socketreadline($fp, "221", "QUIT"))) 
	return FALSE;

socket_close($fp);
unset ($in, $result, $fp, $to);
return 1;
}

function socketreadline($fp, $code, $type) {
# I forgot what this function is for?
	while ($out = socket_read($fp, 1024)) {
		if (strstr($out, "\n")) {break;}
	}
	#echo $out. "\n";

	if (($temp = substr($out, 0, 3)) != "$code") {
		$in = "QUIT\r\n";
		$result = socket_write($fp, $in, strlen($in));
		socket_close($fp);
		global $last_output;
		if ($temp == '550') {
			$last_output .= $out;
			return -1;
		} else {
			$last_output .= $out;
			return FALSE;
		}
	}
	unset ($out, $fp, $code, $type, $temp);
	return 1;
}

function add_to_email_queue($from, $to, $data, $queue_dir) {
	$time = str_replace(' ', '_', microtime());
	if (substr($queue_dir, -1) == '/')
		$full_path = $queue_dir . $time;
	else
		$full_path = $queue_dir . '/' . $time;
	
	for ($i = 0; $i < 100; $i++) {
		if(!(file_exists($full_path . ".$i"))) {
			$full_path .= ".$i";
			break;
		}
	}
	
	$input = $from . "\n" . $to . "\n\n" . $data;

	$fh = fopen($full_path, 'w');
	fwrite($fh, $input, strlen($input));
	fclose($fh);

}

function fold_headers($string) {
	/* This function folds headers so that they are compliant with
	 * rfc 822 (http://www.faqs.org/rfcs/rfc822.html)
	 * NOTE: if there are over 64 concurrent characters without a
	 * space, this will put that set of characters up until the first
	 * space on its own line.
	 */

        if ($string == '') return "";

	$newstring = '';

	$string = trim($string);
	$words = explode(' ', $string);
	
	/* Loop for each word, add each word 1 by 1 to current phrase
	 * if the current phrase is longer than 64 char's, add the 
	 * current phrase - current word to the string on its own line. */
	for ($pos = 0; $pos < count($words); $pos++) {
		if ($pos == 0) {
			$cur_phrase = $words[$pos];
			continue;
		}
		
		$combined = $cur_phrase . ' ' . $words[$pos];
		if (strlen($combined) > 64) {
			$newstring .= $cur_phrase . "\n";
			$cur_phrase = ' ' . $words[$pos];
		} else {
			$cur_phrase = $combined;
		}
	}

	return $newstring . $cur_phrase . "\n";	
}

function create_attachment($file, $name, $open_file=1) {
   global $last_output, $boundary;
   
   /* Get contents of the file */
   if ($open_file == 1) {
      if ( ! is_readable($file)) {
	 $last_output = 'fopen: Could not process attachment';
	 return FALSE;
      }
      $fh = fopen($file, 'r');
      $contents = fread($fh, filesize($file));
      fclose($fh);
   } else {
      $contents = $file;
   }

   if ($boundary == '') create_boundary();

   /* Create attachment */
   $attachment  = "\n\n--".$boundary."\n";
   $attachment .= 'Content-Type: application/octet-stream;'."\n".' name="'.$name.'"'."\n";
   $attachment .= 'Content-Transfer-Encoding: base64'."\n";
   $attachment .= 'Content-Disposition: attachment;'."\n".' filename="'.$name.'"'."\n\n";
   $attachment .= chunk_split(base64_encode($contents), 72);
   $attachment .= $boundary."--";

   return $attachment;
}


function create_boundary() {
   /*
    * Create a random boundary delimiter for multipart messages
    */

   global $boundary;
   if ($boundary != '') return FALSE;
   
   $bound_rand = '';
   for ($i = 0; $i < 24; $i++) {
      $bound_rand .= rand(0,9);
   }
   $boundary = '--------------'.$bound_rand;

   return TRUE;
}


function ping($address) {
   $str = exec("/bin/ping -c 1 -w 1 $address");
   if (strlen($str) > 1) {
      return TRUE;
   } else {
      return FALSE;
   }
}
?>
