<?php

$document = file_get_contents(DOC_DIR.'about.html');
$start_pos = strpos($document, '<body>')+7;
$end_pos   = strrpos($document, '</body>');
$document = substr($document, $start_pos, $end_pos - $start_pos);

?>
