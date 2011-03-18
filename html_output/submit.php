<?php

if ( ! defined('HTMLCLASS')) require(LIB_DIR.'HTML.php');

$cat_opts = '';
for ($i = 0, $iz = count($category->category); $i < $iz; ++$i) {
   $cat = &$category->category[$i];
   $cat_opts .= '<optgroup label="'.$cat['parent']['title'].'">'."\n";
   $cat_opts .= '  <option value="'.$cat['parent']['id'].'">All</option>'."\n";

   for ($j = 0, $jz = count($cat['children']); $j < $jz; ++$j) {
      $child = &$cat['children'][$j];
      $cat_opts .= '  <option value="'.$child['id'].'">'.$child['title'].'</option>'."\n";
   }

   $cat_opts .= '</optgroup>'."\n";
}

$header = HTML::body_header('Post an Event');
$footer = HTML::body_footer();
$form = HTML::submit_form($cat_opts, $error_class);

print <<<EOF
$header
$form
$footer
EOF;

?>
