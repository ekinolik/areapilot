<?php

if ( ! defined('DATABASECLASS'))   require(LIB_DIR.'Database.php');
if ( ! defined('ERRORCLASS'))      require(LIB_DIR.'ErrorClass.php');
if ( ! defined('SESSION'))         require(LIB_DIR.'Session.php');
if ( ! defined('HTMLCLASS'))       require(LIB_DIR.'HTML.php');
if ( ! defined('CATEGORYCLASS'))   require(LIB_DIR.'Category.php');

$error_class = new ErrorClass;
if (ERROR_DEBUG > 0) $error_class->show_errno = TRUE; else $error_class->show_errno = FALSE;

/*
 * Initialize database
 */

$db_class = new Database;
$db_class->sql = DB_TYPE;
$db_class->dbg = 1;

if ( ($db_class->connect_to_db(DB_NAME, DB_USER, DB_PASS, DB_HOST)) === FALSE) {
   define('LOGGED_IN', FALSE);
   $error_class->create_error(1, 'Could not connect to the database', 'init');
   return FALSE;
}

/*
 * Process current category
 */

$current_category = FALSE;
if (isset($_GET['category'])) {
   $current_category = Category::get_by_title($_GET['category'], $db_class);
}

set_category($current_category);
unset($current_category);

/*
 * Process date range
 */

if (isset($_GET['date1'])) $start_date = $_GET['date1']; else $start_date = FALSE;
if (isset($_GET['date2'])) $end_date = $_GET['date2']; else $end_date = FALSE;

set_date_range($start_date, $end_date);
unset($start_date, $end_date);

/*
 * Process current page
 */

$current_page = FALSE;
if (isset($_GET['page'])) {
   $current_page = $_GET['page'];
}

set_page($current_page);
unset($current_page);

/*
 * Set up current session
 */

$session = new Session($error_class);
$session->gc();
if ($session->should_run() === FALSE) {
   define('LOGGED_IN', FALSE);
   return TRUE;
}

if ($session->verify() === FALSE) {
   define('LOGGED_IN', FALSE);
   return TRUE;
}

define('LOGGED_IN', TRUE);

?>
