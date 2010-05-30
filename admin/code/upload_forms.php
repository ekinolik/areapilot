<?php

if ( ! defined('PAGE')) require(LIB_DIR.'Page.php');

$page_c = new Page($db_class, $error_class);
$page_c->get_all(PAGE_FORM_INT);

?>
