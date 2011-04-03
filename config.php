<?php

define('CONFIG', 1);
if ( ! defined('CONFIG_SETTINGS'))	require('config_settings.php');

define('ROOT_URL', PROTOCOL.DOMAIN.'/');
define('SROOT_URL', SPROTOCOL.DOMAIN.'/');
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') 
   define('CURRENT_ROOT', SROOT_URL);
else
   define('CURRENT_ROOT', ROOT_URL);
define('IMG_URL', PROTOCOL.IMG_DOMAIN.'/');
define('LIB_DIR', ROOT_DIR.'lib/');
define('UPLOAD_IMAGE_DIR', ROOT_DIR.'uploaded_images/');
define('UPLOAD_IMAGE_URL', ROOT_URL.'uploaded_images/');
define('UPLOAD_IMAGE_TMP_DIR', UPLOAD_IMAGE_DIR.'tmp/');
define('UPLOAD_IMAGE_TMP_URL', UPLOAD_IMAGE_URL.'tmp/');
define('UPLOAD_FORM_DIR', ROOT_DIR.'forms/');
define('UPLOAD_FORM_URL', ROOT_URL.'forms/');
define('DOC_DIR', ROOT_DIR.'docs/');
$CURRENT_DIR = ROOT_DIR;

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
define('MAX_TITLE_LEN', 90);
define('MAX_URI_TITLE_LEN', 60);
define('MIN_DESCRIPTION_LEN', 50);
define('MAX_DESCRIPTION_LEN', 5000);
define('MIN_VENUENAME_LEN', 1);
define('MAX_VENUENAME_LEN', 100);
define('MAX_COMMENT_LEN', 2000);
define('RESET_PASSWORD_CODE_LEN', 16);

define('EVENT_LIST_COUNT', 5);
define('EVENT_TOP_LIST_COUNT', 5);

define('MAX_PAGES', 5);

/* Garbage Collection */
/* Delete sessions older than this many days, 0 would be anything older than 24 hours */
define('GC_MAXLIFETIME', 14);
/* Probability of a page running the garbage collection ( 1 out of N ) */
define('GC_PROBABILITY', 100);

$TITLE = 'AreaPilot';
$JS = array('https://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js', 'tp/blockUI.js', 'tp/jquery.cookie.min.js', 'tp/jquery.corner.js', 'ap_basics.js');
$CSS = array('ap_default.min.css');

/* Shouldn't modify below here */

define('CURRENT_TIME', time());
define('BIGINT', 9223372036854775807);

$fb_meta = array();
?>
