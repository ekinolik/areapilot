<?php

define('PRODUCTION', TRUE);

define('PROTOCOL', 'http://');
define('DOMAIN', 'www.areapilot.com');
define('IMG_DOMAIN', 'images.areapilot.com');
define('COOKIE_DOMAIN', '.areapilot.com');
define('ROOT_URL', PROTOCOL.DOMAIN.'/');
define('IMG_URL', PROTOCOL.IMG_DOMAIN.'/');
define('ROOT_DIR', '/var/www/html/areapilot.com/');
define('IMG_DIR', '/var/www/html/images.areapilot.com/');
define('LIB_DIR', ROOT_DIR.'lib/');
define('UPLOAD_IMAGE_DIR', ROOT_DIR.'uploaded_images/');
define('UPLOAD_IMAGE_URL', ROOT_URL.'uploaded_images/');
define('UPLOAD_IMAGE_TMP_DIR', UPLOAD_IMAGE_DIR.'tmp/');
define('UPLOAD_IMAGE_TMP_URL', UPLOAD_IMAGE_URL.'tmp/');
define('UPLOAD_FORM_DIR', ROOT_DIR.'forms/');
define('UPLOAD_FORM_URL', ROOT_URL.'forms/');
$CURRENT_DIR = ROOT_DIR;

define('MAIL_RELAY', 'mail.direnetworks.com');
define('MAIL_HOSTNAME', 'areapilot.com');
define('CONTACT_EMAIL', 'eric@direnetworks.com');
define('CONTACT_NAME', 'eric');

define('SESS_RAND', 'RQwB=(Sy03');

define('DB_HOST', 'direpgsql.direnetworks.com');
define('DB_NAME', 'areapilot');
define('DB_USER', 'areapilot');
define('DB_PASS', 'o^uA]_e9&f-`**r,~;/%zof1Yv6B{a$6');
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

define('MIN_TITLE_LEN', 8);
define('MAX_TITLE_LEN', 60);
define('MIN_DESCRIPTION_LEN', 50);
define('MAX_DESCRIPTION_LEN', 5000);
define('MIN_VENUENAME_LEN', 1);
define('MAX_VENUENAME_LEN', 100);
define('MAX_COMMENT_LEN', 2000);

define('EVENT_LIST_COUNT', 5);
define('EVENT_TOP_LIST_COUNT', 5);

define('MAX_PAGES', 5);

/* Garbage Collection */
/* Delete sessions older than this many days, 0 would be anything older than 24 hours */
define('GC_MAXLIFETIME', 14);
/* Probability of a page running the garbage collection ( 1 out of N ) */
define('GC_PROBABILITY', 100);

define('ERROR_DEBUG', 0);

$TITLE = 'Area Pilot';
$JS = array('tp/jquery-1.4.2.min.js', 'tp/blockUI.js', 'tp/jquery.cookie.js', 'tp/rounded.js', 'ap_basics.js');
$CSS = array('ap_default.css');

/* Shouldn't modify below here */

define('CURRENT_TIME', time());
define('BIGINT', 9223372036854775807);
?>
