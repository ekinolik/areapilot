<?php

if ( ! isset($_SERVER['HTTP_REFERER']))
   header('Location: '.ROOT_URL);
else
   header('Location: '.$_SERVER['HTTP_REFERER']);

?>
