<?php

define('PROTOCOL', 'http://');
define('DOMAIN', 'wh.direnetworks.com');
define('IMG_DOMAIN', 'images.direnetwors.com');
define('COOKIE_DOMAIN', 'wh.direnetworks.com');
define('ROOT_URL', PROTOCOL.DOMAIN.'/');
define('IMG_URL', PROTOCOL.IMG_DOMAIN.'/');
define('ROOT_DIR', '/var/www/html/wh.direnetworks.com/');
define('IMG_DIR', '/var/www/html/images.direnetworks.com/');
define('LIB_DIR', ROOT_DIR.'lib/');
define('UPLOAD_IMAGE_DIR', ROOT_DIR.'uploaded_images/');
define('UPLOAD_IMAGE_URL', ROOT_URL.'uploaded_images/');
define('UPLOAD_IMAGE_TMP_DIR', UPLOAD_IMAGE_DIR.'tmp/');
define('UPLOAD_IMAGE_TMP_URL', UPLOAD_IMAGE_URL.'tmp/');
define('UPLOAD_FORM_DIR', ROOT_DIR.'forms/');
define('UPLOAD_FORM_URL', ROOT_URL.'forms/');
$CURRENT_DIR = ROOT_DIR;

define('MAIL_RELAY', '192.168.1.87');
define('MAIL_HOSTNAME', 'wh.direnetworks.com');
define('CONTACT_EMAIL', 'eric@direnetworks.com');
define('CONTACT_NAME', 'eric');

define('SESS_RAND', 853);

define('DB_HOST', '192.168.1.74');
define('DB_NAME', 'wh');
define('DB_USER', 'wh');
define('DB_PASS', 'wh!p@$$w0rD');
define('DB_TYPE', 'pgsql');
define('SESS_DB_HOST', DB_HOST);
define('SESS_DB_NAME', DB_NAME);
define('SESS_DB_USER', DB_USER);
define('SESS_DB_PASS', DB_PASS);
define('SESS_DB_TYPE', DB_TYPE);
define('TS_LANGUAGE', 'english');

define('MIN_USERNAME_LEN', 3);
define('MAX_USERNAME_LEN', 32);
define('MIN_PASS_LEN', 4);
define('NOT_ALLOWED_USERNAME_REGEX', '[^A-Za-z0-9_\-]');
define('MAX_FIRST_NAME_LEN', 32);
define('MAX_LAST_NAME_LEN', 32);
define('THUMBNAIL_WIDTH', 64);
define('THUMBNAIL_HEIGHT', 64);

define('MIN_TITLE_LEN', 10);
define('MAX_TITLE_LEN', 65);
define('MIN_DESCRIPTION_LEN', 50);
define('MAX_DESCRIPTION_LEN', 5000);

/* Garbage Collection */
/* Delete sessions older than this many days, 0 would be anything older than 24 hours */
define('GC_MAXLIFETIME', 14);
/* Probability of a page running the garbage collection ( 1 out of N ) */
define('GC_PROBABILITY', 100);

define('ERROR_DEBUG', 1);

$JS = array('jquery-1.4.2.min.js', 'jquery.cookie.js');
$CSS = array('default.css');

/* Shouldn't modify below here */

define('CURRENT_TIME', time());
?>
