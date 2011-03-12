<?php

if ( ! defined('HTMLCLASS')) require(LIB_DIR.'HTML.php');
if ( ! defined('ACCOUNTCLASS')) require(LIB_DIR.'Account.php');

$header = HTML::body_header('Profile');
$footer = HTML::body_footer();

$account = Account::get_account_details($session->user_id, $db_class);
$details = HTML::profile_details($account);
$profile = HTML::profile($details);

print <<<EOF
$header
$profile
$footer
EOF;

?>
