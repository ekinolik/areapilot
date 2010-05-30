<?php

$msg = '';

$page_rows = '';
for ($i = 0, $iz = count($page_c->pages); $i < $iz; ++$i) {
   $page_rows .= '<div style="clear: both;">'."\n";
   $page_rows .= '  <div style="float: left;">'."\n";
   $page_rows .= '    <a href="'.$CURRENT_URL.'edit_page.php?id='.$page_c->pages[$i]['id'].'">'.$page_c->pages[$i]['name'].'</a>'."\n";
   $page_rows .= '  </div>'."\n";
   $page_rows .= '</div>'."\n";
}

print <<<EOF
    <p class="msg">$msg</p>
    <div style="clear: both;">
      <div style="float: left;">Page Name</div>
    </div>
    $page_rows
EOF;

?>
