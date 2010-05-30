<?php

$msg = '&nbsp;';
if ($error_class->has_error() === TRUE) 
   $msg = $error_class->error;

?>
<?php
$cat_rows = '';
for ($i = 0, $iz = count($categories); $i < $iz; ++$i) {
   if ($categories[$i]['parent'] != '') continue;
   $id          = $categories[$i]['id'];
   $name        = $categories[$i]['name'];
   $parent_name = $categories[$i]['parent_name'];
   $active      = $categories[$i]['active'];
   $type_name   = $categories[$i]['type_name'];
   if (strlen(trim($name)) < 1)        $name = '&nbsp;';
   if (strlen(trim($parent_name)) < 1) $parent_name = '&nbsp;';
   if (strlen(trim($active)) < 1)      $active = '&nbsp;';
   if (strlen(trim($type_name)) < 1)   $type_name = '&nbsp;';

   if ($active == 't') {
      $status = '<a href="view_categories.php?id='.$id.'&status=f">Deactivate</a>';
		$active = '<img src="/images/icons/checkbox.png" border="0" />';
	} else {
      $status = '<a href="view_categories.php?id='.$id.'&status=t">Activate</a>';
		$active = '<img src="/images/icons/error.png" border="0" />';
	}
   $delete = '<a href="view_categories.php?id='.$id.'&status=delete">Delete</a>';

   $cat_rows .= '<tr>';
   $cat_rows .= '<td class="name">'.$name.'</td>';
   $cat_rows .= '<td class="typename">'.$type_name.'</td>';
   $cat_rows .= '<td class="active">'.$active.'</td>';
   $cat_rows .= '<td class="status">'.$status.'</td>';
   $cat_rows .= '<td class="delete">'.$delete.'</td>';
   $cat_rows .= '</tr>'."\n";

   $parent_id = $id; 
   for ($j = 0; $j < $iz; ++$j) {
      if ($categories[$j]['parent'] == '') continue;
      if ($categories[$j]['parent'] != $parent_id) continue;

      $id          = $categories[$j]['id'];
      $name        = $categories[$j]['name'];
      $parent_name = $categories[$j]['parent_name'];
      $active      = $categories[$j]['active'];
      if (strlen(trim($name)) < 1)        $name = '&nbsp;';
      if (strlen(trim($parent_name)) < 1) $parent_name = '&nbsp;';
      if (strlen(trim($active)) < 1)      $active = '&nbsp;';

      if ($active == 't') {
	 $status = '<a href="view_categories.php?id='.$id.'&status=f">Deactivate</a>';
	 $active = '<img src="/images/icons/checkbox.png" border="0" />';
      } else {
	 $status = '<a href="view_categories.php?id='.$id.'&status=t">Activate</a>';
	 $active = '<img src="/images/icons/error.png" border="0" />';
      }
      $delete = '<a href="view_categories.php?id='.$id.'&status=delete">Delete</a>';

      $cat_rows .= '<tr class="indent">';
      $cat_rows .= '<td class="name">'.$name.'</td>';
      $cat_rows .= '<td class="typename">'.$type_name.'</td>';
      $cat_rows .= '<td class="active">'.$active.'</td>';
      $cat_rows .= '<td class="status">'.$status.'</td>';
      $cat_rows .= '<td class="delete">'.$delete.'</td>';
      $cat_rows .= '</tr>'."\n";
   }

}
?>
	<h2 class="pageheader">Category Admin</h2>
    <p class="msg"><?= $msg ?></p>
    <a href="new_category.php">New Category</a><br />
	<table id="admin_category_list" class="hoverlite" cellpadding="0" cellspacing="0" border="0">
		<thead>
			<tr>
				<td>Category Name</td>
				<td>Type</td>
				<td>Active?</td>
				<td>De/Activate</td>
				<td>Delete</td>
			</tr>
		</thead>
		<tbody>
		<?= $cat_rows; ?>
		</tbody>
	</table>
