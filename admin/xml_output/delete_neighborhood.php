<?php

$error = $error_class->error;
if (isset($_POST['hood_id'])) $hood_id = $_POST['hood_id'];

print <<<EOF

<status>$status</status>
<error>$error</error>

<id>$hood_id</id>

EOF;

?>
