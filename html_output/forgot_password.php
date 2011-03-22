<?php

if ( ! defined('HTMLCLASS')) require(LIB_DIR.'HTML.php');

$header = HTML::body_header('Reset My Password');
$footer = HTML::body_footer();

if ($success === TRUE) {
   $span = HTML::message('A confirmation message has been emailed to you.  Please follow the instructions in your email to reset your password');
   $form = '';
} else {
   $span = '';
   $form = HTML::forgot_password_form("fullform", $error_class);
}

print <<<EOF
$header
$span
$form
$footer
EOF;
?>
