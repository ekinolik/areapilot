<?php

if ( ! defined('HTMLCLASS')) require(LIB_DIR.'HTML.php');

$header = HTML::body_header('Change My Password');
$footer = HTML::body_footer();
$form = HTML::change_password_form("fullform", $error_class);

print <<<EOF
$header
$form
$footer
EOF;
?>
