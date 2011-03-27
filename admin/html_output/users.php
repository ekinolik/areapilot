<?php

$html = '';
for ($i = 0, $iz = count($users->users); $i < $iz; ++$i) {
   $id = htmlspecialchars($users->users[$i]['id']);
   if ($users->users[$i]['active'] == 't') {
      $img = '/images/icons/checkbox.png';
      $status_msg = 'Deactivate';
      $status = 'f';
      $current_status = 'Active';
   } else {
      $img = '/images/icons/error.png';
      $status_msg = 'Activate';
      $status = 't';
      $current_status = 'Deactive';
   }

   if ($users->users[$i]['gender'] == 'f') $gender = 'Female'; else $gender = 'Male';
   if ($users->users[$i]['admin'] == 't') $isadmin = 'Yes'; else $isadmin = 'No';

   $p = '<p class="hidden pUsername">'.htmlspecialchars($users->users[$i]['username']).'</p>';
   $p .= '<p class="hidden pEmail">'.htmlspecialchars($users->users[$i]['email']).'</p>';
   $p .= '<p class="hidden pAdmin">'.$isadmin.'</p>';
   $p .= '<p class="hidden pZip">'.htmlspecialchars($users->users[$i]['zip']).'</p>';
   $p .= '<p class="hidden pLastLogin">'.htmlspecialchars(date("M d, Y, h:i a", strtotime($users->users[$i]['last_login']))).'</p>';
   $p .= '<p class="hidden pActive">'.$current_status.'</p>';
   $p .= '<p class="hidden pGender">'.$gender.'</p>';
   $p .= '<p class="hidden pBirthdate">'.htmlspecialchars(date("M d, Y", strtotime($users->users[$i]['birthdate']))).'</p>';
   $p .= '<p class="hidden pDateAdded">'.htmlspecialchars(date("M d, Y", strtotime($users->users[$i]['added']))).'</p>';

   $html .= '<tr>'."\n";
   $html .= '  <td class="name"><a href="#" title="'.$id.'">'.htmlspecialchars($users->users[$i]['username']).'</a>'.$p.'</td>'."\n";
   $html .= '  <td class="name">'.htmlspecialchars($users->users[$i]['email']).'</td>'."\n";
   $html .= '  <td class="active"><img src="'.$img.'" border="0" /></td>'."\n";
   $html .= '  <td class="status"><a href="'.ADMIN_URL.'admin/update_user.php?id='.$id.'&status='.$status.'">'.$status_msg.'</a></td>'."\n";
   $html .= '  <td class="delete"><a href="'.ADMIN_URL.'admin/update_user.php?id='.$id.'&status=d">Delete</a></td>'."\n";
   $html .= '</tr>'."\n";
}

$html = $users->indent($html, '      ');

?>

<table id="admin_user_list" class="hoverlite" border="0" cellspacing="0" cellpadding="0">
   <thead>
      <tr>
        <td>Username</td>
        <td>Email</td>
        <td>Active?</td>
        <td>De/Activate</td>
        <td>Delete</td>
      </tr>
   </thead>
   <tbody>
<?= $html ?>
   </tbody>
</table>
<div class="modal" id="userPreview">
    <a class="closebox" href="#">x</a>
    <div id="userMsg">
		<ul>
			<li>Username : <span id="displayUsername"></span></li>
			<li>Email : <span id="displayEmail"></span></li>
			<li>Is Admin? : <span id="displayAdmin"></span></li>
			<li>Zipcode : <span id="displayZip"></span></li>
			<li>Active? : <span id="displayActive"></span></li>
			<li>Last Login : <span id="displayLastLogin"></span></li>
			<li>Gender : <span id="displayGender"></span></li>
			<li>Birthdate : <span id="displayBirthdate"></span></li>
			<li>Registered On : <span id="displayDateCreated"></span></li>
		</ul>
    </div>
</div>
