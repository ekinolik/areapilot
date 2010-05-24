<?php

define('MCRYPT', 1);

function mcrypt_enc($data, $key, $iv) {
   $td = mcrypt_module_open('rijndael-256', '', 'ofb', '');
   $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), $iv);
   $ks = mcrypt_enc_get_key_size($td);
   $key = substr(md5($key), 0, $ks);
   mcrypt_generic_init($td, $key, $iv);

   $encrypted = mcrypt_generic($td, $data);
   mcrypt_generic_deinit($td);
   mcrypt_module_close($td);

   return $encrypted;
}

function mcrypt_dec($data, $key, $iv) {
   $td = mcrypt_module_open('rijndael-256', '', 'ofb', '');
   $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), $iv);
   $ks = mcrypt_enc_get_key_size($td);
   $key = substr(md5($key), 0, $ks);
   mcrypt_generic_init($td, $key, $iv);

   $decrypted = mdecrypt_generic($td, $data);
   mcrypt_generic_deinit($td);
   mcrypt_module_close($td);

   return $decrypted;
}

?>
