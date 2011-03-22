<?php

if ( ! defined('HTMLCLASS')) require(LIB_DIR.'HTML.php');
if ( ! defined('ACCOUNTCLASS')) require(LIB_DIR.'Account.php');

$header = HTML::body_header('Profile');
$footer = HTML::body_footer();

if (isset($_COOKIE['rp']) && $_COOKIE['rp'] === '1') {
   $error_class->create_error(1, 'Your password has been reset.  Be sure to change your password to a password you can remember', 'HTML');
}

$account = Account::get_account_details($session->user_id, $db_class);
$details = HTML::profile_details($account);
$profile = HTML::profile($details, $error_class);

print <<<EOF
$header
$profile
$footer
EOF;

?>
